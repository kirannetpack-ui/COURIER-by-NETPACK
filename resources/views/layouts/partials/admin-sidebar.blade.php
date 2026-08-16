<!-- Super Admin Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0" x-show="sidebarOpen" x-transition>
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center">
                <i class="fas fa-crown text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-teal-400">NetPack Admin</h2>
                <p class="text-xs text-gray-400">Super Admin Panel</p>
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
                <p class="font-medium text-sm">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-chart-pie w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- User Management -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">User Management</p>
            
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.users*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-users w-5"></i>
                <span>All Users</span>
                <span class="ml-auto bg-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\User::count() }}</span>
            </a>
        </div>

        <!-- Domestic Services -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Domestic Services</p>
            
            <a href="{{ route('admin.partners.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.partners*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-handshake w-5"></i>
                <span>Partners</span>
            </a>
            
            <a href="{{ route('admin.domestic.rates') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.domestic.rates*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-money-bill-wave w-5"></i>
                <span>Domestic Rates</span>
            </a>
            
            <a href="{{ route('admin.domestic.zones') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.domestic.zones*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-map w-5"></i>
                <span>Delivery Zones</span>
            </a>
            
            <a href="{{ route('admin.domestic.shipments') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.domestic.shipments*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-truck w-5"></i>
                <span>Domestic Shipments</span>
            </a>
        </div>

      <!-- MANIFESTS -->
<div class="pt-4">
    <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">MANIFESTS</p>
    
    <!-- All Manifests -->
    <a href="{{ route('domestic.manifests.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition 
              {{ request()->routeIs('domestic.manifests.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="fas fa-boxes w-5 {{ request()->routeIs('domestic.manifests.index') ? 'text-blue-600' : 'text-gray-500' }}"></i>
        <span>All Manifests</span>
        <span class="ml-auto bg-blue-600 text-xs text-white px-2 py-1 rounded-full">{{ App\Models\Manifest::count() }}</span>
    </a>
    
    <!-- Create Manifest -->
    @if(Route::has('domestic.manifests.create'))
    <a href="{{ route('domestic.manifests.create') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition 
              {{ request()->routeIs('domestic.manifests.create') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="fas fa-plus-circle w-5 {{ request()->routeIs('domestic.manifests.create') ? 'text-blue-600' : 'text-green-500' }}"></i>
        <span>Create Manifest</span>
    </a>
    @endif
    
    <!-- Proof of Delivery -->
    <a href="{{ route('domestic.manifests.pods') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition 
              {{ request()->routeIs('domestic.manifests.pods*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="fas fa-file-signature w-5 {{ request()->routeIs('domestic.manifests.pods*') ? 'text-blue-600' : 'text-purple-500' }}"></i>
        <span>Proof of Delivery</span>
        <span class="ml-auto bg-green-600 text-xs text-white px-2 py-1 rounded-full">{{ App\Models\ProofOfDelivery::count() }}</span>
    </a>
</div>

        <!-- International Services -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">International Services</p>
            
            <a href="{{ route('admin.overseas-partners.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.overseas-partners*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-globe w-5"></i>
                <span>Overseas Partners</span>
            </a>
            
            <a href="{{ route('admin.rates.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.rates*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-file-invoice-dollar w-5"></i>
                <span>International Rates</span>
            </a>
        </div>

        <!-- Rider Monitoring -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Rider Monitoring</p>
            
            <a href="{{ route('admin.riders.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.riders*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-motorcycle w-5"></i>
                <span>Rider Dashboard</span>
                <span class="ml-auto bg-green-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\User::where('user_type', 'rider')->where('is_online', true)->count() }}</span>
            </a>
        </div>

        <!-- Settlements -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Settlements</p>
            
            <a href="{{ route('admin.cod-settlements.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.cod-settlements*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-money-bill-transfer w-5"></i>
                <span>COD Settlements</span>
                <span class="ml-auto bg-red-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\CODSettlement::where('settlement_status', 'pending')->count() }}</span>
            </a>
            
            <a href="{{ route('admin.partner-charges.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.partner-charges*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-hand-holding-usd w-5"></i>
                <span>Partner Charges</span>
            </a>
        </div>

        <!-- Reports -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Reports</p>
            
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.reports*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-file-alt w-5"></i>
                <span>Reports</span>
            </a>
            
            <a href="{{ route('admin.reports.shipments') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                <i class="fas fa-truck w-5"></i>
                <span>Shipment Reports</span>
            </a>
            
            <a href="{{ route('admin.reports.financial') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                <i class="fas fa-chart-bar w-5"></i>
                <span>Financial Reports</span>
            </a>
        </div>

        <!-- System -->
        <div class="pt-4 mt-4 border-t border-gray-700">
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