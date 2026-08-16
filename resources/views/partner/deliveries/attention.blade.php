@extends('layouts.partner')

@section('title', 'Attention Needed')
@section('page-title', '⚠️ Attention Needed')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">⚠️ Attention Needed</h1>
                <p class="text-sm text-gray-500 mt-1">Deliveries that need your immediate attention</p>
            </div>
            <a href="{{ route('partner.deliveries.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Deliveries
            </a>
        </div>

<!-- Pending Reminders Alert -->
@if(isset($pendingReminders) && $pendingReminders > 0)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
        <div>
            <i class="fas fa-bell mr-2"></i> 
            <span class="font-medium">{{ $pendingReminders }}</span> reminder(s) pending for delivery deadline!
        </div>
        <a href="{{ route('partner.deliveries.index') }}" class="bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-700">
            View All
        </a>
    </div>
@endif

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

            <!-- Delayed Deliveries -->
            <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2 text-red-600">
                <i class="fas fa-exclamation-triangle mr-2"></i> Delayed Deliveries
            </h3>
            
            @if(isset($delayedDeliveries) && $delayedDeliveries->count() > 0)
                <div class="overflow-x-auto mb-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">ID</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Customer</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Service</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Delay Reason</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($delayedDeliveries as $delivery)
                                <tr class="border-b hover:bg-red-50 transition">
                                    <td class="py-3 px-4 font-mono">#{{ $delivery->id }}</td>
                                    <td class="py-3 px-4">{{ $delivery->seller->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            {{ $delivery->service_tier === 'flash' ? 'bg-red-100 text-red-800' : 
                                               ($delivery->service_tier === 'same_day' ? 'bg-orange-100 text-orange-800' : 
                                               ($delivery->service_tier === 'standard' ? 'bg-blue-100 text-blue-800' : 
                                               'bg-purple-100 text-purple-800')) }}">
                                            {{ strtoupper($delivery->service_tier ?? 'Standard') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-red-600">{{ $delivery->delay_reason ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('partner.deliveries.show', $delivery->id) }}" 
                                               class="text-blue-600 hover:text-blue-800" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('partner.deliveries.report-delay', $delivery->id) }}" 
                                               class="text-red-600 hover:text-red-800" title="Report Delay">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i> No delayed deliveries found. Great job! 👍
                </div>
            @endif

            <!-- Deadline Approaching -->
            <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2 text-orange-600">
                <i class="fas fa-clock mr-2"></i> Approaching Deadline
            </h3>

            @if(isset($deadlineApproaching) && $deadlineApproaching->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">ID</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Customer</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Service</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Deadline</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deadlineApproaching as $delivery)
                                <tr class="border-b hover:bg-orange-50 transition">
                                    <td class="py-3 px-4 font-mono">#{{ $delivery->id }}</td>
                                    <td class="py-3 px-4">{{ $delivery->seller->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            {{ $delivery->service_tier === 'flash' ? 'bg-red-100 text-red-800' : 
                                               ($delivery->service_tier === 'same_day' ? 'bg-orange-100 text-orange-800' : 
                                               ($delivery->service_tier === 'standard' ? 'bg-blue-100 text-blue-800' : 
                                               'bg-purple-100 text-purple-800')) }}">
                                            {{ strtoupper($delivery->service_tier ?? 'Standard') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                               ($delivery->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-orange-600">
                                        {{ $delivery->scheduled_pickup_time ? \Carbon\Carbon::parse($delivery->scheduled_pickup_time)->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('partner.deliveries.show', $delivery->id) }}" 
                                               class="text-blue-600 hover:text-blue-800" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('partner.deliveries.report-delay', $delivery->id) }}" 
                                               class="text-red-600 hover:text-red-800" title="Report Delay">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i> No deliveries approaching deadline. All good! 👍
                </div>
            @endif
        </div>
    </div>
</div>
@endsection