@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-8 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Clinical Referral Slips</h1>
            <p class="text-gray-500 font-medium mt-1">Manage and issue official clinical referrals for patients.</p>
        </div>
        <a href="{{ route('midwife.referral-slips.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-brand-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition-all active:scale-95 gap-2">
            <i class="bi bi-plus-lg"></i>
            New Referral Slip
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <i class="bi bi-check-circle-fill text-xl"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Referral Slips Table --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-500/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Patient</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Date</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Family No.</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Referred To</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($referralSlips as $slip)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 font-black text-sm">
                                        {{ substr($slip->patient->first_name, 0, 1) }}{{ substr($slip->patient->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900 leading-none mb-1 group-hover:text-brand-600 transition-colors">
                                            {{ $slip->patient->full_name }}
                                        </p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                            {{ $slip->patient->purok }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-gray-600 italic">
                                    {{ $slip->date->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black">
                                    {{ $slip->family_no ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($slip->referred_to as $to)
                                        <span class="px-2 py-0.5 bg-brand-50 text-brand-600 rounded text-[9px] font-black uppercase">
                                            {{ $to === 'Others' ? $slip->referred_to_other : $to }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('midwife.referral-slips.show', $slip) }}" 
                                       class="p-2 bg-white border border-gray-100 text-gray-400 hover:text-brand-600 hover:border-brand-100 rounded-xl transition-all shadow-sm"
                                       title="View Details">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('midwife.referral-slips.print', $slip) }}" 
                                       target="_blank"
                                       class="p-2 bg-white border border-gray-100 text-gray-400 hover:text-emerald-600 hover:border-emerald-100 rounded-xl transition-all shadow-sm"
                                       title="Print Slip">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                    <form action="{{ route('midwife.referral-slips.destroy', $slip) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this referral slip?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-white border border-gray-100 text-gray-400 hover:text-red-600 hover:border-red-100 rounded-xl transition-all shadow-sm" title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-[2rem] bg-gray-50 flex items-center justify-center text-gray-200 text-3xl">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <p class="text-gray-400 font-bold uppercase tracking-[0.2em] text-[10px]">No referral slips found</p>
                                    <a href="{{ route('midwife.referral-slips.create') }}" class="text-brand-600 font-black text-xs uppercase tracking-widest hover:underline">Create your first slip</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($referralSlips->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $referralSlips->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
