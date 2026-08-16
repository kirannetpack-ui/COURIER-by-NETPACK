<!-- Customer Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0">
    <div class="p-4 border-b border-gray-700">
        <h2 class="text-xl font-bold text-teal-400">NetPack</h2>
        <p class="text-xs text-gray-400 mt-1">Customer Panel</p>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-chart-pie w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- Shipments -->
        <a href="{{ route('shipments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('shipments*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-truck w-5"></i>
            <span>My Shipments</span>
        </a>

        <!-- Tracking -->
        <a href="{{ route('tracking.page') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('tracking*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-search-location w-5"></i>
            <span>Track Shipment</span>
        </a>

        <!-- Grocery Box -->
        <a href="{{ route('grocery.box') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('grocery.box') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-shopping-basket w-5"></i>
            <span>Grocery Box</span>
        </a>

        <!-- Wallet -->
        <a href="{{ route('client.wallet') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('client.wallet') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-wallet w-5"></i>
            <span>My Wallet</span>
        </a>

        <!-- Feedback -->
        <a href="{{ route('client.feedback') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('client.feedback') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-comment w-5"></i>
            <span>Feedback</span>
        </a>

        <!-- Support -->
        <a href="{{ route('client.support') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('client.support') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-headset w-5"></i>
            <span>Support</span>
        </a>

        <!-- Settings -->
        <a href="{{ route('client.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('client.settings') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-cog w-5"></i>
            <span>Settings</span>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('profile') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-user-circle w-5"></i>
            <span>My Profile</span>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="block mt-4 pt-4 border-t border-gray-700">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition text-gray-300 hover:text-red-400">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</aside>
