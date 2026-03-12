@extends('layouts.app')

@section('title', 'Consultation - ' . $appointment->user->full_name)

@section('content')
<div class="max-w-7xl mx-auto p-6">
    @php
        $user = auth()->user();
        $routePrefix = $user->isMidwife() ? 'midwife' : 'doctor';
    @endphp

    @php
        $today = now()->startOfDay();
        $slotPast = $appointment->slot && $appointment->slot->date ? \Carbon\Carbon::parse($appointment->slot->date)->startOfDay()->isBefore($today) : false;
        $schedPast = $appointment->scheduled_at ? $appointment->scheduled_at->copy()->startOfDay()->isBefore($today) : false;
        $isPastAppt = $slotPast || $schedPast;
        $isNoShow = $appointment->status === 'no_show' || (in_array($appointment->status, ['approved','rescheduled']) && $isPastAppt);
        $hasVitals = $appointment->healthRecord && $appointment->healthRecord->vital_signs;
    @endphp
    <div class="mb-8">
        <a href="{{ route($routePrefix . '.appointments.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors group mb-6">
            <i class="bi bi-arrow-left me-2 group-hover:-translate-x-1 transition-transform"></i>
            Back to Appointments
        </a>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 shadow-inner">
                    <i class="bi bi-person-badge text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $appointment->user->full_name }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                        <span class="text-sm text-gray-500 flex items-center gap-1.5">
                            <i class="bi bi-activity text-blue-500"></i>
                            {{ $appointment->service }}
                        </span>
                        <span class="text-sm text-gray-400">•</span>
                        <span class="text-sm text-gray-500 flex items-center gap-1.5">
                            <i class="bi bi-calendar3 text-blue-500"></i>
                            {{ $appointment->scheduled_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @php
                    if ($isNoShow) {
                        $statusLabel = 'No Show';
                        $statusClass = 'bg-orange-50 text-orange-700 ring-orange-600/20';
                    } else {
                        $statusColors = [
                            'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                            'approved' => 'bg-green-50 text-green-700 ring-green-600/20',
                            'completed' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                            'cancelled' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                            'rejected' => 'bg-red-50 text-red-700 ring-red-600/20',
                        ];
                        $statusClass = $statusColors[$appointment->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                        $statusLabel = ucfirst($appointment->status);
                    }
                @endphp
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold ring-1 ring-inset {{ $statusClass }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current me-2"></span>
                    {{ $statusLabel }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Patient Info & Vitals -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Patient Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Patient Details</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm text-gray-500 font-medium">Age</span>
                            <span class="text-sm font-bold text-gray-900 bg-white px-3 py-1 rounded-lg shadow-sm border border-gray-100">
                                {{ $appointment->user->age ?? 'N/A' }} yrs
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm text-gray-500 font-medium">Sex</span>
                            <span class="text-sm font-bold text-gray-900 bg-white px-3 py-1 rounded-lg shadow-sm border border-gray-100">
                                {{ ucfirst($appointment->user->gender ?? 'N/A') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm text-gray-500 font-medium">Birthday</span>
                            <span class="text-sm font-bold text-gray-900 bg-white px-3 py-1 rounded-lg shadow-sm border border-gray-100">
                                {{ $appointment->user->dob ? $appointment->user->dob->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            @php
                $vitals = $appointment->healthRecord->vital_signs ?? [];
                $metadata = $appointment->healthRecord->metadata ?? [];
                $profile = $appointment->user->patientProfile;
                $isPrenatalAppt = str_contains(strtolower($appointment->service), 'prenatal');
                $isImmunizationAppt = str_contains(strtolower($appointment->service), 'immunization');
            @endphp

            @if($isPrenatalAppt && $profile)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-brand-50/50 px-6 py-4 border-b border-brand-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-brand-900 uppercase tracking-wider">Obstetric History</h3>
                        <i class="bi bi-calendar-heart text-brand-500"></i>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 p-3 bg-gray-50 rounded-xl flex justify-between">
                                <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">LMP</span>
                                <span class="text-xs font-black text-gray-900">{{ $profile->lmp ? $profile->lmp->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="col-span-2 p-3 bg-brand-50 rounded-xl flex justify-between border border-brand-100">
                                <span class="text-xs text-brand-600 font-bold uppercase tracking-wider">EDD</span>
                                <span class="text-xs font-black text-brand-900">{{ $profile->edd ? $profile->edd->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-xl text-center">
                                <span class="text-[9px] text-gray-400 font-black uppercase block">Gravida</span>
                                <span class="text-lg font-black text-gray-900">{{ $profile->gravida ?? '0' }}</span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-xl text-center">
                                <span class="text-[9px] text-gray-400 font-black uppercase block">Para</span>
                                <span class="text-lg font-black text-gray-900">{{ $profile->para ?? '0' }}</span>
                            </div>
                            @if($profile->history_miscarriage)
                                <div class="col-span-2 p-3 bg-red-50 rounded-xl flex items-center gap-2 border border-red-100">
                                    <i class="bi bi-exclamation-triangle-fill text-red-500"></i>
                                    <span class="text-[10px] font-black text-red-700 uppercase tracking-wider">History of Miscarriage</span>
                                </div>
                            @endif
                            @if($profile->is_high_risk)
                                <div class="col-span-2 p-4 bg-red-600 rounded-xl flex flex-col gap-1 shadow-lg shadow-red-200">
                                    <div class="flex items-center gap-2 text-white">
                                        <i class="bi bi-shield-exclamation text-lg"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">High Risk Pregnancy</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-red-100 leading-tight">
                                        {{ $profile->maternal_risk_notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($isImmunizationAppt && !empty($metadata))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-50/50 px-6 py-4 border-b border-indigo-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider">Pre-Vaccination</h3>
                        <i class="bi bi-shield-plus text-indigo-500"></i>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-xl {{ ($metadata['general_condition'] ?? '') === 'Healthy' ? 'bg-emerald-50 text-emerald-700' : 'bg-orange-50 text-orange-700' }}">
                            <span class="text-[10px] font-black uppercase tracking-wider">Condition</span>
                            <span class="text-xs font-black uppercase">{{ $metadata['general_condition'] ?? 'N/A' }}</span>
                        </div>
                        @if(!empty($metadata['allergy_history']))
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <span class="text-[9px] text-gray-400 font-black uppercase block mb-1">Allergies</span>
                                <p class="text-xs font-bold text-gray-700 leading-tight">{{ $metadata['allergy_history'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Vital Signs</h3>
                    <i class="bi bi-heart-pulse text-red-500 animate-pulse"></i>
                </div>
                <div class="p-6">
                    @if(empty($vitals))
                        <div class="text-center py-8 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <i class="bi bi-clipboard-x text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-sm text-gray-500">No vitals recorded</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 group hover:bg-blue-50 transition-colors">
                                <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest block mb-1">Blood Pressure</span>
                                <span class="text-xl font-black text-blue-900">{{ $vitals['blood_pressure'] ?? '--' }}</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-orange-50/50 border border-orange-100 group hover:bg-orange-50 transition-colors">
                                <span class="text-[10px] font-bold text-orange-500 uppercase tracking-widest block mb-1">Temperature</span>
                                <span class="text-xl font-black text-orange-900">{{ $vitals['temperature'] ?? '--' }} <small class="text-sm font-medium">°C</small></span>
                            </div>
                            <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 group hover:bg-emerald-50 transition-colors">
                                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest block mb-1">Weight</span>
                                <span class="text-xl font-black text-emerald-900">{{ $vitals['weight'] ?? '--' }} <small class="text-sm font-medium">kg</small></span>
                            </div>
                            <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100 group hover:bg-purple-50 transition-colors">
                                <span class="text-[10px] font-bold text-purple-500 uppercase tracking-widest block mb-1">Pulse Rate</span>
                                <span class="text-xl font-black text-purple-900">{{ $vitals['pulse_rate'] ?? '--' }} <small class="text-sm font-medium">bpm</small></span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Medical History -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Medical History</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($appointment->user->healthRecords as $record)
                            @if($record->id !== ($appointment->healthRecord->id ?? null))
                                <div class="relative pl-6 pb-4 border-l-2 border-blue-100 last:border-0 last:pb-0 group">
                                    <div class="absolute -left-1.5 top-0 w-3 h-3 rounded-full bg-blue-100 border-2 border-white group-hover:bg-blue-500 transition-colors"></div>
                                    <div class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">{{ $record->created_at->format('M d, Y') }}</div>
                                    <div class="font-bold text-gray-900 text-sm leading-tight mb-1">
                                        @if($record->diagnosis)
                                            {{ Str::limit($record->diagnosis, 50) }}
                                        @else
                                            <span class="text-gray-400 italic font-medium">{{ $record->service->name ?? 'Consultation' }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                        {{ $record->treatment ?? 'No treatment recorded' }}
                                    </p>
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-6 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                <i class="bi bi-clock-history text-2xl text-gray-300 mb-2 block"></i>
                                <p class="text-sm text-gray-500 italic">No previous records</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Consultation Form or No-Show Notice -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                    <div class="p-2 {{ $isNoShow ? 'bg-orange-500' : 'bg-blue-600' }} rounded-lg text-white">
                        <i class="bi {{ $isNoShow ? 'bi-exclamation-triangle-fill' : 'bi-pencil-square' }}"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $isNoShow ? 'Appointment Not Attended' : 'Consultation Entry' }}
                    </h3>
                </div>
                
                <div class="p-8">
                    @if($errors->has('error'))
                        <div class="p-6 bg-red-50 border border-red-100 rounded-2xl mb-8 flex items-center gap-4 animate-shake">
                            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                                <i class="bi bi-shield-lock-fill text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-red-900 uppercase tracking-wider">Clinical Safety Block</h4>
                                <p class="text-xs font-bold text-red-700 mt-1">{{ $errors->first('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($appointment->status === 'completed')
                        <div class="space-y-10 animate-in fade-in duration-700">
                            <!-- Finished Status Banner -->
                            <div class="p-8 bg-purple-50 border-2 border-purple-100 rounded-[2.5rem] flex flex-col md:flex-row items-center gap-8 shadow-sm">
                                <div class="w-20 h-20 rounded-full bg-purple-600 text-white flex items-center justify-center shadow-lg shadow-purple-500/20 ring-8 ring-white shrink-0">
                                    <i class="bi bi-check-all text-4xl"></i>
                                </div>
                                <div class="text-center md:text-left">
                                    <h4 class="text-2xl font-black text-purple-900 tracking-tight">Consultation Finished</h4>
                                    <p class="text-sm font-bold text-purple-600/80 uppercase tracking-widest mt-1">This appointment was successfully completed on {{ $appointment->healthRecord->verified_at->format('M d, Y') }}</p>
                                </div>
                                <div class="ml-auto flex items-center gap-3">
                                    <button onclick="window.print()" class="px-6 py-3 bg-white border border-purple-200 rounded-xl text-xs font-black text-purple-700 uppercase tracking-widest hover:bg-purple-100 transition-all flex items-center gap-2">
                                        <i class="bi bi-printer"></i> Print Record
                                    </button>
                                </div>
                            </div>

                            <!-- Diagnosis & Treatment -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="p-8 bg-white border border-gray-100 rounded-[2.5rem] shadow-sm relative overflow-hidden group">
                                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
                                    <h5 class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                        <i class="bi bi-clipboard2-pulse"></i> Diagnosis
                                    </h5>
                                    <p class="text-lg font-bold text-gray-900 leading-relaxed">{{ $appointment->healthRecord->diagnosis }}</p>
                                </div>

                                <div class="p-8 bg-white border border-gray-100 rounded-[2.5rem] shadow-sm relative overflow-hidden group">
                                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
                                    <h5 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                        <i class="bi bi-capsule"></i> Treatment / Prescription
                                    </h5>
                                    <p class="text-lg font-bold text-gray-900 leading-relaxed whitespace-pre-line">{{ $appointment->healthRecord->treatment }}</p>
                                </div>
                            </div>

                            @if($isImmunizationAppt)
                                <div class="p-10 bg-indigo-50/50 border-2 border-indigo-100 rounded-[3rem] relative overflow-hidden">
                                    <div class="absolute right-0 top-0 p-8 opacity-5">
                                        <i class="bi bi-shield-plus text-9xl"></i>
                                    </div>
                                    <h5 class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                                        <i class="bi bi-shield-plus"></i> Immunization Details
                                    </h5>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Vaccine</span>
                                            <span class="text-sm font-black text-gray-900">{{ $metadata['vaccine_given'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Dose No.</span>
                                            <span class="text-sm font-black text-indigo-600 bg-indigo-100 px-3 py-1 rounded-lg">{{ $metadata['dose_no'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Batch / Lot No.</span>
                                            <span class="text-sm font-black text-gray-900">{{ $metadata['batch_no'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Next Schedule</span>
                                            <span class="text-sm font-black text-orange-600">{{ isset($metadata['next_schedule']) ? \Carbon\Carbon::parse($metadata['next_schedule'])->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Route</span>
                                            <span class="text-sm font-black text-gray-900">{{ $metadata['route'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Injection Site</span>
                                            <span class="text-sm font-black text-gray-900">{{ $metadata['site'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-span-2 space-y-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Adverse Reactions</span>
                                            <p class="text-sm font-bold text-gray-700 leading-tight">{{ $metadata['adverse_reactions'] ?? 'No reactions noted' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($appointment->healthRecord->metadata['doctor_notes'] ?? ''))
                                <div class="p-8 bg-gray-50 border border-gray-100 rounded-[2.5rem]">
                                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                        <i class="bi bi-journal-text"></i> Provider's Internal Notes
                                    </h5>
                                    <p class="text-sm font-medium text-gray-600 leading-relaxed italic">"{{ $appointment->healthRecord->metadata['doctor_notes'] }}"</p>
                                </div>
                            @endif
                        </div>
                    @elseif($isNoShow)
                        <div class="p-6 bg-orange-50 border border-orange-100 rounded-2xl mb-6">
                            <p class="text-sm text-orange-800 font-medium">
                                This appointment is marked as No Show. Consultation entry is disabled.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('midwife.slots.index', ['service' => $appointment->service]) }}"
                               class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                                Rebook Patient
                            </a>
                            <a href="{{ route($routePrefix . '.appointments.index') }}"
                               class="px-5 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition">
                                Back to Appointments
                            </a>
                        </div>
                    @elseif(!$hasVitals)
                        <div class="p-8 bg-amber-50 border border-amber-100 rounded-[2.5rem] text-center space-y-6">
                            <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center text-amber-600 mx-auto shadow-inner shadow-amber-200/50">
                                <i class="bi bi-clipboard-x text-4xl"></i>
                            </div>
                            <div class="max-w-md mx-auto">
                                <h4 class="text-xl font-black text-amber-900 uppercase tracking-tight mb-2">Vitals Not Captured</h4>
                                <p class="text-sm font-bold text-amber-700/80 leading-relaxed uppercase tracking-wider">
                                    Clinical safety protocol requires patient vital signs (Weight, Temperature, BP) to be recorded by a Health Worker before consultation can proceed.
                                </p>
                            </div>
                            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route($routePrefix . '.appointments.index') }}" class="px-8 py-4 bg-white border border-amber-200 rounded-2xl text-xs font-black uppercase tracking-widest text-amber-700 hover:bg-amber-100 transition-all active:scale-95">
                                    Return to List
                                </a>
                                @if($user->isHealthWorker())
                                <a href="{{ route('healthworker.appointments.add-vitals', $appointment->id) }}" class="px-8 py-4 bg-amber-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-amber-700 shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                                    Capture Vitals Now
                                </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <form action="{{ route($routePrefix . '.appointments.consult', $appointment->id) }}" method="POST" id="consultationForm"
                            x-data="{ 
                                service: '{{ $appointment->service }}',
                                isPrenatal: '{{ str_contains(strtolower($appointment->service), 'prenatal') }}',
                                isImmunization: '{{ str_contains(strtolower($appointment->service), 'immunization') }}'
                            }">
                            @csrf
                            
                            <div class="grid grid-cols-1 gap-8">
                                <!-- Prenatal Assessment Section -->
                                <template x-if="isPrenatal">
                                    <div class="space-y-8">
                                        {{-- 1. Obstetric History Update --}}
                                        <div class="bg-brand-50/30 p-8 rounded-[2.5rem] border border-brand-100">
                                            <div class="flex items-center gap-3 mb-6">
                                                <div class="w-10 h-10 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600">
                                                    <i class="bi bi-person-heart text-xl"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-md font-black text-gray-900 uppercase tracking-tight">Obstetric Profile Update</h4>
                                                    <p class="text-[9px] font-bold text-brand-600 uppercase tracking-widest">Updates Patient's permanent record</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <div class="md:col-span-1">
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">LMP</label>
                                                    <input type="date" name="profile[lmp]" value="{{ optional($profile)->lmp ? optional($profile)->lmp->format('Y-m-d') : '' }}"
                                                        class="w-full px-4 py-3 rounded-xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900">
                                                </div>
                                                <div class="md:col-span-1">
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">EDD</label>
                                                    <input type="date" name="profile[edd]" value="{{ optional($profile)->edd ? optional($profile)->edd->format('Y-m-d') : '' }}"
                                                        class="w-full px-4 py-3 rounded-xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900">
                                                </div>
                                                <div class="md:col-span-1">
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Gravida</label>
                                                    <input type="number" name="profile[gravida]" value="{{ optional($profile)->gravida ?? 0 }}"
                                                        class="w-full px-4 py-3 rounded-xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900">
                                                </div>
                                                <div class="md:col-span-1">
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Para</label>
                                                    <input type="number" name="profile[para]" value="{{ optional($profile)->para ?? 0 }}"
                                                        class="w-full px-4 py-3 rounded-xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900">
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="flex items-center gap-3 px-6 py-4 rounded-2xl bg-white shadow-sm cursor-pointer hover:bg-red-50 transition-colors border-2 border-transparent has-[:checked]:border-red-200 group">
                                                        <input type="checkbox" name="profile[history_miscarriage]" value="1" {{ (optional($profile)->history_miscarriage ?? false) ? 'checked' : '' }}
                                                            class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                        <div class="flex flex-col">
                                                            <span class="text-xs font-black text-gray-700 uppercase tracking-widest group-has-[:checked]:text-red-700">History of Miscarriage</span>
                                                            <span class="text-[9px] font-bold text-gray-400 uppercase group-has-[:checked]:text-red-400">Mark if patient had previous pregnancy loss</span>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="flex items-center gap-3 px-6 py-4 rounded-2xl bg-white shadow-sm cursor-pointer hover:bg-orange-50 transition-colors border-2 border-transparent has-[:checked]:border-orange-200 group">
                                                        <input type="checkbox" name="profile[is_high_risk]" value="1" {{ (optional($profile)->is_high_risk ?? false) ? 'checked' : '' }}
                                                            class="w-5 h-5 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                                        <div class="flex flex-col">
                                                            <span class="text-xs font-black text-gray-700 uppercase tracking-widest group-has-[:checked]:text-orange-700">Flag as High Risk</span>
                                                            <span class="text-[9px] font-bold text-gray-400 uppercase group-has-[:checked]:text-orange-400">Overrides automatic risk calculation</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Visit Details --}}
                                        <div class="space-y-8 bg-brand-50/30 p-8 rounded-[2.5rem] border border-brand-100">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 rounded-2xl bg-brand-600 flex items-center justify-center text-white shadow-brand-500/20 shadow-lg">
                                                        <i class="bi bi-fetal-fill text-xl"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-lg font-black text-gray-900 uppercase tracking-tight">Prenatal Assessment</h4>
                                                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-[0.2em]">Maternal & Fetal Monitoring (DOH Protocol)</p>
                                                    </div>
                                                </div>
                                                <div class="hidden sm:block">
                                                    <span class="px-4 py-2 rounded-2xl bg-white border border-brand-100 text-[10px] font-black text-brand-600 uppercase tracking-widest shadow-sm">
                                                        Visit #{{ ($appointment->user->healthRecords()->whereHas('service', fn($q) => $q->where('name', 'like', '%Prenatal%'))->count() + 1) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                                {{-- Basic Metrics --}}
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">AOG (Weeks)</label>
                                                    <div class="relative">
                                                        <input type="number" name="metadata[aog]" class="w-full pl-4 pr-12 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-black text-gray-900" placeholder="0">
                                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-300 uppercase">Wks</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Fundal Height</label>
                                                    <div class="relative">
                                                        <input type="number" step="0.1" name="metadata[fundal_height]" class="w-full pl-4 pr-12 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-black text-gray-900" placeholder="0.0">
                                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-300 uppercase">cm</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Fetal Heart Tone</label>
                                                    <div class="relative">
                                                        <input type="number" name="metadata[fetal_heartbeat]" class="w-full pl-4 pr-12 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-black text-gray-900" placeholder="0">
                                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-300 uppercase">bpm</span>
                                                    </div>
                                                </div>

                                                {{-- Fetal Status --}}
                                                <div class="md:col-span-2">
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Fetal Position & Presentation</label>
                                                    <input type="text" name="metadata[fetal_position]" class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900" placeholder="e.g. Cephalic, Longitudinal">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Tetanus Toxoid (TT)</label>
                                                    <select name="metadata[tt_dose]" class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900">
                                                        <option value="">No dose today</option>
                                                        <option value="TT1">TT1 (Initial)</option>
                                                        <option value="TT2 (4 wks after TT1)">TT2</option>
                                                        <option value="TT3 (6 mos after TT2)">TT3</option>
                                                        <option value="TT4 (1 yr after TT3)">TT4</option>
                                                        <option value="TT5 (1 yr after TT4)">TT5</option>
                                                    </select>
                                                </div>

                                                {{-- Nutrition & Labs --}}
                                                <div class="md:col-span-3">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                        <div class="space-y-4">
                                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Supplements Provided</label>
                                                            <div class="flex flex-wrap gap-3">
                                                                <label class="flex items-center gap-2 px-4 py-3 rounded-xl bg-white shadow-sm cursor-pointer hover:bg-brand-50 transition-colors border border-transparent has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50 group">
                                                                    <input type="checkbox" name="metadata[supplements][]" value="Iron" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                                                    <span class="text-xs font-bold text-gray-600 group-has-[:checked]:text-brand-700">Iron</span>
                                                                </label>
                                                                <label class="flex items-center gap-2 px-4 py-3 rounded-xl bg-white shadow-sm cursor-pointer hover:bg-brand-50 transition-colors border border-transparent has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50 group">
                                                                    <input type="checkbox" name="metadata[supplements][]" value="Folic Acid" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                                                    <span class="text-xs font-bold text-gray-600 group-has-[:checked]:text-brand-700">Folic Acid</span>
                                                                </label>
                                                                <label class="flex items-center gap-2 px-4 py-3 rounded-xl bg-white shadow-sm cursor-pointer hover:bg-brand-50 transition-colors border border-transparent has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50 group">
                                                                    <input type="checkbox" name="metadata[supplements][]" value="Calcium" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                                                    <span class="text-xs font-bold text-gray-600 group-has-[:checked]:text-brand-700">Calcium</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Lab Tests Results/Requested</label>
                                                            <input type="text" name="metadata[lab_tests]" class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900" placeholder="e.g. CBC, Blood Typing, Urinalysis">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Education & Follow-up --}}
                                                <div class="md:col-span-3">
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Health Education & Advice</label>
                                                    <textarea name="metadata[health_teaching]" rows="3" class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-gray-900" placeholder="Describe topics discussed (Danger signs, Breastfeeding, Birth planning)..."></textarea>
                                                </div>
                                                
                                                <div class="md:col-span-3 pt-4">
                                                    <div class="flex items-center gap-4 p-6 bg-white/50 rounded-2xl border border-brand-100/50">
                                                        <div class="w-10 h-10 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600">
                                                            <i class="bi bi-calendar-plus-fill"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Recommended Next Follow-up</label>
                                                            <input type="date" name="metadata[next_appointment]" class="bg-transparent border-none p-0 focus:ring-0 font-black text-gray-900 text-lg">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Immunization Assessment Section -->
                                <template x-if="isImmunization">
                                    <div class="space-y-6 bg-indigo-50/30 p-6 rounded-[2rem] border border-indigo-100" 
                                         x-data="{ 
                                            selectedBatchId: '',
                                            batchNo: '',
                                            expiryDate: '',
                                            vaccineName: '',
                                            updateBatchDetails(e) {
                                                const option = e.target.options[e.target.selectedIndex];
                                                if (option.value) {
                                                    this.batchNo = option.getAttribute('data-batch');
                                                    this.expiryDate = option.getAttribute('data-expiry-raw');
                                                    this.vaccineName = option.getAttribute('data-vaccine');
                                                } else {
                                                    this.batchNo = '';
                                                    this.expiryDate = '';
                                                    this.vaccineName = '';
                                                }
                                            }
                                         }">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-indigo-500/20 shadow-lg">
                                                <i class="bi bi-shield-plus"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest">Vaccine Administration</h4>
                                                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em]">Immunization Log Details</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="md:col-span-2">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Select Vaccine Stock (Batch/Lot)</label>
                                                <select name="metadata[vaccine_batch_id]" x-model="selectedBatchId" @change="updateBatchDetails($event)" required
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold">
                                                    <option value="">-- Choose available batch --</option>
                                                    @foreach($vaccineBatches as $vName => $batches)
                                                        <optgroup label="{{ $vName }}">
                                                            @foreach($batches as $batch)
                                                                <option value="{{ $batch->id }}" 
                                                                    data-vaccine="{{ $vName }}"
                                                                    data-batch="{{ $batch->batch_number }}"
                                                                    data-expiry-raw="{{ $batch->expiry_date->format('Y-m-d') }}">
                                                                    Batch: {{ $batch->batch_number }} (Rem: {{ $batch->quantity_remaining }}) - Exp: {{ $batch->expiry_date->format('M d, Y') }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                @if(count($vaccineBatches) == 0)
                                                    <p class="mt-2 text-xs font-bold text-red-500 uppercase tracking-widest"><i class="bi bi-exclamation-circle"></i> No active vaccine stock found in inventory!</p>
                                                @endif
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Vaccine Administered</label>
                                                <input type="text" name="metadata[vaccine_given]" x-model="vaccineName" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold" placeholder="e.g. Pentavalent, PCV">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Dose Number</label>
                                                <select name="metadata[dose_no]" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold">
                                                    <option value="1">1st Dose</option>
                                                    <option value="2">2nd Dose</option>
                                                    <option value="3">3rd Dose</option>
                                                    <option value="Booster">Booster</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Route</label>
                                                <select name="metadata[route]" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold">
                                                    <option value="IM">Intramuscular (IM)</option>
                                                    <option value="SC">Subcutaneous (SC)</option>
                                                    <option value="Oral">Oral</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Injection Site</label>
                                                <input type="text" name="metadata[site]" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold" placeholder="e.g. Left Thigh">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Batch / Lot Number</label>
                                                <input type="text" name="metadata[batch_no]" x-model="batchNo" readonly class="w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 font-bold text-gray-500" placeholder="Select a batch above...">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Expiry Date</label>
                                                <input type="date" name="metadata[expiry_date]" x-model="expiryDate" readonly class="w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 font-bold text-gray-500">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Next Schedule</label>
                                                <input type="date" name="metadata[next_schedule]" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Adverse Reactions Observed</label>
                                                <textarea name="metadata[adverse_reactions]" rows="2" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold" placeholder="List any reactions observed during the visit..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Diagnosis Section -->
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                        <i class="bi bi-clipboard2-pulse text-blue-500"></i>
                                        Diagnosis <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="diagnosis" 
                                            class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300" 
                                            required 
                                            placeholder="e.g. Acute Upper Respiratory Infection">
                                    </div>
                                </div>

                                <!-- Treatment Section -->
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                        <i class="bi bi-capsule text-blue-500"></i>
                                        Treatment / Prescription <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="treatment" rows="6" 
                                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300 resize-none" 
                                        required 
                                        placeholder="Enter treatment plan, dosage, and specific medications..."></textarea>
                                </div>

                                <!-- Notes Section -->
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                        <i class="bi bi-journal-text text-blue-500"></i>
                                        Provider's Internal Notes
                                    </label>
                                    <textarea name="notes" rows="3" 
                                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300 resize-none bg-gray-50/50" 
                                        placeholder="Confidential notes, follow-up reminders, etc..."></textarea>
                                </div>
                            </div>

                            <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <p class="text-xs text-gray-400 flex items-center gap-1.5 order-2 sm:order-1">
                                    <i class="bi bi-info-circle"></i>
                                    All fields marked with * are required to complete.
                                </p>
                                <div class="flex items-center gap-3 w-full sm:w-auto order-1 sm:order-2">
                                    <button type="button" onclick="history.back()" 
                                        class="flex-1 sm:flex-none px-6 py-3 bg-white text-gray-600 border border-gray-200 rounded-xl font-bold hover:bg-gray-50 hover:text-gray-900 transition-all active:scale-95">
                                        Discard
                                    </button>
                                    <button type="submit" 
                                        class="flex-1 sm:flex-none px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Finish Consultation
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
