<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OverseasPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OverseasPartnerController extends Controller
{
    public function index()
    {
        $partners = OverseasPartner::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.overseas-partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.overseas-partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:overseas_partners',
            'contact_person' => 'required|string',
        ]);

        $autoPassword = Str::random(12);

        $partner = OverseasPartner::create([
            'name' => $request->name,
            'code' => 'OS' . strtoupper(uniqid()),
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($autoPassword),
            'auto_password' => $autoPassword,
            'password_changed' => false,
            'contact_person' => $request->contact_person,
            'status' => 'active'
        ]);

        // Create user record
        User::create([
            'name' => $request->contact_person,
            'email' => $request->email,
            'password' => Hash::make($autoPassword),
            'phone' => $request->phone,
            'user_type' => 'overseas',
            'verification_status' => 'approved'
        ]);

        return redirect()->route('admin.overseas-partners.index')
            ->with('success', 'Overseas partner created! Auto-password sent to email.');
    }

    public function show($id)
    {
        $partner = OverseasPartner::findOrFail($id);
        return view('admin.overseas-partners.show', compact('partner'));
    }

    public function edit($id)
    {
        $partner = OverseasPartner::findOrFail($id);
        return view('admin.overseas-partners.edit', compact('partner'));
    }

    public function update(Request $request, $id)
    {
        $partner = OverseasPartner::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'contact_person' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        $partner->update($request->all());

        // Update user record
        User::where('email', $partner->email)->update([
            'name' => $request->contact_person,
            'phone' => $request->phone,
        ]);

        return redirect()->route('admin.overseas-partners.index')
            ->with('success', 'Partner updated successfully!');
    }

    public function destroy($id)
    {
        $partner = OverseasPartner::findOrFail($id);
        $email = $partner->email;
        $partner->delete();

        User::where('email', $email)->delete();

        return redirect()->route('admin.overseas-partners.index')
            ->with('success', 'Partner deleted successfully!');
    }

    public function resetPassword($id)
    {
        $partner = OverseasPartner::findOrFail($id);
        $newPassword = Str::random(12);

        $partner->update([
            'password' => Hash::make($newPassword),
            'auto_password' => $newPassword,
            'password_changed' => false
        ]);

        User::where('email', $partner->email)->update([
            'password' => Hash::make($newPassword)
        ]);

        return redirect()->route('admin.overseas-partners.index')
            ->with('success', 'Password reset! New password sent to partner.');
    }
}