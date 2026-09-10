<?php

namespace App\Http\Controllers\International;

use App\Http\Controllers\Controller;
use App\Models\OverseasHub;
use Illuminate\Http\Request;

class HubController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin,admin,international_admin,staff']);
    }

    public function index(Request $request)
    {
        $query = OverseasHub::with(['agencies', 'lastMileCarriers', 'mawbs']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('hub_name', 'like', "%{$search}%")
                  ->orWhere('hub_code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $hubs = $query->orderBy('sort_order', 'asc')->paginate(15)->withQueryString();

        return view('international.hubs.index', compact('hubs'));
    }

    public function create()
    {
        return view('international.hubs.create');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        if (empty($input['hub_code']) && !empty($input['code'])) {
            $input['hub_code'] = $input['code'];
        }
        if (empty($input['hub_name']) && !empty($input['name'])) {
            $input['hub_name'] = $input['name'];
        }
        if (empty($input['location']) && !empty($input['city'])) {
            $input['location'] = $input['city'];
        }
        if (empty($input['address']) && !empty($input['airport_name'])) {
            $input['address'] = $input['airport_name'];
        }
        if (empty($input['hub_type'])) {
            $input['hub_type'] = 'main_hub';
        }

        $request->merge($input);

        $validated = $request->validate([
            'hub_code' => 'required|string|max:10|alpha_dash|unique:overseas_hubs,hub_code',
            'hub_name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'hub_type' => 'nullable|in:main_hub,transit_point,sorting_center,delivery_hub',
            'mode_type' => 'required|string|max:100',
            'address' => 'nullable|string',
            'coverage_countries' => 'nullable',
            'service_routes' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $coverageCountries = $this->parseCommaSeparatedList($request->input('coverage_countries'));
        $serviceRoutes = $this->parseCommaSeparatedList($request->input('service_routes'));

        $hub = OverseasHub::create([
            'hub_code' => strtoupper($validated['hub_code']),
            'hub_name' => $validated['hub_name'],
            'country' => $validated['country'],
            'location' => $validated['location'] ?? ($validated['country'] . ' Gateway'),
            'hub_type' => $validated['hub_type'] ?? 'main_hub',
            'mode_type' => $validated['mode_type'],
            'address' => $validated['address'] ?? ($validated['country'] . ' Airport Cargo Terminal'),
            'coverage_countries' => $coverageCountries,
            'service_routes' => $serviceRoutes,
            'is_active' => $request->boolean('is_active', true),
            'is_mandatory' => $request->boolean('is_mandatory', false),
            'sort_order' => $validated['sort_order'] ?? (OverseasHub::max('sort_order') + 1),
        ]);

        // Merge Partner Agency function: create attached agency if details provided
        if ($request->filled('handling_agency_name')) {
            $agency = \App\Models\Agency::create([
                'hub_id' => $hub->id,
                'name' => $request->input('handling_agency_name'),
                'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->input('handling_agency_name')), 0, 4)) . '-' . $hub->code,
                'country' => $hub->country,
                'city' => $hub->city ?? $hub->location,
                'address' => $request->input('agency_address') ?? $hub->address,
                'phone' => $request->input('agency_phone') ?? '+000-0000',
                'primary_contact' => $request->input('agency_contact_person') ?? 'Hub Operations Desk',
                'email' => $request->input('agency_email') ?? ('ops.' . strtolower($hub->code) . '@netpack.com'),
                'password' => bcrypt('Netpack@123'),
                'is_active' => true,
            ]);
            $hub->agencies()->syncWithoutDetaching([$agency->id]);
        }

        return redirect()->route('international.hubs.index')
            ->with('success', "International Hub '{$hub->hub_name}' created successfully.");
    }

    public function edit($id)
    {
        $hub = OverseasHub::with('agencies')->findOrFail($id);
        $agency = $hub->agencies->first();
        return view('international.hubs.edit', compact('hub', 'agency'));
    }

    public function update(Request $request, $id)
    {
        $hub = OverseasHub::with('agencies')->findOrFail($id);

        $input = $request->all();
        if (empty($input['hub_code']) && !empty($input['code'])) {
            $input['hub_code'] = $input['code'];
        }
        if (empty($input['hub_name']) && !empty($input['name'])) {
            $input['hub_name'] = $input['name'];
        }
        if (empty($input['location']) && !empty($input['city'])) {
            $input['location'] = $input['city'];
        }
        if (empty($input['address']) && !empty($input['airport_name'])) {
            $input['address'] = $input['airport_name'];
        }

        $request->merge($input);

        $validated = $request->validate([
            'hub_code' => 'required|string|max:10|alpha_dash|unique:overseas_hubs,hub_code,' . $hub->id,
            'hub_name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'hub_type' => 'nullable|in:main_hub,transit_point,sorting_center,delivery_hub',
            'mode_type' => 'required|string|max:100',
            'address' => 'nullable|string',
            'coverage_countries' => 'nullable',
            'service_routes' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $coverageCountries = $this->parseCommaSeparatedList($request->input('coverage_countries'));
        $serviceRoutes = $this->parseCommaSeparatedList($request->input('service_routes'));

        $hub->update([
            'hub_code' => strtoupper($validated['hub_code']),
            'hub_name' => $validated['hub_name'],
            'country' => $validated['country'],
            'location' => $validated['location'] ?? $hub->location,
            'hub_type' => $validated['hub_type'] ?? $hub->hub_type,
            'mode_type' => $validated['mode_type'],
            'address' => $validated['address'] ?? $hub->address,
            'coverage_countries' => $coverageCountries,
            'service_routes' => $serviceRoutes,
            'sort_order' => $validated['sort_order'] ?? $hub->sort_order,
            'is_active' => $request->boolean('is_active', true),
            'is_mandatory' => $request->boolean('is_mandatory', false),
        ]);

        // Merge Partner Agency function: update or create attached agency
        if ($request->filled('handling_agency_name')) {
            $agency = $hub->agencies->first();
            if ($agency) {
                $agency->update([
                    'name' => $request->input('handling_agency_name'),
                    'country' => $hub->country,
                    'city' => $hub->city ?? $hub->location,
                    'address' => $request->input('agency_address') ?? $hub->address,
                    'phone' => $request->input('agency_phone') ?? $agency->phone,
                    'primary_contact' => $request->input('agency_contact_person') ?? $agency->primary_contact,
                    'email' => $request->input('agency_email') ?? $agency->email,
                ]);
            } else {
                $agency = \App\Models\Agency::create([
                    'hub_id' => $hub->id,
                    'name' => $request->input('handling_agency_name'),
                    'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->input('handling_agency_name')), 0, 4)) . '-' . $hub->code,
                    'country' => $hub->country,
                    'city' => $hub->city ?? $hub->location,
                    'address' => $request->input('agency_address') ?? $hub->address,
                    'phone' => $request->input('agency_phone') ?? '+000-0000',
                    'primary_contact' => $request->input('agency_contact_person') ?? 'Hub Operations Desk',
                    'email' => $request->input('agency_email') ?? ('ops.' . strtolower($hub->code) . '@netpack.com'),
                    'password' => bcrypt('Netpack@123'),
                    'is_active' => true,
                ]);
                $hub->agencies()->syncWithoutDetaching([$agency->id]);
            }
        }

        return redirect()->route('international.hubs.index')
            ->with('success', "International Hub '{$hub->hub_name}' updated successfully.");
    }

    public function toggle($id)
    {
        $hub = OverseasHub::findOrFail($id);
        $hub->is_active = !$hub->is_active;
        $hub->save();

        $status = $hub->is_active ? 'activated' : 'deactivated';
        return redirect()->route('international.hubs.index')
            ->with('success', "International Hub '{$hub->hub_name}' {$status} successfully.");
    }

    /**
     * Helper to reliably split comma, newline, or array input into a clean string list.
     */
    protected function parseCommaSeparatedList($value): array
    {
        if (is_null($value) || $value === '') {
            return [];
        }
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded)));
        }
        $parts = preg_split('/[,\r\n]+/', (string) $value);
        return array_values(array_filter(array_map('trim', $parts)));
    }

    public function destroy($id)
    {
        $hub = OverseasHub::findOrFail($id);

        if ($hub->agencies()->count() > 0) {
            return back()->with('error', "Cannot delete Hub '{$hub->hub_name}'. It currently has {$hub->agencies()->count()} active agencies assigned.");
        }

        $hub->delete();

        return redirect()->route('international.hubs.index')
            ->with('success', "Hub '{$hub->hub_name}' has been deleted.");
    }
}
