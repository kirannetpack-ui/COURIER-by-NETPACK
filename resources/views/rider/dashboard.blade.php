@extends('layouts.app')

@section('title', 'Rider Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-teal-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">🛵 Rider Dashboard</h1>
                <p class="text-blue-100 mt-1">Welcome back, {{ $rider->name }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                    <i class="fas fa-star text-yellow-400 mr-1"></i> {{ number_format($rating, 1) }} ★
                </span>
                <form method="POST" action="{{ route('rider.toggle-status') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg font-semibold transition
                        {{ $rider->is_online ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                        <i class="fas {{ $rider->is_online ? 'fa-circle' : 'fa-circle' }} mr-2"></i>
                        {{ $rider->is_online ? 'Go Offline' : 'Go Online' }}
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-2 text-sm">
            <span class="px-2 py-1 rounded-full {{ $rider->is_online ? 'bg-green-400' : 'bg-gray-400' }}">
                {{ $rider->is_online ? '🟢 Online' : '🔴 Offline' }}
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Available Orders</p>
            <p class="text-2xl font-bold text-blue-600">{{ $availableOrders }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Active Deliveries</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $activeDeliveries }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Today's Earnings</p>
            <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($todayEarnings, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Wallet Balance</p>
            <p class="text-2xl font-bold text-purple-600">Rs. {{ number_format($balance, 2) }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('rider.orders.available') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-search text-2xl text-blue-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Find Orders</span>
            @if($availableOrders > 0)
                <span class="ml-1 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $availableOrders }}</span>
            @endif
        </a>
        <a href="{{ route('rider.orders.my') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-tasks text-2xl text-teal-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">My Deliveries</span>
            @if($activeDeliveries > 0)
                <span class="ml-1 bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $activeDeliveries }}</span>
            @endif
        </a>
        <a href="{{ route('rider.earnings') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-money-bill-wave text-2xl text-green-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Earnings</span>
        </a>
        <a href="{{ route('rider.history') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-history text-2xl text-purple-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">History</span>
        </a>
    </div>

    <!-- Recent Deliveries -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Deliveries</h3>
            <a href="{{ route('rider.orders.my') }}" class="text-sm text-teal-600 hover:underline">View All</a>
        </div>
        <div class="p-4">
            @if($recentDeliveries->count() > 0)
                <div class="space-y-3">
                    @foreach($recentDeliveries as $delivery)
                        <div class="flex items-center justify-between border-b pb-3">
                            <div>
                                <p class="font-medium">#{{ $delivery->order->order_number ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $delivery->recipient_name }}</p>
                                <p class="text-xs text-gray-400">{{ $delivery->address }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $delivery->status_badge }}">
                                    {{ $delivery->status_label }}
                                </span>
                                <p class="text-sm font-medium text-teal-600 mt-1">Rs. {{ number_format($delivery->delivery_fee, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-truck text-4xl block mb-2 text-gray-300"></i>
                    <p>No recent deliveries</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Live Location -->
    <div class="mt-6 bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">📍 Live Location</h3>
                <p class="text-sm text-gray-500">Your current location is being tracked</p>
            </div>
            <button onclick="updateLocation()" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm">
                <i class="fas fa-sync-alt mr-2"></i> Update Location
            </button>
        </div>
        <div id="location-status" class="mt-2 text-sm text-gray-500">
            <i class="fas fa-circle text-green-500 text-xs mr-1"></i>
            Last updated: {{ $rider->last_location_update ? $rider->last_location_update->diffForHumans() : 'Never' }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                
                fetch('{{ route("rider.update-location") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('location-status').innerHTML = 
                            '<i class="fas fa-circle text-green-500 text-xs mr-1"></i> Location updated successfully!';
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                });
            },
            function(error) {
                alert('Unable to get location. Please enable GPS.');
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Auto update location every 30 seconds
setInterval(updateLocation, 30000);
</script>
@endpush
@endsection