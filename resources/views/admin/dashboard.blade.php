@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')


<!-- Pending Users Alert -->
@php
    $pendingCount = App\Models\User::where('verification_status', 'pending')->count();
@endphp

@if($pendingCount > 0)
    <div class="bg-yellow-50 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
        <div>
            <i class="fas fa-clock mr-2"></i>
            <strong>{{ $pendingCount }}</strong> user(s) pending approval.
        </div>
        <a href="/admin/pending-users" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
            Review Now
        </a>
    </div>
@endif

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link active flex items-center space-x-3 px-4 py-3 text-sm">
        <i class="fas fa-home w-5"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-users w-5"></i>
        <span>Users</span>
    </a>
    <a href="{{ route('admin.partners.index') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-handshake w-5"></i>
        <span>Partners</span>
    </a>
    <a href="{{ route('admin.overseas-partners.index') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-globe w-5"></i>
        <span>Overseas Partners</span>
    </a>
    <a href="{{ route('admin.products.index') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-boxes w-5"></i>
        <span>Products</span>
    </a>
    <a href="{{ route('admin.shipments') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-shipping-fast w-5"></i>
        <span>Shipments</span>
    </a>
    <a href="{{ route('admin.pickups') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-box w-5"></i>
        <span>Pickups</span>
    </a>
    <a href="{{ route('admin.analytics') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-chart-line w-5"></i>
        <span>Analytics</span>
    </a>
    <a href="{{ route('admin.settlements') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-coins w-5"></i>
        <span>Settlements</span>
    </a>
    <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center space-x-3 px-4 py-3 text-sm text-teal-200/80 hover:text-white">
        <i class="fas fa-cog w-5"></i>
        <span>Settings</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-2xl font-bold text-teal-600">{{ App\Models\User::count() }}</p>
                </div>
                <div class="bg-teal-100 rounded-full p-3">
                    <i class="fas fa-users text-teal-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Shipments</p>
                    <p class="text-2xl font-bold text-blue-600">{{ App\Models\Shipment::count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-shipping-fast text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Approvals</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ App\Models\User::where('verification_status', 'pending')->count() }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Revenue</p>
                    <p class="text-2xl font-bold text-green-600">Rs. 0</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-lg mb-4">Recent Users</h3>
            @php
                $recentUsers = App\Models\User::orderBy('created_at', 'desc')->take(5)->get();
            @endphp
            <div class="divide-y divide-gray-100">
                @foreach($recentUsers as $user)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-medium text-sm">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-{{ $user->verification_status === 'approved' ? 'green' : 'yellow' }}-100 text-{{ $user->verification_status === 'approved' ? 'green' : 'yellow' }}-700">
                            {{ ucfirst($user->verification_status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

<!-- Quick Actions -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <!-- ... existing cards ... -->
    
    <a href="#" onclick="openTrackingModal()" class="bg-white hover:bg-gray-50 rounded-xl shadow-sm p-4 text-center border border-gray-200 transition">
        <i class="fas fa-sync-alt text-2xl text-teal-600 block mb-2"></i>
        <span class="text-sm font-medium text-gray-700">Update Tracking</span>
    </a>
</div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-lg mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.users.index') }}" class="bg-teal-500 hover:bg-teal-600 text-white text-center py-3 rounded-lg transition">
                    <i class="fas fa-user-plus block text-xl mb-1"></i>
                    Manage Users
                </a>
                <a href="{{ route('admin.partners.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white text-center py-3 rounded-lg transition">
                    <i class="fas fa-handshake block text-xl mb-1"></i>
                    Manage Partners
                </a>
            </div>
        </div>
    </div>
</div>
@endsection