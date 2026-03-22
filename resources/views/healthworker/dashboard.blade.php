@extends('layouts.app')

@section('title', 'Health Worker Dashboard')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-br from-brand-600 via-brand-500 to-indigo-600 rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 text-white shadow-xl shadow-brand-500/20 relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-2xl sm:text-4xl font-black mb-2 sm:mb-3 tracking-tight leading-tight">
                Welcome back, <br class="sm:hidden"> {{ auth()->user()->first_name }}! 👋
            </h1>
            <p class="text-brand-50 text-xs sm:text-lg font-medium opacity-90 max-w-xl">
                Here's what's happening in your clinic today.
            </p>
            
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-2 rounded-xl border border-white/10 shadow-sm w-fit">
                    <i class="bi bi-calendar3 text-brand-200 text-xs sm:text-sm"></i>
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest">{{ now()->format('l, F j, Y') }}</span>
                </div>
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-2 rounded-xl border border-white/10 shadow-sm w-fit">
                    <i class="bi bi-clock text-brand-200 text-xs sm:text-sm"></i>
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute -right-10 -bottom-10 sm:-right-16 sm:-bottom-16 opacity-10 pointer-events-none">
            <i class="bi bi-activity text-[12rem] sm:text-[18rem]"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8">
        
        {{-- Left Column: Main Content --}}
        <div class="lg:col-span-3 space-y-6 sm:gap-8">
            
            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                {{-- Upcoming Today --}}
                <a href="{{ route('healthworker.appointments.index') }}" class="bg-white rounded-[1.5rem] sm:rounded-[2rem] p-5 sm:p-6 border border-gray-100 shadow-sm flex items-center justify-between gap-3 group hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[9px] sm:text-xs font-black text-brand-600 uppercase tracking-widest mb-1">Today</p>
                        <h3 class="text-2xl sm:text-4xl font-black text-gray-900">{{ $upcomingToday }}</h3>
                    </div>
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 text-2xl sm:text-3xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shadow-sm relative z-10">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </a>

                {{-- Total Patients --}}
                <div class="bg-white rounded-[1.5rem] sm:rounded-[2rem] p-5 sm:p-6 border border-gray-100 shadow-sm flex items-center justify-between gap-3 relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[9px] sm:text-xs font-black text-green-600 uppercase tracking-widest mb-1">Patients</p>
                        <h3 class="text-2xl sm:text-4xl font-black text-gray-900">{{ $totalPatients }}</h3>
                    </div>
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 text-2xl sm:text-3xl transition-transform duration-300 shadow-sm relative z-10">
                        <i class="bi bi-people"></i>
                    </div>
                </div>

                {{-- Needing Vitals --}}
                <a href="{{ route('healthworker.appointments.index', ['vitals_only' => 1, 'date' => now()->toDateString()]) }}" class="bg-white rounded-[1.5rem] sm:rounded-[2rem] p-5 sm:p-6 border border-gray-100 shadow-sm flex items-center justify-between gap-3 group hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[9px] sm:text-xs font-black text-yellow-600 uppercase tracking-widest mb-1">Vitals</p>
                        <h3 class="text-2xl sm:text-4xl font-black text-gray-900">{{ $needingVitalsCount }}</h3>
                    </div>
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl sm:text-3xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shadow-sm relative z-10">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                </a>
            </div>

            {{-- Today's Schedule --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 sm:px-8 sm:py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-sm">
                            <i class="bi bi-calendar-check text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight uppercase">Today's Schedule</h2>
                            <p class="text-[8px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Patient agenda and queue</p>
                        </div>
                    </div>
                    <span class="px-4 py-1.5 rounded-full bg-white border border-gray-200 text-[10px] sm:text-xs font-black text-gray-500 uppercase tracking-widest shadow-sm w-fit">
                        {{ now()->format('M d, Y') }}
                    </span>
                </div>
                
                <div class="divide-y divide-gray-50">
                    @forelse($todaysAppointmentsList as $appointment)
                        @php
                            $status = $appointment->status;
                            $hasVitals = $appointment->healthRecord && $appointment->healthRecord->vital_signs;
                            
                            // 🏥 Barangay Operating Hours: 8 AM to 5 PM
                            $closingTime = now()->setTime(17, 0, 0); // 5:00 PM
                            $isAfterHours = now()->isAfter($closingTime);

                            // Check if appointment time has passed
                            $apptDate = $appointment->slot ? \Carbon\Carbon::parse($appointment->slot->date) : $appointment->scheduled_at->copy();
                            
                            if ($appointment->slot && $appointment->slot->end_time) {
                                $endTime = $apptDate->copy()->setTimeFrom($appointment->slot->end_time);
                            } else {
                                $endTime = $appointment->scheduled_at->copy()->addMinutes(30);
                            }
                            
                            $isPast = now()->isAfter($endTime);
                            $isOverdue = $isPast && $status === 'approved' && !$hasVitals;
                            
                            // If it's past 5 PM and they haven't arrived, it's a "No Show"
                            $isNoShowByTime = $isAfterHours && $status === 'approved' && !$hasVitals && $apptDate->isToday();
                            
                            $isFinished = in_array($status, ['completed', 'cancelled', 'rejected']) || $isNoShowByTime;
                            
                            if ($status === 'approved') {
                                if ($isNoShowByTime) {
                                    $label = 'Missing / No-Show';
                                    $colorClass = 'bg-red-50 text-red-700 border-red-100';
                                } else {
                                    $label = $hasVitals ? 'Ready' : 'Vitals';
                                    $colorClass = $hasVitals ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-indigo-50 text-indigo-700 border-indigo-100';
                                }
                            } elseif ($status === 'completed') {
                                $label = 'Finished';
                                $colorClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                            } else {
                                $label = ucfirst($status);
                                $colorClass = 'bg-gray-50 text-gray-700 border-gray-100';
                            }
                        @endphp
                        <div class="p-5 hover:bg-gray-50 transition-colors duration-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group {{ ($isPast || $isFinished) ? 'opacity-60 grayscale-[0.3]' : '' }}">
                            <div class="flex items-center gap-4 sm:gap-5 w-full sm:w-auto">
                                <div class="flex flex-col items-center justify-center w-14 h-14 sm:w-16 sm:h-16 bg-white rounded-2xl text-brand-700 font-bold border-2 {{ $isPast ? 'border-gray-200 text-gray-400' : 'border-brand-100 shadow-sm' }} group-hover:border-brand-300 transition-colors shrink-0">
                                    <span class="text-base sm:text-lg leading-none">{{ $appointment->scheduled_at->format('h:i') }}</span>
                                    <span class="text-[10px] sm:text-xs uppercase font-bold text-brand-400 mt-1">{{ $appointment->scheduled_at->format('A') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-brand-600 transition-colors truncate">{{ $appointment->display_patient_name }}</h4>
                                        @if($appointment->type === 'walk-in')
                                            <span class="px-2 py-0.5 rounded-md bg-orange-100 text-orange-600 text-[9px] font-black uppercase tracking-widest border border-orange-200">
                                                Walk-in
                                            </span>
                                        @endif
                                        @php
                                            $isFutureBadge = $apptDate->isFuture() && !$apptDate->isToday();
                                        @endphp
                                        @if($isFutureBadge)
                                            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest border border-blue-100">
                                                Future
                                            </span>
                                        @endif
                                        @if($isOverdue)
                                            <span class="px-2 py-0.5 rounded-md bg-red-100 text-red-600 text-[9px] font-black uppercase tracking-tighter animate-pulse">
                                                Overdue
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] sm:text-xs text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded uppercase tracking-wider">{{ $appointment->service }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $colorClass }} border">
                                    {{ $label }}
                                </span>
                                
                                @php
                                    $isVitalsAction = ($status === 'approved' && !$hasVitals);
                                    $vitalsUrl = $isVitalsAction ? route('healthworker.appointments.add-vitals', $appointment->id) : route('healthworker.appointments.show', $appointment->id);
                                    
                                    // ⚠️ Operation Warnings
                                    $isFuture = $apptDate->isFuture() && !$apptDate->isToday();
                                    
                                    // 🏥 After Hours Logic: Center closes at 5 PM
                                    $isTodayAfterHours = $apptDate->isToday() && $isAfterHours;
                                    
                                    $confirmMsg = "";
                                    if ($isVitalsAction) {
                                        if ($isFuture) {
                                            $confirmMsg = "This appointment is scheduled for a future date (" . $apptDate->format('M d, Y') . "). Are you sure you want to capture vitals today?";
                                        } elseif ($isTodayAfterHours) {
                                            $confirmMsg = "The center is now closed (Past 5:00 PM). Are you sure you still want to take vitals for this late patient?";
                                        }
                                    }
                                @endphp

                                <a href="{{ $vitalsUrl }}" 
                                   @if($isVitalsAction && ($isFuture || $isTodayAfterHours)) onclick="return confirm('{{ $confirmMsg }}')" @endif
                                   class="flex-1 sm:flex-none py-2.5 sm:p-0 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-brand-600 hover:text-white transition-all duration-200 border border-gray-100 sm:border-0 text-xs font-bold sm:text-base {{ $isNoShowByTime ? 'hover:bg-red-600' : '' }}">
                                    <span class="sm:hidden mr-2">View Details</span>
                                    <i class="bi bi-chevron-right text-lg"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                <i class="bi bi-calendar-x text-4xl"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold text-lg">No appointments today</h3>
                            <p class="text-gray-500 mt-1">You have a clear schedule for today!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="mb-6">
                {{-- Mobile Card View --}}
                <div class="md:hidden space-y-4 mb-6">
                    <div class="flex flex-col mb-4 px-1">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                <i class="bi bi-clock-history text-brand-600"></i> Recently Updated
                            </h3>
                            <a href="{{ route('healthworker.appointments.index') }}" class="text-sm font-bold text-brand-600">See all</a>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Live feed of latest system actions</p>
                    </div>

                    @forelse($recentAppointments as $appointment)
                        <div class="bg-white rounded-[1.5rem] p-5 shadow-sm relative overflow-hidden">
                            {{-- Header: Time & Status --}}
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2 text-gray-500 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded-full">
                                    <i class="bi bi-clock-fill text-brand-400"></i>
                                    {{ $appointment->formatted_time }}
                                </div>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-50 text-yellow-600',
                                        'approved' => 'bg-green-50 text-green-600',
                                        'rejected' => 'bg-red-50 text-red-600',
                                        'cancelled' => 'bg-gray-50 text-gray-600',
                                        'rescheduled' => 'bg-blue-50 text-blue-600',
                                    ];
                                    $statusClass = $statusColors[$appointment->status] ?? 'bg-gray-50 text-gray-600';
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide {{ $statusClass }}">
                                    {{ $appointment->status }}
                                </span>
                            </div>
                            
                            {{-- Body: Patient & Service --}}
                            <div class="flex items-center gap-4 mb-5">
                                 <div class="w-14 h-14 rounded-[1rem] bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-xl shrink-0 shadow-sm">
                                     {{ substr($appointment->display_patient_name, 0, 1) }}
                                 </div>
                                 <div>
                                     <h4 class="text-lg font-bold text-gray-900 leading-tight">{{ $appointment->display_patient_name }}</h4>
                                     <p class="text-xs font-bold text-brand-500 mt-1 uppercase tracking-wide">{{ $appointment->service }}</p>
                                 </div>
                            </div>
               
                            {{-- Footer: Date & Action --}}
                            <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                                 <p class="text-xs text-gray-400 font-bold flex items-center gap-1">
                                     <i class="bi bi-calendar4-week"></i>
                                     {{ $appointment->scheduled_at ? $appointment->scheduled_at->format('F j, Y') : '-' }}
                                 </p>
                                 <a href="{{ ($appointment->status === 'approved' && !($appointment->healthRecord && $appointment->healthRecord->vital_signs)) ? route('healthworker.appointments.add-vitals', $appointment->id) : route('healthworker.appointments.show', $appointment->id) }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-900 hover:bg-brand-600 hover:text-white transition shadow-sm">
                                     <i class="bi bi-arrow-right text-lg"></i>
                                 </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-[1.5rem] p-8 text-center text-gray-500 shadow-sm">
                            <i class="bi bi-inbox text-4xl mb-2 text-gray-300 block"></i>
                            <p class="text-sm">No recent activity found.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Desktop Table View --}}
                <div class="hidden md:block bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="bi bi-clock-history text-xl"></i>
                                </div>
                                Recently Updated
                            </h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 ml-13">Live feed of latest system actions</p>
                        </div>
                        <a href="{{ route('healthworker.appointments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-brand-600 transition-all shadow-sm">
                            View All <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @forelse($recentAppointments as $appointment)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-sm ring-2 ring-white shadow-sm">
                                                    {{ substr($appointment->display_patient_name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                        {{ $appointment->display_patient_name }}
                                                        @if($appointment->type === 'walk-in')
                                                            <span class="px-1.5 py-0.5 rounded bg-orange-50 text-orange-600 text-[8px] font-black uppercase tracking-widest border border-orange-100">
                                                                Walk-in
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <span class="text-sm text-gray-600 font-medium bg-gray-100 px-2 py-1 rounded">{{ $appointment->service }}</span>
                                        </td>
                                        <td class="hidden sm:table-cell px-6 py-5 whitespace-nowrap text-sm text-gray-500 font-medium">
                                            {{ $appointment->formatted_date }}
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @php
                                                $status = $appointment->status;
                                                $hasVitals = $appointment->healthRecord && $appointment->healthRecord->vital_signs;
                                                
                                                // Date check for the badge and confirmation
                                                $apptDate = $appointment->slot ? \Carbon\Carbon::parse($appointment->slot->date) : $appointment->scheduled_at->copy();
                                                $isFuture = $apptDate->isFuture() && !$apptDate->isToday();

                                                if ($status === 'approved') {
                                                    $label = $hasVitals ? 'Ready for Provider' : 'Wait for Vitals';
                                                    $colorClass = $hasVitals ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-indigo-100 text-indigo-700 border-indigo-200';
                                                } elseif ($status === 'completed') {
                                                    $label = 'Finished';
                                                    $colorClass = 'bg-green-100 text-green-700 border-green-200';
                                                } elseif ($status === 'rejected') {
                                                    $label = 'Rejected';
                                                    $colorClass = 'bg-red-100 text-red-700 border-red-200';
                                                } else {
                                                    $label = ucfirst($status);
                                                    $colorClass = 'bg-gray-100 text-gray-700 border-gray-200';
                                                }
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $colorClass }} border">
                                                    {{ $label }}
                                                </span>
                                                @if($isFuture)
                                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest border border-blue-100">
                                                        Future
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-right">
                                            @php
                                                $vitalsUrl = ($status === 'approved' && !$hasVitals) ? route('healthworker.appointments.add-vitals', $appointment->id) : route('healthworker.appointments.show', $appointment->id);
                                                $isVitalsAction = ($status === 'approved' && !$hasVitals);
                                                $confirmMsg = $isFuture ? "This appointment is scheduled for a future date (" . $apptDate->format('M d, Y') . "). Are you sure you want to capture vitals today?" : "";
                                            @endphp
                                            <a href="{{ $vitalsUrl }}" 
                                               @if($isVitalsAction && $isFuture) onclick="return confirm('{{ $confirmMsg }}')" @endif
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                                                <i class="bi bi-chevron-right font-bold"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="bi bi-inbox text-3xl text-gray-300 mb-2"></i>
                                                <span class="font-medium">No recent activity found</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column: Quick Actions & Info --}}
        <div class="lg:col-span-1 space-y-8">
            
            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    Quick Actions
                </h3>
                <div class="space-y-4">
                    <a href="{{ route('healthworker.appointments.index') }}" class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100 hover:bg-brand-50 hover:border-brand-200 transition-all group shadow-sm hover:shadow-md">
                        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                            <i class="bi bi-calendar-plus text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-gray-900 group-hover:text-brand-700 transition-colors">Appointments</span>
                            <span class="text-xs text-gray-500">Manage schedules</span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Top Services --}}
            @if(isset($serviceStats) && $serviceStats->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    Top Services
                </h3>
                <div class="space-y-5">
                    @foreach($serviceStats->take(5) as $stat)
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-700 font-bold">{{ $stat->service }}</span>
                            <span class="text-gray-500 font-medium">{{ $stat->count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-brand-500 to-brand-400 h-2.5 rounded-full shadow-sm" @style(['width: ' . $stat->percentage . '%'])></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
