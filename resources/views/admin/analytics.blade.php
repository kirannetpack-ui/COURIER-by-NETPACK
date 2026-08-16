@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Analytics Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-blue-600">Total Revenue</p>
            <p class="text-2xl font-bold">रू 0</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-green-600">Total Shipments</p>
            <p class="text-2xl font-bold">0</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm text-purple-600">Total Users</p>
            <p class="text-2xl font-bold">{{ \App\Models\User::count() }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-600">Success Rate</p>
            <p class="text-2xl font-bold">0%</p>
        </div>
    </div>
    
    <div class="text-center py-12 text-gray-400">
        <i class="fas fa-chart-line text-5xl mb-3"></i>
        <p>Analytics charts coming soon</p>
    </div>
</div>
@endsection