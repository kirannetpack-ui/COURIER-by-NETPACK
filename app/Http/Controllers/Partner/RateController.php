<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Models\User;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getPartnerId()
    {
        $user = Auth::user();
        if ($user->user_type !== 'partner') {
            abort(403, 'Unauthorized access. Partner only area.');
        }
        return $user->id;
    }

    private function getPartner()
    {
        return Auth::user();
    }

    /**
     * Display all rates for the partner's zones
     */
    public function index()
    {
        $partnerId = $this->getPartnerId();
        $partner = $this->getPartner();
        
        $zones = DeliveryZone::where('partner_id', $partnerId)
            ->orderBy('zone_name')
            ->get();
        
        $services = [
            'flash' => [
                'label' => 'FLASH',
                'icon' => 'fa-bolt',
                'color' => 'red',
                'active' => $partner->flash_active ?? false,
                'fields' => ['base_rate', 'per_kg_rate', 'estimated_hours']
            ],
            'same_day' => [
                'label' => 'SAME DAY',
                'icon' => 'fa-clock',
                'color' => 'orange',
                'active' => $partner->same_day_active ?? false,
                'fields' => ['base_rate', 'per_kg_rate', 'estimated_hours']
            ],
            'standard' => [
                'label' => 'STANDARD',
                'icon' => 'fa-truck',
                'color' => 'green',
                'active' => true,
                'fields' => ['base_rate', 'per_kg_rate', 'estimated_hours']
            ],
            'himalayan' => [
                'label' => 'HIMALAYAN',
                'icon' => 'fa-mountain',
                'color' => 'purple',
                'active' => $partner->himalayan_active ?? false,
                'fields' => ['base_rate', 'per_kg_rate', 'estimated_hours']
            ],
        ];
        
        return view('partner.rates.index', compact('zones', 'services', 'partner'));
    }

    /**
     * Show the form for editing rates for a specific zone
     */
    public function edit($id)
    {
        $partnerId = $this->getPartnerId();
        $partner = $this->getPartner();
        
        $zone = DeliveryZone::where('partner_id', $partnerId)->findOrFail($id);
        
        $services = [
            'flash' => [
                'label' => 'FLASH',
                'icon' => 'fa-bolt',
                'color' => 'red',
                'active' => $partner->flash_active ?? false,
                'rates' => $zone->getServiceRates('flash'),
            ],
            'same_day' => [
                'label' => 'SAME DAY',
                'icon' => 'fa-clock',
                'color' => 'orange',
                'active' => $partner->same_day_active ?? false,
                'rates' => $zone->getServiceRates('same_day'),
            ],
            'standard' => [
                'label' => 'STANDARD',
                'icon' => 'fa-truck',
                'color' => 'green',
                'active' => true,
                'rates' => $zone->getServiceRates('standard'),
            ],
            'himalayan' => [
                'label' => 'HIMALAYAN',
                'icon' => 'fa-mountain',
                'color' => 'purple',
                'active' => $partner->himalayan_active ?? false,
                'rates' => $zone->getServiceRates('himalayan'),
            ],
        ];
        
        return view('partner.rates.edit', compact('zone', 'services', 'partner'));
    }

    /**
     * Update rates for a specific zone
     */
    public function update(Request $request, $id)
    {
        $partnerId = $this->getPartnerId();
        
        $zone = DeliveryZone::where('partner_id', $partnerId)->findOrFail($id);

        $request->validate([
            'flash_base_rate' => 'nullable|numeric|min:0',
            'flash_per_kg_rate' => 'nullable|numeric|min:0',
            'flash_estimated_hours' => 'nullable|integer|min:0',
            'same_day_base_rate' => 'nullable|numeric|min:0',
            'same_day_per_kg_rate' => 'nullable|numeric|min:0',
            'same_day_estimated_hours' => 'nullable|integer|min:0',
            'standard_base_rate' => 'nullable|numeric|min:0',
            'standard_per_kg_rate' => 'nullable|numeric|min:0',
            'standard_estimated_hours' => 'nullable|integer|min:0',
            'himalayan_base_rate' => 'nullable|numeric|min:0',
            'himalayan_per_kg_rate' => 'nullable|numeric|min:0',
            'himalayan_estimated_hours' => 'nullable|integer|min:0',
        ]);

        // Check for rate changes before updating
        $rateChanges = $this->getRateChanges($zone, $request);
        
        $updateData = [
            'flash_base_rate' => $request->flash_base_rate ?? 0,
            'flash_per_kg_rate' => $request->flash_per_kg_rate ?? 0,
            'flash_estimated_hours' => $request->flash_estimated_hours ?? null,
            'same_day_base_rate' => $request->same_day_base_rate ?? 0,
            'same_day_per_kg_rate' => $request->same_day_per_kg_rate ?? 0,
            'same_day_estimated_hours' => $request->same_day_estimated_hours ?? null,
            'standard_base_rate' => $request->standard_base_rate ?? 0,
            'standard_per_kg_rate' => $request->standard_per_kg_rate ?? 0,
            'standard_estimated_hours' => $request->standard_estimated_hours ?? null,
            'himalayan_base_rate' => $request->himalayan_base_rate ?? 0,
            'himalayan_per_kg_rate' => $request->himalayan_per_kg_rate ?? 0,
            'himalayan_estimated_hours' => $request->himalayan_estimated_hours ?? null,
        ];

        $zone->update($updateData);

        // Notify admins if rates changed
        if (!empty($rateChanges)) {
            $this->notifyAdminAboutRateChange($zone, $rateChanges);
        }

        return redirect()->route('partner.rates.index')
            ->with('success', 'Rates updated successfully! Changes have been notified to admins.');
    }

    /**
     * Get rate changes between old and new values
     */
    private function getRateChanges($zone, $request)
    {
        $rateFields = [
            'flash_base_rate' => 'Flash Base Rate',
            'flash_per_kg_rate' => 'Flash Per KG Rate',
            'flash_estimated_hours' => 'Flash Estimated Hours',
            'same_day_base_rate' => 'Same Day Base Rate',
            'same_day_per_kg_rate' => 'Same Day Per KG Rate',
            'same_day_estimated_hours' => 'Same Day Estimated Hours',
            'standard_base_rate' => 'Standard Base Rate',
            'standard_per_kg_rate' => 'Standard Per KG Rate',
            'standard_estimated_hours' => 'Standard Estimated Hours',
            'himalayan_base_rate' => 'Himalayan Base Rate',
            'himalayan_per_kg_rate' => 'Himalayan Per KG Rate',
            'himalayan_estimated_hours' => 'Himalayan Estimated Hours',
        ];
        
        $changes = [];
        
        foreach ($rateFields as $field => $label) {
            $oldValue = $zone->$field;
            $newValue = $request->$field;
            
            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'label' => $label,
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }
        
        return $changes;
    }

    /**
     * Notify admins about rate changes
     */
    private function notifyAdminAboutRateChange($zone, $changes)
    {
        $admins = User::whereIn('user_type', ['admin', 'super_admin', 'domestic_admin'])->get();
        
        $partner = $this->getPartner();
        $message = "📋 RATE CHANGE NOTIFICATION\n\n";
        $message .= "Partner: {$partner->name} ({$partner->email})\n";
        $message .= "Zone: {$zone->zone_name}\n";
        $message .= "Zone Code: {$zone->zone_code}\n\n";
        $message .= "Changes made:\n";
        
        foreach ($changes as $field => $change) {
            $message .= "  • {$change['label']}: {$change['old']} → {$change['new']}\n";
        }
        
        $message .= "\nTime: " . now()->format('Y-m-d H:i:s');
        
        foreach ($admins as $admin) {
            ReminderLog::create([
                'pickup_request_id' => null,
                'reminder_id' => null,
                'reminder_type' => 'admin_alert',
                'sent_to' => $admin->email,
                'message' => $message,
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => [
                    'zone_id' => $zone->id,
                    'partner_id' => $zone->partner_id,
                    'partner_name' => $partner->name,
                    'zone_name' => $zone->zone_name,
                    'changes' => $changes,
                ]
            ]);
        }
    }
}