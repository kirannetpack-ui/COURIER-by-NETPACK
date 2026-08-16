<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show rider settings page
     */
    public function index()
    {
        $rider = Auth::user();
        return view('rider.settings', compact('rider'));
    }

    /**
     * Update rider profile
     */
    public function updateProfile(Request $request)
    {
        $rider = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'vehicle_type' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $rider->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'province' => $request->province,
            'vehicle_type' => $request->vehicle_type,
            'license_number' => $request->license_number,
        ]);

        return redirect()->route('rider.settings')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update rider password
     */
    public function updatePassword(Request $request)
    {
        $rider = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check current password
        if (!Hash::check($request->current_password, $rider->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        $rider->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('rider.settings')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Update rider availability
     */
    public function updateAvailability(Request $request)
    {
        $rider = Auth::user();

        $rider->update([
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('rider.settings')
            ->with('success', 'Availability updated successfully!');
    }

    /**
     * Update rider bank details
     */
    public function updateBank(Request $request)
    {
        $rider = Auth::user();

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
            'bank_address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update wallet or rider table with bank details
        $wallet = $rider->wallet;
        if ($wallet) {
            $wallet->update([
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->account_number,
                'bank_account_name' => $request->account_holder_name,
                'bank_address' => $request->bank_address,
            ]);
        }

        return redirect()->route('rider.settings')
            ->with('success', 'Bank details updated successfully!');
    }
}