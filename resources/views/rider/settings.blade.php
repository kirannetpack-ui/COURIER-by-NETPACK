@extends('layouts.app')

@section('title', 'Rider Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold text-gray-800">Rider Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your rider profile and preferences</p>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Profile Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Profile Information</h3>
                <form method="POST" action="{{ route('rider.update-profile') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $rider->name) }}" required 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone Number *</label>
                            <input type="tel" name="phone" value="{{ old('phone', $rider->phone) }}" required 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Address</label>
                            <input type="text" name="address" value="{{ old('address', $rider->address) }}" 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city', $rider->city) }}" 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">District</label>
                            <input type="text" name="district" value="{{ old('district', $rider->district) }}" 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Province</label>
                            <input type="text" name="province" value="{{ old('province', $rider->province) }}" 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Vehicle Type *</label>
                            <select name="vehicle_type" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="bike" {{ old('vehicle_type', $rider->vehicle_type) === 'bike' ? 'selected' : '' }}>Bike</option>
                                <option value="scooter" {{ old('vehicle_type', $rider->vehicle_type) === 'scooter' ? 'selected' : '' }}>Scooter</option>
                                <option value="car" {{ old('vehicle_type', $rider->vehicle_type) === 'car' ? 'selected' : '' }}>Car</option>
                                <option value="van" {{ old('vehicle_type', $rider->vehicle_type) === 'van' ? 'selected' : '' }}>Van</option>
                                <option value="truck" {{ old('vehicle_type', $rider->vehicle_type) === 'truck' ? 'selected' : '' }}>Truck</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">License Number *</label>
                            <input type="text" name="license_number" value="{{ old('license_number', $rider->license_number) }}" required 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                            <i class="fas fa-save mr-2"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Availability -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Availability</h3>
                <form method="POST" action="{{ route('rider.update-availability') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_available" value="1" 
                                   {{ old('is_available', $rider->is_available) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <span class="text-sm font-medium">Available for deliveries</span>
                        </label>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i> Update Availability
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Change Password</h3>
                <form method="POST" action="{{ route('rider.update-password') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Current Password *</label>
                            <input type="password" name="current_password" required 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">New Password *</label>
                            <input type="password" name="new_password" required 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Confirm New Password *</label>
                            <input type="password" name="new_password_confirmation" required 
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-key mr-2"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection