{{-- resources/views/overseas/dashboard.blade.php --}}
@extends('layouts.overseas')

@section('title', 'Overseas Partner Dashboard')

@section('content')
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-globe-asia text-teal-600"></i>
            <span>Overseas Partner Dashboard</span>
        </h1>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-600 rounded-xl p-4 text-white">
                <p class="text-sm opacity-90">Arrived Shipments</p>
                <p class="text-2xl font-bold">{{ $stats['arrived'] ?? 0 }}</p>
            </div>
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-4 text-white">
                <p class="text-sm opacity-90">Departed Shipments</p>
                <p class="text-2xl font-bold">{{ $stats['departed'] ?? 0 }}</p>
            </div>
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-4 text-white">
                <p class="text-sm opacity-90">Customs Hold</p>
                <p class="text-2xl font-bold">{{ $stats['customs'] ?? 0 }}</p>
            </div>
            <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-4 text-white">
                <p class="text-sm opacity-90">Total Processed</p>
                <p class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('overseas.scan') }}" class="bg-teal-50 border border-teal-200 rounded-xl p-6 text-center hover:bg-teal-100 transition">
                <i class="fas fa-qrcode text-4xl text-teal-600 mb-3"></i>
                <h3 class="font-semibold">Scan QR Code</h3>
                <p class="text-sm text-gray-500">Mark arrival, departure, customs clearance</p>
            </a>
            <a href="{{ route('overseas.shipments') }}" class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center hover:bg-blue-100 transition">
                <i class="fas fa-list text-4xl text-blue-600 mb-3"></i>
                <h3 class="font-semibold">View Shipments</h3>
                <p class="text-sm text-gray-500">Track all international shipments</p>
            </a>
        </div>
    </div>
</div>
@endsection