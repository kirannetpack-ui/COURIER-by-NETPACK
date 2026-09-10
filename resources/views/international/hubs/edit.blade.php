@extends('layouts.app')

@section('title', 'Edit Transit Hub | ' . $hub->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('international.hubs.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-left"></i> Back to All Hubs
            </a>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Edit Transit Hub: {{ $hub->name }}</h1>
            <p class="text-sm text-slate-500">Update destination gateway configuration, customs modes, and routing parameters.</p>
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Hub Code (IATA 3-Letter) <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $hub->code) }}" required maxlength="10" class="w-full uppercase font-mono tracking-wider text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Hub Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $hub->name) }}" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Country <span class="text-rose-500">*</span></label>
                <input type="text" name="country" value="{{ old('country', $hub->country) }}" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">City</label>
                <input type="text" name="city" value="{{ old('city', $hub->city) }}" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Airport Name / Cargo Terminal</label>
                <input type="text" name="airport_name" value="{{ old('airport_name', $hub->airport_name) }}" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Customs Clearance Mode</label>
                <select name="mode_type" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="DDP" {{ old('mode_type', $hub->mode_type) == 'DDP' ? 'selected' : '' }}>DDP (Delivered Duty Paid - Duties Pre-cleared)</option>
                    <option value="DDU" {{ old('mode_type', $hub->mode_type) == 'DDU' ? 'selected' : '' }}>DDU (Delivered Duty Unpaid - Consignee Pays)</option>
                    <option value="HYBRID" {{ old('mode_type', $hub->mode_type) == 'HYBRID' ? 'selected' : '' }}>HYBRID (Supports both DDP & DDU depending on destination)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $hub->sort_order) }}" min="0" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400">
                        Clearance & Delivery Countries (Comma-separated) <span class="text-rose-500">*</span>
                    </label>
                    <a href="{{ route('admin.international-rates.create', ['hub_id' => $hub->id]) }}" class="text-[11px] font-bold text-teal-600 hover:underline">
                        <i class="fas fa-table-cells"></i> Configure Rates
                    </a>
                </div>
                <input type="text" name="coverage_countries" value="{{ old('coverage_countries', is_array($hub->coverage_countries) ? implode(', ', $hub->coverage_countries) : $hub->coverage_countries) }}" placeholder="e.g. Australia, New Zealand or AE, SA, QA, KW" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Countries cleared and delivered via this gateway hub. These countries automatically populate when Super Admin and Staff configure rate matrices.</p>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Service Routes / Corridors (Comma-separated)</label>
                <input type="text" name="service_routes" value="{{ old('service_routes', is_array($hub->service_routes) ? implode(', ', $hub->service_routes) : $hub->service_routes) }}" placeholder="e.g. Trans-Tasman Express, Pacific Linehaul, Obibox Crossing" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Regional corridors or linehaul routes operated from this hub.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $hub->is_active) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
            <label for="is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">Hub is active and available for selection</label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('international.hubs.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                <i class="fas fa-save"></i> Update Hub Settings
            </button>
        </div>
    </form>
</div>
@endsection
