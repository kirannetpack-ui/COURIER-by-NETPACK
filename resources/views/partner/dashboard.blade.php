@extends('layouts.partner')

@section('title', 'Partner Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome, {{ $partner->name }}</h1>
                <p class="text-gray-500 mt-1">Partner Dashboard - Manage your delivery zones and rates</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    <i class="fas fa-circle text-green-500 text-xs mr-1"></i> Active
                </span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    <i class="fas fa-star text-yellow-500 text-xs mr-1"></i> {{ $partner->rating ?? 5.0 }} ★
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Pickups</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_pickups']) }}</p>
                </div>
                <i class="fas fa-box text-blue-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Pickups</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending_pickups']) }}</p>
                </div>
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-green-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['completed_pickups']) }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-purple-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Delivery Zones</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_zones']) }}</p>
                </div>
                <i class="fas fa-map text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>

   <!-- Quick Actions -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('partner.zones.index') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
        <i class="fas fa-map text-2xl text-teal-600 block mb-2"></i>
        <span class="text-sm font-medium text-gray-700">Manage Zones</span>
    </a>
    
    <a href="{{ route('partner.rates.index') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
        <i class="fas fa-money-bill-wave text-2xl text-blue-600 block mb-2"></i>
        <span class="text-sm font-medium text-gray-700">Manage Rates</span>
    </a>
    
    <a href="{{ route('partner.scan') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
        <i class="fas fa-qrcode text-2xl text-purple-600 block mb-2"></i>
        <span class="text-sm font-medium text-gray-700">Scan Delivery</span>
    </a>
    
    <a href="{{ route('partner.deliveries.index') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
        <i class="fas fa-truck text-2xl text-orange-600 block mb-2"></i>
        <span class="text-sm font-medium text-gray-700">View Deliveries</span>
    </a>
</div>

<!-- Zones Overview -->
<div class="bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Delivery Zones</h3>
        <a href="{{ route('partner.zones.index') }}" class="text-sm text-teal-600 hover:underline">View All</a>
    </div>
    <div class="p-4">
        @forelse($zones as $zone)
            <div class="flex items-center justify-between border-b py-2">
                <div>
                    <span class="font-medium">{{ $zone->zone_name }}</span>
                    <span class="text-xs text-gray-500 ml-2">{{ $zone->zone_type_label ?? $zone->zone_type }}</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-xs text-gray-500">
                        {{ ($zone->origin_rates_count ?? 0) + ($zone->destination_rates_count ?? 0) }} rates
                    </span>
                    @if($zone->is_active)
                        <span class="text-xs text-green-600">● Active</span>
                    @else
                        <span class="text-xs text-red-600">● Inactive</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-map text-2xl block mb-2"></i>
                No zones created yet
                <br>
                <a href="{{ route('partner.zones.create') }}" class="text-teal-600 hover:underline text-sm">
                    Create your first zone
                </a>
            </div>
        @endforelse
    </div>
</div>


    <!-- Recent Pickups -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Recent Pickup Requests</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">ID</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Customer</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Service</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Status</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPickups as $pickup)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-3 text-sm">#{{ $pickup->id }}</td>
                                <td class="py-2 px-3">{{ $pickup->seller->name ?? 'N/A' }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($pickup->service_tier ?? 'Standard') }}
                                    </span>
                                </td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $pickup->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $pickup->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $pickup->status === 'assigned' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $pickup->status === 'picked_up' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $pickup->status === 'in_transit' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $pickup->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $pickup->status)) }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-sm">{{ $pickup->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">No pickup requests found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection