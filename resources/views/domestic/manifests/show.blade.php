@extends('layouts.app')

@section('title', 'Manifest Details')
@section('page-title', '📦 Manifest Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Manifest #{{ $manifest->manifest_number }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Created: {{ $manifest->created_at->format('M d, Y H:i') }} | 
                    Status: <span class="px-2 py-1 rounded-full text-xs font-medium {{ $manifest->status_badge }}">{{ $manifest->status_label }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('domestic.manifests.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <button onclick="window.print()" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Manifest Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Load Type</p>
                    <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $manifest->load_type)) }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Total Bags</p>
                    <p class="font-semibold">{{ $manifest->total_bags }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Total Shipments</p>
                    <p class="font-semibold">{{ $manifest->total_shipments }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Total Weight</p>
                    <p class="font-semibold">{{ number_format($manifest->total_weight, 2) }} kg</p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Origin</p>
                    <p class="font-semibold">{{ $manifest->origin_city ?? 'N/A' }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Destination</p>
                    <p class="font-semibold">{{ $manifest->destination_city ?? 'N/A' }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Partner</p>
                    <p class="font-semibold">{{ $manifest->partner->name ?? 'N/A' }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-xs text-gray-500">Created By</p>
                    <p class="font-semibold">{{ $manifest->creator->name ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Bags Section -->
            <div class="border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">🛍️ Bags</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($manifest->bags as $bag)
                        <div class="border rounded-lg p-4 {{ $bag->status === 'scanned' ? 'bg-blue-50 border-blue-200' : ($bag->status === 'sorted' ? 'bg-purple-50 border-purple-200' : ($bag->status === 'dispatched' ? 'bg-green-50 border-green-200' : '')) }}">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">{{ $bag->bag_number }}</p>
                                    <p class="text-xs text-gray-500">Type: {{ ucfirst(str_replace('_', ' ', $bag->bag_type)) }}</p>
                                    <p class="text-xs text-gray-500">Shipments: {{ $bag->shipment_count }}</p>
                                    <p class="text-xs text-gray-500">Weight: {{ number_format($bag->weight, 2) }} kg</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $bag->status_badge }}">
                                        {{ ucfirst($bag->status) }}
                                    </span>
                                    <div class="mt-1">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ $bag->qr_code }}" 
                                             alt="QR Code" class="w-16 h-16 inline-block">
                                        <p class="text-xs text-gray-500">Scan QR</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button onclick="scanBag('{{ $bag->qr_code }}', 'receive')" class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700 transition">
                                    <i class="fas fa-check mr-1"></i> Receive
                                </button>
                                <button onclick="scanBag('{{ $bag->qr_code }}', 'sort')" class="bg-purple-600 text-white px-2 py-1 rounded text-xs hover:bg-purple-700 transition">
                                    <i class="fas fa-sort mr-1"></i> Sort
                                </button>
                                <button onclick="scanBag('{{ $bag->qr_code }}', 'dispatch')" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700 transition">
                                    <i class="fas fa-paper-plane mr-1"></i> Dispatch
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipments Section -->
            <div class="border-t pt-4 mt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">📦 Shipments</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Tracking #</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Receiver</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Bag</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Delivery Type</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manifest->shipments as $manifestShipment)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3 font-mono text-sm">{{ $manifestShipment->shipment->tracking_number ?? 'N/A' }}</td>
                                    <td class="py-2 px-3">{{ $manifestShipment->shipment->receiver_name ?? 'N/A' }}</td>
                                    <td class="py-2 px-3">{{ $manifestShipment->bag->bag_number ?? 'N/A' }}</td>
                                    <td class="py-2 px-3">{{ $manifestShipment->delivery_type_label }}</td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $manifestShipment->status_badge }}">
                                            {{ ucfirst($manifestShipment->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $manifestShipment->payment_status_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $manifestShipment->payment_status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tracking Logs -->
            <div class="border-t pt-4 mt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">📋 Tracking Logs</h3>
                <div class="max-h-60 overflow-y-auto">
                    <div class="space-y-2">
                        @foreach($manifest->trackingLogs->sortByDesc('created_at') as $log)
                            <div class="flex items-start gap-3 border-b pb-2">
                                <div class="w-2 h-2 mt-2 rounded-full 
                                    {{ $log->event_type === 'created' ? 'bg-blue-500' : 
                                       ($log->event_type === 'scanned' ? 'bg-yellow-500' : 
                                       ($log->event_type === 'sorted' ? 'bg-purple-500' : 
                                       ($log->event_type === 'dispatched' ? 'bg-indigo-500' : 
                                       ($log->event_type === 'received' ? 'bg-green-500' : 
                                       'bg-gray-500')))) }}">
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ ucfirst($log->event_type) }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $log->location ?? 'N/A' }} • {{ $log->created_at->diffForHumans() }}
                                        <span class="ml-2">by {{ $log->performedBy->name ?? 'System' }}</span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function scanBag(qrCode, action) {
    const location = prompt('Enter location:', '{{ $manifest->current_location ?? 'Kathmandu' }}');
    if (location === null) return;
    
    fetch('{{ route("domestic.manifests.scan-bag") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            qr_code: qrCode,
            action: action,
            location: location || 'Kathmandu'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Error: ' + error.message);
    });
}
</script>
@endsection