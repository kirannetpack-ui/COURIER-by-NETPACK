@extends('layouts.seller')

@section('title', 'Book Multi-Leg Domestic Courier - Inter-City Logistics')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900">Book Multi-Leg Domestic Courier</h1>
            <p class="text-xs text-slate-500 mt-0.5">Nationwide inter-district delivery connecting First-Mile Rider &rarr; Hub &rarr; Domestic Partner Linehaul &rarr; Destination Rider &rarr; Customer.</p>
        </div>
        <a href="{{ route('seller.ecommerce.index') }}" class="px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-700 transition">
            Back to Registry
        </a>
    </div>

    <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('seller.ecommerce.multileg.store') }}" class="space-y-6">
            @csrf

            <!-- 1. Destination City & Province -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-map-location-dot text-sky-600"></i> 1. Destination Regional Hub & City
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Destination City *</label>
                        <select name="destination_city" required class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-sky-500 font-semibold">
                            <option value="">Select Destination City</option>
                            <option value="Pokhara">Pokhara (Gandaki Province Gateway)</option>
                            <option value="Biratnagar">Biratnagar (Koshi Province Gateway)</option>
                            <option value="Birgunj">Birgunj (Madhesh Province Gateway)</option>
                            <option value="Butwal">Butwal / Bhairahawa (Lumbini Gateway)</option>
                            <option value="Chitwan">Bharatpur / Chitwan</option>
                            <option value="Nepalgunj">Nepalgunj (Banke Gateway)</option>
                            <option value="Dhangadhi">Dhangadhi (Sudurpashchim Gateway)</option>
                            <option value="Surkhet">Birendranagar / Surkhet (Karnali Gateway)</option>
                            <option value="Itahari">Itahari / Dharan</option>
                            <option value="Hetauda">Hetauda</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Domestic Partner Linehaul (Optional)</label>
                        <select name="partner_id" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-sky-500">
                            <option value="">Auto-Assign Fastest Domestic Courier Partner</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->company_name }} ({{ $partner->city ?? 'National' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Customer Delivery Details -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-user-check text-sky-600"></i> 2. Recipient Customer Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Customer Full Name *</label>
                        <input type="text" name="delivery_name" value="{{ old('delivery_name') }}" required placeholder="e.g. Ramesh Gurung"
                               class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Customer Mobile *</label>
                        <input type="text" name="delivery_phone" value="{{ old('delivery_phone') }}" required placeholder="e.g. 9856000000"
                               class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Delivery Street Address *</label>
                        <input type="text" name="delivery_address" value="{{ old('delivery_address') }}" required placeholder="e.g. Lakeside Ward 6"
                               class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-sky-500">
                    </div>
                </div>
            </div>

            <!-- 3. Parcel & COD -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-box text-sky-600"></i> 3. Parcel Weight & COD Collection
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Parcel Gross Weight (KG) *</label>
                        <input type="number" step="0.5" name="parcel_weight" value="{{ old('parcel_weight', 1.0) }}" required
                               class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-sky-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">COD Amount to Collect (NPR)</label>
                        <input type="number" step="1" name="cod_amount" value="{{ old('cod_amount', 0) }}"
                               class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-sky-500 font-bold text-amber-600">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-500 flex items-center gap-1.5">
                    <i class="fas fa-network-wired text-sky-600"></i>
                    <span>First-Mile rider will pick up from your store and deliver to origin gateway for intercity linehaul.</span>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-truck-moving"></i> Dispatch Multi-Leg Consignment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
