<?php

namespace App\Http\Controllers\Domestic;

use App\Http\Controllers\Controller;
use App\Models\Manifest;
use App\Models\ManifestBag;
use App\Models\ManifestShipment;
use App\Models\Shipment;
use App\Models\ProofOfDelivery;
use App\Models\User;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManifestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all manifests
     */
    public function index(Request $request)
    {
        $query = Manifest::with(['creator', 'partner']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('manifest_number', 'LIKE', "%{$search}%")
                  ->orWhere('origin_city', 'LIKE', "%{$search}%")
                  ->orWhere('destination_city', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $manifests = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => Manifest::count(),
            'pending' => Manifest::where('status', 'pending')->count(),
            'in_transit' => Manifest::where('status', 'in_transit')->count(),
            'delivered' => Manifest::where('status', 'delivered')->count(),
        ];

        return view('domestic.manifests.index', compact('manifests', 'stats'));
    }

    /**
     * Show form to create a new manifest
     */
    public function create()
    {
        $partners = User::where('user_type', 'partner')
            ->where('verification_status', 'approved')
            ->get();

        // Get shipments that are not yet manifested
        $shipments = Shipment::where('shipment_type', 'domestic')
            ->where('status', 'pending')
            ->whereDoesntHave('manifestShipment')
            ->get();

        return view('domestic.manifests.create', compact('partners', 'shipments'));
    }

    /**
     * Store a new manifest
     */
    public function store(Request $request)
    {
        // Validation will be added
        return redirect()->route('domestic.manifests.index')
            ->with('success', 'Manifest created successfully!');
    }

    /**
     * Show manifest details
     */
    public function show($id)
    {
        $manifest = Manifest::with(['creator', 'partner', 'bags', 'bags.shipments', 'trackingLogs'])
            ->findOrFail($id);

        return view('domestic.manifests.show', compact('manifest'));
    }

    /**
     * Show edit manifest form
     */
    public function edit($id)
    {
        $manifest = Manifest::with(['bags', 'bags.shipments'])->findOrFail($id);
        $partners = User::where('user_type', 'partner')->where('verification_status', 'approved')->get();

        return view('domestic.manifests.edit', compact('manifest', 'partners'));
    }

    /**
     * Update manifest
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('domestic.manifests.show', $id)
            ->with('success', 'Manifest updated successfully!');
    }

    /**
     * Scan bag QR code
     */
    public function scanBag(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Bag scanned successfully!'
        ]);
    }

    /**
     * List all PODs with filters
     */
    public function pods(Request $request)
{
    $query = ProofOfDelivery::with(['shipment', 'manifest', 'uploadedBy']);

    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->whereHas('shipment', function($q) use ($search) {
            $q->where('tracking_number', 'LIKE', "%{$search}%")
              ->orWhere('receiver_name', 'LIKE', "%{$search}%");
        });
    }

    if ($request->has('status') && $request->status) {
        $query->where('status', $request->status);
    }

    if ($request->has('date_from') && $request->date_from) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->has('date_to') && $request->date_to) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $pods = $query->orderBy('created_at', 'desc')->paginate(20);

    $stats = [
        'total' => ProofOfDelivery::count(),
        'uploaded' => ProofOfDelivery::where('status', 'uploaded')->count(),
        'verified' => ProofOfDelivery::where('status', 'verified')->count(),
        'pending' => ProofOfDelivery::where('status', 'pending')->count(),
    ];

    return view('domestic.manifests.pods', compact('pods', 'stats'));
}

    /**
     * Show single POD details
     */
    public function showPod($id)
    {
        $pod = ProofOfDelivery::with(['shipment', 'manifest', 'uploadedBy'])
            ->findOrFail($id);

        return view('domestic.manifests.pod-details', compact('pod'));
    }

    /**
     * Update POD status
     */
    public function updatePodStatus(Request $request, $id)
    {
        abort_unless(in_array($request->user()->user_type, ['super_admin', 'admin', 'domestic_admin'], true), 403);

        $pod = ProofOfDelivery::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,uploaded,verified,rejected',
            'notes' => 'nullable|string',
        ]);

        $pod->update([
            'status' => $request->status,
            'verified_by' => $request->status === 'verified' ? $request->user()->id : null,
            'verified_at' => $request->status === 'verified' ? now() : null,
            'rejection_reason' => $request->status === 'rejected' ? $request->notes : null,
        ]);

        return redirect()->route('domestic.manifests.pods.show', $pod->id)
            ->with('success', "POD status updated to {$request->status} successfully!");
    }

    /**
 * Upload Proof of Delivery
 */
