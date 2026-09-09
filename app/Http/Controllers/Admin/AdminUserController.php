<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // This is the application's All Users screen.  The previous query
        // silently limited normal views to admin/staff, despite the form
        // allowing customer and client creation.
        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get stats - including ALL pending users
        $stats = [
            'total' => User::count(),
            'admin' => User::where('user_type', 'admin')->count(),
            'staff' => User::where('user_type', 'staff')->count(),
            'approved' => User::where('verification_status', 'approved')->count(),
            'pending' => User::where('verification_status', 'pending')->count(), // ALL pending users
            'rejected' => User::where('verification_status', 'rejected')->count(),
            'suspended' => User::where('verification_status', 'suspended')->count(),
            'active' => User::where('verification_status', 'approved')->count(), // ALL approved users
            'inactive' => User::where('verification_status', '!=', 'approved')->count(),
            'all_pending' => User::where('verification_status', 'pending')->count(), // All pending users
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('admin.users.create', ['manageableUserTypes' => $this->manageableUserTypes()]);
    }

    /**
     * Store a newly created admin user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:12|confirmed',
            'user_type' => ['required', Rule::in(array_keys($this->manageableUserTypes()))],
            'phone' => 'nullable|string|max:20',
            'verification_status' => 'required|in:pending,approved,rejected,suspended',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'permanent_address' => 'nullable|string',
            'temporary_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'verification_status' => $request->verification_status,
            'registration_completed' => $request->verification_status === 'approved',
            'approved_at' => $request->verification_status === 'approved' ? now() : null,
            'approved_by' => $request->verification_status === 'approved' ? $request->user()->id : null,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'permanent_address' => $request->permanent_address,
            'temporary_address' => $request->temporary_address,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "{$user->user_type_label} account created successfully.");
    }

    /**
     * Display the specified admin user.
     */
    public function show(User $user)
    {
        $this->ensureCanManage($user);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $user)
    {
        $this->ensureCanManage($user);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified admin user.
     */
    public function update(Request $request, User $user)
    {
        $this->ensureCanManage($user);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'user_type' => ['required', Rule::in(array_keys($this->manageableUserTypes()))],
            'phone' => 'nullable|string|max:20',
            'verification_status' => 'required|in:pending,approved,rejected,suspended',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'permanent_address' => 'nullable|string',
            'temporary_address' => 'nullable|string',
            'password' => 'nullable|string|min:12|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'verification_status' => $request->verification_status,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'permanent_address' => $request->permanent_address,
            'temporary_address' => $request->temporary_address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user updated successfully!');
    }

    /**
     * Remove the specified admin user.
     */
    public function destroy(User $user)
    {
        $this->ensureCanManage($user);
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last admin
        if ($user->user_type === User::TYPE_SUPER_ADMIN && User::where('user_type', User::TYPE_SUPER_ADMIN)->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete the last Super Administrator.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user deleted successfully!');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        $this->ensureCanManage($user);
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot change your own status.');
        }

        $newStatus = $user->verification_status === 'approved' ? 'suspended' : 'approved';
        $user->update(['verification_status' => $newStatus]);

        $message = $newStatus === 'approved' ? 'activated' : 'suspended';
        return redirect()->route('admin.users.index')
            ->with('success', "User {$message} successfully!");
    }

    /**
     * Show verify user page.
     * This shows the pending user details for approval
     */
    public function verify(User $user)
    {
        $this->ensureCanManage($user);
        // Allow verification for any pending user
        if ($user->verification_status !== 'pending') {
            return redirect()->route('admin.users.index')
                ->with('error', 'This user is not pending verification.');
        }
        
        return view('admin.users.verify', compact('user'));
    }

    /**
     * Approve a user (any type).
     */
    public function approve(User $user)
    {
        $this->ensureCanManage($user);
        $user->update([
            'verification_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'registration_completed' => true,
        ]);

        \Log::info('User approved', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'approved_by' => auth()->id()
        ]);

        return redirect()->route('admin.users.index', ['status' => 'pending'])
            ->with('success', "User {$user->name} has been approved successfully!");
    }

    /**
     * Reject a user (any type).
     */
    public function reject(Request $request, User $user)
    {
        $this->ensureCanManage($user);
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $user->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        \Log::info('User rejected', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'rejected_by' => auth()->id(),
            'reason' => $request->rejection_reason
        ]);

        return redirect()->route('admin.users.index', ['status' => 'pending'])
            ->with('success', "User {$user->name} has been rejected.");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->ensureCanManage($user);
        $request->validate([
            'password' => 'required|string|min:12|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password reset successfully!');
    }

    /**
     * Super administrators and legacy administrators may manage every normal
     * operational role. Domestic administrators are intentionally limited to
     * customer-facing and domestic delivery roles.
     */
    private function manageableUserTypes(): array
    {
        $all = [
            'admin' => 'Administrator',
            User::TYPE_STAFF => User::USER_TYPES[User::TYPE_STAFF],
            User::TYPE_DOMESTIC_ADMIN => User::USER_TYPES[User::TYPE_DOMESTIC_ADMIN],
            User::TYPE_INTERNATIONAL_ADMIN => User::USER_TYPES[User::TYPE_INTERNATIONAL_ADMIN],
            User::TYPE_SELLER => User::USER_TYPES[User::TYPE_SELLER],
            User::TYPE_RIDER => User::USER_TYPES[User::TYPE_RIDER],
            User::TYPE_PARTNER => User::USER_TYPES[User::TYPE_PARTNER],
            User::TYPE_OVERSEAS => User::USER_TYPES[User::TYPE_OVERSEAS],
            User::TYPE_CLIENT => User::USER_TYPES[User::TYPE_CLIENT],
            User::TYPE_CUSTOMER => User::USER_TYPES[User::TYPE_CUSTOMER],
        ];

        if (in_array(auth()->user()->user_type, [User::TYPE_SUPER_ADMIN, 'admin'], true)) {
            return $all;
        }

        return array_intersect_key($all, array_flip([
            User::TYPE_SELLER,
            User::TYPE_RIDER,
            User::TYPE_PARTNER,
            User::TYPE_CLIENT,
            User::TYPE_CUSTOMER,
        ]));
    }

    private function ensureCanManage(User $user): void
    {
        if ($user->is(auth()->user())) {
            return;
        }

        if (array_key_exists($user->user_type, $this->manageableUserTypes())) {
            return;
        }

        abort(403, 'You are not allowed to manage this account type.');
    }
}
