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
        $pod = ProofOfDelivery::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,uploaded,verified,rejected',
            'notes' => 'nullable|string',
        ]);

        $pod->status = $request->status;
        $pod->save();

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
        'signature_data' => 'nullable|string',
    ]);

    try {
        DB::beginTransaction();

        // Get the shipment
        $shipment = Shipment::find($request->shipment_id);
        $manifestShipment = ManifestShipment::find($request->manifest_shipment_id);

        // Handle file upload
        $podFile = null;
        $podPhoto = null;

        if ($request->hasFile('pod_photo')) {
            $file = $request->file('pod_photo');
            $filename = 'pod_photo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('pods/photos', $filename, 'public');
            $podPhoto = $path;
        }

        if ($request->hasFile('pod_file')) {
            $file = $request->file('pod_file');
            $filename = 'pod_file_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('pods/files', $filename, 'public');
            $podFile = $path;
        }

        // Handle signature (if provided as base64)
        if ($request->signature_data) {
            $signatureData = $request->signature_data;
            if (strpos($signatureData, 'data:image') === 0) {
                // Decode base64 and save
                $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
                $signatureData = str_replace(' ', '+', $signatureData);
                $image = base64_decode($signatureData);
                
                $filename = 'signature_' . time() . '_' . uniqid() . '.png';
                $path = 'pods/signatures/' . $filename;
                Storage::disk('public')->put($path, $image);
                $signaturePath = $path;
            }
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