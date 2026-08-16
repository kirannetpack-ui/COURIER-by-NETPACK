@extends('layouts.app')

@section('title', 'Earnings')
@section('page-title', 'My Earnings')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total Earnings</p>
            <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($stats['total_earnings'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Today's Earnings</p>
            <p class="text-2xl font-bold text-blue-600">Rs. {{ number_format($stats['today_earnings'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">This Week</p>
            <p class="text-2xl font-bold text-purple-600">Rs. {{ number_format($stats['week_earnings'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-500">This Month</p>
            <p class="text-2xl font-bold text-orange-600">Rs. {{ number_format($stats['month_earnings'] ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Delivery Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">Total Deliveries</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_deliveries'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">Pending Deliveries</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending_deliveries'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">Rating</p>
            <p class="text-2xl font-bold text-teal-600">{{ number_format($rider->rating ?? 0, 1) }} ★</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">Wallet Balance</p>
            <p class="text-2xl font-bold text-purple-600">Rs. {{ number_format($walletBalance ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
            <a href="{{ route('rider.wallet') }}" class="text-sm text-teal-600 hover:underline">
                <i class="fas fa-wallet mr-1"></i> View Wallet
            </a>
        </div>
        <div class="p-4">
            @if(isset($transactions) && $transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Date</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Description</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Amount</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Type</th>
                                <th class="text-left py-2 px-3 text-sm font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3 text-sm">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-2 px-3">{{ $transaction->description }}</td>
                                    <td class="py-2 px-3 font-medium {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }} Rs. {{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-4xl block mb-2 text-gray-300"></i>
                    <p>No transactions yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4">
        <a href="{{ route('rider.wallet') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-wallet text-2xl text-teal-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">View Wallet</span>
        </a>
        <a href="{{ route('rider.orders.available') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-search text-2xl text-blue-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Find Orders</span>
        </a>
        <a href="{{ route('rider.orders.my') }}" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
            <i class="fas fa-tasks text-2xl text-purple-600 block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">My Deliveries</span>
        </a>
    </div>
</div>
@endsection