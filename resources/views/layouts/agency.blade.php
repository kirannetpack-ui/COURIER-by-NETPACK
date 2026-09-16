<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Hub Portal - COURIER with NETPACK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
    <nav class="bg-slate-900 text-white shadow-xl border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <span class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center font-bold text-lg">
                        <i class="fas fa-building"></i>
                    </span>
                    <div>
                        <span class="font-black text-lg tracking-tight">Agency Inbound Desk</span>
                        <span class="ml-2 text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full font-medium">
                            {{ auth('agency')->user()?->name ?? auth()->user()?->name ?? 'Hub Staff' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-4 text-xs font-semibold">
                    <a href="{{ route('agency.manifests.index') }}" class="hover:text-indigo-300 flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition">
                        <i class="fas fa-plane-arrival text-indigo-400"></i> Inbound Flight Manifests
                    </a>
                    <a href="{{ route('agency.scan') }}" class="hover:text-emerald-300 flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition">
                        <i class="fas fa-qrcode text-emerald-400"></i> Scan Box QR
                    </a>
                    <a href="{{ route('agency.shipments.index') }}" class="hover:text-slate-300 flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition">
                        <i class="fas fa-boxes text-slate-400"></i> Shipments
                    </a>
                    <a href="{{ route('international.dashboard') }}" class="text-slate-400 hover:text-white px-2 py-1">
                        <i class="fas fa-arrow-left mr-1"></i> Admin Portal
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="py-6">
        @yield('content')
    </main>
</body>
</html>
