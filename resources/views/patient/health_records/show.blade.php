@extends('layouts.app')

@section('title', 'Record Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 relative overflow-hidden bg-[#fcfcfd] font-sans">
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 right-0 w-[30rem] h-[30rem] bg-brand-50/20 rounded-full blur-3xl -mr-60 -mt-60 opacity-30 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[30rem] h-[30rem] bg-blue-50/20 rounded-full blur-3xl -ml-60 -mb-60 opacity-30 pointer-events-none"></div>

    {{-- Header Section --}}
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5 mb-2.5">
                <span class="text-[8px] font-black text-brand-600 uppercase tracking-[0.2em] bg-brand-50 px-3 py-1.5 rounded-lg border border-brand-100 flex items-center gap-2 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    Medical Archive
                </span>
                <span class="text-[8px] font-black text-blue-600 uppercase tracking-[0.2em] bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 flex items-center gap-2 shadow-sm">
                    <i class="bi bi-shield-check-fill text-xs"></i>
                    Verified Record
                </span>
            </div>
            
            <div class="space-y-2">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight leading-tight">
                    <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-2">{{ $record->service->name ?? 'General Consultation' }}</span>
                </h1>
                <div class="flex flex-wrap items-center gap-2 pt-2">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm text-xs">
                        <i class="bi bi-calendar3 text-brand-500"></i>
                        <span class="text-gray-600 font-black uppercase tracking-widest">
                            {{ $record->created_at->format('F d, Y') }} • {{ $record->created_at->format('h:i A') }}
                        </span>
                    </div>
                    <span class="hidden md:block w-1 h-1 rounded-full bg-gray-200"></span>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm text-xs">
                        <i class="bi bi-hash text-brand-500"></i>
                        <span class="text-gray-400 font-black text-[7px] uppercase tracking-[0.1em]">
                            ID: {{ str_pad($record->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 print:hidden">
            <a href="{{ route('patient.health-records.index') }}" 
               class="group inline-flex items-center px-5 py-2.5 bg-white text-gray-500 rounded-lg font-black text-[8px] uppercase tracking-[0.2em] border border-gray-100 hover:bg-gray-50 hover:text-brand-600 hover:border-brand-200 transition-all shadow-sm gap-2 active:scale-95">
                <i class="bi bi-arrow-left text-sm group-hover:-translate-x-1 transition-transform"></i>
                Back to History
            </a>
            <button onclick="window.print()" class="group px-4 py-2.5 bg-white text-gray-500 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] border border-gray-100 hover:bg-gray-50 hover:text-brand-600 hover:border-brand-200 transition-all flex items-center gap-2 shadow-sm active:scale-95">
                <i class="bi bi-printer-fill text-sm group-hover:scale-110 transition-transform"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-10">
        
        {{-- Left Column: Provider & Vitals --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Provider Card --}}
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-brand-100 transition-all duration-700">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full -mr-12 -mt-12 opacity-40 group-hover:opacity-100 transition-opacity duration-1000"></div>
                
                <h3 class="relative z-10 text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-sm shadow-inner border border-brand-100">
                        <i class="bi bi-person-badge-fill"></i>
                    </span>
                    Clinical Provider
                </h3>

                <div class="relative z-10 space-y-3">
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-gray-50 border border-gray-100 group/provider hover:bg-white hover:shadow-md transition-all duration-500">
                        <div class="w-10 h-10 rounded-lg bg-white text-brand-200 flex items-center justify-center shadow-sm border border-gray-100 group-hover/provider:bg-brand-600 group-hover/provider:text-white transition-all duration-500">
                            <i class="bi bi-person-check-fill text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[7px] font-black text-gray-400 uppercase tracking-[0.1em] mb-0.5">Attending Clinician</p>
                            <p class="text-sm font-black text-gray-900 tracking-tight">
                                {{ $record->provider_name ?? 'Medical Staff' }}
                            </p>
                        </div>
                    </div>

                    @if($record->creator && $record->verifier && $record->creator->id !== $record->verifier->id)
                        <div class="flex items-center gap-3 p-4 rounded-lg bg-gray-50 border border-gray-100 group/worker hover:bg-white hover:shadow-md transition-all duration-500">
                            <div class="w-10 h-10 rounded-lg bg-white text-blue-200 flex items-center justify-center shadow-sm border border-gray-100 group-hover/worker:bg-blue-600 group-hover/worker:text-white transition-all duration-500">
                                <i class="bi bi-clipboard2-pulse-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[7px] font-black text-gray-400 uppercase tracking-[0.1em] mb-0.5">Health Assistant</p>
                                <p class="text-sm font-black text-gray-900 tracking-tight">{{ $record->creator->full_name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vital Signs Card --}}
            @php
                $vitals = $record->vital_signs;
                $hasVitals = false;
                
                if (is_string($vitals)) {
                    $decoded = json_decode($vitals, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $vitals = $decoded;
                    }
                }
                
                if (!empty($vitals) && is_array($vitals)) {
                    $hasVitals = true;
                }
            @endphp

            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-blue-100 transition-all duration-700">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-12 -mt-12 opacity-40 group-hover:opacity-100 transition-opacity duration-1000"></div>
                
                <h3 class="relative z-10 text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-inner border border-blue-100">
                        <i class="bi bi-activity"></i>
                    </span>
                    Clinical Vitals
                </h3>

                @php
                    $bmiValue = $vitals['bmi'] ?? null;
                    if (!$bmiValue && isset($vitals['weight']) && isset($vitals['height_cm'])) {
                        $weight = (float) $vitals['weight'];
                        $height = (float) $vitals['height_cm'] / 100;
                        if ($height > 0) {
                            $bmiValue = round($weight / ($height * $height), 1);
                        }
                    }

                    $bmiStatus = '';
                    $bmiColor = 'text-gray-400';
                    $bmiBg = 'bg-gray-50';
                    $bmiBarWidth = 0;

                    if ($bmiValue && is_numeric($bmiValue)) {
                        if ($bmiValue < 18.5) { 
                            $bmiStatus = 'Underweight'; $bmiColor = 'text-blue-500'; $bmiBg = 'bg-blue-50'; $bmiBarWidth = 20;
                        } elseif ($bmiValue < 25) { 
                            $bmiStatus = 'Healthy'; $bmiColor = 'text-emerald-500'; $bmiBg = 'bg-emerald-50'; $bmiBarWidth = 45;
                        } elseif ($bmiValue < 30) { 
                            $bmiStatus = 'Overweight'; $bmiColor = 'text-orange-500'; $bmiBg = 'bg-orange-50'; $bmiBarWidth = 70;
                        } else { 
                            $bmiStatus = 'Obese'; $bmiColor = 'text-red-500'; $bmiBg = 'bg-red-50'; $bmiBarWidth = 95;
                        }
                    }
                @endphp

                @if($bmiValue)
                    <div class="relative z-10 w-full p-4 rounded-lg {{ $bmiBg }} border border-gray-100 mb-4 group/bmi hover:bg-white hover:shadow-md transition-all duration-700 overflow-hidden">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-gray-400 group-hover/bmi:text-brand-600 shadow-sm border border-gray-100">
                                    <i class="bi bi-person-bounding-box text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[7px] font-black text-gray-400 uppercase tracking-tighter">Body Mass Index</p>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-2xl font-black text-gray-900 tracking-tight">{{ $bmiValue }}</span>
                                        <span class="text-[8px] font-black uppercase tracking-widest {{ $bmiColor }}">{{ $bmiStatus }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative h-2 w-full bg-white rounded-full overflow-hidden border border-gray-100 shadow-inner">
                            <div class="h-full bg-brand-600 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-brand-500/20" style="width: {{ $bmiBarWidth }}%"></div>
                        </div>
                        <div class="flex justify-between mt-2 px-1">
                            <span class="text-[7px] font-black text-gray-300 uppercase tracking-tighter">18.5</span>
                            <span class="text-[7px] font-black text-gray-300 uppercase tracking-tighter">25.0</span>
                            <span class="text-[7px] font-black text-gray-300 uppercase tracking-tighter">30.0</span>
                        </div>
                    </div>
                @endif

                @if($hasVitals)
                    <div class="relative z-10 grid grid-cols-1 gap-3">
                        @foreach($vitals as $label => $value)
                            @if(!empty($value) && $label != 'bmi')
                                @php
                                    $normalizedKey = strtolower(str_replace([' ', '_'], '', $label));
                                    $icon = match($normalizedKey) {
                                        'bloodpressure', 'bp', 'blood_pressure' => 'bi-droplet-fill',
                                        'temperature', 'temp' => 'bi-thermometer-half',
                                        'weight', 'wt' => 'bi-speedometer2',
                                        'height', 'ht', 'height_cm' => 'bi-rulers',
                                        'pulse', 'pulserate', 'hr' => 'bi-heart-pulse-fill',
                                        'respiratoryrate', 'rr' => 'bi-wind',
                                        'oxygensaturation', 'spo2' => 'bi-lungs-fill',
                                        default => 'bi-activity'
                                    };
                                @endphp
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100 group/vital hover:bg-white hover:shadow-md hover:border-blue-100 transition-all duration-500">
                                    <div class="flex flex-col">
                                        <span class="text-[7px] font-black text-gray-400 uppercase tracking-[0.1em] leading-none mb-1">
                                            {{ str_replace('_', ' ', $label) }}
                                        </span>
                                        <span class="text-sm font-black text-gray-900 leading-relaxed tracking-tight">
                                            @if(is_array($value))
                                                {{ implode(', ', $value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="w-9 h-9 rounded-lg bg-white text-blue-200 flex items-center justify-center shadow-sm group-hover/vital:bg-blue-600 group-hover/vital:text-white transition-all duration-700 border border-gray-100">
                                        <i class="bi {{ $icon }} text-sm"></i>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="relative z-10 text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-gray-200 mx-auto mb-2 shadow-sm">
                            <i class="bi bi-activity text-2xl"></i>
                        </div>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em]">No Vitals Captured</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Consultation Details --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- Main Clinical Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative group/content hover:border-brand-100 transition-all duration-700">
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-2xl -mr-32 -mt-32 opacity-30 group-hover/content:opacity-60 transition-opacity duration-1000"></div>
                
                <div class="p-6 lg:p-8 relative z-10">
                    {{-- Diagnosis --}}
                    <div class="mb-6">
                        <h4 class="text-[8px] font-black text-gray-400 uppercase tracking-[0.3em] mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-blue-600 rounded-full shadow-lg"></span>
                            Clinical Diagnosis
                        </h4>
                        <div class="p-5 bg-blue-50/50 rounded-lg border border-blue-100 relative group/diag overflow-hidden hover:bg-blue-50 transition-all duration-700 shadow-sm">
                            <div class="absolute -right-4 -bottom-4 w-20 h-20 text-blue-100 group-hover/diag:scale-110 transition-all duration-1000 pointer-events-none">
                                <i class="bi bi-clipboard2-pulse-fill text-5xl"></i>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 leading-tight tracking-tight relative z-10">
                                {{ $record->diagnosis ?: 'General Medical Consultation' }}
                            </h2>
                        </div>
                    </div>

                    {{-- Consultation Notes --}}
                    <div class="mb-6">
                        <h4 class="text-[8px] font-black text-gray-400 uppercase tracking-[0.3em] mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-brand-600 rounded-full shadow-lg"></span>
                            Clinical Observations
                        </h4>
                        <div class="text-gray-700 text-base leading-relaxed font-bold whitespace-pre-line tracking-tight">
                            @if($record->consultation)
                                {!! nl2br(e($record->consultation)) !!}
                            @else
                                <div class="flex flex-col items-center gap-4 py-12 bg-gray-50 rounded-lg border border-dashed border-gray-200 justify-center group/empty shadow-inner">
                                    <div class="w-12 h-12 bg-white rounded-lg text-gray-200 flex items-center justify-center text-2xl shadow-sm relative z-10">
                                        <i class="bi bi-chat-left-dots-fill"></i>
                                    </div>
                                    <div class="text-center space-y-1.5 px-4">
                                        <p class="text-gray-400 font-black text-xs uppercase tracking-[0.2em]">No Detailed Notes</p>
                                        <p class="text-gray-300 font-bold text-xs max-w-sm mx-auto leading-relaxed uppercase tracking-widest">No qualitative observations were documented.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Treatment / Prescriptions --}}
                    @if($record->treatment)
                        <div class="space-y-10">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] flex items-center gap-4">
                                <span class="w-2 h-8 bg-emerald-600 rounded-full shadow-lg"></span>
                                Treatment & Prescriptions
                            </h4>
                            <div class="p-10 bg-emerald-50/50 rounded-[3rem] border border-emerald-100 min-h-[200px] relative group/treat overflow-hidden hover:bg-emerald-50 transition-all duration-700 shadow-sm">
                                <div class="absolute -right-6 -bottom-6 w-32 h-32 text-emerald-100 group-hover/treat:scale-125 group-hover/treat:-rotate-12 transition-all duration-1000 pointer-events-none">
                                    <i class="bi bi-capsule-pill text-9xl"></i>
                                </div>
                                <div class="text-gray-900 font-black text-xl leading-relaxed relative z-10 tracking-tight">
                                    {!! nl2br(e($record->treatment)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Record Footer Info --}}
                @php
                    $signatureMeta = $record->metadata['signature'] ?? null;
                    $signatureImage = is_array($signatureMeta) ? ($signatureMeta['signature_data'] ?? null) : null;
                    $signedByName = is_array($signatureMeta) ? ($signatureMeta['signed_by_name'] ?? $record->provider_name) : $record->provider_name;
                    $signedRole = is_array($signatureMeta) ? ($signatureMeta['signed_role'] ?? null) : null;
                    $signedAtRaw = is_array($signatureMeta) ? ($signatureMeta['signed_at'] ?? null) : null;
                    $signedAt = $signedAtRaw ? \Carbon\Carbon::parse($signedAtRaw) : ($record->verified_at ?? $record->created_at);
                @endphp
                <div class="bg-gray-50 px-12 py-10 border-t border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex flex-col md:flex-row md:items-center gap-5 w-full">
                        @if(!empty($signatureImage) && str_starts_with($signatureImage, 'data:image/'))
                            <div class="w-full md:w-64 h-24 rounded-2xl bg-white flex items-center justify-center shadow-sm border border-gray-200 overflow-hidden p-3 relative">
                                <div class="absolute inset-0 opacity-40" style="background-image: linear-gradient(to right, rgba(156,163,175,0.15) 1px, transparent 1px), linear-gradient(to bottom, rgba(156,163,175,0.15) 1px, transparent 1px); background-size: 18px 18px;"></div>
                                <img src="{{ $signatureImage }}" alt="Provider signature" class="max-h-full max-w-full object-contain relative z-10">
                            </div>
                        @else
                            <div class="w-full md:w-64 h-24 rounded-2xl bg-white text-gray-300 flex items-center justify-center shadow-sm border border-gray-100 group-hover/content:text-brand-600 transition-colors">
                                <div class="text-center">
                                    <i class="bi bi-fingerprint text-2xl"></i>
                                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mt-1">No Signature Image</p>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-2 flex-1 min-w-0">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] leading-none">Electronic Signature</p>
                            <p class="text-base font-black text-gray-900 leading-tight tracking-tight break-words">
                                {{ $signedByName ?? 'Medical Staff' }}@if($signedRole) ({{ $signedRole }}) @endif
                                • {{ $signedAt ? $signedAt->format('M d, Y H:i') : '' }}
                            </p>
                            @if(!empty($signatureMeta['content_hash'] ?? null))
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest break-all">
                                    Ref: {{ substr($signatureMeta['content_hash'], 0, 16) }}
                                </p>
                            @endif
                            @if(empty($signatureImage))
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Signature image not available for this record</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Immunizations --}}
            @php
                $immunizations = $record->immunizations;
                if (is_string($immunizations)) {
                    $immunizations = json_decode($immunizations, true);
                }
            @endphp

            @if(!empty($immunizations) && is_array($immunizations))
                <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-indigo-100 transition-all duration-700">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-50 rounded-full -mr-20 -mt-20 opacity-40 group-hover:opacity-100 transition-opacity duration-1000"></div>
                    
                    <h3 class="relative z-10 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-10 flex items-center gap-4">
                        <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-inner border border-indigo-100 group-hover:rotate-12 transition-transform duration-700">
                            <i class="bi bi-shield-check-fill"></i>
                        </span>
                        Immunizations
                    </h3>

                    <div class="relative z-10 grid gap-6 sm:grid-cols-2">
                        @foreach($immunizations as $imm)
                            <div class="flex items-center p-6 bg-gray-50 border border-gray-100 rounded-[2rem] shadow-sm hover:bg-white hover:shadow-md transition-all group/imm">
                                <div class="w-14 h-14 rounded-2xl bg-white text-indigo-200 flex items-center justify-center mr-5 group-hover/imm:bg-indigo-600 group-hover/imm:text-white group-hover/imm:rotate-6 transition-all duration-500 shadow-sm border border-gray-100">
                                    <i class="bi bi-capsule-fill text-2xl"></i>
                                </div>
                                <div>
                                    @if(is_array($imm))
                                        <p class="font-black text-gray-900 text-base leading-tight uppercase tracking-tight">{{ $imm['name'] ?? 'Unknown Vaccine' }}</p>
                                        @if(!empty($imm['date']))
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5">
                                                Given: {{ \Carbon\Carbon::parse($imm['date'])->format('M d, Y') }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="font-black text-gray-900 text-base leading-tight uppercase tracking-tight">{{ $imm }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    @media print {
        .print\:hidden {
            display: none !important;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        html,
        body {
            background: #fff !important;
            font-family: Arial, Helvetica, sans-serif !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 11px !important;
            line-height: 1.35 !important;
        }

        .max-w-7xl {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            gap: 8px !important;
        }

        .space-y-8 > * + *,
        .space-y-6 > * + * {
            margin-top: 8px !important;
        }

        .grid.grid-cols-1.lg\:grid-cols-12 {
            display: grid !important;
            grid-template-columns: 34% 66% !important;
            gap: 8px !important;
            align-items: start !important;
        }

        .lg\:col-span-4,
        .lg\:col-span-8,
        .bg-white,
        .bg-gray-50,
        .bg-blue-50\/50,
        .bg-emerald-50\/50 {
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .rounded-xl,
        .rounded-lg,
        .rounded-\[2\.5rem\],
        .rounded-\[3rem\],
        .rounded-\[4rem\],
        .rounded-\[3\.5rem\] {
            border-radius: 6px !important;
        }

        .shadow-sm,
        .shadow-soft,
        .shadow-xl,
        .shadow-2xl {
            box-shadow: none !important;
        }

        .border,
        .border-gray-100,
        .border-blue-100,
        .border-emerald-100 {
            border-color: #d1d5db !important;
        }

        .p-5,
        .p-6,
        .p-8,
        .p-10,
        .p-12,
        .px-12,
        .py-10 {
            padding: 10px !important;
        }

        .min-h-\[200px\] {
            min-height: 90px !important;
        }

        .text-3xl,
        .text-4xl,
        .text-2xl,
        .text-xl {
            font-size: 15px !important;
            line-height: 1.25 !important;
        }

        .text-base,
        .text-sm {
            font-size: 11px !important;
            line-height: 1.35 !important;
        }

        .text-xs,
        .text-\[10px\],
        .text-\[9px\],
        .text-\[8px\],
        .text-\[7px\] {
            font-size: 9px !important;
            line-height: 1.3 !important;
            letter-spacing: 0.04em !important;
        }

        .absolute,
        .animate-pulse {
            display: none !important;
        }

        img[alt="Provider signature"] {
            max-height: 50px !important;
        }
    }
</style>
@endsection
