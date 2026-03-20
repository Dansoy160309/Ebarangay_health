@extends('layouts.app')

@section('title', 'Add Health Record')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            @php
                $backRoute = auth()->user()->isAdmin() ? route('admin.health-records.index') : route('healthworker.health-records.index');
            @endphp
            <a href="{{ $backRoute }}" class="bg-white p-2.5 rounded-xl shadow-sm text-gray-500 hover:text-brand-600 transition border border-gray-100">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Create Health Record</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Consultation & Clinical Documentation</p>
            </div>
        </div>
        <div class="hidden sm:flex flex-col items-end">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 mr-1">Record Date</label>
            <input type="date" name="created_at" form="healthRecordForm" value="{{ now()->format('Y-m-d') }}"
                   class="rounded-xl border-gray-100 bg-white p-2 text-xs font-bold focus:ring-brand-500 focus:border-brand-500 transition shadow-sm">
        </div>
    </div>

    @php
        $storeRoute = auth()->user()->isAdmin() ? route('admin.health-records.store') : route('healthworker.health-records.store');
    @endphp

    <form method="POST" action="{{ $storeRoute }}" id="healthRecordForm"
          x-data="{ 
            patientId: '{{ old('patient_id') ?? request('patient_id') }}',
            serviceId: '{{ old('service_id') ?? request('service_id') }}',
            appointmentId: '{{ request('appointment_id') }}',
            passToDoctor: {{ request('appointment_id') ? 'true' : 'false' }},
            services: {{ Js::from($services) }},
            patientInfo: null,
            history: [],
            loading: false,
            bp: '', 
            temp: '', 
            hr: '', 
            rr: '', 
            o2: '', 
            weight: '', 
            height: '',
            bmi: '',
            prenatal: {
                lmp: '',
                edd: '',
                gravida: '',
                para: '',
                miscarriage: false,
                complications: ''
            },
            immunization: {
                condition: 'Healthy',
                allergies: '',
                prev_reaction: false
            },
            get selectedService() {
                return this.services.find(s => s.id == this.serviceId);
            },
            get isPrenatal() {
                return this.selectedService?.name.toLowerCase().includes('prenatal') || this.selectedService?.name.toLowerCase().includes('pre-natal');
            },
            get isImmunization() {
                return this.selectedService?.name.toLowerCase().includes('immunization') || this.selectedService?.name.toLowerCase().includes('vaccin');
            },
            calculateEDD() {
                if (this.prenatal.lmp) {
                    let lmpDate = new Date(this.prenatal.lmp);
                    let eddDate = new Date(lmpDate);
                    eddDate.setDate(lmpDate.getDate() + 280); // Naegele's rule approximation (9 months + 7 days)
                    this.prenatal.edd = eddDate.toISOString().split('T')[0];
                }
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
                if (!this.patientId) {
                    this.patientInfo = null;
                    this.history = [];
                    return;
                }
                this.loading = true;
                fetch(`/healthworker/patients/${this.patientId}/details`)
                    .then(res => res.json())
                    .then(data => {
                        this.patientInfo = data.patient;
                        this.history = data.history;
                        this.loading = false;
                    })
                    .catch(err => {
                        console.error(err);
                        this.loading = false;
                    });
            },
            init() {
                if(this.patientId) this.fetchDetails();
            }
        }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Patient & Consultation -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Patient Selection Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600">
                            <i class="bi bi-person"></i>
                        </div>
                        Patient Information
                    </h3>
                    
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Patient Name</label>
                        
                        @if(isset($selectedPatient) && $selectedPatient)
                            {{-- Read-only Display --}}
                            <div class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-gray-800 font-medium flex items-center justify-between">
                                <span>{{ $selectedPatient->full_name }}</span>
                                <span class="text-xs bg-brand-100 text-brand-700 px-2 py-1 rounded-full">Selected</span>
                            </div>
                            <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}" x-model="patientId">
                        @else
                            {{-- No Patient Selected --}}
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <i class="bi bi-person-x text-xl"></i>
                                </div>
                                <h3 class="text-gray-900 font-medium mb-1">No Patient Selected</h3>
                                <p class="text-sm text-gray-500 mb-1">Please select a patient from the health records search page.</p>
                            </div>
                        @endif

                        @error('patient_id')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Patient Details (Dynamic) -->
                    <div class="mt-6 pt-4 border-t border-gray-100" x-show="patientInfo" x-transition>
                        <div class="grid grid-cols-2 gap-y-3 text-sm">
                            <div>
                                <span class="text-gray-500 block text-xs uppercase font-bold">Full Name</span>
                                <span class="font-medium text-gray-800" x-text="patientInfo?.full_name"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-xs uppercase font-bold">Age / Gender</span>
                                <span class="font-medium text-gray-800"><span x-text="patientInfo?.age"></span> / <span x-text="patientInfo?.gender"></span></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-xs uppercase font-bold">Birthdate</span>
                                <span class="font-medium text-gray-800" x-text="patientInfo?.dob"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-xs uppercase font-bold">Contact</span>
                                <span class="font-medium text-gray-800" x-text="patientInfo?.contact_no"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500 block text-xs uppercase font-bold">Address</span>
                                <span class="font-medium text-gray-800" x-text="patientInfo?.address"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Selection Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        Service Selection
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="service in services" :key="service.id">
                            <label class="relative flex items-center p-4 border rounded-xl transition-all duration-200 cursor-pointer hover:bg-gray-50"
                                :class="serviceId == service.id ? 'border-brand-500 ring-2 ring-brand-200 bg-brand-50' : 'border-gray-200'">
                                <input type="radio" name="service_id" :value="service.id" x-model="serviceId" class="sr-only">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900" x-text="service.name"></span>
                                        <span x-show="serviceId == service.id" class="text-brand-600">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" x-text="service.description"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                    @error('service_id')
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Medical Background (Dynamic) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" x-show="patientInfo" x-transition>
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                             <i class="bi bi-file-earmark-medical"></i>
                        </div>
                        Medical Background
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                             <span class="text-gray-500 block text-xs uppercase font-bold mb-1">Blood Type</span>
                             <span class="font-medium text-gray-800 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200 inline-block" x-text="patientInfo?.blood_type"></span>
                        </div>
                        <div>
                             <span class="text-gray-500 block text-xs uppercase font-bold mb-1">Allergies</span>
                             <p class="font-medium text-gray-800 text-sm" x-text="patientInfo?.allergies"></p>
                        </div>
                        <div class="col-span-1 md:col-span-2">
                             <span class="text-gray-500 block text-xs uppercase font-bold mb-1">Medical History</span>
                             <p class="font-medium text-gray-800 text-sm bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed" x-text="patientInfo?.medical_history"></p>
                        </div>
                        <div class="col-span-1 md:col-span-2">
                             <span class="text-gray-500 block text-xs uppercase font-bold mb-1">Current Medications</span>
                             <p class="font-medium text-gray-800 text-sm bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed" x-text="patientInfo?.current_medications"></p>
                        </div>
                         <div class="col-span-1 md:col-span-2">
                             <span class="text-gray-500 block text-xs uppercase font-bold mb-1">Family History</span>
                             <p class="font-medium text-gray-800 text-sm bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed" x-text="patientInfo?.family_history"></p>
                        </div>
                    </div>
                </div>

                <!-- Previous History (Dynamic) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" x-show="history && history.length > 0" x-transition>
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                             <i class="bi bi-clock-history"></i>
                        </div>
                        Previous History
                    </h3>
                    <div class="space-y-4">
                        <template x-for="record in history" :key="record.id">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition group">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-sm font-bold text-gray-800" x-text="record.created_at"></span>
                                </div>
                                <div class="text-sm text-gray-600 mb-2 line-clamp-2" x-text="record.diagnosis"></div>
                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <span>Attended by: <span class="font-medium text-gray-700" x-text="record.creator"></span></span>
                                    <a :href="`/healthworker/health-records/${record.id}`" target="_blank" class="text-brand-600 hover:underline group-hover:text-brand-700">View Details &rarr;</a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Service Specific Details -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" x-show="serviceId" x-transition>
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                        <span x-text="selectedService?.name + ' Details'"></span>
                    </h3>

                    <!-- Prenatal Fields -->
                    <div x-show="isPrenatal" class="space-y-8 animate-in fade-in slide-in-from-top-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                                    <i class="bi bi-person-heart"></i>
                                </div>
                                <h4 class="text-sm font-black text-brand-900 uppercase tracking-widest">Pregnancy & Obstetric History</h4>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Last Menstrual Period (LMP)</label>
                                <input type="date" name="metadata[lmp]" x-model="prenatal.lmp" @change="calculateEDD()"
                                    class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Estimated Date of Delivery (EDD)</label>
                                <input type="date" name="metadata[edd]" x-model="prenatal.edd" readonly
                                    class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-brand-50/30 font-black text-brand-700">
                                <p class="text-[9px] text-brand-500 font-bold mt-1 uppercase tracking-widest"><i class="bi bi-info-circle"></i> Auto-computed based on LMP</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Gravida (G)</label>
                                    <input type="number" name="metadata[gravida]" x-model="prenatal.gravida" min="1"
                                        class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold" placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Para (P)</label>
                                    <input type="number" name="metadata[para]" x-model="prenatal.para" min="0"
                                        class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold" placeholder="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Fetal Heart Tone (FHT)</label>
                                <div class="relative">
                                    <input type="text" name="metadata[fht]" placeholder="140" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold pr-12">
                                    <span class="absolute right-4 top-2.5 text-[10px] font-black text-gray-400 uppercase">bpm</span>
                                </div>
                            </div>
                        </div>

                        <div class="h-px bg-gray-100"></div>

                        <div class="space-y-4">
                            <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-colors"
                                :class="prenatal.miscarriage ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="checkbox" name="metadata[history_miscarriage]" x-model="prenatal.miscarriage" value="1" class="w-5 h-5 text-red-600 rounded focus:ring-red-500 border-gray-300">
                                <div>
                                    <span class="font-bold text-gray-900 block text-sm">History of Miscarriage</span>
                                    <span class="text-xs text-gray-500 block">Check if the patient has had any previous miscarriages</span>
                                </div>
                            </label>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Previous Complications / Risk Factors</label>
                                <textarea name="metadata[previous_complications]" x-model="prenatal.complications" rows="2" 
                                    class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold" 
                                    placeholder="e.g. Pre-eclampsia, gestational diabetes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Immunization Fields -->
                    <div x-show="isImmunization" class="space-y-8 animate-in fade-in slide-in-from-top-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i class="bi bi-shield-plus"></i>
                                </div>
                                <h4 class="text-sm font-black text-emerald-900 uppercase tracking-widest">Vaccination & Growth Monitoring</h4>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Vaccine Antigen Given</label>
                                <input type="text" name="metadata[vaccine_given]" placeholder="e.g. Pentavalent, PCV"
                                    class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Dose Number</label>
                                <select name="metadata[dose_no]" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold">
                                    <option value="1">1st Dose</option>
                                    <option value="2">2nd Dose</option>
                                    <option value="3">3rd Dose</option>
                                    <option value="booster">Booster</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">MUAC (cm)</label>
                                    <input type="number" step="0.1" name="metadata[muac]" placeholder="12.5"
                                        class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold text-center">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Head Circ. (cm)</label>
                                    <input type="number" step="0.1" name="metadata[head_circ]" placeholder="45.0"
                                        class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold text-center">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">General Condition</label>
                                <select name="metadata[general_condition]" x-model="immunization.condition"
                                    class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold">
                                    <option value="Healthy">Healthy</option>
                                    <option value="With Fever">With Fever</option>
                                    <option value="Sick">Sick</option>
                                </select>
                            </div>
                        </div>

                        <template x-if="temp > 37.5 && isImmunization">
                            <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-700">
                                <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                                <p class="text-xs font-black uppercase tracking-widest leading-relaxed">
                                    Warning: High temperature detected. Clinical guidelines recommend delaying vaccination if fever is present.
                                </p>
                            </div>
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 uppercase tracking-widest text-[10px] font-black">Remarks / Adverse Reactions</label>
                                <textarea name="metadata[remarks]" x-model="immunization.remarks" rows="2" 
                                    class="w-full border-gray-200 rounded-xl shadow-sm focus:border-brand-500 focus:ring-brand-500 bg-gray-50 font-bold" 
                                    placeholder="e.g. No reaction from previous dose, well-baby..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Default/Other Service Fields -->
                    <div x-show="!isPrenatal && !isImmunization" class="text-gray-500 text-sm italic bg-gray-50 p-4 rounded-lg">
                        <p class="flex items-center gap-2">
                            <i class="bi bi-info-circle"></i>
                            Please enter clinical details in the notes section below.
                        </p>
                    </div>
                </div>

                <!-- Consultation Notes Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                            <i class="bi bi-file-medical"></i>
                        </div>
                        Clinical Notes
                    </h3>
                    
                    <div>
                        <label for="consultation" class="block text-sm font-medium text-gray-700 mb-2">Consultation Details</label>
                        <textarea name="consultation" id="consultation" rows="6" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 leading-relaxed" placeholder="Enter chief complaint and history of present illness...">{{ old('consultation') }}</textarea>
                        @error('consultation')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                        <textarea name="diagnosis" id="diagnosis" rows="3" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 leading-relaxed" placeholder="Enter diagnosis...">{{ old('diagnosis') }}</textarea>
                        @error('diagnosis')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="treatment" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                        <textarea name="treatment" id="treatment" rows="3" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 leading-relaxed" placeholder="Enter treatment plan...">{{ old('treatment') }}</textarea>
                        @error('treatment')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Right Column: Vitals & Immunization -->
            <div class="space-y-8">
                <!-- Vital Signs Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i class="bi bi-activity"></i>
                        </div>
                        Vital Signs
                    </h3>
                    
                    <div class="space-y-4">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Blood Pressure -->
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Blood Pressure</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-heart-pulse text-gray-400"></i>
                                    </div>
                                    <input type="text" name="vital_signs[blood_pressure]" x-model="bp" placeholder="120/80" class="w-full pl-10 pr-12 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">mmHg</span>
                                </div>
                            </div>

                            <!-- Temperature -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Temperature</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-thermometer-half text-gray-400"></i>
                                    </div>
                                    <input type="number" step="0.1" name="vital_signs[temperature]" x-model="temp" placeholder="36.5" class="w-full pl-10 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">°C</span>
                                </div>
                            </div>

                            <!-- Heart Rate -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Heart Rate</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-activity text-gray-400"></i>
                                    </div>
                                    <input type="number" name="vital_signs[pulse_rate]" x-model="hr" placeholder="75" class="w-full pl-10 pr-10 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">bpm</span>
                                </div>
                            </div>

                            <!-- Respiratory Rate -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Resp. Rate</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-lungs text-gray-400"></i>
                                    </div>
                                    <input type="number" name="vital_signs[respiratory_rate]" x-model="rr" placeholder="16" class="w-full pl-10 pr-10 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">cpm</span>
                                </div>
                            </div>

                            <!-- Oxygen Saturation -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Oxygen Sat.</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-droplet text-gray-400"></i>
                                    </div>
                                    <input type="number" name="vital_signs[oxygen_saturation]" x-model="o2" placeholder="98" class="w-full pl-10 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">%</span>
                                </div>
                            </div>

                            <!-- Weight -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Weight</label>
                                <div class="relative">
                                    <input type="number" step="0.1" name="vital_signs[weight]" x-model="weight" @input="calculateBMI()" placeholder="70" class="w-full pl-3 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">kg</span>
                                </div>
                            </div>

                            <!-- Height -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Height</label>
                                <div class="relative">
                                    <input type="number" name="vital_signs[height]" x-model="height" @input="calculateBMI()" placeholder="170" class="w-full pl-3 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">cm</span>
                                </div>
                            </div>

                            <!-- BMI Display -->
                            <div class="col-span-2" x-show="bmi">
                                <input type="hidden" name="vital_signs[bmi]" x-model="bmi">
                                <div class="bg-blue-50 rounded-lg p-3 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-blue-700">BMI</span>
                                    <span class="text-lg font-bold text-blue-800" x-text="bmi"></span>
                                </div>
                            </div>
                        </div>

                        @error('vital_signs')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Pass to Doctor Action -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        Next Steps
                    </h3>

                    <input type="hidden" name="appointment_id" value="{{ request('appointment_id') }}">

                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-colors"
                        :class="passToDoctor ? 'border-brand-500 bg-brand-50' : 'border-gray-200 hover:bg-gray-50'">
                        <input type="checkbox" name="pass_to_doctor" value="1" x-model="passToDoctor" class="mt-1 w-5 h-5 text-brand-600 rounded focus:ring-brand-500 border-gray-300">
                        <div>
                            <span class="font-semibold text-gray-900 block">Pass to Doctor for Consultation</span>
                            <span class="text-sm text-gray-500 block mt-1">
                                This will create an appointment (if none exists) and notify the doctor.
                            </span>
                        </div>
                    </label>

                    <div x-show="passToDoctor && !appointmentId" x-transition class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Doctor</label>
                        <select name="doctor_id" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">-- Select a Doctor --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">Dr. {{ $doctor->full_name }}</option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <!-- If Appointment Exists, show info -->
                    <div x-show="appointmentId && passToDoctor" class="mt-4 p-3 bg-blue-50 text-blue-700 rounded-lg text-sm flex items-center gap-2">
                        <i class="bi bi-info-circle-fill"></i>
                        Linked to Appointment #<span x-text="appointmentId"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                        <i class="bi bi-check2-circle text-lg"></i> Save Record
                    </button>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.health-records.index') : route('healthworker.health-records.index') }}" class="w-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium py-3 rounded-xl text-center transition">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
