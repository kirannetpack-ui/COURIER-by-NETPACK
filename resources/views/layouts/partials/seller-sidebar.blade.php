<!-- Seller Sidebar -->
<aside class="w-64 bg-slate-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0 custom-scrollbar select-none" x-show="sidebarOpen" x-transition>
    <!-- Brand Header -->
    <div class="p-4 border-b border-slate-800 flex flex-col gap-2">
        <x-logo variant="white" size="sm" :href="route('seller.dashboard')" />
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 w-fit tracking-wider uppercase">
            <i class="fas fa-store text-[9px] text-emerald-400"></i> Merchant Portal
        </span>
    </div>
    
    <!-- User / Store Info Card -->
    <div class="p-3 mx-3 my-2 rounded-xl bg-slate-800/60 border border-slate-700/60">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-emerald-600 to-teal-500 rounded-xl flex items-center justify-center font-bold text-white shadow-sm flex-shrink-0">
                <i class="fas fa-shop text-sm"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-xs text-white truncate">{{ auth()->user()->business_name ?? auth()->user()->name ?? 'Merchant Store' }}</p>
                <p class="text-[11px] text-emerald-300/80 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
        @php
            $sellerWallet = \App\Models\Wallet::where('user_id', auth()->id())->first();
        @endphp
        <div class="mt-2 pt-2 border-t border-slate-700/50 flex items-center justify-between text-[11px]">
            <span class="text-slate-400">Available Wallet</span>
            <span class="font-bold font-mono text-emerald-400">Rs. {{ number_format($sellerWallet->balance ?? 0, 2) }}</span>
        </div>
    </div>

    <!-- Quick Dispatch Action Button -->
    <div class="px-3 py-1.5">
        <a href="{{ route('seller.orders.create') }}" 
           class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-md shadow-emerald-900/30 transition transform hover:-translate-y-0.5">
            <i class="fas fa-plus-circle text-sm"></i>
            <span>Book / Create Order</span>
        </a>
    </div>
    
    <nav class="p-3 space-y-1 text-xs">
        <!-- Dashboard Overview -->
        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('seller.dashboard') ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-chart-pie w-4 text-center"></i>
            <span>Dashboard</span>
        </a>

        <!-- ============================================== -->
        <!-- ORDERS & DISPATCH -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-widest px-3 mb-1">E-Commerce Orders</p>
            
            <!-- All Orders -->
            <a href="{{ route('seller.orders') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.orders') && !request()->routeIs('seller.orders.create') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-shopping-cart w-4 text-center text-amber-400"></i>
                <span>Orders Registry</span>
                @php
                    $pendingOrdersCount = \App\Models\Order::where('seller_id', auth()->id())->where('status', 'pending')->count();
                @endphp
                @if($pendingOrdersCount > 0)
                    <span class="ml-auto bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-mono font-bold px-1.5 py-0.5 rounded">
                        {{ $pendingOrdersCount }} Pending
                    </span>
                @endif
            </a>

            <!-- New Order Booking -->
            <a href="{{ route('seller.orders.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.orders.create') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-truck-ramp-box w-4 text-center text-emerald-400"></i>
                <span>Instant Order Booking</span>
            </a>

            <!-- Export Orders -->
            <a href="{{ route('seller.orders.export') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-slate-300 hover:bg-slate-800 hover:text-white">
                <i class="fas fa-file-export w-4 text-center text-sky-400"></i>
                <span>Export Orders (CSV)</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- SHIPMENTS & RADAR TRACKING -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-widest px-3 mb-1">Logistics & Tracking</p>
            
            <!-- Book Courier Shipment -->
            <a href="{{ route('seller.shipments.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.shipments.create') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-box-open w-4 text-center text-teal-400"></i>
                <span>Book Courier Parcel</span>
            </a>

            <!-- Rate Inquiry & Tariff Desk -->
            <a href="{{ route('rates.inquiry') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rates.inquiry*') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-calculator w-4 text-center text-amber-400"></i>
                <span>Rate Inquiry Desk</span>
            </a>

            <!-- My Shipments -->
            <a href="{{ route('seller.shipments') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.shipments') && !request()->routeIs('seller.shipments.create') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-truck-fast w-4 text-center text-indigo-400"></i>
                <span>Courier Shipments</span>
            </a>

            <!-- Live Radar Tracking -->
            <a href="{{ route('tracking.page') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-slate-300 hover:bg-slate-800 hover:text-emerald-300 group">
                <i class="fas fa-satellite-dish w-4 text-center text-emerald-400 group-hover:scale-110 transition"></i>
                <span class="font-medium">Live Radar Tracking</span>
                <i class="fas fa-external-link-alt ml-auto text-[10px] opacity-60"></i>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- PRODUCTS CATALOG -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-widest px-3 mb-1">Catalog Management</p>
            
            <!-- Products List -->
            <a href="{{ route('seller.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.products.index') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-boxes-stacked w-4 text-center text-purple-400"></i>
                <span>My Products</span>
                @php
                    $sellerProductCount = \App\Models\Product::where('user_id', auth()->id())->count();
                @endphp
                @if($sellerProductCount > 0)
                    <span class="ml-auto bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-mono px-1.5 py-0.5 rounded">
                        {{ $sellerProductCount }}
                    </span>
                @endif
            </a>

            <!-- Add Product -->
            <a href="{{ route('seller.products.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.products.create') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-plus w-4 text-center text-purple-300"></i>
                <span>Add New Product</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- FINANCE & SETTLEMENTS -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-widest px-3 mb-1">Finance & Settlements</p>
            
            <!-- Earnings -->
            <a href="{{ route('seller.earnings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.earnings*') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-money-bill-trend-up w-4 text-center text-emerald-400"></i>
                <span>Earnings & Reports</span>
            </a>

            <!-- Digital Wallet -->
            <a href="{{ route('seller.wallet') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.wallet*') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-wallet w-4 text-center text-teal-400"></i>
                <span>Digital Wallet</span>
            </a>

            <!-- Withdraw Funds -->
            <a href="{{ route('seller.withdraw') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.withdraw*') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-hand-holding-dollar w-4 text-center text-amber-400"></i>
                <span>Payout & Withdraw</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- SUPPORT & SETTINGS -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-widest px-3 mb-1">Settings & Help</p>

            <a href="{{ route('seller.support') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.support*') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-headset w-4 text-center text-rose-400"></i>
                <span>Support Tickets</span>
            </a>

            <a href="{{ route('seller.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.settings*') ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-gear w-4 text-center text-slate-400"></i>
                <span>Store Settings</span>
            </a>
        </div>

        <!-- Logout -->
        <div class="pt-3 border-t border-slate-800 mt-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-500/10 transition text-slate-400 hover:text-red-400 font-medium">
                    <i class="fas fa-arrow-right-from-bracket w-4 text-center"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </nav>
</aside>