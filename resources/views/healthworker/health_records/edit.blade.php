@extends('layouts.app')

@section('title', 'Edit Health Record')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        @php
            $backRoute = auth()->user()->isAdmin() ? route('admin.health-records.index') : route('healthworker.health-records.index');
        @endphp
        <a href="{{ $backRoute }}" class="bg-white p-2 rounded-lg shadow-sm text-gray-500 hover:text-brand-600 transition">
            <i class="bi bi-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Health Record</h1>
            <p class="text-gray-500 text-sm">Update medical record information</p>
        </div>
    </div>

    @php
        $updateRoute = auth()->user()->isAdmin() ? route('admin.health-records.update', $record->id) : route('healthworker.health-records.update', $record->id);
        $cancelRoute = auth()->user()->isAdmin() ? route('admin.health-records.index') : route('healthworker.health-records.index');
        
        $imm = is_string($record->immunizations) ? json_decode($record->immunizations, true) : ($record->immunizations ?? []);
        $immJson = json_encode(count($imm) > 0 ? $imm : [['name' => '', 'date' => '']]);
    @endphp

    <form method="POST" action="{{ $updateRoute }}"
          x-data="{
            serviceId: '{{ old('service_id', $record->service_id) }}',
            services: {{ Js::from($services) }},
            get selectedService() {
                return this.services.find(s => s.id == this.serviceId);
            },
            get isPrenatal() {
                return this.selectedService?.name.toLowerCase().includes('prenatal') || this.selectedService?.name.toLowerCase().includes('pre-natal');
            },
            get isImmunization() {
                return this.selectedService?.name.toLowerCase().includes('immunization') || this.selectedService?.name.toLowerCase().includes('vaccin');
            }
          }">
        @csrf
        @method('PUT')

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
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Select Patient</label>
                        <select name="patient_id" id="patient_id" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 py-2.5" required>
                            <option value="">Choose a patient...</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ (old('patient_id') ?? $record->patient_id) == $p->id ? 'selected' : '' }}>{{ $p->full_name }}</option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
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
                            <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-all duration-200"
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

                <!-- Service Specific Details -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" x-show="serviceId" x-transition>
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                        <span x-text="selectedService?.name + ' Details'"></span>
                    </h3>

                    <!-- Prenatal Fields -->
                    <div x-show="isPrenatal" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Menstrual Period (LMP)</label>
                            <input type="date" name="metadata[lmp]" value="{{ $record->metadata['lmp'] ?? '' }}" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Date of Confinement (EDC)</label>
                            <input type="date" name="metadata[edc]" value="{{ $record->metadata['edc'] ?? '' }}" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gravida (G)</label>
                            <input type="number" name="metadata[gravida]" value="{{ $record->metadata['gravida'] ?? '' }}" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Total pregnancies">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Para (P)</label>
                            <input type="number" name="metadata[para]" value="{{ $record->metadata['para'] ?? '' }}" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Deliveries reached viability">
                        </div>
                    </div>

                    <!-- Immunization Fields -->
                    <div x-show="isImmunization" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name</label>
                            <input type="text" name="metadata[vaccine_name]" value="{{ $record->metadata['vaccine_name'] ?? '' }}" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="e.g. BCG, Hepatitis B">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dose Number</label>
                            <select name="metadata[dose_number]" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @php $dose = $record->metadata['dose_number'] ?? ''; @endphp
                                <option value="1" {{ $dose == '1' ? 'selected' : '' }}>1st Dose</option>
                                <option value="2" {{ $dose == '2' ? 'selected' : '' }}>2nd Dose</option>
                                <option value="3" {{ $dose == '3' ? 'selected' : '' }}>3rd Dose</option>
                                <option value="Booster 1" {{ $dose == 'Booster 1' ? 'selected' : '' }}>Booster 1</option>
                                <option value="Booster 2" {{ $dose == 'Booster 2' ? 'selected' : '' }}>Booster 2</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lot Number</label>
                            <input type="text" name="metadata[lot_number]" value="{{ $record->metadata['lot_number'] ?? '' }}" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Injection Site</label>
                            <select name="metadata[injection_site]" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @php $site = $record->metadata['injection_site'] ?? ''; @endphp
                                <option value="Left Arm" {{ $site == 'Left Arm' ? 'selected' : '' }}>Left Arm</option>
                                <option value="Right Arm" {{ $site == 'Right Arm' ? 'selected' : '' }}>Right Arm</option>
                                <option value="Left Thigh" {{ $site == 'Left Thigh' ? 'selected' : '' }}>Left Thigh</option>
                                <option value="Right Thigh" {{ $site == 'Right Thigh' ? 'selected' : '' }}>Right Thigh</option>
                            </select>
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
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-full">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                            <i class="bi bi-file-medical"></i>
                        </div>
                        Clinical Notes
                    </h3>
                    
                    <div>
                        <label for="consultation" class="block text-sm font-medium text-gray-700 mb-2">Consultation Details</label>
                        <textarea name="consultation" id="consultation" rows="6" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 leading-relaxed" placeholder="Enter chief complaint and history of present illness...">{{ old('consultation', $record->consultation) }}</textarea>
                        @error('consultation')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                        <textarea name="diagnosis" id="diagnosis" rows="3" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 leading-relaxed" placeholder="Enter diagnosis...">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                        @error('diagnosis')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="treatment" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                        <textarea name="treatment" id="treatment" rows="3" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 leading-relaxed" placeholder="Enter treatment plan...">{{ old('treatment', $record->treatment) }}</textarea>
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
                <!-- Status & Date Card -->
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        Record Details
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Date Field -->
                        <div>
                            <label for="created_at" class="block text-sm font-medium text-gray-700 mb-2">Date & Time</label>
                            <input type="datetime-local" name="created_at" id="created_at" value="{{ old('created_at', $record->created_at->format('Y-m-d\TH:i')) }}" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 py-2.5" required>
                            @error('created_at')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status Field -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                            <select name="status" id="status" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 py-2.5">
                                <option value="active" {{ (old('status') ?? $record->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="archived" {{ (old('status') ?? $record->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Vital Signs Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" 
                     x-data="{ 
                         bp: '', 
                         temp: '', 
                         hr: '', 
                         rr: '', 
                         o2: '', 
                         weight: '', 
                         height: '',
                         bmi: '',
                         parseVitals(val) {
                             if(!val) return;
                             
                             // Handle array case (passed as JSON string from PHP)
                             if (typeof val === 'object') {
                                 this.bp = val.blood_pressure || '';
                                 this.temp = val.temperature || '';
                                 this.hr = val.pulse_rate || '';
                                 this.rr = val.respiratory_rate || '';
                                 this.o2 = val.oxygen_saturation || '';
                                 this.weight = val.weight || '';
                                 this.height = val.height_cm || val.height || '';
                                 this.bmi = val.bmi || '';
                                 this.calculateBMI();
                                 return;
                             }

                             // Handle legacy string case
                             let match;
                             match = val.match(/BP: (.*?) mmHg/); if(match) this.bp = match[1];
                             match = val.match(/Temp: (.*?) °C/); if(match) this.temp = match[1];
                             match = val.match(/HR: (.*?) bpm/); if(match) this.hr = match[1];
                             match = val.match(/RR: (.*?) cpm/); if(match) this.rr = match[1];
                             match = val.match(/O2 Sat: (.*?) %/); if(match) this.o2 = match[1];
                             match = val.match(/Weight: (.*?) kg/); if(match) this.weight = match[1];
                             match = val.match(/Height: (.*?) cm/); if(match) this.height = match[1];
                             this.calculateBMI();
                         },
                         calculateBMI() {
                             if(this.weight && this.height) {
                                 let h_m = this.height / 100;
                                 let bmi_val = this.weight / (h_m * h_m);
                                 this.bmi = bmi_val.toFixed(1);
                             } else {
                                 this.bmi = '';
                             }
                             this.updateVitals();
                         },
                         updateVitals() {
                             // We want to send an array if it's already an array-friendly form
                             // But the existing controller expects a string for some roles
                             // Let's stick to the string format for compatibility if needed, 
                             // or let the controller handle both.
                             // For this specific view, we'll keep the string format as it's what the hidden input does.
                             let vitals = [];
                             if(this.bp) vitals.push('BP: ' + this.bp + ' mmHg');
                             if(this.temp) vitals.push('Temp: ' + this.temp + ' °C');
                             if(this.hr) vitals.push('HR: ' + this.hr + ' bpm');
                             if(this.rr) vitals.push('RR: ' + this.rr + ' cpm');
                             if(this.o2) vitals.push('O2 Sat: ' + this.o2 + ' %');
                             if(this.weight) vitals.push('Weight: ' + this.weight + ' kg');
                             if(this.height) vitals.push('Height: ' + this.height + ' cm');
                             if(this.bmi) vitals.push('BMI: ' + this.bmi);
                             $refs.vitalsInput.value = vitals.join('\n');
                         }
                     }"
                     x-init="parseVitals({{ Js::from(old('vital_signs', $record->vital_signs)) }})">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i class="bi bi-activity"></i>
                        </div>
                        Vital Signs
                    </h3>
                    
                    <div class="space-y-4">
                        <input type="hidden" name="vital_signs" x-ref="vitalsInput">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Blood Pressure -->
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Blood Pressure</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-heart-pulse text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="bp" @input="updateVitals()" placeholder="120/80" class="w-full pl-10 pr-12 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
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
                                    <input type="number" step="0.1" x-model="temp" @input="updateVitals()" placeholder="36.5" class="w-full pl-10 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
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
                                    <input type="number" x-model="hr" @input="updateVitals()" placeholder="75" class="w-full pl-10 pr-10 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
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
                                    <input type="number" x-model="rr" @input="updateVitals()" placeholder="16" class="w-full pl-10 pr-10 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
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
                                    <input type="number" x-model="o2" @input="updateVitals()" placeholder="98" class="w-full pl-10 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">%</span>
                                </div>
                            </div>

                            <!-- Weight -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Weight</label>
                                <div class="relative">
                                    <input type="number" step="0.1" x-model="weight" @input="calculateBMI()" placeholder="70" class="w-full pl-3 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">kg</span>
                                </div>
                            </div>

                            <!-- Height -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Height</label>
                                <div class="relative">
                                    <input type="number" x-model="height" @input="calculateBMI()" placeholder="170" class="w-full pl-3 pr-8 py-2.5 border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 font-medium">
                                    <span class="absolute right-3 top-2.5 text-xs font-semibold text-gray-500">cm</span>
                                </div>
                            </div>

                            <!-- BMI Display -->
                            <div class="col-span-2" x-show="bmi">
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

                <!-- Immunizations Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" x-data="{ items: {{ $immJson }} }">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center justify-between pb-4 border-b">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="bi bi-eyedropper"></i>
                            </div>
                            <span>Immunizations</span>
                        </div>
                        <button type="button" @click="items.push({name: '', date: ''})" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md transition flex items-center gap-1">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </h3>
                    
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex gap-2 items-start group">
                                <div class="flex-1 grid grid-cols-5 gap-2">
                                    <div class="col-span-3">
                                        <input type="text" :name="'immunizations[' + index + '][name]'" x-model="item.name" placeholder="Vaccine Name" class="w-full border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="date" :name="'immunizations[' + index + '][date]'" x-model="item.date" class="w-full border-gray-200 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500">
                                    </div>
                                </div>
                                <button type="button" @click="items.length > 1 ? items = items.filter((_, i) => i !== index) : items[0] = {name:'', date:''}" class="text-gray-300 hover:text-red-500 p-2 transition" title="Remove">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                        <i class="bi bi-check2-circle text-lg"></i> Update Record
                    </button>
                    <a href="{{ $cancelRoute }}" class="w-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium py-3 rounded-xl text-center transition">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection