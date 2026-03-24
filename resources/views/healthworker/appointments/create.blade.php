@extends('layouts.app')

@section('title', 'Schedule Appointment')

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <!-- Hero Header with Gradient -->
    <div class="bg-gradient-to-r from-brand-600 to-cyan-600 text-white pb-24 pt-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start justify-between">
                <div>
                    <a href="{{ route('healthworker.appointments.index') }}" class="inline-flex items-center text-white/80 hover:text-white mb-6 transition">
                        <i class="bi bi-arrow-left mr-2"></i> Back to Appointments
                    </a>
                    <h1 class="text-3xl font-bold mb-2">Schedule New Appointment</h1>
                    <p class="text-white/80 text-lg max-w-2xl">
                        Create a new walk-in appointment. Follow the timeline below to complete the scheduling process.
                    </p>
                </div>
                <div class="hidden sm:block opacity-20">
                    <i class="bi bi-calendar-check text-9xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20" x-data="{ 
        show: false, 
        selectedPatientId: '{{ old('patient_id', $patientId ?? '') }}',
        patients: {{ $patients->map(fn($p) => $p->only(['id', 'first_name', 'last_name', 'email', 'dob', 'gender', 'blood_type', 'allergies', 'contact_no']))->toJson() }},
        search: '',
        showDropdown: false,
        showModal: false,
        isSubmitting: false,
        selectedService: '{{ old('service') }}',
        services: {{ $services->toJson() }},
        doctors: {{ $doctors->map(fn($d) => ['id' => $d->id, 'full_name' => $d->full_name, 'role' => $d->role])->toJson() }},
        newPatient: {
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    dob: '',
                    gender: '',
                    civil_status: '',
                    address: '',
                    purok: '',
                    family_no: '',
                    contact_no: '',
                    allergies: '',
                    medical_history: '',
                    current_medications: ''
                },
        get filteredPatients() {
            if (this.search === '') return this.patients.slice(0, 10);
            return this.patients.filter(p => 
                `${p.first_name} ${p.last_name}`.toLowerCase().includes(this.search.toLowerCase()) ||
                (p.email && p.email.toLowerCase().includes(this.search.toLowerCase()))
            ).slice(0, 10);
        },
        get selectedPatient() {
            return this.patients.find(p => p.id == this.selectedPatientId);
        },
        get filteredDoctors() {
            if (!this.selectedService) return this.doctors;
            
            const serviceObj = this.services.find(s => s.name === this.selectedService);
            if (!serviceObj) return this.doctors;
            
            if (serviceObj.provider_type === 'Both') return this.doctors;
            
            return this.doctors.filter(d => 
                d.role.toLowerCase() === serviceObj.provider_type.toLowerCase()
            );
        },
        selectPatient(patient) {
            this.selectedPatientId = patient.id;
            this.search = `${patient.last_name}, ${patient.first_name}`;
            this.showDropdown = false;
        },
        calculateAge(dob) {
            if (!dob) return 'N/A';
            return new Date().getFullYear() - new Date(dob).getFullYear();
        },
        async submitNewPatient() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            try {
                const response = await window.axios.post('{{ route('healthworker.patients.store') }}', {
                    ...this.newPatient,
                    no_account: true,
                    ajax: true
                });
                
                if (response.data.success) {
                            const patient = response.data.patient;
                            this.patients.unshift(patient);
                            this.selectPatient(patient);
                            this.showModal = false;
                            // Reset form
                            this.newPatient = { 
                                first_name: '', 
                                middle_name: '',
                                last_name: '', 
                                dob: '', 
                                gender: '', 
                                civil_status: '',
                                address: '', 
                                purok: '', 
                                family_no: '',
                                contact_no: '', 
                                allergies: '', 
                                medical_history: '',
                                current_medications: '' 
                            };
                        }
            } catch (error) {
                alert(error.response?.data?.message || 'Error creating patient. Please check all fields.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }" x-init="setTimeout(() => show = true, 100)">
        <form action="{{ route('healthworker.appointments.store') }}" method="POST">
            @csrf

            <!-- Timeline Container -->
            <div class="relative">
                <!-- Vertical Line -->
                <div class="absolute left-8 top-8 bottom-8 w-0.5 bg-gray-200 hidden md:block" 
                     x-show="show" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 scale-y-0 origin-top" x-transition:enter-end="opacity-100 scale-y-100"></div>

                <!-- Step 1: Patient Selection -->
                <div class="relative pl-0 md:pl-24 py-6 group" x-show="show" x-transition:enter="transition ease-out duration-500 delay-100" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
                    <!-- Timeline Dot -->
                    <div class="absolute left-4 top-10 w-8 h-8 rounded-full bg-brand-600 border-4 border-white shadow-md z-10 hidden md:flex items-center justify-center transform transition-transform hover:scale-110">
                        <span class="text-white text-xs font-bold">1</span>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="p-5 md:p-8">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center md:hidden shrink-0">
                                        <span class="font-bold">1</span>
                                    </div>
                                    <i class="bi bi-person text-brand-500 text-xl hidden md:block"></i>
                                    <span class="truncate">Select Patient</span>
                                </h3>
                                <button type="button" @click="showModal = true"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 sm:py-2 bg-brand-50 text-brand-700 text-sm font-bold rounded-xl hover:bg-brand-100 transition-colors border border-brand-100">
                                    <i class="bi bi-person-plus-fill mr-2"></i> Add New Patient
                                </button>
                            </div>
                            
                            <div class="max-w-xl">
                                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Who is this appointment for? <span class="text-red-500">*</span>
                                </label>
                                
                                <!-- Searchable Dropdown -->
                                <div class="relative" @click.away="showDropdown = false">
                                    <input type="hidden" name="patient_id" :value="selectedPatientId" required>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                            <i class="bi bi-search"></i>
                                        </div>
                                        <input type="text" x-model="search" @focus="showDropdown = true"
                                               placeholder="Search by name or email..."
                                               class="block w-full pl-11 pr-10 py-4 text-base border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-50 rounded-xl transition-all shadow-sm font-medium">
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 cursor-pointer text-gray-400 hover:text-red-500" 
                                             x-show="selectedPatientId" @click="selectedPatientId = ''; search = ''">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </div>
                                    </div>

                                    <!-- Dropdown List -->
                                    <div x-show="showDropdown" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                            <template x-for="patient in filteredPatients" :key="patient.id">
                                                <button type="button" @click="selectPatient(patient)"
                                                        class="w-full text-left px-6 py-4 hover:bg-brand-50 transition-colors border-b border-gray-50 last:border-0 group">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="font-bold text-gray-900 group-hover:text-brand-700" x-text="`${patient.last_name}, ${patient.first_name}`"></p>
                                                            <p class="text-xs text-gray-500" x-text="patient.email || 'No email provided'"></p>
                                                        </div>
                                                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 bg-gray-50 px-2 py-1 rounded group-hover:bg-white transition-colors" x-text="`ID: #${patient.id}`"></div>
                                                    </div>
                                                </button>
                                            </template>
                                            <div x-show="filteredPatients.length === 0" class="px-6 py-10 text-center text-gray-500">
                                                <i class="bi bi-search text-2xl mb-2 block opacity-20"></i>
                                                <p>No patients found matching your search.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @error('patient_id')
                                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                                @enderror

                                <!-- Patient Summary Card -->
                                <template x-if="selectedPatient">
                                    <div class="mt-6 p-5 bg-blue-50/50 rounded-xl border border-blue-100 animate-fade-in-up">
                                        <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                                            <i class="bi bi-person-vcard"></i> Patient Information
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                            <div class="bg-white p-3 rounded-lg border border-blue-50 shadow-sm">
                                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Full Name</span>
                                                <span class="font-bold text-gray-800 text-lg" x-text="`${selectedPatient.last_name}, ${selectedPatient.first_name}`"></span>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border border-blue-50 shadow-sm">
                                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Allergies</span>
                                                <span class="font-bold" :class="selectedPatient.allergies ? 'text-red-600' : 'text-gray-500'" x-text="selectedPatient.allergies || 'None'"></span>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border border-blue-50 shadow-sm">
                                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Age / Gender</span>
                                                <span class="font-medium text-gray-700">
                                                    <span x-text="calculateAge(selectedPatient.dob)"></span> yrs / <span x-text="selectedPatient.gender" class="capitalize"></span>
                                                </span>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border border-blue-50 shadow-sm">
                                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Contact</span>
                                                <span class="font-medium text-gray-700" x-text="selectedPatient.contact_no || 'N/A'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Service Details -->
                <div class="relative pl-0 md:pl-24 py-6 group" x-show="show" x-transition:enter="transition ease-out duration-500 delay-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
                    <!-- Timeline Dot -->
                    <div class="absolute left-4 top-10 w-8 h-8 rounded-full bg-white border-4 border-brand-200 shadow-sm z-10 hidden md:flex items-center justify-center transform transition-transform hover:scale-110">
                        <span class="text-brand-600 text-xs font-bold">2</span>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="p-6 md:p-8">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center md:hidden">
                                    <span class="font-bold">2</span>
                                </div>
                                <i class="bi bi-bandaid text-brand-500 text-xl hidden md:block"></i>
                                Service Details
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label for="service" class="block text-sm font-medium text-gray-700 mb-2">
                                        What service do they need? <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group-focus-within:ring-2 ring-brand-100 rounded-lg transition">
                                        <select name="service" id="service" required x-model="selectedService"
                                                class="block w-full pl-4 pr-10 py-4 text-base border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-0 rounded-xl transition-colors cursor-pointer shadow-sm">
                                            <option value="">-- Select Service Type --</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->name }}">
                                                    {{ $service->name }} ({{ $service->provider_type }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </div>
                                    @error('service')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Assign to a specific provider? <span class="text-gray-400 font-normal">(Optional)</span>
                                    </label>
                                    <div class="relative group-focus-within:ring-2 ring-brand-100 rounded-lg transition">
                                        <select name="doctor_id" id="doctor_id"
                                                class="block w-full pl-4 pr-10 py-4 text-base border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-0 rounded-xl transition-colors cursor-pointer shadow-sm">
                                            <option value="">-- Any Available Provider --</option>
                                            <template x-for="doctor in filteredDoctors" :key="doctor.id">
                                                <option :value="doctor.id" x-text="(doctor.role === 'doctor' ? 'Dr. ' : 'Midwife ') + doctor.full_name" :selected="doctor.id == '{{ old('doctor_id') }}'"></option>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Date & Time -->
                <div class="relative pl-0 md:pl-24 py-6 group" x-show="show" x-transition:enter="transition ease-out duration-500 delay-500" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
                    <!-- Timeline Dot -->
                    <div class="absolute left-4 top-10 w-8 h-8 rounded-full bg-white border-4 border-brand-200 shadow-sm z-10 hidden md:flex items-center justify-center transform transition-transform hover:scale-110">
                        <span class="text-brand-600 text-xs font-bold">3</span>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="p-6 md:p-8">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center md:hidden">
                                    <span class="font-bold">3</span>
                                </div>
                                <i class="bi bi-calendar-event text-brand-500 text-xl hidden md:block"></i>
                                Date & Time
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                        When is the appointment? <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group-focus-within:ring-2 ring-brand-100 rounded-lg transition">
                                        <input type="date" name="date" id="date" min="{{ date('Y-m-d') }}" value="{{ old('date') }}" required
                                               class="block w-full pl-4 pr-4 py-4 text-base border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-0 rounded-xl transition-colors shadow-sm">
                                    </div>
                                    @error('date')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="time" class="block text-sm font-medium text-gray-700 mb-2">
                                        What time? <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group-focus-within:ring-2 ring-brand-100 rounded-lg transition">
                                        <input type="time" name="time" id="time" value="{{ old('time') }}" required
                                               class="block w-full pl-4 pr-4 py-4 text-base border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-0 rounded-xl transition-colors shadow-sm">
                                    </div>
                                    @error('time')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Additional Notes -->
                <div class="relative pl-0 md:pl-24 py-6 group" x-show="show" x-transition:enter="transition ease-out duration-500 delay-700" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
                    <!-- Timeline Dot -->
                    <div class="absolute left-4 top-10 w-8 h-8 rounded-full bg-white border-4 border-brand-200 shadow-sm z-10 hidden md:flex items-center justify-center transform transition-transform hover:scale-110">
                        <span class="text-brand-600 text-xs font-bold">4</span>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="p-6 md:p-8">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center md:hidden">
                                    <span class="font-bold">4</span>
                                </div>
                                <i class="bi bi-pencil-square text-brand-500 text-xl hidden md:block"></i>
                                Additional Notes
                            </h3>
                            
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for visit or any special instructions <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <div class="group-focus-within:ring-2 ring-brand-100 rounded-lg transition">
                                <textarea id="reason" name="reason" rows="3"
                                          class="block w-full p-4 text-base border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-0 rounded-xl transition-colors shadow-sm"
                                          placeholder="E.g. Follow-up checkup, complaining of headache...">{{ old('reason') }}</textarea>
                            </div>
                            @error('reason')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 mt-8 md:pl-24">
                <a href="{{ route('healthworker.appointments.index') }}" 
                   class="px-8 py-4 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-10 py-4 bg-gradient-to-r from-brand-600 to-brand-700 text-white rounded-xl font-bold hover:from-brand-700 hover:to-brand-800 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-3 text-lg">
                    <span>Confirm Schedule</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </form>

        <!-- Add Patient Modal -->
        <div x-show="showModal" 
             class="fixed inset-0 z-[100] overflow-y-auto" 
             x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showModal = false"
                 class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm"></div>

            <!-- Modal Content -->
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2.5rem] border border-gray-100">
                
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        Register New Patient
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                <form @submit.prevent="submitNewPatient()" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">First Name</label>
                            <input type="text" x-model="newPatient.first_name" required
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Middle Initial <span class="text-gray-400 font-normal normal-case">(Optional)</span></label>
                            <input type="text" x-model="newPatient.middle_name" maxlength="10"
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" placeholder="E.g. M.">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Last Name</label>
                            <input type="text" x-model="newPatient.last_name" required
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Date of Birth</label>
                            <input type="date" x-model="newPatient.dob" required
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Gender</label>
                            <select x-model="newPatient.gender" required
                                    class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                                <option value="">-- Select --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Civil Status</label>
                            <select x-model="newPatient.civil_status" required
                                    class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                                <option value="">-- Select --</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Home Address</label>
                            <input type="text" x-model="newPatient.address" required
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Purok</label>
                            <input type="text" x-model="newPatient.purok" required
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Family Number <span class="text-gray-400 font-normal normal-case">(Optional)</span></label>
                            <input type="text" x-model="newPatient.family_no"
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" placeholder="E.g. FAM-2026-0001">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Contact Number</label>
                            <input type="text" x-model="newPatient.contact_no" required
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Allergies</label>
                            <textarea x-model="newPatient.allergies" rows="2" placeholder="e.g., Penicillin, Peanuts (or 'None')"
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all"></textarea>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Medical History / Background</label>
                            <textarea x-model="newPatient.medical_history" rows="2" placeholder="e.g., Hypertension, Diabetes, Past surgeries"
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all"></textarea>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Current Medications <span class="text-gray-400 font-normal normal-case">(Optional)</span></label>
                            <textarea x-model="newPatient.current_medications" rows="2" placeholder="e.g. Maintenance drugs, Vitamin supplements..."
                                   class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all"></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row gap-4">
                        <button type="button" @click="showModal = false"
                                class="w-full sm:w-auto px-8 py-4 text-gray-500 font-bold uppercase tracking-widest text-[10px] hover:text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                                class="flex-1 px-8 py-4 bg-brand-600 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl shadow-xl shadow-brand-500/20 hover:bg-brand-700 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                            <span x-show="!isSubmitting">Register Patient</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Registering...
                            </span>
                        </button>
                    </div>
                    <p class="text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                        <i class="bi bi-info-circle mr-1 text-brand-500"></i> A walk-in account will be automatically created without an email requirement.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
