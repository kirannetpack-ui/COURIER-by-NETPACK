@extends('layouts.public')

@section('title', 'Track Shipment & Air Waybill - COURIER with NETPACK')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-teal-950 to-teal-800 p-8 md:p-12 text-white shadow-2xl">
        <div class="relative z-10 max-w-2xl mx-auto text-center space-y-4">
            <span class="inline-flex items-center gap-2 rounded-full bg-teal-500/20 px-4 py-1.5 text-xs font-semibold text-teal-300 backdrop-blur border border-teal-400/30">
                <i class="fas fa-satellite-dish"></i> Live Logistics Intelligence · Nepal to Worldwide
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">
                Track & Trace Your Shipment
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                Enter your Tracking Number, regional House Air Waybill (HAWB), or E-Commerce Order ID for real-time milestone tracking.
            </p>

            <!-- Search Form -->
            <form method="GET" action="{{ route('tracking.search') }}" class="pt-4 max-w-xl mx-auto" id="trackingForm">
                <div class="relative flex flex-col sm:flex-row gap-2 bg-white/10 backdrop-blur-md p-2 rounded-2xl border border-white/20 shadow-inner">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-teal-300">
                            <i class="fas fa-barcode text-lg"></i>
                        </span>
                        <input type="text" name="tracking" id="trackingInput"
                               placeholder="e.g. NPI-2026-000001-4 or USNP-2026-001"
                               class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-900 font-mono font-semibold rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-400 uppercase text-sm md:text-base tracking-wide"
                               required
                               autocomplete="off"
                               autofocus>
                    </div>
                    <button type="submit"
                            class="bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-7 py-3.5 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-lg shadow-teal-500/30 shrink-0">
                        <i class="fas fa-magnifying-glass"></i>
                        <span>Track Now</span>
                    </button>
                </div>
            </form>

            <!-- Quick Test Chips -->
            <div class="pt-2 text-xs text-slate-300 flex flex-wrap items-center justify-center gap-2">
                <span class="text-slate-400 font-medium">Quick samples:</span>
                <button type="button" onclick="fillTracking('NPI-2026-000001-4')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-teal-200 font-mono border border-white/10 transition">
                    NPI-2026-000001-4 (Intl)
                </button>
                <button type="button" onclick="fillTracking('USNP-2026-001')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-teal-200 font-mono border border-white/10 transition">
                    USNP-2026-001 (HAWB)
                </button>
                <button type="button" onclick="fillTracking('NPD-2026-000001-8')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-teal-200 font-mono border border-white/10 transition">
                    NPD-2026-000001-8 (Domestic)
                </button>
                <button type="button" onclick="fillTracking('NPE-2026-000001-6')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-teal-200 font-mono border border-white/10 transition">
                    NPE-2026-000001-6 (Ecom)
                </button>
            </div>
        </div>

        <!-- Background Ambient Glow -->
        <div class="absolute -bottom-16 -right-16 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -top-16 -left-16 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 3 Core Services Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- International -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:border-teal-500/40 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <span class="h-10 w-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg">
                    <i class="fas fa-plane-departure"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-900">International Air Cargo</h3>
                    <p class="text-xs text-slate-500">Air Waybill Tracking</p>
                </div>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Track global air freight and express courier dispatches from Nepal to USA, UK, Europe, Australia, and 50+ countries with automated HAWB records and customs clearance status.
            </p>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-sky-700">
                <span>Format: NPI-... / HAWB</span>
                <i class="fas fa-arrow-right"></i>
            </div>
        </div>

        <!-- Domestic -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:border-teal-500/40 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <span class="h-10 w-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">
                    <i class="fas fa-truck-fast"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-900">Domestic Express</h3>
                    <p class="text-xs text-slate-500">All 7 Provinces & 77 Districts</p>
                </div>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Real-time visibility across Flash, Same-Day Kathmandu Valley delivery, Standard inter-city transport, and remote Himalayan district deliveries with verified proof of delivery.
            </p>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-teal-700">
                <span>Format: NPD-YYYY-XXXXXX-C</span>
                <i class="fas fa-arrow-right"></i>
            </div>
        </div>

        <!-- E-Commerce -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:border-teal-500/40 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <span class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fas fa-motorcycle"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-900">E-Commerce & Riders</h3>
                    <p class="text-xs text-slate-500">Last-Mile Fulfillment & COD</p>
                </div>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Uber-style real-time rider GPS tracking for active parcels, milestone steppers, instant Cash on Delivery (COD) accounting, and verified delivery confirmations.
            </p>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-indigo-700">
                <span>Format: NPE-... / ORD-...</span>
                <i class="fas fa-arrow-right"></i>
            </div>
        </div>
    </div>

    <!-- Security & Privacy Guarantee -->
    <div class="bg-slate-100 rounded-2xl p-6 border border-slate-200 text-xs text-slate-600 flex flex-col sm:flex-row items-center gap-4">
        <span class="h-10 w-10 shrink-0 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-lg">
            <i class="fas fa-shield-halved"></i>
        </span>
        <div class="space-y-1">
            <p class="font-bold text-slate-800">Privacy-First Tracking Protection</p>
            <p class="leading-relaxed">
                In compliance with strict data protection guidelines, public tracking displays verified operational milestones, departure hubs, and destination cities without exposing sender/receiver telephone numbers, full personal street addresses, or private financial records.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function fillTracking(number) {
    const input = document.getElementById('trackingInput');
    input.value = number;
    input.focus();
}
</script>
@endpush
@endsection
