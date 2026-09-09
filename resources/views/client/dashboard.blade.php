@extends('layouts.app')

@section('title', 'Client Portal & Dashboard - COURIER with NETPACK')
@section('page-title', 'Client Portal')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 rounded-2xl p-6 text-white shadow-sm border border-teal-900/40">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        Unified Client Portal
                    </span>
                    <span class="text-xs text-slate-400">&bull; Verified Logistics Account</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">
                    <span>Welcome back, {{ Auth::user()->name }}!</span>
                </h1>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Manage pickup requests, calculate rates, track packages in real-time, and view proof-of-delivery receipts across Nepal and international destinations.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-white/10 rounded-xl text-xs font-semibold border border-white/10 flex items-center gap-1.5">
                    <i class="fas fa-shield-halved text-teal-400"></i>
                    <span>Verified Client</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Consignments</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalShipments) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">In Transit</p>
                    <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($inTransit) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fas fa-truck-fast"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Delivered</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($delivered) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Processing</p>
                    <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($pending) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- ACTIVE SHIPMENT LIVE TRACKING RADAR (ALWAYS VISIBLE) -->
    <!-- ============================================================= -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <!-- Section Header -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 px-6 py-4 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-black uppercase tracking-wider text-white">Active Consignment Live Tracking</h2>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                            REAL-TIME RADAR
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 mt-0.5">Live status, movement telemetry, and milestone tracking for ongoing consignments.</p>
                </div>
            </div>

            @if(!empty($latestActiveShipment))
                <a href="{{ route('tracking.show', $latestActiveShipment->tracking_number) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs uppercase tracking-wider transition shadow-sm flex-shrink-0">
                    <i class="fas fa-satellite-dish"></i>
                    <span>Open Full Tracking Page</span>
                    <i class="fas fa-arrow-right text-[11px]"></i>
                </a>
            @else
                <a href="{{ route('tracking.page') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition border border-slate-700 flex-shrink-0">
                    <i class="fas fa-search-location text-teal-400"></i>
                    <span>Public Radar Lookup</span>
                </a>
            @endif
        </div>

        @if(!empty($latestActiveShipment))
            <div class="p-6 space-y-6">
                <!-- Top Consignment Summary Row (Clickable) -->
                <a href="{{ route('tracking.show', $latestActiveShipment->tracking_number) }}" 
                   class="block p-4 rounded-xl bg-slate-50 hover:bg-teal-50/40 border border-slate-200/80 hover:border-teal-500/50 transition group">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span class="font-mono text-base sm:text-lg font-black text-slate-900 group-hover:text-teal-700 flex items-center gap-2">
                                    <i class="fas fa-barcode text-teal-600"></i>
                                    {{ $latestActiveShipment->tracking_number }}
                                </span>
                                @if($latestActiveShipment->hawb_number)
                                    <span class="px-2 py-0.5 rounded bg-slate-200/80 text-slate-700 text-xs font-mono font-semibold">
                                        HAWB: {{ $latestActiveShipment->hawb_number }}
                                    </span>
                                @endif
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-teal-100 text-teal-800 border border-teal-200">
                                    {{ ucwords(str_replace('_', ' ', $latestActiveShipment->service_type ?? 'Standard Express')) }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 flex items-center gap-2">
                                <span class="font-medium text-slate-700">{{ $latestActiveShipment->origin ?? ($latestActiveShipment->sender_city ?? 'Kathmandu Hub') }}</span>
                                <i class="fas fa-arrow-right-long text-teal-600 text-[10px]"></i>
                                <span class="font-bold text-slate-900">{{ $latestActiveShipment->destination ?? ($latestActiveShipment->receiver_city ?? 'Destination') }}</span>
                                @if($latestActiveShipment->receiver_name)
                                    <span class="text-slate-400">&bull; Consignee: {{ $latestActiveShipment->receiver_name }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                @php
                                    $st = strtolower($latestActiveShipment->status ?? 'pending');
                                    $statusBadgeClass = match($st) {
                                        'in_transit' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'out_for_delivery' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'picked_up' => 'bg-teal-100 text-teal-800 border-teal-200',
                                        'manifested', 'created', 'confirmed' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $statusBadgeClass }}">
                                    <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                                    {{ str_replace('_', ' ', $latestActiveShipment->status) }}
                                </span>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Updated {{ $latestActiveShipment->updated_at ? $latestActiveShipment->updated_at->diffForHumans() : 'Recently' }}
                                </p>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center group-hover:scale-110 group-hover:bg-teal-700 transition flex-shrink-0">
                                <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- 5-Step Visual Milestone Stepper -->
                @php
                    $curStatus = strtolower($latestActiveShipment->status ?? 'pending');
                    $stepIndex = 1;
                    if (in_array($curStatus, ['picked_up', 'arrived_at_hub', 'sorted'])) {
                        $stepIndex = 2;
                    } elseif (in_array($curStatus, ['in_transit', 'customs_cleared', 'departed_hub', 'linehaul'])) {
                        $stepIndex = 3;
                    } elseif (in_array($curStatus, ['out_for_delivery', 'with_rider'])) {
                        $stepIndex = 4;
                    } elseif (in_array($curStatus, ['delivered'])) {
                        $stepIndex = 5;
                    }
                @endphp

                <div class="relative px-2">
                    <div class="grid grid-cols-5 gap-2 text-center relative">
                        <!-- Connecting Progress Bar -->
                        <div class="absolute top-4 left-6 right-6 h-1 bg-slate-200 -z-0">
                            <div class="h-1 bg-gradient-to-r from-teal-500 to-blue-600 transition-all duration-500"
                                 style="width: {{ (($stepIndex - 1) / 4) * 100 }}%;"></div>
                        </div>

                        <!-- Step 1: Booked / Manifested -->
                        <div class="flex flex-col items-center z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-xs {{ $stepIndex >= 1 ? 'bg-teal-600 text-white ring-4 ring-teal-100' : 'bg-slate-200 text-slate-500' }}">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <p class="text-[11px] font-bold text-slate-800 mt-2">Booked</p>
                            <span class="text-[9px] text-slate-400 hidden sm:block">Intake Verified</span>
                        </div>

                        <!-- Step 2: Picked Up -->
                        <div class="flex flex-col items-center z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-xs {{ $stepIndex >= 2 ? 'bg-teal-600 text-white ring-4 ring-teal-100' : 'bg-slate-200 text-slate-500' }}">
                                <i class="fas fa-box"></i>
                            </div>
                            <p class="text-[11px] font-bold text-slate-800 mt-2">Picked Up</p>
                            <span class="text-[9px] text-slate-400 hidden sm:block">Origin Facility</span>
                        </div>

                        <!-- Step 3: In Transit -->
                        <div class="flex flex-col items-center z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-xs {{ $stepIndex >= 3 ? 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' : 'bg-slate-200 text-slate-500' }}">
                                <i class="fas fa-truck-fast"></i>
                            </div>
                            <p class="text-[11px] font-bold text-slate-800 mt-2">In Transit</p>
                            <span class="text-[9px] text-slate-400 hidden sm:block">Corridor Movement</span>
                        </div>

                        <!-- Step 4: Out for Delivery -->
                        <div class="flex flex-col items-center z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-xs {{ $stepIndex >= 4 ? 'bg-amber-600 text-white ring-4 ring-amber-100' : 'bg-slate-200 text-slate-500' }}">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <p class="text-[11px] font-bold text-slate-800 mt-2">Out for Delivery</p>
                            <span class="text-[9px] text-slate-400 hidden sm:block">Last-Mile Dispatch</span>
                        </div>

                        <!-- Step 5: Delivered -->
                        <div class="flex flex-col items-center z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-xs {{ $stepIndex >= 5 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-200 text-slate-500' }}">
                                <i class="fas fa-circle-check"></i>
                            </div>
                            <p class="text-[11px] font-bold text-slate-800 mt-2">Delivered</p>
                            <span class="text-[9px] text-slate-400 hidden sm:block">Signed Receipt</span>
                        </div>
                    </div>
                </div>

                <!-- Current Location & Details Box -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-slate-100 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider flex items-center gap-1">
                            <i class="fas fa-location-dot text-teal-600"></i> Current Telemetry Location
                        </span>
                        <p class="font-bold text-slate-900 mt-1 text-xs">
                            {{ $latestActiveShipment->current_location ?? ($latestActiveShipment->origin ?? 'Kathmandu Central Gateway') }}
                        </p>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider flex items-center gap-1">
                            <i class="fas fa-stopwatch text-blue-600"></i> Estimated Delivery Window
                        </span>
                        <p class="font-bold text-slate-900 mt-1 text-xs">
                            {{ $latestActiveShipment->estimated_delivery ? \Carbon\Carbon::parse($latestActiveShipment->estimated_delivery)->format('M d, Y (h:i A)') : 'Standard Corridor (Within 24-48 hrs)' }}
                        </p>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider flex items-center gap-1">
                                <i class="fas fa-scale-balanced text-amber-600"></i> Chargeable Weight
                            </span>
                            <p class="font-bold text-slate-900 mt-1 text-xs font-mono">
                                {{ number_format($latestActiveShipment->chargeable_weight ?: $latestActiveShipment->actual_weight ?: 1.0, 1) }} KG
                            </p>
                        </div>
                        <a href="{{ route('tracking.show', $latestActiveShipment->tracking_number) }}" 
                           class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold text-[11px] transition shadow-xs flex items-center gap-1.5">
                            <span>Inspect &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- If Multiple Active Shipments, List Other Active Ones -->
                @if($activeShipments->count() > 1)
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <i class="fas fa-boxes-stacked text-teal-600"></i> Other Active Shipments Moving ({{ $activeShipments->count() - 1 }} More)
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($activeShipments->skip(1)->take(3) as $other)
                                <a href="{{ route('tracking.show', $other->tracking_number) }}" 
                                   class="p-2.5 rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50/40 transition flex items-center justify-between gap-2 group">
                                    <div class="min-w-0">
                                        <p class="font-mono font-bold text-xs text-slate-900 group-hover:text-teal-700 truncate">
                                            {{ $other->tracking_number }}
                                        </p>
                                        <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                            &rarr; {{ $other->destination ?? 'Destination' }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 group-hover:bg-teal-600 group-hover:text-white transition flex-shrink-0">
                                        {{ str_replace('_', ' ', $other->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Standby Empty State When No Active Consignment is Moving -->
            <div class="p-8 text-center space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-xs border border-emerald-100">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div class="max-w-md mx-auto">
                    <h3 class="text-sm font-bold text-slate-900">All Consignments Up to Date</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        You have no active shipments currently moving in transit. When you book a new parcel pickup or dispatch a package, its real-time radar telemetry will stream here automatically.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    <a href="{{ route('domestic.pickup.create') }}" 
                       class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Book New Pickup</span>
                    </a>
                    <a href="{{ route('shipments.index', ['status' => 'delivered']) }}" 
                       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <i class="fas fa-clock-rotate-left text-blue-600"></i>
                        <span>View Tracking History ({{ $delivered }} Delivered)</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================= -->
    <!-- CLIENT FORM WORKBENCH: EMBEDDED OPERATIONAL FORMS -->
    <!-- ============================================================= -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden" x-data="{ clientTab: 'pickup' }">
        <div class="border-b border-slate-200 bg-slate-50/60 px-5 pt-3 flex flex-wrap gap-2">
            <button type="button" 
                    @click="clientTab = 'pickup'" 
                    :class="clientTab === 'pickup' ? 'border-teal-600 text-teal-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-white/50 font-medium'"
                    class="px-4 py-2.5 text-xs rounded-t-xl border-b-2 transition flex items-center gap-2">
                <i class="fas fa-truck-ramp-box text-teal-600"></i>
                <span>Book Instant Package Pickup</span>
            </button>

            <button type="button" 
                    @click="clientTab = 'track'" 
                    :class="clientTab === 'track' ? 'border-teal-600 text-teal-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-white/50 font-medium'"
                    class="px-4 py-2.5 text-xs rounded-t-xl border-b-2 transition flex items-center gap-2">
                <i class="fas fa-radar text-blue-600"></i>
                <span>Live Consignment Radar</span>
            </button>

            <button type="button" 
                    @click="clientTab = 'quote'" 
                    :class="clientTab === 'quote' ? 'border-teal-600 text-teal-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-white/50 font-medium'"
                    class="px-4 py-2.5 text-xs rounded-t-xl border-b-2 transition flex items-center gap-2">
                <i class="fas fa-calculator text-amber-600"></i>
                <span>Quick Rate Estimator</span>
            </button>
        </div>

        <!-- TAB 1: Instant Package Pickup Request Form -->
        <div x-show="clientTab === 'pickup'" class="p-6 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Request Doorstep Courier Pickup</h3>
                    <p class="text-xs text-slate-500">Our nearest dispatch rider will collect your consignment and issue an on-site receipt.</p>
                </div>
                <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200">
                    Doorstep Service
                </span>
            </div>

            <form action="{{ route('domestic.pickup.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pickup Address & Landmark *</label>
                        <input type="text" name="pickup_address" required 
                               value="{{ Auth::user()->address }}"
                               placeholder="e.g. Ward 4, Baluwatar, Near Prime Minister House" 
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contact Phone *</label>
                        <input type="text" name="phone" required 
                               value="{{ Auth::user()->phone }}"
                               placeholder="e.g. 9841000000" 
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Package Category *</label>
                        <select name="package_type" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                            <option value="document">Legal / Business Documents</option>
                            <option value="parcel" selected>Standard Parcel / Goods</option>
                            <option value="fragile">Fragile Electronics / Glassware</option>
                            <option value="grocery">Grocery & Food Parcel</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Estimated Weight (KG) *</label>
                        <input type="number" step="0.5" min="0.1" name="estimated_weight" value="1.0" required 
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Preferred Pickup Window *</label>
                        <select name="pickup_slot" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                            <option value="morning">Morning (09:00 AM - 12:00 PM)</option>
                            <option value="afternoon" selected>Afternoon (12:00 PM - 03:00 PM)</option>
                            <option value="evening">Evening (03:00 PM - 07:00 PM)</option>
                            <option value="flash">⚡ Urgent Flash (Within 60 Minutes)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pickup Notes / Delivery Instructions</label>
                    <input type="text" name="instructions" placeholder="e.g. Ring doorbell at gate 2, call upon arrival" 
                           class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                </div>

                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs text-slate-500">
                        <i class="fas fa-shield-check text-teal-600 mr-1"></i> Complimentary insurance up to Rs. 10,000 included.
                    </span>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Confirm Pickup Request</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: Live Consignment Radar Lookup -->
        <div x-show="clientTab === 'track'" class="p-6 space-y-5" style="display: none;">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Track Consignment / HAWB</h3>
                    <p class="text-xs text-slate-500">Search any active or completed domestic/international shipment for real-time status.</p>
                </div>
            </div>

            <form action="{{ route('tracking.search') }}" method="GET" class="max-w-xl space-y-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="text" name="tracking" placeholder="Enter HAWB / AWB Number (e.g. NP-DOM-98214)..." required
                           class="w-full pl-10 pr-24 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                    <button type="submit" class="absolute right-1.5 top-1.5 px-4 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold uppercase rounded-lg shadow-xs transition">
                        Radar Search
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 3: Quick Rate Estimator -->
        <div x-show="clientTab === 'quote'" class="p-6 space-y-5" style="display: none;"
             x-data="{
                dest: 'inside_valley',
                weight: 1,
                get cost() {
                    if (this.dest === 'inside_valley') return 100 + (Math.max(0, this.weight - 1) * 50);
                    if (this.dest === 'outside_valley') return 180 + (Math.max(0, this.weight - 1) * 80);
                    return 2200 + (Math.max(0, this.weight - 1) * 900);
                }
             }">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Instant Freight Rate Estimator</h3>
                    <p class="text-xs text-slate-500">Transparent pricing for Kathmandu Valley, Inter-district, and Global destinations.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Destination Zone</label>
                    <select x-model="dest" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                        <option value="inside_valley">Inside Kathmandu Valley (Kathmandu, Lalitpur, Bhaktapur)</option>
                        <option value="outside_valley">Major Cities Outside Valley (Pokhara, Biratnagar, Chitwan, etc.)</option>
                        <option value="international">International Air Courier (Worldwide 220+ Countries)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Gross Weight (KG)</label>
                    <input type="number" step="0.5" min="0.5" x-model="weight" 
                           class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono">
                </div>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex flex-col justify-center">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Estimated Cost</span>
                    <p class="text-2xl font-black text-teal-700 mt-0.5">Rs. <span x-text="cost.toLocaleString()"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT CONSIGNMENTS & ACCOUNT SUMMARY -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Shipments -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fas fa-boxes text-teal-600"></i>
                    <span>My Recent Consignments</span>
                </h3>
                <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                    View All &rarr;
                </a>
            </div>

            @if($recentShipments->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($recentShipments as $shipment)
                        <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition px-2 rounded-lg">
                            <div>
                                <a href="{{ route('tracking.show', $shipment->tracking_number) }}"
                                   class="font-mono font-bold text-xs text-slate-900 hover:text-teal-700 flex items-center gap-1.5">
                                    <span>{{ $shipment->tracking_number ?? 'N/A' }}</span>
                                    <i class="fas fa-arrow-up-right-from-square text-[9px] text-teal-600"></i>
                                </a>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $shipment->destination ?? 'Destination' }} &bull; {{ $shipment->service_type ?? 'Standard' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                    {{ str_replace('_', ' ', $shipment->status ?? 'pending') }}
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $shipment->created_at ? $shipment->created_at->diffForHumans() : '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-400">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    <p class="text-xs">No shipments found in your account yet.</p>
                </div>
            @endif
        </div>

        <!-- Client Account Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5">
            <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-id-card text-blue-600"></i>
                <span>Client Account Overview</span>
            </h3>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Account Name:</span>
                    <span class="font-bold text-slate-800">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Email:</span>
                    <span class="font-medium text-slate-800">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Contact Phone:</span>
                    <span class="font-medium text-slate-800 font-mono">{{ Auth::user()->phone ?? 'Not provided' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Entity Classification:</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200">
                        Client Account
                    </span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-500">Verification:</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ Auth::user()->verification_status ?? 'Approved' }}
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('profile') }}" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-pen-to-square"></i>
                    <span>Edit Profile & Saved Addresses</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
