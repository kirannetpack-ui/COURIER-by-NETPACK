<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DomesticShipment;
use App\Models\LogisticsService;
use App\Models\PickupRequest;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Client Portal Dashboard: High-level overview & live consignment radar.
     */
    public function index()
    {
        $user = Auth::user();

        // Query shipments belonging to this client
        $shipmentsQuery = Shipment::where('customer_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                $q->where('seller_id', $user->id)->whereNull('customer_id');
            });

        $totalShipments = (clone $shipmentsQuery)->count();
        $inTransit = (clone $shipmentsQuery)->whereIn('status', ['in_transit', 'picked_up', 'out_for_delivery', 'departed_hub', 'arrived_at_hub'])->count();
        $delivered = (clone $shipmentsQuery)->where('status', 'delivered')->count();
        $pending = (clone $shipmentsQuery)->whereIn('status', ['pending', 'created', 'confirmed', 'manifested'])->count();

        // Recent consignments
        $recentShipments = (clone $shipmentsQuery)->latest()->take(5)->get();

        // Active ongoing shipments for live radar
        $activeShipments = (clone $shipmentsQuery)
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->latest('updated_at')
            ->get();
        $latestActiveShipment = $activeShipments->first();

        // Total active inquiries / pickup requests
        $totalInquiries = PickupRequest::where('seller_id', $user->id)->count();
        $pendingInquiries = PickupRequest::where('seller_id', $user->id)
            ->whereIn('status', ['pending', 'assigned'])
            ->count();

        return view('client.dashboard', compact(
            'totalShipments',
            'inTransit',
            'delivered',
            'pending',
            'recentShipments',
            'activeShipments',
            'latestActiveShipment',
            'totalInquiries',
            'pendingInquiries'
        ));
    }

    /**
     * Unified Shipment Inquiries & Pickup Booking Desk.
     */
    public function inquiries(Request $request)
    {
        $user = Auth::user();

        $query = PickupRequest::where('seller_id', $user->id);

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('delivery_city', 'LIKE', "%{$search}%")
                    ->orWhere('pickup_address', 'LIKE', "%{$search}%");
            });
        }

        $inquiries = $query->latest()->paginate(12);

        // Dynamic logistics service tiers for quick selection
        $services = LogisticsService::where('is_active', true)
            ->whereIn('category', ['domestic', 'ecommerce'])
            ->orderBy('sort_order')
            ->get();

        $statusCounts = [
            'all' => PickupRequest::where('seller_id', $user->id)->count(),
            'pending' => PickupRequest::where('seller_id', $user->id)->where('status', 'pending')->count(),
            'assigned' => PickupRequest::where('seller_id', $user->id)->where('status', 'assigned')->count(),
            'picked_up' => PickupRequest::where('seller_id', $user->id)->where('status', 'picked_up')->count(),
            'in_transit' => PickupRequest::where('seller_id', $user->id)->where('status', 'in_transit')->count(),
            'delivered' => PickupRequest::where('seller_id', $user->id)->where('status', 'delivered')->count(),
        ];

        return view('client.inquiries', compact('inquiries', 'services', 'statusCounts'));
    }

    /**
     * Store new shipment inquiry / doorstep pickup request.
     */
    public function storeInquiry(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'pickup_address' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:30',
            'destination_scope' => 'required|in:inside_valley,outside_valley,international',
            'delivery_address' => 'required|string|max:255',
            'delivery_city' => 'required|string|max:100',
            'recipient_name' => 'required|string|max:150',
            'recipient_phone' => 'required|string|max:30',
            'package_type' => 'required|string|max:80',
            'estimated_weight_kg' => 'required|numeric|min:0.1|max:1000',
            'service_tier' => 'nullable|string|max:50',
            'scheduled_pickup_time' => 'nullable|date',
            'pickup_slot' => 'nullable|string|max:50',
            'instructions' => 'nullable|string|max:500',
        ]);

        $scheduledTime = !empty($validated['scheduled_pickup_time'])
            ? Carbon::parse($validated['scheduled_pickup_time'])
            : now()->addHours(2);

        $pickup = PickupRequest::create([
            'seller_id' => $user->id,
            'pickup_address' => $validated['pickup_address'],
            'pickup_city' => 'Kathmandu Valley',
            'contact_person_name' => $user->name,
            'contact_person_phone' => $validated['contact_phone'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_city' => $validated['delivery_city'],
            'customer_name' => $validated['recipient_name'],
            'customer_phone' => $validated['recipient_phone'],
            'items_description' => $validated['package_type'] . (!empty($validated['instructions']) ? ' (' . $validated['instructions'] . ')' : ''),
            'estimated_weight_kg' => $validated['estimated_weight_kg'],
            'service_tier' => $validated['service_tier'] ?? 'standard',
            'scheduled_pickup_time' => $scheduledTime,
            'status' => 'pending',
            'status_notes' => 'Shipment inquiry submitted via Client Portal: ' . ucfirst(str_replace('_', ' ', $validated['destination_scope'])),
        ]);

        return redirect()->route('client.inquiries')
            ->with('success', "Shipment inquiry logged successfully! Consignment Reference: {$pickup->tracking_number}. Our dispatch desk will coordinate collection.");
    }

    /**
     * Dedicated Scoped Shipment History & Tracking Portal.
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Shipment::where('customer_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                $q->where('seller_id', $user->id)->whereNull('customer_id');
            });

        // Tab / Status filter
        $filter = $request->get('filter', 'all');
        if ($filter === 'active' || $filter === 'ongoing') {
            $query->whereNotIn('status', ['delivered', 'cancelled', 'returned']);
        } elseif ($filter === 'delivered') {
            $query->where('status', 'delivered');
        } elseif ($filter === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        // Search query
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'LIKE', "%{$search}%")
                    ->orWhere('hawb_number', 'LIKE', "%{$search}%")
                    ->orWhere('receiver_name', 'LIKE', "%{$search}%")
                    ->orWhere('destination', 'LIKE', "%{$search}%")
                    ->orWhere('receiver_city', 'LIKE', "%{$search}%");
            });
        }

        $shipments = $query->latest()->paginate(15);

        // Scoped counts for tabs
        $scopedBase = Shipment::where('customer_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                $q->where('seller_id', $user->id)->whereNull('customer_id');
            });

        $counts = [
            'all' => (clone $scopedBase)->count(),
            'active' => (clone $scopedBase)->whereNotIn('status', ['delivered', 'cancelled', 'returned'])->count(),
            'delivered' => (clone $scopedBase)->where('status', 'delivered')->count(),
            'cancelled' => (clone $scopedBase)->where('status', 'cancelled')->count(),
        ];

        return view('client.history', compact('shipments', 'counts', 'filter'));
    }
}
