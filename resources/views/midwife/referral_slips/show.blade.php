@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-8 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('midwife.referral-slips.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-brand-600 font-bold text-xs uppercase tracking-widest transition-colors mb-4">
                <i class="bi bi-arrow-left"></i> Back to list
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Referral Slip Details</h1>
            <p class="text-gray-500 font-medium mt-1">Review the referral information for {{ $referralSlip->patient->full_name }}.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('midwife.referral-slips.print', $referralSlip) }}" 
               target="_blank"
               class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-all active:scale-95 gap-2">
                <i class="bi bi-printer"></i>
                Print Slip
            </a>
        </div>
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-500/5 p-8 sm:p-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-2 h-full bg-brand-500"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            {{-- Patient Info --}}
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Patient Name</label>
                    <p class="text-lg font-black text-gray-900">{{ $referralSlip->patient->full_name }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Address</label>
                    <p class="font-bold text-gray-600">{{ $referralSlip->patient->address }}, {{ $referralSlip->patient->purok }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Age</label>
                        <p class="font-bold text-gray-600">{{ $referralSlip->patient->age }} years old</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Family No.</label>
                        <p class="font-bold text-gray-600">{{ $referralSlip->family_no ?? 'None' }}</p>
                    </div>
                </div>
            </div>

            {{-- Referral Info --}}
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Referral Date</label>
                    <p class="font-bold text-gray-600">{{ $referralSlip->date->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Referred From</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($referralSlip->referred_from as $from)
                            <span class="px-3 py-1 bg-brand-50 text-brand-600 rounded-xl text-[10px] font-black uppercase">
                                {{ $from === 'Others' ? $referralSlip->referred_from_other : $from }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Referred To</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($referralSlip->referred_to as $to)
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase">
                                {{ $to === 'Others' ? $referralSlip->referred_to_other : $to }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 space-y-8 pt-12 border-t border-gray-50">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Pertinent Findings</label>
                <div class="p-6 bg-gray-50 rounded-3xl font-medium text-gray-700 leading-relaxed italic">
                    {{ $referralSlip->pertinent_findings ?: 'No findings recorded.' }}
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Reason for Referral</label>
                <div class="p-6 bg-gray-50 rounded-3xl font-medium text-gray-700 leading-relaxed italic">
                    {{ $referralSlip->reason_for_referral ?: 'No reason recorded.' }}
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Instructions</label>
                <div class="p-6 bg-gray-50 rounded-3xl font-medium text-gray-700 leading-relaxed italic">
                    {{ $referralSlip->instruction_to_referring_level ?: 'No instructions recorded.' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
