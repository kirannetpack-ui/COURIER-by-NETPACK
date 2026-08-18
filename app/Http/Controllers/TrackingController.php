<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\DomesticShipment;
use App\Models\Order;
use App\Models\TrackingLocation;
use App\Services\ShipmentScanService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct(private readonly ShipmentScanService $scanService)
    {
        $this->middleware('auth')->only(['getLiveLocation', 'getOrderLiveLocation', 'updateLocation', 'updateStatus']);
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

            // E-commerce and rider deliveries share the same public lookup,
            // but exact rider coordinates are only exposed to authorized users.
            $order = Order::where('tracking_number', $trackingNumber)->with('rider')->first();
            if ($order) {
                $canViewLive = $this->canViewOrderLiveLocation(request()->user(), $order);

                return view('tracking.order', compact('order', 'canViewLive'));
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

        return view('tracking.public', compact('shipment'));
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
     * Return the latest real rider GPS point for an e-commerce order.
     */
    public function getOrderLiveLocation(Order $order)
    {
        abort_unless($this->canViewOrderLiveLocation(request()->user(), $order), 403,
            'You are not authorized to view this rider location.');

        $location = $order->trackingLocations()->latest('timestamp')->first();
        $lastUpdated = $location?->timestamp ?? $order->rider?->last_location_update;
        $latitude = $location?->latitude ?? $order->rider?->current_latitude;
        $longitude = $location?->longitude ?? $order->rider?->current_longitude;
        $staleAfter = config('tracking.live.stale_after_seconds', 120);

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $order->status,
                'status_label' => $order->status_label,
                'latitude' => $latitude !== null ? (float) $latitude : null,
                'longitude' => $longitude !== null ? (float) $longitude : null,
                'accuracy' => $location?->accuracy !== null ? (float) $location->accuracy : null,
                'speed' => $location?->speed !== null ? (float) $location->speed : null,
                'bearing' => $location?->bearing !== null ? (float) $location->bearing : null,
                'last_updated' => $lastUpdated?->toIso8601String(),
                'is_stale' => !$lastUpdated || $lastUpdated->diffInSeconds(now()) > $staleAfter,
                'delivery' => [
                    'latitude' => $order->delivery_latitude !== null ? (float) $order->delivery_latitude : null,
                    'longitude' => $order->delivery_longitude !== null ? (float) $order->delivery_longitude : null,
                ],
            ],
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
    
    $eventCode = $this->scanService->eventCodeForStatus($request->status);
    abort_unless($eventCode, 422, 'No operational scan event is configured for this status.');

    $this->scanService->record(
        $shipment,
        $eventCode,
        $request->location,
        $request->description,
        $user,
        'admin_status_update',
    );
    
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
    if (in_array($user->user_type, ['super_admin', 'admin', 'domestic_admin', 'international_admin'], true)) {
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
    if (in_array($user->user_type, ['super_admin', 'admin', 'staff', 'domestic_admin', 'international_admin'], true)) {
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

private function canViewOrderLiveLocation($user, Order $order): bool
{
    if (!$user) {
        return false;
    }

    if (in_array($user->user_type, ['super_admin', 'admin', 'staff', 'domestic_admin'], true)) {
        return true;
    }

    return in_array((int) $user->id, array_filter([
        (int) $order->customer_id,
        (int) $order->client_id,
        (int) $order->seller_id,
        (int) $order->rider_id,
    ]), true);
}

private function authorizeLocationUpdate($user, Shipment $shipment): void
{
    if (in_array($user->user_type, ['super_admin', 'admin', 'staff', 'domestic_admin', 'international_admin'], true)) {
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

}
