@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @php
        $user = auth()->user();
        $routePrefix = $user->isMidwife() ? 'midwife' : 'doctor';
        $providerLabel = $user->isMidwife() ? 'Midwife' : 'Doctor';
    @endphp

    {{-- Top-Aligned Compact Header --}}
    <div class="bg-gradient-to-br from-brand-700 via-brand-600 to-brand-500 rounded-2xl sm:rounded-2xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden mb-6 group">
        <div class="absolute inset-0 bg-white/5 opacity-5" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="max-w-xl">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 shadow-inner">
                        <i class="bi bi-heart-pulse-fill text-sm"></i>
                    </div>
                    <span class="text-[8px] font-black uppercase tracking-[0.15em] opacity-80">Clinical Workspace</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black mb-1 tracking-tight leading-tight">
                    My Appointments
                </h1>
                <p class="text-brand-50 text-xs sm:text-xs font-medium leading-relaxed opacity-80">
                    Welcome, {{ $providerLabel }} {{ $user->last_name }}. Manage your schedule.
                </p>
            </div>

            {{-- Compact Header Stats --}}
            <div class="flex items-center gap-2 sm:gap-3 w-full lg:w-auto shrink-0 overflow-x-auto pb-2 sm:pb-0 no-scrollbar">
                <div class="bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-lg border border-white/20 flex flex-col items-center justify-center min-w-[90px] shadow-lg group-hover:bg-white/20 transition-all duration-300">
                    <p class="text-[7px] font-black uppercase tracking-tighter opacity-70 mb-0.5">Today</p>
                    <p class="text-lg sm:text-xl font-black tracking-tighter">{{ $stats['today_total'] }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-lg border border-white/20 flex flex-col items-center justify-center min-w-[90px] shadow-lg group-hover:bg-white/20 transition-all duration-300">
                    <p class="text-[7px] font-black uppercase tracking-tighter opacity-70 mb-0.5 text-red-200">Needs Completion</p>
                    <p class="text-lg sm:text-xl font-black tracking-tighter text-red-300">{{ $stats['ready'] }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-lg border border-white/20 flex flex-col items-center justify-center min-w-[90px] shadow-lg group-hover:bg-white/20 transition-all duration-300">
                    <p class="text-[7px] font-black uppercase tracking-tighter opacity-70 mb-0.5 text-orange-200">Wait</p>
                    <p class="text-lg sm:text-xl font-black tracking-tighter text-orange-300">{{ $stats['pending_vitals'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Bar & Filters (Made more compact) --}}
    <div class="bg-white rounded-xl shadow-md shadow-gray-200/30 border border-gray-100 p-4 sm:p-5 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            
            <!-- Quick Navigation (Midwife Only) -->
            @if(auth()->user()->isMidwife())
            <div class="flex items-center p-1 bg-gray-50 rounded-lg border border-gray-100 shrink-0">
                <a href="{{ route($routePrefix . '.appointments.index') }}" 
                   class="px-4 py-2 rounded-md text-[8px] font-black uppercase tracking-tighter transition-all {{ !request()->routeIs('midwife.slots.*') ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-600' }}">
                    Appointments
                </a>
                <a href="{{ route('midwife.slots.index') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('midwife.slots.*') ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-600' }}">
                    Slots
                </a>
            </div>
            @endif

            <!-- Unified Filter Form -->
            <form action="{{ route($routePrefix . '.appointments.index') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-search text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off"
                           class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all shadow-inner placeholder:text-gray-400"
                           placeholder="Patient Name...">
                </div>

                <!-- Date -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-calendar3 text-xs"></i>
                    </div>
                    <input type="date" name="date" id="dateInput" value="{{ request('date') }}"
                           class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all shadow-inner">
                </div>

                <!-- Status -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-funnel-fill text-xs"></i>
                    </div>
                    <select name="status" class="block w-full pl-10 pr-10 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-black uppercase tracking-wider focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all appearance-none cursor-pointer shadow-inner">
                        <option value="">Status: Active</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Items</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>No Show</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Records</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md shadow-brand-600/20 active:scale-95 flex items-center justify-center gap-2">
                        Apply
                    </button>
                    <button type="button" id="btnToday" class="p-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all active:scale-95 shadow-sm" title="Today">
                        <i class="bi bi-calendar-event"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'date', 'status']))
                        <a href="{{ route($routePrefix . '.appointments.index') }}" class="p-2.5 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all active:scale-95 shadow-sm" title="Clear Filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @include('components.status-legend')

    @php
        $hasManualReminderTargets = $appointments->getCollection()->contains(function ($appt) {
            $isPastAppt = false;

            // Prioritize slot end-time for no-show logic; scheduled_at alone can be earlier than slot end.
            if ($appt->slot) {
                $isPastAppt = $appt->slot->isExpired();
            } elseif ($appt->scheduled_at) {
                $isPastAppt = $appt->scheduled_at->isPast();
            }

            return in_array($appt->status, ['pending', 'approved', 'rescheduled']) && !$isPastAppt;
        });
    @endphp

    @if($hasManualReminderTargets)
        <div class="mb-4 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-xs text-amber-800 font-semibold flex items-center gap-2">
            <i class="bi bi-hand-index-thumb text-amber-600"></i>
            Click <span class="font-black">SMS</span> or <span class="font-black">Email</span> in the action column to send a manual reminder.
        </div>
    @endif

    {{-- ACTION REQUIRED ALERT --}}
    @if($stats['ready'] > 0)
        <div class="mb-4 rounded-xl border-2 border-red-300 bg-gradient-to-r from-red-50 to-red-50/50 px-4 py-4 text-sm text-red-900 font-black flex items-center justify-between gap-4 shadow-lg shadow-red-200/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0 animate-pulse">
                    <i class="bi bi-exclamation-circle-fill text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-black uppercase tracking-widest">⚠️ Action Required</p>
                    <p class="text-xs font-bold text-red-700 mt-0.5">{{ $stats['ready'] }} appointment(s) ready for {{ $providerLabel }}</p>
                    <p class="text-[10px] font-medium text-red-600 mt-1">Vitals captured - patient waiting for consultation</p>
                </div>
            </div>
            <a href="{{ route($routePrefix . '.appointments.index', ['status' => 'active']) }}" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-all shadow-lg hover:shadow-red-500/50 active:scale-95 flex-shrink-0 whitespace-nowrap">
                View All
            </a>
        </div>
    @endif

    {{-- Main List Container --}}
    <div class="space-y-6">
        
        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-6">
            @forelse($appointments as $appt)
                @php
                    $needsCompletion = in_array($appt->id, $readyAppointmentIds);
                @endphp
                <div class="bg-white rounded-[2.5rem] p-6 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden transition transform active:scale-[0.98] {{ $needsCompletion ? 'ring-2 ring-red-400 ring-offset-0' : '' }}">
                    {{-- Alert Badge if Needs Completion --}}
                    @if($needsCompletion)
                        <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest animate-pulse shadow-lg flex items-center gap-1.5 z-10">
                            <i class="bi bi-exclamation-lg"></i> Action
                        </div>
                    @endif

                    {{-- Status Indicator --}}
                    @php
                        $status = $appt->status;
                        $hasVitals = $appt->healthRecord && $appt->healthRecord->vital_signs;
                        $scheduleDate = $appt->slot?->date ? $appt->slot->date->format('M d, Y') : ($appt->scheduled_at ? $appt->scheduled_at->format('M d, Y') : '-');
                        $scheduleTime = $appt->slot ? $appt->slot->displayTime() : ($appt->scheduled_at ? $appt->scheduled_at->format('h:i A') : '--:--');
                        
                        $isPastAppt = false;
                        if ($appt->slot) {
                            $isPastAppt = $appt->slot->isExpired();
                        } elseif ($appt->scheduled_at) {
                            $isPastAppt = $appt->scheduled_at->isPast();
                        }

                        if (!$isPastAppt && $status === 'no_show') $status = 'approved';

                        if (in_array($status, ['approved','rescheduled', 'no_show', 'pending']) && $isPastAppt) {
                            $label = 'No Show'; $statusClass = 'bg-orange-50 text-orange-600 border-orange-100'; $icon = 'bi-person-x-fill';
                        } elseif ($status === 'approved') {
                            $label = $hasVitals ? 'Ready for Provider' : 'Wait for Vitals';
                            $statusClass = $hasVitals ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100';
                            $icon = $hasVitals ? 'bi-clipboard-check-fill' : 'bi-hourglass-split';
                        } elseif ($status === 'completed') {
                            $label = 'Finished'; $statusClass = 'bg-green-50 text-green-600 border-green-100'; $icon = 'bi-check-circle-fill';
                        } elseif ($status === 'rescheduled') {
                            $label = 'Rescheduled'; $statusClass = 'bg-sky-50 text-sky-600 border-sky-100'; $icon = 'bi-arrow-repeat';
                        } elseif ($status === 'no_show') {
                            $label = 'No Show'; $statusClass = 'bg-orange-50 text-orange-600 border-orange-100'; $icon = 'bi-person-x-fill';
                        } else {
                            $label = ucfirst($status); $statusClass = 'bg-gray-50 text-gray-600 border-gray-100'; $icon = 'bi-info-circle-fill';
                        }
                    @endphp

                    <div class="flex justify-between items-start mb-6">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">Appointment Status</span>
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                                <i class="bi {{ $icon }}"></i> {{ $label }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1 block">Time</span>
                            <span class="text-sm font-black text-gray-900 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 shadow-sm">
                                {{ $scheduleTime }}
                            </span>
                        </div>
                    </div>

                    {{-- Patient Details --}}
                    <div class="flex items-center gap-5 mb-6 p-4 bg-gray-50/50 rounded-3xl border border-gray-100 shadow-inner">
                        <div class="w-16 h-16 rounded-2xl bg-brand-100 flex items-center justify-center text-brand-700 font-black text-2xl shadow-sm border border-white">
                            {{ substr($appt->user->first_name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-gray-900 leading-tight mb-1">{{ $appt->user->full_name }}</h4>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black text-brand-500 uppercase tracking-widest bg-brand-50 px-2 py-0.5 rounded-lg border border-brand-100">{{ $appt->service }}</span>
                                <span class="text-[10px] font-bold text-gray-400">#{{ str_pad($appt->user->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    @php
                        $canManualReminder = in_array($appt->status, ['pending', 'approved', 'rescheduled']) && !$isPastAppt;
                    @endphp
                    <div class="flex flex-col gap-3">
                        @if($canManualReminder)
                        <div class="grid grid-cols-2 gap-2">
                            <form action="{{ route($routePrefix . '.appointments.send-reminder-sms', $appt->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="Manual send reminder SMS" aria-label="Manual send reminder SMS" class="w-full inline-flex items-center justify-center gap-1 bg-blue-50 text-blue-700 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border border-blue-100 hover:bg-blue-600 hover:text-white transition-all">
                                    <i class="bi bi-chat-dots-fill"></i> SMS
                                </button>
                            </form>
                            <form action="{{ route($routePrefix . '.appointments.send-reminder-email', $appt->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="Manual send reminder Email" aria-label="Manual send reminder Email" class="w-full inline-flex items-center justify-center gap-1 bg-emerald-50 text-emerald-700 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all">
                                    <i class="bi bi-envelope-fill"></i> Email
                                </button>
                            </form>
                        </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-gray-400 text-[10px] font-black uppercase tracking-widest">
                            <i class="bi bi-calendar4-week text-sm"></i>
                            {{ $scheduleDate }}
                            </div>
                            <a href="{{ route($routePrefix . '.appointments.show', $appt->id) }}" 
                               class="inline-flex items-center gap-2 bg-brand-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.15em] hover:bg-brand-700 shadow-lg shadow-brand-600/20 transition-all">
                                Review <i class="bi bi-arrow-right text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-[2.5rem] p-12 text-center text-gray-500 shadow-xl border border-gray-100">
                    <div class="mb-6 bg-gray-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto border-4 border-white shadow-inner">
                        <i class="bi bi-inbox text-5xl text-gray-300"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">No Appointments Found</h3>
                    <p class="font-medium text-gray-500 max-w-xs mx-auto">Try adjusting your filters or date selection to find what you're looking for.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop View: Master Table --}}
        <div class="hidden md:block bg-white rounded-[3rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Patient Profile</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Medical Service</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Schedule</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Clinical Status</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Workspace</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($appointments as $appt)
                            @php
                                $status = $appt->status;
                                $hasVitals = $appt->healthRecord && $appt->healthRecord->vital_signs;
                                $needsCompletion = in_array($appt->id, $readyAppointmentIds);
                                $scheduleDate = $appt->slot?->date ? $appt->slot->date->format('M j, Y') : ($appt->scheduled_at ? $appt->scheduled_at->format('M j, Y') : 'N/A');
                                $scheduleTime = $appt->slot ? $appt->slot->displayTime() : ($appt->scheduled_at ? $appt->scheduled_at->format('h:i A') : '');
                                
                                $isPastAppt = false;
                                if ($appt->slot) {
                                    $isPastAppt = $appt->slot->isExpired();
                                } elseif ($appt->scheduled_at) {
                                    $isPastAppt = $appt->scheduled_at->isPast();
                                }

                                if (!$isPastAppt && $status === 'no_show') $status = 'approved';

                                if (in_array($status, ['approved','rescheduled', 'no_show', 'pending']) && $isPastAppt) {
                                    $label = 'No Show'; $statusClass = 'bg-orange-50 text-orange-600 border-orange-100'; $icon = 'bi-person-x-fill';
                                } elseif ($status === 'approved') {
                                    $label = $hasVitals ? 'Ready for Provider' : 'Wait for Vitals';
                                    $statusClass = $hasVitals ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100';
                                    $icon = $hasVitals ? 'bi-clipboard-check-fill' : 'bi-hourglass-split';
                                } elseif ($status === 'completed') {
                                    $label = 'Finished'; $statusClass = 'bg-green-50 text-green-600 border-green-100'; $icon = 'bi-check-circle-fill';
                                } elseif ($status === 'rescheduled') {
                                    $label = 'Rescheduled'; $statusClass = 'bg-sky-50 text-sky-600 border-sky-100'; $icon = 'bi-arrow-repeat';
                                } else {
                                    $label = ucfirst($status); $statusClass = 'bg-gray-50 text-gray-600 border-gray-100'; $icon = 'bi-info-circle-fill';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-all group {{ $needsCompletion ? 'border-l-4 border-l-red-500 bg-red-50/30' : '' }}">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        @if($needsCompletion)
                                            <div class="text-red-500 font-black text-lg" title="Needs completion">
                                                <i class="bi bi-exclamation-circle-fill"></i>
                                            </div>
                                        @else
                                            <div></div>
                                        @endif
                                        <div class="h-12 w-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 font-black text-lg border border-brand-100 shadow-sm transition-transform group-hover:scale-110">
                                            {{ substr($appt->user->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 group-hover:text-brand-600 transition-colors leading-tight mb-0.5">{{ $appt->user->full_name }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PID: #{{ str_pad($appt->user->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-[0.1em] border border-gray-200">
                                        {{ $appt->service }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 leading-tight mb-0.5">
                                            {{ $scheduleDate }}
                                        </span>
                                        <span class="text-[10px] font-black text-brand-500 uppercase tracking-widest">
                                            {{ $scheduleTime }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                                        <i class="bi {{ $icon }}"></i> {{ $label }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @php
                                        $canManualReminder = in_array($appt->status, ['pending', 'approved', 'rescheduled']) && !$isPastAppt;
                                    @endphp
                                    <div class="inline-flex flex-col items-end gap-2">
                                        @if($canManualReminder)
                                            <div class="flex items-center gap-2">
                                                <form action="{{ route($routePrefix . '.appointments.send-reminder-sms', $appt->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" title="Manual send reminder SMS" aria-label="Manual send reminder SMS" class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border border-blue-100 hover:bg-blue-600 hover:text-white transition-all">
                                                        <i class="bi bi-chat-dots-fill"></i> SMS
                                                    </button>
                                                </form>
                                                <form action="{{ route($routePrefix . '.appointments.send-reminder-email', $appt->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" title="Manual send reminder Email" aria-label="Manual send reminder Email" class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all">
                                                        <i class="bi bi-envelope-fill"></i> Email
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                        <a href="{{ route($routePrefix . '.appointments.show', $appt->id) }}" 
                                           class="inline-flex items-center gap-2 bg-gray-50 text-gray-400 px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-600 hover:text-white transition-all shadow-sm hover:shadow-brand-600/20 active:scale-[0.98]">
                                            Review <i class="bi bi-chevron-right text-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-24 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 text-gray-200 border-4 border-white shadow-inner">
                                            <i class="bi bi-calendar-x text-5xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-black text-gray-900 mb-2">No Appointments</h3>
                                        <p class="text-sm font-medium text-gray-500 mb-8">There are no records matching your current filter criteria.</p>
                                        @if(request()->anyFilled(['search', 'date', 'status']))
                                            <a href="{{ route($routePrefix . '.appointments.index') }}" class="inline-flex items-center px-8 py-4 bg-brand-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-700 shadow-xl shadow-brand-600/20 transition-all">
                                                Reset Filters
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($appointments->hasPages())
        <div class="mt-12 bg-white px-8 py-6 rounded-[2rem] shadow-lg border border-gray-100 flex items-center justify-center">
            {{ $appointments->links() }}
        </div>
    @endif
</div>

{{-- Inline JS for Quick Actions --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnToday = document.getElementById('btnToday');
        const dateInput = document.getElementById('dateInput');
        if (btnToday && dateInput) {
            btnToday.addEventListener('click', () => {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                dateInput.value = `${year}-${month}-${day}`;
                dateInput.form.submit();
            });
        }
    });
</script>

<style>
    /* Custom Scrollbar for the table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>
@endsection
