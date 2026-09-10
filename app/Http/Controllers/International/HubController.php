<?php

namespace App\Http\Controllers\International;

use App\Http\Controllers\Controller;
use App\Models\OverseasHub;
use Illuminate\Http\Request;

class HubController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            'is_active' => 'nullable',
        ]);

        $hub = OverseasHub::create([
            'hub_code' => strtoupper($validated['hub_code']),
            'hub_name' => $validated['hub_name'],
            'country' => $validated['country'],
            'location' => $validated['location'] ?? ($validated['country'] . ' Gateway'),
            'hub_type' => $validated['hub_type'] ?? 'main_hub',
            'mode_type' => $validated['mode_type'],
            'address' => $validated['address'] ?? ($validated['country'] . ' Airport Cargo Terminal'),
            'coverage_countries' => $validated['coverage_countries'] ?? [],
            'service_routes' => $validated['service_routes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => OverseasHub::max('sort_order') + 1,
        ]);

        return redirect()->route('international.hubs.index')
            ->with('success', "International Hub '{$hub->hub_name}' created successfully.");
    }

    public function edit($id)
    {
        $hub = OverseasHub::findOrFail($id);
        return view('international.hubs.edit', compact('hub'));
    }

    public function update(Request $request, $id)
    {
        $hub = OverseasHub::findOrFail($id);

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
            'is_active' => 'nullable',
        ]);

        $hub->update([
            'hub_code' => strtoupper($validated['hub_code']),
            'hub_name' => $validated['hub_name'],
            'country' => $validated['country'],
            'location' => $validated['location'] ?? $hub->location,
            'hub_type' => $validated['hub_type'] ?? $hub->hub_type,
            'mode_type' => $validated['mode_type'],
            'address' => $validated['address'] ?? $hub->address,
            'coverage_countries' => $validated['coverage_countries'] ?? $hub->coverage_countries,
            'service_routes' => $validated['service_routes'] ?? $hub->service_routes,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('international.hubs.index')
            ->with('success', "International Hub '{$hub->hub_name}' updated successfully.");
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
