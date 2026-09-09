<!-- Client Sidebar -->
<aside class="w-64 bg-slate-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0 custom-scrollbar select-none" x-show="sidebarOpen" x-transition>
    <div class="p-4 border-b border-slate-800 flex flex-col gap-2">
        <x-logo variant="white" size="sm" :href="route('client.dashboard')" />
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 w-fit tracking-wider uppercase">
            <i class="fas fa-user-shield text-[9px] text-teal-400"></i> Client Portal
        </span>
    </div>

    <!-- Client Card -->
    <div class="p-3 mx-3 my-2 rounded-xl bg-slate-800/60 border border-slate-700/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-tr from-teal-600 to-teal-400 rounded-lg flex items-center justify-center font-bold text-white shadow-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-xs text-white truncate">{{ auth()->user()->name ?? 'Valued Client' }}</p>
                <p class="text-[11px] text-teal-300/80 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
    
    @php
        $sidebarClient = auth()->user();
        $sidebarOngoingShipments = $sidebarClient ? $sidebarClient->clientShipments()
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->latest('updated_at')
            ->take(3)
            ->get() : collect();
        $sidebarOngoingCount = $sidebarClient ? $sidebarClient->clientShipments()
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->count() : 0;
        $sidebarHistoryCount = $sidebarClient ? $sidebarClient->clientShipments()
            ->where('status', 'delivered')
            ->count() : 0;
    @endphp

    <nav class="p-3 space-y-1 text-xs">
        <!-- Dashboard -->
        <a href="{{ route('client.dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('client.dashboard*') || request()->routeIs('dashboard') ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-chart-pie w-4 text-center"></i>
            <span>Client Dashboard</span>
        </a>

        <!-- Express Pickup Request (Booking) -->
        <a href="{{ route('domestic.pickup.create') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('domestic.pickup.create*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-plus-circle w-4 text-center text-teal-400"></i>
            <span>Book New Pickup</span>
        </a>

        <!-- ============================================== -->
        <!-- TRACKING & CONSIGNMENT RADAR (ONGOING + HISTORY) -->
        <!-- ============================================== -->
        <div class="pt-2">
            <div class="flex items-center justify-between px-3 mb-1.5">
                <p class="text-[10px] text-teal-400 font-extrabold uppercase tracking-widest">Tracking & Radar</p>
                @if($sidebarOngoingCount > 0)
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ $sidebarOngoingCount }} Live
                    </span>
                @else
                    <span class="px-1.5 py-0.5 bg-slate-800 text-slate-400 text-[9px] rounded font-mono">STANDBY</span>
                @endif
            </div>

            <!-- Ongoing Tracking (Active Consignments) -->
            <a href="{{ route('shipments.index', ['status' => 'in_transit']) }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->get('status') === 'in_transit' ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-satellite-dish w-4 text-center text-emerald-400"></i>
                <span class="font-medium">Ongoing Tracking</span>
                <span class="ml-auto bg-emerald-500/20 text-emerald-300 text-[10px] font-mono font-bold px-1.5 py-0.5 rounded">
                    {{ $sidebarOngoingCount }} Active
                </span>
            </a>

            <!-- Active Ongoing Items Preview in Sidebar -->
            @if($sidebarOngoingShipments->isNotEmpty())
                <div class="ml-3 pl-3 my-1.5 border-l border-slate-800 space-y-1">
                    @foreach($sidebarOngoingShipments as $ongoing)
                        <a href="{{ route('tracking.show', $ongoing->tracking_number) }}" 
                           class="block px-2.5 py-1.5 rounded-lg bg-slate-800/40 hover:bg-slate-800 hover:border-teal-500/40 border border-slate-700/40 transition group"
                           title="Track {{ $ongoing->tracking_number }} ({{ $ongoing->destination }})">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono font-bold text-[11px] text-teal-300 group-hover:text-teal-200 truncate">
                                    {{ $ongoing->tracking_number }}
                                </span>
                                <i class="fas fa-arrow-up-right-from-square text-[9px] text-slate-400 group-hover:text-teal-300"></i>
                            </div>
                            <div class="flex items-center justify-between gap-1 text-[10px] text-slate-400 mt-0.5">
                                <span class="truncate">{{ $ongoing->destination ?? 'Destination' }}</span>
                                <span class="uppercase font-semibold text-[9px] px-1 py-0.2 rounded bg-slate-900 text-teal-400 border border-teal-500/20">
                                    {{ str_replace('_', ' ', $ongoing->status ?? 'Active') }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Tracking History (Delivered Shipments) -->
            <a href="{{ route('shipments.index', ['status' => 'delivered']) }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->get('status') === 'delivered' ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-clock-rotate-left w-4 text-center text-blue-400"></i>
                <span>Tracking History</span>
                <span class="ml-auto bg-slate-800 text-slate-300 text-[10px] font-mono px-1.5 py-0.5 rounded">
                    {{ $sidebarHistoryCount }} Delivered
                </span>
            </a>

            <!-- Master Shipment Registry -->
            <a href="{{ route('shipments.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('shipments.index') && !request()->has('status') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-boxes-stacked w-4 text-center text-slate-400"></i>
                <span>All Consignments</span>
            </a>

            <!-- Sidebar Quick Tracking Lookup Form -->
            <div class="px-2 py-1.5 my-1">
                <form action="{{ route('tracking.search') }}" method="GET" class="relative">
                    <input type="text" name="tracking" placeholder="Track HAWB / AWB..." required
                           class="w-full bg-slate-950/80 border border-slate-800 rounded-lg pl-7 pr-2 py-1.5 text-[11px] text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-mono">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-500 text-[10px]"></i>
                </form>
            </div>

            <!-- Public Radar Portal Link -->
            <a href="{{ route('tracking.page') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-slate-400 hover:bg-slate-800 hover:text-teal-300 group">
                <i class="fas fa-search-location w-4 text-center text-teal-400 group-hover:scale-110 transition"></i>
                <span class="text-[11px]">Public Radar Map</span>
                <i class="fas fa-external-link-alt ml-auto text-[10px] opacity-60"></i>
            </a>
        </div>

        <!-- Grocery Box -->
        <a href="{{ route('grocery.box') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('grocery.box*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-box-open w-4 text-center text-amber-400"></i>
            <span>Grocery & Cargo Box</span>
        </a>

        <!-- Wallet -->
        <a href="{{ route('client.wallet') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('client.wallet*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-wallet w-4 text-center text-purple-400"></i>
            <span>Billing & Wallet</span>
        </a>

        <!-- Feedback -->
        <a href="{{ route('client.feedback') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('client.feedback*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-comment-dots w-4 text-center text-rose-400"></i>
            <span>Feedback</span>
        </a>

        <!-- Support -->
        <a href="{{ route('client.support') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('client.support*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-headset w-4 text-center text-sky-400"></i>
            <span>Priority Support</span>
        </a>

        <!-- Settings & Profile -->
        <div class="pt-3 border-t border-slate-800 mt-3">
            <a href="{{ route('profile') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('profile*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-id-badge w-4 text-center"></i>
                <span>Profile & Addresses</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="block pt-1">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-500/10 transition text-slate-400 hover:text-red-400 font-medium">
                    <i class="fas fa-arrow-right-from-bracket w-4 text-center"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
