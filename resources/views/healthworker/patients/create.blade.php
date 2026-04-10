@extends('layouts.app')

@section('title', 'Add Patient')

<style>
.health-date-input {
    -webkit-appearance: auto;
    appearance: auto;
    color-scheme: light;
}

.health-date-input::-webkit-calendar-picker-indicator {
    display: block;
    opacity: 1;
    cursor: pointer;
    filter: invert(38%) sepia(6%) saturate(945%) hue-rotate(175deg) brightness(93%) contrast(88%);
}
</style>

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Breadcrumbs --}}
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[9px] font-black uppercase tracking-tighter">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center text-gray-400">
                    <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                    <a href="{{ route('healthworker.patients.index') }}" class="hover:text-brand-600 transition-colors">Patients</a>
                </div>
            </li>
            <li>
                <div class="flex items-center text-gray-400">
                    <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                    <span class="text-brand-600">Add New Patient</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-50 rounded-full blur-2xl -ml-16 -mb-16 opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-3">
                    <i class="bi bi-person-plus-fill text-[8px]"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">Registration Hub</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight leading-tight">
                    Add New <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Patient</span>
                </h1>
                <p class="text-gray-500 font-medium mt-2 sm:mt-3 text-xs lg:text-sm leading-relaxed">
                    Create a new digital health profile for a community member. Ensure all personal and contact details are accurate.
                </p>
            </div>
            
            <div class="flex flex-col gap-2 w-full lg:w-auto">
                <div class="bg-gray-50/80 backdrop-blur-sm p-4 rounded-lg border border-gray-100 shadow-inner flex items-center gap-3 transform hover:scale-105 transition-all duration-500">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-brand-600 shadow-sm border border-brand-50">
                        <i class="bi bi-shield-lock text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">Privacy Protected</p>
                        <p class="text-xs font-black text-gray-900 tracking-tight">HIPAA Compliant</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-xs text-red-800 flex items-start gap-3 animate-fade-in-down shadow-sm">
            <i class="bi bi-exclamation-triangle-fill text-lg mt-0.5"></i>
            <div>
                <p class="font-black uppercase tracking-tighter text-[8px] mb-1.5">Correction Required</p>
                <ul class="list-disc list-inside space-y-1 font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('healthworker.patients.store') }}" method="POST" class="space-y-6" x-data="{ hasAccount: true }">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-6">
            {{-- Section 1: Personal Information --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner border border-brand-100">
                        <i class="bi bi-person-vcard text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-gray-900 tracking-tight">Personal Information</h2>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">Legal Name & Identification</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}"
                               class="block w-full px-4 py-2.5 bg-gray-50 border-none rounded-lg focus:ring-3 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Middle Name <span class="text-gray-300">(Optional)</span></label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob') }}"
                               class="health-date-input block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Civil Status</label>
                        <select name="civil_status" class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                            <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>-- Select Status --</option>
                            <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Gender</label>
                        <select name="gender" class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all" required>
                            <option value="" disabled selected>-- Select Gender --</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="h-px bg-gray-50"></div>

            {{-- Section 2: Contact Information --}}
            <div class="space-y-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner border border-brand-100">
                        <i class="bi bi-geo-alt text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">Contact Information</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Address & Emergency Details</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="space-y-3 lg:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Home Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Purok</label>
                        <input type="text" name="purok" value="{{ old('purok') }}"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Family Number <span class="text-gray-300">(Optional)</span></label>
                        <input type="text" name="family_no" value="{{ old('family_no') }}"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Primary Contact Number</label>
                        <input type="text" name="contact_no" value="{{ old('contact_no') }}"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Full Name"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" placeholder="e.g. Spouse, Parent"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Emergency Phone Number</label>
                        <input type="text" name="emergency_no" value="{{ old('emergency_no') }}" placeholder="e.g. 0912..."
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>

                    <div class="space-y-3 lg:col-span-2" x-show="hasAccount">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="patient@example.com"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                        <p class="text-[9px] font-bold text-brand-500 uppercase tracking-widest ml-4 mt-2">
                            <i class="bi bi-info-circle mr-1"></i> A secure password will be automatically generated upon creation.
                        </p>
                    </div>

                    <div class="space-y-3 flex items-end pb-2">
                         <label class="inline-flex items-center cursor-pointer bg-white px-6 py-3 rounded-2xl border border-gray-200 shadow-sm transition-all hover:border-brand-200 w-full">
                            <input type="checkbox" name="no_account" value="1" class="sr-only peer" @click="hasAccount = !hasAccount">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                            <span class="ms-4 text-xs font-black text-gray-700 uppercase tracking-widest">Walk-in Patient (No Account)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="h-px bg-gray-50"></div>

            {{-- Section 3: Medical Background --}}
            <div class="space-y-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner border border-brand-100">
                        <i class="bi bi-hospital text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">Medical Background</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Clinical History & Vitals</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Blood Type</label>
                        <select name="blood_type" class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                            <option value="" {{ old('blood_type') ? '' : 'selected' }}>-- Select Blood Type (Optional) --</option>
                            <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Allergies</label>
                        <input type="text" name="allergies" value="{{ old('allergies') }}" placeholder="e.g. Penicillin, Seafood"
                               class="block w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>

                    <div class="space-y-3 md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Medical Conditions & History</label>
                        <textarea name="medical_history" rows="3" placeholder="List existing conditions like Diabetes, Hypertension, etc."
                                  class="block w-full px-8 py-6 bg-gray-50 border-none rounded-[2.5rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">{{ old('medical_history') }}</textarea>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Current Medications</label>
                        <textarea name="current_medications" rows="2" placeholder="List current maintenance medicines..."
                                  class="block w-full px-8 py-6 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">{{ old('current_medications') }}</textarea>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Family Medical History</label>
                        <textarea name="family_history" rows="2" placeholder="e.g. Hereditary conditions..."
                                  class="block w-full px-8 py-6 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">{{ old('family_history') }}</textarea>
                    </div>
                </div>
            </div>


        </div>

        {{-- Actions --}}
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3 bg-brand-50/50 px-6 py-3 rounded-2xl border border-brand-100/50">
                <i class="bi bi-info-circle-fill text-brand-500"></i>
                <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest">Double check information before saving</p>
            </div>
            
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <a href="{{ route('healthworker.patients.index') }}" 
                   class="flex-1 sm:flex-none px-10 py-5 bg-white text-gray-500 rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] border border-gray-200 hover:bg-gray-50 transition-all text-center">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 sm:flex-none px-10 py-5 bg-brand-600 text-white rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all active:scale-95 flex items-center justify-center gap-3">
                    Save Patient Profile <i class="bi bi-check-circle-fill"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
