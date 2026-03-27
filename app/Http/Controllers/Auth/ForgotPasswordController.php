<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;
use Throwable;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending reset token email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'Email not found.');
        }

        if (!$user->isActive()) {
            return back()->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // Generate unique token
        $token = Str::random(64);

        // Save token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Send email with token link
        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetLink));
        } catch (Throwable $e) {
            if (config('app.debug')) {
                return back()
                    ->with('success', 'Mail is not configured. Use the reset link below.')
                    ->with('debug_reset_link', $resetLink);
            }

            return back()->with('error', 'Unable to send reset email right now. Please try again later.');
        }

        return back()->with('success', 'Password reset link sent to your email.');
    }
}
