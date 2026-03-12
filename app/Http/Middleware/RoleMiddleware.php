<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request and check for role access.
     *
     * Example usage:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,health_worker')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles  List of allowed roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // 🚫 If user is not logged in, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ Allow access if user's role matches any of the allowed roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 🚫 User is logged in but does not have permission
        // You can either abort or redirect to dashboard with an error
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'You do not have access to that page.');
    }
}
