@extends('layouts.partner')

@section('title', 'Edit Rates')
@section('page-title', 'Edit Service Rates')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Edit Rates</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Zone: <span class="font-semibold text-teal-600">{{ $zone->zone_name }}</span>
                    <span class="text-xs text-gray-400 ml-2">({{ $zone->zone_code }})</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('partner.rates.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Rates
                </a>
                <a href="{{ route('partner.zones.edit', $zone->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-edit mr-2"></i> Edit Zone
                </a>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Alert -->
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-4 flex items-start gap-3">
                <i class="fas fa-info-circle mt-1"></i>
                <div>
                    <p class="font-medium">Rate Changes will be Notified to Admins</p>
                    <p class="text-sm">Any changes you make to rates will be automatically notified to the administrators for review.</p>
                </div>
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt text-teal-600 mr-2"></i>
                    <strong>Districts Covered:</strong> 
                    {{ implode(', ', $zone->districts ?? []) }}
                </p>
            </div>

            <form method="POST" action="{{ route('partner.rates.update', $zone->id) }}">
                @csrf
                @method('PUT')

                @foreach($services as $serviceKey => $service)
                    @if($service['active'])
                        <div class="bg-{{ $service['color'] }}-50 rounded-lg p-4 mb-4 border border-{{ $service['color'] }}-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-{{ $service['color'] }}-800">
                                    <i class="fas {{ $service['icon'] }} mr-2"></i>
                                    {{ $service['label'] }} Service
                                    <span class="text-xs text-gray-500 font-normal">(Active)</span>
                                </h4>
                                <span class="text-xs bg-{{ $service['color'] }}-200 text-{{ $service['color'] }}-700 px-2 py-1 rounded-full">
                                    Active
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Base Rate (NPR)</label>
                                    <input type="number" name="{{ $serviceKey }}_base_rate" step="0.01" 
                                           value="{{ old($serviceKey . '_base_rate', $service['rates']['base_rate'] ?? 0) }}" 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-{{ $service['color'] }}-500">
                                    <p class="text-xs text-gray-500 mt-1">Fixed charge per delivery</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Per KG Rate (NPR)</label>
                                    <input type="number" name="{{ $serviceKey }}_per_kg_rate" step="0.01" 
                                           value="{{ old($serviceKey . '_per_kg_rate', $service['rates']['per_kg_rate'] ?? 0) }}" 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-{{ $service['color'] }}-500">
                                    <p class="text-xs text-gray-500 mt-1">Additional charge per KG</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Estimated Time</label>
                                    <input type="number" name="{{ $serviceKey }}_estimated_hours" 
                                           value="{{ old($serviceKey . '_estimated_hours', $service['rates']['estimated_hours'] ?? '') }}" 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-{{ $service['color'] }}-500"
                                           placeholder="{{ $service['label'] === 'STANDARD' || $service['label'] === 'HIMALAYAN' ? 'Hours' : 'Hours' }}">
                                    <p class="text-xs text-gray-500 mt-1">Estimated delivery time in hours</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200 opacity-60">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-500">
                                    <i class="fas {{ $service['icon'] }} mr-2"></i>
                                    {{ $service['label'] }} Service
                                    <span class="text-xs text-gray-400 font-normal">(Not Active)</span>
                                </h4>
                                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">
                                    Inactive
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-500">Base Rate (NPR)</label>
                                    <input type="number" name="{{ $serviceKey }}_base_rate" step="0.01" 
                                           value="0" disabled
                                           class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-500">
                                    <p class="text-xs text-gray-400 mt-1">Contact admin to activate this service</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-500">Per KG Rate (NPR)</label>
                                    <input type="number" name="{{ $serviceKey }}_per_kg_rate" step="0.01" 
                                           value="0" disabled
                                           class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-500">Estimated Time</label>
                                    <input type="number" name="{{ $serviceKey }}_estimated_hours" 
                                           value="" disabled
                                           class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-500">
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="flex gap-3 pt-4 border-t mt-6">
                    <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                        <i class="fas fa-save mr-2"></i> Update Rates
                    </button>
                    <a href="{{ route('partner.rates.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection