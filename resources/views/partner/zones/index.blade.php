@extends('layouts.partner')

@section('title', 'My Zones')
@section('page-title', 'My Delivery Zones')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">My Delivery Zones</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your delivery zones and rates</p>
                @if(auth()->user()->district)
                    <p class="text-xs text-teal-600 mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i> Operating District: <strong>{{ auth()->user()->district }}</strong>
                    </p>
                @endif
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

            @if(!auth()->user()->district)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Your operating district is not set. Please contact admin to set your district.
                </div>
            @endif

            @if($zones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Zone Name</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">District</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Wards</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Approval</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $zone)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="py-3 px-4 font-medium">{{ $zone->zone_name }}</td>
                                    <td class="py-3 px-4">{{ implode(', ', $zone->districts ?? []) }}</td>
                                    <td class="py-3 px-4">{{ implode(', ', $zone->wards ?? []) ?: 'All' }}</td>
                                    <td class="py-3 px-4">
                                        @if($zone->is_active)
                                            <span class="text-xs text-green-600">● Active</span>
                                        @else
                                            <span class="text-xs text-red-600">● Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($zone->approval_status === 'approved')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">✅ Approved</span>
                                        @elseif($zone->approval_status === 'rejected')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">❌ Rejected</span>
                                        @else
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">⏳ Pending</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('partner.zones.edit', $zone->id) }}" 
                                               class="text-teal-600 hover:text-teal-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('partner.zones.show', $zone->id) }}" 
                                               class="text-blue-600 hover:text-blue-800" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('partner.zones.destroy', $zone->id) }}" 
                                                  class="inline" onsubmit="return confirm('Delete this zone?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $zones->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-map text-5xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-lg font-semibold text-gray-700">No Zones Created Yet</h3>
                    <p class="text-gray-500 mt-2">Create your first delivery zone to start serving customers.</p>
                    @if(auth()->user()->district)
                        <a href="{{ route('partner.zones.create') }}" class="inline-block mt-4 bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                            <i class="fas fa-plus mr-2"></i> Create Your First Zone
                        </a>
                    @else
                        <p class="text-sm text-yellow-600 mt-2">Please contact admin to set your operating district first.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection