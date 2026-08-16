@extends('layouts.app')

@section('title', 'Create Shipment')
@section('page-title', 'Create Shipment')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .nav-tabs .nav-link {
        padding: 10px 20px;
        border-radius: 8px 8px 0 0;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        background: #0d9488;
        color: white;
    }
    .nav-tabs .nav-link:not(.active) {
        background: #f3f4f6;
        color: #6b7280;
    }
    .nav-tabs .nav-link:not(.active):hover {
        background: #e5e7eb;
    }
    .tab-content .tab-pane {
        display: none;
    }
    .tab-content .tab-pane.active {
        display: block;
    }
    .map-container {
        height: 250px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .delivery-address-line {
        border-left: 3px solid #0d9488;
        padding-left: 12px;
    }
    .pickup-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
        background: #f9fafb;
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold text-gray-800">Create New Shipment</h1>
            <p class="text-sm text-gray-500 mt-1">Select service type and enter shipment details</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('shipments.store') }}" id="shipmentForm">
                @csrf

                <!-- Service Type Tabs -->
                <div class="mb-6">
                    <div class="nav-tabs flex border-b" role="tablist">
                        <button type="button" class="nav-link active" data-tab="domestic" onclick="switchTab('domestic')">
                            <i class="fas fa-truck mr-2"></i> Domestic
                        </button>
                        <button type="button" class="nav-link" data-tab="international" onclick="switchTab('international')">
                            <i class="fas fa-globe mr-2"></i> International
                        </button>
                        <button type="button" class="nav-link" data-tab="ecommerce" onclick="switchTab('ecommerce')">
                            <i class="fas fa-shopping-cart mr-2"></i> E-Commerce
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Domestic Tab -->
                    <div class="tab-pane active" id="tab-domestic">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Service Type <span class="text-red-500">*</span></label>
                                <select name="service_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="flash">⚡ FLASH (1-2 Hours)</option>
                                    <option value="same_day">🕐 SAME DAY (4-6 Hours)</option>
                                    <option value="standard" selected>🚚 STANDARD (1-2 Days)</option>
                                    <option value="himalayan">🏔️ HIMALAYAN (2-4 Days)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Package Type</label>
                                <select name="package_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="parcel">📦 Parcel</option>
                                    <option value="box">📦 Box</option>
                                    <option value="envelope">✉️ Envelope</option>
                                    <option value="other">📦 Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- International Tab -->
<div class="tab-pane" id="tab-international">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Service Type <span class="text-red-500">*</span></label>
            <select name="service_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="economy" selected>🌍 Economy (7-15 working days)</option>
                <option value="express">✈️ Express (3-4 working days)</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Default: Economy delivery</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Package Type</label>
            <select name="package_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="parcel">📦 Parcel</option>
                <option value="box">📦 Box</option>
                <option value="envelope">✉️ Envelope</option>
                <option value="other">📦 Other</option>
            </select>
        </div>
    </div>
</div>

                    <!-- E-Commerce Tab -->
                    <div class="tab-pane" id="tab-ecommerce">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Service Type</label>
                                <select name="service_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="ecommerce">🛒 E-Commerce Delivery</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Package Type</label>
                                <select name="package_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="parcel">📦 Parcel</option>
                                    <option value="box">📦 Box</option>
                                    <option value="envelope">✉️ Envelope</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden field for shipment type -->
                <input type="hidden" name="shipment_type" id="shipment_type" value="domestic">

                <!-- ============================================= -->
                <!-- PICKUP POINTS -->
                <!-- ============================================= -->
                <div class="mt-6 border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">📍 Pickup Points</h3>
                    <div id="pickup-container">
                        <div class="pickup-card" id="pickup-0">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium text-teal-600">Pickup Point #1</span>
                                <button type="button" onclick="removePickup(0)" class="text-red-500 hover:text-red-700 hidden">Remove</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Contact Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="pickup_name[]" required 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                           placeholder="Contact name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="pickup_phone[]" required 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                           placeholder="Phone number">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Pickup Address <span class="text-red-500">*</span></label>
                                    <textarea name="pickup_address[]" rows="2" required 
                                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                              placeholder="Full pickup address"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <div id="pickup-map" class="map-container"></div>
                                    <div class="flex gap-2 mt-2">
                                        <input type="hidden" name="pickup_lat[]" id="pickup-lat-0">
                                        <input type="hidden" name="pickup_lng[]" id="pickup-lng-0">
                                        <button type="button" onclick="getLocation('pickup', 0)" class="text-sm text-teal-600 hover:text-teal-800">
                                            <i class="fas fa-location-arrow mr-1"></i> Use Current Location
                                        </button>
                                        <button type="button" onclick="searchLocation('pickup', 0)" class="text-sm text-teal-600 hover:text-teal-800">
                                            <i class="fas fa-search mr-1"></i> Search Location
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addPickup()" class="text-teal-600 hover:text-teal-800 text-sm mt-2">
                        <i class="fas fa-plus mr-1"></i> Add Another Pickup Point
                    </button>
                </div>

                <!-- ============================================= -->
                <!-- DELIVERY ADDRESS (Standard 5-Line Format) -->
                <!-- ============================================= -->
                <div class="mt-6 border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">📦 Delivery Address</h3>
                    
                    <!-- Domestic / E-commerce: Multiple delivery points -->
                    <div id="delivery-multiple" class="delivery-type-section">
                        <div id="delivery-container">
                            <div class="pickup-card" id="delivery-0">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-teal-600">Delivery Point #1</span>
                                    <button type="button" onclick="removeDelivery(0)" class="text-red-500 hover:text-red-700 hidden">Remove</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Contact Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="delivery_name[]" required 
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                               placeholder="Contact name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                                        <input type="text" name="delivery_phone[]" required 
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                               placeholder="Phone number">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium mb-1">Delivery Address <span class="text-red-500">*</span></label>
                                        <textarea name="delivery_address[]" rows="2" required 
                                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                                  placeholder="Full delivery address"></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <div id="delivery-map" class="map-container"></div>
                                        <div class="flex gap-2 mt-2">
                                            <input type="hidden" name="delivery_lat[]" id="delivery-lat-0">
                                            <input type="hidden" name="delivery_lng[]" id="delivery-lng-0">
                                            <button type="button" onclick="getLocation('delivery', 0)" class="text-sm text-teal-600 hover:text-teal-800">
                                                <i class="fas fa-location-arrow mr-1"></i> Use Current Location
                                            </button>
                                            <button type="button" onclick="searchLocation('delivery', 0)" class="text-sm text-teal-600 hover:text-teal-800">
                                                <i class="fas fa-search mr-1"></i> Search Location
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addDelivery()" class="text-teal-600 hover:text-teal-800 text-sm mt-2">
                            <i class="fas fa-plus mr-1"></i> Add Another Delivery Point
                        </button>
                    </div>

                    <!-- International: Single delivery with 5-line format -->
                    <div id="delivery-international" class="delivery-type-section hidden">
                        <div class="delivery-address-line p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500 mb-2">Standard 5-Line International Address Format</p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Recipient Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="receiver_name" id="receiver_name" 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                           placeholder="Full name and company (if applicable)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Street Address <span class="text-red-500">*</span></label>
                                    <input type="text" name="receiver_street" id="receiver_street" 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                           placeholder="House number, street name, unit/apt number">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">City <span class="text-red-500">*</span></label>
                                        <input type="text" name="receiver_city" id="receiver_city" 
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                               placeholder="City or Town">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">State/Province <span class="text-red-500">*</span></label>
                                        <input type="text" name="receiver_state" id="receiver_state" 
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                               placeholder="State or Province">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Postal Code <span class="text-red-500">*</span></label>
                                        <input type="text" name="receiver_postal_code" id="receiver_postal_code" 
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                               placeholder="Postal code / ZIP code">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Country <span class="text-red-500">*</span></label>
                                        <select name="receiver_country" id="receiver_country" 
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            <option value="">Select Country</option>
                                            <option value="United States">🇺🇸 United States</option>
                                            <option value="United Kingdom">🇬🇧 United Kingdom</option>
                                            <option value="Australia">🇦🇺 Australia</option>
                                            <option value="Canada">🇨🇦 Canada</option>
                                            <option value="Germany">🇩🇪 Germany</option>
                                            <option value="France">🇫🇷 France</option>
                                            <option value="Japan">🇯🇵 Japan</option>
                                            <option value="China">🇨🇳 China</option>
                                            <option value="India">🇮🇳 India</option>
                                            <option value="UAE">🇦🇪 UAE</option>
                                            <option value="Singapore">🇸🇬 Singapore</option>
                                            <option value="Malaysia">🇲🇾 Malaysia</option>
                                            <option value="Thailand">🇹🇭 Thailand</option>
                                            <option value="Vietnam">🇻🇳 Vietnam</option>
                                            <option value="South Korea">🇰🇷 South Korea</option>
                                            <option value="Other">🌍 Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Tax ID (Optional)</label>
                                    <input type="text" name="receiver_tax_id" id="receiver_tax_id" 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                           placeholder="Tax ID / VAT number">
                                </div>
                            </div>
                        </div>
                        <div id="delivery-international-map" class="map-container mt-3"></div>
                        <div class="flex gap-2 mt-2">
                            <input type="hidden" name="delivery_lat_international" id="delivery-lat-intl">
                            <input type="hidden" name="delivery_lng_international" id="delivery-lng-intl">
                            <button type="button" onclick="getInternationalLocation()" class="text-sm text-teal-600 hover:text-teal-800">
                                <i class="fas fa-location-arrow mr-1"></i> Use Current Location
                            </button>
                            <button type="button" onclick="searchInternationalLocation()" class="text-sm text-teal-600 hover:text-teal-800">
                                <i class="fas fa-search mr-1"></i> Search Location
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================= -->
                <!-- SHIPMENT DETAILS -->
                <!-- ============================================= -->
                <div class="mt-6 border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">📋 Shipment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                            <input type="number" name="weight" step="0.01" min="0.1" value="1" required 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Length (cm)</label>
                            <input type="number" name="length" step="0.1" min="0" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Width (cm)</label>
                            <input type="number" name="width" step="0.1" min="0" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Height (cm)</label>
                            <input type="number" name="height" step="0.1" min="0" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Description (Optional)</label>
                            <textarea name="description" rows="2" 
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                      placeholder="Describe the contents of the shipment"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-6 flex gap-3 pt-4 border-t">
                    <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                        <i class="fas fa-save mr-2"></i> Create Shipment
                    </button>
                    <button type="reset" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
                    <a href="{{ route('shipments.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let pickupCount = 1;
    let deliveryCount = 1;
    let pickupMaps = {};
    let deliveryMaps = {};
    let internationalMap = null;

    // =============================================
    // TAB SWITCHING
    // =============================================
    function switchTab(tab) {
        // Update tabs
        document.querySelectorAll('.nav-tabs .nav-link').forEach(el => el.classList.remove('active'));
        document.querySelector(`.nav-tabs .nav-link[data-tab="${tab}"]`).classList.add('active');
        
        // Update content
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.getElementById(`tab-${tab}`).classList.add('active');
        
        // Update shipment type
        document.getElementById('shipment_type').value = tab;
        
        // Show/hide delivery sections
        if (tab === 'international') {
            document.getElementById('delivery-multiple').classList.add('hidden');
            document.getElementById('delivery-international').classList.remove('hidden');
        } else {
            document.getElementById('delivery-multiple').classList.remove('hidden');
            document.getElementById('delivery-international').classList.add('hidden');
        }
    }

    // =============================================
    // PICKUP FUNCTIONS
    // =============================================
    function addPickup() {
        const container = document.getElementById('pickup-container');
        const id = pickupCount;
        const div = document.createElement('div');
        div.className = 'pickup-card';
        div.id = `pickup-${id}`;
        div.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <span class="font-medium text-teal-600">Pickup Point #${id + 1}</span>
                <button type="button" onclick="removePickup(${id})" class="text-red-500 hover:text-red-700">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Name <span class="text-red-500">*</span></label>
                    <input type="text" name="pickup_name[]" required 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                           placeholder="Contact name">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="pickup_phone[]" required 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                           placeholder="Phone number">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Pickup Address <span class="text-red-500">*</span></label>
                    <textarea name="pickup_address[]" rows="2" required 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                              placeholder="Full pickup address"></textarea>
                </div>
                <div class="md:col-span-2">
                    <div id="pickup-map-${id}" class="map-container"></div>
                    <div class="flex gap-2 mt-2">
                        <input type="hidden" name="pickup_lat[]" id="pickup-lat-${id}">
                        <input type="hidden" name="pickup_lng[]" id="pickup-lng-${id}">
                        <button type="button" onclick="getLocation('pickup', ${id})" class="text-sm text-teal-600 hover:text-teal-800">
                            <i class="fas fa-location-arrow mr-1"></i> Use Current Location
                        </button>
                        <button type="button" onclick="searchLocation('pickup', ${id})" class="text-sm text-teal-600 hover:text-teal-800">
                            <i class="fas fa-search mr-1"></i> Search Location
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        pickupCount++;
        
        setTimeout(() => initPickupMap(id), 200);
    }

    function removePickup(id) {
        const el = document.getElementById(`pickup-${id}`);
        if (el && document.querySelectorAll('.pickup-card').length > 1) {
            el.remove();
        } else {
            alert('You need at least one pickup point.');
        }
    }

    function initPickupMap(id) {
        const mapId = `pickup-map-${id}`;
        const mapElement = document.getElementById(mapId);
        if (!mapElement) return;
        
        const map = L.map(mapId).setView([27.7172, 85.3240], 13);
        pickupMaps[id] = map;
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
        
        const marker = L.marker([27.7172, 85.3240], { draggable: true }).addTo(map);
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            document.getElementById(`pickup-lat-${id}`).value = pos.lat;
            document.getElementById(`pickup-lng-${id}`).value = pos.lng;
        });
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById(`pickup-lat-${id}`).value = e.latlng.lat;
            document.getElementById(`pickup-lng-${id}`).value = e.latlng.lng;
        });
        map.marker = marker;
    }

    // =============================================
    // DELIVERY FUNCTIONS (Domestic/E-commerce)
    // =============================================
    function addDelivery() {
        const container = document.getElementById('delivery-container');
        const id = deliveryCount;
        const div = document.createElement('div');
        div.className = 'pickup-card';
        div.id = `delivery-${id}`;
        div.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <span class="font-medium text-teal-600">Delivery Point #${id + 1}</span>
                <button type="button" onclick="removeDelivery(${id})" class="text-red-500 hover:text-red-700">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Name <span class="text-red-500">*</span></label>
                    <input type="text" name="delivery_name[]" required 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                           placeholder="Contact name">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="delivery_phone[]" required 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                           placeholder="Phone number">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Delivery Address <span class="text-red-500">*</span></label>
                    <textarea name="delivery_address[]" rows="2" required 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                              placeholder="Full delivery address"></textarea>
                </div>
                <div class="md:col-span-2">
                    <div id="delivery-map-${id}" class="map-container"></div>
                    <div class="flex gap-2 mt-2">
                        <input type="hidden" name="delivery_lat[]" id="delivery-lat-${id}">
                        <input type="hidden" name="delivery_lng[]" id="delivery-lng-${id}">
                        <button type="button" onclick="getLocation('delivery', ${id})" class="text-sm text-teal-600 hover:text-teal-800">
                            <i class="fas fa-location-arrow mr-1"></i> Use Current Location
                        </button>
                        <button type="button" onclick="searchLocation('delivery', ${id})" class="text-sm text-teal-600 hover:text-teal-800">
                            <i class="fas fa-search mr-1"></i> Search Location
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        deliveryCount++;
        
        setTimeout(() => initDeliveryMap(id), 200);
    }

    function removeDelivery(id) {
        const el = document.getElementById(`delivery-${id}`);
        if (el && document.querySelectorAll('#delivery-container .pickup-card').length > 1) {
            el.remove();
        } else {
            alert('You need at least one delivery point.');
        }
    }

    function initDeliveryMap(id) {
        const mapId = `delivery-map-${id}`;
        const mapElement = document.getElementById(mapId);
        if (!mapElement) return;
        
        const map = L.map(mapId).setView([27.7172, 85.3240], 13);
        deliveryMaps[id] = map;
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
        
        const marker = L.marker([27.7172, 85.3240], { draggable: true }).addTo(map);
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            document.getElementById(`delivery-lat-${id}`).value = pos.lat;
            document.getElementById(`delivery-lng-${id}`).value = pos.lng;
        });
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById(`delivery-lat-${id}`).value = e.latlng.lat;
            document.getElementById(`delivery-lng-${id}`).value = e.latlng.lng;
        });
        map.marker = marker;
    }

    // =============================================
    // INTERNATIONAL DELIVERY MAP
    // =============================================
    function initInternationalMap() {
        const mapElement = document.getElementById('delivery-international-map');
        if (!mapElement) return;
        
        internationalMap = L.map('delivery-international-map').setView([27.7172, 85.3240], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(internationalMap);
        
        const marker = L.marker([27.7172, 85.3240], { draggable: true }).addTo(internationalMap);
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            document.getElementById('delivery-lat-intl').value = pos.lat;
            document.getElementById('delivery-lng-intl').value = pos.lng;
        });
        internationalMap.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('delivery-lat-intl').value = e.latlng.lat;
            document.getElementById('delivery-lng-intl').value = e.latlng.lng;
        });
        internationalMap.marker = marker;
        
        // Also update address fields when map is clicked
        internationalMap.on('click', function(e) {
            // Reverse geocode to get address (using Nominatim)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        // You can populate address fields here if needed
                    }
                })
                .catch(() => {});
        });
    }

    function getInternationalLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    updateInternationalMap(position.coords.latitude, position.coords.longitude);
                },
                function() {
                    alert('Unable to get location.');
                }
            );
        }
    }

    function searchInternationalLocation() {
        const address = prompt('Enter location to search:');
        if (address) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        updateInternationalMap(parseFloat(data[0].lat), parseFloat(data[0].lon));
                    } else {
                        alert('Location not found.');
                    }
                });
        }
    }

    function updateInternationalMap(lat, lng) {
        if (internationalMap) {
            internationalMap.setView([lat, lng], 15);
            if (internationalMap.marker) {
                internationalMap.marker.setLatLng([lat, lng]);
            }
            document.getElementById('delivery-lat-intl').value = lat;
            document.getElementById('delivery-lng-intl').value = lng;
        }
    }

    // =============================================
    // LOCATION FUNCTIONS (Shared)
    // =============================================
    function getLocation(type, id) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    updateMapLocation(type, id, lat, lng);
                },
                function() {
                    alert('Unable to get location. Please enter address manually.');
                }
            );
        }
    }

    function searchLocation(type, id) {
        const address = prompt('Enter location to search:');
        if (address) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        updateMapLocation(type, id, parseFloat(data[0].lat), parseFloat(data[0].lon));
                    } else {
                        alert('Location not found.');
                    }
                });
        }
    }

    function updateMapLocation(type, id, lat, lng) {
        const maps = type === 'pickup' ? pickupMaps : deliveryMaps;
        const latId = type === 'pickup' ? `pickup-lat-${id}` : `delivery-lat-${id}`;
        const lngId = type === 'pickup' ? `pickup-lng-${id}` : `delivery-lng-${id}`;
        
        const map = maps[id];
        if (map) {
            map.setView([lat, lng], 15);
            if (map.marker) {
                map.marker.setLatLng([lat, lng]);
            }
            document.getElementById(latId).value = lat;
            document.getElementById(lngId).value = lng;
        }
    }

    // =============================================
    // INITIALIZE
    // =============================================
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize pickup map
        setTimeout(() => initPickupMap(0), 300);
        
        // Initialize delivery map
        setTimeout(() => initDeliveryMap(0), 300);
        
        // Initialize international map
        setTimeout(initInternationalMap, 300);
    });
</script>
@endpush
@endsection