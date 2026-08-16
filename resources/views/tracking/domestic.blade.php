@extends('layouts.app')

@section('title', 'Track Domestic Shipment')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-center mb-6">
            <i class="fas fa-truck text-4xl text-teal-600 mb-2"></i>
            <h1 class="text-2xl font-bold text-gray-800">Domestic Shipment Tracking</h1>
            <p class="text-gray-500">Tracking #: {{ $shipment->tracking_number ?? 'N/A' }}</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded-lg p-4">
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-semibold">{{ ucfirst($shipment->status ?? 'Unknown') }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-sm text-gray-500">Service Type</p>
                <p class="font-semibold">{{ ucfirst($shipment->service_type ?? 'Standard') }}</p>
            </div>
        </div>
        
        <div class="mt-6">
            <a href="{{ route('tracking.page') }}" class="text-teal-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Track Another
            </a>
        </div>
    </div>
</div>
@endsection