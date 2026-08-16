<?php

namespace App\Http\Controllers\Overseas;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffScanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function scan()
    {
        return view('overseas.staff.scan');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|exists:shipments,tracking_number',
            'action' => 'required|in:arrival,departure,customs_clearance',
            'notes' => 'nullable|string',
        ]);

        $staff = Auth::user();
        $shipment = Shipment::where('tracking_number', $request->tracking_number)->first();

        if (!$shipment) {
            return redirect()->back()
                ->with('error', 'Shipment not found.');
        }

        // Update tracking based on action
        $trackingHistory = $shipment->tracking_history ?? [];
        $trackingHistory[] = [
            'action' => $request->action,
            'processed_by' => $staff->name,
            'notes' => $request->notes,
            'time' => now()->toDateTimeString(),
        ];

        $status = $shipment->status;
        switch ($request->action) {
            case 'arrival':
                $status = 'in_transit';
                break;
            case 'departure':
                $status = 'in_transit';
                break;
            case 'customs_clearance':
                $status = 'customs_clearance';
                $shipment->customs_cleared_at = now();
                break;
        }

        $shipment->update([
            'status' => $status,
            'tracking_history' => $trackingHistory,
            'processed_by' => $staff->id,
            'status_notes' => $request->notes,
        ]);

        return redirect()->route('overseas.staff.scan')
            ->with('success', "Shipment {$shipment->tracking_number} processed successfully!");
    }
}