@extends('layouts.app')

@section('title', 'Domestic Rates')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Domestic Rates</h1>
                <p class="text-sm text-gray-500 mt-1">Manage domestic delivery rates for FLASH, SAME DAY, STANDARD & HIMALAYAN services</p>
            </div>
            <a href="{{ route('admin.domestic.rates.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                <i class="fas fa-plus mr-2"></i> Add Rate
            </a>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" class="mb-5 grid grid-cols-1 gap-3 rounded-lg bg-gray-50 p-4 md:grid-cols-4">
                <select name="approval_status" class="rounded-lg border-gray-300"><option value="">All approval states</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('approval_status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                <select name="partner_id" class="rounded-lg border-gray-300"><option value="">All partners</option>@foreach($partners as $partner)<option value="{{ $partner->id }}" @selected(request('partner_id') == $partner->id)>{{ $partner->name }}</option>@endforeach</select>
                <select name="service_type" class="rounded-lg border-gray-300"><option value="">All services</option>@foreach($serviceTypes as $code => $service)<option value="{{ $code }}" @selected(request('service_type') === $code)>{{ $service['name'] }}</option>@endforeach</select>
                <button class="rounded-lg bg-slate-700 px-4 py-2 font-semibold text-white">Apply filters</button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Partner</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Service</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Rate Type</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">From Zone</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">To Zone</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Weight Range</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Base Rate</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="py-3 px-4">{{ $rate->partner->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-medium">{{ $rate->service_name }}</span>
                                    <span class="text-xs text-gray-500 block">{{ ucfirst($rate->service_type) }}</span>
                                </td>
                                <td class="py-3 px-4 text-sm">{{ ucfirst(str_replace('_', ' ', $rate->rate_type ?? 'door_to_door')) }}</td>
                                <td class="py-3 px-4">{{ $rate->originZone->zone_name ?? 'N/A' }}</td>
                                <td class="py-3 px-4">{{ $rate->destinationZone->zone_name ?? 'N/A' }}</td>
                                <td class="py-3 px-4">{{ $rate->weight_from }} - {{ $rate->weight_to }} kg</td>
                                <td class="py-3 px-4 font-medium">Rs. {{ number_format($rate->base_rate, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $rate->approval_status === 'approved' ? 'bg-green-100 text-green-800' : ($rate->approval_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($rate->approval_status ?? 'approved') }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        @if($rate->approval_status === 'pending')
                                            <form method="POST" action="{{ route('admin.domestic.rates.approve', $rate) }}">@csrf<button title="Approve" class="text-green-700"><i class="fas fa-check"></i></button></form>
                                            <form method="POST" action="{{ route('admin.domestic.rates.reject', $rate) }}" class="flex gap-1">@csrf<input name="rejection_reason" required minlength="5" placeholder="Reason" class="w-28 rounded border-gray-300 text-xs"><button title="Reject" class="text-red-700"><i class="fas fa-ban"></i></button></form>
                                        @endif
                                        <a href="{{ route('admin.domestic.rates.edit', $rate->id) }}" class="text-teal-600 hover:text-teal-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.domestic.rates.destroy', $rate->id) }}" class="inline" onsubmit="return confirm('Delete this rate?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-tachometer-alt text-4xl block mb-2"></i>
                                    No domestic rates found. Click "Add Rate" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $rates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
