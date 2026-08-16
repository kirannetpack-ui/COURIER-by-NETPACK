<?php
// app/Services/SplitPaymentService.php

namespace App\Services;

use App\Models\PaymentIntent;
use App\Models\Settlement;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SplitPaymentService
{
    // Default split percentages (70% seller, 15% NETPACK, 10% rider, 5% tax)
    protected $defaultPercentages = [
        'seller' => 70,
        'netpack' => 15,
        'rider' => 10,
        'tax' => 5
    ];
    
    public function calculateSplit($totalAmount, $customPercentages = null)
    {
        $percentages = $customPercentages ?? $this->defaultPercentages;
        $split = [];
        
        foreach ($percentages as $recipient => $percentage) {
            $amount = round($totalAmount * $percentage / 100, 2);
            $split[$recipient] = $amount;
        }
        
        // Adjust for rounding differences
        $totalSplit = array_sum($split);
        if ($totalSplit != $totalAmount) {
            $diff = $totalAmount - $totalSplit;
            $split['netpack'] += $diff;
        }
        
        return $split;
    }
    
    public function processInstantSplit(PaymentIntent $paymentIntent)
    {
        DB::beginTransaction();
        
        try {
            $split = $paymentIntent->split_breakdown;
            $results = [];
            
            foreach ($split as $recipientType => $amount) {
                if ($amount <= 0) continue;
                
                $recipientId = $this->getRecipientUserId($recipientType, $paymentIntent);
                
                if ($recipientId) {
                    $result = $this->processPayout($recipientId, $amount, $paymentIntent->id, $recipientType);
                    $results[$recipientType] = $result;
                }
            }
            
            $paymentIntent->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);
            
            DB::commit();
            
            Log::info('Split payment processed', [
                'payment_intent_id' => $paymentIntent->id,
                'split' => $split,
                'results' => $results
            ]);
            
            return $results;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Split payment failed', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    protected function getRecipientUserId($recipientType, PaymentIntent $paymentIntent)
    {
        switch ($recipientType) {
            case 'seller':
                return $paymentIntent->seller_id;
            case 'rider':
                return $paymentIntent->rider_id;
            case 'netpack':
                return 1; // Admin/Company account
            case 'tax':
                return null;
            default:
                return null;
        }
    }
    
    protected function processPayout($userId, $amount, $paymentIntentId, $recipientType)
    {
        // Get or create wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['user_type' => $recipientType, 'balance' => 0]
        );
        
        // Create settlement record
        $settlement = Settlement::create([
            'payment_intent_id' => $paymentIntentId,
            'recipient_user_id' => $userId,
            'recipient_type' => $recipientType,
            'amount' => $amount,
            'payout_method' => 'wallet',
            'status' => 'completed',
            'initiated_at' => now(),
            'completed_at' => now()
        ]);
        
        // Credit to wallet
        $wallet->credit($amount, 'settlement', $paymentIntentId, 
            "Payment settlement for shipment #{$paymentIntentId}");
        
        return [
            'success' => true,
            'settlement_id' => $settlement->id,
            'amount' => $amount,
            'recipient' => $recipientType
        ];
    }
}