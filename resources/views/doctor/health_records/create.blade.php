@extends('layouts.app')

@section('title', 'Create Health Record')

@php
    $user = auth()->user();
    $routePrefix = $user->isMidwife() ? 'midwife' : ($user->isDoctor() ? 'doctor' : 'healthworker');
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{ 
        patientId: '{{ $selectedPatient?->id ?? '' }}',
        selectedPatientGender: '{{ $selectedPatient?->gender ?? '' }}',
        serviceId: '{{ old('service_id') ?? '' }}',
        serviceName: '',
        loading: false,
        patientInfo: null,
        history: [],
        weight: '',
        height: '',
        bmi: '',
        get currentGender() {
            return String(this.patientInfo?.gender || this.selectedPatientGender || '').toLowerCase();
        },
        isPrenatalServiceName(name) {
            const serviceName = String(name || '').toLowerCase();
            return serviceName.includes('prenatal') || serviceName.includes('antenatal') || serviceName.includes('obstetric');
        },
        isServiceAllowed(name) {
            if (!name) return true;
            if (this.currentGender === 'male' && this.isPrenatalServiceName(name)) {
                return false;
            }
            return true;
        },
        filterServiceOptions() {
            const select = this.$refs.serviceSelect;
            if (!select) return;

            Array.from(select.options).forEach((option) => {
                const optionServiceName = option.dataset.serviceName || '';
                if (!optionServiceName) return;
                const allowed = this.isServiceAllowed(optionServiceName);
                option.hidden = !allowed;
                option.disabled = !allowed;
            });

            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.disabled) {
                this.serviceId = '';
                this.serviceName = '';
                select.value = '';
            }
        },
        updateServiceName(el) {
            const selectedOption = el.options[el.selectedIndex];
            this.serviceName = selectedOption ? selectedOption.text.toUpperCase() : '';
            this.filterServiceOptions();
        },
        calculateBMI() {
            if(this.weight && this.height) {
                let h_m = this.height / 100;
                let bmi_val = this.weight / (h_m * h_m);
                this.bmi = bmi_val.toFixed(1);
            } else {
                this.bmi = '';
            }
        },
        fetchDetails() {
            if (!this.patientId) return;
            this.loading = true;
            fetch(`/healthworker/patients/${this.patientId}/details`)
                .then(res => res.json())
                .then(data => {
                    this.patientInfo = data.patient;
                    this.history = data.history;
                    this.filterServiceOptions();
                    this.loading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loading = false;
                });
        },
        init() {
            if(this.patientId) this.fetchDetails();
            this.$nextTick(() => {
                let select = this.$refs.serviceSelect;
                this.filterServiceOptions();
                if(select && select.value) this.updateServiceName(select);
            });
        }
     }">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route($routePrefix . '.health-records.index') }}" 
               class="bg-white p-2.5 rounded-xl shadow-sm text-gray-500 hover:text-brand-600 transition border border-gray-100">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900">New Medical Record</h1>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Consultation & Clinical Documentation</p>
            </div>
        </div>
    </div>

    <form action="{{ route($routePrefix . '.health-records.store') }}" method="POST">
        @csrf
        <input type="hidden" name="patient_id" :value="patientId">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Clinical Documentation -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Patient Info Card -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <i class="bi bi-person-bounding-box text-8xl"></i>
                    </div>
                    
                    <div class="flex flex-col md:flex-row md:items-center gap-6 relative z-10">
                        @if($selectedPatient)
                            <div class="w-20 h-20 rounded-3xl bg-brand-100 flex items-center justify-center text-brand-600 text-3xl font-black shadow-inner">
                                {{ substr($selectedPatient->first_name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-black text-gray-900 leading-tight">{{ $selectedPatient->full_name }}</h2>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm font-bold text-gray-500 uppercase tracking-wider">
                                    <span class="flex items-center gap-1"><i class="bi bi-fingerprint text-brand-500"></i> ID: {{ $selectedPatient->id }}</span>
                                    <span class="flex items-center gap-1"><i class="bi bi-gender-ambiguous text-brand-500"></i> {{ $selectedPatient->gender }}</span>
                                    <span class="flex items-center gap-1"><i class="bi bi-calendar3 text-brand-500"></i> {{ $selectedPatient->age }} Years Old</span>
                                </div>
                            </div>
                        @else
                            <div class="w-full">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Select Patient</label>
                                <select name="patient_id" x-model="patientId" @change="fetchDetails()" 
                                        class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition shadow-sm">
                                    <option value="">-- Choose a patient --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Medical Background Summary -->
                    <template x-if="patientInfo">
                        <div class="mt-8 pt-8 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 rounded-2xl bg-red-50/50 border border-red-100">
                                <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Known Allergies</p>
                                <p class="text-sm font-bold text-red-900" x-text="patientInfo.allergies || 'No known allergies'"></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Blood Type</p>
                                <p class="text-sm font-bold text-blue-900" x-text="patientInfo.blood_type || 'Unknown'"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Clinical Notes Card -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h3 class="text-lg font-black text-gray-900 flex items-center gap-3">
                            <i class="bi bi-file-earmark-medical-fill text-brand-600"></i>
                            Clinical Assessment
                        </h3>
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Record Date</label>
                                <input type="date" name="created_at" value="{{ now()->format('Y-m-d') }}"
                                       class="rounded-xl border-gray-100 bg-gray-50 p-2 text-xs font-bold focus:ring-brand-500 focus:border-brand-500 transition shadow-sm">
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Service Type</label>
                                <select name="service_id" x-model="serviceId" x-ref="serviceSelect" @change="updateServiceName($el)" required
                                        class="rounded-xl border-gray-100 bg-gray-50 p-2 text-xs font-black uppercase tracking-wider focus:ring-brand-500 focus:border-brand-500 transition shadow-sm">
                                    <option value="">Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-service-name="{{ $service->name }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Service Fields (PRENATAL) -->
                    <template x-if="serviceName.includes('PRENATAL')">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-3xl bg-brand-50/50 border border-brand-100 animate-in fade-in slide-in-from-top-4">
                            <div class="md:col-span-2 flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                                    <i class="bi bi-person-heart"></i>
                                </div>
                                <h4 class="text-sm font-black text-brand-900 uppercase tracking-widest">Obstetric History & Pregnancy Details</h4>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-brand-400 uppercase tracking-widest ml-1">LMP (Last Menstrual Period)</label>
                                <input type="date" name="metadata[lmp]" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-brand-500">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-brand-400 uppercase tracking-widest ml-1">EDD (Estimated Due Date)</label>
                                <input type="date" name="metadata[edd]" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-brand-500">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-brand-400 uppercase tracking-widest ml-1">Gravida (G)</label>
                                    <input type="number" name="metadata[gravida]" placeholder="0" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-brand-500 text-center">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-brand-400 uppercase tracking-widest ml-1">Para (P)</label>
                                    <input type="number" name="metadata[para]" placeholder="0" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-brand-500 text-center">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-brand-400 uppercase tracking-widest ml-1">Fetal Heart Tone (FHT)</label>
                                <div class="relative">
                                    <input type="text" name="metadata[fht]" placeholder="140" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-brand-500 pr-12">
                                    <span class="absolute right-4 top-3 text-[10px] font-black text-brand-400 uppercase">bpm</span>
                                </div>
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-[10px] font-black text-brand-400 uppercase tracking-widest ml-1">Previous Complications / Notes</label>
                                <textarea name="metadata[previous_complications]" rows="2" class="w-full rounded-xl border-white bg-white p-4 text-sm font-medium shadow-sm focus:ring-brand-500" placeholder="History of miscarriage, pre-eclampsia, etc."></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- Dynamic Service Fields (IMMUNIZATION) -->
                    <template x-if="serviceName.includes('IMMUNIZATION')">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-3xl bg-emerald-50/50 border border-emerald-100 animate-in fade-in slide-in-from-top-4">
                            <div class="md:col-span-2 flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i class="bi bi-shield-plus"></i>
                                </div>
                                <h4 class="text-sm font-black text-emerald-900 uppercase tracking-widest">Vaccination & Growth Tracking</h4>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest ml-1">Vaccine Antigen Given</label>
                                <input type="text" name="metadata[vaccine_given]" placeholder="e.g. Pentavalent, PCV" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-emerald-500">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest ml-1">Dose Number</label>
                                <select name="metadata[dose_no]" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-emerald-500">
                                    <option value="1">1st Dose</option>
                                    <option value="2">2nd Dose</option>
                                    <option value="3">3rd Dose</option>
                                    <option value="booster">Booster</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest ml-1">MUAC (cm)</label>
                                    <input type="number" step="0.1" name="metadata[muac]" placeholder="12.5" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-emerald-500 text-center">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest ml-1">Head Circ. (cm)</label>
                                    <input type="number" step="0.1" name="metadata[head_circ]" placeholder="45.0" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-emerald-500 text-center">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest ml-1">Remarks</label>
                                <input type="text" name="metadata[remarks]" placeholder="e.g. Well-baby checkup" class="w-full rounded-xl border-white bg-white p-3 text-sm font-bold shadow-sm focus:ring-emerald-500">
                            </div>
                        </div>
                    </template>

                    <!-- Consultation -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Consultation / Subjective</label>
                        <textarea name="consultation" rows="5" required
                                  class="w-full rounded-3xl border-gray-100 bg-gray-50/50 p-6 text-sm font-medium placeholder:text-gray-300 focus:ring-brand-500 focus:border-brand-500 transition leading-relaxed"
                                  placeholder="Patient's complaints, history of present illness, etc.">{{ old('consultation') }}</textarea>
                    </div>

                    <!-- Diagnosis -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Diagnosis / Assessment</label>
                        <textarea name="diagnosis" rows="3" required
                                  class="w-full rounded-3xl border-gray-100 bg-gray-50/50 p-6 text-sm font-medium placeholder:text-gray-300 focus:ring-brand-500 focus:border-brand-500 transition leading-relaxed"
                                  placeholder="Clinical findings and diagnosis...">{{ old('diagnosis') }}</textarea>
                    </div>

                    <!-- Treatment -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Treatment / Plan</label>
                        <textarea name="treatment" rows="4" required
                                  class="w-full rounded-3xl border-gray-100 bg-gray-50/50 p-6 text-sm font-medium placeholder:text-gray-300 focus:ring-brand-500 focus:border-brand-500 transition leading-relaxed"
                                  placeholder="Medications, procedures, advice, and follow-up plan...">{{ old('treatment') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Vitals & History -->
            <div class="space-y-8">
                
                <!-- Vital Signs Card -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-black text-gray-900 mb-8 flex items-center gap-3">
                        <i class="bi bi-activity text-red-500"></i>
                        Vital Signs
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Blood Pressure</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="bi bi-heart-pulse text-gray-300 group-focus-within:text-red-500 transition-colors"></i>
                                </div>
                                <input type="text" name="vital_signs[blood_pressure]" placeholder="120/80" 
                                       class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-black focus:ring-red-500 focus:border-red-500 transition shadow-sm">
                                <span class="absolute right-4 top-3.5 text-[10px] font-black text-gray-400 uppercase">mmHg</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Temp</label>
                            <div class="relative group">
                                <input type="number" step="0.1" name="vital_signs[temperature]" placeholder="36.5" 
                                       class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-black focus:ring-red-500 focus:border-red-500 transition shadow-sm text-center">
                                <span class="absolute right-3 top-3.5 text-xs font-bold text-gray-400">°C</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Heart Rate</label>
                            <div class="relative group">
                                <input type="number" name="vital_signs[pulse_rate]" placeholder="75" 
                                       class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-black focus:ring-red-500 focus:border-red-500 transition shadow-sm text-center">
                                <span class="absolute right-3 top-3.5 text-[10px] font-black text-gray-400 uppercase">bpm</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Weight</label>
                            <div class="relative group">
                                <input type="number" step="0.1" name="vital_signs[weight]" x-model="weight" @input="calculateBMI()" placeholder="70" 
                                       class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-black focus:ring-red-500 focus:border-red-500 transition shadow-sm text-center">
                                <span class="absolute right-3 top-3.5 text-[10px] font-black text-gray-400 uppercase">kg</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Height</label>
                            <div class="relative group">
                                <input type="number" name="vital_signs[height]" x-model="height" @input="calculateBMI()" placeholder="170" 
                                       class="w-full px-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-black focus:ring-red-500 focus:border-red-500 transition shadow-sm text-center">
                                <span class="absolute right-3 top-3.5 text-[10px] font-black text-gray-400 uppercase">cm</span>
                            </div>
                        </div>

                        <template x-if="bmi">
                            <div class="col-span-2 mt-4 p-4 rounded-2xl bg-brand-900 text-white flex items-center justify-between shadow-lg shadow-brand-200 animate-in fade-in slide-in-from-top-2">
                                <div>
                                    <p class="text-[10px] font-black text-brand-400 uppercase tracking-widest">Calculated BMI</p>
                                    <p class="text-2xl font-black" x-text="bmi"></p>
                                    <input type="hidden" name="vital_signs[bmi]" :value="bmi">
                                </div>
                                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-xl">
                                    <i class="bi bi-calculator"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- History Preview Card -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 overflow-hidden" x-show="history && history.length > 0">
                    <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-3">
                        <i class="bi bi-clock-history text-orange-500"></i>
                        Recent History
                    </h3>
                    
                    <div class="space-y-4">
                        <template x-for="record in history.slice(0, 3)" :key="record.id">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-brand-200 transition-all cursor-pointer group">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="record.created_at"></span>
                                </div>
                                <p class="text-xs font-bold text-gray-900 line-clamp-2 leading-relaxed" x-text="record.diagnosis"></p>
                                <div class="mt-3 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-[9px] font-black text-brand-600 uppercase tracking-widest">View Record</span>
                                    <i class="bi bi-arrow-right text-brand-600"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="space-y-4 pt-4">
                    <button type="submit" 
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-brand-200 transform transition hover:-translate-y-1 active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                        <i class="bi bi-check2-circle text-lg"></i>
                        Save Medical Record
                    </button>
                    <a href="{{ route($routePrefix . '.health-records.index') }}" 
                       class="w-full bg-white border border-gray-100 text-gray-400 hover:text-gray-600 font-black py-4 rounded-2xl text-center transition block uppercase tracking-widest text-[10px]">
                        Discard Entry
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
