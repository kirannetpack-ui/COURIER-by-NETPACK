<!-- Partner Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0" x-show="sidebarOpen" x-transition>
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center">
                <i class="fas fa-handshake text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-teal-400">NetPack Partner</h2>
                <p class="text-xs text-gray-400">Partner Delivery Panel</p>
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
                <p class="font-medium text-sm">{{ auth()->user()->name ?? 'Partner' }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('partner.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('partner.dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-chart-pie w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- Delivery Management -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Delivery Management</p>
            
            <a href="{{ route('partner.deliveries.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('partner.deliveries*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-truck w-5"></i>
                <span>All Deliveries</span>
                @php
                    $pendingCount = \App\Models\PickupRequest::where('partner_id', auth()->id())->where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-yellow-600 text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
            
            <a href="{{ route('partner.deliveries.attention') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('partner.deliveries.attention*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-exclamation-triangle w-5"></i>
                <span>Attention Needed</span>
                @php
                    $attentionCount = \App\Models\PickupRequest::where('partner_id', auth()->id())->where('is_delayed', true)->where('status', '!=', 'delivered')->count();
                @endphp
                @if($attentionCount > 0)
                    <span class="ml-auto bg-red-600 text-xs px-2 py-1 rounded-full">{{ $attentionCount }}</span>
                @endif
            </a>
            
            <a href="{{ route('partner.scan') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('partner.scan') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-qrcode w-5"></i>
                <span>QR Scan</span>
            </a>
        </div>

        <!-- Zone & Rate Management -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Zone & Rate Management</p>
            
            <a href="{{ route('partner.zones.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('partner.zones*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-map w-5"></i>
                <span>Delivery Zones</span>
                <span class="ml-auto bg-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\DeliveryZone::where('partner_id', auth()->id())->count() }}</span>
            </a>
            
            <!-- Rates Menu - NEW -->
            <a href="{{ route('partner.rates.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('partner.rates*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-money-bill-wave w-5"></i>
                <span>Rates</span>
                @php
                    $zoneCount = \App\Models\DeliveryZone::where('partner_id', auth()->id())->count();
                @endphp
                @if($zoneCount > 0)
                    <span class="ml-auto bg-green-600 text-xs px-2 py-1 rounded-full">{{ $zoneCount }}</span>
                @endif
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
</div>        <!-- Staff Management -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Staff Management</p>
            
            <a href="{{ route('partner.staff.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('partner.staff*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-users w-5"></i>
                <span>Staff Members</span>
                <span class="ml-auto bg-purple-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\PartnerStaff::where('partner_id', auth()->id())->count() ?? 0 }}</span>
            </a>
        </div>

        <!-- Reports -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Reports</p>
            
            <a href="{{ route('partner.deliveries.export') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                <i class="fas fa-file-export w-5"></i>
                <span>Export Report</span>
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