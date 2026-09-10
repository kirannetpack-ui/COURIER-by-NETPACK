<!-- Rider Sidebar -->
<aside class="w-64 bg-slate-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0 custom-scrollbar select-none" x-show="sidebarOpen" x-transition>
    <!-- Brand Header -->
    <div class="p-4 border-b border-slate-800 flex flex-col gap-2">
        <x-logo variant="white" size="sm" :href="route('rider.dashboard')" />
        <div class="flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 w-fit tracking-wider uppercase">
                <i class="fas fa-motorcycle text-[9px] text-amber-400"></i> Rider Cockpit
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold {{ auth()->user()->is_online ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-700 text-slate-300' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->is_online ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400' }}"></span>
                {{ auth()->user()->is_online ? 'ONLINE' : 'OFFLINE' }}
            </span>
        </div>
    </div>
    
    <!-- Rider Info & Deposit Status Card -->
    <div class="p-3 mx-3 my-2 rounded-xl bg-slate-800/60 border border-slate-700/60">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-amber-600 to-yellow-500 rounded-xl flex items-center justify-center font-bold text-white shadow-sm flex-shrink-0">
                <i class="fas fa-helmet-safety text-sm"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-xs text-white truncate">{{ auth()->user()->name ?? 'Rider Fleet' }}</p>
                <p class="text-[11px] text-amber-300/80 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
        <div class="mt-2 pt-2 border-t border-slate-700/50 flex items-center justify-between text-[11px]">
            <span class="text-slate-400">Deposit Buffer</span>
            <span class="font-bold font-mono text-emerald-400">Rs. {{ number_format(auth()->user()->rider_deposit_balance ?? 0, 2) }}</span>
        </div>
    </div>

    <!-- Quick Duty Status & Orders Pool Action -->
    <div class="px-3 py-1.5">
        <a href="{{ route('rider.orders.available') }}" 
           class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-500 hover:to-yellow-500 text-white font-bold text-xs shadow-md shadow-amber-900/30 transition transform hover:-translate-y-0.5">
            <i class="fas fa-radar text-sm"></i>
            <span>Scan Available Jobs</span>
            @php
                $availableOrdersCount = \App\Models\Order::where('status', 'pending')->whereNull('rider_id')->count();
            @endphp
            @if($availableOrdersCount > 0)
                <span class="bg-white text-amber-800 text-[10px] font-mono px-1.5 py-0.2 rounded-full font-black">
                    {{ $availableOrdersCount }}
                </span>
            @endif
        </a>
    </div>
    
    <nav class="p-3 space-y-1 text-xs">
        <!-- Dashboard Overview -->
        <a href="{{ route('rider.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('rider.dashboard') ? 'bg-amber-600 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-chart-pie w-4 text-center"></i>
            <span>Rider Cockpit</span>
        </a>

        <!-- ============================================== -->
        <!-- DELIVERIES & ORDERS -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-amber-400 font-extrabold uppercase tracking-widest px-3 mb-1">Fleet Operations</p>
            
            <!-- Available Orders -->
            <a href="{{ route('rider.orders.available') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.orders.available') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-search-location w-4 text-center text-yellow-400"></i>
                <span>Available Orders Pool</span>
                @if($availableOrdersCount > 0)
                    <span class="ml-auto bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-mono font-bold px-1.5 py-0.5 rounded">
                        {{ $availableOrdersCount }} New
                    </span>
                @endif
            </a>

            <!-- My Active Orders -->
            <a href="{{ route('rider.orders.my') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.orders.my') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-route w-4 text-center text-emerald-400"></i>
                <span>Active E-Commerce</span>
                @php
                    $riderActiveOrders = \App\Models\Order::where('rider_id', auth()->id())
                        ->whereIn('status', ['assigned', 'picked_up', 'in_transit', 'out_for_delivery'])
                        ->count();
                @endphp
                @if($riderActiveOrders > 0)
                    <span class="ml-auto bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-mono font-bold px-1.5 py-0.5 rounded">
                        {{ $riderActiveOrders }} On Route
                    </span>
                @endif
            </a>

            <!-- Courier Deliveries -->
            <a href="{{ route('rider.deliveries.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.deliveries*') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-truck-ramp-box w-4 text-center text-sky-400"></i>
                <span>Courier Deliveries</span>
            </a>

            <!-- Delivery History -->
            <a href="{{ route('rider.history') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.history') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-clock-rotate-left w-4 text-center text-slate-400"></i>
                <span>Delivery History</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- COD CASH & FINANCIALS -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-amber-400 font-extrabold uppercase tracking-widest px-3 mb-1">Cash on Delivery & Pay</p>
            
            <!-- COD Settlements -->
            <a href="{{ route('rider.cod.settlement-summary') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.cod*') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-hand-holding-dollar w-4 text-center text-emerald-400"></i>
                <span>COD Collections & Summary</span>
            </a>

            <!-- Top up Deposit -->
            <a href="{{ route('rider.deposit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.deposit*') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-shield-halved w-4 text-center text-teal-400"></i>
                <span>Deposit Guarantee</span>
            </a>

            <!-- Digital Wallet -->
            <a href="{{ route('rider.wallet') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.wallet*') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-wallet w-4 text-center text-purple-400"></i>
                <span>Rider Wallet</span>
            </a>

            <!-- Earnings -->
            <a href="{{ route('rider.earnings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.earnings*') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-money-bill-trend-up w-4 text-center text-green-400"></i>
                <span>Earnings & Commission</span>
            </a>

            <!-- Payment Methods -->
            <a href="{{ route('rider.payment-methods') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.payment-methods*') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-credit-card w-4 text-center text-indigo-400"></i>
                <span>Payout Bank Accounts</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- NAVIGATION & RADAR -->
        <!-- ============================================== -->
        <div class="pt-2">
            <p class="text-[10px] text-amber-400 font-extrabold uppercase tracking-widest px-3 mb-1">Radar & Support</p>

            <!-- Public Radar Portal Link -->
            <a href="{{ route('tracking.page') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-slate-300 hover:bg-slate-800 hover:text-amber-300 group">
                <i class="fas fa-satellite-dish w-4 text-center text-amber-400 group-hover:scale-110 transition"></i>
                <span class="font-medium">Live Radar Map</span>
                <i class="fas fa-external-link-alt ml-auto text-[10px] opacity-60"></i>
            </a>

            <!-- Rider Settings -->
            <a href="{{ route('rider.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.settings*') ? 'bg-amber-600/30 text-amber-200 border border-amber-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-gear w-4 text-center text-slate-400"></i>
                <span>Rider Settings & Vehicle</span>
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