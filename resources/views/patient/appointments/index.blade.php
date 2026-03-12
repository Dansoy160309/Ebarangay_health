{{-- resources/views/patient/appointments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="py-10 space-y-12" x-data="{ 
    openModal: false, 
    selectedSlot: null, 
    isSubmitting: false,
    selectedPatientId: {{ Auth::user()->id }},
    patients: @js($dependents->prepend(Auth::user())->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->full_name,
            'age' => $p->age,
            'gender' => strtolower($p->gender),
            'is_child' => $p->age <= 12,
            'is_infant' => $p->age <= 5,
            'is_female' => strtolower($p->gender) === 'female',
            'is_reproductive_age' => strtolower($p->gender) === 'female' && $p->age >= 12 && $p->age <= 55
        ];
    })),
    get selectedPatient() {
        return this.patients.find(p => p.id == this.selectedPatientId);
    },
    isSlotVisible(service) {
        const p = this.selectedPatient;
        if (!p) return false;
        
        const s = service.toLowerCase();
        
        // Immunization logic: Children/Infants primarily
        if (s.includes('immunization')) {
            return p.is_child || p.is_infant;
        }
        
        // Prenatal logic: Females of reproductive age (12-55)
        if (s.includes('prenatal') || s.includes('pre-natal')) {
            return p.is_female && p.is_reproductive_age;
        }
        
        // Consultation & others: Everyone
        return true;
    }
}">

    {{-- Breadcrumbs & Top Meta --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-[0.2em]">
                <li class="inline-flex items-center">
                    <a href="{{ route('patient.dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center gap-2">
                        <i class="bi bi-house-door"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center text-gray-400">
                        <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                        <span class="text-brand-600">Appointments</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex items-center gap-4 bg-white px-5 py-2.5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Active Visits</p>
                <p class="text-xs font-black text-gray-900 leading-tight">{{ $appointments->whereIn('status', ['pending', 'approved', 'rescheduled'])->count() }} Scheduled</p>
            </div>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="bg-white rounded-[3.5rem] p-10 lg:p-14 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-50 rounded-full blur-3xl -mr-48 -mt-48 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-12">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-2xl bg-brand-50 text-brand-600 border border-brand-100 mb-6">
                    <i class="bi bi-calendar2-heart text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Schedule Management</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tight leading-[1.1]">
                    Your <span class="text-brand-600 underline decoration-brand-200 decoration-8 underline-offset-4">Appointments</span>
                </h1>
                <p class="text-gray-500 font-medium mt-6 text-lg leading-relaxed">
                    Manage your upcoming clinic visits, view medical consultation history, and book new slots with our medical team.
                </p>
            </div>
            
            <div class="flex flex-col gap-4 w-full lg:w-auto">
                <button @click="document.getElementById('available-slots-section').scrollIntoView({ behavior: 'smooth' })" 
                        class="px-10 py-5 bg-brand-600 text-white rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all active:scale-95 flex items-center justify-center gap-3">
                    Book New Visit <i class="bi bi-plus-circle-fill"></i>
                </button>
            </div>
        </div>
    </div>

    @include('components.status-legend')
    
    <!-- Mobile Card View -->
    <div class="md:hidden space-y-6">
        <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] px-4 mb-4">Your Schedule</h2>
        @forelse($appointments as $appointment)
            @php $slot = $appointment->slot; @endphp
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden border border-gray-100 group transition-all duration-500 hover:shadow-xl hover:border-brand-100 mx-4">
                {{-- Header: Time & Status --}}
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3 text-gray-500 text-[10px] font-black uppercase tracking-widest bg-gray-50 px-4 py-2 rounded-2xl border border-gray-100 shadow-sm">
                        <i class="bi bi-calendar3 text-brand-400"></i>
                        {{ $appointment->formatted_date ?? $slot?->date?->format('M d, Y') ?? 'N/A' }}
                    </div>
                    @php
                        $status = $appointment->status;
                        // 💉 Fix: Slot date should always take precedence for status check, matching display logic
                        $apptDate = ($slot ? \Illuminate\Support\Carbon::parse($slot->date) : null) ?? $appointment->scheduled_at;
                        
                        $today = \Illuminate\Support\Carbon::today();
                        $apptDateOnly = $apptDate ? $apptDate->copy()->startOfDay() : null;

                        $isToday = $apptDateOnly ? $apptDateOnly->equalTo($today) : false;
                        $isFuture = $apptDateOnly ? $apptDateOnly->greaterThan($today) : false;
                        $isPast = $apptDateOnly ? $apptDateOnly->lessThan($today) : false;

                        $label = ucfirst($status);
                        $statusClass = 'bg-gray-50 text-gray-500 border-gray-100';

                        // 1. Future Logic (Strict check)
                        if ($isFuture && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                            $label = 'Upcoming';
                            $statusClass = 'bg-brand-50 text-brand-700 border-brand-100';
                        } 
                        // 2. Today Logic
                        elseif ($isToday && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                            if ($appointment->healthRecord && $appointment->healthRecord->vital_signs) {
                                $label = "Ready for Provider";
                                $statusClass = 'bg-blue-50 text-blue-700 border-blue-100';
                            } else {
                                $label = 'Confirmed (Wait for Vitals)';
                                $statusClass = 'bg-indigo-50 text-indigo-700 border-indigo-100';
                            }
                        }
                        // 3. Past Logic (No Show)
                        elseif ($isPast && in_array($status, ['approved', 'rescheduled', 'pending'])) {
                            $label = 'No Show';
                            $statusClass = 'bg-orange-50 text-orange-700 border-orange-100';
                        }
                        // 4. Final States
                        elseif ($status === 'completed') {
                            $label = 'Finished';
                            $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                        } elseif ($status === 'rejected') {
                            $statusClass = 'bg-red-50 text-red-700 border-red-100';
                        } elseif ($status === 'cancelled') {
                            $statusClass = 'bg-gray-50 text-gray-500 border-gray-100';
                        }
                    @endphp
                    <span class="px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] border {{ $statusClass }}">
                        {{ $label }}
                    </span>
                </div>

                {{-- Body: Service --}}
                <div class="mb-8">
                    <div class="flex items-center gap-5 mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 text-xl font-bold shrink-0 shadow-inner border border-brand-100">
                            <i class="bi bi-bandaid-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 leading-tight group-hover:text-brand-600 transition">{{ $slot?->service ?? $appointment->service ?? 'N/A' }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">
                                    @if($slot?->doctor)
                                        @if($slot->doctor->isDoctor())
                                            Dr. {{ $slot->doctor->last_name }}
                                        @elseif($slot->doctor->isMidwife())
                                            Midwife {{ $slot->doctor->first_name }}
                                        @else
                                            {{ $slot->doctor->full_name }}
                                        @endif
                                    @else
                                        Medical Team
                                    @endif
                                </p>
                                <span class="text-gray-300 text-[8px]">•</span>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ $appointment->user->id === Auth::user()->id ? 'Myself' : $appointment->user->full_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-700 bg-gray-50/50 p-4 rounded-2xl border border-gray-100 group-hover:bg-white transition-colors">
                        <i class="bi bi-clock text-brand-500"></i>
                        <span class="font-bold text-gray-900">
                            @if($slot?->start_time)
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} -
                                {{ $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A') : '' }}
                            @else
                                {{ $appointment->formatted_time ?? 'N/A' }}
                            @endif
                        </span>
                        @if($appointment->type === 'walk-in')
                            <span class="ml-auto text-[9px] bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-black uppercase tracking-widest border border-purple-200">Walk-In</span>
                        @endif
                    </div>
                </div>

                {{-- Footer: Actions --}}
                <div class="flex items-center justify-end pt-6 border-t border-gray-50 gap-3">
                    @if(!in_array($appointment->status, ['cancelled', 'rejected', 'completed']))
                        <form action="{{ route('patient.appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100">
                                <i class="bi bi-x-circle"></i> Cancel Appointment
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[3rem] p-12 text-center shadow-sm border border-gray-100 mx-4">
                <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-gray-300 border border-gray-100">
                    <i class="bi bi-calendar-x text-4xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">No Appointments Found</h3>
                <p class="text-gray-400 font-medium max-w-sm mx-auto leading-relaxed">Your history is empty. Book a new appointment to start your health tracking.</p>
            </div>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-[3.5rem] shadow-sm border border-gray-100 overflow-hidden relative group/table">
        <div class="px-12 py-10 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                    <i class="bi bi-clock-history"></i>
                </div>
                Appointment History
            </h2>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Viewing All Records</span>
            </div>
        </div>

        @if($appointments->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/30">
                        <tr>
                            <th scope="col" class="px-12 py-8 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Service Details</th>
                            <th scope="col" class="px-12 py-8 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Schedule</th>
                            <th scope="col" class="px-12 py-8 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Status</th>
                            <th scope="col" class="px-12 py-8 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @foreach($appointments as $appointment)
                            @php $slot = $appointment->slot; @endphp
                            <tr class="hover:bg-gray-50/80 transition-all duration-300 group/row">
                                <td class="px-12 py-10 whitespace-nowrap">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 text-xl shadow-inner group-hover/row:scale-110 transition-transform">
                                            <i class="bi bi-bandaid-fill"></i>
                                        </div>
                                        <div>
                                            <div class="text-lg font-black text-gray-900 tracking-tight">{{ $slot?->service ?? $appointment->service ?? 'N/A' }}</div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="text-[10px] font-black text-blue-500 uppercase tracking-widest">
                                                    @if($slot?->doctor)
                                                        @if($slot->doctor->isDoctor())
                                                            Dr. {{ $slot->doctor->last_name }}
                                                        @elseif($slot->doctor->isMidwife())
                                                            Midwife {{ $slot->doctor->first_name }}
                                                        @else
                                                            {{ $slot->doctor->full_name }}
                                                        @endif
                                                    @else
                                                        Medical Team
                                                    @endif
                                                </div>
                                                <span class="text-gray-300 text-[8px]">•</span>
                                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                                    <i class="bi bi-person-fill"></i>
                                                    {{ $appointment->user->id === Auth::user()->id ? 'Myself' : $appointment->user->full_name }}
                                                </div>
                                            </div>
                                            @if($appointment->type === 'walk-in')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black bg-purple-50 text-purple-600 border border-purple-100 uppercase tracking-widest mt-2">
                                                    Walk-In
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-12 py-10 whitespace-nowrap">
                                    <div class="text-base font-black text-gray-900 tracking-tight">{{ $appointment->formatted_date ?? $slot?->date?->format('M d, Y') ?? 'N/A' }}</div>
                                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mt-1 flex items-center gap-2">
                                        <i class="bi bi-clock text-brand-400"></i>
                                        @if($slot?->start_time)
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} -
                                            {{ $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A') : '' }}
                                        @else
                                            {{ $appointment->formatted_time ?? 'N/A' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-12 py-10 whitespace-nowrap">
                                    @php
                                    $status = $appointment->status;
                                    // 💉 Fix: Slot date should always take precedence for status check, matching display logic
                                    $apptDate = ($slot ? \Illuminate\Support\Carbon::parse($slot->date) : null) ?? $appointment->scheduled_at;
                                    
                                    $today = \Illuminate\Support\Carbon::today();
                                    $apptDateOnly = $apptDate ? $apptDate->copy()->startOfDay() : null;

                                    $isToday = $apptDateOnly ? $apptDateOnly->equalTo($today) : false;
                                    $isFuture = $apptDateOnly ? $apptDateOnly->greaterThan($today) : false;
                                    $isPast = $apptDateOnly ? $apptDateOnly->lessThan($today) : false;

                                    $label = ucfirst($status);
                                    $colorClass = 'bg-gray-100 text-gray-800';

                                    // 1. Future Logic (Strict check)
                                    if ($isFuture && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                                        $label = 'Upcoming';
                                        $colorClass = 'bg-brand-50 text-brand-700 border-brand-100';
                                    } 
                                    // 2. Today Logic
                                    elseif ($isToday && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                                        if ($appointment->healthRecord && $appointment->healthRecord->vital_signs) {
                                            $label = "Ready for Provider";
                                            $colorClass = 'bg-blue-50 text-blue-700 border-blue-100';
                                        } else {
                                            $label = 'Confirmed (Wait for Vitals)';
                                            $colorClass = 'bg-indigo-50 text-indigo-700 border-indigo-100';
                                        }
                                    }
                                    // 3. Past Logic (No Show)
                                    elseif ($isPast && in_array($status, ['approved', 'rescheduled', 'pending'])) {
                                        $label = 'No Show';
                                        $colorClass = 'bg-orange-50 text-orange-700 border-orange-100';
                                    }
                                    // 4. Final States
                                    elseif ($status === 'completed') {
                                        $label = 'Finished';
                                        $colorClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                    } elseif ($status === 'rejected') {
                                        $colorClass = 'bg-red-50 text-red-700 border-red-100';
                                    } elseif ($status === 'cancelled') {
                                        $colorClass = 'bg-gray-50 text-gray-500 border-gray-100';
                                    }
                                    @endphp
                                    <span class="inline-flex px-5 py-2.5 rounded-[1.25rem] text-[10px] font-black uppercase tracking-[0.2em] border {{ $colorClass }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-12 py-10 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        @if(!in_array($appointment->status, ['cancelled', 'rejected', 'completed']))
                                            <form action="{{ route('patient.appointments.cancel', $appointment->id) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-8 py-4 bg-red-50 text-red-600 rounded-[1.25rem] text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-600 hover:text-white transition-all border border-red-100 shadow-sm active:scale-95">
                                                    Cancel Visit
                                                </button>
                                            </form>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 border border-gray-100 mx-auto">
                                                <i class="bi bi-lock-fill"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-32 text-gray-500">
                <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center text-gray-200 mb-8 border border-gray-50 shadow-inner">
                    <i class="bi bi-calendar-x text-5xl"></i>
                </div>
                <p class="text-2xl font-black text-gray-900 tracking-tight">No appointment records found</p>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-3">Book a slot below to begin your health journey.</p>
            </div>
        @endif
    </div>

    {{-- ===========================
         AVAILABLE SLOTS
    ============================ --}}
    <div id="available-slots-section" class="pt-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 px-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                        <i class="bi bi-calendar-plus-fill"></i>
                    </div>
                    Available Slots
                </h2>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-4 ml-1">Choose an open consultation slot to book your visit</p>
            </div>
            
            <div class="flex flex-col gap-4">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Who is this appointment for?</label>
                <div class="relative group">
                    <select x-model="selectedPatientId" 
                            class="pl-12 pr-10 py-4 bg-white border border-gray-200 rounded-2xl text-xs font-black uppercase tracking-widest focus:ring-4 focus:ring-brand-500/10 transition-all w-72 shadow-sm appearance-none cursor-pointer">
                        <template x-for="p in patients" :key="p.id">
                            <option :value="p.id" x-text="p.id == {{ Auth::user()->id }} ? 'Myself (' + p.name + ')' : p.name"></option>
                        </template>
                    </select>
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-600 group-hover:scale-110 transition-transform">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </div>
            </div>
        </div>

        @if($availableSlots->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($availableSlots as $slot)
                    @php $slotAvailable = $slot->isBookable(); @endphp
                    <div x-show="isSlotVisible('{{ $slot->service }}')" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="bg-white shadow-sm rounded-[3.5rem] p-10 border border-gray-100 hover:border-brand-200 hover:shadow-2xl transition-all duration-500 flex flex-col h-full group transform hover:-translate-y-3">
                        <div class="flex justify-between items-start mb-8">
                            <div class="bg-brand-50 text-brand-700 rounded-2xl px-5 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] border border-brand-100">
                                {{ $slot->service }}
                            </div>
                            <div class="flex items-center gap-2.5 px-4 py-1.5 rounded-full {{ $slotAvailable ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-500 border border-red-100' }}">
                                <span class="relative flex h-2 w-2">
                                    <span class="{{ $slotAvailable ? 'bg-emerald-500' : 'bg-red-500' }} absolute inline-flex h-full w-full rounded-full opacity-75 {{ $slotAvailable ? 'animate-ping' : '' }}"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $slotAvailable ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                </span>
                                <span class="text-[9px] font-black uppercase tracking-widest">{{ $slotAvailable ? 'Open' : 'Full' }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-6 mb-10 flex-1">
                            <div class="flex items-center gap-5 text-gray-700">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-all border border-gray-100/50 shadow-inner">
                                    <i class="bi bi-calendar-event text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Schedule Date</p>
                                    <p class="font-black text-gray-900 text-base tracking-tight">{{ $slot->date?->format('F d, Y') ?? 'N/A' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-5 text-gray-700">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-all border border-gray-100/50 shadow-inner">
                                    <i class="bi bi-person-badge text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Medical Provider</p>
                                    <p class="font-black text-gray-900 text-base tracking-tight">
                                        @if($slot->doctor)
                                            @if($slot->doctor->isDoctor())
                                                Dr. {{ $slot->doctor->last_name }}
                                            @elseif($slot->doctor->isMidwife())
                                                Midwife {{ $slot->doctor->first_name }}
                                            @else
                                                {{ $slot->doctor->full_name }}
                                            @endif
                                        @else
                                            Medical Team
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-5 text-gray-700">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-all border border-gray-100/50 shadow-inner">
                                    <i class="bi bi-clock text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Time Slot</p>
                                    <p class="font-black text-gray-900 text-base tracking-tight">
                                        @if($slot->start_time)
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A') : '' }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-gray-50 mb-8 flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Available Spots</span>
                            <span class="text-sm font-black text-gray-900 bg-gray-50 px-4 py-1.5 rounded-xl border border-gray-100">{{ $slot->available_spots ?? 0 }} Left</span>
                        </div>

                        <button
                            @click="openModal = true; selectedSlot = {
                                id: {{ $slot->id }},
                                service: '{{ $slot->service }}',
                                date: '{{ $slot->date?->format('M d, Y') }}',
                                time: '{{ ($slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('g:i A') : 'N/A') . ' - ' . ($slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A') : '') }}',
                                provider: '{{ $slot->doctor ? ($slot->doctor->isDoctor() ? 'Dr. ' . $slot->doctor->last_name : 'Midwife ' . $slot->doctor->first_name) : 'Medical Team' }}'
                            }"
                            class="w-full py-5 rounded-[1.75rem] text-xs font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-3 shadow-lg group/btn"
                            :class="{
                                'bg-brand-600 hover:bg-brand-700 text-white shadow-brand-500/20 hover:shadow-brand-500/40 transform active:scale-95': {{ $slotAvailable ? 'true' : 'false' }},
                                'bg-gray-100 text-gray-400 cursor-not-allowed opacity-60': {{ $slotAvailable ? 'false' : 'true' }}
                            }"
                            {{ $slotAvailable ? '' : 'disabled' }}>
                            @if($slotAvailable)
                                Book Visit <i class="bi bi-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                            @else
                                Currently Full
                            @endif
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Dynamic Empty State for Filtered Slots --}}
            <div x-show="!document.querySelector('.grid > div[style*=\'display: block\']') && !document.querySelector('.grid > div:not([style*=\'display: none\'])')" 
                 class="bg-white rounded-[4rem] border-2 border-dashed border-gray-100 p-20 text-center shadow-sm"
                 style="display: none;">
                <div class="w-20 h-20 bg-brand-50 rounded-3xl flex items-center justify-center text-brand-300 mx-auto mb-6">
                    <i class="bi bi-person-x text-4xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">No Matching Slots</h3>
                <p class="text-gray-400 font-medium max-w-sm mx-auto">There are no slots available for the selected patient's profile (Age/Service requirements).</p>
            </div>
        @else
            <div class="bg-white rounded-[5rem] border-2 border-dashed border-gray-100 p-24 text-center shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-80 h-80 bg-gray-50 rounded-full blur-3xl -mr-40 -mt-40"></div>
                <div class="relative z-10">
                    <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center text-gray-300 mx-auto mb-8 border border-gray-100 shadow-inner transform -rotate-6">
                        <i class="bi bi-calendar-x text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">No Available Slots</h3>
                    <p class="text-gray-400 font-medium max-w-sm mx-auto leading-relaxed text-lg">Check back later for new schedules or contact the health center for urgent concerns.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- ===========================
         BOOK CONFIRMATION MODAL
    ============================ --}}
    <div
        x-show="openModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/70 backdrop-blur-md flex items-center justify-center z-50 px-4"
        style="display: none;"
    >
        <div @click.away="openModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative border border-gray-100 scrollbar-hide">
            
            {{-- Decorative Header Background --}}
            <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-brand-600 to-brand-500">
                <div class="absolute inset-0 opacity-10">
                    <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                    </svg>
                </div>
            </div>

            <div class="relative pt-8 px-6 pb-8">
                {{-- Icon --}}
                <div class="w-16 h-16 bg-white rounded-[1.5rem] shadow-xl flex items-center justify-center mx-auto mb-4 transform -rotate-3 ring-4 ring-brand-500/10 border border-brand-50">
                    <i class="bi bi-calendar-check-fill text-3xl text-brand-600"></i>
                </div>
                
                <div class="text-center mb-6">
                    <h2 class="text-xl font-black text-gray-900 mb-1 tracking-tight">Confirm Booking</h2>
                    <p class="text-xs text-gray-500 font-medium">Review appointment details below</p>
                </div>

                <template x-if="selectedSlot">
                    <div class="bg-gray-50 rounded-[1.5rem] p-5 border border-gray-100 shadow-inner mb-6 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-brand-500 shadow-sm shrink-0">
                                <i class="bi bi-person-fill text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Patient</p>
                                <p class="text-gray-900 font-black text-sm tracking-tight leading-none" x-text="selectedPatient.name"></p>
                            </div>
                        </div>

                        <div class="h-px bg-gray-200/60 mx-2"></div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-brand-500 shadow-sm shrink-0">
                                <i class="bi bi-bandaid-fill text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Service</p>
                                <p class="text-gray-900 font-black text-sm tracking-tight leading-none" x-text="selectedSlot.service"></p>
                            </div>
                        </div>

                        <div class="h-px bg-gray-200/60 mx-2"></div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-indigo-500 shadow-sm shrink-0">
                                <i class="bi bi-person-badge-fill text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Provider</p>
                                <p class="text-gray-900 font-black text-sm tracking-tight leading-none" x-text="selectedSlot.provider"></p>
                            </div>
                        </div>

                        <div class="h-px bg-gray-200/60 mx-2"></div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-purple-500 shadow-sm shrink-0">
                                <i class="bi bi-calendar-event-fill text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Date</p>
                                <p class="text-gray-900 font-black text-sm tracking-tight leading-none" x-text="selectedSlot.date"></p>
                            </div>
                        </div>

                        <div class="h-px bg-gray-200/60 mx-2"></div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-orange-500 shadow-sm shrink-0">
                                <i class="bi bi-clock-fill text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Time</p>
                                <p class="text-gray-900 font-black text-sm tracking-tight leading-none" x-text="selectedSlot.time"></p>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        @click="openModal = false"
                        class="px-4 py-3 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 rounded-xl text-gray-700 font-black text-[10px] uppercase tracking-widest transition shadow-sm active:scale-95">
                        Cancel
                    </button>

                    <form :action="`{{ url('/patient/appointments/book') }}/${selectedSlot.id}`" method="POST"
                          @submit.prevent="isSubmitting = true; $el.submit(); openModal = false;">
                        @csrf
                        <input type="hidden" name="patient_id" :value="selectedPatientId">
                        <button type="submit"
                            :disabled="isSubmitting"
                            class="w-full px-4 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition shadow-lg shadow-brand-500/30 flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed active:scale-95">
                            <template x-if="!isSubmitting">
                                <span class="flex items-center gap-2">
                                    Confirm <i class="bi bi-check-circle-fill"></i>
                                </span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 018 8h-4l3 3 3-3h-4a8 8 0 01-8 8v-4l-3 3 3 3v-4a8 8 0 01-8-8z"/>
                                    </svg>
                                    Booking...
                                </span>
                            </template>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
