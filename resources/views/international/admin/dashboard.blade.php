@extends('layouts.app')

@section('title', 'International Service Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-teal-600 to-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">International Service Dashboard</h1>
                <p class="text-teal-100 mt-1">Manage overseas partners, rates, and international shipments</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                    <i class="fas fa-globe mr-1"></i> Global Operations
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Overseas Partners</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['overseas_partners'] ?? 0) }}</p>
                </div>
                <i class="fas fa-handshake text-blue-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-green-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Base Rates</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['rates'] ?? 0) }}</p>
                </div>
                <i class="fas fa-file-invoice-dollar text-green-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Surcharges</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['surcharges'] ?? 0) }}</p>
                </div>
                <i class="fas fa-map-marker-alt text-yellow-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-purple-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">International Shipments</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['international_shipments'] ?? 0) }}</p>
                </div>
                <i class="fas fa-ship text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-indigo-50 rounded-lg p-3">
            <p class="text-xs text-gray-600">Additional Charges</p>
            <p class="text-xl font-bold text-indigo-600">{{ number_format($stats['additional_charges'] ?? 0) }}</p>
        </div>
        <div class="bg-pink-50 rounded-lg p-3">
            <p class="text-xs text-gray-600">Active Partners</p>
            <p class="text-xl font-bold text-pink-600">{{ number_format(\App\Models\User::where('user_type', 'overseas')->where('verification_status', 'approved')->count()) }}</p>
        </div>
        <div class="bg-teal-50 rounded-lg p-3">
            <p class="text-xs text-gray-600">Pending Partners</p>
            <p class="text-xl font-bold text-teal-600">{{ number_format(\App\Models\User::where('user_type', 'overseas')->where('verification_status', 'pending')->count()) }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('international.rates.create') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-upload text-2xl text-teal-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Upload Rate Sheet</span>
        </a>
        <a href="{{ route('international.partners') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-handshake text-2xl text-blue-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Manage Partners</span>
        </a>
        <a href="{{ route('international.shipments') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-ship text-2xl text-purple-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">View Shipments</span>
        </a>
        <a href="{{ route('international.surcharges') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-map-marker-alt text-2xl text-red-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Remote Surcharges</span>
        </a>
 <button onclick="openTrackingModal()" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
        <i class="fas fa-sync-alt text-2xl text-teal-600 block mb-2"></i>
        <span class="text-sm font-medium text-gray-700">Update Tracking</span>
    </button>
    </div>

    <!-- Recent Shipments -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Recent International Shipments</h3>
        </div>
        <div class="p-6">
            @php
                $recentShipments = \App\Models\Shipment::with(['customer', 'overseasPartner'])
                    ->whereNotNull('overseas_partner_id')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            @endphp
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Tracking</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Customer</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Destination</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Partner</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentShipments as $shipment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-3 font-mono text-sm">{{ $shipment->tracking_number ?? 'N/A' }}</td>
                                <td class="py-2 px-3">{{ $shipment->customer->name ?? 'N/A' }}</td>
                                <td class="py-2 px-3">{{ $shipment->receiver_country ?? 'N/A' }}</td>
                                <td class="py-2 px-3">{{ $shipment->overseasPartner->name ?? 'N/A' }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $shipment->status === 'delivered' ? 'green' : ($shipment->status === 'pending' ? 'yellow' : 'blue') }}-100 text-{{ $shipment->status === 'delivered' ? 'green' : ($shipment->status === 'pending' ? 'yellow' : 'blue') }}-800">
                                        {{ ucfirst($shipment->status ?? 'Unknown') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">No shipments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection