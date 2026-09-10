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
        $sidebarOngoingShipments = collect();
        $sidebarOngoingCount = 0;
        $sidebarHistoryCount = 0;

        if ($sidebarClient) {
            $sidebarOngoingShipments = $sidebarClient->clientShipments()
                ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                ->latest('updated_at')
                ->take(4)
                ->get();

            $sidebarOngoingCount = $sidebarClient->clientShipments()
                ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                ->count();

            $sidebarHistoryCount = $sidebarClient->clientShipments()
                ->where('status', 'delivered')
                ->count();

            if (class_exists(\App\Models\DomesticShipment::class)) {
                $domOngoingCount = \App\Models\DomesticShipment::where('client_id', $sidebarClient->id)
                    ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                    ->count();
                $sidebarOngoingCount += $domOngoingCount;

                $sidebarHistoryCount += \App\Models\DomesticShipment::where('client_id', $sidebarClient->id)
                    ->where('status', 'delivered')
                    ->count();

                if ($sidebarOngoingShipments->isEmpty() && $domOngoingCount > 0) {
                    $sidebarOngoingShipments = \App\Models\DomesticShipment::where('client_id', $sidebarClient->id)
                        ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                        ->latest('updated_at')
                        ->take(4)
                        ->get();
                }
            }
        }
    @endphp

    <nav class="p-3 space-y-1 text-xs">
        <!-- Dashboard -->
        <a href="{{ route('client.dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('client.dashboard*') || request()->routeIs('dashboard') ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-chart-pie w-4 text-center"></i>
            <span>Client Dashboard</span>
        </a>

        <!-- Booking Actions Section -->
        <div class="pt-2">
            <p class="text-[10px] text-teal-400 font-extrabold uppercase tracking-widest px-3 mb-1.5">Create / Book</p>
            <div class="space-y-1">
                <!-- Express Pickup Request (Booking) -->
                <a href="{{ route('domestic.pickup.create') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('domestic.pickup.create*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-truck-pickup w-4 text-center text-teal-400"></i>
                    <span>Book Domestic Pickup</span>
                </a>
                <!-- Rate Inquiry & Tariff Calculator -->
                <a href="{{ route('rates.inquiry') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rates.inquiry*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-calculator w-4 text-center text-amber-400"></i>
                    <span>Rate Inquiry Desk</span>
                </a>
                <!-- New Air Cargo Consignment -->
                <a href="{{ route('shipments.create') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('shipments.create*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-plus-circle w-4 text-center text-indigo-400"></i>
                    <span>New Consignment (AWB)</span>
                </a>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- SHIPMENTS SUITE (ONGOING, HISTORY & HAWB)     -->
        <!-- ============================================== -->
        <div class="pt-2">
            <div class="flex items-center justify-between px-3 mb-1.5">
                <p class="text-[10px] text-teal-400 font-extrabold uppercase tracking-widest">Shipments & Radar</p>
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
            <a href="{{ route('shipments.index', ['status' => 'ongoing']) }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->get('status') === 'ongoing' || request()->get('status') === 'in_transit' ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-satellite-dish w-4 text-center text-emerald-400"></i>
                <span class="font-medium">Ongoing Tracking</span>
                <span class="ml-auto bg-emerald-500/20 text-emerald-300 text-[10px] font-mono font-bold px-1.5 py-0.5 rounded">
                    {{ $sidebarOngoingCount }} Active
                </span>
            </a>

            <!-- Active Ongoing Items Preview in Sidebar with Details and HAWB buttons -->
            @if($sidebarOngoingShipments->isNotEmpty())
                <div class="ml-2 pl-2 my-2 border-l-2 border-teal-500/40 space-y-2">
                    @foreach($sidebarOngoingShipments as $ongoing)
                        @php
                            $isDom = ($ongoing instanceof \App\Models\DomesticShipment) || (($ongoing->shipment_type ?? null) === 'domestic');
                            $destText = $ongoing->destination ?? $ongoing->receiver_city ?? 'Destination';
                            $ongoingType = $isDom ? 'domestic' : 'international';
                        @endphp
                        <div class="p-2 rounded-xl bg-slate-800/60 border border-slate-700/60 hover:border-teal-500/40 transition">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono font-bold text-[11px] text-teal-300 truncate">
                                    {{ $ongoing->tracking_number }}
                                </span>
                                <span class="uppercase font-semibold text-[8px] px-1.5 py-0.5 rounded bg-slate-900 text-emerald-400 border border-emerald-500/20">
                                    {{ str_replace('_', ' ', $ongoing->status ?? 'Active') }}
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400 truncate mt-0.5">
                                <i class="fas fa-location-dot text-[9px] text-teal-400 mr-0.5"></i> {{ $destText }}
                            </p>
                            <!-- 3 Direct Action Buttons: Details, Radar Tracking, HAWB Print -->
                            <div class="flex items-center gap-1 mt-1.5 pt-1.5 border-t border-slate-700/50">
                                <a href="{{ route('shipments.show', $ongoing->id) }}" 
                                   class="flex-1 py-1 px-1 rounded-md bg-slate-700/60 hover:bg-teal-600 text-[9px] font-semibold text-slate-200 hover:text-white text-center transition flex items-center justify-center gap-1"
                                   title="View Shipment Details">
                                    <i class="fas fa-eye text-[9px]"></i>
                                    <span>Details</span>
                                </a>
                                <a href="{{ route('tracking.show', $ongoing->tracking_number) }}" 
                                   class="flex-1 py-1 px-1 rounded-md bg-slate-700/60 hover:bg-blue-600 text-[9px] font-semibold text-slate-200 hover:text-white text-center transition flex items-center justify-center gap-1"
                                   title="Live Radar Tracking">
                                    <i class="fas fa-satellite-dish text-[9px]"></i>
                                    <span>Radar</span>
                                </a>
                                <a href="{{ route('hawb.print', ['id' => $ongoing->id, 'type' => $ongoingType]) }}" 
                                   target="_blank"
                                   class="flex-1 py-1 px-1 rounded-md bg-purple-600/30 hover:bg-purple-600 text-[9px] font-semibold text-purple-200 hover:text-white text-center transition flex items-center justify-center gap-1"
                                   title="Print / View HAWB Copy">
                                    <i class="fas fa-print text-[9px]"></i>
                                    <span>HAWB</span>
                                </a>
                            </div>
                        </div>
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

            <!-- Master Shipment Registry & Details -->
            <a href="{{ route('shipments.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('shipments.index') && !request()->has('status') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-boxes-stacked w-4 text-center text-slate-400"></i>
                <span>All Consignments & Details</span>
            </a>

            <!-- HAWB Copies & Print Documents -->
            <a href="{{ route('shipments.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-slate-300 hover:bg-slate-800 hover:text-purple-300">
                <i class="fas fa-file-invoice w-4 text-center text-purple-400"></i>
                <span>HAWB Copies & Print</span>
                <span class="ml-auto bg-purple-950/80 text-purple-300 border border-purple-800/50 text-[10px] font-mono px-1.5 py-0.5 rounded">
                    {{ $sidebarOngoingCount + $sidebarHistoryCount }} Copies
                </span>
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

        <!-- Domestic Pickup Requests Tracker -->
        <a href="{{ route('domestic.pickup.my-requests') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('domestic.pickup.my-requests*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-dolly w-4 text-center text-amber-400"></i>
            <span>Pickup Requests</span>
        </a>

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
