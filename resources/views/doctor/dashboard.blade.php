@extends('layouts.app')

@section('title', auth()->user()->isMidwife() ? 'Midwife Dashboard' : 'Doctor Dashboard')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">

    @php
        $user = auth()->user();
        $routePrefix = $user->isMidwife() ? 'midwife' : 'doctor';
    @endphp

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-br from-brand-600 via-brand-500 to-indigo-600 rounded-2xl sm:rounded-2xl p-5 sm:p-6 text-white shadow-lg shadow-brand-500/15 relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-xl sm:text-2xl font-black mb-1 sm:mb-2 tracking-tight leading-tight">
                Welcome, <br class="sm:hidden"> {{ $user->isMidwife() ? $user->full_name : 'Dr. ' . $user->last_name }}! {{ $user->isMidwife() ? '🧑‍⚕️' : '👨‍⚕️' }}
            </h1>
            <p class="text-brand-50 text-xs sm:text-sm font-medium opacity-90 max-w-xl">
                You have {{ $upcomingToday }} appointments scheduled for today.
            </p>
            
            <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10 shadow-sm w-fit">
                    <i class="bi bi-calendar3 text-brand-200 text-xs sm:text-sm"></i>
                    <span class="text-xs sm:text-sm font-black uppercase tracking-tighter">{{ now()->format('l, F j, Y') }}</span>
                </div>
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10 shadow-sm w-fit">
                    <i class="bi bi-clock text-brand-200 text-[8px] sm:text-xs"></i>
                    <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-tighter">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute -right-10 -bottom-10 sm:-right-16 sm:-bottom-16 opacity-10 pointer-events-none">
            <i class="bi bi-heart-pulse text-[8rem] sm:text-[12rem]"></i>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        
        {{-- Today's Appointments --}}
        <div class="bg-white rounded-lg sm:rounded-lg shadow-sm border border-gray-100 p-4 sm:p-5 relative overflow-hidden group hover:shadow-md transition">
            <p class="text-xs sm:text-sm font-black text-brand-600 uppercase tracking-tighter relative z-10 mb-0.5">Today</p>
            <p class="text-xl sm:text-2xl font-black text-gray-900 relative z-10">{{ $upcomingToday }}</p>
            <div class="absolute right-0 top-0 p-3 opacity-5 group-hover:opacity-10 transition">
                <i class="bi bi-calendar-check-fill text-4xl text-brand-600"></i>
            </div>
        </div>

        {{-- Pending Consultations --}}
        <div class="bg-white rounded-lg sm:rounded-lg shadow-sm border border-gray-100 p-4 sm:p-5 relative overflow-hidden group hover:shadow-md transition">
            <p class="text-[8px] sm:text-[9px] font-black text-orange-600 uppercase tracking-tighter relative z-10 mb-0.5">Pending</p>
            <p class="text-xl sm:text-2xl font-black text-gray-900 relative z-10">{{ $pendingConsultations }}</p>
            <div class="absolute right-0 top-0 p-3 opacity-5 group-hover:opacity-10 transition">
                <i class="bi bi-clipboard-pulse text-4xl text-orange-500"></i>
            </div>
        </div>

        {{-- Total Patients --}}
        <div class="bg-white rounded-lg sm:rounded-lg shadow-sm border border-gray-100 p-4 sm:p-5 relative overflow-hidden group hover:shadow-md transition">
            <p class="text-[8px] sm:text-[9px] font-black text-green-600 uppercase tracking-tighter relative z-10 mb-0.5">Patients</p>
            <p class="text-xl sm:text-2xl font-black text-gray-900 relative z-10">{{ $totalPatients }}</p>
            <div class="absolute right-0 top-0 p-3 opacity-5 group-hover:opacity-10 transition">
                <i class="bi bi-people-fill text-4xl text-green-600"></i>
            </div>
        </div>
    </div>

    {{-- Midwife Specific Alerts --}}
    @if($user->isMidwife())
    <div class="space-y-3">
        <h2 class="text-sm font-black text-gray-900 uppercase tracking-tighter flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-[10px] shadow-inner">
                <i class="bi bi-shield-exclamation"></i>
            </span>
            Midwife Monitoring Alerts
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- High Risk Alerts --}}
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-16 h-16 -mr-8 -mt-8 bg-red-50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-3">
                    <h4 class="text-xs font-black text-red-600 uppercase tracking-tighter flex items-center gap-1">
                        <i class="bi bi-exclamation-triangle-fill text-sm"></i> High-Risk Pregnancies
                    </h4>
                    <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded-full text-xs font-black uppercase tracking-tighter border border-red-100">{{ $midwifeAlerts['high_risk']->count() }} Cases</span>
                </div>
                <div class="space-y-2">
                    @forelse($midwifeAlerts['high_risk'] as $risk)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50/50 border border-gray-50 group-hover:border-red-100 transition-all">
                        <span class="text-sm font-bold text-gray-900">{{ $risk->user->full_name }}</span>
                        <a href="{{ route('midwife.patients.show', $risk->user_id) }}" class="text-xs font-black text-red-600 uppercase tracking-tighter hover:underline">View</a>
                    </div>
                    @empty
                    <p class="text-[9px] text-gray-400 italic">No high-risk alerts.</p>
                    @endforelse
                </div>
            </div>

            {{-- Overdue Prenatal --}}
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-16 h-16 -mr-8 -mt-8 bg-purple-50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-3">
                    <h4 class="text-[9px] font-black text-purple-600 uppercase tracking-tighter flex items-center gap-1">
                        <i class="bi bi-calendar-x-fill text-xs"></i> Overdue Prenatal
                    </h4>
                    <span class="px-2 py-0.5 bg-purple-50 text-purple-600 rounded-full text-[8px] font-black uppercase tracking-tighter border border-purple-100">{{ $midwifeAlerts['overdue_prenatal']->count() }} Alerts</span>
                </div>
                <div class="space-y-2">
                    @forelse($midwifeAlerts['overdue_prenatal'] as $overdue)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50/50 border border-gray-50 group-hover:border-purple-100 transition-all">
                        <span class="text-[10px] font-bold text-gray-900">{{ $overdue->user->full_name }}</span>
                        <a href="{{ route('midwife.patients.show', $overdue->user_id) }}" class="text-[8px] font-black text-purple-600 uppercase tracking-tighter hover:underline">View</a>
                    </div>
                    @empty
                    <p class="text-[9px] text-gray-400 italic">No overdue prenatal visits.</p>
                    @endforelse
                </div>
            </div>

            {{-- Immunization Due --}}
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-16 h-16 -mr-8 -mt-8 bg-blue-50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-3">
                    <h4 class="text-[9px] font-black text-blue-600 uppercase tracking-tighter flex items-center gap-1">
                        <i class="bi bi-shield-plus text-xs"></i> Immunization Due
                    </h4>
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded-full text-[8px] font-black uppercase tracking-tighter border border-blue-100">{{ $midwifeAlerts['immunization_due']->count() }} Due</span>
                </div>
                <div class="space-y-2">
                    @forelse($midwifeAlerts['immunization_due'] as $due)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50/50 border border-gray-100 group-hover:border-blue-100 transition-all">
                        <span class="text-[10px] font-bold text-gray-900">{{ $due->user->full_name }}</span>
                        <a href="{{ route('midwife.patients.show', $due->user_id) }}" class="text-[8px] font-black text-blue-600 uppercase tracking-tighter hover:underline">View</a>
                    </div>
                    @empty
                    <p class="text-[9px] text-gray-400 italic">No immunization alerts.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Today's Schedule --}}
    <div class="space-y-4">
        {{-- Section Header --}}
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-clipboard-pulse text-blue-600"></i>
                Today's Consultations
            </h2>
            <a href="{{ route($routePrefix . '.appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
        </div>

        {{-- Mobile View: Bento Cards --}}
        <div class="md:hidden space-y-4">
            @forelse($todaysAppointmentsList as $appt)
                <div class="bg-white rounded-[1.5rem] p-5 shadow-sm relative overflow-hidden">
                    {{-- Header: Time & Status --}}
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2 text-gray-500 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded-full">
                            <i class="bi bi-clock-fill text-blue-400"></i>
                            {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('h:i A') }}
                        </div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-50 text-yellow-600',
                                'approved' => 'bg-green-50 text-green-600',
                                'rejected' => 'bg-red-50 text-red-600',
                                'cancelled' => 'bg-gray-50 text-gray-600',
                                'rescheduled' => 'bg-blue-50 text-blue-600',
                                'completed' => 'bg-purple-50 text-purple-600',
                            ];
                            $statusClass = $statusColors[$appt->status] ?? 'bg-gray-50 text-gray-600';
                        @endphp
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide {{ $statusClass }}">
                            {{ $appt->status }}
                        </span>
                    </div>

                    {{-- Body: Patient & Service --}}
                    <div class="flex items-center gap-4 mb-5">
                         <div class="w-14 h-14 rounded-[1rem] bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xl shrink-0 shadow-sm">
                             {{ substr($appt->user->first_name, 0, 1) }}
                         </div>
                         <div>
                             <h4 class="text-lg font-bold text-gray-900 leading-tight">{{ $appt->user->full_name }}</h4>
                             <p class="text-xs font-bold text-blue-500 mt-1 uppercase tracking-wide">{{ $appt->service }}</p>
                         </div>
                    </div>

                    {{-- Footer: Date & Action --}}
                    <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                         <p class="text-xs text-gray-400 font-bold flex items-center gap-1">
                             <i class="bi bi-calendar4-week"></i>
                             {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('F j, Y') }}
                         </p>
                         <a href="{{ route($routePrefix . '.appointments.show', $appt->id) }}" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-full text-xs font-bold hover:bg-blue-700 transition shadow-sm shadow-blue-200">
                             <span>Start</span>
                             <i class="bi bi-arrow-right"></i>
                         </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-[1.5rem] p-8 text-center text-gray-500 shadow-sm border border-gray-100">
                    <div class="mb-3 bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                        <i class="bi bi-clipboard-check text-2xl text-gray-400"></i>
                    </div>
                    <p class="font-medium text-sm">No consultations pending at the moment.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop View: List --}}
        <div class="hidden md:block bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($todaysAppointmentsList as $appt)
                    <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                {{ substr($appt->user->first_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $appt->user->full_name }}</p>
                                <p class="text-sm text-gray-500 flex items-center gap-2">
                                    <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('h:i A') }}
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    {{ $appt->service }}
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span class="text-xs font-bold uppercase tracking-wider {{ $appt->status === 'approved' ? 'text-green-500' : 'text-orange-500' }}">
                                        {{ $appt->status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <a href="{{ route($routePrefix . '.appointments.show', $appt->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm shadow-blue-200">
                            Start Consultation
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <div class="mb-3 bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                            <i class="bi bi-clipboard-check text-2xl text-gray-400"></i>
                        </div>
                        <p class="font-medium">No consultations pending at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
