@extends('layouts.app')

@section('title', 'Assigned Appointments')

@section('content')
<div class="space-y-4">
    
    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-2xl shadow-lg p-4 sm:p-5 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-5" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center">
                        <i class="bi bi-calendar-check text-sm"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Assigned Appointments</h1>
                </div>
                <p class="text-brand-100 text-xs sm:text-sm font-medium">Manage clinical schedules and assessments.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-lg border border-white/20 text-center">
                    <p class="text-[7px] font-black uppercase tracking-tighter opacity-70">Total Today</p>
                    <p class="text-lg sm:text-xl font-black">{{ $counts['scheduled'] + $counts['walkin'] }}</p>
                </div>
                <a href="{{ route('healthworker.appointments.create') }}" 
                   class="inline-flex items-center px-5 py-2 rounded-lg bg-white text-brand-600 font-black hover:bg-brand-50 transition-all shadow-md transform hover:-translate-y-0.5 active:scale-95 whitespace-nowrap uppercase tracking-tighter text-xs">
                    <i class="bi bi-plus-lg mr-1.5 text-sm"></i>
                    New Appointment
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="bg-white p-1 rounded-lg shadow-sm border border-gray-100 flex flex-wrap gap-1 overflow-hidden">
        <a href="{{ route('healthworker.appointments.index', ['type' => 'scheduled', 'date' => request('date')]) }}"
           class="flex-1 min-w-[120px] text-center px-3 sm:px-5 py-2 rounded-md text-[8px] sm:text-xs font-black uppercase tracking-tighter transition-all {{ request('type') == 'scheduled' && !request('vitals_only') ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
            Online <span class="hidden sm:inline">Booking</span> <span class="ml-1 opacity-50">{{ $counts['scheduled'] }}</span>
        </a>
        <a href="{{ route('healthworker.appointments.index', ['type' => 'walk-in', 'date' => request('date')]) }}"
           class="flex-1 min-w-[120px] text-center px-3 sm:px-5 py-2 rounded-md text-[8px] sm:text-xs font-black uppercase tracking-tighter transition-all {{ request('type') == 'walk-in' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
            Walk-In <span class="ml-1 opacity-50">{{ $counts['walkin'] }}</span>
        </a>
        <a href="{{ route('healthworker.appointments.index', ['vitals_only' => 1, 'date' => request('date')]) }}"
           class="flex-1 min-w-[120px] text-center px-3 sm:px-5 py-2 rounded-md text-[8px] sm:text-xs font-black uppercase tracking-tighter transition-all {{ request('vitals_only') ? 'bg-orange-500 text-white shadow-md' : 'text-gray-400 hover:text-orange-500 hover:bg-orange-50' }}">
            Vitals <span class="hidden sm:inline">Needed</span> <span class="ml-1 opacity-50">{{ $counts['needing_vitals'] }}</span>
        </a>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
            @if(request('vitals_only')) <input type="hidden" name="vitals_only" value="1"> @endif

            <div class="space-y-1.5">
                <label class="block text-[8px] font-black text-gray-400 uppercase tracking-tighter ml-1">Filter Date</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" 
                           class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition shadow-sm">
                </div>
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Search Patient</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition shadow-sm"
                           placeholder="Name, ID, or Keywords...">
                    <button type="submit" class="absolute right-2 top-2 px-4 py-1.5 bg-brand-100 text-brand-600 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-brand-200 transition">Search</button>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition shadow-sm">
                    <option value="">All Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Finished</option>
                    <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            @if(request()->anyFilled(['search', 'status', 'date']) || request('vitals_only'))
                <div class="md:col-span-4 flex justify-end">
                    <a href="{{ route('healthworker.appointments.index') }}" class="text-xs font-black text-red-500 uppercase tracking-widest hover:text-red-600 flex items-center gap-2">
                        <i class="bi bi-x-circle"></i> Clear All Filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    @if(session('success') && session('queue_number'))
        <div class="mb-10 animate-in fade-in slide-in-from-top duration-700">
            <div class="bg-gradient-to-br from-brand-50 to-white border-2 border-brand-200 rounded-[2.5rem] p-8 md:p-10 shadow-2xl shadow-brand-200/20 relative overflow-hidden group text-center md:text-left">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-brand-500/5 rounded-full blur-3xl group-hover:bg-brand-500/10 transition-colors"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 md:gap-12">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-brand-600 text-white flex items-center justify-center shadow-xl shadow-brand-500/30 ring-8 ring-white transform group-hover:scale-105 transition-transform">
                        <i class="bi bi-check-lg text-5xl md:text-6xl font-black"></i>
                    </div>
                    
                    <div class="flex-1">
                        <h2 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight mb-2">Registration Successful!</h2>
                        <p class="text-gray-500 font-medium text-lg mb-6">The appointment has been recorded. Please provide the queue number to the patient.</p>
                        
                        <div class="inline-flex flex-col items-center md:items-start">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-500 mb-2 ml-1">Assigned Queue Number</span>
                            <div class="px-10 py-4 bg-white border-2 border-brand-500 rounded-3xl shadow-inner-lg group-hover:border-brand-600 transition-colors">
                                <span class="text-5xl md:text-6xl font-black text-brand-600 tracking-tighter">{{ session('queue_number') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-3 w-full md:w-auto">
                        <button onclick="window.print()" class="w-full px-8 py-4 bg-brand-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-printer-fill text-lg"></i> Print Slip
                        </button>
                        <a href="{{ route('healthworker.appointments.add-vitals', session('new_appointment_id')) }}" class="w-full px-8 py-4 bg-white border-2 border-brand-100 text-gray-700 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-gray-50 hover:border-brand-200 transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-heart-pulse-fill text-lg text-red-500"></i> Proceed to Vitals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('components.status-legend')

    <!-- Appointments Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient Details</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Medical Service</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Schedule</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($appointments as $appointment)
                        @php
                            $status = $appointment->status;
                            $hasVitals = $appointment->healthRecord && $appointment->healthRecord->vital_signs;
                            
                            // 🏥 Barangay Operating Hours: 8 AM to 5 PM
                            $closingTime = now()->setTime(17, 0, 0); // 5:00 PM
                            $isAfterHours = now()->isAfter($closingTime);
                            $apptDate = $appointment->slot ? \Carbon\Carbon::parse($appointment->slot->date) : $appointment->scheduled_at->copy();

                            // If it's past 5 PM and they haven't arrived, it's a "No Show"
                            $isNoShowByTime = $isAfterHours && $status === 'approved' && !$hasVitals && $apptDate->isToday();

                            if ($status === 'approved') {
                                if ($isNoShowByTime) {
                                    $label = 'Missing / No-Show';
                                    $colorClass = 'bg-red-50 text-red-700 border-red-100';
                                } else {
                                    $label = $hasVitals ? 'Ready for Provider' : 'Wait for Vitals';
                                    $colorClass = $hasVitals ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100';
                                }
                            } elseif ($status === 'completed') {
                                $label = 'Finished';
                                $colorClass = 'bg-green-50 text-green-600 border-green-100';
                            } elseif ($status === 'no_show') {
                                $label = 'No Show';
                                $colorClass = 'bg-orange-50 text-orange-600 border-orange-100';
                            } else {
                                $label = ucfirst($status);
                                $colorClass = 'bg-gray-50 text-gray-600 border-gray-100';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 font-black text-lg shadow-inner ring-4 ring-white transition group-hover:scale-110">
                                        {{ substr($appointment->display_patient_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 leading-none mb-1">{{ $appointment->display_patient_name }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Patient ID: #{{ $appointment->user_id ?? 'Walk-in' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <span class="px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest border border-gray-200">
                                    {{ $appointment->service }}
                                </span>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="text-sm font-black text-gray-900 leading-none mb-1">{{ $appointment->formatted_time }}</div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $appointment->formatted_date }}</div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $colorClass }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($status === 'approved' && !$hasVitals)
                                        @php
                                            $isFuture = $apptDate->isFuture() && !$apptDate->isToday();
                                            
                                            // 🏥 After Hours Logic: Center closes at 5 PM
                                            $isTodayAfterHours = $apptDate->isToday() && $isAfterHours;
                                            
                                            $confirmMsg = "";
                                            if ($isFuture) {
                                                $confirmMsg = "This appointment is scheduled for a future date (" . $apptDate->format('M d, Y') . "). Are you sure you want to capture vitals today?";
                                            } elseif ($isTodayAfterHours) {
                                                $confirmMsg = "The center is now closed (Past 5:00 PM). Are you sure you still want to take vitals for this late patient?";
                                            }
                                        @endphp
                                        <a href="{{ route('healthworker.appointments.add-vitals', $appointment->id) }}" 
                                           @if($isFuture || $isTodayAfterHours) onclick="return confirm('{{ $confirmMsg }}')" @endif
                                           class="p-2.5 rounded-xl bg-orange-500 text-white shadow-lg shadow-orange-200 hover:bg-orange-600 transition transform hover:-translate-y-0.5 {{ $isNoShowByTime ? 'bg-red-500 shadow-red-200 hover:bg-red-600' : '' }}"
                                           title="{{ $isNoShowByTime ? 'Capture Vitals for Late Patient' : 'Capture Vital Signs' }}">
                                            <i class="bi bi-heart-pulse-fill text-lg"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('healthworker.appointments.show', $appointment->id) }}" 
                                       class="p-2.5 rounded-xl bg-gray-50 text-gray-400 hover:bg-brand-600 hover:text-white transition transform hover:-translate-y-0.5 shadow-sm"
                                       title="View Details">
                                        <i class="bi bi-eye-fill text-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="max-w-xs mx-auto">
                                    <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-gray-200 border-4 border-white shadow-inner">
                                        <i class="bi bi-calendar-x text-4xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 mb-2">No appointments found</h3>
                                    <p class="text-sm font-medium text-gray-500 leading-relaxed mb-6">We couldn't find any appointments for this criteria. Try adjusting your filters or date selection.</p>
                                    @if(request()->anyFilled(['search', 'status', 'date']) || request('vitals_only'))
                                        <a href="{{ route('healthworker.appointments.index') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-lg shadow-brand-200 hover:bg-brand-700 transition">
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
        
        @if($appointments->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
