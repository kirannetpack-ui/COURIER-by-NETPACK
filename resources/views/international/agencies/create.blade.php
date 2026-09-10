@extends('layouts.app')

@section('title', 'Add Partner Agency | International Logistics')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('international.agencies.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-left"></i> Back to All Agencies
            </a>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Add International Partner Agency</h1>
            <p class="text-sm text-slate-500">Register an overseas receiving agency under a gateway hub, complete with multiple dispatch notification emails.</p>
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

    <form action="{{ route('international.agencies.store') }}" method="POST" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Assigned Destination Hub <span class="text-rose-500">*</span></label>
                <select name="hub_id" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    <option value="">-- Select Transit Gateway Hub --</option>
                    @foreach($hubs as $hub)
                        <option value="{{ $hub->id }}" {{ old('hub_id') == $hub->id ? 'selected' : '' }}>
                            {{ $hub->code }} - {{ $hub->name }} ({{ $hub->country }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Hub under which this agency operates</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Agency Code <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. DXB-EXP, LHR-DDP, SYD-AUS" required maxlength="20" class="w-full uppercase font-mono tracking-wider text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Agency Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Dubai Express Clearance Services LLC" required class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Primary Contact Person</label>
                <input type="text" name="primary_contact" value="{{ old('primary_contact') }}" placeholder="Operations Manager name" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Primary Phone / Mobile</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+971 4 123 4567" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Primary Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="ops@agency.com" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
        </div>

        <!-- Notification Emails (Multi-target) -->
        <div class="bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4 space-y-2">
            <label class="block text-xs font-bold uppercase text-amber-900 dark:text-amber-300">
                <i class="fas fa-paper-plane mr-1"></i> Multi-Target Dispatch Notification Emails
            </label>
            <textarea name="notification_emails" rows="2" placeholder="e.g. ops@agency.ae, cargo@agency.ae, manifest@agency.ae" class="w-full font-mono text-sm px-3.5 py-2.5 rounded-xl border border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">{{ old('notification_emails') }}</textarea>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Enter multiple email addresses separated by commas or new lines. When an international air cargo manifest is generated, our engine will send the flight manifest, pre-alert, and data sheet directly to all these addresses.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Country</label>
                <input type="text" name="country" value="{{ old('country') }}" placeholder="United Arab Emirates" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">City / Cargo Terminal Location</label>
                <input type="text" name="city" value="{{ old('city') }}" placeholder="Dubai Cargo Village, DAFZA" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Full Warehouse / Office Address</label>
            <textarea name="address" rows="2" placeholder="Building, Street, Cargo Free Zone, PO Box" class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">{{ old('address') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Operational Handover & Clearance Notes</label>
            <textarea name="operational_notes" rows="2" placeholder="e.g. Crosses worldwide via UPS account #123. Canada DDP forwarded to Toronto via Canpar/Obibox." class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">{{ old('operational_notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
            <label for="is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">Agency is actively accepting shipments and inbound manifests</label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('international.agencies.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-amber-600/30 transition">
                <i class="fas fa-save"></i> Save Partner Agency
            </button>
        </div>
    </form>
</div>
@endsection
