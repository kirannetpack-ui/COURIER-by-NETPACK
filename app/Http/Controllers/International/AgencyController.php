<?php

namespace App\Http\Controllers\International;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\OverseasHub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Agency::with(['hub', 'staff', 'manifests']);

        if ($request->filled('hub_id')) {
            $query->where('hub_id', $request->hub_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $agencies = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $hubs = OverseasHub::active()->orderBy('sort_order')->get();

        return view('international.agencies.index', compact('agencies', 'hubs'));
    }

    public function create()
    {
        $hubs = OverseasHub::active()->orderBy('sort_order')->get();
        return view('international.agencies.create', compact('hubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hub_id' => 'required|exists:overseas_hubs,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:agencies,code',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:30',
            'phone_secondary' => 'nullable|string|max:30',
            'primary_contact' => 'nullable|string|max:255',
            'email' => 'required|email|unique:agencies,email',
            'notification_emails' => 'nullable|string', // Comma or newline separated
            'operational_notes' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        $emails = $this->parseEmails($request->input('notification_emails'));

        Agency::create([
            'hub_id' => $validated['hub_id'],
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'country' => $validated['country'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'phone_secondary' => $validated['phone_secondary'] ?? null,
            'primary_contact' => $validated['primary_contact'] ?? null,
            'email' => strtolower($validated['email']),
            'notification_emails' => $emails,
            'operational_notes' => $validated['operational_notes'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return redirect()->route('international.agencies.index')
            ->with('success', "Agency '{$validated['name']}' created successfully with " . count($emails) . " pre-defined notification email(s).");
    }

    public function edit($id)
    {
        $agency = Agency::findOrFail($id);
        $hubs = OverseasHub::active()->orderBy('sort_order')->get();

        return view('international.agencies.edit', compact('agency', 'hubs'));
    }

    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);

        $validated = $request->validate([
            'hub_id' => 'required|exists:overseas_hubs,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:agencies,code,' . $agency->id,
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:30',
            'phone_secondary' => 'nullable|string|max:30',
            'primary_contact' => 'nullable|string|max:255',
            'email' => 'required|email|unique:agencies,email,' . $agency->id,
            'notification_emails' => 'nullable|string',
            'operational_notes' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
        ]);

        $emails = $this->parseEmails($request->input('notification_emails'));

        $updates = [
            'hub_id' => $validated['hub_id'],
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'country' => $validated['country'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'phone_secondary' => $validated['phone_secondary'] ?? null,
            'primary_contact' => $validated['primary_contact'] ?? null,
            'email' => strtolower($validated['email']),
            'notification_emails' => $emails,
            'operational_notes' => $validated['operational_notes'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
        }

        $agency->update($updates);

        return redirect()->route('international.agencies.index')
            ->with('success', "Agency '{$agency->name}' updated successfully.");
    }

    /**
     * Show & Update Customizable Manifest and Data Sheet Fields for this Agency
     */
    public function formatSettings($id)
    {
        $agency = Agency::findOrFail($id);
        return view('international.agencies.format-settings', compact('agency'));
    }

    public function updateFormatSettings(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);

        $manifestInputs = $request->input('manifest_fields', []);
        $datasheetInputs = $request->input('datasheet_fields', []);

        $manifestFields = [];
        if (is_array($manifestInputs)) {
            foreach ($manifestInputs as $key => $item) {
                if (is_array($item) && !empty($item['enabled'])) {
                    $manifestFields[$key] = !empty($item['label']) ? trim($item['label']) : $key;
                } elseif (is_string($item)) {
                    $manifestFields[$key] = $item;
                }
            }
        }

        $datasheetFields = [];
        if (is_array($datasheetInputs)) {
            foreach ($datasheetInputs as $key => $item) {
                if (is_array($item) && !empty($item['enabled'])) {
                    $datasheetFields[$key] = !empty($item['label']) ? trim($item['label']) : $key;
                } elseif (is_string($item)) {
                    $datasheetFields[$key] = $item;
                }
            }
        }

        $agency->update([
            'manifest_fields' => $manifestFields,
            'datasheet_fields' => $datasheetFields,
        ]);

        return redirect()->route('international.agencies.format-settings', $agency->id)
            ->with('success', "Manifest & Data Sheet formats for '{$agency->name}' updated successfully.");
    }

    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);

        if ($agency->manifests()->count() > 0 || $agency->shipments()->count() > 0) {
            return back()->with('error', "Cannot delete agency '{$agency->name}' because it has existing manifests or shipments.");
        }

        $agency->delete();

        return redirect()->route('international.agencies.index')
            ->with('success', "Agency '{$agency->name}' deleted successfully.");
    }

    private function parseEmails(?string $input): array
    {
        if (empty($input)) return [];

        $parts = preg_split('/[\r\n,;]+/', $input);
        $clean = [];

        foreach ($parts as $part) {
            $email = strtolower(trim($part));
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $clean, true)) {
                $clean[] = $email;
            }
        }

        return $clean;
    }
}
