<?php

namespace App\Http\Controllers\Overseas;

use App\Http\Controllers\Controller;
use App\Models\OverseasTransitPoint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransitPointController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = OverseasTransitPoint::with('partner');

        // Filter by partner if specified
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        } else {
            // If super admin, show all; if partner, show only their own
            if (auth()->user()->user_type !== 'super_admin' && auth()->user()->user_type !== 'admin') {
                $query->where('partner_id', auth()->id());
            }
        }

        $transitPoints = $query->orderBy('created_at', 'desc')->paginate(20);
        $partners = User::where('user_type', 'overseas')->orWhere('user_type', 'overseas_partner')->get();

        return view('overseas.transit-points.index', compact('transitPoints', 'partners'));
    }

    public function create()
    {
        $types = OverseasTransitPoint::TYPES;
        $partners = User::where('user_type', 'overseas')->orWhere('user_type', 'overseas_partner')->get();
        
        return view('overseas.transit-points.create', compact('types', 'partners'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:hub,transit',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'is_mandatory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if hub already exists for this partner
        if ($request->type === 'hub') {
            $existingHub = OverseasTransitPoint::where('partner_id', $request->partner_id)
                ->where('type', 'hub')
                ->first();
            
            if ($existingHub) {
                return redirect()->back()
                    ->with('error', 'This partner already has a Hub. Only one Hub is allowed per partner.')
                    ->withInput();
            }
        }

        OverseasTransitPoint::create([
            'partner_id' => $request->partner_id,
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'country' => $request->country,
            'is_mandatory' => $request->has('is_mandatory'),
            'is_active' => true,
        ]);

        return redirect()->route('overseas.transit-points.index')
            ->with('success', 'Transit point added successfully!');
    }

    public function edit($id)
    {
        $transitPoint = OverseasTransitPoint::with('partner')->findOrFail($id);
        $types = OverseasTransitPoint::TYPES;
        $partners = User::where('user_type', 'overseas')->orWhere('user_type', 'overseas_partner')->get();

        return view('overseas.transit-points.edit', compact('transitPoint', 'types', 'partners'));
    }

    public function update(Request $request, $id)
    {
        $transitPoint = OverseasTransitPoint::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:hub,transit',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'is_mandatory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // If changing type to hub, check if hub already exists for this partner
        if ($request->type === 'hub') {
            $existingHub = OverseasTransitPoint::where('partner_id', $request->partner_id)
                ->where('type', 'hub')
                ->where('id', '!=', $id)
                ->first();
            
            if ($existingHub) {
                return redirect()->back()
                    ->with('error', 'This partner already has a Hub. Only one Hub is allowed per partner.')
                    ->withInput();
            }
        }

        $transitPoint->update([
            'partner_id' => $request->partner_id,
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'country' => $request->country,
            'is_mandatory' => $request->has('is_mandatory'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('overseas.transit-points.index')
            ->with('success', 'Transit point updated successfully!');
    }

    public function destroy($id)
    {
        $transitPoint = OverseasTransitPoint::findOrFail($id);
        $transitPoint->delete();

        return redirect()->route('overseas.transit-points.index')
            ->with('success', 'Transit point deleted successfully!');
    }

    public function toggle($id)
    {
        $transitPoint = OverseasTransitPoint::findOrFail($id);
        $transitPoint->update(['is_active' => !$transitPoint->is_active]);

        $status = $transitPoint->is_active ? 'activated' : 'deactivated';
        return redirect()->route('overseas.transit-points.index')
            ->with('success', "Transit point {$status} successfully!");
    }
}