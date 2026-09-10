<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckServiceRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        
        // Super Admin and Admin have access to everything
        if (in_array($user->user_type, ['super_admin', 'admin'], true) || in_array($user->role, ['super_admin', 'admin'], true)) {
            return $next($request);
        }

        // Normalize user type and role
        $userType = strtolower(trim($user->user_type ?? ''));
        $userRole = strtolower(trim($user->role ?? ''));

        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roles as $role) {
            $r = strtolower(trim($role));
            if ($userType === $r || $userRole === $r) {
                $hasRole = true;
                break;
            }
            // Client and Customer are treated as unified client entities
            if (in_array($r, ['client', 'customer'], true) && in_array($userType, ['client', 'customer'], true)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            abort(403, 'Unauthorized access. You do not have permission to access this service.');
        }

        return $next($request);
    }
}