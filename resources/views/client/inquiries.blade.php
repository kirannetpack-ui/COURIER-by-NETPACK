@extends('layouts.app')

@section('title', 'Shipment Inquiries & Booking Desk - NETPACK')
@section('page-title', 'Shipment Inquiries Desk')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ activeTab: '{{ request()->has('status') || request()->has('search') ? 'list' : 'form' }}' }">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 rounded-2xl p-6 text-white shadow-sm border border-teal-900/40">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        Client Operations
                    </span>
                    <span class="text-xs text-slate-400">&bull; Doorstep Intake & Consignment Inquiries</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">
                    <i class="fas fa-truck-ramp-box text-teal-400"></i>
                    <span>Shipment Inquiries & Booking Desk</span>
                </h1>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Request doorstep collection for domestic and international packages, schedule pickups, and monitor the live dispatch status of all your inquiries in one place.
                </p>
            </div>

            <!-- View Switcher Tabs -->
            <div class="flex items-center gap-2 bg-slate-800/80 p-1.5 rounded-xl border border-slate-700/60 self-start md:self-auto">
                <button type="button" 
                        @click="activeTab = 'form'"
                        :class="activeTab === 'form' ? 'bg-teal-600 text-white shadow-sm font-bold' : 'text-slate-300 hover:text-white font-medium'"
                        class="px-4 py-2 rounded-lg text-xs transition flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>New Inquiry</span>
                </button>

                <button type="button" 
                        @click="activeTab = 'list'"
                        :class="activeTab === 'list' ? 'bg-teal-600 text-white shadow-sm font-bold' : 'text-slate-300 hover:text-white font-medium'"
                        class="px-4 py-2 rounded-lg text-xs transition flex items-center gap-2">
                    <i class="fas fa-list-check"></i>
                    <span>My Inquiries ({{ $statusCounts['all'] ?? 0 }})</span>
                </button>
            </div>
        </div>
    </div>

    <!-- TAB 1: New Shipment / Pickup Inquiry Form -->
    <div x-show="activeTab === 'form'" x-transition class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-pen text-teal-600"></i>
                    <span>Submit New Consignment Pickup Inquiry</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Fill out your origin and destination details to request on-site courier collection.</p>
            </div>
            <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200">
                Instant Dispatch
            </span>
        </div>

        <form action="{{ route('client.inquiries.store') }}" method="POST" class="p-6 space-y-6"
              x-data="{
                  scope: 'inside_valley',
                  calcWeight: 1.0,
                  get estimatedEstimate() {
                      if (this.scope === 'inside_valley') return 100 + (Math.max(0, this.calcWeight - 1) * 50);
                      if (this.scope === 'outside_valley') return 180 + (Math.max(0, this.calcWeight - 1) * 80);
                      return 2200 + (Math.max(0, this.calcWeight - 1) * 900);
                  }
              }">
            @csrf

            <!-- Destination Corridor Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    1. Select Delivery Destination Scope *
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label :class="scope === 'inside_valley' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 hover:border-slate-300'"
                           class="border rounded-xl p-3.5 cursor-pointer transition flex items-start gap-3">
                        <input type="radio" name="destination_scope" value="inside_valley" x-model="scope" class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Inside Kathmandu Valley</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Kathmandu, Lalitpur, Bhaktapur (Same-Day / Standard)</span>
                        </div>
                    </label>

                    <label :class="scope === 'outside_valley' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 hover:border-slate-300'"
                           class="border rounded-xl p-3.5 cursor-pointer transition flex items-start gap-3">
                        <input type="radio" name="destination_scope" value="outside_valley" x-model="scope" class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Nepal Inter-District</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Pokhara, Biratnagar, Chitwan, Butwal & 7 Provinces</span>
                        </div>
                    </label>

                    <label :class="scope === 'international' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 hover:border-slate-300'"
                           class="border rounded-xl p-3.5 cursor-pointer transition flex items-start gap-3">
                        <input type="radio" name="destination_scope" value="international" x-model="scope" class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">International Air Courier</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Worldwide Global Hubs (USA, UK, AUS, UAE, 220+ Countries)</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Origin Pickup Section -->
                <div class="p-4 rounded-xl bg-slate-50/60 border border-slate-200/80 space-y-4">
                    <div class="flex items-center gap-2 text-teal-700 font-bold text-xs uppercase tracking-wider pb-2 border-b border-slate-200/60">
                        <i class="fas fa-location-dot"></i>
                        <span>Origin Pickup Details</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pickup Address & Landmark *</label>
                        <input type="text" name="pickup_address" required 
                               value="{{ Auth::user()->address ?? Auth::user()->permanent_address }}"
                               placeholder="e.g. Ward 4, Baluwatar, Near Prime Minister Residence" 
                               class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pickup Contact Phone *</label>
                        <input type="text" name="contact_phone" required 
                               value="{{ Auth::user()->phone }}"
                               placeholder="e.g. 9841000000" 
                               class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono bg-white">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pickup Date *</label>
                            <input type="date" name="scheduled_pickup_time" 
                                   value="{{ date('Y-m-d') }}"
                                   min="{{ date('Y-m-d') }}" required
                                   class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Time Slot *</label>
                            <select name="pickup_slot" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                                <option value="morning">Morning (09:00 - 12:00)</option>
                                <option value="afternoon" selected>Afternoon (12:00 - 15:00)</option>
                                <option value="evening">Evening (15:00 - 18:00)</option>
                                <option value="flash">⚡ Urgent Flash Pickup</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Destination Consignee Section -->
                <div class="p-4 rounded-xl bg-slate-50/60 border border-slate-200/80 space-y-4">
                    <div class="flex items-center gap-2 text-indigo-700 font-bold text-xs uppercase tracking-wider pb-2 border-b border-slate-200/60">
                        <i class="fas fa-user-check"></i>
                        <span>Destination Consignee Details</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Recipient Name *</label>
                            <input type="text" name="recipient_name" required 
                                   placeholder="Full name of consignee" 
                                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Recipient Phone *</label>
                            <input type="text" name="recipient_phone" required 
                                   placeholder="e.g. 9800000000 / International format" 
                                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Destination City / Country *</label>
                        <input type="text" name="delivery_city" required 
                               placeholder="e.g. Pokhara, Biratnagar, or New York, USA" 
                               class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Detailed Delivery Address *</label>
                        <input type="text" name="delivery_address" required 
                               placeholder="Street address, building, ward number, postal code" 
                               class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- Package Specifications & Estimate Row -->
            <div class="p-4 rounded-xl border border-slate-200/80 bg-white grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Package Classification *</label>
                    <select name="package_type" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                        <option value="Legal & Business Documents">Documents (Letter, Contract, Passport)</option>
                        <option value="Parcel & Commercial Goods" selected>Standard Parcel / Goods</option>
                        <option value="Fragile & Electronics">Fragile Electronics / Glassware</option>
                        <option value="Apparel & Samples">Garments / Apparel Samples</option>
                        <option value="Perishable & Grocery">Dry Grocery / Foodstuff</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Estimated Weight (KG) *</label>
                    <input type="number" step="0.5" min="0.1" name="estimated_weight_kg" x-model="calcWeight" required
                           class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Service Tier SLA</label>
                    <select name="service_tier" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                        @if(!empty($services) && $services->count() > 0)
                            @foreach($services as $svc)
                                <option value="{{ $svc->code }}">{{ $svc->name }} ({{ $svc->transit_display }})</option>
                            @endforeach
                        @else
                            <option value="standard">Standard Corridor (24-48 Hours)</option>
                            <option value="same_day">Same Day Express (Within 12 Hours)</option>
                            <option value="flash">⚡ Flash Doorstep (60 Mins)</option>
                        @endif
                    </select>
                </div>

                <div class="p-3 bg-teal-50/70 border border-teal-200/80 rounded-xl text-center">
                    <span class="text-[10px] font-bold text-teal-800 uppercase tracking-wider block">Estimated Freight</span>
                    <span class="text-xl font-black text-teal-700 font-mono mt-0.5 block">
                        Rs. <span x-text="estimatedEstimate.toLocaleString()"></span>
                    </span>
                    <span class="text-[9px] text-teal-600 block mt-0.5">Subject to scale verification</span>
                </div>
            </div>

            <!-- Notes & Submission -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Special Handling Instructions (Optional)</label>
                <input type="text" name="instructions" 
                       placeholder="e.g. Call before coming, handle with care, gate code #4" 
                       class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <i class="fas fa-shield-halved text-teal-600"></i>
                    <span>NetPack Standard Courier Guarantee with real-time GPS telemetry assignment.</span>
                </div>

                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-teal-900/20 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Shipment Inquiry</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: My Inquiries & Requests List -->
    <div x-show="activeTab === 'list'" x-transition class="space-y-4">
        <!-- Status Filter Tabs & Search -->
        <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <a href="{{ route('client.inquiries', ['status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ !request('status') || request('status') === 'all' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }}">
                    All ({{ $statusCounts['all'] ?? 0 }})
                </a>
                <a href="{{ route('client.inquiries', ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'pending' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }}">
                    Pending ({{ $statusCounts['pending'] ?? 0 }})
                </a>
                <a href="{{ route('client.inquiries', ['status' => 'assigned']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'assigned' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }}">
                    Assigned ({{ $statusCounts['assigned'] ?? 0 }})
                </a>
                <a href="{{ route('client.inquiries', ['status' => 'picked_up']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'picked_up' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }}">
                    Picked Up ({{ $statusCounts['picked_up'] ?? 0 }})
                </a>
                <a href="{{ route('client.inquiries', ['status' => 'in_transit']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'in_transit' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }}">
                    In Transit ({{ $statusCounts['in_transit'] ?? 0 }})
                </a>
                <a href="{{ route('client.inquiries', ['status' => 'delivered']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'delivered' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }}">
                    Delivered ({{ $statusCounts['delivered'] ?? 0 }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('client.inquiries') }}" method="GET" class="relative max-w-xs w-full">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search reference or consignee..." 
                       class="w-full text-xs pl-8 pr-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none">
                <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs pointer-events-none"></i>
            </form>
        </div>

        <!-- Inquiries Cards Grid -->
        @if($inquiries->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($inquiries as $inquiry)
                    @php
                        $st = strtolower($inquiry->status ?? 'pending');
                        $badge = match($st) {
                            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'assigned' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'picked_up' => 'bg-teal-100 text-teal-800 border-teal-200',
                            'in_transit' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                            default => 'bg-slate-100 text-slate-800 border-slate-200'
                        };
                    @endphp
                    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 hover:border-teal-500/50 transition flex flex-col justify-between">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                                <div>
                                    <span class="font-mono font-black text-xs text-slate-900 flex items-center gap-1.5">
                                        <i class="fas fa-barcode text-teal-600"></i>
                                        {{ $inquiry->tracking_number ?? ('#REQ-' . $inquiry->id) }}
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $inquiry->created_at ? $inquiry->created_at->format('M d, Y (h:i A)') : '' }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badge }} flex-shrink-0">
                                    {{ str_replace('_', ' ', $inquiry->status) }}
                                </span>
                            </div>

                            <!-- Route & Consignee -->
                            <div class="py-3 space-y-2 text-xs">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pickup From:</span>
                                    <p class="text-slate-800 font-medium truncate mt-0.5">
                                        <i class="fas fa-location-dot text-teal-600 text-[10px] mr-1"></i>
                                        {{ $inquiry->pickup_address }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Delivering To:</span>
                                    <p class="text-slate-900 font-bold truncate mt-0.5">
                                        <i class="fas fa-arrow-right-long text-teal-600 text-[10px] mr-1"></i>
                                        {{ $inquiry->delivery_city ?? 'Destination' }}
                                        @if($inquiry->customer_name)
                                            <span class="text-slate-500 font-normal">({{ $inquiry->customer_name }})</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-1 text-[11px] text-slate-600">
                                    <span>
                                        <i class="fas fa-box text-slate-400 mr-1"></i>
                                        {{ $inquiry->items_description ?? 'Package Goods' }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-800">
                                        {{ number_format($inquiry->estimated_weight_kg ?? 1.0, 1) }} KG
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Actions -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="text-[10px] text-slate-500 font-mono">
                                Slot: {{ ucfirst($inquiry->service_tier ?? 'Standard') }}
                            </span>

                            @if($inquiry->tracking_number)
                                <a href="{{ route('tracking.show', $inquiry->tracking_number) }}" 
                                   class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1.5 shadow-2xs">
                                    <i class="fas fa-satellite-dish"></i>
                                    <span>Track Inquiry</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $inquiries->withQueryString()->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl mb-3">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900">No Inquiries Found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    You have not submitted any pickup inquiries under this filter yet. Submit a new request using the form above!
                </p>
                <button type="button" @click="activeTab = 'form'" 
                        class="mt-4 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Create First Inquiry</span>
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
