<!-- Rider Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0" x-show="sidebarOpen" x-transition>
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center">
                <i class="fas fa-motorcycle text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-teal-400">NetPack Rider</h2>
                <p class="text-xs text-gray-400">Rider Delivery Panel</p>
            </div>
        </div>
    </div>
    
    <!-- User Info -->
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-white"></i>
            </div>
            <div>
                <p class="font-medium text-sm">{{ auth()->user()->name ?? 'Rider' }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
        <!-- Deposit Balance -->
        <div class="mt-2 p-2 bg-gray-800 rounded-lg">
            <p class="text-xs text-gray-400">Deposit Balance</p>
            <p class="text-sm font-bold text-teal-400">Rs. {{ number_format(auth()->user()->rider_deposit_balance ?? 0, 2) }}</p>
        </div>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('rider.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('rider.dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-chart-pie w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- Available Orders -->
        <a href="{{ route('rider.orders.available') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('rider.orders.available') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-search w-5"></i>
            <span>Available Orders</span>
            @php
                $availableCount = \App\Models\Order::where('status', 'pending')->whereNull('rider_id')->count();
            @endphp
            @if($availableCount > 0)
                <span class="ml-auto bg-red-600 text-xs px-2 py-1 rounded-full">{{ $availableCount }}</span>
            @endif
        </a>

        <!-- My Orders -->
        <a href="{{ route('rider.orders.my') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('rider.orders.my') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-tasks w-5"></i>
            <span>My Deliveries</span>
            @php
                $activeCount = \App\Models\Order::where('rider_id', auth()->id())->whereIn('status', ['assigned', 'picked_up', 'in_transit', 'out_for_delivery'])->count();
            @endphp
            @if($activeCount > 0)
                <span class="ml-auto bg-yellow-600 text-xs px-2 py-1 rounded-full">{{ $activeCount }}</span>
            @endif
        </a>

        <!-- Wallet -->
        <a href="{{ route('rider.wallet') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('rider.wallet') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-wallet w-5"></i>
            <span>Wallet</span>
            @php
                $walletBalance = \App\Models\Wallet::where('user_id', auth()->id())->first();
            @endphp
            @if($walletBalance && $walletBalance->balance > 0)
                <span class="ml-auto bg-green-600 text-xs px-2 py-1 rounded-full">Rs. {{ number_format($walletBalance->balance, 0) }}</span>
            @endif
        </a>
<a href="{{ route('rider.deposit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition text-teal-400">
    <i class="fas fa-plus-circle w-5"></i>
    <span>Deposit Funds</span>
</a>

        <!-- Earnings -->
        <a href="{{ route('rider.earnings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('rider.earnings') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-money-bill-wave w-5"></i>
            <span>Earnings</span>
        </a>

        <!-- History -->
        <a href="{{ route('rider.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('rider.history') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-history w-5"></i>
            <span>History</span>
        </a>

        <!-- Settings -->
        <a href="{{ route('rider.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('rider.settings') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-cog w-5"></i>
            <span>Settings</span>
        </a>

        <!-- Logout -->
        <div class="pt-4 mt-4 border-t border-gray-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition text-gray-300 hover:text-red-400">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>