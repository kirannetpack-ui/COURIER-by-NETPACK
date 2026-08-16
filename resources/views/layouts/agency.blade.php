<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Portal - NETPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-teal-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-building text-2xl"></i>
                    <span class="font-bold text-xl">Agency Portal</span>
                    <span class="text-sm text-teal-200">{{ auth('agency')->user()->name }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('agency.dashboard') }}" class="hover:text-teal-200">Dashboard</a>
                    <a href="{{ route('agency.scan') }}" class="hover:text-teal-200">
                        <i class="fas fa-qrcode mr-1"></i> Scan QR
                    </a>
                    <a href="{{ route('agency.shipments') }}" class="hover:text-teal-200">Shipments</a>
                    <form method="POST" action="{{ route('agency.logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-teal-200">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="py-6">
        @yield('content')
    </main>
</body>
</html>