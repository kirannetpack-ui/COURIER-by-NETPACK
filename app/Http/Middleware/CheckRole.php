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

        // Super Admin has access to everything
        if ($user->user_type === 'super_admin') {
            return $next($request);
        }

        // Check if user has required role
        foreach ($roles as $role) {
            if ($user->user_type === $role) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }
}