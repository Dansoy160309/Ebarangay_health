@extends('layouts.app')

@section('title', 'Defaulter Tracking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-6 relative overflow-hidden">
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-brand-50/50 rounded-full blur-3xl -mr-48 -mt-48 opacity-50 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-50/50 rounded-full blur-3xl -ml-48 -mb-48 opacity-50 pointer-events-none"></div>

    <!-- Header Section -->
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="text-[10px] font-black text-red-600 uppercase tracking-[0.25em] bg-red-50 px-3.5 py-1.5 rounded-lg border border-red-100/50 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    Critical Follow-up Required
                </span>
            </div>
            <h1 class="text-5xl font-black text-gray-900 tracking-tight leading-tight">Defaulter Tracking</h1>
            <p class="text-gray-500 font-bold mt-2 text-base flex items-center gap-2">
                Monitoring patients who missed their <span class="text-gray-900">scheduled health services</span>
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4">
            <form action="{{ route('midwife.appointments.defaulters') }}" method="GET" class="w-full sm:w-auto flex flex-col sm:flex-row gap-3 items-center">
                {{-- Keep current service filter in search --}}
                <input type="hidden" name="service" value="{{ $serviceFilter }}">
                
                <div class="flex gap-2 bg-white p-1.5 rounded-lg shadow-lg shadow-brand-500/5 border border-gray-100 hover:border-brand-200 transition-all group w-full sm:w-auto">
                    <div class="relative flex-grow min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-400 group-hover:text-brand-500 transition-colors text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" 
                               class="block w-full pl-12 pr-3 py-3 border-none rounded-lg bg-transparent placeholder-gray-400 focus:ring-0 font-bold text-sm" 
                               placeholder="Search patient name...">
                    </div>
                    <button type="submit" class="inline-flex items-center px-5 py-3 border border-transparent text-sm font-black rounded-lg shadow-lg shadow-brand-500/20 text-white bg-brand-600 hover:bg-brand-700 transition-all duration-300 uppercase tracking-tighter">
                        Search
                    </button>
                </div>

                @if(request('search') || $serviceFilter !== 'all')
                <a href="{{ route('midwife.appointments.defaulters') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-tighter hover:text-red-500 transition-colors flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-lg border border-gray-100">
                    <i class="bi bi-x-circle text-sm"></i>
                    Clear Filters
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
        {{-- Total Defaulters --}}
        <div class="bg-white rounded-lg p-7 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-red-200 transition-all duration-500">
            <div class="absolute top-0 right-0 w-20 h-20 bg-red-50 rounded-full blur-2xl -mr-10 -mt-10 opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg bg-red-50 flex items-center justify-center text-red-600 shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i class="bi bi-person-x-fill text-3xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.15em] mb-1">Total Defaulters</p>
                    <h3 class="text-4xl font-black text-gray-900 group-hover:text-red-600 transition-colors">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        
        {{-- Immunization Misses --}}
        <a href="{{ route('midwife.appointments.defaulters', ['service' => 'Immunization']) }}" 
           class="bg-white rounded-lg p-7 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-indigo-200 transition-all duration-500">
            <div class="absolute top-0 right-0 w-20 h-20 bg-indigo-50 rounded-full blur-2xl -mr-10 -mt-10 opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i class="bi bi-shield-plus text-3xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.15em] mb-1">Immunization</p>
                    <h3 class="text-4xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $stats['immunization'] }}</h3>
                </div>
            </div>
        </a>

        {{-- Prenatal Misses --}}
        <a href="{{ route('midwife.appointments.defaulters', ['service' => 'Prenatal']) }}" 
           class="bg-white rounded-lg p-7 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-brand-200 transition-all duration-500">
            <div class="absolute top-0 right-0 w-20 h-20 bg-brand-50 rounded-full blur-2xl -mr-10 -mt-10 opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i class="bi bi-calendar-heart text-3xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.15em] mb-1">Prenatal Care</p>
                    <h3 class="text-4xl font-black text-gray-900 group-hover:text-brand-600 transition-colors">{{ $stats['prenatal'] }}</h3>
                </div>
            </div>
        </a>
    </div>

    <!-- Filter Pills -->
    <div class="flex flex-wrap items-center gap-3 relative z-10">
        <a href="{{ route('midwife.appointments.defaulters', array_merge(request()->query(), ['service' => 'all'])) }}" 
           class="px-6 py-3 rounded-lg text-sm font-black uppercase tracking-tighter transition-all {{ $serviceFilter === 'all' ? 'bg-gray-900 text-white shadow-lg' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
            All Services
        </a>
        <a href="{{ route('midwife.appointments.defaulters', array_merge(request()->query(), ['service' => 'Immunization'])) }}" 
           class="px-6 py-3 rounded-lg text-sm font-black uppercase tracking-tighter transition-all {{ $serviceFilter === 'Immunization' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
            Immunization Only
        </a>
        <a href="{{ route('midwife.appointments.defaulters', array_merge(request()->query(), ['service' => 'Prenatal'])) }}" 
           class="px-6 py-3 rounded-lg text-sm font-black uppercase tracking-tighter transition-all {{ $serviceFilter === 'Prenatal' ? 'bg-brand-600 text-white shadow-lg' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
            Prenatal Only
        </a>
    </div>

    <!-- Defaulters Table Container -->
    <div class="bg-white rounded-[3.5rem] shadow-soft border border-gray-100 overflow-hidden relative z-10">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-12 py-5 text-sm font-black text-gray-400 uppercase tracking-[0.3em]">Patient Identity</th>
                        <th class="px-12 py-5 text-sm font-black text-gray-400 uppercase tracking-[0.3em]">Scheduled On</th>
                        <th class="px-12 py-5 text-sm font-black text-gray-400 uppercase tracking-[0.3em]">Health Service</th>
                        <th class="px-12 py-5 text-sm font-black text-gray-400 uppercase tracking-[0.3em]">Contact Actions</th>
                        <th class="px-12 py-5 text-right text-[11px] font-black text-gray-400 uppercase tracking-[0.3em]">Management</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($defaulters as $appt)
                    <tr class="hover:bg-gray-50/80 transition-all duration-300 group/row">
                        <td class="px-12 py-6">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-black text-xl shadow-inner group-hover/row:scale-110 transition-transform duration-500">
                                    {{ substr($appt->user->first_name, 0, 1) }}{{ substr($appt->user->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-lg font-black text-gray-900 group-hover/row:text-brand-600 transition-colors leading-tight">{{ $appt->user->full_name }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-100 px-2.5 py-0.5 rounded-md">{{ $appt->user->age ?? 'N/A' }} Yrs</span>
                                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $appt->user->gender }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-12 py-6">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-calendar-x text-red-500 text-base"></i>
                                    <span class="text-base font-black text-gray-900 uppercase tracking-tight">
                                        {{ $appt->scheduled_at ? $appt->scheduled_at->format('M d, Y') : ($appt->slot ? \Carbon\Carbon::parse($appt->slot->date)->format('M d, Y') : 'N/A') }}
                                    </span>
                                </div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-7">
                                    {{ $appt->scheduled_at ? $appt->scheduled_at->format('h:i A') : 'N/A' }}
                                </p>
                            </div>
                        </td>
                        <td class="px-12 py-6">
                            @php
                                $isImm = str_contains(strtolower($appt->service), 'immunization');
                                $colorClass = $isImm ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-brand-50 text-brand-700 border-brand-100';
                            @endphp
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl {{ $colorClass }} text-[11px] font-black uppercase tracking-widest border shadow-sm">
                                <i class="bi {{ $isImm ? 'bi-shield-plus' : 'bi-calendar-heart' }}"></i>
                                {{ $appt->service }}
                            </span>
                        </td>
                        <td class="px-12 py-6">
                            @php
                                $contactNo = $appt->user->contact_no;
                                $isDependent = $appt->user->isDependent();
                                
                                if (empty($contactNo) && $isDependent) {
                                    $contactNo = $appt->user->guardian->contact_no ?? null;
                                }
                            @endphp
                            <div class="flex flex-col gap-2">
                                @if($contactNo)
                                <a href="tel:{{ $contactNo }}" class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-emerald-50 text-emerald-700 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all border border-emerald-100 w-fit">
                                    <i class="bi bi-telephone-fill"></i>
                                    {{ $contactNo }}
                                </a>
                                @else
                                <span class="text-[11px] font-black text-gray-300 uppercase tracking-widest italic">No Contact Provided</span>
                                @endif
                                
                                @if($isDependent)
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1.5">
                                        <i class="bi bi-people-fill text-gray-300"></i>
                                        Via: {{ $appt->user->guardian->first_name ?? 'Guardian' }}
                                    </p>
                                @endif
                            </div>
                        </td>
                        <td class="px-12 py-6 text-right">
                            <div class="flex items-center justify-end gap-3 opacity-40 group-hover/row:opacity-100 transition-opacity">
                                {{-- Recall SMS Button --}}
                                @if($contactNo)
                                <form action="{{ route('midwife.appointments.recall-sms', $appt->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Send an urgent recall SMS alert to this patient?')"
                                            class="p-3 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100 group/btn" 
                                            title="Send Recall SMS">
                                        <i class="bi bi-chat-dots-fill text-lg"></i>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('midwife.appointments.no-show', $appt->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Mark this appointment as a No-Show? This will remove it from the active defaulter list.')"
                                            class="p-3 bg-orange-50 text-orange-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm border border-orange-100 group/btn" 
                                            title="Mark as No-Show">
                                        <i class="bi bi-person-dash-fill text-lg"></i>
                                    </button>
                                </form>
                                <a href="{{ route('midwife.appointments.show', $appt->id) }}" class="p-3 bg-brand-600 text-white rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/20" title="Review Case">
                                    <i class="bi bi-arrow-right-circle-fill text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-32 text-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-emerald-50/30 -z-10"></div>
                            <div class="w-24 h-24 bg-white rounded-[2rem] flex items-center justify-center text-emerald-500 mx-auto mb-8 shadow-xl shadow-emerald-500/10 border border-emerald-50 animate-bounce-subtle">
                                <i class="bi bi-shield-check text-5xl"></i>
                            </div>
                            <h4 class="text-3xl font-black text-gray-900 tracking-tight mb-3">Excellent Compliance!</h4>
                            <p class="text-gray-400 font-bold uppercase tracking-[0.2em] text-xs max-w-md mx-auto leading-loose">
                                Your patient population is strictly following their schedules. There are no critical misses at this time.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($defaulters->hasPages())
        <div class="px-10 py-8 border-t border-gray-50 bg-gray-50/30">
            {{ $defaulters->links() }}
        </div>
        @endif
    </div>

    <!-- Guidelines / Legend -->
    <div class="bg-gray-900 rounded-[3rem] p-10 lg:p-14 text-white relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500 rounded-full blur-[100px] -mr-32 -mt-32 opacity-20 group-hover:opacity-40 transition-opacity duration-700"></div>
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-2xl font-black mb-4 tracking-tight">Management Protocols</h3>
                <p class="text-gray-400 font-medium leading-relaxed mb-8">
                    Patients listed here have missed their appointments by at least 24 hours. Follow-up is required to ensure continuity of care and public health safety.
                </p>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-3 bg-white/5 px-5 py-3 rounded-2xl border border-white/10">
                        <div class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Priority 1: Immunization</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/5 px-5 py-3 rounded-2xl border border-white/10">
                        <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Priority 2: High-Risk Prenatal</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="p-6 bg-white/5 rounded-3xl border border-white/10">
                    <i class="bi bi-telephone-outbound text-brand-400 text-2xl mb-4 block"></i>
                    <h4 class="text-sm font-black uppercase tracking-widest mb-2">Direct Contact</h4>
                    <p class="text-[10px] text-gray-500 font-bold leading-relaxed">Call the patient or guardian immediately to reschedule.</p>
                </div>
                <div class="p-6 bg-white/5 rounded-3xl border border-white/10">
                    <i class="bi bi-chat-dots text-indigo-400 text-2xl mb-4 block"></i>
                    <h4 class="text-sm font-black uppercase tracking-widest mb-2">BHW Dispatch</h4>
                    <p class="text-[10px] text-gray-500 font-bold leading-relaxed">Coordinate with BHWs for home visitation if reachable.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce-subtle {
        animation: bounce-subtle 3s infinite ease-in-out;
    }
</style>
@endsection
