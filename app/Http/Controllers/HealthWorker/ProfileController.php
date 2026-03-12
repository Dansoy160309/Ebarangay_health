<?php

namespace App\Http\Controllers\HealthWorker;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('healthworker.profile.index', compact('user'));
    }
}
