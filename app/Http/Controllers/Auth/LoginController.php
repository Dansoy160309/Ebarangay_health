<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Inactive account check
            if ($user->status == 0) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is inactive. Please contact the administrator.',
                ]);
            }

            // ✅ Do NOT block login for admin or health_worker
            // Only patients are forced to change password via middleware

            // Redirect based on role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'health_worker' => redirect()->route('healthworker.dashboard'),
                'doctor' => redirect()->route('doctor.dashboard'),
                'midwife' => redirect()->route('midwife.dashboard'),
                'patient' => redirect()->route('patient.dashboard'),
                default => redirect('/'),
            };
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Logout user and invalidate session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
