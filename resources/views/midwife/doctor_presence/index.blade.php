@extends('layouts.app')

@section('title', 'Doctor Presence Tracking')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-3">
                    <i class="bi bi-person-check-fill text-[8px]"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">Operational Tracking</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight leading-tight mb-1 sm:mb-2">
                    Doctor <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Presence</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs lg:text-sm leading-relaxed">
                    Track the arrival and status of doctors for today's consultations. Marking a status will notify scheduled patients automatically.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <div class="bg-white border border-gray-200 px-3.5 py-2 rounded-lg shadow-sm">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Today's Date</p>
                    <p class="text-xs font-black text-gray-900">{{ now()->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Doctor List for Today --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($todayAvailabilities as $block)
            <div class="bg-white rounded-lg shadow-md shadow-gray-200/25 border border-gray-100 p-5 sm:p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-20 h-20 bg-brand-50 rounded-full blur-2xl -mr-10 -mt-10 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-lg font-black border border-brand-100 shadow-inner">
                                {{ substr($block->doctor->full_name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900 tracking-tight">Dr. {{ $block->doctor->full_name }}</h3>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">{{ $block->doctor->role }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Schedule</p>
                            <p class="text-sm font-black text-brand-600">
                                {{ \Carbon\Carbon::parse($block->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($block->end_time)->format('h:i A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Current Status</p>
                            @php
                                $statusClasses = [
                                    'scheduled' => 'text-gray-600',
                                    'arrived' => 'text-emerald-600',
                                    'delayed' => 'text-amber-600',
                                    'absent' => 'text-red-600',
                                ];
                            @endphp
                            <p class="text-sm font-black uppercase tracking-widest {{ $statusClasses[$block->status] }}">
                                {{ $block->status }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Actual Arrival</p>
                            <p class="text-sm font-black text-gray-900">
                                {{ $block->actual_arrival_time ? $block->actual_arrival_time->format('h:i A') : '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if($block->status === 'scheduled' || $block->status === 'delayed')
                        <div class="flex flex-wrap gap-3">
                            <form action="{{ route('midwife.doctor-presence.arrived', $block) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 hover:bg-emerald-700 transition-all active:scale-95">
                                    <i class="bi bi-person-check-fill mr-2"></i>
                                    Mark Arrived
                                </button>
                            </form>
                            <form action="{{ route('midwife.doctor-presence.absent', $block) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-red-600 text-white text-[10px] font-black uppercase tracking-widest shadow-xl shadow-red-500/20 hover:bg-red-700 transition-all active:scale-95"
                                        onclick="return confirm('This will notify ALL patients with today\'s appointments that the doctor is absent. Proceed?')">
                                    <i class="bi bi-person-x-fill mr-2"></i>
                                    Mark Absent
                                </button>
                            </form>
                        </div>
                        <div class="mt-4">
                            <form action="{{ route('midwife.doctor-presence.delayed', $block) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="notes" placeholder="Reason for delay (optional)..." class="flex-1 px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-medium focus:ring-4 focus:ring-amber-500/10 transition-all shadow-inner">
                                <button type="submit" class="px-6 py-3 bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-200">
                                    Delay
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center justify-center py-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tracking Complete for this Doctor</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-20 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-300 mb-6">
                        <i class="bi bi-person-dash text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2 tracking-tight leading-tight">No Doctors Scheduled Today</h3>
                    <p class="text-gray-500 max-w-sm mx-auto font-medium">There are no availability blocks registered for any doctors today. Healthcare providers should coordinate with doctors to ensure their schedules are updated.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
