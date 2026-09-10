@extends('layouts.seller')

@section('title', 'Merchant Operations Dashboard')
@section('page-title', 'Merchant Operations Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Welcome & Fast Dispatch Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-950 via-slate-900 to-teal-950 p-6 sm:p-8 text-white border border-emerald-500/20 shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase tracking-widest">
                        <i class="fas fa-shop text-[9px] mr-1"></i> Merchant Dispatch Hub
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        <i class="fas fa-circle-check text-[9px] mr-1 text-emerald-400"></i> Verified Seller
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Welcome back, {{ Auth::user()->name }}!
                </h1>
                <p class="text-sm text-slate-300 max-w-xl">
                    Dispatch customer parcels across Nepal, track live rider deliveries with GPS telemetry, and manage your digital wallet settlements seamlessly.
                </p>
            </div>

            <!-- Fast Action Booking CTA Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('seller.orders.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-900/40 transition transform hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle text-base"></i>
                    <span>Book / Create Order</span>
                </a>
                <a href="{{ route('seller.shipments.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-semibold text-sm border border-slate-700 transition">
                    <i class="fas fa-box text-base text-teal-400"></i>
                    <span>Courier Parcel</span>
                </a>
                <a href="{{ route('tracking.page') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 font-semibold text-sm border border-slate-700 transition">
                    <i class="fas fa-satellite-dish text-base text-emerald-400"></i>
                    <span>Radar Map</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Core KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Orders -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">E-Commerce Orders</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format($orderStats['total'] ?? 0) }}</p>
                    <div class="flex items-center gap-2 mt-2 text-[11px]">
                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold border border-amber-200">
                            {{ $orderStats['pending'] ?? 0 }} Pending
                        </span>
                        <span class="text-slate-400">
                            {{ $orderStats['completed'] ?? 0 }} Completed
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>

        <!-- Total Earnings -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Sales / Revenue</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">Rs. {{ number_format($earnings['total']['amount'] ?? 0, 2) }}</p>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        <i class="fas fa-calendar-week text-emerald-500 mr-1"></i> Rs. {{ number_format($weekEarnings ?? 0, 0) }} this week
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-money-bill-trend-up"></i>
                </div>
            </div>
        </div>

        <!-- Digital Wallet Balance -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Available Wallet</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Rs. {{ number_format($balance ?? 0, 2) }}</p>
                    <div class="flex items-center gap-2 mt-2 text-[11px]">
                        <a href="{{ route('seller.withdraw') }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                            Request Payout &rarr;
                        </a>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>

        <!-- Products in Catalog -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Product Catalog</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format($totalProducts ?? 0) }}</p>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        <span class="text-emerald-600 font-bold">{{ $activeProducts ?? 0 }} Active</span> • {{ $productStats['low_stock'] ?? 0 }} Low Stock
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Operations Navigation -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('seller.orders.create') }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-sm transition flex items-center gap-4 group">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Book Order</p>
                <p class="text-xs text-slate-400">Instant customer dispatch</p>
            </div>
        </a>

        <a href="{{ route('seller.orders') }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-amber-500 hover:shadow-sm transition flex items-center gap-4 group">
            <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fas fa-list-check"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Manage Orders</p>
                <p class="text-xs text-slate-400">{{ $orderStats['pending'] ?? 0 }} awaiting fulfillment</p>
            </div>
        </a>

        <a href="{{ route('seller.products.create') }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-purple-500 hover:shadow-sm transition flex items-center gap-4 group">
            <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fas fa-tag"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Add Product</p>
                <p class="text-xs text-slate-400">Create new item</p>
            </div>
        </a>

        <a href="{{ route('seller.wallet') }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-teal-500 hover:shadow-sm transition flex items-center gap-4 group">
            <div class="w-11 h-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fas fa-building-columns"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Wallet & Payout</p>
                <p class="text-xs text-slate-400">COD settlements & balance</p>
            </div>
        </a>
    </div>

    <!-- Live Order & Shipments Stream -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent E-Commerce Orders -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Recent Orders & Dispatch Status</h3>
                    <p class="text-xs text-slate-500">Live order fulfillment with riders and couriers</p>
                </div>
                <a href="{{ route('seller.orders') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                    View All Orders &rarr;
                </a>
            </div>

            <div class="p-4">
                @php
                    $sellerRecentOrders = \App\Models\Order::where('seller_id', Auth::id())
                        ->with(['rider', 'client'])
                        ->latest()
                        ->take(6)
                        ->get();
                @endphp

                @if($sellerRecentOrders->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach($sellerRecentOrders as $order)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/80 rounded-xl px-3 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-xs text-slate-700">
                                        <i class="fas fa-cube text-slate-500"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-xs text-slate-900">#{{ $order->order_number }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                                {{ $order->status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                                   ($order->status === 'out_for_delivery' || $order->status === 'in_transit' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                                   ($order->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700')) }}">
                                                {{ str_replace('_', ' ', $order->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            <i class="fas fa-user text-[10px] mr-1 text-slate-400"></i> {{ $order->customer_name ?? $order->client?->name ?? 'Customer' }}
                                            @if($order->customer_phone)
                                                • {{ $order->customer_phone }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 sm:text-right">
                                    <div>
                                        <p class="font-bold font-mono text-sm text-slate-900">Rs. {{ number_format($order->total_amount, 2) }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="{{ route('seller.orders.show', $order->id) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 text-xs font-bold transition">
                                        Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3 text-xl">
                            <i class="fas fa-cart-arrow-down"></i>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">No Orders Yet</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">Start by booking your first customer order with door-to-door rider pickup and delivery.</p>
                        <a href="{{ route('seller.orders.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                            Book First Order
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Wallet Payouts & Fast Links -->
        <div class="space-y-6">
            <!-- Digital Settlement Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 rounded-2xl p-6 text-white border border-slate-800 shadow-md space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Merchant Wallet</span>
                    <i class="fas fa-shield-halved text-slate-500"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Withdrawable Balance</p>
                    <p class="text-2xl font-black font-mono text-white mt-0.5">Rs. {{ number_format($balance ?? 0, 2) }}</p>
                </div>
                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Pending Clearance</span>
                    <span class="font-mono font-bold text-amber-400">Rs. {{ number_format($pendingBalance ?? 0, 2) }}</span>
                </div>
                <a href="{{ route('seller.withdraw') }}" class="block w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-center font-bold text-xs transition shadow-md shadow-emerald-950">
                    Request Fast Payout
                </a>
            </div>

            <!-- Top Products Widget -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-slate-900 text-sm">Top Products</h4>
                    <a href="{{ route('seller.products.index') }}" class="text-xs text-emerald-600 font-bold hover:underline">All &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse($topProducts ?? [] as $product)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100 last:border-0">
                            <div class="min-w-0 pr-2">
                                <p class="font-bold text-slate-800 truncate">{{ $product->name }}</p>
                                <p class="text-[10px] text-slate-400">Rs. {{ number_format($product->price, 2) }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold font-mono text-[10px] flex-shrink-0">
                                {{ $product->total_sold ?? 0 }} sold
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">No products cataloged yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection