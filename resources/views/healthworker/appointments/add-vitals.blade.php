@extends('layouts.app')

@section('title', 'Add Vital Signs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{ 
        weight: '{{ old('weight') }}', 
        height: '{{ old('height_cm') }}', 
        temp: '{{ old('temperature') }}',
        bmi: '',
        service: '{{ strtolower($appointment->service) }}',
        get isPrenatal() {
            return this.service.includes('prenatal') || this.service.includes('pre-natal');
        },
        get isImmunization() {
            return this.service.includes('immunization') || this.service.includes('vaccin');
        },
        prenatal: {
            lmp: '{{ old('metadata.lmp') }}',
            edd: '{{ old('metadata.edd') }}',
            gravida: '{{ old('metadata.gravida') }}',
            para: '{{ old('metadata.para') }}',
            miscarriage: {{ old('metadata.history_miscarriage') ? 'true' : 'false' }},
            complications: '{{ old('metadata.previous_complications') }}'
        },
        immunization: {
            condition: '{{ old('metadata.general_condition', 'Healthy') }}',
            allergies: '{{ old('metadata.allergy_history') }}',
            prev_reaction: {{ old('metadata.previous_vaccine_reaction') ? 'true' : 'false' }}
        },
        calculateEDD() {
            if (this.prenatal.lmp) {
                let lmpDate = new Date(this.prenatal.lmp);
                let eddDate = new Date(lmpDate);
                eddDate.setDate(lmpDate.getDate() + 280); 
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
        init() {
            this.calculateBMI();
        }
     }">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('healthworker.dashboard') }}" 
               class="bg-white p-2.5 rounded-xl shadow-sm text-gray-500 hover:text-brand-600 transition border border-gray-100">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Capture Vital Signs</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Pre-Consultation Assessment</p>
            </div>
        </div>
    </div>

    <form action="{{ route('healthworker.appointments.store-vitals', $appointment) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Patient Profile & Context -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Patient Card -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <i class="bi bi-person-bounding-box text-8xl"></i>
                    </div>
                    
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div class="w-20 h-20 rounded-3xl bg-brand-100 flex items-center justify-center text-brand-600 text-3xl font-black shadow-inner mb-4">
                            {{ substr($appointment->user->first_name, 0, 1) }}
                        </div>
                        <h2 class="text-xl font-black text-gray-900 leading-tight">{{ $appointment->user->full_name }}</h2>
                        <div class="flex flex-wrap items-center justify-center gap-3 mt-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <span class="flex items-center gap-1 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100">
                                <i class="bi bi-gender-ambiguous text-brand-500"></i> {{ $appointment->user->gender }}
                            </span>
                            <span class="flex items-center gap-1 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100">
                                <i class="bi bi-calendar3 text-brand-500"></i> {{ $appointment->user->age }} Years
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100 space-y-4">
                        <div class="p-4 rounded-2xl bg-brand-50/50 border border-brand-100">
                            <p class="text-[10px] font-black text-brand-400 uppercase tracking-widest mb-1">Assigned Service</p>
                            <p class="text-sm font-bold text-brand-900">{{ $appointment->service }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Scheduled Time</p>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $appointment->scheduled_at->format('h:i A') }}
                                @if($appointment->slot && $appointment->slot->end_time)
                                    - {{ $appointment->slot->end_time->format('h:i A') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="bg-blue-600 rounded-[2rem] p-8 text-white shadow-xl shadow-blue-200 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <i class="bi bi-info-circle text-9xl"></i>
                    </div>
                    <h4 class="font-black uppercase tracking-widest text-[10px] mb-3 opacity-80">Clinician Note</h4>
                    <p class="text-sm font-bold leading-relaxed">
                        Recording accurate vitals is critical for the {{ strtolower($serviceProviderLabel) }} to make informed medical decisions.
                    </p>
                </div>
            </div>

            <!-- Right Column: Vitals Form -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-black text-gray-900 mb-10 flex items-center gap-3">
                        <i class="bi bi-activity text-red-500"></i>
                        Clinical Vital Signs
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Prenatal Assessment Fields (Dynamic) -->
                        <template x-if="isPrenatal">
                            <div class="col-span-2 space-y-8 bg-brand-50/30 p-8 rounded-[2rem] border border-brand-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl bg-brand-600 flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                                        <i class="bi bi-calendar-heart-fill"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest">Obstetric History</h4>
                                        <p class="text-[9px] font-bold text-brand-600 uppercase tracking-widest">Pregnancy & Delivery Profile</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Last Menstrual Period (LMP)</label>
                                        <input type="date" name="metadata[lmp]" x-model="prenatal.lmp" @change="calculateEDD()"
                                            class="w-full px-5 py-4 bg-white border-gray-100 rounded-2xl text-sm font-black focus:ring-brand-500 shadow-sm transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Estimated Date of Delivery (EDD)</label>
                                        <input type="date" name="metadata[edd]" x-model="prenatal.edd" readonly
                                            class="w-full px-5 py-4 bg-brand-50/50 border-brand-100 rounded-2xl text-sm font-black text-brand-700 shadow-inner">
                                        <p class="text-[9px] text-brand-500 font-bold mt-2 ml-1 uppercase tracking-widest"><i class="bi bi-magic mr-1"></i> Auto-computed based on LMP</p>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Gravida (G)</label>
                                        <input type="number" name="metadata[gravida]" x-model="prenatal.gravida" min="1" placeholder="Total pregnancies"
                                            class="w-full px-5 py-4 bg-white border-gray-100 rounded-2xl text-sm font-black focus:ring-brand-500 shadow-sm transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Para (P)</label>
                                        <input type="number" name="metadata[para]" x-model="prenatal.para" min="0" placeholder="Reached viability"
                                            class="w-full px-5 py-4 bg-white border-gray-100 rounded-2xl text-sm font-black focus:ring-brand-500 shadow-sm transition-all">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="flex items-center gap-4 p-5 bg-white border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all"
                                            :class="prenatal.miscarriage ? 'border-red-500 ring-2 ring-red-50' : ''">
                                            <input type="checkbox" name="metadata[history_miscarriage]" x-model="prenatal.miscarriage" value="1" class="w-5 h-5 text-red-600 rounded focus:ring-red-500 border-gray-200">
                                            <div>
                                                <span class="text-xs font-black text-gray-900 uppercase tracking-widest block">History of Miscarriage</span>
                                                <span class="text-[10px] text-gray-500 font-bold">Check if the patient has had any previous miscarriages</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Previous Complications</label>
                                        <textarea name="metadata[previous_complications]" x-model="prenatal.complications" rows="2" placeholder="List any previous complications..."
                                            class="w-full px-5 py-4 bg-white border-gray-100 rounded-2xl text-sm font-black focus:ring-brand-500 shadow-sm transition-all"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Immunization Assessment Fields (Dynamic) -->
                        <template x-if="isImmunization">
                            <div class="col-span-2 space-y-8 bg-indigo-50/30 p-8 rounded-[2rem] border border-indigo-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                                        <i class="bi bi-shield-plus-fill"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest">Pre-Vaccination Screening</h4>
                                        <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest">Child Health Status Assessment</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">General Condition</label>
                                        <div class="grid grid-cols-3 gap-4">
                                            <label class="flex flex-col items-center justify-center p-4 bg-white border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all text-center"
                                                :class="immunization.condition === 'Healthy' ? 'border-emerald-500 ring-2 ring-emerald-50 text-emerald-600' : 'text-gray-400'">
                                                <input type="radio" name="metadata[general_condition]" value="Healthy" x-model="immunization.condition" class="sr-only">
                                                <i class="bi bi-emoji-smile text-2xl mb-2"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Healthy</span>
                                            </label>
                                            <label class="flex flex-col items-center justify-center p-4 bg-white border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all text-center"
                                                :class="immunization.condition === 'With Fever' ? 'border-orange-500 ring-2 ring-orange-50 text-orange-600' : 'text-gray-400'">
                                                <input type="radio" name="metadata[general_condition]" value="With Fever" x-model="immunization.condition" class="sr-only">
                                                <i class="bi bi-thermometer-sun text-2xl mb-2"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest">With Fever</span>
                                            </label>
                                            <label class="flex flex-col items-center justify-center p-4 bg-white border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all text-center"
                                                :class="immunization.condition === 'Sick' ? 'border-red-500 ring-2 ring-red-50 text-red-600' : 'text-gray-400'">
                                                <input type="radio" name="metadata[general_condition]" value="Sick" x-model="immunization.condition" class="sr-only">
                                                <i class="bi bi-heart-break text-2xl mb-2"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Sick</span>
                                            </label>
                                        </div>

                                        <template x-if="temp > 37.5">
                                            <div class="mt-5 p-5 bg-red-600 rounded-2xl text-white shadow-lg shadow-red-200 flex items-center gap-4 animate-bounce">
                                                <i class="bi bi-exclamation-octagon-fill text-2xl"></i>
                                                <div>
                                                    <p class="text-[11px] font-black uppercase tracking-widest">Safety Warning: High Fever</p>
                                                    <p class="text-[10px] font-bold opacity-80 uppercase tracking-widest">Vaccination should be delayed until temperature is normal.</p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Allergy History</label>
                                        <textarea name="metadata[allergy_history]" x-model="immunization.allergies" rows="2" placeholder="e.g. History of allergies..."
                                            class="w-full px-5 py-4 bg-white border-gray-100 rounded-2xl text-sm font-black focus:ring-indigo-500 shadow-sm transition-all"></textarea>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="flex items-center gap-4 p-5 bg-white border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all"
                                            :class="immunization.prev_reaction ? 'border-orange-500 ring-2 ring-orange-50' : ''">
                                            <input type="checkbox" name="metadata[previous_vaccine_reaction]" x-model="immunization.prev_reaction" value="1" class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500 border-gray-200">
                                            <div>
                                                <span class="text-xs font-black text-gray-900 uppercase tracking-widest block">Previous Vaccine Reaction</span>
                                                <span class="text-[10px] text-gray-500 font-bold">Check if the child had an adverse reaction to previous doses</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Blood Pressure -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Blood Pressure</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="bi bi-heart-pulse text-gray-300 group-focus-within:text-red-500 transition-colors"></i>
                                </div>
                                <input type="text" name="blood_pressure" required placeholder="e.g. 120/80" value="{{ old('blood_pressure') }}"
                                       class="w-full pl-12 pr-16 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] text-sm font-black focus:ring-red-500 focus:border-red-500 transition-all shadow-sm">
                                <span class="absolute right-5 top-4.5 text-[10px] font-black text-gray-400 uppercase">mmHg</span>
                            </div>
                            @error('blood_pressure') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <!-- Temperature -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Temperature</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="bi bi-thermometer-half text-gray-300 group-focus-within:text-orange-500 transition-colors"></i>
                                </div>
                                <input type="number" step="0.1" name="temperature" x-model="temp" required placeholder="36.5" 
                                       class="w-full pl-12 pr-12 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] text-sm font-black focus:ring-orange-500 focus:border-orange-500 transition-all shadow-sm text-center">
                                <span class="absolute right-5 top-4.5 text-[10px] font-black text-gray-400 uppercase">°C</span>
                            </div>
                            @error('temperature') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <!-- Weight -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Body Weight</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="bi bi-speedometer2 text-gray-300 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                                <input type="number" step="0.1" name="weight" x-model="weight" @input="calculateBMI()" required placeholder="70" 
                                       class="w-full pl-12 pr-12 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] text-sm font-black focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm text-center">
                                <span class="absolute right-5 top-4.5 text-[10px] font-black text-gray-400 uppercase">kg</span>
                            </div>
                            @error('weight') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <!-- Height -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Height</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="bi bi-rulers text-gray-300 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                                <input type="number" step="0.1" name="height_cm" x-model="height" @input="calculateBMI()" required placeholder="170" 
                                       class="w-full pl-12 pr-12 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] text-sm font-black focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm text-center">
                                <span class="absolute right-5 top-4.5 text-[10px] font-black text-gray-400 uppercase">cm</span>
                            </div>
                            @error('height_cm') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <!-- Pulse Rate -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Pulse Rate</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="bi bi-heart-fill text-gray-300 group-focus-within:text-red-500 transition-colors"></i>
                                </div>
                                <input type="number" name="pulse_rate" required placeholder="75" value="{{ old('pulse_rate') }}"
                                       class="w-full pl-12 pr-16 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] text-sm font-black focus:ring-red-500 focus:border-red-500 transition-all shadow-sm text-center">
                                <span class="absolute right-5 top-4.5 text-[10px] font-black text-gray-400 uppercase">bpm</span>
                            </div>
                            @error('pulse_rate') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <!-- Respiratory Rate -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Respiratory Rate</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="bi bi-lungs-fill text-gray-300 group-focus-within:text-teal-500 transition-colors"></i>
                                </div>
                                <input type="number" name="respiratory_rate" placeholder="16" value="{{ old('respiratory_rate') }}"
                                       class="w-full pl-12 pr-16 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] text-sm font-black focus:ring-teal-500 focus:border-teal-500 transition-all shadow-sm text-center">
                                <span class="absolute right-5 top-4.5 text-[10px] font-black text-gray-400 uppercase">bpm</span>
                            </div>
                        </div>

                        <!-- Oxygen Saturation -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Oxygen Saturation</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="bi bi-droplet-half text-gray-300 group-focus-within:text-blue-600 transition-colors"></i>
                                </div>
                                <input type="number" name="oxygen_saturation" placeholder="98" value="{{ old('oxygen_saturation') }}"
                                       class="w-full pl-12 pr-12 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] text-sm font-black focus:ring-blue-600 focus:border-blue-600 transition-all shadow-sm text-center">
                                <span class="absolute right-5 top-4.5 text-[10px] font-black text-gray-400 uppercase">%</span>
                            </div>
                        </div>

                        <!-- BMI (Calculated) -->
                        <div class="col-span-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Calculated BMI</label>
                            <div class="relative">
                                <input type="text" x-bind:value="bmi" readonly placeholder="--" 
                                       class="w-full px-5 py-4 bg-gray-900 text-brand-400 rounded-[1.5rem] text-lg font-black shadow-lg border-none text-center">
                                <div class="absolute left-4 top-4.5 text-brand-400/50">
                                    <i class="bi bi-calculator"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="mt-12 pt-10 border-t border-gray-50 flex flex-col sm:flex-row gap-4">
                        <button type="submit" 
                                class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-brand-200 transform transition hover:-translate-y-1 active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                            <i class="bi bi-send-fill text-lg"></i>
                            Submit & Send to {{ $serviceProviderLabel }}
                        </button>
                        <a href="{{ route('healthworker.dashboard') }}" 
                           class="sm:w-48 bg-white border border-gray-100 text-gray-400 hover:text-gray-600 font-black py-4 rounded-2xl text-center transition block uppercase tracking-widest text-[10px]">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
