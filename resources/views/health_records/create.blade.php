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
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="bi bi-person"></i>
                        </div>
                        Patient Information
                    </h3>
                    
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Select Patient</label>
                        <select name="patient_id" id="patient_id" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 py-2.5" required>
                            <option value="">Choose a patient...</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->full_name }}</option>
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
                        <textarea name="consultation" id="consultation" rows="12" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500 leading-relaxed" placeholder="Enter chief complaint, history of present illness, diagnosis, and treatment plan...">{{ old('consultation') }}</textarea>
                        @error('consultation')
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
                    
                    <div>
                        <label for="vital_signs" class="block text-sm font-medium text-gray-700 mb-2">Record Vitals</label>
                        <textarea name="vital_signs" id="vital_signs" rows="4" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="BP: 120/80&#10;Temp: 36.5°C&#10;HR: 80 bpm&#10;RR: 20&#10;O2: 98%">{{ old('vital_signs') }}</textarea>
                        <p class="text-xs text-gray-400 mt-2">Format: One vital sign per line is recommended.</p>
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
