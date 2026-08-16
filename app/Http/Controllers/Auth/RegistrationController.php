<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm(Request $request)
    {
        $userType = $request->get('type', 'customer');
        $allowedTypes = ['customer', 'seller', 'rider', 'partner'];
        
        if (!in_array($userType, $allowedTypes)) {
            $userType = 'customer';
        }
        
        return view('auth.register', compact('userType'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // Validate only the fields that exist in the form
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = $this->create($request->all());

        // Log the registration
        \Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'type' => $user->user_type
        ]);

        return redirect()->route('registration.pending')
            ->with('success', 'Registration successful! Please wait for admin approval.')
            ->with('user', $user);
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        // Base rules for all users
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'dob' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['required', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:customer,seller,rider,partner'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'terms' => ['required', 'accepted'],
        ];

        // Additional validation based on user type
        if (isset($data['user_type'])) {
            if (in_array($data['user_type'], ['seller', 'partner'])) {
                $rules['business_name'] = ['required', 'string', 'max:255'];
                $rules['business_address'] = ['required', 'string', 'max:500'];
                $rules['pan_number'] = ['nullable', 'string', 'max:50'];
            }

            if ($data['user_type'] === 'rider') {
                $rules['license_number'] = ['required', 'string', 'max:50'];
                $rules['vehicle_type'] = ['required', 'string', 'max:50'];
                $rules['vehicle_registration_number'] = ['required', 'string', 'max:50'];
            }
        }

        // Custom error messages
        $messages = [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'dob.required' => 'Date of birth is required.',
            'dob.before' => 'Date of birth must be before today.',
            'dob.after' => 'Invalid date of birth.',
            'gender.required' => 'Gender is required.',
            'nationality.required' => 'Nationality is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'user_type.required' => 'User type is required.',
            'user_type.in' => 'Invalid user type selected.',
            'terms.required' => 'You must accept the terms and conditions.',
            'terms.accepted' => 'You must accept the terms and conditions.',
            'business_name.required' => 'Business name is required.',
            'business_address.required' => 'Business address is required.',
            'license_number.required' => 'License number is required.',
            'vehicle_type.required' => 'Vehicle type is required.',
            'vehicle_registration_number.required' => 'Vehicle registration number is required.',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        $userData = [
            'name' => $data['name'],
            'username' => Str::slug($data['name']) . '-' . rand(1000, 9999),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'user_type' => $data['user_type'],
            'verification_status' => 'pending',
            'registration_completed' => true,
            
            // Personal Information
            'dob' => $data['dob'] ?? null,
            'gender' => $data['gender'] ?? null,
            'nationality' => $data['nationality'] ?? 'Nepali',
            
            // Address Information
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'province' => $data['province'] ?? null,
            'country' => 'Nepal',
        ];

        // Add business fields for sellers and partners
        if (in_array($data['user_type'], ['seller', 'partner'])) {
            $userData['business_name'] = $data['business_name'] ?? null;
            $userData['business_address'] = $data['business_address'] ?? null;
            $userData['pan_number'] = $data['pan_number'] ?? null;
        }

        // Add rider fields
        if ($data['user_type'] === 'rider') {
            $userData['license_number'] = $data['license_number'] ?? null;
            $userData['vehicle_type'] = $data['vehicle_type'] ?? null;
            $userData['vehicle_registration_number'] = $data['vehicle_registration_number'] ?? null;
            $userData['is_available'] = true;
            $userData['rating'] = 5.00;
        }

        return User::create($userData);
    }

    /**
     * Show pending approval page
     */
    public function showPendingPage()
    {
        return view('auth.pending-approval');
    }

    /**
     * Show approval status for a specific user
     */
    public function showApprovalStatus($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->verification_status === 'approved') {
            return redirect()->route('login')
                ->with('success', 'Your account has been approved! Please login.');
        }
        
        if ($user->verification_status === 'rejected') {
            return redirect()->route('login')
                ->with('error', 'Your account has been rejected. Reason: ' . ($user->rejection_reason ?? 'Please contact support.'));
        }
        
        return view('auth.pending-approval', compact('user'));
    }
}