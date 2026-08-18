@extends('layouts.public')

@section('title', 'Track Your Shipment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8 text-center">
        <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-truck text-3xl text-teal-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Track Your Shipment</h1>
        <p class="text-gray-600 mb-6">Enter your tracking number to get real-time status updates</p>

        <form method="GET" action="{{ route('tracking.search') }}" class="max-w-md mx-auto">
            <div class="flex gap-2">
                <input type="text" name="tracking" placeholder="Enter tracking number" 
                       class="flex-1 border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500"
                       required>
                <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-box text-teal-600"></i>
                    <h4 class="font-medium">Real-time Tracking</h4>
                </div>
                <p class="text-sm text-gray-600">Get live updates on your shipment location</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-bell text-teal-600"></i>
                    <h4 class="font-medium">Status Alerts</h4>
                </div>
                <p class="text-sm text-gray-600">Receive notifications on status changes</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-history text-teal-600"></i>
                    <h4 class="font-medium">Complete History</h4>
                </div>
                <p class="text-sm text-gray-600">View full shipment journey timeline</p>
            </div>
        </div>
    </div>
</div>
@endsection
