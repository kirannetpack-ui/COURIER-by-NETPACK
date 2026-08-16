@extends('layouts.seller')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-teal-600 to-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</h1>
                <p class="text-teal-100 mt-1">Manage your products, orders, and shipments</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                    <i class="fas fa-store text-teal-200 mr-1"></i> {{ Auth::user()->business_name ?? 'Seller' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Orders -->
        <div class="stat-card bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalOrders ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Last 30 days</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Earnings -->
        <div class="stat-card bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Earnings</p>
                    <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($totalEarnings ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">All time</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="stat-card bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">My Products</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $totalProducts ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $activeProducts ?? 0 }} active</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Shipments -->
        <div class="stat-card bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Shipments</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $totalShipments ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $pendingShipments ?? 0 }} pending</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-3">
            <p class="text-xs text-gray-500">This Week</p>
            <p class="text-xl font-bold text-teal-600">Rs. {{ number_format($weekEarnings ?? 0, 2) }}</p>
            <p class="text-xs text-gray-400">{{ $weekTransactions ?? 0 }} transactions</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <p class="text-xs text-gray-500">This Month</p>
            <p class="text-xl font-bold text-blue-600">Rs. {{ number_format($monthEarnings ?? 0, 2) }}</p>
            <p class="text-xs text-gray-400">{{ $monthTransactions ?? 0 }} transactions</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <p class="text-xs text-gray-500">Pending Orders</p>
            <p class="text-xl font-bold text-yellow-600">{{ $pendingOrders ?? 0 }}</p>
            <p class="text-xs text-gray-400">Need attention</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <p class="text-xs text-gray-500">Wallet Balance</p>
            <p class="text-xl font-bold text-purple-600">Rs. {{ number_format($walletBalance ?? 0, 2) }}</p>
            <p class="text-xs text-gray-400">
                <a href="{{ route('seller.wallet') }}" class="text-teal-600 hover:underline">View Wallet →</a>
            </p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('seller.products.index') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-plus-circle text-2xl text-teal-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Add Product</span>
        </a>
        <a href="{{ route('seller.orders.create') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-plus-circle text-2xl text-blue-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Create Order</span>
        </a>
        <a href="{{ route('seller.shipments.create') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-plus-circle text-2xl text-purple-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Create Shipment</span>
        </a>
        <a href="{{ route('seller.wallet') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-wallet text-2xl text-green-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">View Wallet</span>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">📋 Recent Orders</h3>
                <a href="{{ route('seller.orders') }}" class="text-sm text-teal-600 hover:underline">View All</a>
            </div>
            <div class="p-4">
                @if(isset($recentOrders) && $recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between border-b pb-2">
                                <div>
                                    <p class="font-medium text-sm">#{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->customer_name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-teal-600">Rs. {{ number_format($order->total_amount, 2) }}</p>
                                    <span class="text-xs px-2 py-0.5 rounded-full 
                                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                           ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                           'bg-blue-100 text-blue-800')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-shopping-cart text-3xl block mb-2 text-gray-300"></i>
                        <p>No recent orders</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">🚚 Recent Shipments</h3>
                <a href="{{ route('seller.shipments') }}" class="text-sm text-teal-600 hover:underline">View All</a>
            </div>
            <div class="p-4">
                @if(isset($recentShipments) && $recentShipments->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentShipments as $shipment)
                            <div class="flex items-center justify-between border-b pb-2">
                                <div>
                                    <p class="font-medium text-sm font-mono">{{ $shipment->tracking_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $shipment->receiver_name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs px-2 py-0.5 rounded-full 
                                        {{ $shipment->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                           ($shipment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($shipment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                           'bg-blue-100 text-blue-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">{{ $shipment->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-truck text-3xl block mb-2 text-gray-300"></i>
                        <p>No recent shipments</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <h4 class="text-sm font-semibold text-blue-700 mb-2">📦 Product Status</h4>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Products</span>
                <span class="font-bold">{{ $totalProducts ?? 0 }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Active</span>
                <span class="font-bold text-green-600">{{ $activeProducts ?? 0 }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Inactive</span>
                <span class="font-bold text-red-600">{{ ($totalProducts ?? 0) - ($activeProducts ?? 0) }}</span>
            </div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <h4 class="text-sm font-semibold text-green-700 mb-2">💰 Earnings Summary</h4>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Earnings</span>
                <span class="font-bold">Rs. {{ number_format($totalEarnings ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">This Month</span>
                <span class="font-bold text-teal-600">Rs. {{ number_format($monthEarnings ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">This Week</span>
                <span class="font-bold text-blue-600">Rs. {{ number_format($weekEarnings ?? 0, 2) }}</span>
            </div>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <h4 class="text-sm font-semibold text-purple-700 mb-2">📋 Order Summary</h4>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Orders</span>
                <span class="font-bold">{{ $totalOrders ?? 0 }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Pending</span>
                <span class="font-bold text-yellow-600">{{ $pendingOrders ?? 0 }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Completed</span>
                <span class="font-bold text-green-600">{{ ($totalOrders ?? 0) - ($pendingOrders ?? 0) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection