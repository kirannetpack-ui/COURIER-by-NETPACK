@extends('layouts.app')

@section('title', 'Shipment Not Found')

@section('content')
<div class="max-w-2xl mx-auto text-center py-12">
    <div class="bg-white rounded-xl shadow-sm p-8">
        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Shipment Not Found</h1>
        <p class="text-gray-600 mb-4">
            We couldn't find any shipment with tracking number: 
            <strong>{{ $trackingNumber ?? 'N/A' }}</strong>
        </p>
        <p class="text-sm text-gray-500 mb-6">Please check the tracking number and try again.</p>
        <a href="{{ route('tracking.page') }}" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Tracking
        </a>
    </div>
</div>
@endsection