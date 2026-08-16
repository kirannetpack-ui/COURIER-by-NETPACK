<!-- Seller Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0" x-show="sidebarOpen" x-transition>
    <div class="p-4 border-b border-gray-700">
        <h2 class="text-xl font-bold text-teal-400">NetPack Seller</h2>
        <p class="text-xs text-gray-400 mt-1">Seller Panel</p>
    </div>
    
    <!-- User Info -->
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-white"></i>
            </div>
            <div>
                <p class="font-medium text-sm">{{ auth()->user()->name ?? 'Seller' }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-chart-pie w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- Products -->
        <a href="{{ route('seller.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.products*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-box w-5"></i>
            <span>Products</span>
            @php
                $productCount = \App\Models\Product::where('user_id', auth()->id())->count();
            @endphp
            @if($productCount > 0)
                <span class="ml-auto bg-blue-600 text-xs px-2 py-1 rounded-full">{{ $productCount }}</span>
            @endif
        </a>

        <!-- Orders -->
        <a href="{{ route('seller.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.orders*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-shopping-cart w-5"></i>
            <span>Orders</span>
            @php
                $orderCount = \App\Models\Order::where('seller_id', auth()->id())->where('status', 'pending')->count();
            @endphp
            @if($orderCount > 0)
                <span class="ml-auto bg-yellow-600 text-xs px-2 py-1 rounded-full">{{ $orderCount }}</span>
            @endif
        </a>

        <!-- Shipments -->
        <a href="{{ route('seller.shipments') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.shipments*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-truck w-5"></i>
            <span>Shipments</span>
        </a>

        <!-- Earnings -->
        <a href="{{ route('seller.earnings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.earnings*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-money-bill-wave w-5"></i>
            <span>Earnings</span>
        </a>

        <!-- Wallet -->
        <a href="{{ route('seller.wallet') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.wallet*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-wallet w-5"></i>
            <span>Wallet</span>
        </a>

        <!-- Withdraw -->
        <a href="{{ route('seller.withdraw') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.withdraw*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-hand-holding-usd w-5"></i>
            <span>Withdraw</span>
        </a>

        <!-- Support -->
        <a href="{{ route('seller.support') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.support*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-headset w-5"></i>
            <span>Support</span>
        </a>

        <!-- Settings -->
        <a href="{{ route('seller.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('seller.settings*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
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