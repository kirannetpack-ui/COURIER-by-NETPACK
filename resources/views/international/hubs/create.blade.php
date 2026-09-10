@extends('layouts.app')

@section('title', 'Create International Transit Hub | Netpack')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('international.hubs.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-left"></i> Back to All Hubs
            </a>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Create International Hub</h1>
            <p class="text-sm text-slate-500">Configure a destination gateway for international consolidations and customs routing.</p>
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

    <form action="{{ route('international.hubs.store') }}" method="POST" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Hub Code (IATA 3-Letter) <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. DXB, LHR, SYD, AKL" required maxlength="10" class="w-full uppercase font-mono tracking-wider text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Primary gateway / airport code</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Hub Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. DUBAI (DXB) - GULF & WORLDWIDE GATEWAY" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Country <span class="text-rose-500">*</span></label>
                <input type="text" name="country" value="{{ old('country') }}" placeholder="e.g. United Arab Emirates, United Kingdom" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">City</label>
                <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Dubai, London, Sydney" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Airport Name / Cargo Terminal</label>
                <input type="text" name="airport_name" value="{{ old('airport_name') }}" placeholder="e.g. Dubai Cargo Village (DXB)" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Default Customs Clearance Mode</label>
                <select name="mode_type" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="DDP" {{ old('mode_type') == 'DDP' ? 'selected' : '' }}>DDP (Delivered Duty Paid - Duties Pre-cleared)</option>
                    <option value="DDU" {{ old('mode_type') == 'DDU' ? 'selected' : '' }}>DDU (Delivered Duty Unpaid - Consignee Pays)</option>
                    <option value="HYBRID" {{ old('mode_type', 'HYBRID') == 'HYBRID' ? 'selected' : '' }}>HYBRID (Supports both DDP & DDU depending on destination)</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1">E.g., UK supports UK/EU under DDP and USA/Canada under DDU.</p>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Order in dropdowns and manifests</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Coverage Countries (Comma-separated ISO-2 or names)</label>
                <input type="text" name="coverage_countries" value="{{ old('coverage_countries') }}" placeholder="e.g. AE, SA, QA, KW, BH, OM, CA" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">All countries reachable via this hub gateway</p>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Service Routes / Corridors (Comma-separated)</label>
                <input type="text" name="service_routes" value="{{ old('service_routes') }}" placeholder="e.g. Gulf Express, UPS Worldwide Crossing, Canada DDP (Direct to Toronto)" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Corridors supported by this hub</p>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
            <label for="is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">Hub is active and available for booking selection and manifests</label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('international.hubs.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                <i class="fas fa-save"></i> Save Transit Hub
            </button>
        </div>
    </form>
</div>
@endsection
