{{-- resources/views/healthworker/patients/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div 
    class="relative min-h-screen bg-[#f8fafc] overflow-hidden" 
    x-data="{ isSubmitting: false }"
>
    {{-- Decorative Background Elements --}}
    <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-brand-50/50 to-transparent -z-10"></div>
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-brand-200/20 rounded-full blur-[120px] -z-10 animate-pulse"></div>
    <div class="absolute top-[10%] right-[-5%] w-[30%] h-[30%] bg-purple-200/20 rounded-full blur-[100px] -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative">
        {{-- Breadcrumb / Header Navigation --}}
        <nav class="mb-10 flex items-center justify-between">
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('midwife.dashboard') }}" class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-500 hover:text-brand-600 hover:shadow-md transition-all duration-300">
                    <i class="bi bi-house-door-fill"></i>
                </a>
                <i class="bi bi-chevron-right text-[10px] text-gray-300"></i>
                <a href="{{ route('midwife.patients.index') }}" class="px-4 py-2 rounded-xl bg-white shadow-sm border border-gray-100 font-bold text-gray-500 hover:text-brand-600 hover:shadow-md transition-all duration-300">
                    Patients
                </a>
                <i class="bi bi-chevron-right text-[10px] text-gray-300"></i>
                <span class="px-4 py-2 rounded-xl bg-brand-50 text-brand-700 font-black border border-brand-100">
                    Edit Patient
                </span>
            </div>
            
            <a href="{{ route('midwife.patients.show', $patient->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-black text-gray-700 hover:bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md group">
                <i class="bi bi-arrow-left text-brand-500 group-hover:-translate-x-1 transition-transform"></i>
                Back to Profile
            </a>
        </nav>

        {{-- Hero Header --}}
        <div class="mb-12 relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-brand-600 to-purple-600 rounded-[2.5rem] blur opacity-10"></div>
            <div class="relative bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 -mr-20 -mt-20 bg-brand-50/50 rounded-full blur-3xl -z-10"></div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-brand-100">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-none mb-2">Edit Patient Information</h1>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="bi bi-person-circle text-brand-400"></i>
                                Updating: {{ $patient->full_name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('midwife.patients.update', $patient->id) }}" method="POST" @submit="isSubmitting = true">
            @csrf
            @method('PUT')

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-10 animate-shake">
                    <div class="bg-red-50 border-2 border-red-100 rounded-[2rem] p-6 flex items-start gap-4 shadow-sm shadow-red-50">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 text-xl flex-shrink-0">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div>
                            <p class="text-red-800 font-black text-sm uppercase tracking-wider mb-2">Please correct the following errors:</p>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1 list-none">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-600 text-xs font-bold flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-red-400"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-12 gap-8">
                
                {{-- Left Column: Personal & Contact Info --}}
                <div class="col-span-12 lg:col-span-7 space-y-8">
                    
                    {{-- Personal Information Section --}}
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-4 mb-10">
                            <span class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- First Name --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-person-fill text-brand-400"></i> First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Middle Name --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-person text-gray-300"></i> Middle Name <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $patient->middle_name) }}"
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-gray-50 focus:border-gray-400 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Last Name --}}
                            <div class="col-span-2 space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-person-fill text-brand-400"></i> Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Date of Birth --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-calendar-event-fill text-blue-400"></i> Date of Birth <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="dob" value="{{ old('dob', $patient->dob ? $patient->dob->format('Y-m-d') : '') }}" required
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Gender --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-gender-ambiguous text-pink-400"></i> Gender <span class="text-red-500">*</span>
                                </label>
                                <select name="gender" required 
                                        class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-pink-50 focus:border-pink-500 transition-all duration-300 font-bold text-sm appearance-none">
                                    <option value="Male" {{ old('gender', $patient->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $patient->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $patient->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            {{-- Civil Status --}}
                            <div class="col-span-2 space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-heart-fill text-rose-400"></i> Civil Status
                                </label>
                                <select name="civil_status" 
                                        class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-rose-50 focus:border-rose-500 transition-all duration-300 font-bold text-sm appearance-none">
                                    <option value="" disabled {{ old('civil_status', $patient->civil_status) ? '' : 'selected' }}>-- Select Status --</option>
                                    <option value="Single" {{ old('civil_status', $patient->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $patient->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $patient->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ old('civil_status', $patient->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Maternal Health Section (Only for Female Patients) --}}
                    @if($patient->gender == 'Female')
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-4 mb-10">
                            <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-gender-female"></i>
                            </span>
                            Maternal Health (DOH Monitoring)
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-calendar-check text-purple-400"></i> LMP
                                </label>
                                <input type="date" name="lmp" value="{{ old('lmp', optional($patient->patientProfile)->lmp ? $patient->patientProfile->lmp->format('Y-m-d') : '') }}"
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-purple-50 focus:border-purple-500 transition-all duration-300 font-bold text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-calendar-heart text-purple-400"></i> EDD
                                </label>
                                <input type="date" name="edd" value="{{ old('edd', optional($patient->patientProfile)->edd ? $patient->patientProfile->edd->format('Y-m-d') : '') }}"
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-purple-50 focus:border-purple-500 transition-all duration-300 font-bold text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-hash text-purple-400"></i> Gravida
                                </label>
                                <input type="number" name="gravida" value="{{ old('gravida', optional($patient->patientProfile)->gravida) }}" placeholder="Total pregnancies"
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-purple-50 focus:border-purple-500 transition-all duration-300 font-bold text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-hash text-purple-400"></i> Para
                                </label>
                                <input type="number" name="para" value="{{ old('para', optional($patient->patientProfile)->para) }}" placeholder="Total births"
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-purple-50 focus:border-purple-500 transition-all duration-300 font-bold text-sm">
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Child Health Section (Only for children < 7) --}}
                    @if($patient->age < 7)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-4 mb-10">
                            <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            Child Immunization (FIC)
                        </h3>
                        
                        <div class="flex items-center gap-6 p-6 rounded-3xl bg-blue-50/50 border border-blue-100">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="is_fully_immunized" id="is_fully_immunized" value="1" {{ old('is_fully_immunized', optional($patient->patientProfile)->is_fully_immunized) ? 'checked' : '' }}
                                       class="w-6 h-6 rounded-lg border-blue-200 text-blue-600 focus:ring-blue-500 transition-all">
                                <label for="is_fully_immunized" class="text-xs font-black text-gray-900 uppercase tracking-widest cursor-pointer">Fully Immunized Child (FIC)</label>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Contact Information Section --}}
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-4 mb-10">
                            <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            Contact Information
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-6">
                            {{-- Address --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-geo-fill text-indigo-400"></i> Address <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="address" value="{{ old('address', $patient->address) }}" placeholder="House No., Street" required
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Purok --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-pin-map-fill text-indigo-400"></i> Purok <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="purok" value="{{ old('purok', $patient->purok) }}" required
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Family Number --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-hash text-indigo-400"></i> Family Number <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" name="family_no" value="{{ old('family_no', $patient->family_no) }}"
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Contact Number --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-telephone-fill text-brand-400"></i> Contact Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="contact_no" value="{{ old('contact_no', $patient->contact_no) }}" required
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            {{-- Email --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-envelope-fill text-brand-400"></i> Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $patient->email) }}" required
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all duration-300 font-bold text-sm">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column: Medical, Emergency, Account --}}
                <div class="col-span-12 lg:col-span-5 space-y-8">

                    {{-- Medical Background Section --}}
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 -mr-16 -mt-16 bg-red-50 rounded-full blur-3xl -z-10"></div>
                        
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-4 mb-10">
                            <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </span>
                            Medical Background
                        </h3>

                        <div class="space-y-6">
                            {{-- Blood Type --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-droplet-fill text-red-400"></i> Blood Type
                                </label>
                                @php($bloodType = old('blood_type', optional($patient->patientProfile)->blood_type))
                                <select name="blood_type" 
                                        class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all duration-300 font-bold text-sm appearance-none">
                                    <option value="" {{ $bloodType ? '' : 'selected' }}>-- Optional --</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $type)
                                        <option value="{{ $type }}" {{ $bloodType == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Allergies --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-shield-exclamation text-amber-500"></i> Allergies
                                </label>
                                <textarea name="allergies" rows="2" placeholder="List any allergies..."
                                          class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all duration-300 font-bold text-sm">{{ old('allergies', optional($patient->patientProfile)->allergies) }}</textarea>
                            </div>

                            {{-- Medical History --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-journal-medical text-blue-500"></i> Medical History
                                </label>
                                <textarea name="medical_history" rows="2" placeholder="Past surgeries, conditions..."
                                          class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all duration-300 font-bold text-sm">{{ old('medical_history', optional($patient->patientProfile)->medical_history) }}</textarea>
                            </div>

                            {{-- Current Medications --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-capsule text-emerald-500"></i> Current Medications
                                </label>
                                <textarea name="current_medications" rows="2" placeholder="Maintenance meds, supplements..."
                                          class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 transition-all duration-300 font-bold text-sm">{{ old('current_medications', optional($patient->patientProfile)->current_medications) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Emergency Contact Section --}}
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-4 mb-10">
                            <span class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-shield-fill-check"></i>
                            </span>
                            Emergency Contact
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-person-badge-fill text-orange-400"></i> Contact Name
                                </label>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                                       class="block w-full px-5 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-orange-50 focus:border-orange-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                        <i class="bi bi-people-fill text-orange-400"></i> Relation
                                    </label>
                                    <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $patient->emergency_contact_relationship) }}"
                                           class="block w-full px-4 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-orange-50 focus:border-orange-500 transition-all duration-300 font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                        <i class="bi bi-telephone-fill text-orange-400"></i> Phone
                                    </label>
                                    <input type="text" name="emergency_no" value="{{ old('emergency_no', $patient->emergency_no) }}"
                                           class="block w-full px-4 py-3.5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-orange-50 focus:border-orange-500 transition-all duration-300 font-bold text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Account Settings Section --}}
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-4 mb-10">
                            <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-shield-lock-fill"></i>
                            </span>
                            Account Security
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-6 bg-amber-50/30 p-6 rounded-3xl border border-amber-50">
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-key-fill text-amber-400"></i> New Password
                                </label>
                                <input type="password" name="password" placeholder="Leave blank to keep current"
                                       class="block w-full px-5 py-3.5 border-2 border-white rounded-2xl bg-white/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all duration-300 font-bold text-sm">
                            </div>

                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                    <i class="bi bi-shield-check text-amber-400"></i> Confirm New Password
                                </label>
                                <input type="password" name="password_confirmation"
                                       class="block w-full px-5 py-3.5 border-2 border-white rounded-2xl bg-white/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all duration-300 font-bold text-sm">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Form Actions --}}
            <div class="mt-12 flex flex-col sm:flex-row justify-end gap-4">
                <a href="{{ route('midwife.patients.show', $patient->id) }}"
                   class="order-2 sm:order-1 px-10 py-4 rounded-[1.5rem] text-sm font-black text-gray-400 hover:bg-white hover:text-gray-600 transition-all duration-300 uppercase tracking-widest text-center border border-transparent hover:border-gray-100">
                    Cancel Changes
                </a>
                <button type="submit" :disabled="isSubmitting"
                        class="order-1 sm:order-2 px-12 py-4 rounded-[1.5rem] text-sm font-black text-white bg-gradient-to-r from-brand-600 to-purple-600 shadow-xl shadow-brand-100 hover:shadow-brand-200 hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span x-show="!isSubmitting">Update Patient Profile</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Saving Changes...
                    </span>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
