<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\RiderDeposit;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\CODSettlement;
use App\Models\ReminderLog;
use App\Models\SellerPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CODSettlementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if rider can accept COD order
     */
    public function canAcceptCOD($orderId)
    {
        $rider = Auth::user();
        $order = Order::findOrFail($orderId);

        if ($order->payment_method !== 'cod') {
            return response()->json([
                'success' => true,
                'can_accept' => true,
                'message' => 'This is not a COD order'
            ]);
        }

        $depositBalance = $rider->rider_deposit_balance ?? 0;
        $codAmount = $order->cod_amount ?? 0;

        if ($depositBalance < $codAmount) {
            return response()->json([
                'success' => false,
                'can_accept' => false,
                'message' => "Insufficient deposit balance. Required: Rs. {$codAmount}, Available: Rs. {$depositBalance}",
                'required_amount' => $codAmount,
                'available_balance' => $depositBalance,
            ]);
        }

        return response()->json([
            'success' => true,
            'can_accept' => true,
            'message' => 'You can accept this COD order',
            'available_balance' => $depositBalance,
        ]);
    }

    /**
     * Complete COD delivery - IMMEDIATE SETTLEMENT
     */
    public function settleCOD(Request $request, $orderId)
    {
        $rider = Auth::user();
        $order = Order::where('rider_id', $rider->id)
            ->where('id', $orderId)
            ->whereIn('status', ['out_for_delivery', 'assigned', 'picked_up', 'in_transit'])
            ->firstOrFail();

        $request->validate([
            'cod_collected_amount' => 'required|numeric|min:0',
            'signature' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Get pre-negotiated rates from rider
        $deliveryFee = $rider->rider_delivery_fee ?? 100;
        $commissionRate = $rider->rider_commission_rate ?? 10; // 10% commission
        $marginRate = $rider->rider_margin_rate ?? 15; // 15% admin margin

        $codAmount = $order->cod_amount ?? 0;
        $riderCommission = ($deliveryFee * $commissionRate) / 100;
        $adminMargin = ($deliveryFee * $marginRate) / 100;
        $riderEarnings = $deliveryFee + $riderCommission;
        $sellerAmount = $codAmount;

        DB::beginTransaction();

        try {
            // 1. Update order status
            $order->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'cod_collected_amount' => $request->cod_collected_amount,
                'cod_collected_at' => now(),
                'cod_status' => 'settled',
                'settlement_status' => 'completed',
                'seller_amount' => $sellerAmount,
                'rider_amount' => $riderEarnings,
                'margin_amount' => $adminMargin,
            ]);

            // 2. Update delivery
            $delivery = Delivery::where('order_id', $order->id)->first();
            if ($delivery) {
                $delivery->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
            }

            // 3. DEDUCT COD AMOUNT FROM RIDER DEPOSIT
            $rider->rider_deposit_balance -= $codAmount;
            $rider->save();

            // 4. Create rider deposit record for deduction
            RiderDeposit::create([
                'rider_id' => $rider->id,
                'amount' => -$codAmount,
                'balance' => $rider->rider_deposit_balance,
                'type' => 'settlement',
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'description' => "COD Settlement - Order #{$order->order_number} (COD amount deducted)",
                'status' => 'completed',
                'verified_at' => now(),
                'metadata' => [
                    'order_id' => $order->id,
                    'cod_amount' => $codAmount,
                    'action' => 'deduct',
                ]
            ]);

            // 5. RELEASE SELLER PAYMENT (to seller's default account)
            $this->releaseSellerPayment($order, $sellerAmount);

            // 6. ADD RIDER EARNINGS to rider wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $rider->id],
                ['balance' => 0, 'pending_balance' => 0]
            );
            $wallet->addBalance(
                $riderEarnings,
                "Delivery fee + commission for Order #{$order->order_number}",
                'delivery'
            );

            // 7. ADD ADMIN MARGIN to admin wallet
            $this->addAdminMargin($adminMargin, $order);

            // 8. Update rider stats
            $rider->increment('total_deliveries');
            $rider->total_earnings += $riderEarnings;
            $rider->save();

            // 9. Create COD settlement record (for reference)
            $settlement = CODSettlement::create([
                'order_id' => $order->id,
                'delivery_id' => $delivery->id ?? null,
                'seller_id' => $order->seller_id,
                'rider_id' => $rider->id,
                'cod_amount' => $codAmount,
                'delivery_charge' => $deliveryFee,
                'admin_margin' => $adminMargin,
                'seller_amount' => $sellerAmount,
                'rider_amount' => $riderEarnings,
                'margin_amount' => $adminMargin,
                'settlement_status' => 'completed',
                'settlement_date' => now(),
                'settlement_reference' => 'SET-' . date('Ymd') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'collected_at' => now(),
                'verified_at' => now(),
                'verified_by' => $rider->id,
                'metadata' => [
                    'collected_amount' => $request->cod_collected_amount,
                    'signature' => $request->signature,
                    'notes' => $request->notes,
                    'rider_earnings' => $riderEarnings,
                    'admin_margin' => $adminMargin,
                ]
            ]);

            DB::commit();

            // Notify all parties
            $this->notifySettlementComplete($settlement, $order, $rider);

            return redirect()->route('rider.orders.my')
                ->with('success', "🎉 COD delivery settled! 
                    Amount deducted from deposit: Rs. {$codAmount} | 
                    You earned: Rs. {$riderEarnings}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to settle COD: ' . $e->getMessage());
        }
    }

    /**
     * Release seller payment to their default account
     */
    private function releaseSellerPayment($order, $amount)
    {
        $seller = User::find($order->seller_id);
        if (!$seller) return;

        // Get seller's default payment method
        $paymentMethod = SellerPaymentMethod::where('user_id', $seller->id)
            ->where('is_default', true)
            ->where('is_verified', true)
            ->first();

        // Add to seller's wallet (they can withdraw later)
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $seller->id],
            ['balance' => 0, 'pending_balance' => 0]
        );
        $wallet->addBalance(
            $amount,
            "COD payment for Order #{$order->order_number}",
            'cod_settlement'
        );

        // Log the seller payment
        ReminderLog::create([
            'pickup_request_id' => null,
            'reminder_id' => null,
            'reminder_type' => 'seller_payment',
            'sent_to' => $seller->email,
            'message' => "COD payment of Rs. {$amount} has been added to your wallet for Order #{$order->order_number}. Payment method: " . ($paymentMethod ? $paymentMethod->display_name : 'Wallet'),
            'channel' => 'database',
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => [
                'order_id' => $order->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod ? $paymentMethod->method_type : 'wallet',
            ]
        ]);
    }

    /**
     * Add admin margin to admin wallet
     */
    private function addAdminMargin($amount, $order)
    {
        // Get super admin
        $admin = User::where('user_type', 'super_admin')->first();
        if (!$admin) {
            $admin = User::where('user_type', 'admin')->first();
        }

        if ($admin) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $admin->id],
                ['balance' => 0, 'pending_balance' => 0]
            );
            $wallet->addBalance(
                $amount,
                "Admin margin for Order #{$order->order_number}",
                'admin_margin'
            );
        }

        // Log admin margin
        ReminderLog::create([
            'pickup_request_id' => null,
            'reminder_id' => null,
            'reminder_type' => 'admin_margin',
            'sent_to' => 'admin',
            'message' => "Admin margin of Rs. {$amount} added for Order #{$order->order_number}",
            'channel' => 'database',
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => [
                'order_id' => $order->id,
                'amount' => $amount,
            ]
        ]);
    }

    /**
     * Get rider deposit balance
     */
    public function getDepositBalance()
    {
        $rider = Auth::user();
        return response()->json([
            'balance' => $rider->rider_deposit_balance ?? 0,
            'limit' => $rider->rider_deposit_limit ?? 50000,
            'available' => ($rider->rider_deposit_balance ?? 0),
        ]);
    }

    /**
     * Get rider deposit history
     */
    public function depositHistory()
    {
        $riderId = Auth::id();
        
        $deposits = RiderDeposit::where('rider_id', $riderId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('rider.deposit-history', compact('deposits'));
    }

    /**
     * Get rider settlement summary
     */
    public function settlementSummary()
    {
        $riderId = Auth::id();
        
        $stats = [
            'total_settlements' => CODSettlement::where('rider_id', $riderId)->count(),
            'total_cod_amount' => CODSettlement::where('rider_id', $riderId)->sum('cod_amount'),
            'total_earnings' => CODSettlement::where('rider_id', $riderId)->sum('rider_amount'),
            'total_margin' => CODSettlement::where('rider_id', $riderId)->sum('admin_margin'),
            'today_settlements' => CODSettlement::where('rider_id', $riderId)
                ->whereDate('created_at', today())
                ->count(),
        ];

        return response()->json($stats);
    }

    private function notifySettlementComplete($settlement, $order, $rider)
    {
        $message = "✅ COD SETTLEMENT COMPLETED\n\n";
        $message .= "Order: #{$order->order_number}\n";
        $message .= "Rider: {$rider->name}\n";
        $message .= "COD Amount: Rs. {$settlement->cod_amount}\n";
        $message .= "Rider Earnings: Rs. {$settlement->rider_amount}\n";
        $message .= "Admin Margin: Rs. {$settlement->admin_margin}\n";
        $message .= "Seller Amount: Rs. {$settlement->seller_amount}\n";
        $message .= "Settlement Reference: {$settlement->settlement_reference}\n";
        $message .= "Completed at: " . now()->format('Y-m-d H:i:s');

        // Notify admin
        $admins = User::whereIn('user_type', ['admin', 'super_admin', 'domestic_admin'])->get();
        foreach ($admins as $admin) {
            ReminderLog::create([
                'pickup_request_id' => null,
                'reminder_id' => null,
                'reminder_type' => 'cod_settlement_complete',
                'sent_to' => $admin->email,
                'message' => $message,
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => [
                    'settlement_id' => $settlement->id,
                    'order_id' => $order->id,
                    'action' => 'settled',
                ]
            ]);
        }

        // Notify seller
        $seller = User::find($order->seller_id);
        if ($seller) {
            ReminderLog::create([
                'pickup_request_id' => null,
                'reminder_id' => null,
                'reminder_type' => 'seller_settlement',
                'sent_to' => $seller->email,
                'message' => "COD payment of Rs. {$settlement->seller_amount} has been credited to your wallet for Order #{$order->order_number}",
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => [
                    'order_id' => $order->id,
                    'amount' => $settlement->seller_amount,
                ]
            ]);
        }
    }
}