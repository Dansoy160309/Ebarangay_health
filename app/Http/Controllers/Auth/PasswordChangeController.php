<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    /**
     * Show the notice page for users who must change their password.
     */
    public function showNotice()
    {
        return view('auth.password-change-notice');
    }

    /**
     * Show the password change form.
     */
    public function showChangeForm()
    {
        return view('auth.force-change-password');
    }

    /**
     * Update the authenticated user's password.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate password input
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        // Update password and reset must_change_password flag
        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', '✅ Your password has been changed successfully!');
    }
}
