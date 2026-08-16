<!-- Default Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto sticky top-0">
    <div class="p-4 border-b border-gray-700">
        <h2 class="text-xl font-bold text-teal-400">NetPack</h2>
        <p class="text-xs text-gray-400 mt-1">Welcome</p>
    </div>
    
    <nav class="p-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-home w-5"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('profile') ? 'bg-gray-800 text-teal-400' : 'text-gray-300' }}">
            <i class="fas fa-user w-5"></i>
            <span>My Profile</span>
        </a>
    </nav>
</aside>