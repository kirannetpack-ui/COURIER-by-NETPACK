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

        <!-- My Shipments -->
        <a href="{{ route('shipments.index') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('shipments*') ? 'bg-teal-600/30 text-teal-200 border border-teal-500/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-truck-fast w-4 text-center text-blue-400"></i>
            <span>My Consignments</span>
        </a>

        <!-- Tracking Radar -->
        <a href="{{ route('tracking.page') }}" target="_blank"
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-slate-300 hover:bg-slate-800 hover:text-white group">
            <i class="fas fa-search-location w-4 text-center text-emerald-400 group-hover:scale-110 transition"></i>
            <span>Live Tracking Radar</span>
            <i class="fas fa-external-link-alt ml-auto text-[10px] opacity-60"></i>
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
