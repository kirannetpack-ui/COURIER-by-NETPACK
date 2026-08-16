<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get client statistics
        $totalShipments = $user->shipments()->count() ?? 0;
        $inTransit = $user->shipments()->where('status', 'in_transit')->count() ?? 0;
        $delivered = $user->shipments()->where('status', 'delivered')->count() ?? 0;
        $pending = $user->shipments()->where('status', 'pending')->count() ?? 0;
        
        return view('client.dashboard', compact(
            'totalShipments',
            'inTransit',
            'delivered',
            'pending'
        ));
    }
}