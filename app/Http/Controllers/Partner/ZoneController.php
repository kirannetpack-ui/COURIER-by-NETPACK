<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Models\User;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
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
     * Display partner's zones
     */
    public function index()
    {
        $partnerId = $this->getPartnerId();
        $partner = $this->getPartner();
        
        $zones = DeliveryZone::where('partner_id', $partnerId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('partner.zones.index', compact('zones', 'partner'));
    }

    /**
     * Show form to create a new zone
     */
    public function create()
{
    $partner = $this->getPartner();
    
    // Check if partner has a district set
    if (!$partner->district) {
        return redirect()->route('partner.zones.index')
            ->with('error', 'Please contact admin to set your operating district first.');
    }
    
    $services = [
        'flash' => $partner->flash_active ?? false,
        'same_day' => $partner->same_day_active ?? false,
        'standard' => $partner->standard_active ?? true,
        'himalayan' => $partner->himalayan_active ?? false,
    ];
    
    return view('partner.zones.create', compact('services', 'partner'));
}


    /**
     * Store a new partner zone
     */
    public function store(Request $request)
{
    $partnerId = $this->getPartnerId();
    $partner = $this->getPartner();

    // Partner can only create zones in their district
    if (!$partner->district) {
        return redirect()->route('partner.zones.index')
            ->with('error', 'Please contact admin to set your operating district first.');
    }

    $request->validate([
        'zone_name' => 'required|string|max:255|unique:delivery_zones,zone_name,NULL,id,partner_id,' . $partnerId,
        'municipalities' => 'nullable|string',
        'wards' => 'nullable|string',
        'description' => 'nullable|string',
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

    $zoneCode = strtoupper(substr($request->zone_name, 0, 3)) . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Partner's zone uses their district
    $zone = DeliveryZone::create([
        'partner_id' => $partnerId,
        'zone_name' => $request->zone_name,
        'zone_code' => $zoneCode,
        'zone_type' => 'partner', // Fixed type for partner zones
        'districts' => [$partner->district], // Only partner's district
        'municipalities' => $request->municipalities ? explode(',', $request->municipalities) : [],
        'wards' => $request->wards ? explode(',', $request->wards) : [],
        'description' => $request->description,
        'is_active' => true,
        'approval_status' => 'pending',
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
    ]);

    // Notify admins about new zone
    $this->notifyAdminAboutNewZone($zone);

    return redirect()->route('partner.zones.index')
        ->with('success', 'Zone created successfully! Admin has been notified for approval.');
}

    /**
     * Show zone details
     */
    public function show($id)
    {
        $partnerId = $this->getPartnerId();
        $partner = $this->getPartner();
        
        $zone = DeliveryZone::where('partner_id', $partnerId)->findOrFail($id);
        
        $services = [
            'flash' => [
                'active' => $partner->flash_active ?? false,
                'rates' => $zone->getServiceRates('flash'),
                'label' => 'FLASH',
            ],
            'same_day' => [
                'active' => $partner->same_day_active ?? false,
                'rates' => $zone->getServiceRates('same_day'),
                'label' => 'SAME DAY',
            ],
            'standard' => [
                'active' => $partner->standard_active ?? true,
                'rates' => $zone->getServiceRates('standard'),
                'label' => 'STANDARD',
            ],
            'himalayan' => [
                'active' => $partner->himalayan_active ?? false,
                'rates' => $zone->getServiceRates('himalayan'),
                'label' => 'HIMALAYAN',
            ],
        ];
        
        return view('partner.zones.show', compact('zone', 'services', 'partner'));
    }

    /**
     * Show edit form for zone
     */
    public function edit($id)
    {
        $partnerId = $this->getPartnerId();
        $partner = $this->getPartner();
        
        $zone = DeliveryZone::where('partner_id', $partnerId)->findOrFail($id);
        $districts = $this->getNepalDistricts();
        
        $services = [
            'flash' => $partner->flash_active ?? false,
            'same_day' => $partner->same_day_active ?? false,
            'standard' => $partner->standard_active ?? true,
            'himalayan' => $partner->himalayan_active ?? false,
        ];
        
        return view('partner.zones.edit', compact('zone', 'districts', 'services', 'partner'));
    }

    /**
     * Update zone
     */
    public function update(Request $request, $id)
    {
        $partnerId = $this->getPartnerId();
        
        $zone = DeliveryZone::where('partner_id', $partnerId)->findOrFail($id);

        $request->validate([
            'zone_name' => 'required|string|max:255|unique:delivery_zones,zone_name,' . $id . ',id,partner_id,' . $partnerId,
            'zone_type' => 'required|string|in:urban,semi_urban,rural,hilly,himalayan',
            'districts' => 'required|array|min:1',
            'districts.*' => 'string',
            'municipalities' => 'nullable|string',
            'wards' => 'nullable|string',
            'description' => 'nullable|string',
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

        // Check for changes
        $rateChanges = $this->getRateChanges($zone, $request);
        $zoneChanges = $this->getZoneChanges($zone, $request);
        
        $updateData = [
            'zone_name' => $request->zone_name,
            'zone_type' => $request->zone_type,
            'districts' => $request->districts,
            'municipalities' => $request->municipalities ? explode(',', $request->municipalities) : [],
            'wards' => $request->wards ? explode(',', $request->wards) : [],
            'description' => $request->description,
            'approval_status' => 'pending', // Reset to pending for admin review
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

        // Notify admins about changes
        if (!empty($rateChanges) || !empty($zoneChanges)) {
            $this->notifyAdminAboutZoneUpdate($zone, $rateChanges, $zoneChanges);
        }

        return redirect()->route('partner.zones.index')
            ->with('success', 'Zone updated successfully! Admin has been notified for review.');
    }

    /**
     * Delete zone
     */
    public function destroy($id)
    {
        $partnerId = $this->getPartnerId();
        
        $zone = DeliveryZone::where('partner_id', $partnerId)->findOrFail($id);
        
        // Check if zone has shipments
        $shipmentCount = \App\Models\DomesticShipment::where('origin_zone_id', $id)
            ->orWhere('destination_zone_id', $id)
            ->count();
            
        if ($shipmentCount > 0) {
            return redirect()->route('partner.zones.index')
                ->with('error', 'Cannot delete zone. It is being used by ' . $shipmentCount . ' shipments.');
        }
        
        $zoneData = [
            'zone_name' => $zone->zone_name,
            'zone_code' => $zone->zone_code,
            'districts' => $zone->districts,
        ];
        
        $zone->delete();

        // Notify admins about deletion
        $this->notifyAdminAboutZoneDeletion($zoneData);

        return redirect()->route('partner.zones.index')
            ->with('success', 'Zone deleted successfully! Admin has been notified.');
    }

    /**
     * Get rate changes
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
     * Get zone info changes
     */
    private function getZoneChanges($zone, $request)
    {
        $changes = [];
        
        if ($zone->zone_name != $request->zone_name) {
            $changes['zone_name'] = [
                'label' => 'Zone Name',
                'old' => $zone->zone_name,
                'new' => $request->zone_name
            ];
        }
        
        if ($zone->zone_type != $request->zone_type) {
            $changes['zone_type'] = [
                'label' => 'Zone Type',
                'old' => $zone->zone_type,
                'new' => $request->zone_type
            ];
        }
        
        if ($zone->districts != $request->districts) {
            $changes['districts'] = [
                'label' => 'Districts',
                'old' => implode(', ', $zone->districts ?? []),
                'new' => implode(', ', $request->districts ?? [])
            ];
        }
        
        return $changes;
    }

    /**
     * Notify admins about new zone
     */
    private function notifyAdminAboutNewZone($zone)
    {
        $admins = User::whereIn('user_type', ['admin', 'super_admin', 'domestic_admin'])->get();
        $partner = $this->getPartner();
        
        $message = "🆕 NEW PARTNER ZONE CREATED\n\n";
        $message .= "Partner: {$partner->name} ({$partner->email})\n";
        $message .= "Zone Name: {$zone->zone_name}\n";
        $message .= "Zone Code: {$zone->zone_code}\n";
        $message .= "Zone Type: {$zone->zone_type}\n";
        $message .= "Districts: " . implode(', ', $zone->districts ?? []) . "\n";
        $message .= "Status: Pending Approval\n\n";
        $message .= "Rates set:\n";
        $message .= "  • Flash Base: Rs. {$zone->flash_base_rate}\n";
        $message .= "  • Same Day Base: Rs. {$zone->same_day_base_rate}\n";
        $message .= "  • Standard Base: Rs. {$zone->standard_base_rate}\n";
        $message .= "  • Himalayan Base: Rs. {$zone->himalayan_base_rate}\n\n";
        $message .= "Created at: " . now()->format('Y-m-d H:i:s');
        
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
                    'action' => 'created',
                ]
            ]);
        }
    }

    /**
     * Notify admins about zone update
     */
    private function notifyAdminAboutZoneUpdate($zone, $rateChanges, $zoneChanges)
    {
        $admins = User::whereIn('user_type', ['admin', 'super_admin', 'domestic_admin'])->get();
        $partner = $this->getPartner();
        
        $message = "✏️ PARTNER ZONE UPDATED\n\n";
        $message .= "Partner: {$partner->name} ({$partner->email})\n";
        $message .= "Zone: {$zone->zone_name} ({$zone->zone_code})\n";
        $message .= "Status: Pending Review\n\n";
        
        if (!empty($zoneChanges)) {
            $message .= "Zone Info Changes:\n";
            foreach ($zoneChanges as $change) {
                $message .= "  • {$change['label']}: {$change['old']} → {$change['new']}\n";
            }
            $message .= "\n";
        }
        
        if (!empty($rateChanges)) {
            $message .= "Rate Changes:\n";
            foreach ($rateChanges as $change) {
                $message .= "  • {$change['label']}: {$change['old']} → {$change['new']}\n";
            }
            $message .= "\n";
        }
        
        $message .= "Updated at: " . now()->format('Y-m-d H:i:s');
        
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
                    'action' => 'updated',
                    'rate_changes' => $rateChanges,
                    'zone_changes' => $zoneChanges,
                ]
            ]);
        }
    }

    /**
     * Notify admins about zone deletion
     */
    private function notifyAdminAboutZoneDeletion($zoneData)
    {
        $admins = User::whereIn('user_type', ['admin', 'super_admin', 'domestic_admin'])->get();
        $partner = $this->getPartner();
        
        $message = "🗑️ PARTNER ZONE DELETED\n\n";
        $message .= "Partner: {$partner->name} ({$partner->email})\n";
        $message .= "Zone Name: {$zoneData['zone_name']}\n";
        $message .= "Zone Code: {$zoneData['zone_code']}\n";
        $message .= "Districts: " . implode(', ', $zoneData['districts'] ?? []) . "\n\n";
        $message .= "Deleted at: " . now()->format('Y-m-d H:i:s');
        
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
                    'partner_id' => $partner->id,
                    'partner_name' => $partner->name,
                    'zone_name' => $zoneData['zone_name'],
                    'action' => 'deleted',
                ]
            ]);
        }
    }

    /**
     * Get Nepal districts
     */
    private function getNepalDistricts()
    {
        return [
            'Kathmandu', 'Lalitpur', 'Bhaktapur', 'Pokhara', 'Lumbini',
            'Chitwan', 'Butwal', 'Biratnagar', 'Birgunj', 'Dharan',
            'Janakpur', 'Hetauda', 'Nepalgunj', 'Birendranagar', 'Tulsipur',
            'Kapilvastu', 'Rupandehi', 'Nawalparasi', 'Parsa', 'Bara',
            'Sarlahi', 'Dhanusha', 'Mahottari', 'Saptari', 'Siraha',
            'Udayapur', 'Sunsari', 'Morang', 'Jhapa', 'Ilam',
            'Panchthar', 'Taplejung', 'Sankhuwasabha', 'Tehrathum', 'Dhankuta',
            'Bhojpur', 'Khotang', 'Solukhumbu', 'Okhaldhunga', 'Ramechhap',
            'Dolakha', 'Sindhuli', 'Sindhupalchok', 'Rasuwa', 'Dhading',
            'Nuwakot', 'Gorkha', 'Lamjung', 'Tanahun', 'Kaski',
            'Parbat', 'Syangja', 'Palpa', 'Gulmi', 'Arghakhanchi',
            'Rukum', 'Rolpa', 'Pyuthan', 'Dang', 'Banke',
            'Bardiya', 'Doti', 'Achham', 'Bajhang', 'Bajura',
            'Kanchanpur', 'Kailali', 'Dadeldhura', 'Baitadi', 'Darchula',
            'Humla', 'Jumla', 'Kalikot', 'Mugu', 'Dolpa',
            'Mustang', 'Manang', 'Myagdi', 'Baglung', 'Jajarkot',
            'Salyan', 'Surkhet', 'Dailekh', 'Jajarkot'
        ];
    }
}