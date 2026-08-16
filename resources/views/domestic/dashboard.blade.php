{{-- resources/views/domestic/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Flash Deliveries</p>
                    <p class="text-2xl font-bold">2-4 Hours</p>
                </div>
                <i class="fas fa-bolt text-3xl text-yellow-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Same Day</p>
                    <p class="text-2xl font-bold">By 8 PM</p>
                </div>
                <i class="fas fa-sun text-3xl text-orange-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Himalayan</p>
                    <p class="text-2xl font-bold">3-7 Days</p>
                </div>
                <i class="fas fa-mountain text-3xl text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Quick Pickup Request</h2>
            <a href="{{ route('domestic.pickup.create') }}" class="block w-full bg-teal-600 text-white text-center py-3 rounded-lg hover:bg-teal-700">
                <i class="fas fa-plus mr-2"></i> New Pickup Request
            </a>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Recent Requests</h2>
            @forelse($recentRequests ?? [] as $request)
                <div class="border-b py-2">
                    <p class="font-medium">#{{ $request->id }} - {{ ucfirst($request->status) }}</p>
                    <p class="text-sm text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-gray-500">No requests yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection