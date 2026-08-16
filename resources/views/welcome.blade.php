{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NETPACK Courier - International Shipping from Nepal</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #0A2540 0%, #00D2B6 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <i class="fas fa-box-open text-teal-600 text-2xl"></i>
                    <span class="font-bold text-xl text-gray-800">NETPACK</span>
                    <span class="text-xs text-teal-600 hidden md:block">Courier Service</span>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#home" class="text-gray-600 hover:text-teal-600 transition">Home</a>
                    <a href="#services" class="text-gray-600 hover:text-teal-600 transition">Services</a>
                    <a href="{{ url('/grocery-box') }}" class="text-gray-600 hover:text-teal-600 transition">Grocery Box</a>
                    <a href="{{ url('/track') }}" class="text-gray-600 hover:text-teal-600 transition">Track Order</a>
                </div>
                
                <!-- Auth Buttons -->
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-teal-600">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ url('/logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="text-gray-600 hover:text-teal-600 transition">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="{{ url('/register') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                            <i class="fas fa-user-plus mr-1"></i> Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">
                        Ship from Nepal to <span class="text-teal-300">Worldwide</span>
                    </h1>
                    <p class="text-lg md:text-xl mb-8 text-white/90">
                        International courier service with real-time tracking, instant payments, and professional support.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ url('/grocery-box') }}" class="bg-white text-teal-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition inline-flex items-center">
                            <i class="fas fa-box-open mr-2"></i> Start Shopping
                        </a>
                        <a href="{{ url('/track') }}" class="glass-effect text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/10 transition inline-flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i> Track Package
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mt-12 pt-8 border-t border-white/20">
                        <div>
                            <p class="text-2xl font-bold">50+</p>
                            <p class="text-sm text-white/80">Countries</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">10K+</p>
                            <p class="text-sm text-white/80">Packages Shipped</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">98%</p>
                            <p class="text-sm text-white/80">On-Time Delivery</p>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 text-white">
                                <i class="fas fa-check-circle text-teal-400"></i>
                                <span>Instant Tracking Updates</span>
                            </div>
                            <div class="flex items-center space-x-3 text-white">
                                <i class="fas fa-check-circle text-teal-400"></i>
                                <span>70% Instant Seller Payout</span>
                            </div>
                            <div class="flex items-center space-x-3 text-white">
                                <i class="fas fa-check-circle text-teal-400"></i>
                                <span>Professional HAWB with QR</span>
                            </div>
                            <div class="flex items-center space-x-3 text-white">
                                <i class="fas fa-check-circle text-teal-400"></i>
                                <span>24/7 Customer Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Our Services</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Comprehensive logistics solutions tailored for your business needs
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center hover-scale shadow-lg hover:shadow-xl">
                    <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box-open text-teal-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Grocery Box</h3>
                    <p class="text-gray-600 mb-4">
                        Pack authentic Nepali products and ship worldwide with our smart box packing system.
                    </p>
                    <a href="{{ url('/grocery-box') }}" class="text-teal-600 font-semibold hover:text-teal-700 inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <!-- Service 2 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center hover-scale shadow-lg hover:shadow-xl">
                    <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-teal-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">International Shipping</h3>
                    <p class="text-gray-600 mb-4">
                        Fast and reliable shipping to USA, UK, Australia, Japan, and 50+ countries.
                    </p>
                    <a href="#" class="text-teal-600 font-semibold hover:text-teal-700 inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <!-- Service 3 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center hover-scale shadow-lg hover:shadow-xl">
                    <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hand-holding-usd text-teal-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Instant Payouts</h3>
                    <p class="text-gray-600 mb-4">
                        70% instant settlement for sellers upon successful delivery.
                    </p>
                    <a href="#" class="text-teal-600 font-semibold hover:text-teal-700 inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">How It Works</h2>
                <p class="text-gray-600">Simple steps to ship your package worldwide</p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">1</div>
                    <h3 class="font-semibold mb-2">Create Order</h3>
                    <p class="text-sm text-gray-600">Pack your products in our smart grocery box</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">2</div>
                    <h3 class="font-semibold mb-2">Make Payment</h3>
                    <p class="text-sm text-gray-600">Pay online via Khalti or eSewa</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">3</div>
                    <h3 class="font-semibold mb-2">We Ship</h3>
                    <p class="text-sm text-gray-600">Your package is picked up and shipped</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">4</div>
                    <h3 class="font-semibold mb-2">Track Delivery</h3>
                    <p class="text-sm text-gray-600">Real-time tracking until delivered</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="hero-gradient py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Ship?</h2>
            <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                Join thousands of satisfied customers who trust NETPACK for their international shipping needs.
            </p>
            <a href="{{ url('/register') }}" class="bg-white text-teal-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition inline-flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Create Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-box-open text-teal-400 text-2xl"></i>
                        <span class="font-bold text-xl">NETPACK</span>
                    </div>
                    <p class="text-gray-400 text-sm">International courier service from Nepal to worldwide.</p>
                </div>
                <div>
                    <h3 class="font-semibold mb-3">Quick Links</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ url('/grocery-box') }}" class="hover:text-teal-400">Grocery Box</a></li>
                        <li><a href="{{ url('/track') }}" class="hover:text-teal-400">Track Shipment</a></li>
                        <li><a href="#" class="hover:text-teal-400">Shipping Rates</a></li>
                        <li><a href="#" class="hover:text-teal-400">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-3">Legal</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-teal-400">Terms & Conditions</a></li>
                        <li><a href="#" class="hover:text-teal-400">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-teal-400">Refund Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-3">Contact</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i> +977-1-5970123</li>
                        <li><i class="fas fa-envelope mr-2"></i> support@couriernetpack.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Kathmandu, Nepal</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} COURIER by NETPACK. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>