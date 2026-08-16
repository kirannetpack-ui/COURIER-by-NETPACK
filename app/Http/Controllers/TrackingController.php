<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\DomesticShipment;
use App\Models\TrackingLocation;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['updateLocation', 'updateStatus']);
    }

    /**
     * Show tracking lookup page
     */
    public function lookup()
    {
        return view('tracking.lookup');
    }

    /**
     * Search for tracking number
     */
    public function search(Request $request)
    {
        $trackingNumber = $request->get('tracking');
        
        if ($trackingNumber) {
            return redirect()->route('tracking.show', $trackingNumber);
        }
        
        return redirect()->route('tracking.page');
    }

    /**
     * Show tracking details for a shipment
     */
    public function show($trackingNumber)
    {
        // Try to find in Shipment table first
        $trackingNumber = trim($trackingNumber);
        $shipment = Shipment::where('tracking_number', $trackingNumber)->first();

        if (!$shipment) {
            // Try domestic shipments
            $domesticShipment = DomesticShipment::where('tracking_number', $trackingNumber)->first();
            if ($domesticShipment) {
                return view('tracking.domestic', ['shipment' => $domesticShipment]);
            }
            
            // Not found
            return view('tracking.not-found', ['trackingNumber' => $trackingNumber]);
        }

        // Add tracking history if empty
        if (!$shipment->tracking_history) {
            $shipment->tracking_history = [
                [
                    'status' => $shipment->status,
                    'status_label' => $this->getStatusLabel($shipment->status),
                    'description' => $this->getStatusDescription($shipment->status),
                    'time' => $shipment->created_at->toDateTimeString(),
                    'location' => $shipment->sender_city ?? 'Nepal',
                ]
            ];
        }

        return view('tracking.show', compact('shipment'));
    }

    /**
     * Get live location of a shipment
     */
    public function getLiveLocation($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $this->authorizeTrackingView(request()->user(), $shipment);
        
        $location = TrackingLocation::where('shipment_id', $shipmentId)
            ->orderBy('recorded_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $shipment->status,
                'status_label' => $this->getStatusLabel($shipment->status),
                'location' => $location ? $location->location_name : ($shipment->current_location ?? null),
                'latitude' => $location ? $location->latitude : ($shipment->current_latitude ?? null),
                'longitude' => $location ? $location->longitude : ($shipment->current_longitude ?? null),
                'last_updated' => $location ? $location->recorded_at->toDateTimeString() : $shipment->updated_at->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Update shipment location (Rider/Staff)
     */
    public function updateLocation(Request $request, $shipmentId)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,confirmed,processing,picked_up,in_transit,customs_clearance,out_for_delivery,delivered,failed_delivery,returned,cancelled',
        ]);

        $shipment = Shipment::findOrFail($shipmentId);
        $this->authorizeLocationUpdate($request->user(), $shipment);
        
        // Create tracking location
        $location = TrackingLocation::create([
            'shipment_id' => $shipment->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name ?? 'En route',
            'status' => $request->status ?? $shipment->status,
            'recorded_at' => now()
        ]);
        
        // Update shipment current location
        $shipment->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
            'current_location' => $request->location_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => $location
        ]);
    }

    /**
     * Update shipment status (Rider/Staff)
     */
public function updateStatus(Request $request, $shipmentId)
{
    // 1. Validate input
    $request->validate([
        'status' => 'required|string|in:pending,confirmed,processing,picked_up,in_transit,customs_clearance,out_for_delivery,delivered,failed_delivery,returned,cancelled',
        'description' => 'nullable|string',
        'location' => 'nullable|string',
    ]);

    // 2. Find the shipment
    $shipment = Shipment::findOrFail($shipmentId);
    
    // 3. Get current user who is updating
    $user = auth()->user();
    
    // 4. Check if user has permission based on role and shipment type
    $this->authorizeStatusUpdate($user, $shipment, $request->status);
    
    $this->assertValidStatusTransition($shipment->status, $request->status);

    // 5. Update the status
    $oldStatus = $shipment->status;
    $newStatus = $request->status;
    $shipment->status = $newStatus;
    $shipment->save();

    // 6. Add tracking event
    $this->addTrackingEvent($shipment, $newStatus, $request->location, $request->description, $user);
    
    return response()->json(['success' => true]);
}


    /**
     * Get tracking history with pagination
     */
    public function getTrackingHistory($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        
        $history = $shipment->tracking_history ?? [];
        
        return response()->json([
            'success' => true,
            'data' => array_reverse($history),
        ]);
    }

    /**
     * Get status label
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Order Placed',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'customs_clearance' => 'Customs Clearance',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'failed_delivery' => 'Delivery Failed',
            'returned' => 'Returned',
            'cancelled' => 'Cancelled',
        ];
        
        return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Get status description
     */
    private function getStatusDescription($status)
    {
        $descriptions = [
            'pending' => 'Your order has been placed and is being processed',
            'confirmed' => 'Your shipment has been confirmed',
            'processing' => 'Your shipment is being prepared',
            'picked_up' => 'Your shipment has been picked up by the courier',
            'in_transit' => 'Your shipment is on its way to the destination',
            'customs_clearance' => 'Your shipment is going through customs clearance',
            'out_for_delivery' => 'Your shipment is out for delivery',
            'delivered' => 'Your shipment has been successfully delivered',
            'failed_delivery' => 'Delivery attempt was unsuccessful',
            'returned' => 'Your shipment is being returned to sender',
            'cancelled' => 'Your shipment has been cancelled',
        ];
        
        return $descriptions[$status] ?? 'Status updated';
    }

