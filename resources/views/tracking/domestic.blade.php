@extends('layouts.public')

@section('title', 'Track Domestic Shipment - ' . $shipment->tracking_number)

@section('content')
@php
    $service = config('tracking.services.' . $shipment->service_type, config('tracking.services.default'));
    $status = config('tracking.statuses.' . $shipment->status, config('tracking.statuses.pending'));
    $events = $shipment->tracking_history ?: [[
        'status' => $shipment->status,
        'status_label' => $status['label'],
        'description' => $status['description'],
        'location' => $shipment->sender_city ?: 'Origin facility',
        'time' => $shipment->created_at->toIso8601String(),
    ]];
@endphp

<div class="space-y-6">
    <section class="rounded-2xl bg-gradient-to-r from-slate-900 to-teal-700 p-6 text-white shadow-xl md:p-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4"><span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 text-3xl"><i class="fas {{ $service['icon'] }}"></i></span><div><p class="text-sm font-semibold uppercase tracking-[.18em] text-teal-200">{{ $service['label'] }}</p><h1 class="mt-1 font-mono text-2xl font-bold md:text-3xl">{{ $shipment->tracking_number }}</h1><p class="mt-2 text-sm text-white/70">Last updated {{ $shipment->updated_at->diffForHumans() }}</p></div></div>
            <div class="rounded-xl bg-white/10 px-5 py-4"><p class="text-xs uppercase text-white/60">Current status</p><p class="mt-1 text-lg font-bold"><i class="fas {{ $status['icon'] }} mr-2 text-teal-200"></i>{{ $status['label'] }}</p></div>
        </div>
    </section>

    @if(in_array($shipment->status, ['failed_delivery', 'returned'], true))
        <section class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900"><p class="font-bold"><i class="fas fa-triangle-exclamation mr-2"></i>Attention may be required</p><p class="mt-1 text-sm">Please contact NETPACK support with the tracking number above to arrange the next step.</p></section>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center justify-between"><div><p class="text-sm font-semibold text-teal-700">Journey</p><h2 class="text-xl font-bold">Verified scan history</h2></div><span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">{{ count($events) }} updates</span></div>
            @foreach($events as $event)
                @php $eventInfo = config('tracking.statuses.' . ($event['status'] ?? ''), config('tracking.statuses.pending')); @endphp
                <article class="relative grid grid-cols-[40px_1fr] gap-4 pb-7 last:pb-0">
                    @unless($loop->last)<span class="absolute bottom-0 left-[19px] top-10 w-0.5 bg-slate-200"></span>@endunless
                    <span class="relative z-10 flex h-10 w-10 items-center justify-center rounded-full {{ $loop->last ? 'bg-teal-600' : 'bg-emerald-500' }} text-white"><i class="fas {{ $event['icon'] ?? $eventInfo['icon'] }}"></i></span>
                    <div class="rounded-xl border {{ $loop->last ? 'border-teal-200 bg-teal-50/50' : 'border-slate-200' }} p-4">
                        <div class="flex flex-col gap-1 sm:flex-row sm:justify-between"><h3 class="font-bold">{{ $event['status_label'] ?? $eventInfo['label'] }}</h3>@if(!empty($event['time']))<time class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($event['time'])->format('M d, Y · h:i A') }}</time>@endif</div>
                        @if(!empty($event['description']))<p class="mt-2 text-sm text-slate-600">{{ $event['description'] }}</p>@endif
                        @if(!empty($event['location']))<p class="mt-2 text-sm font-semibold text-slate-700"><i class="fas fa-location-dot mr-1 text-teal-600"></i>{{ $event['location'] }}</p>@endif
                    </div>
                </article>
            @endforeach
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-lg font-bold">Shipment summary</h2><dl class="mt-4 divide-y divide-slate-100 text-sm"><div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Service</dt><dd class="font-semibold"><i class="fas {{ $service['icon'] }} mr-1 text-teal-600"></i>{{ $service['label'] }}</dd></div><div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">From</dt><dd class="font-semibold">{{ $shipment->sender_city ?: 'Origin' }}</dd></div><div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">To</dt><dd class="font-semibold">{{ $shipment->receiver_city ?: 'Destination' }}</dd></div><div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Estimated</dt><dd class="text-right font-semibold">{{ $shipment->estimated_delivery_at?->format('M d, Y · h:i A') ?? 'Being scheduled' }}</dd></div></dl></section>
            <section class="rounded-2xl bg-slate-900 p-6 text-white"><h2 class="font-bold">Privacy-safe public view</h2><p class="mt-2 text-sm leading-6 text-slate-300">Addresses, contact details, payment data and documents are hidden.</p><a href="{{ route('tracking.page') }}" class="mt-4 inline-flex text-sm font-semibold text-teal-300"><i class="fas fa-search mr-2"></i>Track another</a></section>
        </aside>
    </div>
</div>
@endsection
