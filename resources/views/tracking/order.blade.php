@extends('layouts.public')

@section('title', 'Track E-Commerce Delivery - ' . $order->tracking_number)

@if($canViewLive)
    @push('styles')
        <style>@keyframes rider-pulse{0%{box-shadow:0 0 0 0 rgba(13,148,136,.45)}70%{box-shadow:0 0 0 12px rgba(13,148,136,0)}100%{box-shadow:0 0 0 0 rgba(13,148,136,0)}}.rider-pulse{animation:rider-pulse 2s infinite}.leaflet-container img.leaflet-tile,.leaflet-container img.leaflet-marker-icon{max-width:none!important}</style>
    @endpush
@endif

@section('content')
@php
    $service = config('tracking.services.ecommerce');
    $statusInfo = config('tracking.statuses.' . $order->status, config('tracking.statuses.pending'));
    $milestones = [
        ['label' => 'Order placed', 'icon' => 'fa-receipt', 'time' => $order->created_at],
        ['label' => 'Rider assigned', 'icon' => 'fa-user-check', 'time' => $order->rider_assigned_at],
        ['label' => 'Picked up', 'icon' => 'fa-box', 'time' => $order->picked_up_at],
        ['label' => 'Out for delivery', 'icon' => 'fa-motorcycle', 'time' => $order->out_for_delivery_at],
        ['label' => 'Delivered', 'icon' => 'fa-circle-check', 'time' => $order->delivered_at],
    ];
@endphp

<div class="space-y-6">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-teal-900 to-teal-700 p-6 text-white shadow-xl md:p-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-3xl"><i class="fas {{ $service['icon'] }}"></i></span>
                <div><p class="text-sm font-semibold uppercase tracking-[.18em] text-teal-200">{{ $service['label'] }}</p><h1 class="mt-1 font-mono text-2xl font-bold md:text-3xl">{{ $order->tracking_number }}</h1><p class="mt-2 text-sm text-white/70">Order {{ $order->order_number }} · Updated {{ $order->updated_at->diffForHumans() }}</p></div>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4">
                <p class="text-xs uppercase tracking-wide text-white/60">Current status</p><p class="mt-1 flex items-center gap-2 text-lg font-bold"><i class="fas {{ $statusInfo['icon'] }} text-teal-200"></i>{{ $statusInfo['label'] }}</p><p class="mt-1 max-w-xs text-xs text-white/70">{{ $statusInfo['description'] }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-2 sm:grid-cols-5">
            @foreach($milestones as $milestone)
                <div class="rounded-xl p-3 {{ $milestone['time'] ? 'bg-teal-50 text-teal-800' : 'bg-slate-50 text-slate-400' }}"><i class="fas {{ $milestone['icon'] }}"></i><p class="mt-2 text-xs font-bold">{{ $milestone['label'] }}</p><p class="mt-1 text-[11px]">{{ $milestone['time']?->format('M d · H:i') ?? 'Upcoming' }}</p></div>
            @endforeach
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(300px,1fr)]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <div><p class="text-sm font-semibold text-teal-700">Final-mile delivery</p><h2 class="text-xl font-bold text-slate-900">Live rider tracking</h2></div>
                <span id="liveBadge" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><i class="fas fa-satellite-dish mr-1"></i>Checking signal</span>
            </div>
            @if($canViewLive)
                <div id="liveMap" class="h-[430px] bg-slate-100"></div>
                <div class="grid gap-3 border-t border-slate-100 p-4 sm:grid-cols-3">
                    <div><p class="text-xs uppercase text-slate-400">Last GPS update</p><p id="lastUpdated" class="mt-1 text-sm font-semibold">Waiting for signal</p></div>
                    <div><p class="text-xs uppercase text-slate-400">Accuracy</p><p id="accuracy" class="mt-1 text-sm font-semibold">—</p></div>
                    <div><p class="text-xs uppercase text-slate-400">Automatic refresh</p><p class="mt-1 text-sm font-semibold">Every {{ config('tracking.live.refresh_seconds') }} seconds</p></div>
                </div>
            @else
                <div class="flex min-h-[360px] items-center justify-center bg-gradient-to-br from-slate-50 to-teal-50 p-8 text-center">
                    <div class="max-w-md"><span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-2xl text-teal-700 shadow"><i class="fas fa-location-crosshairs"></i></span><h3 class="mt-5 text-xl font-bold">Live location is privacy protected</h3><p class="mt-2 text-sm leading-6 text-slate-600">Public visitors see delivery milestones. Exact rider coordinates are limited to the customer, seller, assigned rider, and authorized NETPACK staff.</p><a href="{{ route('login') }}" class="mt-5 inline-flex rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700"><i class="fas fa-lock mr-2"></i>Sign in for live map</a></div>
                </div>
            @endif
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold">Delivery summary</h2>
                <dl class="mt-4 divide-y divide-slate-100 text-sm">
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Service</dt><dd class="font-semibold"><i class="fas {{ $service['icon'] }} mr-1 text-teal-600"></i>E-Commerce</dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Delivery window</dt><dd class="text-right font-semibold">{{ $order->delivery_time_slot ?: ($order->delivery_date?->format('M d, Y') ?? 'Being scheduled') }}</dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Progress</dt><dd class="font-semibold text-teal-700">{{ $order->progress }}%</dd></div>
                </dl>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-teal-500" style="width: {{ $order->progress }}%"></div></div>
            </section>
            <section class="rounded-2xl bg-slate-900 p-6 text-white"><h2 class="font-bold">Safe tracking by design</h2><p class="mt-2 text-sm leading-6 text-slate-300">Customer address, phone, payment details, and exact coordinates never appear publicly.</p><a href="{{ route('tracking.page') }}" class="mt-4 inline-flex text-sm font-semibold text-teal-300"><i class="fas fa-arrow-left mr-2"></i>Track another delivery</a></section>
        </aside>
    </div>
