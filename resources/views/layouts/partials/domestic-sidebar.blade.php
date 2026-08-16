<!-- Domestic Admin Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0" x-show="sidebarOpen" x-transition>
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center">
                <i class="fas fa-truck text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-teal-400">NetPack Domestic</h2>
                <p class="text-xs text-gray-400">Domestic & E-commerce Panel</p>
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
        <a href="{{ route('domestic.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-chart-pie w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- DOMESTIC SERVICES -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Domestic Services</p>
            
            <a href="{{ route('domestic.partners') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.partners*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-handshake w-5"></i>
                <span>Partners</span>
                <span class="ml-auto bg-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\User::where('user_type', 'partner')->count() }}</span>
            </a>
            
            <a href="{{ route('domestic.rates') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.rates*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-money-bill-wave w-5"></i>
                <span>Rates</span>
                <span class="ml-auto bg-green-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\DomesticRate::count() }}</span>
            </a>
            
            <a href="{{ route('domestic.zones') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.zones*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-map w-5"></i>
                <span>Delivery Zones</span>
                <span class="ml-auto bg-purple-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\DeliveryZone::count() }}</span>
            </a>
            
            <a href="{{ route('domestic.shipments') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.shipments*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-truck w-5"></i>
                <span>Shipments</span>
                <span class="ml-auto bg-yellow-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\DomesticShipment::count() }}</span>
            </a>
            
            <a href="{{ route('domestic.pickups') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.pickups*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-hand-holding-box w-5"></i>
                <span>Pickup Requests</span>
                <span class="ml-auto bg-orange-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\PickupRequest::count() }}</span>
            </a>
        </div>

        <!-- MANIFESTS -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Manifests</p>
            
            <a href="{{ route('domestic.manifests.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.manifests*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-boxes w-5"></i>
                <span>All Manifests</span>
                <span class="ml-auto bg-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\Manifest::count() }}</span>
            </a>
            
            <a href="{{ route('domestic.manifests.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.manifests.create') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-plus-circle w-5"></i>
                <span>Create Manifest</span>
            </a>
            
            <a href="{{ route('domestic.manifests.pods') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.manifests.pods*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-file-signature w-5"></i>
                <span>Proof of Delivery (POD)</span>
                <span class="ml-auto bg-green-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\ProofOfDelivery::count() }}</span>
            </a>
        </div>

        <!-- E-COMMERCE MANAGEMENT -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">E-commerce Management</p>
            
            <a href="{{ route('domestic.sellers') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.sellers*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-store w-5"></i>
                <span>Sellers</span>
                <span class="ml-auto bg-pink-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\User::where('user_type', 'seller')->count() }}</span>
            </a>
            
            <a href="{{ route('domestic.products') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.products*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-box w-5"></i>
                <span>Products</span>
                <span class="ml-auto bg-indigo-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\Product::count() }}</span>
            </a>
            
            <a href="{{ route('domestic.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.orders*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-shopping-cart w-5"></i>
                <span>Orders</span>
                <span class="ml-auto bg-red-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\Order::count() }}</span>
            </a>
        </div>

        <!-- REPORTS -->
        <div class="pt-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Reports</p>
            
            <a href="{{ route('domestic.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('domestic.reports*') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
                <i class="fas fa-file-alt w-5"></i>
                <span>Reports</span>
            </a>
        </div>

        <!-- SYSTEM -->
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