@extends('layouts.app')

@section('title', 'Super Admin Operations Dashboard - COURIER with NETPACK')
@section('page-title', 'Operations Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Pending Users Approval Alert -->
    @php
        $pendingCount = \App\Models\User::where('verification_status', 'pending')->count();
    @endphp

    @if($pendingCount > 0)
        <div class="bg-amber-50 border border-amber-300 text-amber-900 px-5 py-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold">Pending Registrations Require Review</h3>
                    <p class="text-xs text-amber-700"><strong>{{ $pendingCount }}</strong> client/partner account(s) are awaiting administrative approval.</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}?status=pending" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg transition shadow-xs text-center flex-shrink-0">
                Review Accounts &rarr;
            </a>
        </div>
    @endif

    <!-- Operational KPI Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 hover:border-teal-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Consignments</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format(\App\Models\Shipment::count()) }}</p>
                    <p class="text-[11px] text-teal-600 font-medium mt-1"><i class="fas fa-arrow-trend-up"></i> Master Registry</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-box-archive"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 hover:border-blue-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active In-Transit</p>
                    <p class="text-2xl sm:text-3xl font-black text-blue-600 mt-1">{{ number_format(\App\Models\Shipment::whereIn('status', ['in_transit', 'out_for_delivery', 'picked_up'])->count()) }}</p>
                    <p class="text-[11px] text-blue-600 font-medium mt-1"><i class="fas fa-route"></i> Moving On Ground</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-truck-fast"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Successfully Delivered</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">{{ number_format(\App\Models\Shipment::where('status', 'delivered')->count()) }}</p>
                    <p class="text-[11px] text-emerald-600 font-medium mt-1"><i class="fas fa-circle-check"></i> Completed Cycles</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 hover:border-amber-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Registered Network</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format(\App\Models\User::count()) }}</p>
                    <p class="text-[11px] text-amber-600 font-medium mt-1"><i class="fas fa-users"></i> Clients, Partners, Riders</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-network-wired"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- FORM WORKBENCH: OPERATIONAL INTERACTIVE FORMS EMBEDDED IN PAGE -->
    <!-- ============================================================= -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden" x-data="{ activeTab: 'dispatch' }">
        <!-- Tab Navigation Bar -->
        <div class="border-b border-slate-200 bg-slate-50/60 px-5 pt-3 flex flex-wrap gap-2">
            <button type="button" 
                    @click="activeTab = 'dispatch'" 
                    :class="activeTab === 'dispatch' ? 'border-teal-600 text-teal-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-white/50 font-medium'"
                    class="px-4 py-2.5 text-xs rounded-t-xl border-b-2 transition flex items-center gap-2">
                <i class="fas fa-paper-plane text-teal-600"></i>
                <span>Quick Consignment Booking Form</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'telemetry'" 
                    :class="activeTab === 'telemetry' ? 'border-teal-600 text-teal-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-white/50 font-medium'"
                    class="px-4 py-2.5 text-xs rounded-t-xl border-b-2 transition flex items-center gap-2">
                <i class="fas fa-satellite-dish text-blue-600"></i>
                <span>Live Checkpoint & Status Telemetry</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'calculator'" 
                    :class="activeTab === 'calculator' ? 'border-teal-600 text-teal-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-white/50 font-medium'"
                    class="px-4 py-2.5 text-xs rounded-t-xl border-b-2 transition flex items-center gap-2">
                <i class="fas fa-calculator text-amber-600"></i>
                <span>Rate & Freight Calculator</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'delay'" 
                    :class="activeTab === 'delay' ? 'border-teal-600 text-teal-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-white/50 font-medium'"
                    class="px-4 py-2.5 text-xs rounded-t-xl border-b-2 transition flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                <span>Delay & Reason Logger</span>
            </button>
        </div>

        <!-- FORM 1: Express Consignment Booking & Dispatch -->
        <div x-show="activeTab === 'dispatch'" class="p-6 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Direct Consignment Manifest & Booking</h3>
                    <p class="text-xs text-slate-500">Create, validate, and issue official Air Waybill / HAWB directly from the operations desk.</p>
                </div>
                <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200">
                    ⚡ Express Intake
                </span>
            </div>

            <form action="{{ route('shipments.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Sender Details -->
                    <div class="p-4 bg-slate-50/70 rounded-xl border border-slate-200/80 space-y-3">
                        <p class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-user-arrow-up-long text-teal-600"></i> 1. Consignor / Sender
                        </p>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sender Name / Company *</label>
                            <input type="text" name="sender_name" required placeholder="Full Name or Business Name" 
                                   class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sender Phone *</label>
                            <input type="text" name="sender_phone" required placeholder="e.g. 9851000000" 
                                   class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Origin City / Hub *</label>
                            <select name="origin" required class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                <option value="Kathmandu (KTM Hub)" selected>Kathmandu (KTM Central Hub)</option>
                                <option value="Pokhara Central Hub">Pokhara Central Hub</option>
                                <option value="Biratnagar Express Hub">Biratnagar Express Hub</option>
                                <option value="Birgunj Cargo Terminal">Birgunj Cargo Terminal</option>
                                <option value="Nepalgunj Western Gateway">Nepalgunj Western Gateway</option>
                                <option value="Bhairahawa Station">Bhairahawa Station</option>
                            </select>
                        </div>
                    </div>

                    <!-- Receiver Details -->
                    <div class="p-4 bg-slate-50/70 rounded-xl border border-slate-200/80 space-y-3">
                        <p class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-user-arrow-down-long text-blue-600"></i> 2. Consignee / Receiver
                        </p>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Receiver Name *</label>
                            <input type="text" name="receiver_name" required placeholder="Consignee Full Name" 
                                   class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Receiver Phone *</label>
                            <input type="text" name="receiver_phone" required placeholder="e.g. 9841000000" 
                                   class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Destination City & Ward *</label>
                            <input type="text" name="destination" required placeholder="e.g. Pokhara-08, Srijana Chowk or London, UK" 
                                   class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                    </div>

                    <!-- Service Level & Package Specs -->
                    <div class="p-4 bg-slate-50/70 rounded-xl border border-slate-200/80 space-y-3">
                        <p class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-box-open text-amber-600"></i> 3. Specifications & Service
                        </p>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Service Class *</label>
                            <select name="service_type" required class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-medium">
                                <option value="flash">⚡ Flash Priority (2-4 hrs Kathmandu Corridor)</option>
                                <option value="standard" selected>Standard Express (1-3 Business Days)</option>
                                <option value="same_day">Same Day Inter-District Express</option>
                                <option value="himalayan">Himalayan Rugged Express (Mountain Wards)</option>
                                <option value="international">✈️ International Air Courier (Worldwide)</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Weight (KG) *</label>
                                <input type="number" step="0.1" min="0.1" name="weight" value="1.0" required 
                                       class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">COD Amount (NPR)</label>
                                <input type="number" step="1" min="0" name="cod_amount" value="0" 
                                       class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Item Contents / Description *</label>
                            <input type="text" name="description" required placeholder="e.g. Legal documents, apparel, electronic accessories" 
                                   class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-xs text-slate-500">
                        <i class="fas fa-circle-info text-teal-600 mr-1"></i> A unique tracking number and official barcode will be automatically generated upon submission.
                    </p>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        <span>Generate Consignment & HAWB</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- FORM 2: Live Checkpoint & Status Telemetry Broadcast -->
        <div x-show="activeTab === 'telemetry'" class="p-6 space-y-5" style="display: none;">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Broadcast Checkpoint & Telemetry Milestone</h3>
                    <p class="text-xs text-slate-500">Push real-time location and milestone updates to live public trackers and customer notifications.</p>
                </div>
                <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                    🛰️ Radar Stream
                </span>
            </div>

            <form action="{{ route('tracking.page') }}" method="GET" class="space-y-4 max-w-2xl">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Consignment / HAWB Number *</label>
                    <div class="relative">
                        <i class="fas fa-barcode absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input type="text" name="tracking" placeholder="Enter Tracking Number (e.g. NP-DOM-98214)" required
                               class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Current Milestone Status *</label>
                        <select class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                            <option value="MANIFESTED">MANIFESTED — Intake Confirmed</option>
                            <option value="PICKED_UP">PICKED UP — Collected by Rider / Courier</option>
                            <option value="IN_TRANSIT" selected>IN TRANSIT — Corridor Movement</option>
                            <option value="OUT_FOR_DELIVERY">OUT FOR DELIVERY — Assigned to Last-Mile Rider</option>
                            <option value="CUSTOMS_CLEARED">CUSTOMS CLEARED — TIA Cargo Export</option>
                            <option value="DELIVERED">DELIVERED — Consignee Signature Acquired</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Checkpoint / Hub Name *</label>
                        <input type="text" placeholder="e.g. Mugling Checkpoint, Tribhuvan Cargo Terminal" 
                               class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Checkpoint Remark</label>
                    <input type="text" placeholder="e.g. Package cleared primary sorting scan; loaded onto Pokhara Express line haul vehicle."
                           class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('tracking.update') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition inline-flex items-center gap-2">
                        <i class="fas fa-satellite"></i> Open Dedicated Telemetry Console
                    </a>
                    <a href="{{ route('hawb.scanner') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center gap-2">
                        <i class="fas fa-qrcode"></i> Launch Optical Barcode Scanner
                    </a>
                </div>
            </form>
        </div>

        <!-- FORM 3: Instant Multi-Service Rate & Surcharge Estimator -->
        <div x-show="activeTab === 'calculator'" class="p-6 space-y-5" style="display: none;" 
             x-data="{
                mode: 'domestic',
                weight: 1.5,
                l: 20, w: 15, h: 10,
                serviceTier: 'standard',
                get volumetric() {
                    return ((this.l * this.w * this.h) / 5000).toFixed(2);
                },
                get billableWeight() {
                    return Math.max(parseFloat(this.weight || 0), parseFloat(this.volumetric || 0)).toFixed(2);
                },
                get estimatedCost() {
                    let base = this.mode === 'international' ? 2400 : 120;
                    let ratePerKg = this.mode === 'international' ? 950 : 80;
                    let tierMultiplier = this.serviceTier === 'flash' ? 1.6 : (this.serviceTier === 'himalayan' ? 1.4 : 1.0);
                    let subtotal = (base + (this.billableWeight * ratePerKg)) * tierMultiplier;
                    return Math.round(subtotal);
                }
             }">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Instant Shipping Tariff & Volumetric Calculator</h3>
                    <p class="text-xs text-slate-500">Compute billable dimensional weight, zone rates, and surcharges instantly on the counter.</p>
                </div>
                <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                    🧮 Tariff Engine
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Logistics Corridor</label>
                            <select x-model="mode" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                                <option value="domestic">Domestic (Nepal 7 Provinces)</option>
                                <option value="international">International Air Cargo (Global)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Priority</label>
                            <select x-model="serviceTier" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                                <option value="standard">Standard Corridor (1-3 Days)</option>
                                <option value="flash">⚡ Flash Priority (2-4 hrs Express)</option>
                                <option value="himalayan">Himalayan Rugged Terrain Route</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Actual (KG)</label>
                            <input type="number" step="0.1" min="0.1" x-model="weight" 
                                   class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Length (CM)</label>
                            <input type="number" step="1" min="1" x-model="l" 
                                   class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Width (CM)</label>
                            <input type="number" step="1" min="1" x-model="w" 
                                   class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Height (CM)</label>
                            <input type="number" step="1" min="1" x-model="h" 
                                   class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono">
                        </div>
                    </div>
                </div>

                <!-- Estimated Cost Card -->
                <div class="bg-gradient-to-br from-slate-900 to-teal-950 p-5 rounded-xl text-white flex flex-col justify-between shadow-sm">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-teal-300 font-bold">Estimated Tariff Summary</p>
                        <div class="mt-3 space-y-1 text-xs">
                            <div class="flex justify-between text-slate-300">
                                <span>Volumetric Weight:</span>
                                <span class="font-mono font-bold" x-text="volumetric + ' kg'"></span>
                            </div>
                            <div class="flex justify-between text-slate-300">
                                <span>Billable Chargeable:</span>
                                <span class="font-mono font-bold text-teal-300" x-text="billableWeight + ' kg'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 mt-4">
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Estimated Total</span>
                        <p class="text-3xl font-black text-white mt-0.5">
                            Rs. <span x-text="estimatedCost.toLocaleString()"></span>
                        </p>
                        <span class="text-[10px] text-teal-300 block mt-1">*Excluding VAT / Remote Area Surcharge</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM 4: Delay & Reason Logger -->
        <div x-show="activeTab === 'delay'" class="p-6 space-y-5" style="display: none;">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Broadcast Transit Delay Exception</h3>
                    <p class="text-xs text-slate-500">Record uncontrollable obstructions (weather, landslides, customs hold) with mandatory ETA.</p>
                </div>
                <a href="{{ route('admin.communications') }}" class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded text-xs font-bold hover:bg-rose-100 transition">
                    View Delay Hub &rarr;
                </a>
            </div>

            <form action="{{ route('admin.communications.send-delay') }}" method="POST" class="max-w-2xl space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tracking Number *</label>
                        <input type="text" name="tracking_number" required placeholder="e.g. NP-DOM-98214"
                               class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Reason Code *</label>
                        <select name="reason_code" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="weather">Adverse Weather (Monsoon / Fog / Heavy Snow)</option>
                            <option value="highway_blocked">Highway Landslide / Road Obstruction</option>
                            <option value="customs_hold">Customs Clearance / Document Check</option>
                            <option value="recipient_unreachable">Consignee Phone Unreachable</option>
                            <option value="address_incomplete">Incomplete Locality / Ward Verification</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Expected Delivery Reschedule</label>
                        <input type="datetime-local" name="expected_resolution" 
                               class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Channel *</label>
                        <select name="channel" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="all" selected>All (SMS & Email)</option>
                            <option value="email">Email Notification Only</option>
                            <option value="sms">SMS Priority Dispatch Only</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Delay Explanation Note</label>
                    <textarea name="custom_note" rows="2" placeholder="Detail reason (e.g. Mugling road closed due to dry landslide, re-route via Hetauda)..."
                              class="w-full text-xs border border-slate-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>Log Delay & Notify Parties</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- RECENT ACTIVITY & MONITORING TABLES -->
    <!-- ============================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Shipments Registry -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fas fa-truck-fast text-teal-600"></i>
                    <span>Recent Consignments</span>
                </h3>
                <a href="{{ route('admin.shipments.index') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                    View All &rarr;
                </a>
            </div>

            @php
                $recentShipments = \App\Models\Shipment::orderBy('created_at', 'desc')->take(6)->get();
            @endphp

            <div class="divide-y divide-slate-100">
                @forelse($recentShipments as $s)
                    <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition px-2 rounded-lg">
                        <div class="min-w-0">
                            <a href="{{ route('tracking.page') }}?tracking={{ $s->tracking_number }}" target="_blank" 
                               class="font-mono font-bold text-xs text-slate-900 hover:text-teal-700 flex items-center gap-1.5">
                                <span>{{ $s->tracking_number }}</span>
                                <i class="fas fa-external-link-alt text-[9px] text-slate-400"></i>
                            </a>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                {{ $s->origin ?? 'Kathmandu' }} &rarr; {{ $s->destination ?? 'Destination' }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                {{ str_replace('_', ' ', $s->status ?? 'pending') }}
                            </span>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $s->created_at ? $s->created_at->diffForHumans() : '' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No recent consignments logged.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Registered Users / Portals -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fas fa-users text-blue-600"></i>
                    <span>Recent Accounts & Approvals</span>
                </h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                    Manage &rarr;
                </a>
            </div>

            @php
                $recentUsers = \App\Models\User::orderBy('created_at', 'desc')->take(6)->get();
            @endphp

            <div class="divide-y divide-slate-100">
                @forelse($recentUsers as $u)
                    <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition px-2 rounded-lg">
                        <div class="min-w-0">
                            <p class="font-bold text-xs text-slate-900 truncate">{{ $u->name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $u->email }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200">
                                {{ (in_array($u->user_type, ['customer', 'client'])) ? 'Client' : ucfirst($u->user_type) }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $u->verification_status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $u->verification_status ?? 'approved' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No recent registered users found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
