@extends('layouts.app')

@section('title', 'Edit Health Record')

@php
    $user = auth()->user();
    $routePrefix = $user->isMidwife() ? 'midwife' : ($user->isDoctor() ? 'doctor' : 'healthworker');
@endphp

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10"
     x-data="{ 
        weight: '{{ old('vital_signs.weight', is_array($record->vital_signs) ? ($record->vital_signs['weight'] ?? '') : '') }}',
        height: '{{ old('vital_signs.height', is_array($record->vital_signs) ? ($record->vital_signs['height'] ?? '') : '') }}',
        bmi: '{{ old('vital_signs.bmi', is_array($record->vital_signs) ? ($record->vital_signs['bmi'] ?? '') : '') }}',
        calculateBMI() {
            if(this.weight && this.height) {
                let h_m = this.height / 100;
                let bmi_val = this.weight / (h_m * h_m);
                this.bmi = bmi_val.toFixed(1);
            } else {
                this.bmi = '';
            }
        }
     }">

    {{-- Breadcrumbs --}}
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-[0.2em]">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center text-gray-400">
                    <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                    <a href="{{ route($routePrefix . '.health-records.index') }}" class="hover:text-brand-600 transition-colors">Health Records</a>
                </div>
            </li>
            <li>
                <div class="flex items-center text-gray-400">
                    <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                    <span class="text-brand-600">Edit Record</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="bg-white rounded-[3.5rem] p-10 lg:p-14 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-50 rounded-full blur-3xl -mr-48 -mt-48 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-50 rounded-full blur-3xl -ml-32 -mb-32 opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-12">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-2xl bg-brand-50 text-brand-600 border border-brand-100 mb-6">
                    <i class="bi bi-pencil-square text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Clinical Editor</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tight leading-[1.1]">
                    Edit <span class="text-brand-600 underline decoration-brand-200 decoration-8 underline-offset-4">Medical Record</span>
                </h1>
                <p class="text-gray-500 font-medium mt-6 text-lg leading-relaxed">
                    Update clinical findings, vital signs, and treatment plans for <span class="text-gray-900 font-black">{{ $record->patient->full_name }}</span>.
                </p>
            </div>
            
            <div class="flex flex-col gap-4 w-full lg:w-auto">
                <div class="bg-gray-50/80 backdrop-blur-sm p-6 rounded-[2.5rem] border border-gray-100 shadow-inner flex items-center gap-4 transform hover:scale-105 transition-all duration-500">
                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-brand-600 shadow-sm border border-brand-50">
                        <i class="bi bi-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Record Date</p>
                        <p class="text-lg font-black text-gray-900 tracking-tight">{{ $record->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route($routePrefix . '.health-records.update', $record->id) }}" method="POST" class="space-y-10">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="patient_id" value="{{ $record->patient_id }}">
        <input type="hidden" name="status" value="{{ $record->status }}">
        <input type="hidden" name="created_at" value="{{ $record->created_at }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            {{-- Left Column: Vital Signs --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 sticky top-10">
                    <h3 class="text-lg font-black text-gray-900 mb-8 flex items-center gap-3">
                        <i class="bi bi-activity text-red-500"></i>
                        Vital Signs
                    </h3>

                    @if(!is_array($record->vital_signs) && !empty($record->vital_signs))
                        <div class="mb-6 p-4 rounded-2xl bg-orange-50 border border-orange-100 text-[10px] font-bold text-orange-700 uppercase tracking-widest leading-relaxed">
                            <i class="bi bi-info-circle-fill mr-1"></i> Legacy text data detected. Updating will convert this to structured data.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Blood Pressure</label>
                            <div class="relative group">
                                <input type="text" name="vital_signs[blood_pressure]" 
                                       value="{{ old('vital_signs.blood_pressure', is_array($record->vital_signs) ? ($record->vital_signs['blood_pressure'] ?? '') : '') }}"
                                       placeholder="120/80" 
                                       class="w-full pl-6 pr-12 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-4 focus:ring-red-50 focus:bg-white transition shadow-inner">
                                <span class="absolute right-4 top-4 text-[10px] font-black text-gray-300 uppercase">mmHg</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Temp</label>
                                <div class="relative group">
                                    <input type="number" step="0.1" name="vital_signs[temperature]" 
                                           value="{{ old('vital_signs.temperature', is_array($record->vital_signs) ? ($record->vital_signs['temperature'] ?? '') : '') }}"
                                           placeholder="36.5" 
                                           class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-4 focus:ring-red-50 focus:bg-white transition shadow-inner text-center">
                                    <span class="absolute right-3 top-4 text-xs font-bold text-gray-300">°C</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Heart Rate</label>
                                <div class="relative group">
                                    <input type="number" name="vital_signs[pulse_rate]" 
                                           value="{{ old('vital_signs.pulse_rate', is_array($record->vital_signs) ? ($record->vital_signs['pulse_rate'] ?? '') : '') }}"
                                           placeholder="75" 
                                           class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-4 focus:ring-red-50 focus:bg-white transition shadow-inner text-center">
                                    <span class="absolute right-3 top-4 text-[10px] font-black text-gray-300 uppercase">bpm</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Weight</label>
                                <div class="relative group">
                                    <input type="number" step="0.1" name="vital_signs[weight]" x-model="weight" @input="calculateBMI()"
                                           placeholder="70" 
                                           class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-4 focus:ring-red-50 focus:bg-white transition shadow-inner text-center">
                                    <span class="absolute right-3 top-4 text-[10px] font-black text-gray-300 uppercase">kg</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Height</label>
                                <div class="relative group">
                                    <input type="number" name="vital_signs[height]" x-model="height" @input="calculateBMI()"
                                           placeholder="170" 
                                           class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-4 focus:ring-red-50 focus:bg-white transition shadow-inner text-center">
                                    <span class="absolute right-3 top-4 text-[10px] font-black text-gray-300 uppercase">cm</span>
                                </div>
                            </div>
                        </div>

                        <template x-if="bmi">
                            <div class="mt-4 p-6 rounded-3xl bg-brand-900 text-white flex items-center justify-between shadow-xl shadow-brand-100 animate-in fade-in slide-in-from-top-2">
                                <div>
                                    <p class="text-[10px] font-black text-brand-400 uppercase tracking-widest">Calculated BMI</p>
                                    <p class="text-3xl font-black" x-text="bmi"></p>
                                    <input type="hidden" name="vital_signs[bmi]" :value="bmi">
                                </div>
                                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-2xl">
                                    <i class="bi bi-calculator"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right Column: Clinical Details --}}
            <div class="lg:col-span-2 space-y-10">
                <div class="bg-white rounded-[3.5rem] p-8 lg:p-12 shadow-sm border border-gray-100 space-y-10">
                    
                    {{-- Consultation Notes --}}
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Consultation / Subjective</label>
                        <textarea name="consultation" rows="6" required
                                  class="w-full rounded-[2.5rem] border-none bg-gray-50/50 p-8 text-lg font-medium placeholder:text-gray-300 focus:ring-4 focus:ring-brand-50 focus:bg-white transition leading-relaxed shadow-inner"
                                  placeholder="Patient's complaints, history of present illness, etc.">{{ old('consultation', $record->consultation) }}</textarea>
                    </div>

                    {{-- Diagnosis --}}
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Diagnosis / Assessment</label>
                        <textarea name="diagnosis" rows="3" required
                                  class="w-full rounded-[2.5rem] border-none bg-gray-50/50 p-8 text-lg font-medium placeholder:text-gray-300 focus:ring-4 focus:ring-brand-50 focus:bg-white transition leading-relaxed shadow-inner"
                                  placeholder="Clinical findings and diagnosis...">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                    </div>

                    {{-- Treatment --}}
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Treatment / Plan</label>
                        <textarea name="treatment" rows="4" required
                                  class="w-full rounded-[2.5rem] border-none bg-gray-50/50 p-8 text-lg font-medium placeholder:text-gray-300 focus:ring-4 focus:ring-brand-50 focus:bg-white transition leading-relaxed shadow-inner"
                                  placeholder="Medications, procedures, advice, and follow-up plan...">{{ old('treatment', $record->treatment) }}</textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-10 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-3 bg-brand-50/50 px-5 py-3 rounded-2xl border border-brand-100/50">
                            <i class="bi bi-info-circle-fill text-brand-500"></i>
                            <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest">Record will be saved with current timestamp</p>
                        </div>
                        
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <a href="{{ route($routePrefix . '.health-records.show', $record->id) }}" 
                               class="flex-1 sm:flex-none px-10 py-5 bg-white text-gray-500 rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] border border-gray-200 hover:bg-gray-50 transition-all text-center">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="flex-1 sm:flex-none px-10 py-5 bg-brand-600 text-white rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all active:scale-95 flex items-center justify-center gap-3">
                                Update Record <i class="bi bi-check-circle-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
