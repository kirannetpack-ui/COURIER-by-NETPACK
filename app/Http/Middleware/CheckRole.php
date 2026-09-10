<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super Admin and Admin have access to everything
        if (in_array($user->user_type, ['super_admin', 'admin'], true) || in_array($user->role, ['super_admin', 'admin'], true)) {
            return $next($request);
        }

        $userType = strtolower(trim($user->user_type ?? ''));
        $userRole = strtolower(trim($user->role ?? ''));

        // Check if user has required role
        foreach ($roles as $role) {
            $r = strtolower(trim($role));
            if ($userType === $r || $userRole === $r) {
                return $next($request);
            }
            if (in_array($r, ['client', 'customer'], true) && in_array($userType, ['client', 'customer'], true)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }
}