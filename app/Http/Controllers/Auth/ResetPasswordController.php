<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /**
     * Show the Reset Password form
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Handle resetting the password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check token - Case-insensitive email check to be safer
        $record = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            // Debug: Check if email exists but token is different
            $emailExists = DB::table('password_reset_tokens')
                ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
                ->exists();
            
            $msg = $emailExists ? 'Invalid token for this email.' : 'Invalid or expired token.';
            return back()->with('error', $msg);
        }

        // Optional: check expiration (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return back()->with('error', 'Token expired. Please request a new password reset.');
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password has been reset successfully. You can now login.');
    }
}
