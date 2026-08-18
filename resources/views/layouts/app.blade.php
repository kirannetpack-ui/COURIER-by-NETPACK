<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NetPack')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>
<body>
    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        @auth
            @php
                $user = auth()->user();
                $sidebar = 'layouts.partials.default-sidebar';
                
                // Determine sidebar based on user type using helper methods
                if ($user->isSuperAdmin()) {
                    $sidebar = 'layouts.partials.admin-sidebar';
                } elseif ($user->isDomesticAdmin()) {
                    $sidebar = 'layouts.partials.domestic-sidebar';
                } elseif ($user->isInternationalAdmin()) {
                    $sidebar = 'layouts.partials.international-sidebar';
                } elseif ($user->isPartner()) {
                    $sidebar = 'layouts.partials.partner-sidebar';
                } elseif ($user->isSeller()) {
                    $sidebar = 'layouts.partials.seller-sidebar';
                } elseif ($user->isRider()) {
                    $sidebar = 'layouts.partials.rider-sidebar';
                } elseif ($user->isCustomer()) {
                    $sidebar = 'layouts.partials.customer-sidebar';
                } else {
                    $sidebar = 'layouts.partials.default-sidebar';
                }
            @endphp
            @include($sidebar)
        @endauth

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm px-6 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-500">{{ auth()->user()->name ?? '' }}</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Online</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
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

                @if(session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-4">
                        {{ session('info') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
