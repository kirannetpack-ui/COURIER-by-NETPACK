@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold text-gray-800">Add New User</h1>
            <p class="text-sm text-gray-500 mt-1">Create a new user account with any role</p>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Personal Information -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Personal Information</h3>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Gender</label>
                        <select name="gender" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob') }}" 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <!-- Role & Status -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Account Settings</h3>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">User Role *</label>
                        <select name="user_type" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('user_type') border-red-500 @enderror">
                            <option value="">Select User Role</option>
                            <option value="admin" {{ old('user_type') === 'admin' ? 'selected' : '' }}>👑 Administrator</option>
                            <option value="staff" {{ old('user_type') === 'staff' ? 'selected' : '' }}>👔 Staff</option>
                            <option value="seller" {{ old('user_type') === 'seller' ? 'selected' : '' }}>🛒 Seller</option>
                            <option value="rider" {{ old('user_type') === 'rider' ? 'selected' : '' }}>🏍️ Rider</option>
                            <option value="client" {{ old('user_type') === 'client' ? 'selected' : '' }}>💼 Client</option>
                            <option value="partner" {{ old('user_type') === 'partner' ? 'selected' : '' }}>🤝 Partner</option>
                            <option value="overseas" {{ old('user_type') === 'overseas' ? 'selected' : '' }}>🌍 Overseas Partner</option>
                            <option value="domestic" {{ old('user_type') === 'domestic' ? 'selected' : '' }}>🏠 Domestic User</option>
                            <option value="customer" {{ old('user_type') === 'customer' ? 'selected' : '' }}>👤 Customer</option>
                        </select>
                        @error('user_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Select the role that best describes this user's function</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Account Status *</label>
                        <select name="verification_status" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('verification_status') border-red-500 @enderror">
                            <option value="pending" {{ old('verification_status') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="approved" {{ old('verification_status') === 'approved' ? 'selected' : '' }}>✅ Approved</option>
                            <option value="rejected" {{ old('verification_status') === 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                            <option value="suspended" {{ old('verification_status') === 'suspended' ? 'selected' : '' }}>⛔ Suspended</option>
                        </select>
                        @error('verification_status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Security</h3>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Password *</label>
                        <input type="password" name="password" required 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Address Information</h3>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Permanent Address</label>
                        <input type="text" name="permanent_address" value="{{ old('permanent_address') }}" 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Temporary Address</label>
                        <input type="text" name="temporary_address" value="{{ old('temporary_address') }}" 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3 pt-4 border-t">
                    <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                        <i class="fas fa-user-plus mr-2"></i> Create User
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection