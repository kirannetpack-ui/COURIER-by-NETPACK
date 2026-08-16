@extends('layouts.partner')

@section('title', 'Rates')
@section('page-title', 'Service Rates')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Service Rates</h1>
                <p class="text-sm text-gray-500 mt-1">View and manage rates for all your delivery zones</p>
            </div>
            <a href="{{ route('partner.zones.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                <i class="fas fa-plus mr-2"></i> Add Zone
            </a>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($zones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Zone</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Service</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Base Rate (NPR)</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Per KG (NPR)</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Est. Time</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $zone)
                                @php
                                    $serviceTypes = [];
                                    if ($services['flash']['active']) $serviceTypes[] = 'flash';
                                    if ($services['same_day']['active']) $serviceTypes[] = 'same_day';
                                    if ($services['standard']['active']) $serviceTypes[] = 'standard';
                                    if ($services['himalayan']['active']) $serviceTypes[] = 'himalayan';
                                @endphp
                                
                                @foreach($serviceTypes as $serviceType)
                                    @php
                                        $service = $services[$serviceType];
                                        $rates = $zone->getServiceRates($serviceType);
                                        $rowColor = $loop->first ? 'bg-white' : 'bg-gray-50';
                                    @endphp
                                    <tr class="border-b hover:bg-gray-100 transition {{ $rowColor }}">
                                        @if($loop->first)
                                            <td class="py-3 px-4 font-medium" rowspan="{{ count($serviceTypes) }}">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-map text-teal-600"></i>
                                                    <span>{{ $zone->zone_name }}</span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ implode(', ', array_slice($zone->districts ?? [], 0, 3)) }}
                                                    @if(count($zone->districts ?? []) > 3)
                                                        +{{ count($zone->districts) - 3 }} more
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                {{ $service['color'] === 'red' ? 'bg-red-100 text-red-800' : 
                                                   ($service['color'] === 'orange' ? 'bg-orange-100 text-orange-800' : 
                                                   ($service['color'] === 'green' ? 'bg-green-100 text-green-800' : 
                                                   'bg-purple-100 text-purple-800')) }}">
                                                <i class="fas {{ $service['icon'] }} mr-1"></i>
                                                {{ $service['label'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 font-medium">
                                            Rs. {{ number_format($rates['base_rate'] ?? 0, 2) }}
                                        </td>
                                        <td class="py-3 px-4">
                                            Rs. {{ number_format($rates['per_kg_rate'] ?? 0, 2) }}
                                        </td>
                                        <td class="py-3 px-4">
                                            {{ $rates['estimated_hours'] ?? 'N/A' }}
                                            @if(isset($rates['estimated_hours']))
                                                <span class="text-xs text-gray-500">
                                                    {{ $rates['estimated_hours'] <= 24 ? 'hrs' : 'days' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($service['active'])
                                                <span class="text-xs text-green-600">● Active</span>
                                            @else
                                                <span class="text-xs text-red-600">● Inactive</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex gap-2">
                                                <a href="{{ route('partner.rates.edit', $zone->id) }}" 
                                                   class="text-teal-600 hover:text-teal-800" title="Edit Rates">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('partner.zones.show', $zone->id) }}" 
                                                   class="text-blue-600 hover:text-blue-800" title="View Zone">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Stats -->
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Total Zones</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $zones->count() }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Active Services</p>
                        @php
                            $activeServices = 0;
                            foreach ($services as $service) {
                                if ($service['active']) $activeServices++;
                            }
                        @endphp
                        <p class="text-2xl font-bold text-green-600">{{ $activeServices }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Total Districts</p>
                        @php
                            $totalDistricts = 0;
                            foreach ($zones as $zone) {
                                $totalDistricts += count($zone->districts ?? []);
                            }
                        @endphp
                        <p class="text-2xl font-bold text-purple-600">{{ $totalDistricts }}</p>
                    </div>
                    <div class="bg-teal-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Last Updated</p>
                        <p class="text-sm font-medium text-teal-600">
                            {{ $zones->max('updated_at') ? $zones->max('updated_at')->diffForHumans() : 'N/A' }}
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-money-bill-wave text-5xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-lg font-semibold text-gray-700">No Rates Found</h3>
                    <p class="text-gray-500 mt-2">You haven't created any zones with rates yet.</p>
                    <a href="{{ route('partner.zones.create') }}" class="inline-block mt-4 bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                        <i class="fas fa-plus mr-2"></i> Create Your First Zone
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection