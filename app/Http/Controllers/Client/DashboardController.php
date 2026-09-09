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
        $shipments = $user->clientShipments();
        $totalShipments = $shipments->count();
        $inTransit = (clone $shipments)->where('status', 'in_transit')->count();
        $delivered = (clone $shipments)->where('status', 'delivered')->count();
        $pending = (clone $shipments)->whereIn('status', ['pending', 'created', 'confirmed'])->count();
        $recentShipments = (clone $shipments)->latest()->take(5)->get();
        
        // Active ongoing shipments for this client (not delivered, cancelled, or returned)
        $activeShipments = (clone $shipments)
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->latest('updated_at')
            ->get();
        $latestActiveShipment = $activeShipments->first();
        
        return view('client.dashboard', compact(
            'totalShipments',
            'inTransit',
            'delivered',
            'pending',
            'recentShipments',
            'activeShipments',
            'latestActiveShipment'
        ));
    }
}
