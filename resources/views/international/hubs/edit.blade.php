@extends('layouts.app')

@section('title', 'Edit International Hub | ' . $hub->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('international.hubs.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-left"></i> Back to All Hubs
            </a>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Edit International Hub: {{ $hub->name }}</h1>
            <p class="text-sm text-slate-500">Update international gateway hub, transit point parameters, customs modes, and handling operations.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-xl text-sm font-mono font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                Code: {{ $hub->code }}
            </span>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-500 p-4 rounded-xl text-sm space-y-1">
            <p class="font-bold flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Please correct the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('international.hubs.update', $hub->id) }}" method="POST" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: HUB IDENTITY -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i class="fas fa-network-wired"></i> 1. Hub Identification & Type
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Hub Code (IATA 3-Letter) <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $hub->code) }}" placeholder="e.g. DXB, LHR, SYD, AKL" required maxlength="10" class="w-full uppercase font-mono tracking-wider text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">Primary gateway / airport code</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Hub Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $hub->name) }}" placeholder="e.g. DUBAI (DXB) - GULF & WORLDWIDE GATEWAY" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Hub Role / Classification <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-xs">
                            <input type="radio" name="hub_type" value="main_hub" {{ old('hub_type', $hub->hub_type ?? 'main_hub') === 'main_hub' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="font-bold block text-slate-800 dark:text-white">Main Gateway</span>
                                <span class="text-[10px] text-slate-400">Primary consolidation</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-xs">
                            <input type="radio" name="hub_type" value="transit_point" {{ old('hub_type', $hub->hub_type) === 'transit_point' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="font-bold block text-slate-800 dark:text-white">Transit Point</span>
                                <span class="text-[10px] text-slate-400">Intermediate routing</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Customs Clearance Mode <span class="text-rose-500">*</span></label>
                    <select name="mode_type" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="DDP" {{ old('mode_type', $hub->mode_type) == 'DDP' ? 'selected' : '' }}>DDP (Delivered Duty Paid - Duties Pre-cleared)</option>
                        <option value="DDU" {{ old('mode_type', $hub->mode_type) == 'DDU' ? 'selected' : '' }}>DDU (Delivered Duty Unpaid - Consignee Pays)</option>
                        <option value="HYBRID" {{ old('mode_type', $hub->mode_type) == 'HYBRID' ? 'selected' : '' }}>HYBRID (Supports both DDP & DDU depending on destination)</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <input type="checkbox" name="is_mandatory" id="is_mandatory" value="1" {{ old('is_mandatory', $hub->is_mandatory) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="is_mandatory" class="text-xs font-medium text-slate-700 dark:text-slate-300">
                    <span class="font-bold">Mandatory Regional Hub</span> (Primary transit and clearance gateway for all consignments routed to this sector)
                </label>
            </div>
        </div>

        <!-- SECTION 2: LOCATION & AIRPORT -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i class="fas fa-plane-departure"></i> 2. Location & Airport Terminal
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Country <span class="text-rose-500">*</span></label>
                    <input type="text" name="country" value="{{ old('country', $hub->country) }}" placeholder="e.g. United Arab Emirates, United Kingdom, USA" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">City / Location <span class="text-rose-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $hub->city) }}" placeholder="e.g. Dubai, London, New York" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Airport Name / Cargo Terminal</label>
                    <input type="text" name="airport_name" value="{{ old('airport_name', $hub->airport_name) }}" placeholder="e.g. Dubai Cargo Village (DXB)" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Coverage Countries (Clearance & Delivery Scope)</label>
                    <input type="text" name="coverage_countries" value="{{ old('coverage_countries', is_array($hub->coverage_countries) ? implode(', ', $hub->coverage_countries) : $hub->coverage_countries) }}" placeholder="e.g. AE, SA, QA, KW, BH, OM, CA" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">Comma-separated ISO codes or country names served by this hub</p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Service Corridors / Routes</label>
                    <input type="text" name="service_routes" value="{{ old('service_routes', is_array($hub->service_routes) ? implode(', ', $hub->service_routes) : $hub->service_routes) }}" placeholder="e.g. Gulf Express, Direct London Heathrow, North America DDU" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">Routing corridors operated through this gateway</p>
                </div>
            </div>
        </div>

        <!-- SECTION 3: HANDLING AGENCY & OPERATIONS (MERGED) -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i class="fas fa-building"></i> 3. Handling Agency & Local Operations
            </h3>

            @php
                $attachedAgency = $agency ?? $hub->agencies->first();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Handling Agency / Facility Name</label>
                    <input type="text" name="handling_agency_name" value="{{ old('handling_agency_name', $attachedAgency?->name) }}" placeholder="e.g. Gulf Express Clearance Services LLC" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">Local partner agency handling customs clearance & last-mile dispatch</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Operations Contact Person</label>
                    <input type="text" name="agency_contact_person" value="{{ old('agency_contact_person', $attachedAgency?->primary_contact) }}" placeholder="e.g. Operations Manager / Rashid Khan" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Operations Phone</label>
                    <input type="text" name="agency_phone" value="{{ old('agency_phone', $attachedAgency?->phone) }}" placeholder="e.g. +971-4-1234567" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Dispatch & Pre-alert Email</label>
                    <input type="email" name="agency_email" value="{{ old('agency_email', $attachedAgency?->email) }}" placeholder="e.g. ops@clearance-partner.com" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Facility / Warehouse Address</label>
                    <input type="text" name="agency_address" value="{{ old('agency_address', $attachedAgency?->address) }}" placeholder="e.g. Cargo Terminal 2, Air Freight City" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- SECTION 4: DISPLAY & STATUS -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $hub->is_active) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">Hub is active and available for selection and flight manifests</label>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-bold uppercase text-slate-500">Sort Order:</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $hub->sort_order) }}" min="0" class="w-20 text-sm px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('international.hubs.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                <i class="fas fa-save"></i> Update International Hub
            </button>
        </div>
    </form>
</div>
@endsection
