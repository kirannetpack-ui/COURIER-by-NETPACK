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

        // Redirect based on user type
        switch ($user->user_type) {
            case 'super_admin':
                return redirect()->intended('/admin/dashboard');
                
            case 'domestic_admin':
                return redirect()->intended('/domestic/dashboard');
                
            case 'international_admin':
                return redirect()->intended('/international/dashboard');
                
            case 'partner':
                return redirect()->intended('/partner/dashboard');
                
            case 'seller':
                return redirect()->intended('/seller/dashboard');
                
            case 'rider':
                return redirect()->intended('/rider/dashboard');
                
            case 'customer':
            case 'client':
                return redirect()->intended('/client/dashboard');
                
            default:
                return redirect()->intended('/dashboard');
        }
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
