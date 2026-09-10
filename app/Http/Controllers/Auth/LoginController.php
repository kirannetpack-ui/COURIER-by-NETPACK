<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->remember)) {
        $user = Auth::user();

        if ($user->verification_status !== 'approved') {
            Auth::logout();
            return back()->with('error', 'Your account is pending approval.');
        }

        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        if (!$user->password_changed) {
            return redirect()->route('password.change');
        }

        $targetUrl = $user->dashboardUrl();

        // Safety check for session intended URL:
        // Clear any cross-portal mismatch stored in url.intended
        $intended = $request->session()->get('url.intended');
        if ($intended) {
            $isAuthorizedForIntended = true;

            if (str_contains($intended, '/client') && !in_array($user->user_type, ['client', 'customer', 'super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            } elseif (str_contains($intended, '/admin') && !in_array($user->user_type, ['super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            } elseif (str_contains($intended, '/seller') && !in_array($user->user_type, ['seller', 'super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            } elseif (str_contains($intended, '/rider') && !in_array($user->user_type, ['rider', 'super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            } elseif (str_contains($intended, '/partner') && !in_array($user->user_type, ['partner', 'super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            } elseif (str_contains($intended, '/overseas') && !in_array($user->user_type, ['overseas', 'super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            } elseif (str_contains($intended, '/domestic') && !in_array($user->user_type, ['domestic_admin', 'staff', 'super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            } elseif (str_contains($intended, '/international') && !in_array($user->user_type, ['international_admin', 'staff', 'super_admin', 'admin'], true)) {
                $isAuthorizedForIntended = false;
            }

            if (!$isAuthorizedForIntended) {
                $request->session()->forget('url.intended');
            }
        }

        return redirect()->intended($targetUrl);
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
