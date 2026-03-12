<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date'],
            'gender' => ['required', 'in:Male,Female'],
            'contact_no' => ['required', 'string', 'max:20'],
            'emergency_no' => ['required', 'string', 'max:20'],
            'purok' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'contact_no' => $validated['contact_no'],
            'emergency_no' => $validated['emergency_no'],
            'purok' => $validated['purok'],
            'address' => $validated['address'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'patient',
            'status' => 1, // Active
            'must_change_password' => 0,
        ]);

        Auth::login($user);

        return redirect()->route('patient.dashboard');
    }
}
