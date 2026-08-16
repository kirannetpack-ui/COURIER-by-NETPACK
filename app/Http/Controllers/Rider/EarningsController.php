<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rider = Auth::user();
        $riderId = $rider->id;

        // Get earnings stats
        $stats = [
            'total_earnings' => Delivery::where('rider_id', $riderId)
                ->where('status', 'delivered')
                ->sum('delivery_fee'),
            'today_earnings' => Delivery::where('rider_id', $riderId)
                ->where('status', 'delivered')
                ->whereDate('delivered_at', today())
                ->sum('delivery_fee'),
            'week_earnings' => Delivery::where('rider_id', $riderId)
                ->where('status', 'delivered')
                ->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('delivery_fee'),
            'month_earnings' => Delivery::where('rider_id', $riderId)
                ->where('status', 'delivered')
                ->whereMonth('delivered_at', now()->month)
                ->sum('delivery_fee'),
            'total_deliveries' => Delivery::where('rider_id', $riderId)
                ->where('status', 'delivered')
                ->count(),
            'pending_deliveries' => Delivery::where('rider_id', $riderId)
                ->whereIn('status', ['assigned', 'picked_up', 'in_transit', 'out_for_delivery'])
                ->count(),
        ];

        // Get wallet balance
        $wallet = Wallet::where('user_id', $riderId)->first();
        $walletBalance = $wallet ? $wallet->balance : 0;

        // Get recent transactions
        $transactions = Transaction::where('wallet_id', $wallet->id ?? 0)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('rider.earnings', compact('rider', 'stats', 'walletBalance', 'transactions'));
    }
}