<?php
// app/Http/Controllers/Agency/AgencyShipmentController.php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyShipmentController extends Controller
{
    public function scan()
    {
        return view('agency.scan');
    }
    
    public function processScan(Request $request)
    {
        $request->validate([
            'hawb_number' => 'required|string',
            'action' => 'required|in:arrival,departure',
            'status_note' => 'nullable|string'
        ]);
        
        $shipment = Shipment::where('hawb_number', $request->hawb_number)
            ->orWhere('tracking_number', $request->hawb_number)
            ->first();
        
        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ]);
        }
        
        $agency = Auth::guard('agency')->user();
        
        if ($request->action == 'arrival') {
            return $this->processArrival($shipment, $agency, $request->status_note);
        } else {
            return $this->processDeparture($shipment, $agency, $request->status_note);
        }
    }
    
    private function processArrival($shipment, $agency, $note)
    {
        if ($shipment->arrived_at_agency) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment already marked as arrived'
            ]);
        }
        
        $shipment->update([
            'current_agency_id' => $agency->id,
            'arrived_at_agency' => now(),
            'status' => 'arrived_at_agency'
        ]);
        
        // Add to agency history
        $history = $shipment->agency_status_history ?? [];
        $history[] = [
            'action' => 'arrival',
            'agency' => $agency->name,
            'note' => $note,
            'timestamp' => now()->toIso8601String()
        ];
        $shipment->agency_status_history = $history;
        $shipment->save();
        
        // Add tracking event
        $shipment->addTrackingEvent(
            'arrived_at_agency',
            $agency->city . ', ' . $agency->country,
            $note ?? 'Package arrived at international hub'
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Arrival recorded successfully',
            'shipment' => [
                'hawb' => $shipment->hawb_number,
                'tracking' => $shipment->tracking_number,
                'status' => 'Arrived at agency',
                'location' => $agency->city . ', ' . $agency->country,
                'time' => now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
    
    private function processDeparture($shipment, $agency, $note)
    {
        if (!$shipment->arrived_at_agency) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment must arrive before departure'
            ]);
        }
        
        if ($shipment->departed_from_agency) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment already marked as departed'
            ]);
        }
        
        $shipment->update([
            'departed_from_agency' => now(),
            'status' => 'out_for_delivery'
        ]);
        
        // Add to agency history
        $history = $shipment->agency_status_history ?? [];
        $history[] = [
            'action' => 'departure',
            'agency' => $agency->name,
            'note' => $note,
            'timestamp' => now()->toIso8601String()
        ];
        $shipment->agency_status_history = $history;
        $shipment->save();
        
        // Add tracking event
        $shipment->addTrackingEvent(
            'handover_to_local',
            $agency->city . ', ' . $agency->country,
            $note ?? 'Package handed over for last mile delivery'
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Departure recorded successfully',
            'shipment' => [
                'hawb' => $shipment->hawb_number,
                'tracking' => $shipment->tracking_number,
                'status' => 'Out for delivery',
                'location' => $agency->city . ', ' . $agency->country,
                'time' => now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
    
    public function shipments(Request $request)
    {
        $agency = Auth::guard('agency')->user();
        
        $query = Shipment::where('current_agency_id', $agency->id);
        
        if ($request->filter == 'arrived') {
            $query->whereNotNull('arrived_at_agency')->whereNull('departed_from_agency');
        } elseif ($request->filter == 'departed') {
            $query->whereNotNull('departed_from_agency');
        }
        
        $shipments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('agency.shipments', compact('shipments'));
    }
    
    public function show($id)
    {
        $agency = Auth::guard('agency')->user();
        $shipment = Shipment::where('current_agency_id', $agency->id)
            ->where('id', $id)
            ->firstOrFail();
        
        return view('agency.shipment-detail', compact('shipment'));
    }
}