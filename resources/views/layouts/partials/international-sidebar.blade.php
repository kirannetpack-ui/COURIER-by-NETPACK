<!-- International Service Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0">
    <div class="p-4 border-b border-gray-800 flex flex-col gap-2">
        <x-logo variant="white" size="sm" :href="route('international.dashboard')" />
        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30 w-fit">
            <i class="fas fa-plane-departure text-[9px]"></i> International Air Cargo
        </span>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <div class="mb-2">
            <a href="{{ route('international.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-chart-pie w-5"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Shipment Management -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Shipment Management</p>
            
            <a href="{{ route('international.shipments') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.shipments*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-ship w-5"></i>
                <span>All Shipments</span>
                <span class="ml-auto bg-orange-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\Shipment::whereNotNull('overseas_partner_id')->count() }}</span>
            </a>
            
            <a href="{{ route('international.shipments.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.shipments.create') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-plus-circle w-5"></i>
                <span>Create Shipment</span>
            </a>
            
            <!-- Tracking -->
            <a href="{{ route('tracking.page') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('tracking*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-search-location w-5"></i>
                <span>Track Shipment</span>
            </a>
        </div>

        <!-- International Air Cargo & Gateway Hubs -->
        <div class="pt-4">
            <p class="text-xs text-sky-400 uppercase tracking-wider px-4 mb-2 font-bold flex items-center gap-1.5">
                <i class="fas fa-plane-departure text-sky-500"></i> Air Cargo Logistics
            </p>
            
            <a href="{{ route('international.hubs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.hubs*') ? 'bg-gray-800 text-sky-400' : 'text-gray-300' }}">
                <i class="fas fa-globe-americas w-5 text-indigo-400"></i>
                <span>Gateway Hubs</span>
                <span class="ml-auto bg-indigo-600 text-xs px-2 py-0.5 rounded-full">{{ \App\Models\OverseasHub::count() }}</span>
            </a>

            <a href="{{ route('international.agencies.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.agencies*') ? 'bg-gray-800 text-amber-400' : 'text-gray-300' }}">
                <i class="fas fa-building w-5 text-amber-400"></i>
                <span>Partner Agencies</span>
                <span class="ml-auto bg-amber-600 text-xs px-2 py-0.5 rounded-full">{{ \App\Models\Agency::count() }}</span>
            </a>

            <a href="{{ route('international.last-mile.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.last-mile*') ? 'bg-gray-800 text-emerald-400' : 'text-gray-300' }}">
                <i class="fas fa-truck-moving w-5 text-emerald-400"></i>
                <span>Last Mile Couriers</span>
                <span class="ml-auto bg-emerald-600 text-xs px-2 py-0.5 rounded-full">{{ \App\Models\LastMileCarrier::count() }}</span>
            </a>

            <a href="{{ route('international.mawbs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.mawbs*') ? 'bg-gray-800 text-sky-400' : 'text-gray-300' }}">
                <i class="fas fa-barcode w-5 text-sky-400"></i>
                <span>MAWB Pool</span>
                <span class="ml-auto bg-sky-600 text-xs px-2 py-0.5 rounded-full">{{ \App\Models\MAWB::unused()->count() }} Unused</span>
            </a>

            <a href="{{ route('international.manifests.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.manifests*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-file-invoice-dollar w-5 text-teal-400"></i>
                <span>Flight Manifests</span>
                <span class="ml-auto bg-teal-600 text-xs px-2 py-0.5 rounded-full">{{ \App\Models\Manifest::international()->count() }}</span>
            </a>

            <a href="{{ route('agency.manifests.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('agency*') ? 'bg-gray-800 text-emerald-400' : 'text-gray-300' }}">
                <i class="fas fa-inbox w-5 text-emerald-400"></i>
                <span>Agency Inbound Desk</span>
            </a>
        </div>

        <!-- Partner Management -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Partner Management</p>
            
            <a href="{{ route('international.partners') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.partners*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-handshake w-5"></i>
                <span>Overseas Partners</span>
                <span class="ml-auto bg-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\User::where('user_type', 'overseas')->count() }}</span>
            </a>
            
            <a href="{{ route('international.partners.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.partners.create') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-user-plus w-5"></i>
                <span>Add Partner</span>
            </a>
            
            <a href="{{ route('overseas.transit-points.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('overseas.transit-points*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-route w-5"></i>
                <span>Transit Points</span>
                <span class="ml-auto bg-purple-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\OverseasTransitPoint::count() }}</span>
            </a>
        </div>

        <!-- Rate Management -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Rate Management</p>
            
            <a href="{{ route('international.rates') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.rates*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-file-invoice-dollar w-5"></i>
                <span>Base Rates</span>
                <span class="ml-auto bg-indigo-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\OverseasBaseRate::count() }}</span>
            </a>
            
            <a href="{{ route('international.rates.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.rates.create') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-upload w-5"></i>
                <span>Upload Rate Sheet</span>
            </a>
            
            <a href="{{ route('international.surcharges') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.surcharges*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-map-marker-alt w-5"></i>
                <span>Remote Surcharges</span>
                <span class="ml-auto bg-red-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\RemoteAreaSurcharge::count() }}</span>
            </a>
        </div>

        <!-- Reports -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Reports</p>
            
            <a href="{{ route('international.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('international.reports*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-file-alt w-5"></i>
                <span>Reports</span>
            </a>
        </div>

        <!-- System -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">System</p>
            
            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('profile') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-user-circle w-5"></i>
                <span>My Profile</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition text-gray-300 hover:text-red-400">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
