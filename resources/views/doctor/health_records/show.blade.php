@extends('layouts.app')

@section('title', 'Medical Record - ' . $record->patient->full_name)

@section('content')
@php
    $user = auth()->user();
    $prefix = $user->isMidwife() ? 'midwife' : ($user->isDoctor() ? 'doctor' : 'healthworker');
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8 bg-[#f8fafc]">
    <!-- Header: Professional & Clean -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-3xl border border-slate-200 shadow-sm shadow-slate-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none opacity-30"></div>
        
        <div class="flex items-center gap-6 relative z-10">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white flex items-center justify-center text-3xl font-bold shadow-lg shadow-brand-500/20">
                {{ substr($record->patient->first_name, 0, 1) }}{{ substr($record->patient->last_name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $record->patient->full_name }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider border border-slate-200">
                        PID: {{ str_pad($record->patient->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="text-sm font-medium text-slate-500 flex items-center gap-1.5">
                        <i class="bi bi-calendar-check text-brand-500"></i>
                        Record Date: {{ $record->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <button onclick="window.print()" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="bi bi-printer text-lg"></i> Print
            </button>
            <a href="{{ route($prefix . '.health-records.index') }}" class="px-6 py-3 bg-brand-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-all flex items-center gap-2 shadow-lg shadow-brand-500/20">
                <i class="bi bi-arrow-left text-lg"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Sidebar: Patient Info & Vitals (Col-Span-4) -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Patient Stats -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                    <i class="bi bi-person-circle text-brand-500"></i>
                    Patient Profile
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Age</span>
                        <span class="text-lg font-bold text-slate-800">{{ $record->patient->age ?? 'N/A' }} <small class="text-xs font-medium text-slate-400">Yrs</small></span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Gender</span>
                        <span class="text-lg font-bold text-slate-800">{{ ucfirst($record->patient->gender) }}</span>
                    </div>
                </div>

                @php
                    $bmiValue = $record->vital_signs['bmi'] ?? null;
                    if (!$bmiValue && isset($record->vital_signs['weight']) && isset($record->vital_signs['height_cm'])) {
                        $weight = floatval($record->vital_signs['weight']);
                        $height = floatval($record->vital_signs['height_cm']) / 100;
                        if ($height > 0) $bmiValue = round($weight / ($height * $height), 1);
                    }
                    $bmiStatus = ''; $bmiColor = 'text-slate-400'; $bmiBarWidth = 0;
                    if ($bmiValue && is_numeric($bmiValue)) {
                        if ($bmiValue < 18.5) { $bmiStatus = 'Underweight'; $bmiColor = 'text-blue-500'; $bmiBarWidth = 20; }
                        elseif ($bmiValue < 25) { $bmiStatus = 'Healthy'; $bmiColor = 'text-emerald-500'; $bmiBarWidth = 45; }
                        elseif ($bmiValue < 30) { $bmiStatus = 'Overweight'; $bmiColor = 'text-orange-500'; $bmiBarWidth = 70; }
                        else { $bmiStatus = 'Obese'; $bmiColor = 'text-red-500'; $bmiBarWidth = 95; }
                    }
                @endphp

                @if($bmiValue)
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Body Mass Index</span>
                        <span class="text-xs font-bold {{ $bmiColor }} uppercase">{{ $bmiStatus }}</span>
                    </div>
                    <div class="flex items-baseline gap-2 mb-3">
                        <span class="text-3xl font-black text-slate-800">{{ $bmiValue }}</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-500 transition-all duration-1000" @style(['width' => $bmiBarWidth . '%'])></div>
                    </div>
                </div>
                @endif

                <div class="pt-6 border-t border-slate-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase">Provider</span>
                        <span class="text-sm font-bold text-slate-700">{{ $record->creator->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase">Service</span>
                        <span class="text-sm font-bold text-brand-600 uppercase tracking-wider">{{ $record->service->name ?? 'Consultation' }}</span>
                    </div>
                </div>
            </div>

            <!-- Vitals Grid -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                    <i class="bi bi-activity text-red-500"></i>
                    Vital Signs
                </h3>

                @if($record->vital_signs && is_array($record->vital_signs))
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($record->vital_signs as $label => $value)
                            @if(!empty($value) && $label != 'bmi')
                                @php
                                    $icon = match($label) {
                                        'blood_pressure' => 'bi-droplet-fill text-blue-500',
                                        'temperature' => 'bi-thermometer-half text-orange-500',
                                        'weight' => 'bi-speedometer2 text-emerald-500',
                                        'height_cm' => 'bi-rulers text-indigo-500',
                                        'pulse_rate' => 'bi-heart-pulse text-red-500',
                                        'respiratory_rate' => 'bi-wind text-teal-500',
                                        'oxygen_saturation' => 'bi-lungs text-blue-600',
                                        default => 'bi-activity text-slate-400'
                                    };
                                @endphp
                                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:bg-white transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="bi {{ $icon }} text-lg"></i>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ str_replace('_', ' ', $label) }}</span>
                                    </div>
                                    <span class="text-sm font-black text-slate-800">{{ $value }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <p class="text-sm text-slate-400 italic">No vitals recorded</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Content: Consultation, Diagnosis, Treatment (Col-Span-8) -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-10 space-y-12">
                    <!-- Consultation Notes -->
                    <div class="space-y-6">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-brand-500 rounded-full"></span>
                            Consultation Notes & Observations
                        </h4>
                        <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100 min-h-[150px]">
                            @if($record->consultation)
                                <p class="text-slate-700 text-lg leading-relaxed font-medium whitespace-pre-line">{{ $record->consultation }}</p>
                            @else
                                <div class="flex flex-col items-center justify-center py-10 text-center opacity-40">
                                    <i class="bi bi-chat-left-dots text-4xl mb-4"></i>
                                    <p class="text-sm font-bold uppercase tracking-widest">No detailed notes documented</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Diagnosis & Treatment Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                                Clinical Diagnosis
                            </h4>
                            <div class="p-8 bg-blue-50 rounded-[2rem] border border-blue-100 min-h-[200px] relative group overflow-hidden">
                                <i class="bi bi-clipboard2-pulse text-6xl absolute -right-4 -bottom-4 text-blue-100 group-hover:scale-110 transition-transform"></i>
                                <p class="text-slate-800 font-bold text-xl leading-relaxed relative z-10">
                                    {{ $record->diagnosis ?: 'Not specified' }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-orange-400 rounded-full"></span>
                                Treatment & Prescription
                            </h4>
                            <div class="p-8 bg-orange-50 rounded-[2rem] border border-orange-100 min-h-[200px] relative group overflow-hidden">
                                <i class="bi bi-capsule text-6xl absolute -right-4 -bottom-4 text-orange-100 group-hover:scale-110 transition-transform"></i>
                                <p class="text-slate-800 font-bold text-xl leading-relaxed relative z-10">
                                    {{ $record->treatment ?: 'No treatment prescribed' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Record Verification Footer -->
                <div class="px-10 py-6 bg-slate-50 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-300">
                            <i class="bi bi-fingerprint text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Electronic Signature</p>
                            <p class="text-xs font-bold text-slate-600">{{ $record->creator->full_name }} • {{ $record->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-[9px] font-mono text-slate-300 uppercase tracking-widest">
                        REF: {{ strtoupper(substr(md5($record->id), 0, 16)) }}
                    </div>
                </div>
            </div>

            <!-- Clinical Metadata: Now Light & Organized -->
            @if($record->metadata && count($record->metadata) > 0)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-10 space-y-8">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                            Clinical Context & Metadata
                        </h4>
                        <span class="px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-widest border border-indigo-100">
                            Extended Data Active
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($record->metadata as $key => $val)
                            @php
                                $displayVal = is_array($val) ? implode(', ', $val) : $val;
                                $isLong = strlen($displayVal) > 50;
                                
                                $icon = match(strtolower(str_replace(' ', '_', $key))) {
                                    'lmp', 'edd', 'next_appointment' => 'bi-calendar-date text-brand-500',
                                    'gravida', 'para' => 'bi-person-badge text-blue-500',
                                    'aog' => 'bi-clock-history text-orange-500',
                                    'fetal_heartbeat' => 'bi-heart-pulse text-red-500',
                                    'vaccine_given' => 'bi-shield-plus text-indigo-500',
                                    'dose_no' => 'bi-hash text-purple-500',
                                    'batch_no' => 'bi-qr-code text-slate-500',
                                    default => 'bi-info-circle text-slate-400'
                                };
                            @endphp
                            <div class="{{ $isLong ? 'md:col-span-2' : 'col-span-1' }} p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:bg-white transition-all group">
                                <div class="flex items-start justify-between mb-4">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">
                                        {{ str_replace('_', ' ', $key) }}
                                    </span>
                                    <i class="bi {{ $icon }} opacity-40 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-800 leading-relaxed">
                                    {{ $displayVal ?: 'N/A' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }

    .tracking-tight { letter-spacing: -0.02em; }
    .tracking-tighter { letter-spacing: -0.05em; }

    @media print {
        .max-w-7xl { max-width: 100% !important; padding: 0 !important; }
        body { background: white !important; }
        .rounded-3xl, .rounded-2xl { border-radius: 8px !important; }
        .shadow-sm, .shadow-lg { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        .bg-brand-600, .bg-brand-500 { background: #000 !important; color: white !important; }
        button, a[href*="index"] { display: none !important; }
    }
</style>
@endsection
