@extends('layouts.app')

@section('title', 'Shipments')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b">
        <h1 class="text-xl font-semibold text-gray-800">Shipments</h1>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm">Tracking</th>
                        <th class="px-4 py-3 text-left text-sm">HAWB</th>
                        <th class="px-4 py-3 text-left text-sm">Customer</th>
                        <th class="px-4 py-3 text-left text-sm">Total</th>
                        <th class="px-4 py-3 text-left text-sm">Status</th>
                        <th class="px-4 py-3 text-left text-sm">Date</th>
                        <th class="px-4 py-3 text-left text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $shipment)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono">{{ $shipment->tracking_number }}</td>
                        <td class="px-4 py-3 font-mono">{{ $shipment->hawb_number }}</td>
                        <td class="px-4 py-3">{{ $shipment->customer->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">रू {{ number_format($shipment->total_amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($shipment->status == 'delivered') bg-green-100 text-green-800
                                @elseif($shipment->status == 'in_transit') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $shipment->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="text-teal-600">View</a>
                        </td>

<td class="py-3 px-4">
    <div class="flex gap-2">
        <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
            <i class="fas fa-eye"></i>
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <button onclick="openTrackingModal('{{ $shipment->id }}', '{{ $shipment->tracking_number }}')" 
                class="text-teal-600 hover:text-teal-800" title="Update Tracking">
            <i class="fas fa-sync-alt"></i>
        </button>
        @endif
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        @endif
    </div>
</td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No shipments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $shipments->links() }}
    </div>
</div>
@endsection