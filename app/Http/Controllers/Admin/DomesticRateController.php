<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomesticRate;
use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DomesticRateController extends Controller
{
    public function index()
    {
        $rates = DomesticRate::with(['partner', 'originZone', 'destinationZone'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $partners = User::where('user_type', 'partner')->where('verification_status', 'approved')->get();
        $zones = DeliveryZone::where('is_active', true)->get();

        return view('admin.domestic.rates.index', compact('rates', 'partners', 'zones'));
    }

    public function create()
    {
        $partners = User::where('user_type', 'partner')->get();
        $zones = DeliveryZone::where('is_active', true)->get();
        $serviceTypes = DomesticRate::getServiceTypeOptions();
        
        return view('admin.domestic.rates.create', compact('partners', 'zones', 'serviceTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partner_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('user_type', 'partner')->where('verification_status', 'approved'))],
            'origin_zone_id' => 'required|exists:delivery_zones,id',
            'destination_zone_id' => 'required|exists:delivery_zones,id',
            'service_type' => 'required|in:flash,same_day,standard,himalayan',
            'base_rate' => 'required|numeric|min:0',
            'per_kg_rate' => 'required|numeric|min:0',
            'per_km_rate' => 'nullable|numeric|min:0',
            'minimum_rate' => 'nullable|numeric|min:0',
            'logistical_charge' => 'nullable|numeric|min:0',
            'additional_charge' => 'nullable|numeric|min:0',
            'additional_charge_reason' => 'nullable|string|max:1000',
            'currency' => 'required|string|size:3',
            'weight_from' => 'required|numeric|min:0',
            'weight_to' => 'required|numeric|gt:weight_from',
            'estimated_hours' => 'nullable|integer|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->ensureNoOverlappingRate($request);

        $serviceNames = [
            'flash' => 'FLASH',
            'same_day' => 'SAME DAY',
            'standard' => 'STANDARD',
            'himalayan' => 'HIMALAYAN',
        ];

        $originZone = DeliveryZone::findOrFail($request->origin_zone_id);
        $destinationZone = DeliveryZone::findOrFail($request->destination_zone_id);

        DomesticRate::create([
            'partner_id' => $request->partner_id,
            'origin_zone_id' => $request->origin_zone_id,
            'destination_zone_id' => $request->destination_zone_id,
            // These are required by the original domestic_rates schema and
            // remain populated for backwards-compatible quote lookups.
            'origin_city' => $originZone->zone_name,
            'origin_zone' => $originZone->zone_code,
            'destination_city' => $destinationZone->zone_name,
            'destination_zone' => $destinationZone->zone_code,
            'service_type' => $request->service_type,
            'service_name' => $serviceNames[$request->service_type],
            'base_rate' => $request->base_rate,
            'per_kg_rate' => $request->per_kg_rate,
            'rate_per_kg' => $request->per_kg_rate,
            'per_km_rate' => $request->per_km_rate ?? 0,
            'minimum_rate' => $request->minimum_rate ?? 0,
            'logistical_charge' => $request->logistical_charge ?? 0,
            'additional_charge' => $request->additional_charge ?? 0,
            'additional_charge_reason' => $request->additional_charge_reason,
            'currency' => strtoupper($request->currency),
            'weight_from' => $request->weight_from,
            'weight_to' => $request->weight_to,
            'estimated_hours' => $request->estimated_hours,
            'estimated_days' => $request->estimated_days,
            'is_active' => true,
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
        ]);

        return redirect()->route('admin.domestic.rates')
            ->with('success', 'Domestic rate created successfully!');
    }

    public function edit($id)
    {
        $rate = DomesticRate::findOrFail($id);
        $partners = User::where('user_type', 'partner')->get();
        $zones = DeliveryZone::where('is_active', true)->get();
        $serviceTypes = DomesticRate::getServiceTypeOptions();
        
        return view('admin.domestic.rates.edit', compact('rate', 'partners', 'zones', 'serviceTypes'));
    }

    public function update(Request $request, $id)
    {
        $rate = DomesticRate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'partner_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('user_type', 'partner')->where('verification_status', 'approved'))],
            'origin_zone_id' => 'required|exists:delivery_zones,id',
            'destination_zone_id' => 'required|exists:delivery_zones,id',
            'service_type' => 'required|in:flash,same_day,standard,himalayan',
            'base_rate' => 'required|numeric|min:0',
            'per_kg_rate' => 'required|numeric|min:0',
            'per_km_rate' => 'nullable|numeric|min:0',
            'minimum_rate' => 'nullable|numeric|min:0',
            'logistical_charge' => 'nullable|numeric|min:0',
            'additional_charge' => 'nullable|numeric|min:0',
            'additional_charge_reason' => 'nullable|string|max:1000',
            'currency' => 'required|string|size:3',
            'weight_from' => 'required|numeric|min:0',
            'weight_to' => 'required|numeric|gt:weight_from',
            'estimated_hours' => 'nullable|integer|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->ensureNoOverlappingRate($request, $rate->id);

        $serviceNames = [
            'flash' => 'FLASH',
            'same_day' => 'SAME DAY',
            'standard' => 'STANDARD',
            'himalayan' => 'HIMALAYAN',
        ];

        $originZone = DeliveryZone::findOrFail($request->origin_zone_id);
        $destinationZone = DeliveryZone::findOrFail($request->destination_zone_id);

        $rate->update([
            'partner_id' => $request->partner_id,
            'origin_zone_id' => $request->origin_zone_id,
            'destination_zone_id' => $request->destination_zone_id,
            'origin_city' => $originZone->zone_name,
            'origin_zone' => $originZone->zone_code,
            'destination_city' => $destinationZone->zone_name,
            'destination_zone' => $destinationZone->zone_code,
            'service_type' => $request->service_type,
            'service_name' => $serviceNames[$request->service_type],
            'base_rate' => $request->base_rate,
            'per_kg_rate' => $request->per_kg_rate,
            'rate_per_kg' => $request->per_kg_rate,
            'per_km_rate' => $request->per_km_rate ?? 0,
            'minimum_rate' => $request->minimum_rate ?? 0,
            'logistical_charge' => $request->logistical_charge ?? 0,
            'additional_charge' => $request->additional_charge ?? 0,
            'additional_charge_reason' => $request->additional_charge_reason,
            'currency' => strtoupper($request->currency),
            'weight_from' => $request->weight_from,
            'weight_to' => $request->weight_to,
            'estimated_hours' => $request->estimated_hours,
            'estimated_days' => $request->estimated_days,
            'is_active' => $request->has('is_active'),
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
        ]);

        return redirect()->route('admin.domestic.rates')
            ->with('success', 'Domestic rate updated successfully!');
    }

    public function destroy($id)
    {
        $rate = DomesticRate::findOrFail($id);
        $rate->delete();

        return redirect()->route('admin.domestic.rates')
            ->with('success', 'Domestic rate deleted successfully!');
    }

    /** Reject ambiguous weight/date bands before they can affect quoting. */
    private function ensureNoOverlappingRate(Request $request, ?int $ignoreRateId = null): void
    {
        $effectiveTo = $request->input('effective_to') ?: '9999-12-31';

        $query = DomesticRate::query()
            ->where('partner_id', $request->partner_id)
            ->where('origin_zone_id', $request->origin_zone_id)
            ->where('destination_zone_id', $request->destination_zone_id)
            ->where('service_type', $request->service_type)
            ->where('weight_from', '<=', $request->weight_to)
            ->where('weight_to', '>=', $request->weight_from)
            ->whereDate('effective_from', '<=', $effectiveTo)
            ->where(function ($query) use ($request) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $request->effective_from);
            });

        if ($ignoreRateId) {
            $query->whereKeyNot($ignoreRateId);
        }

        if ($query->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'weight_from' => 'This rate overlaps an existing active or scheduled rate for the same partner, route, service, weight band, and effective period.',
            ]);
        }
    }
}
