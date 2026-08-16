<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\PaymentIntent;
use App\Models\Shipment;
use App\Services\SplitPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $splitService;
    
    public function __construct(SplitPaymentService $splitService)
    {
        $this->splitService = $splitService;
    }
    
    public function createPaymentIntent(Shipment $shipment)
    {
        // Calculate split based on total amount
        $split = $this->splitService->calculateSplit($shipment->total_amount);
        
        $paymentIntent = PaymentIntent::create([
            'intent_id' => 'PI_' . Str::uuid(),
            'shipment_id' => $shipment->id,
            'customer_id' => auth()->id(),
            'seller_id' => $shipment->seller_id ?? null,
            'rider_id' => $shipment->rider_id ?? null,
            'total_amount' => $shipment->total_amount,
            'split_breakdown' => $split,
            'split_percentages' => [
                'seller' => 70,
                'netpack' => 15,
                'rider' => 10,
                'tax' => 5
            ],
            'status' => 'pending',
            'payment_gateway' => 'khalti'
        ]);
        
        return response()->json([
            'success' => true,
            'payment_intent' => $paymentIntent,
            'amount' => $paymentIntent->total_amount,
            'split' => $split
        ]);
    }
    
    public function paymentSuccess(Request $request)
    {
        $paymentIntent = PaymentIntent::where('intent_id', $request->intent_id)->first();
        
        if (!$paymentIntent) {
            return redirect()->route('shipments.index')->with('error', 'Payment intent not found');
        }
        
        // Process instant split payment
        $results = $this->splitService->processInstantSplit($paymentIntent);
        
        // Update shipment status
        $shipment = $paymentIntent->shipment;
        $shipment->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        
        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Payment successful! Funds have been distributed instantly.');
    }
}