<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomesticRate;
use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DomesticRateController extends Controller
{
    public function index()
    {
        try {
            $rates = DomesticRate::with(['partner', 'originZone', 'destinationZone'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                
            $partners = User::where('user_type', 'partner')->get();
            $zones = DeliveryZone::where('is_active', true)->get();
            
            return view('admin.domestic.rates.index', compact('rates', 'partners', 'zones'));
        } catch (\Exception $e) {
            // If tables don't exist yet, show empty state
            return view('admin.domestic.rates.index', [
                'rates' => collect([]),
                'partners' => collect([]),
                'zones' => collect([])
            ])->with('warning', 'Please run migrations first: php artisan migrate');
        }
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
            'partner_id' => 'required|exists:users,id',
            'origin_zone_id' => 'required|exists:delivery_zones,id',
            'destination_zone_id' => 'required|exists:delivery_zones,id',
            'service_type' => 'required|in:flash,same_day,standard,himalayan',
            'base_rate' => 'required|numeric|min:0',
            'per_kg_rate' => 'required|numeric|min:0',
            'per_km_rate' => 'nullable|numeric|min:0',
            'minimum_rate' => 'nullable|numeric|min:0',
            'logistical_charge' => 'nullable|numeric|min:0',
            'additional_charge' => 'nullable|numeric|min:0',
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

        $serviceNames = [
            'flash' => 'FLASH',
            'same_day' => 'SAME DAY',
            'standard' => 'STANDARD',
            'himalayan' => 'HIMALAYAN',
        ];

        DomesticRate::create([
            'partner_id' => $request->partner_id,
            'origin_zone_id' => $request->origin_zone_id,
            'destination_zone_id' => $request->destination_zone_id,
            'service_type' => $request->service_type,
            'service_name' => $serviceNames[$request->service_type],
            'base_rate' => $request->base_rate,
            'per_kg_rate' => $request->per_kg_rate,
            'per_km_rate' => $request->per_km_rate ?? 0,
            'minimum_rate' => $request->minimum_rate ?? 0,
            'logistical_charge' => $request->logistical_charge ?? 0,
            'additional_charge' => $request->additional_charge ?? 0,
            'additional_charge_reason' => $request->additional_charge_reason,
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
            'partner_id' => 'required|exists:users,id',
            'origin_zone_id' => 'required|exists:delivery_zones,id',
            'destination_zone_id' => 'required|exists:delivery_zones,id',
            'service_type' => 'required|in:flash,same_day,standard,himalayan',
            'base_rate' => 'required|numeric|min:0',
            'per_kg_rate' => 'required|numeric|min:0',
            'per_km_rate' => 'nullable|numeric|min:0',
            'minimum_rate' => 'nullable|numeric|min:0',
            'logistical_charge' => 'nullable|numeric|min:0',
            'additional_charge' => 'nullable|numeric|min:0',
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

        $serviceNames = [
            'flash' => 'FLASH',
            'same_day' => 'SAME DAY',
            'standard' => 'STANDARD',
            'himalayan' => 'HIMALAYAN',
        ];

        $rate->update([
            'partner_id' => $request->partner_id,
            'origin_zone_id' => $request->origin_zone_id,
            'destination_zone_id' => $request->destination_zone_id,
            'service_type' => $request->service_type,
            'service_name' => $serviceNames[$request->service_type],
            'base_rate' => $request->base_rate,
            'per_kg_rate' => $request->per_kg_rate,
            'per_km_rate' => $request->per_km_rate ?? 0,
            'minimum_rate' => $request->minimum_rate ?? 0,
            'logistical_charge' => $request->logistical_charge ?? 0,
            'additional_charge' => $request->additional_charge ?? 0,
            'additional_charge_reason' => $request->additional_charge_reason,
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
}