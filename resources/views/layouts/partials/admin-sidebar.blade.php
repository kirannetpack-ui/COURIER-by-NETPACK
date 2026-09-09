<!-- Super Admin Sidebar -->
<aside class="w-64 bg-slate-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0 custom-scrollbar select-none" x-show="sidebarOpen" x-transition>
    <!-- Brand Header -->
    <div class="p-4 border-b border-slate-800 flex flex-col gap-2">
        <x-logo variant="white" size="sm" :href="route('admin.dashboard')" />
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 w-fit tracking-wider uppercase">
            <i class="fas fa-crown text-[10px] text-amber-400"></i> Super Administrator
        </span>
    </div>
    
    <!-- User Card -->
    <div class="p-3 mx-3 my-2 rounded-xl bg-slate-800/60 border border-slate-700/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-tr from-teal-600 to-teal-400 rounded-lg flex items-center justify-center font-bold text-white shadow-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-xs text-white truncate">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                <p class="text-[11px] text-teal-300/80 truncate">{{ auth()->user()->email ?? 'admin@netpack.com' }}</p>
            </div>
        </div>
    </div>
    
    <nav class="p-3 space-y-1 text-xs">
        <!-- Dashboard Overview -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-chart-pie w-4 text-center"></i>
            <span>Operations Dashboard</span>
        </a>

        <!-- ============================================== -->
        <!-- TRACKING SUITE (NEW - Full Visibility) -->
        <!-- ============================================== -->
        <div class="pt-3">
            <div class="flex items-center justify-between px-3 mb-1">
                <p class="text-[10px] text-teal-400 font-extrabold uppercase tracking-widest">Tracking Suite</p>
                <span class="px-1.5 py-0.2 bg-teal-500/20 text-teal-300 text-[9px] rounded font-mono font-bold">LIVE</span>
            </div>

            <!-- Public / Master Tracker -->
            <a href="{{ route('tracking.page') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-slate-300 hover:bg-slate-800 hover:text-white group">
                <i class="fas fa-search-location w-4 text-center text-teal-400 group-hover:scale-110 transition"></i>
                <span class="font-medium">Master Search & Radar</span>
                <i class="fas fa-external-link-alt ml-auto text-[10px] opacity-60"></i>
            </a>

            <!-- Shipment Registry -->
            <a href="{{ route('admin.shipments.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.shipments*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-boxes-stacked w-4 text-center text-blue-400"></i>
                <span>Shipment Registry</span>
                <span class="ml-auto bg-blue-500/20 text-blue-300 text-[10px] font-mono px-1.5 py-0.5 rounded">{{ \App\Models\Shipment::count() }}</span>
            </a>

            <!-- Live Telemetry & GPS Update -->
            <a href="{{ route('tracking.update') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('tracking.update*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-satellite-dish w-4 text-center text-emerald-400"></i>
                <span>Update Telemetry / Status</span>
            </a>

            <!-- HAWB Barcode Scanner -->
            <a href="{{ route('hawb.scanner') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('hawb.scanner*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-qrcode w-4 text-center text-amber-400"></i>
                <span>HAWB Barcode Scanner</span>
            </a>

            <!-- Rider Live Fleet Radar -->
            <a href="{{ route('admin.riders.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.riders*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-motorcycle w-4 text-center text-purple-400"></i>
                <span>Rider GPS Radar</span>
                <span class="ml-auto bg-emerald-500/20 text-emerald-300 text-[10px] font-mono px-1.5 py-0.5 rounded">
                    {{ \App\Models\User::where('user_type', 'rider')->where('is_online', true)->count() }} Online
                </span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- SERVICE PORTALS (All Service Admin Portals) -->
        <!-- ============================================== -->
        <div class="pt-3">
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest px-3 mb-1">Service Portals</p>

            <a href="{{ route('international.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('international.*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-plane-departure w-4 text-center text-sky-400"></i>
                <span>✈️ International Admin</span>
            </a>

            <a href="{{ route('domestic.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('domestic.dashboard*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-truck-fast w-4 text-center text-teal-400"></i>
                <span>🚚 Domestic Admin</span>
            </a>

            <a href="{{ route('ecommerce.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('ecommerce.*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-store w-4 text-center text-amber-400"></i>
                <span>🛒 E-Commerce Admin</span>
            </a>

            <a href="{{ route('seller.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('seller.*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-shop w-4 text-center text-emerald-400"></i>
                <span>🏪 Merchant Seller</span>
            </a>

            <a href="{{ route('rider.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('rider.*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-helmet-safety w-4 text-center text-yellow-400"></i>
                <span>🛵 Rider Dispatch</span>
            </a>

            <a href="{{ route('partner.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('partner.*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-handshake w-4 text-center text-indigo-400"></i>
                <span>🤝 Partner Network</span>
            </a>

            <a href="{{ route('client.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('client.*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-user-shield w-4 text-center text-pink-400"></i>
                <span>👤 Client Portal</span>
            </a>

            <a href="{{ route('overseas.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('overseas.*') ? 'bg-slate-800 text-teal-300 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-earth-asia w-4 text-center text-cyan-400"></i>
                <span>🌐 Overseas Hub</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- USER & STAKEHOLDER MANAGEMENT -->
        <!-- ============================================== -->
        <div class="pt-3">
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest px-3 mb-1">Users & Partners</p>
            
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-users-gear w-4 text-center"></i>
                <span>All Users</span>
                <span class="ml-auto bg-slate-700 text-slate-200 text-[10px] px-2 py-0.5 rounded-full font-mono">{{ \App\Models\User::count() }}</span>
            </a>

            <a href="{{ route('admin.partners.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.partners*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-handshake-angle w-4 text-center"></i>
                <span>Domestic Partners</span>
            </a>

            <a href="{{ route('admin.overseas-partners.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.overseas-partners*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-globe-americas w-4 text-center"></i>
                <span>Overseas Partners</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- DOMESTIC LOGISTICS -->
        <!-- ============================================== -->
        <div class="pt-3">
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest px-3 mb-1">Domestic Logistics</p>
            
            <a href="{{ route('admin.domestic.rates') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.domestic.rates*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-money-bill-wave w-4 text-center"></i>
                <span>Domestic Rates</span>
            </a>
            
            <a href="{{ route('admin.domestic.zones') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.domestic.zones*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-map-location-dot w-4 text-center"></i>
                <span>Delivery Zones</span>
            </a>
            
            <a href="{{ route('admin.domestic.shipments') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.domestic.shipments*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-truck-ramp-box w-4 text-center"></i>
                <span>Domestic Shipments</span>
            </a>

            <a href="{{ route('domestic.manifests.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('domestic.manifests.index') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-file-lines w-4 text-center"></i>
                <span>Manifests</span>
                <span class="ml-auto bg-slate-700 text-slate-200 text-[10px] px-1.5 py-0.5 rounded font-mono">{{ \App\Models\Manifest::count() }}</span>
            </a>

            <a href="{{ route('domestic.manifests.pods') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('domestic.manifests.pods*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-signature w-4 text-center"></i>
                <span>Proof of Delivery (POD)</span>
                <span class="ml-auto bg-emerald-500/20 text-emerald-300 text-[10px] px-1.5 py-0.5 rounded font-mono">{{ \App\Models\ProofOfDelivery::count() }}</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- INTERNATIONAL LOGISTICS -->
        <!-- ============================================== -->
        <div class="pt-3">
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest px-3 mb-1">International Cargo</p>
            
            <a href="{{ route('admin.rates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.rates*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-file-invoice-dollar w-4 text-center"></i>
                <span>International Rates</span>
            </a>

            <a href="{{ route('admin.rates.surcharges') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.rates.surcharges*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-receipt w-4 text-center"></i>
                <span>Remote & Surcharges</span>
            </a>

            <a href="{{ route('international.transit-points.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('international.transit-points*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-hubspot w-4 text-center"></i>
                <span>Transit Hubs & Points</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- COMMUNICATIONS & REMINDERS (NEW) -->
        <!-- ============================================== -->
        <div class="pt-3">
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest px-3 mb-1">Operations & SLAs</p>
            
            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.services*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-sliders w-4 text-center text-teal-400"></i>
                <span>Services & Transit SLA</span>
                <span class="ml-auto bg-teal-500/20 text-teal-300 text-[10px] font-mono px-1.5 py-0.5 rounded">{{ \App\Models\LogisticsService::count() }}</span>
            </a>

            <a href="{{ route('admin.communications') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.communications*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-triangle-exclamation w-4 text-center text-amber-400"></i>
                <span>Alerts & Delay Hub</span>
                @php
                    $pendingReminders = \App\Models\DeliveryReminder::where('is_sent', false)->count();
                @endphp
                @if($pendingReminders > 0)
                    <span class="ml-auto bg-amber-500/20 text-amber-300 text-[10px] font-mono px-1.5 py-0.5 rounded">{{ $pendingReminders }}</span>
                @endif
            </a>
        </div>

        <!-- ============================================== -->
        <!-- SETTLEMENTS & FINANCE -->
        <!-- ============================================== -->
        <div class="pt-3">
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest px-3 mb-1">Financial Settlements</p>
            
            <a href="{{ route('admin.cod-settlements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.cod-settlements*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-money-bill-transfer w-4 text-center"></i>
                <span>COD Settlements</span>
                @php
                    $pendingCod = \App\Models\CODSettlement::where('settlement_status', 'pending')->count();
                @endphp
                @if($pendingCod > 0)
                    <span class="ml-auto bg-red-500/20 text-red-300 text-[10px] font-mono px-1.5 py-0.5 rounded">{{ $pendingCod }}</span>
                @endif
            </a>
            
            <a href="{{ route('admin.partner-charges.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.partner-charges*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-hand-holding-dollar w-4 text-center"></i>
                <span>Partner Charges</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- SYSTEM & REPORTS -->
        <!-- ============================================== -->
        <div class="pt-3">
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest px-3 mb-1">System & Reports</p>
            
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.reports*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-chart-line w-4 text-center"></i>
                <span>Analytics & Reports</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings*') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-sliders w-4 text-center"></i>
                <span>System Settings</span>
            </a>

            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('profile') ? 'bg-teal-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-id-badge w-4 text-center"></i>
                <span>My Profile</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="block pt-2">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-500/10 transition text-slate-400 hover:text-red-400 font-medium">
                    <i class="fas fa-arrow-right-from-bracket w-4 text-center"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </nav>
</aside>