private function authorizeStatusUpdate($user, $shipment, $newStatus)
{
    // Super Admin and Admin can update any status
    if ($user->user_type === 'super_admin' || $user->user_type === 'admin') {
        return true;
    }
    
    // Staff can update specific statuses
    if ($user->user_type === 'staff') {
        $allowedStatuses = ['confirmed', 'processing', 'picked_up', 'in_transit', 'customs_clearance'];
        if (!in_array($newStatus, $allowedStatuses)) {
            abort(403, 'Staff cannot update this status');
        }
        return true;
    }
    
    // Rider can update specific statuses
    if ($user->user_type === 'rider') {
        // Check if rider is assigned to this shipment
        if ($shipment->rider_id !== $user->id) {
            abort(403, 'You are not assigned to this shipment');
        }
        
        $allowedStatuses = ['picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed_delivery'];
        if (!in_array($newStatus, $allowedStatuses)) {
            abort(403, 'Rider cannot update this status');
        }
        return true;
    }
    
    // Partner can update specific statuses
    if ($user->user_type === 'partner' || $user->user_type === 'overseas') {
        // Check if partner is associated with this shipment
        $isAssociated = $this->isPartnerAssociated($user, $shipment);
        if (!$isAssociated) {
            abort(403, 'You are not associated with this shipment');
        }
        
        $allowedStatuses = ['picked_up', 'in_transit', 'customs_clearance', 'out_for_delivery'];
        if (!in_array($newStatus, $allowedStatuses)) {
            abort(403, 'Partner cannot update this status');
        }
        return true;
    }
    
    abort(403, 'You are not authorized to update this shipment');
}

private function authorizeTrackingView($user, Shipment $shipment): void
{
    if (in_array($user->user_type, ['super_admin', 'admin', 'staff'], true)) {
        return;
    }

    $ownerIds = array_map('intval', array_filter([
        $shipment->customer_id,
        $shipment->seller_id,
        $shipment->rider_id,
        $shipment->overseas_partner_id,
    ]));

    if (in_array((int) $user->id, $ownerIds, true)) {
        return;
    }

    abort(403, 'You are not authorized to view this live location.');
}

private function authorizeLocationUpdate($user, Shipment $shipment): void
{
    if (in_array($user->user_type, ['super_admin', 'admin', 'staff'], true)) {
        return;
    }

    if ($user->user_type === 'rider' && (int) $shipment->rider_id === (int) $user->id) {
        return;
    }

    if ($this->isPartnerAssociated($user, $shipment)) {
        return;
    }

    abort(403, 'You are not authorized to update this shipment location.');
}

private function isPartnerAssociated($user, Shipment $shipment): bool
{
    return $user->user_type === 'overseas'
        && (int) $shipment->overseas_partner_id === (int) $user->id;
}

private function addTrackingEvent(Shipment $shipment, string $status, ?string $location, ?string $description, $user): void
{
    $history = $shipment->tracking_history ?? [];
    $history[] = [
        'status' => $status,
        'status_label' => $this->getStatusLabel($status),
        'description' => $description ?: $this->getStatusDescription($status),
        'location' => $location,
        'time' => now()->toIso8601String(),
        'recorded_by' => $user->id,
        'recorded_by_role' => $user->user_type,
    ];

    $shipment->forceFill(['tracking_history' => $history])->save();
}

private function assertValidStatusTransition(string $currentStatus, string $newStatus): void
{
    if ($currentStatus === $newStatus) {
        return;
    }

    $transitions = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'picked_up', 'cancelled'],
        'processing' => ['picked_up', 'in_transit', 'cancelled'],
        'picked_up' => ['in_transit', 'failed_delivery'],
        'in_transit' => ['customs_clearance', 'out_for_delivery', 'failed_delivery', 'returned'],
        'customs_clearance' => ['in_transit', 'out_for_delivery', 'returned'],
        'out_for_delivery' => ['delivered', 'failed_delivery', 'returned'],
        'failed_delivery' => ['out_for_delivery', 'returned'],
        'delivered' => [],
        'returned' => [],
        'cancelled' => [],
    ];

    if (!in_array($newStatus, $transitions[$currentStatus] ?? [], true)) {
        abort(422, "Invalid shipment status transition from {$currentStatus} to {$newStatus}.");
    }
}

}
