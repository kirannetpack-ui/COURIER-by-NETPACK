<?php

namespace App\Http\Controllers\Overseas;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function scan()
    {
        return view('overseas.scan.index');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|exists:shipments,tracking_number',
            'status' => 'required|string',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $overseas = Auth::user();
        $shipment = Shipment::where('overseas_partner_id', $overseas->id)
            ->where('tracking_number', $request->tracking_number)
            ->first();

        if (!$shipment) {
            return redirect()->back()
                ->with('error', 'Shipment not found or not assigned to you.');
        }

        // Update tracking
        $trackingHistory = $shipment->tracking_history ?? [];
        $trackingHistory[] = [
            'status' => $request->status,
            'location' => $request->location,
            'notes' => $request->notes,
            'time' => now()->toDateTimeString(),
        ];

        $shipment->update([
            'status' => $request->status,
            'tracking_history' => $trackingHistory,
            'status_notes' => $request->notes,
        ]);

        return redirect()->route('overseas.scan')
            ->with('success', "Shipment {$shipment->tracking_number} updated to: " . ucfirst($request->status));
    }

    public function fetchShipment($trackingNumber)
    {
        $overseas = Auth::user();
        $shipment = Shipment::where('overseas_partner_id', $overseas->id)
            ->where('tracking_number', $trackingNumber)
            ->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found or not assigned to you.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ]);
    }
}