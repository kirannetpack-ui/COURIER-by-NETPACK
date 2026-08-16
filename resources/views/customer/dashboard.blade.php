{{-- resources/views/customer/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4">
        <h1 class="text-xl font-semibold text-white">Welcome, {{ $user->name }}! 👋</h1>
        <p class="text-teal-100 text-sm">Your dashboard to manage shipments and track orders</p>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <i class="fas fa-box-open text-3xl text-blue-600 mb-2"></i>
                <p class="text-2xl font-bold">{{ $user->shipmentsAsCustomer->count() }}</p>
                <p class="text-sm text-gray-600">Total Shipments</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <i class="fas fa-check-circle text-3xl text-green-600 mb-2"></i>
                <p class="text-2xl font-bold">{{ $user->shipmentsAsCustomer->where('status', 'delivered')->count() }}</p>
                <p class="text-sm text-gray-600">Delivered</p>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 text-center">
                <i class="fas fa-clock text-3xl text-yellow-600 mb-2"></i>
                <p class="text-2xl font-bold">{{ $user->shipmentsAsCustomer->whereIn('status', ['pending', 'in_transit'])->count() }}</p>
                <p class="text-sm text-gray-600">In Transit</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('grocery.box') }}" class="bg-teal-600 text-white p-4 rounded-xl text-center hover:bg-teal-700">
                <i class="fas fa-box-open mr-2"></i> Start Grocery Shopping
            </a>
            <a href="{{ route('tracking.page') }}" class="bg-blue-600 text-white p-4 rounded-xl text-center hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i> Track Shipment
            </a>
        </div>
    </div>
</div>
@endsection