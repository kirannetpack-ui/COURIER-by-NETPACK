@extends('layouts.app')

@section('title', 'International Shipment - ' . ($shipment->hawb_number ?? $shipment->tracking_number))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header / Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-teal-100 text-teal-700">
                        <i class="fas fa-plane-departure text-lg"></i>
                    </span>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">
                            International Shipment Details
                        </h1>
                        <p class="text-sm text-gray-500">
                            HAWB: <strong class="font-mono text-teal-700">{{ $shipment->hawb_number ?? 'Not assigned' }}</strong>
                            &nbsp;·&nbsp;
                            Tracking: <strong class="font-mono text-gray-800">{{ $shipment->tracking_number }}</strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('hawb.international', $shipment->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-teal-700 transition shadow-sm">
                    <i class="fas fa-print"></i> Generate / Print HAWB
                </a>
                <a href="{{ route('tracking.show', $shipment->tracking_number) }}" target="_blank"
                   class="inline-flex items-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-900 transition shadow-sm">
                    <i class="fas fa-search-location"></i> Public Tracker
                </a>
                <a href="{{ route('international.shipments') }}"
                   class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fas fa-circle-check text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Specs Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Current Status</p>
            <p class="mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                    {{ $shipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' :
                       (in_array($shipment->status, ['in_transit', 'customs_clearance', 'out_for_delivery']) ? 'bg-blue-100 text-blue-800' :
                       ($shipment->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')) }}">
                    {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                </span>
            </p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Service</p>
            <p class="mt-2 text-sm font-bold text-gray-900">{{ strtoupper($shipment->service_type ?? 'Express') }}</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Chargeable Weight</p>
            <p class="mt-2 text-sm font-bold text-gray-900">{{ number_format($shipment->chargeable_weight ?? $shipment->actual_weight ?? $shipment->weight ?? 0, 2) }} kg</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Package Type</p>
            <p class="mt-2 text-sm font-bold text-gray-900">{{ ucfirst($shipment->package_type ?? 'Parcel') }}</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Destination</p>
            <p class="mt-2 text-sm font-bold text-gray-900">{{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Overseas Partner</p>
            <p class="mt-2 text-sm font-bold text-gray-900">{{ $shipment->overseasPartner->name ?? 'NETPACK Global Hub' }}</p>
        </div>
    </div>

    <!-- Shipper & Consignee Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Shipper / Sender -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-4 border-b pb-3">
                <i class="fas fa-paper-plane text-teal-600"></i>
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wide">Shipper / Sender (Nepal)</h2>
            </div>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-gray-400">Name</dt>
                    <dd class="font-semibold text-gray-900">{{ $shipment->sender_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">Contact Phone</dt>
                    <dd class="font-medium text-gray-800">{{ $shipment->sender_phone ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">Address</dt>
                    <dd class="text-gray-700">{{ $shipment->sender_address ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">Origin Facility</dt>
                    <dd class="text-gray-700">{{ $shipment->sender_city ?? 'Kathmandu' }}, {{ $shipment->sender_country ?? 'Nepal' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Consignee / Receiver -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-4 border-b pb-3">
                <i class="fas fa-location-dot text-blue-600"></i>
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wide">Consignee / Destination Receiver</h2>
            </div>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-gray-400">Recipient Name</dt>
                    <dd class="font-semibold text-gray-900">{{ $shipment->receiver_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">Contact Phone</dt>
                    <dd class="font-medium text-gray-800">{{ $shipment->receiver_phone ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">Delivery Address</dt>
                    <dd class="text-gray-700">{{ $shipment->receiver_address ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">City / State / Postal Code / Country</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $shipment->receiver_city }}, {{ $shipment->receiver_state ?? '' }} {{ $shipment->receiver_postal_code ?? '' }}, {{ $shipment->receiver_country }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Status Update & Operations -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-2 mb-4 border-b pb-3">
            <i class="fas fa-sliders text-teal-600"></i>
            <h2 class="text-base font-bold text-gray-800">Operational Milestone Update</h2>
        </div>
        <form method="POST" action="{{ route('international.shipments.update-status', $shipment->id) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">New Milestone Status *</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
                        <option value="pending" {{ $shipment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $shipment->status === 'confirmed' ? 'selected' : '' }}>Booking Confirmed</option>
                        <option value="processing" {{ $shipment->status === 'processing' ? 'selected' : '' }}>Preparing Shipment</option>
                        <option value="picked_up" {{ $shipment->status === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                        <option value="in_transit" {{ $shipment->status === 'in_transit' ? 'selected' : '' }}>In Transit (Airlift / Flight)</option>
                        <option value="customs_clearance" {{ $shipment->status === 'customs_clearance' ? 'selected' : '' }}>Customs Clearance</option>
                        <option value="out_for_delivery" {{ $shipment->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Delivered Successfully</option>
                        <option value="returned" {{ $shipment->status === 'returned' ? 'selected' : '' }}>Returned to Sender</option>
                        <option value="cancelled" {{ $shipment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Audit Notes / Hub Location</label>
                    <input type="text" name="notes" placeholder="e.g., Cleared export customs at Tribhuvan International Airport (TIA) Hub"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
            <div>
                <button type="submit" class="bg-teal-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-teal-700 transition">
                    <i class="fas fa-check mr-1"></i> Record Milestone Update
                </button>
            </div>
        </form>
    </div>

    <!-- Tracking History -->
    @if(!empty($shipment->tracking_history))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-teal-600"></i> Audit History & Scan Log
            </h2>
            <div class="space-y-4">
                @foreach(array_reverse($shipment->tracking_history) as $event)
                    <div class="flex items-start gap-4 p-3 rounded-lg bg-gray-50 border border-gray-100 text-sm">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-teal-100 text-teal-700 text-xs">
                            <i class="fas fa-barcode"></i>
                        </span>
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <span class="font-bold text-gray-900">{{ $event['status_label'] ?? ucfirst(str_replace('_', ' ', $event['status'] ?? 'Updated')) }}</span>
                                @if(!empty($event['time']))
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event['time'])->format('M d, Y · h:i A') }}</span>
                                @endif
                            </div>
                            @if(!empty($event['description']))
                                <p class="text-xs text-gray-600 mt-1">{{ $event['description'] }}</p>
                            @endif
                            @if(!empty($event['location']))
                                <p class="text-xs text-teal-700 font-medium mt-1"><i class="fas fa-location-dot mr-1"></i>{{ $event['location'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
