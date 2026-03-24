<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\ReferralSlip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralSlipController extends Controller
{
    public function index()
    {
        $referralSlips = ReferralSlip::with('patient')->latest()->paginate(10);
        return view('midwife.referral_slips.index', compact('referralSlips'));
    }

    public function create(Request $request)
    {
        $patients = User::where('role', 'patient')->get();
        $selectedPatient = null;
        
        if ($request->has('patient_id')) {
            $selectedPatient = User::find($request->patient_id);
        }

        return view('midwife.referral_slips.create', compact('patients', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'family_no' => 'nullable|string',
            'referred_from' => 'required|array',
            'referred_from.*' => 'string',
            'referred_from_other' => 'nullable|string',
            'referred_to' => 'required|array',
            'referred_to.*' => 'string',
            'referred_to_other' => 'nullable|string',
            'pertinent_findings' => 'nullable|string',
            'reason_for_referral' => 'nullable|string',
            'instruction_to_referring_level' => 'nullable|string',
            'actions_taken_by_referred_level' => 'nullable|string',
            'instructions_to_referring_level_final' => 'nullable|string',
        ]);

        $validated['midwife_id'] = Auth::id();

        ReferralSlip::create($validated);

        return redirect()->route('midwife.referral-slips.index')
            ->with('success', 'Referral slip created successfully.');
    }

    public function show(ReferralSlip $referralSlip)
    {
        $referralSlip->load(['patient', 'midwife']);
        return view('midwife.referral_slips.show', compact('referralSlip'));
    }

    public function print(ReferralSlip $referralSlip)
    {
        $referralSlip->load(['patient', 'midwife']);
        return view('midwife.referral_slips.print', compact('referralSlip'));
    }

    public function destroy(ReferralSlip $referralSlip)
    {
        $referralSlip->delete();
        return back()->with('success', 'Referral slip deleted successfully.');
    }
}
