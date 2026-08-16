<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('tracking_number', 'like', '%' . $request->search . '%')
                  ->orWhere('hawb_number', 'like', '%' . $request->search . '%');
            });
        }

        $shipments = $query->paginate(20);
        return view('admin.shipments.index', compact('shipments'));
    }

    public function show($id)
    {
        $shipment = Shipment::findOrFail($id);
        return view('admin.shipments.show', compact('shipment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update(['status' => $request->status]);
        return back()->with('success', 'Status updated successfully!');
    }
}