<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user()->load(['dependents' => function ($query) {
            $query->where('role', 'patient')
                ->orderBy('first_name')
                ->orderBy('last_name');
        }]);

        return view('patient.profile.index', compact('user'));
    }

    public function updatePreferences(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->sms_notifications = $request->has('sms_notifications');
        $user->save();

        return redirect()->back()->with('success', 'Preferences updated successfully.');
    }
}