public function uploadPOD(Request $request)
{
    $request->validate([
        'shipment_id' => 'required|exists:shipments,id',
        'manifest_shipment_id' => 'required|exists:manifest_shipments,id',
        'recipient_name' => 'required|string|max:255',
        'delivered_at' => 'nullable|date',
        'delivery_notes' => 'nullable|string',
        'pod_photo' => 'nullable|image|max:5120', // 5MB max
        'pod_file' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
        'signature_data' => 'nullable|string|max:1048576',
    ]);

    try {
        DB::beginTransaction();

        // Lock the record and verify that this manifest line belongs to the
        // submitted shipment. This prevents a valid user from attaching POD to
        // an unrelated shipment by altering request IDs.
        $shipment = Shipment::lockForUpdate()->findOrFail($request->shipment_id);
        $manifestShipment = ManifestShipment::with('manifest')->lockForUpdate()->findOrFail($request->manifest_shipment_id);

        if ((int) $manifestShipment->shipment_id !== (int) $shipment->id) {
            abort(422, 'The selected manifest line does not belong to this shipment.');
        }

        $user = $request->user();
        $isOperationsUser = in_array($user->user_type, ['super_admin', 'admin', 'domestic_admin', 'staff'], true);
        $isAssignedPartner = $user->user_type === 'partner'
            && ((int) $manifestShipment->partner_id === (int) $user->id || (int) optional($manifestShipment->manifest)->partner_id === (int) $user->id);

        abort_unless($isOperationsUser || $isAssignedPartner, 403);

        if ($manifestShipment->status === 'delivered' || ProofOfDelivery::where('manifest_shipment_id', $manifestShipment->id)->exists()) {
            abort(422, 'A proof of delivery already exists for this manifest shipment.');
        }

        // Handle file upload
        $podFile = null;
        $podPhoto = null;
        $signaturePath = null;

        if ($request->hasFile('pod_photo')) {
            $file = $request->file('pod_photo');
            $filename = 'pod_photo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('pods/photos', $filename, 'private');
            $podPhoto = $path;
        }

        if ($request->hasFile('pod_file')) {
            $file = $request->file('pod_file');
            $filename = 'pod_file_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('pods/files', $filename, 'private');
            $podFile = $path;
        }

        // Handle signature (if provided as base64)
        if ($request->signature_data) {
            $signatureData = $request->signature_data;
            if (str_starts_with($signatureData, 'data:image/png;base64,')) {
                // Decode base64 and save
                $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
                $signatureData = str_replace(' ', '+', $signatureData);
                $image = base64_decode($signatureData, true);
                if ($image === false || $image === '') {
                    abort(422, 'The signature image is invalid.');
                }
                
                $filename = 'signature_' . time() . '_' . uniqid() . '.png';
                $path = 'pods/signatures/' . $filename;
                Storage::disk('private')->put($path, $image);
                $signaturePath = $path;
            }
        }

        if (!$podPhoto && !$podFile && !$signaturePath) {
            abort(422, 'Provide a photo, document, or signature as proof of delivery.');
        }

        // Create POD record
        $pod = ProofOfDelivery::create([
            'manifest_shipment_id' => $manifestShipment->id,
            'shipment_id' => $shipment->id,
            'manifest_id' => $manifestShipment->manifest_id,
            'uploaded_by' => auth()->id(),
            'pod_type' => $request->pod_photo ? 'photo' : ($request->pod_file ? 'file' : 'signature'),
            'pod_file' => $podFile,
            'pod_photo' => $podPhoto,
            'recipient_name' => $request->recipient_name,
            'recipient_signature' => $signaturePath ?? null,
            'delivery_notes' => $request->delivery_notes,
            'delivered_at' => $request->delivered_at ?? now(),
            'status' => 'uploaded',
            'metadata' => ['storage_disk' => 'private'],
        ]);

        // Update shipment status
        $shipment->status = 'delivered';
        $shipment->save();

        // Update manifest shipment status
        $manifestShipment->status = 'delivered';
        $manifestShipment->save();

        DB::commit();

        return redirect()->route('domestic.manifests.pods')
            ->with('success', '✅ Proof of Delivery uploaded successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()
            ->with('error', '❌ Failed to upload POD: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Serve POD artifacts only to authorised operations users or the partner
     * assigned to the manifest. New records live on the private disk; this
     * keeps old records viewable while they are migrated out of public storage.
     */
    public function downloadPodFile(Request $request, $id, $type)
    {
        $pod = ProofOfDelivery::with('manifestShipment.manifest')->findOrFail($id);
        $user = $request->user();
        $isOperationsUser = in_array($user->user_type, ['super_admin', 'admin', 'domestic_admin', 'staff'], true);
        $manifestShipment = $pod->manifestShipment;
        $isAssignedPartner = $user->user_type === 'partner' && $manifestShipment
            && ((int) $manifestShipment->partner_id === (int) $user->id || (int) optional($manifestShipment->manifest)->partner_id === (int) $user->id);

        abort_unless($isOperationsUser || $isAssignedPartner, 403);

        $path = match ($type) {
            'photo' => $pod->pod_photo,
            'file' => $pod->pod_file,
            'signature' => $pod->recipient_signature,
            default => abort(404),
        };

        abort_unless($path, 404);
        $disk = data_get($pod->metadata, 'storage_disk') === 'private' ? 'private' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);

        if (in_array($type, ['photo', 'signature'], true)) {
            return Storage::disk($disk)->response($path, basename($path), [
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            ]);
        }

        return Storage::disk($disk)->download($path, basename($path));
    }

/**
 * Show upload form for POD
 */
public function showUploadForm($shipmentId)
{
    $shipment = Shipment::findOrFail($shipmentId);
    $manifestShipment = ManifestShipment::where('shipment_id', $shipmentId)->first();
    
    if (!$manifestShipment) {
        return redirect()->route('domestic.manifests.pods')
            ->with('error', 'No manifest found for this shipment.');
    }
    
    return view('domestic.manifests.pod-upload', compact('shipment', 'manifestShipment'));
}


}
