<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CarrierTrackingSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarrierWebhookController extends Controller
{
    public function __construct(private readonly CarrierTrackingSyncService $syncService)
    {
    }

    /**
     * Inbound webhook endpoint for global carriers (FedEx, DHL, UPS, AfterShip/17Track format)
     */
    public function handle(Request $request, string $carrier): JsonResponse
    {
        Log::info("Inbound carrier tracking webhook received from {$carrier}", [
            'payload' => $request->all(),
            'ip' => $request->ip(),
        ]);

        $payload = $request->all();

        $result = $this->syncService->handleInboundWebhook($carrier, $payload);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Carrier tracking telemetry processed and synchronized.',
            'data' => $result,
        ]);
    }
}
