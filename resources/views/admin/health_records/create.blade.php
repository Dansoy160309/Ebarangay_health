@extends('layouts.app')

@section('title', 'Add Health Record')

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
            <h1 class="text-2xl font-bold text-gray-800">Create Health Record</h1>
            <p class="text-gray-500 text-sm">Add a new medical record entry for a patient</p>
        </div>
    </div>

    @php
        $storeRoute = auth()->user()->isAdmin() ? route('admin.health-records.store') : route('healthworker.health-records.store');
    @endphp

    <form method="POST" action="{{ $storeRoute }}">
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
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Select Patient</label>
                        <select name="patient_id" id="patient_id" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 py-2.5" required>
                            <option value="">Choose a patient...</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ (old('patient_id') ?? request('patient_id')) == $p->id ? 'selected' : '' }}>{{ $p->full_name }}</option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
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
                     }">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i class="bi bi-activity"></i>
                        </div>
                        Vital Signs
                    </h3>
                    
                    <div class="space-y-4">
                        <input type="hidden" name="vital_signs" x-ref="vitalsInput" value="{{ old('vital_signs') }}">
                        
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
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" x-data="{ items: [{name: '', date: ''}] }">
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
                    
                    <p class="text-xs text-gray-400 mt-4">Record any vaccines administered during this visit.</p>
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