</div>
@endsection

@if($canViewLive)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const map = L.map('liveMap').setView([27.7172, 85.3240], 13);
    L.tileLayer(@json(config('tracking.live.tile_url')), {maxZoom: 19, attribution: @json(config('tracking.live.tile_attribution'))}).addTo(map);
    const riderIcon = L.divIcon({className:'',html:'<div class="rider-pulse" style="width:22px;height:22px;border:5px solid white;border-radius:99px;background:#0d9488"></div>',iconSize:[22,22],iconAnchor:[11,11]});
    const destinationIcon = L.divIcon({className:'',html:'<div style="width:20px;height:20px;border:5px solid white;border-radius:99px;background:#ef4444"></div>',iconSize:[20,20],iconAnchor:[10,10]});
    let riderMarker, destinationMarker, fitted = false;
    async function refresh() {
        const badge = document.getElementById('liveBadge');
        try {
            const response = await fetch(@json(route('tracking.orders.live', $order)), {headers:{Accept:'application/json'}});
            if (!response.ok) throw new Error();
            const data = (await response.json()).data;
            if (data.latitude === null || data.longitude === null) { badge.className='rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700'; badge.textContent='Awaiting rider signal'; return; }
            const point=[data.latitude,data.longitude];
            riderMarker=riderMarker?riderMarker.setLatLng(point):L.marker(point,{icon:riderIcon}).addTo(map).bindTooltip('Rider · live GPS');
            if(data.delivery.latitude!==null&&data.delivery.longitude!==null){const destination=[data.delivery.latitude,data.delivery.longitude];destinationMarker=destinationMarker||L.marker(destination,{icon:destinationIcon}).addTo(map).bindTooltip('Delivery destination');if(!fitted){map.fitBounds([point,destination],{padding:[50,50],maxZoom:16});fitted=true}}
            if(!fitted){map.setView(point,15);fitted=true}
            badge.className=`rounded-full px-3 py-1 text-xs font-semibold ${data.is_stale?'bg-amber-100 text-amber-700':'bg-emerald-100 text-emerald-700'}`;badge.textContent=data.is_stale?'Signal delayed':'● Live';
            document.getElementById('lastUpdated').textContent=data.last_updated?new Date(data.last_updated).toLocaleString():'Not available';document.getElementById('accuracy').textContent=data.accuracy?`Within ${Math.round(data.accuracy)} m`:'Not reported';
        } catch (_) { badge.className='rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700';badge.textContent='Temporarily unavailable'; }
    }
    refresh(); setInterval(refresh, {{ max(5, config('tracking.live.refresh_seconds')) * 1000 }});
});
</script>
@endpush
@endif
