<?php
// app/Http/Controllers/Api/CustomerPaymentController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\PaymentIntent;
use App\Services\KhaltiPaymentService;
use App\Services\EsewaPaymentService;
use App\Services\SplitPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerPaymentController extends Controller
{
    protected $khaltiService;
    protected $esewaService;
    protected $splitService;
    
    public function __construct(
        KhaltiPaymentService $khaltiService,
        EsewaPaymentService $esewaService,
        SplitPaymentService $splitService
    ) {
        $this->khaltiService = $khaltiService;
        $this->esewaService = $esewaService;
        $this->splitService = $splitService;
    }
    
    public function initiatePayment(Request $request, $shipmentId)
    {
        $shipment = Shipment::where('customer_id', auth()->id())
            ->where('id', $shipmentId)
            ->firstOrFail();
        
        if ($shipment->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment already completed'
            ], 400);
        }
        
        $request->validate([
            'gateway' => 'required|in:khalti,esewa'
        ]);
        
        $customer = auth()->user();
        $gateway = $request->gateway;
        
        // Create payment intent
        $split = $this->splitService->calculateSplit($shipment->total_amount);
        
        $paymentIntent = PaymentIntent::create([
            'intent_id' => 'PI_' . Str::uuid(),
            'shipment_id' => $shipment->id,
            'customer_id' => $customer->id,
            'seller_id' => $shipment->seller_id,
            'total_amount' => $shipment->total_amount,
            'split_breakdown' => $split,
            'split_percentages' => [
                'seller' => 70,
                'netpack' => 15,
                'rider' => 10,
                'tax' => 5
            ],
            'status' => 'pending',
            'payment_gateway' => $gateway
        ]);
        
        if ($gateway === 'khalti') {
            $result = $this->khaltiService->initiatePayment(
                $shipment->total_amount,
                $paymentIntent->intent_id,
                'NETPACK Shipment #' . $shipment->hawb_number,
                $customer->name,
                $customer->email,
                $customer->phone
            );
            
            if ($result['success']) {
                $paymentIntent->update([
                    'gateway_transaction_id' => $result['pidx']
                ]);
                
                return response()->json([
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'pidx' => $result['pidx']
                ]);
            }
        }
        
        if ($gateway === 'esewa') {
            $result = $this->esewaService->initiatePayment(
                $shipment->total_amount,
                $paymentIntent->intent_id,
                'NETPACK Shipment #' . $shipment->hawb_number
            );
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'form_data' => $result['form_data']
                ]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Payment initiation failed'
        ], 500);
    }
    
    public function verifyKhaltiPayment(Request $request)
    {
        $pidx = $request->pidx;
        
        if (!$pidx) {
            return redirect()->route('home')->with('error', 'Invalid payment request');
        }
        
        $paymentIntent = PaymentIntent::where('gateway_transaction_id', $pidx)->first();
        
        if (!$paymentIntent) {
            return redirect()->route('home')->with('error', 'Payment record not found');
        }
        
        $verification = $this->khaltiService->verifyPayment($pidx);
        
        if ($verification['success']) {
            // Process instant split payment
            $this->splitService->processInstantSplit($paymentIntent);
            
            // Update shipment
            $shipment = $paymentIntent->shipment;
            $shipment->update([
                'payment_status' => 'paid',
                'status' => 'confirmed'
            ]);
            $shipment->addTrackingEvent('confirmed', 'System', 'Payment confirmed');
            
            return redirect()->route('payment.success', $shipment->id)
                ->with('success', 'Payment successful! Your order is confirmed.');
        }
        
        return redirect()->route('payment.failure')
            ->with('error', 'Payment verification failed');
    }
    
    public function verifyEsewaPayment(Request $request)
    {
        $transactionUuid = $request->transaction_uuid;
        $amount = $request->total_amount;
        
        $paymentIntent = PaymentIntent::where('intent_id', $transactionUuid)->first();
        
        if (!$paymentIntent) {
            return redirect()->route('home')->with('error', 'Payment record not found');
        }
        
        $verification = $this->esewaService->verifyPayment($transactionUuid, $amount);
        
        if ($verification['success']) {
            $this->splitService->processInstantSplit($paymentIntent);
            
            $shipment = $paymentIntent->shipment;
            $shipment->update([
                'payment_status' => 'paid',
                'status' => 'confirmed'
            ]);
            $shipment->addTrackingEvent('confirmed', 'System', 'Payment confirmed');
            
            return redirect()->route('payment.success', $shipment->id)
                ->with('success', 'Payment successful! Your order is confirmed.');
        }
        
        return redirect()->route('payment.failure')
            ->with('error', 'Payment verification failed');
    }
    
    public function paymentSuccess($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        return view('payment.success', compact('shipment'));
    }
    
    public function paymentFailure()
    {
        return view('payment.failure');
    }
    
    public function getPaymentStatus($shipmentId)
    {
        $shipment = Shipment::where('customer_id', auth()->id())
            ->where('id', $shipmentId)
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'payment_status' => $shipment->payment_status,
            'payment_method' => $shipment->payment_method,
            'status' => $shipment->status
        ]);
    }
}