@extends('layouts.app')

@section('title', 'Health Record Details')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    
    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ route('healthworker.health-records.index', ['subject_id' => $record->patient_id]) }}" 
           class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 transition font-medium text-sm">
            <i class="bi bi-arrow-left"></i> Back to Patient
        </a>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8 sm:p-10">
            
            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-10 pb-8 border-b border-gray-100">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        {{ $record->service ? $record->service->name : 'Consultation' }} Record
                    </h1>
                    <div class="flex flex-col gap-1 text-gray-500">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-calendar-event text-brand-500"></i>
                            <span class="font-medium text-gray-700">{{ $record->created_at->format('F d, Y') }}</span>
                            <span class="text-gray-400">•</span>
                            <span class="text-sm">{{ $record->created_at->format('h:i A') }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-gray-400">Patient:</span>
                            <span class="font-semibold text-gray-900">
                                {{ $record->patient->full_name }}
                            </span>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium ml-2">
                                {{ $record->patient->age }} yrs
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Verification Status (Hidden for Health Workers as per policy) --}}
            </div>

            {{-- Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                {{-- Left Column: Vital Signs --}}
                <div class="lg:col-span-1">
                    <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 h-full">
                        <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="bi bi-activity"></i>
                            </div>
                            Vital Signs
                        </h3>
                        
                        @if($record->vital_signs && is_array($record->vital_signs))
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($record->vital_signs as $label => $value)
                                    @if(!empty($value))
                                        <div class="flex items-center justify-between p-3 rounded-xl bg-white/60 border border-blue-100/50 shadow-sm transition hover:shadow-md">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">
                                                    {{ str_replace('_', ' ', $label) }}
                                                </span>
                                                <span class="text-sm font-black text-blue-900 leading-none">
                                                    {{ $value }}
                                                </span>
                                            </div>
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-400">
                                                @php
                                                    $icon = match($label) {
                                                        'blood_pressure' => 'bi-droplet-fill',
                                                        'temperature' => 'bi-thermometer-half',
                                                        'weight' => 'bi-scale',
                                                        'height_cm' => 'bi-rulers',
                                                        'bmi' => 'bi-person-badge',
                                                        'pulse_rate' => 'bi-heart-pulse',
                                                        'respiratory_rate' => 'bi-wind',
                                                        'oxygen_saturation' => 'bi-lungs',
                                                        default => 'bi-activity'
                                                    };
                                                @endphp
                                                <i class="bi {{ $icon }} text-xs"></i>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif($record->vital_signs && is_string($record->vital_signs))
                            <div class="font-mono text-sm text-blue-900 leading-relaxed space-y-2">
                                @foreach(explode("\n", $record->vital_signs) as $line)
                                    @if(trim($line))
                                        <div class="flex items-start gap-2 bg-white/60 p-2 rounded-lg">
                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-2 flex-shrink-0"></div>
                                            <span>{{ $line }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 text-blue-300 italic">
                                <i class="bi bi-activity text-4xl mb-2 block opacity-50"></i>
                                No vital signs recorded
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Clinical Details --}}
                <div class="lg:col-span-2 space-y-10">

                    {{-- Service Specific Metadata --}}
                    @if($record->metadata && count($record->metadata) > 0)
                        <div class="p-6 bg-teal-50 rounded-xl border border-teal-100">
                            <h3 class="text-lg font-bold text-teal-900 mb-4 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center text-teal-600">
                                    <i class="bi bi-clipboard-data"></i>
                                </div>
                                Specific Details
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                @foreach($record->metadata as $key => $value)
                                    <div>
                                        <span class="block text-xs font-bold text-teal-600 uppercase mb-1">
                                            {{ ucwords(str_replace('_', ' ', $key)) }}
                                        </span>
                                        <span class="text-teal-900 font-medium">
                                            {{ $value }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- Consultation Notes --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Consultation Notes</h4>
                        <div class="text-gray-800 text-lg leading-relaxed font-medium">
                            @if($record->consultation)
                                {{ $record->consultation }}
                            @else
                                <span class="text-gray-400 italic font-normal">No notes recorded.</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-8">
                        {{-- Diagnosis --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Diagnosis</h4>
                            <div class="text-gray-800 leading-relaxed">
                                @if($record->diagnosis)
                                    {{ $record->diagnosis }}
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </div>
                        </div>

                        {{-- Treatment --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Treatment</h4>
                            <div class="text-gray-800 leading-relaxed">
                                @if($record->treatment)
                                    {{ $record->treatment }}
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Immunizations --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Immunizations</h4>
                        @php
                            $immunizations = $record->immunizations;
                            if (is_string($immunizations)) {
                                $immunizations = json_decode($immunizations, true);
                            }
                        @endphp

                        @if(!empty($immunizations) && is_array($immunizations))
                            <div class="flex flex-wrap gap-3">
                                @foreach($immunizations as $imm)
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 text-sm font-medium">
                                        <i class="bi bi-capsule text-brand-500"></i>
                                        @if(is_array($imm))
                                            <span>{{ $imm['name'] ?? 'Unknown' }}</span>
                                            @if(!empty($imm['date']))
                                                <span class="text-gray-400 text-xs border-l border-gray-200 pl-2 ml-1">
                                                    {{ \Carbon\Carbon::parse($imm['date'])->format('M d, Y') }}
                                                </span>
                                            @endif
                                        @else
                                            <span>{{ $imm }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-gray-400 italic">No immunizations recorded.</div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Footer Meta --}}
            <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-400">
                <div class="flex items-center gap-2">
                    <span>Recorded by:</span>
                    <span class="font-medium text-gray-600 flex items-center gap-1.5">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($record->creator->full_name) }}&background=random&size=20" class="w-5 h-5 rounded-full" alt="">
                        {{ $record->creator->full_name ?? 'Unknown' }}
                    </span>
                    @if($record->creator->isHealthWorker())
                        <span class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Health Worker</span>
                    @endif
                </div>
                
                {{-- Hidden verification footer for HW view to simplify UI --}}
            </div>

        </div>
    </div>
</div>
@endsection
