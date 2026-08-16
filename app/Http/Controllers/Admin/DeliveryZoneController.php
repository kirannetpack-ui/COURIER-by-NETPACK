<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::with('partner')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $partners = User::where('user_type', 'partner')->get();
        
        return view('admin.domestic.zones.index', compact('zones', 'partners'));
    }

    public function create()
    {
        $partners = User::where('user_type', 'partner')->get();
        $zoneTypes = ['urban', 'semi_urban', 'rural', 'hilly', 'himalayan'];
        
        return view('admin.domestic.zones.create', compact('partners', 'zoneTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|exists:users,id',
            'zone_name' => 'required|string|max:255',
            'zone_type' => 'required|in:urban,semi_urban,rural,hilly,himalayan',
            'districts' => 'nullable|array',
            'municipalities' => 'nullable|array',
            'wards' => 'nullable|array',
            'postal_codes' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $zoneCode = Str::upper(Str::random(6));

        DeliveryZone::create([
            'partner_id' => $request->partner_id,
            'zone_name' => $request->zone_name,
            'zone_code' => $zoneCode,
            'zone_type' => $request->zone_type,
            'districts' => $request->districts ?? [],
            'municipalities' => $request->municipalities ?? [],
            'wards' => $request->wards ?? [],
            'postal_codes' => $request->postal_codes ?? [],
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('admin.domestic.zones')
            ->with('success', 'Delivery zone created successfully! Code: ' . $zoneCode);
    }

    public function edit($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $partners = User::where('user_type', 'partner')->get();
        $zoneTypes = ['urban', 'semi_urban', 'rural', 'hilly', 'himalayan'];
        
        return view('admin.domestic.zones.edit', compact('zone', 'partners', 'zoneTypes'));
    }

    public function update(Request $request, $id)
    {
        $zone = DeliveryZone::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|exists:users,id',
            'zone_name' => 'required|string|max:255',
            'zone_type' => 'required|in:urban,semi_urban,rural,hilly,himalayan',
            'districts' => 'nullable|array',
            'municipalities' => 'nullable|array',
            'wards' => 'nullable|array',
            'postal_codes' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $zone->update([
            'partner_id' => $request->partner_id,
            'zone_name' => $request->zone_name,
            'zone_type' => $request->zone_type,
            'districts' => $request->districts ?? [],
            'municipalities' => $request->municipalities ?? [],
            'wards' => $request->wards ?? [],
            'postal_codes' => $request->postal_codes ?? [],
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.domestic.zones')
            ->with('success', 'Delivery zone updated successfully!');
    }

    public function destroy($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $zone->delete();

        return redirect()->route('admin.domestic.zones')
            ->with('success', 'Delivery zone deleted successfully!');
    }
}