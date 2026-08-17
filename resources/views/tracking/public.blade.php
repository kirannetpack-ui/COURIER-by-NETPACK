@extends('layouts.public')

@section('title', 'Track Shipment - ' . $shipment->formatted_tracking_number)

@push('styles')
<style>
    @keyframes tracker-pulse {
        0% { box-shadow: 0 0 0 0 rgba(13, 148, 136, .35); }
        70% { box-shadow: 0 0 0 10px rgba(13, 148, 136, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 148, 136, 0); }
    }
    .tracker-active { animation: tracker-pulse 2s infinite; }
</style>
@endpush

@section('content')
@php
    $statusInfo = $shipment->tracking_status;
    $events = $shipment->tracking_history ?: [[
        'status' => $shipment->status,
        'status_label' => $statusInfo['label'],
        'description' => $statusInfo['description'],
        'location' => $shipment->sender_city ?: 'Nepal',
        'time' => $shipment->created_at->toIso8601String(),
    ]];
@endphp

<div class="space-y-6">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-teal-600 to-blue-600 p-6 text-white shadow-lg md:p-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-medium text-white/75">Shipment tracking</p>
                <h1 class="mt-1 text-2xl font-bold md:text-3xl">{{ $shipment->formatted_tracking_number }}</h1>
                <p class="mt-2 text-sm text-white/80">Last updated {{ $shipment->updated_at->diffForHumans() }}</p>
            </div>
            <div class="rounded-xl bg-white/15 px-5 py-4 backdrop-blur">
                <p class="text-xs uppercase tracking-wide text-white/70">Current status</p>
                <p class="mt-1 flex items-center gap-2 text-lg font-semibold">
                    <i class="fas {{ $statusInfo['icon'] }}"></i> {{ $statusInfo['label'] }}
                </p>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Service</p>
            <p class="mt-2 font-semibold text-slate-900">{{ ucfirst($shipment->service_type ?: 'Standard') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Origin</p>
            <p class="mt-2 font-semibold text-slate-900">{{ $shipment->sender_city ?: 'Nepal' }}, {{ $shipment->sender_country ?: 'Nepal' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Destination</p>
            <p class="mt-2 font-semibold text-slate-900">{{ $shipment->receiver_city ?: 'Destination' }}, {{ $shipment->receiver_country }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Estimated delivery</p>
            <p class="mt-2 font-semibold text-slate-900">{{ $shipment->estimated_delivery?->format('M d, Y') ?? 'To be updated' }}</p>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-teal-700">Journey</p>
                    <h2 class="text-xl font-bold text-slate-900">Tracking timeline</h2>
                </div>
                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">{{ count($events) }} updates</span>
            </div>

            <div class="space-y-0">
                @foreach($events as $index => $event)
                    @php $isCurrent = $index === count($events) - 1; @endphp
                    <article class="relative grid grid-cols-[36px_1fr] gap-4 pb-8 last:pb-0">
                        @if(!$loop->last)
                            <span class="absolute bottom-0 left-[17px] top-9 w-0.5 bg-slate-200"></span>
                        @endif
                        <span class="{{ $isCurrent ? 'tracker-active bg-teal-600' : 'bg-emerald-500' }} relative z-10 flex h-9 w-9 items-center justify-center rounded-full text-sm text-white">
                            <i class="fas {{ $isCurrent ? 'fa-location-dot' : 'fa-check' }}"></i>
                        </span>
                        <div class="rounded-xl border {{ $isCurrent ? 'border-teal-200 bg-teal-50/50' : 'border-slate-200' }} p-4">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="font-semibold text-slate-900">{{ $event['status_label'] ?? ucfirst(str_replace('_', ' ', $event['status'])) }}</h3>
                                @if(!empty($event['time']))
                                    <time class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($event['time'])->format('M d, Y · h:i A') }}</time>
                                @endif
                            </div>
                            @if(!empty($event['description']))<p class="mt-2 text-sm text-slate-600">{{ $event['description'] }}</p>@endif
                            @if(!empty($event['location']))<p class="mt-2 text-sm font-medium text-slate-700"><i class="fas fa-location-dot mr-1 text-teal-600"></i>{{ $event['location'] }}</p>@endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Shipment summary</h2>
                <dl class="mt-4 divide-y divide-slate-100 text-sm">
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Tracking</dt><dd class="font-mono font-semibold text-slate-900">{{ $shipment->formatted_tracking_number }}</dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">HAWB</dt><dd class="font-mono text-slate-900">{{ $shipment->hawb_number ?: 'Not assigned' }}</dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Weight</dt><dd class="font-medium text-slate-900">{{ $shipment->chargeable_weight ?? $shipment->actual_weight }} kg</dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Booked</dt><dd class="font-medium text-slate-900">{{ $shipment->created_at->format('M d, Y') }}</dd></div>
                </dl>
                <p class="mt-4 rounded-lg bg-slate-50 p-3 text-xs leading-relaxed text-slate-500">
                    Personal addresses, phone numbers, payment information, and shipment documents are hidden on the public tracker.
                </p>
            </section>

            <section class="rounded-2xl bg-slate-900 p-6 text-white shadow-sm">
                <h2 class="font-semibold">Need another update?</h2>
                <p class="mt-2 text-sm text-slate-300">Use the same tracking number when contacting NETPACK support.</p>
                <div class="mt-5 grid gap-2">
                    <button type="button" onclick="window.print()" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-100"><i class="fas fa-print mr-2"></i>Print status</button>
                    <a href="{{ route('tracking.page') }}" class="rounded-lg border border-white/20 px-4 py-2 text-center text-sm font-semibold hover:bg-white/10"><i class="fas fa-search mr-2"></i>Track another</a>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
