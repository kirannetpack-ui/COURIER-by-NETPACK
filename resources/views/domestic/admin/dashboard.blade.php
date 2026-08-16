@extends('layouts.app')

@section('title', 'Domestic & E-commerce Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-teal-600 to-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <h1 class="text-2xl font-bold">Domestic & E-commerce Dashboard</h1>
        <p class="text-teal-100 mt-1">Manage domestic delivery and e-commerce services</p>
    </div>

    <!-- Domestic Stats -->
    <h3 class="text-lg font-semibold text-gray-700 mb-3">Domestic Services</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Partners</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($domesticStats['total_partners'] ?? 0) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Shipments</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($domesticStats['total_domestic_shipments'] ?? 0) }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Pending Pickups</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($domesticStats['pending_pickups'] ?? 0) }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Rates</p>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($domesticStats['total_rates'] ?? 0) }}</p>
        </div>
    </div>

    <!-- E-commerce Stats -->
    <h3 class="text-lg font-semibold text-gray-700 mb-3">E-commerce Services</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-pink-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Sellers</p>
            <p class="text-2xl font-bold text-pink-600">{{ number_format($ecommerceStats['total_sellers'] ?? 0) }}</p>
        </div>
        <div class="bg-indigo-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Products</p>
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($ecommerceStats['total_products'] ?? 0) }}</p>
        </div>
        <div class="bg-orange-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Orders</p>
            <p class="text-2xl font-bold text-orange-600">{{ number_format($ecommerceStats['total_orders'] ?? 0) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Revenue</p>
            <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($ecommerceStats['total_revenue'] ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('domestic.partners') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-handshake text-2xl text-teal-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Manage Partners</span>
        </a>
        <a href="{{ route('domestic.shipments') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-truck text-2xl text-blue-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">View Shipments</span>
        </a>
        <a href="{{ route('domestic.sellers') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-store text-2xl text-purple-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Manage Sellers</span>
        </a>
        <a href="{{ route('domestic.orders') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-shopping-cart text-2xl text-orange-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">View Orders</span>
        </a>
    </div>
</div>
@endsection