<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return view('welcome');
        }
        
        // Redirect based on user type
        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'seller':
                return redirect()->route('seller.dashboard');
            case 'rider':
                return redirect()->route('rider.dashboard');
            default:
                return redirect()->route('client.dashboard');
        }
    }
}