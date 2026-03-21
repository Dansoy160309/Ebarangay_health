{{-- resources/views/patient/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Patient Dashboard')

@section('content')
<div x-data="{
    openModal: false,
    selectedSlot: null,
    isSubmitting: false,
    rescheduleOpen: false,
    appointmentId: null,
    patientId: '{{ auth()->id() }}',
    family: {{ Js::from($family) }},
    appointments: {{ Js::from($appointments) }},
    currentTime: '',
    currentDate: '',
    availabilities: {{ Js::from($doctorAvailabilities) }},
    calendarMonth: new Date().getMonth(),
    calendarYear: new Date().getFullYear(),
    calendarDays: [],
    showCalendarModal: false,
    initClock() {
        const update = () => {
            const now = new Date();
            this.currentDate = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            this.currentTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        };
        update();
        setInterval(update, 1000);
    },
    isSlotVisible(service) {
        const p = this.family.find(f => f.id == this.patientId);
        if (!p) return true;
        
        const s = service.toLowerCase();
        if (!p.dob) return !s.includes('immunization') && !s.includes('prenatal') && !s.includes('pre-natal');
        
        const dob = new Date(p.dob);
        const age = (new Date() - dob) / (1000 * 60 * 60 * 24 * 365.25);
        
        if (s.includes('immunization')) return age <= 12;
        if (s.includes('prenatal') || s.includes('pre-natal')) {
            const isFemale = p.gender && p.gender.toLowerCase() === 'female';
            return isFemale && age >= 12 && age <= 55;
        }
        
        return true;
    },
    hasExistingAppointment(service) {
        return this.appointments.some(appt => 
            appt.user_id == this.patientId && 
            appt.service.toLowerCase().includes(service.toLowerCase()) &&
            !['cancelled', 'rejected', 'completed'].includes(appt.status)
        );
    },
    updateCalendar() {
        const daysInMonth = new Date(this.calendarYear, this.calendarMonth + 1, 0).getDate();
        const firstDay = new Date(this.calendarYear, this.calendarMonth, 1).getDay();
        const days = [];
        
        // Padding for previous month
        for (let i = 0; i < firstDay; i++) {
            days.push({ day: '', date: null });
        }
        
        for (let i = 1; i <= daysInMonth; i++) {
            const dateStr = `${this.calendarYear}-${String(this.calendarMonth + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            days.push({ 
                day: i, 
                date: dateStr,
                hasAvailability: !!this.availabilities[dateStr]
            });
        }
        this.calendarDays = days;
    },
    changeMonth(delta) {
        this.calendarMonth += delta;
        if (this.calendarMonth < 0) {
            this.calendarMonth = 11;
            this.calendarYear--;
        } else if (this.calendarMonth > 11) {
            this.calendarMonth = 0;
            this.calendarYear++;
        }
        this.updateCalendar();
    },
    getMonthName() {
        return new Date(this.calendarYear, this.calendarMonth).toLocaleString('default', { month: 'long' });
    },
    selectCalendarDate(dateStr) {
        if (!this.availabilities[dateStr]) return;
        this.showCalendarModal = false;
        // In a real SPA we would filter, for now we can just highlight or show a toast
        // but since the requirement is visual, we will just close and maybe scroll.
        const el = document.getElementById('available-slots-section');
        if (el) el.scrollIntoView({ behavior: 'smooth' });
    }
}" x-init="initClock(); updateCalendar()" class="flex flex-col gap-8" x-cloak>

    {{-- 1. Hero Welcome Banner --}}
    <div class="bg-gradient-to-br from-brand-600 via-brand-500 to-indigo-600 rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-8 text-white shadow-xl shadow-brand-500/20 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <h1 class="text-2xl sm:text-4xl font-black mb-2 tracking-tight leading-tight">
                    Good Day, {{ auth()->user()->first_name }}! 👋
                </h1>
                <p class="text-brand-50 text-xs sm:text-sm font-medium opacity-90 mb-0">
                    Your health journey starts here. Manage appointments and view your records with ease.
                </p>
            </div>
            
            <div class="flex flex-row items-center gap-2 sm:gap-3 shrink-0">
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 sm:px-4 py-2 rounded-xl sm:rounded-2xl border border-white/10 shadow-sm w-fit">
                    <i class="bi bi-calendar3 text-brand-200 text-xs"></i>
                    <span x-text="currentDate" class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest">{{ now()->format('l, F j, Y') }}</span>
                </div>
                
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 sm:px-4 py-2 rounded-xl sm:rounded-2xl border border-white/10 shadow-sm w-fit">
                    <i class="bi bi-clock text-brand-200 text-xs"></i>
                    <span x-text="currentTime" class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Background Icon -->
        <div class="absolute -right-6 -bottom-6 sm:-right-10 sm:-bottom-10 opacity-10 pointer-events-none">
            <i class="bi bi-heart-pulse-fill text-[8rem] sm:text-[12rem]"></i>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="animate-fade-in-up">
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                <i class="bi bi-check-circle-fill text-xl"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="animate-fade-in-up">
            <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                <i class="bi bi-exclamation-circle-fill text-xl"></i>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Doctor Presence Tracking (For Today's Appointments) --}}
    @php
        $todaysDoctorAppointments = auth()->user()->appointments()
            ->whereDate('scheduled_at', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('slot', fn($q) => $q->whereNotNull('doctor_id'))
            ->with(['slot.doctor.availabilities' => fn($q) => $q->where('date', now()->toDateString())])
            ->get();
    @endphp

    @if($todaysDoctorAppointments->isNotEmpty())
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600">
                    <i class="bi bi-person-pulse-fill"></i>
                </div>
                <h2 class="text-sm font-black text-gray-900 tracking-tight uppercase">Doctor Status for Today</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($todaysDoctorAppointments as $appointment)
                    @php
                        $availability = $appointment->slot->doctor->availabilities->first();
                        $status = $availability?->status ?? 'scheduled';
                        $statusConfig = [
                            'scheduled' => ['label' => 'Waiting for Arrival', 'icon' => 'bi-clock', 'class' => 'bg-gray-50 text-gray-600 border-gray-100'],
                            'arrived' => ['label' => 'Doctor is Present', 'icon' => 'bi-check-circle-fill', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                            'delayed' => ['label' => 'Running Late', 'icon' => 'bi-exclamation-circle-fill', 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
                            'absent' => ['label' => 'Unavailable Today', 'icon' => 'bi-x-circle-fill', 'class' => 'bg-red-50 text-red-600 border-red-100'],
                        ][$status];
                    @endphp
                    <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg font-black">
                                {{ substr($appointment->slot->doctor->full_name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-sm">Dr. {{ $appointment->slot->doctor->full_name }}</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $appointment->service }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border {{ $statusConfig['class'] }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                                <i class="bi {{ $statusConfig['icon'] }}"></i>
                                {{ $statusConfig['label'] }}
                            </span>
                            @if($availability?->notes && $status === 'delayed')
                                <span class="text-[8px] font-bold text-amber-500 uppercase tracking-tighter">
                                    Note: {{ Str::limit($availability->notes, 30) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 2. Latest Health Advisories --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                    <i class="bi bi-megaphone-fill text-xl"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Latest Health Advisories</h2>
            </div>
            <a href="{{ route('patient.announcements.index') }}" class="text-xs font-black text-brand-600 hover:text-brand-700 uppercase tracking-widest flex items-center gap-1 group">
                View All <i class="bi bi-chevron-right group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($activeAnnouncements->take(3) as $announcement)
                <a href="{{ route('patient.announcements.show', $announcement) }}" class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all group relative block">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-brand-50 text-brand-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-brand-100">
                            {{ $announcement->type ?? 'Official' }}
                        </span>
                        @if($announcement->created_at->diffInDays() < 3)
                            <span class="px-2 py-0.5 bg-red-50 text-red-500 text-[8px] font-black uppercase tracking-widest rounded-md border border-red-100">
                                New
                            </span>
                        @endif
                    </div>
                    <h3 class="font-black text-gray-900 mb-2 line-clamp-1 group-hover:text-brand-600 transition-colors text-sm">
                        {{ $announcement->title }}
                    </h3>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-4 leading-relaxed">
                        {{ $announcement->message ?? $announcement->content }}
                    </p>
                    <div class="flex items-center gap-2 text-[10px] text-gray-400 font-bold mt-auto">
                        <i class="bi bi-calendar3"></i>
                        {{ $announcement->created_at->format('M d, Y') }}
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white rounded-[2rem] p-10 text-center border border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No active advisories at this time</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 3. Stats & Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Mini Calendar Card --}}
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm flex flex-col hover:border-brand-100 transition-all cursor-pointer group"
             @click="showCalendarModal = true">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600">
                        <i class="bi bi-calendar-range-fill text-sm"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Clinical Schedule</span>
                </div>
                <i class="bi bi-arrows-angle-expand text-gray-300 group-hover:text-brand-500 transition-colors text-xs"></i>
            </div>
            
            <div class="flex-1 flex flex-col justify-center">
                <h3 class="text-xl font-black text-gray-900 mb-1" x-text="getMonthName()"></h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tap to view doctor availability</p>
                
                {{-- Tiny preview dots for current week --}}
                <div class="flex gap-1.5 mt-4">
                    <template x-for="i in 7">
                        <div class="w-1.5 h-1.5 rounded-full" 
                             :class="calendarDays.slice(0, 7)[i-1]?.hasAvailability ? 'bg-brand-500' : 'bg-gray-100'"></div>
                    </template>
                </div>
            </div>
        </div>

        <a href="{{ route('patient.appointments.index') }}" class="bg-white rounded-[1.5rem] sm:rounded-[2rem] p-6 sm:p-8 border border-gray-100 shadow-sm flex items-center justify-between group hover:border-brand-100 transition-all transform hover:-translate-y-1">
            <div>
                <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 sm:mb-2">Total Appointments</p>
                <h3 class="text-2xl sm:text-4xl font-black text-gray-900 mb-2 sm:mb-4">{{ $totalAppointments }}</h3>
                <span class="text-[10px] sm:text-xs font-black text-brand-600 group-hover:text-brand-700 uppercase tracking-widest flex items-center gap-1.5">
                    View History <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </span>
            </div>
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-brand-50 flex items-center justify-center text-brand-200 group-hover:text-brand-500 transition-colors">
                <i class="bi bi-calendar-check text-2xl sm:text-4xl"></i>
            </div>
        </a>

        <a href="{{ route('patient.health-records.index') }}" class="bg-white rounded-[1.5rem] sm:rounded-[2rem] p-6 sm:p-8 border border-gray-100 shadow-sm flex items-center justify-between group hover:border-brand-100 transition-all transform hover:-translate-y-1">
            <div>
                <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 sm:mb-2">My Health Records</p>
                <h3 class="text-2xl sm:text-4xl font-black text-gray-900 mb-2 sm:mb-4">{{ auth()->user()->healthRecords->count() }}</h3>
                <span class="text-[10px] sm:text-xs font-black text-brand-600 group-hover:text-brand-700 uppercase tracking-widest flex items-center gap-1.5">
                    View Records <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </span>
            </div>
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-brand-50 flex items-center justify-center text-brand-200 group-hover:text-brand-500 transition-colors">
                <i class="bi bi-file-earmark-medical text-2xl sm:text-4xl"></i>
            </div>
        </a>
    </div>

    {{-- 4. Available Slots --}}
    <div id="available-slots-section" class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                    <i class="bi bi-calendar-plus-fill text-xl"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Available Slots</h2>
            </div>
            
            <div class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-gray-100 shadow-sm">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">For:</span>
                <select x-model="patientId" class="border-0 bg-transparent text-xs font-black uppercase tracking-widest focus:ring-0 cursor-pointer text-brand-600">
                    <template x-for="p in family" :key="p.id">
                        <option :value="p.id" x-text="p.name"></option>
                    </template>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5">
            @forelse($availableSlots as $slot)
                @php $slotAvailable = $slot->isBookable(); @endphp
                <div x-show="isSlotVisible('{{ $slot->service }}')" 
                    class="bg-white shadow-sm rounded-[1.5rem] sm:rounded-[2rem] p-5 md:p-6 border border-gray-100 hover:border-brand-200 hover:shadow-xl transition-all duration-500 flex flex-col h-full group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-brand-50 text-brand-700 rounded-lg px-2.5 py-1 sm:px-3 sm:py-1.5 text-[8px] sm:text-[9px] font-black uppercase tracking-[0.2em] border border-brand-100">
                            {{ $slot->service }}
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full {{ $slotAvailable ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-500 border border-red-100' }}">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="{{ $slotAvailable ? 'bg-emerald-500' : 'bg-red-500' }} absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $slotAvailable ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                            </span>
                            <span class="text-[7px] sm:text-[8px] font-black uppercase tracking-widest">{{ $slotAvailable ? 'Available' : 'Full' }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6 flex-1">
                        <div class="flex items-center gap-2 sm:gap-3 text-gray-700">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-brand-500 transition-colors">
                                <i class="bi bi-calendar-event text-xs sm:text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest">Date</p>
                                <p class="font-black text-gray-900 text-[10px] sm:text-xs">{{ $slot->date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 sm:gap-3 text-gray-700">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-brand-500 transition-colors">
                                <i class="bi bi-person-badge text-xs sm:text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest">Provider</p>
                                <p class="font-black text-gray-900 text-[10px] sm:text-xs truncate max-w-[100px] sm:max-w-[120px]">
                                    @if($slot->doctor)
                                        @if($slot->doctor->isDoctor())
                                            Dr. {{ $slot->doctor->last_name }}
                                        @elseif($slot->doctor->isMidwife())
                                            Midwife {{ $slot->doctor->first_name }}
                                        @else
                                            {{ $slot->doctor->full_name }}
                                        @endif
                                    @else
                                        Health Center
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3 text-gray-700">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-brand-500 transition-colors">
                                <i class="bi bi-clock text-xs sm:text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest">Time</p>
                                <p class="font-black text-gray-900 text-[10px] sm:text-xs">{{ $slot->displayTime() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-50 mt-auto flex items-center justify-between mb-4">
                        <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest">Remaining</span>
                        <span class="text-[10px] sm:text-xs font-black text-gray-900 bg-gray-50 px-2 py-0.5 rounded-lg border border-gray-100">{{ $slot->available }} Spots</span>
                    </div>

                    <button type="button"
                        x-show="!hasExistingAppointment('{{ $slot->service }}')"
                        @click="openModal = true; selectedSlot = {
                            id: {{ $slot->id }},
                            service: '{{ $slot->service }}',
                            date: '{{ $slot->date->format('M d, Y') }}',
                            time: '{{ $slot->displayTime() }}',
                            provider: '{{ $slot->doctor ? ($slot->doctor->isDoctor() ? 'Dr. '.$slot->doctor->last_name : ($slot->doctor->isMidwife() ? 'Midwife '.$slot->doctor->first_name : $slot->doctor->full_name)) : 'Health Center' }}'
                        }"
                        :disabled="!{{ $slotAvailable ? 'true' : 'false' }}"
                        class="w-full py-2.5 sm:py-3 rounded-xl sm:rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest transition-all shadow-lg disabled:opacity-50 disabled:shadow-none {{ $slotAvailable ? 'bg-brand-600 text-white shadow-brand-200 hover:bg-brand-700 hover:-translate-y-0.5' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                        {{ $slotAvailable ? 'Book Appointment' : 'Slot Full' }}
                    </button>

                    <div x-show="hasExistingAppointment('{{ $slot->service }}')"
                        class="w-full py-2.5 sm:py-3 rounded-xl sm:rounded-2xl bg-orange-50 text-orange-600 border border-orange-100 font-black text-[10px] sm:text-xs uppercase tracking-widest text-center shadow-sm">
                        Already Booked
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-gray-100 shadow-sm">
                    <i class="bi bi-calendar-x text-4xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-lg font-black text-gray-900 mb-2">No Available Slots</h3>
                    <p class="text-gray-400 font-medium max-w-sm mx-auto text-sm">Please check back later or contact the health center for urgent concerns.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 5. Upcoming Appointments --}}
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                <i class="bi bi-calendar-week-fill text-xl"></i>
            </div>
            <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Upcoming Visits</h2>
        </div>

        <div class="bg-white shadow-sm rounded-[2.5rem] border border-gray-100 overflow-hidden">
            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Service</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Date & Time</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                                            <i class="bi bi-bandaid-fill"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900 text-sm">{{ $appointment->slot->service ?? 'N/A' }}</p>
                                            @if($appointment->user_id !== auth()->id())
                                                <p class="text-[9px] font-bold text-brand-600 uppercase tracking-widest">Patient: {{ $appointment->user->first_name }}</p>
                                            @else
                                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Patient: Me</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-black text-gray-900">{{ $appointment->scheduled_at?->format('M d, Y') ?? '-' }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $appointment->scheduled_at?->format('h:i A') ?? '-' }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $status = $appointment->status;
                                        $apptDate = ($appointment->slot ? \Illuminate\Support\Carbon::parse($appointment->slot->date) : null) ?? $appointment->scheduled_at;
                                        $today = \Illuminate\Support\Carbon::today();
                                        $apptDateOnly = $apptDate ? $apptDate->copy()->startOfDay() : null;
                                        $isToday = $apptDateOnly ? $apptDateOnly->equalTo($today) : false;
                                        $isFuture = $apptDateOnly ? $apptDateOnly->greaterThan($today) : false;
                                        $isPast = $apptDateOnly ? $apptDateOnly->lessThan($today) : false;
                                        $label = ucfirst($status);
                                        $colorClass = 'bg-gray-50 text-gray-500 border-gray-100';
                                        if ($isFuture && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                                            $label = 'Upcoming'; $colorClass = 'bg-brand-50 text-brand-600 border-brand-100';
                                        } elseif ($isToday && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                                            if ($appointment->healthRecord && $appointment->healthRecord->vital_signs) {
                                                $label = "Ready for Provider"; $colorClass = 'bg-blue-50 text-blue-600 border-blue-100';
                                            } else {
                                                $label = 'Confirmed (Wait for Vitals)'; $colorClass = 'bg-indigo-50 text-indigo-600 border-indigo-100';
                                            }
                                        } elseif ($isPast && in_array($status, ['approved', 'rescheduled', 'pending'])) {
                                            $label = 'No Show'; $colorClass = 'bg-orange-50 text-orange-600 border-orange-100';
                                        } elseif ($status === 'completed') {
                                            $label = 'Finished'; $colorClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                        }
                                    @endphp
                                    <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $colorClass }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @if(!in_array($appointment->status, ['cancelled', 'rejected', 'completed']))
                                        <form action="{{ route('patient.appointments.cancel', $appointment->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all border border-red-100" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                Cancel
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">No Actions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                                    No upcoming visits scheduled
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden divide-y divide-gray-50">
                @forelse($appointments as $appointment)
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                                    <i class="bi bi-bandaid-fill"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 text-sm">{{ $appointment->slot->service ?? 'N/A' }}</p>
                                    <p class="text-[9px] font-bold {{ $appointment->user_id !== auth()->id() ? 'text-brand-600' : 'text-gray-400' }} uppercase tracking-widest">
                                        Patient: {{ $appointment->user_id !== auth()->id() ? $appointment->user->first_name : 'Me' }}
                                    </p>
                                </div>
                            </div>
                            @php
                                $status = $appointment->status;
                                $apptDate = ($appointment->slot ? \Illuminate\Support\Carbon::parse($appointment->slot->date) : null) ?? $appointment->scheduled_at;
                                $today = \Illuminate\Support\Carbon::today();
                                $apptDateOnly = $apptDate ? $apptDate->copy()->startOfDay() : null;
                                $isToday = $apptDateOnly ? $apptDateOnly->equalTo($today) : false;
                                $isFuture = $apptDateOnly ? $apptDateOnly->greaterThan($today) : false;
                                $isPast = $apptDateOnly ? $apptDateOnly->lessThan($today) : false;
                                $label = ucfirst($status);
                                $colorClass = 'bg-gray-50 text-gray-500 border-gray-100';
                                if ($isFuture && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                                    $label = 'Upcoming'; $colorClass = 'bg-brand-50 text-brand-600 border-brand-100';
                                } elseif ($isToday && in_array($status, ['approved', 'pending', 'rescheduled'])) {
                                    if ($appointment->healthRecord && $appointment->healthRecord->vital_signs) {
                                        $label = "Ready"; $colorClass = 'bg-blue-50 text-blue-600 border-blue-100';
                                    } else {
                                        $label = 'Confirmed'; $colorClass = 'bg-indigo-50 text-indigo-600 border-indigo-100';
                                    }
                                } elseif ($isPast && in_array($status, ['approved', 'rescheduled', 'pending'])) {
                                    $label = 'No Show'; $colorClass = 'bg-orange-50 text-orange-600 border-orange-100';
                                } elseif ($status === 'completed') {
                                    $label = 'Finished'; $colorClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                }
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $colorClass }}">
                                {{ $label }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between text-[10px]">
                            <div class="flex items-center gap-2 text-gray-500 font-bold uppercase tracking-widest">
                                <i class="bi bi-calendar3"></i>
                                {{ $appointment->scheduled_at?->format('M d, Y') ?? '-' }}
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 font-bold uppercase tracking-widest">
                                <i class="bi bi-clock"></i>
                                {{ $appointment->scheduled_at?->format('h:i A') ?? '-' }}
                            </div>
                        </div>

                        @if(!in_array($appointment->status, ['cancelled', 'rejected', 'completed']))
                            <form action="{{ route('patient.appointments.cancel', $appointment->id) }}" method="POST" class="block w-full">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all border border-red-100" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    Cancel Appointment
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                        No upcoming visits
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Expanded Calendar Modal --}}
    <div x-show="showCalendarModal" 
          class="fixed inset-0 z-[100] overflow-y-auto" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100"
          x-transition:leave-end="opacity-0"
          @keydown.escape.window="showCalendarModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 py-12 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="showCalendarModal = false"></div>

            <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[3rem] sm:align-middle relative z-10">
                <div class="absolute top-0 right-0 p-6">
                    <button @click="showCalendarModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>

                <div class="p-8 sm:p-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight" x-text="getMonthName() + ' ' + calendarYear"></h3>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Select a date with a blue dot to view available slots</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="changeMonth(-1)" class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 transition-all">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button @click="changeMonth(1)" class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 transition-all">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-7 gap-2 mb-4">
                        <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                            <div class="text-center text-[10px] font-black text-gray-400 uppercase tracking-widest py-2" x-text="day"></div>
                        </template>
                        
                        <template x-for="dayObj in calendarDays">
                            <div class="relative aspect-square flex flex-col items-center justify-center rounded-2xl transition-all border border-transparent"
                                 :class="{
                                     'hover:bg-brand-50 cursor-pointer border-gray-50': dayObj.day !== '',
                                     'bg-brand-50 border-brand-100': dayObj.hasAvailability
                                 }"
                                 @click="selectCalendarDate(dayObj.date)">
                                <span class="text-sm font-bold" :class="dayObj.hasAvailability ? 'text-brand-700' : 'text-gray-700'" x-text="dayObj.day"></span>
                                <template x-if="dayObj.hasAvailability">
                                    <div class="absolute bottom-2 w-1 h-1 rounded-full bg-brand-500"></div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="mt-8 flex items-center gap-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-brand-500"></div>
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Doctor Available</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">No Schedule</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Modal --}}
     <div x-show="openModal" 
          x-cloak
          class="fixed inset-0 z-[60] overflow-y-auto" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100"
          x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 py-12 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="openModal = false"></div>

            <div class="inline-block w-full max-w-xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[3rem] sm:align-middle relative z-10">
                <div class="absolute top-0 right-0 p-6">
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>

                <div class="p-10 sm:p-12">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 text-2xl shadow-inner border border-brand-100">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Confirm Booking</h3>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Please review your appointment details</p>
                        </div>
                    </div>

                    <div class="space-y-6 mb-10">
                        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 space-y-4">
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient</span>
                                <span class="font-black text-brand-600" x-text="family.find(f => f.id == patientId)?.name"></span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Service</span>
                                <span class="font-black text-gray-900" x-text="selectedSlot?.service"></span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</span>
                                <span class="font-black text-gray-900" x-text="selectedSlot?.date"></span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Time</span>
                                <span class="font-black text-gray-900" x-text="selectedSlot?.time"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Provider</span>
                                <span class="font-black text-gray-900" x-text="selectedSlot?.provider"></span>
                            </div>
                        </div>
                    </div>

                    <form :action="'{{ url('/patient/appointments/book') }}/' + (selectedSlot ? selectedSlot.id : '')" method="POST" @submit="isSubmitting = true">
                        @csrf
                        <input type="hidden" name="patient_id" :value="patientId">
                        <button type="submit" 
                                x-show="selectedSlot"
                                class="w-full py-5 bg-brand-600 hover:bg-brand-700 text-white rounded-[1.5rem] font-black uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 transition-all flex items-center justify-center gap-3 group disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isSubmitting">
                            <template x-if="!isSubmitting">
                                <span class="flex items-center gap-3">
                                    Confirm Appointment <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="flex items-center gap-3">
                                    <i class="bi bi-arrow-repeat animate-spin"></i> Processing...
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
