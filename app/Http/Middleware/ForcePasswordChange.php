<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Force change password for ALL users who have the flag set
            if ($user->must_change_password && !$request->is('password/change*') && !$request->is('logout')) {
                return redirect()->route('password.change.notice')
                    ->with('warning', 'You must change your password before accessing this page.');
            }
        }

        return $next($request);
    }
}
