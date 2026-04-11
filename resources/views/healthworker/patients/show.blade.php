@extends('layouts.app')

@section('title', 'Patient Details')

<style>
.dependent-date-input {
    -webkit-appearance: auto;
    appearance: auto;
    color-scheme: light;
}

.dependent-date-input::-webkit-calendar-picker-indicator {
    display: block;
    opacity: 1;
    cursor: pointer;
    filter: invert(38%) sepia(6%) saturate(945%) hue-rotate(175deg) brightness(93%) contrast(88%);
}
</style>

@section('content')
<div 
    class="relative min-h-screen bg-[#f8fafc] overflow-hidden" 
    x-data="{ showDependentModal: {{ request()->boolean('add_dependent') ? 'true' : 'false' }} }"
>
    @php
        $isMidwifeView = auth()->user()->isMidwife();
        $dashboardRoute = $isMidwifeView ? 'midwife.dashboard' : 'healthworker.dashboard';
        $patientRoutePrefix = $isMidwifeView ? 'midwife' : 'healthworker';
        $isDependent = $patient->guardian_id && $patient->guardian;
        $displayContactNo = $isDependent ? ($patient->guardian->contact_no ?? $patient->contact_no) : $patient->contact_no;
        $displayEmail = $isDependent ? ($patient->guardian->email ?? $patient->email) : $patient->email;
    @endphp

    {{-- Decorative Background Elements --}}
    <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-brand-50/50 to-transparent -z-10"></div>
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-brand-200/20 rounded-full blur-[120px] -z-10 animate-pulse"></div>
    <div class="absolute top-[10%] right-[-5%] w-[30%] h-[30%] bg-purple-200/20 rounded-full blur-[100px] -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative">
        {{-- Breadcrumb / Header Navigation --}}
        <nav class="mb-10 flex items-center justify-between">
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route($dashboardRoute) }}" class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-500 hover:text-brand-600 hover:shadow-md transition-all duration-300">
                    <i class="bi bi-house-door-fill"></i>
                </a>
                <i class="bi bi-chevron-right text-[10px] text-gray-300"></i>
                <a href="{{ route($patientRoutePrefix . '.patients.index') }}" class="px-4 py-2 rounded-xl bg-white shadow-sm border border-gray-100 font-bold text-gray-500 hover:text-brand-600 hover:shadow-md transition-all duration-300">
                    Patients
                </a>
                <i class="bi bi-chevron-right text-[10px] text-gray-300"></i>
                <span class="px-4 py-2 rounded-xl bg-brand-50 text-brand-700 font-black border border-brand-100">
                    Patient Profile
                </span>
            </div>
            
            @if(!$isMidwifeView)
            <a href="{{ route('healthworker.patients.edit', $patient->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-black text-gray-700 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-all duration-300 shadow-sm hover:shadow-md group">
                <i class="bi bi-pencil-square text-brand-500 group-hover:scale-110 transition-transform"></i>
                Edit Profile
            </a>
            @else
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-black uppercase tracking-widest">
                <i class="bi bi-eye-fill"></i>
                View Only
            </span>
            @endif
        </nav>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-12 gap-8">
            
            {{-- Left Column: Identity & Personal (Bento Layout) --}}
            <div class="col-span-12 lg:col-span-4 space-y-8">
                
                {{-- Profile Hero Card --}}
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-brand-600 to-purple-600 rounded-[2.5rem] blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>
                    <div class="relative bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden p-8 flex flex-col items-center text-center">
                        <div class="relative mb-6">
                            <div class="absolute -inset-4 bg-brand-50 rounded-full animate-pulse opacity-50"></div>
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center text-white text-4xl font-black shadow-2xl border-4 border-white relative">
                                {{ substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1) }}
                            </div>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-none mb-2">{{ $patient->full_name }}</h1>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Patient Code: {{ $patient->patient_code }}</p>
                        
                        <div class="flex items-center gap-2 mb-8">
                            <span class="px-4 py-1.5 rounded-full font-black text-[10px] uppercase tracking-wider {{ $patient->status ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                {{ $patient->status ? 'Active Profile' : 'Inactive Profile' }}
                            </span>
                        </div>

                        <div class="w-full pt-8 border-t border-gray-50 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Age</p>
                                <p class="text-xl font-black text-gray-900">{{ $patient->dob->age }} <span class="text-xs font-bold text-gray-400">Yrs</span></p>
                            </div>
                            <div class="text-center border-l border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Gender</p>
                                <p class="text-xl font-black text-gray-900">{{ ucfirst($patient->gender) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Info Card --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs">
                            <i class="bi bi-telephone-fill"></i>
                        </span>
                        Contact Details
                    </h3>
                    @if($isDependent)
                        <div class="rounded-2xl border border-amber-100 bg-amber-50/70 px-4 py-3 text-[10px] font-bold text-amber-700 uppercase tracking-widest">
                            Inherited from account holder: {{ $patient->guardian->full_name }}
                        </div>
                    @endif
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-50 group hover:border-brand-100 transition-all duration-300">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Phone Number</p>
                            <p class="text-sm font-bold text-gray-900">{{ $displayContactNo }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-50 group hover:border-brand-100 transition-all duration-300">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Email Address</p>
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $displayEmail }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-50 group hover:border-brand-100 transition-all duration-300">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Residence</p>
                            <p class="text-sm font-bold text-gray-900 leading-relaxed">{{ $patient->address }}, {{ $patient->purok }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Medical & Dependents --}}
            <div class="col-span-12 lg:col-span-8 space-y-8">
                
                {{-- Medical Summary (Bento Box) --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-xl shadow-inner">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            Clinical Information
                        </h3>
                        @php($profile = $patient->patientProfile)
                        @if($profile && $profile->blood_type)
                            <div class="px-6 py-2 bg-red-50 text-red-600 rounded-2xl border border-red-100 flex items-center gap-3">
                                <i class="bi bi-droplet-fill animate-pulse"></i>
                                <span class="text-sm font-black tracking-widest uppercase">Type {{ $profile->blood_type }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 rounded-3xl bg-amber-50/30 border border-amber-100 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 bg-amber-200/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                            <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="bi bi-shield-exclamation"></i> Allergies
                            </h4>
                            <p class="text-sm font-bold text-gray-700 leading-relaxed">
                                {{ $profile && $profile->allergies ? $profile->allergies : 'No known allergies' }}
                            </p>
                        </div>
                        
                        <div class="p-6 rounded-3xl bg-blue-50/30 border border-blue-100 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 bg-blue-200/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                            <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="bi bi-journal-medical"></i> Medical History
                            </h4>
                            <p class="text-sm font-bold text-gray-700 leading-relaxed">
                                {{ $profile && $profile->medical_history ? $profile->medical_history : 'None recorded' }}
                            </p>
                        </div>

                        <div class="md:col-span-2 p-6 rounded-3xl bg-emerald-50/30 border border-emerald-100 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 -mr-12 -mt-12 bg-emerald-200/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                            <h4 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="bi bi-capsule"></i> Current Medications
                            </h4>
                            <p class="text-sm font-bold text-gray-700 leading-relaxed">
                                {{ $profile && $profile->current_medications ? $profile->current_medications : 'No active medications recorded' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Family & Dependents Section --}}
                @if(!$patient->guardian_id)
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-brand-600 rounded-[2.5rem] blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>
                    <div class="relative bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                Family & Dependents
                            </h3>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Optional</span>
                        </div>

                        @if(!$isMidwifeView)
                        <button
                            type="button"
                            @click="showDependentModal = true"
                            class="group relative w-full p-1 rounded-3xl bg-gradient-to-r from-purple-50 to-brand-50 hover:from-purple-100 hover:to-brand-100 transition-all duration-500 overflow-hidden shadow-sm"
                        >
                            <div class="relative bg-white/80 backdrop-blur-sm rounded-[1.4rem] p-6 flex items-center justify-between group-hover:bg-white/40 transition-all duration-500">
                                <div class="flex items-center gap-6 text-left">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-brand-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-purple-200 group-hover:scale-110 transition-transform duration-500">
                                        <i class="bi bi-person-plus-fill"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-gray-900 tracking-tight leading-none mb-2">Add New Dependent</p>
                                        <p class="text-xs font-bold text-gray-500 leading-relaxed">Register family members under this account for centralized health tracking.</p>
                                    </div>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-brand-500 group-hover:translate-x-2 transition-transform duration-500">
                                    <i class="bi bi-chevron-right font-black"></i>
                                </div>
                            </div>
                        </button>
                        @endif

                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Registered Dependents</p>
                                <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-widest border border-purple-100">
                                    {{ $patient->dependents->count() }} Total
                                </span>
                            </div>

                            @if($patient->dependents->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($patient->dependents as $dependent)
                                        <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-100 flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-brand-600 font-black text-sm shrink-0">
                                                    {{ substr($dependent->first_name, 0, 1) }}{{ substr($dependent->last_name, 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-black text-gray-900 truncate">{{ $dependent->full_name }}</p>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">
                                                        {{ $dependent->age ?? 'N/A' }} yrs • {{ $dependent->gender }} • {{ $dependent->relationship ?? 'Dependent' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ route($patientRoutePrefix . '.patients.show', $dependent->id) }}"
                                               class="px-3 py-2 rounded-xl bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-brand-600 hover:border-brand-200 transition-all shrink-0">
                                                View
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-5 rounded-2xl bg-gray-50 border border-dashed border-gray-200 text-center">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No dependents added yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Add Dependent Modal -->
    @if(!$patient->guardian_id && !$isMidwifeView)
    <div 
        x-show="showDependentModal" 
        x-cloak 
        style="display: none;"
        class="fixed inset-0 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showDependentModal = false"></div>

        <div class="relative bg-white w-full max-w-xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] rounded-[2.5rem] border border-gray-100 overflow-hidden transform transition-all">
            {{-- Top Accent --}}
            <div class="h-2 bg-gradient-to-r from-purple-600 via-brand-500 to-blue-500"></div>

            <div class="p-6 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl shadow-inner">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight">Add Dependent</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Guardian: {{ $patient->full_name }}</p>
                        </div>
                    </div>
                    <button @click="showDependentModal = false" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all duration-300">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                
                <form action="{{ route('healthworker.patients.dependents.store', $patient->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name Fields -->
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-person-fill text-purple-400"></i> First Name
                            </label>
                            <input type="text" name="first_name" required 
                                   class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-purple-50 focus:border-purple-500 transition-all duration-300 font-bold text-xs">
                        </div>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-person text-gray-300"></i> Middle Name
                            </label>
                            <input type="text" name="middle_name" 
                                   class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-gray-50 focus:border-gray-400 transition-all duration-300 font-bold text-xs">
                        </div>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-person-fill text-purple-400"></i> Last Name
                            </label>
                            <input type="text" name="last_name" required 
                                   class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-purple-50 focus:border-purple-500 transition-all duration-300 font-bold text-xs">
                        </div>
                        
                        <!-- Basic Info -->
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-calendar-event-fill text-blue-400"></i> Date of Birth
                            </label>
                            <input type="date" name="dob" required max="{{ date('Y-m-d') }}" 
                                   class="dependent-date-input block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all duration-300 font-bold text-xs">
                        </div>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-gender-ambiguous text-pink-400"></i> Gender
                            </label>
                            <select name="gender" required 
                                    class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-pink-50 focus:border-pink-500 transition-all duration-300 font-bold text-xs appearance-none">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Relationship Info -->
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-people-fill text-brand-400"></i> Relationship
                            </label>
                            <input type="text" name="relationship" required placeholder="e.g. Son, Daughter" 
                                   class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all duration-300 font-bold text-xs">
                        </div>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-info-circle-fill text-orange-400"></i> Reason
                            </label>
                            <input type="text" name="dependency_reason" placeholder="e.g. Minor, Elderly" 
                                   class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-orange-50 focus:border-orange-500 transition-all duration-300 font-bold text-xs">
                        </div>

                        <!-- Medical Info -->
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-droplet-fill text-red-400"></i> Blood Type
                            </label>
                            <select name="blood_type" 
                                    class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all duration-300 font-bold text-xs appearance-none">
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-shield-exclamation text-yellow-500"></i> Allergies
                            </label>
                            <textarea name="allergies" rows="2" placeholder="List any known allergies..."
                                      class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-yellow-50 focus:border-yellow-500 transition-all duration-300 font-bold text-xs"></textarea>
                        </div>
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                <i class="bi bi-activity text-emerald-500"></i> Existing Conditions
                            </label>
                            <textarea name="existing_conditions" rows="2" placeholder="List any chronic illnesses..."
                                      class="block w-full px-4 py-2.5 border-2 border-gray-50 rounded-xl bg-gray-50/50 placeholder-gray-300 focus:outline-none focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 transition-all duration-300 font-bold text-xs"></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" @click="showDependentModal = false" 
                                class="order-2 sm:order-1 px-6 py-3 rounded-xl text-[10px] font-black text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-all duration-300 uppercase tracking-widest">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="order-1 sm:order-2 px-8 py-3 rounded-xl text-[10px] font-black text-white bg-gradient-to-r from-purple-600 to-brand-600 shadow-lg shadow-purple-100 hover:shadow-purple-200 hover:-translate-y-0.5 transition-all duration-300 uppercase tracking-widest">
                            Add Dependent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection


