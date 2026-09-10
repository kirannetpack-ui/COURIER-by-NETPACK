@extends('layouts.app')

@section('title', 'Global Transit Hubs | International Logistics')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 w-96 h-full opacity-10 pointer-events-none flex items-center justify-end pr-8">
            <i class="fas fa-network-wired text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        <i class="fas fa-globe-americas mr-1"></i> Global Gateway Hubs
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Active Routing Network
                    </span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">International Transit Hubs</h1>
                <p class="text-slate-300 text-sm mt-1 max-w-2xl">
                    Configure regional consolidation gateways (Dubai, UK/LHR, Australia/SYD, New Zealand/AKL). Add, edit, and tailor destinations, customs modes (DDP/DDU), and attached partner agencies.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('international.agencies.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-medium border border-slate-700 shadow-sm transition">
                    <i class="fas fa-building text-amber-400"></i> View Agencies
                </a>
                <a href="{{ route('international.hubs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i> Add New Hub
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Hubs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($hubs as $hub)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition flex flex-col justify-between overflow-hidden group">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 text-lg shadow-inner">
                            {{ $hub->code }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($hub->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Inactive</span>
                            @endif
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                {{ $hub->mode_type ?? 'HYBRID' }}
                            </span>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition">
                        {{ $hub->name }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-1">
                        <i class="fas fa-map-marker-alt text-rose-500"></i> {{ $hub->city ?? 'Gateway City' }}, {{ $hub->country ?? 'Global' }}
                    </p>

                    <div class="space-y-2 py-3 border-t border-b border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Partner Agencies:</span>
                            <span class="font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                {{ $hub->agencies_count ?? $hub->agencies->count() }} Attached
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Airports / Handlers:</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $hub->airport_name ?? 'Primary Airport' }}</span>
                        </div>
                    </div>

                    @if(!empty($hub->service_routes))
                        <div class="mt-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Route Scope</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach((array)$hub->service_routes as $route)
                                    <span class="text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                                        {{ $route }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('international.agencies.index', ['hub_id' => $hub->id]) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                        <i class="fas fa-users-cog"></i> Agencies
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('international.hubs.edit', $hub->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition" title="Edit Hub">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('international.hubs.destroy', $hub->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Hub? All related agencies will be affected.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition" title="Delete Hub">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center">
                <i class="fas fa-plane-slash text-4xl text-slate-400 mb-3"></i>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">No International Hubs Configured</h3>
                <p class="text-sm text-slate-500 mt-1">Get started by creating default regional hubs like Dubai, UK, Australia, and New Zealand.</p>
                <div class="mt-4">
                    <a href="{{ route('international.hubs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold">
                        <i class="fas fa-plus"></i> Add Initial Hub
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
