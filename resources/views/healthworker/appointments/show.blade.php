@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <nav class="flex mb-10" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <a href="{{ route('healthworker.dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center">
                        <i class="bi bi-house-door-fill"></i>
                    </div>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-300"></i>
                    <a href="{{ route('healthworker.appointments.index') }}" class="ml-4 text-sm font-black text-gray-400 hover:text-brand-600 transition-colors uppercase tracking-widest">Appointments</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-300"></i>
                    <span class="ml-4 text-sm font-black text-brand-600 uppercase tracking-widest">Details</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column: Patient Profile Card -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-center relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-brand-600 to-brand-400 opacity-5 group-hover:opacity-10 transition-opacity"></div>
                
                <!-- Avatar -->
                <div class="relative z-10 mb-6">
                    <div class="w-28 h-28 mx-auto bg-gradient-to-br from-brand-500 to-brand-600 rounded-[2rem] flex items-center justify-center text-white text-4xl font-black shadow-xl shadow-brand-100 ring-8 ring-white group-hover:scale-105 transition-transform duration-500">
                        {{ substr($appointment->display_patient_name, 0, 1) }}
                    </div>
                </div>
                
                <h2 class="text-2xl font-black text-gray-900 mb-1">{{ $appointment->display_patient_name }}</h2>
                <p class="text-sm text-brand-600 font-bold mb-8 bg-brand-50 inline-block px-4 py-1 rounded-full border border-brand-100">{{ $appointment->display_patient_email }}</p>

                @if($appointment->user)
                    <div class="border-t-2 border-dashed border-gray-100 pt-8 text-left space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Contact Number</p>
                                <p class="text-sm font-bold text-gray-900">{{ $appointment->user->contact_no ?: 'No contact provided' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Address</p>
                                <p class="text-sm font-bold text-gray-900 line-clamp-2">
                                    {{ $appointment->user->address ?: 'No address' }} {{ $appointment->user->purok ? ', Purok ' . $appointment->user->purok : '' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Gender</p>
                                <p class="text-sm font-bold text-gray-900">{{ ucfirst($appointment->user->gender ?: 'N/A') }}</p>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Age</p>
                                <p class="text-sm font-bold text-gray-900">{{ $appointment->user->age ? $appointment->user->age . ' Years' : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Appointment Clinical Info -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Status & Service Header -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-10 h-1 bg-brand-600 rounded-full"></span>
                            <span class="text-[10px] font-black text-brand-600 uppercase tracking-[0.3em]">Appointment Service</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-black text-gray-900 leading-tight">
                            {{ $appointment->service ?: 'General Consultation' }}
                        </h1>
                        <p class="text-gray-400 font-bold text-sm mt-2 flex items-center gap-2">
                            <i class="bi bi-hash"></i> Appointment ID: <span class="text-gray-600">#{{ $appointment->id }}</span>
                        </p>
                    </div>

                    @php
                        $status = $appointment->status;
                        $hasVitals = $appointment->healthRecord && $appointment->healthRecord->vital_signs;
                        
                        if ($status === 'approved') {
                            $label = $hasVitals ? 'Ready for Provider' : 'Wait for Vitals';
                            $statusClass = $hasVitals ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-indigo-50 text-indigo-600 border-indigo-200';
                        } elseif ($status === 'completed') {
                            $label = 'Finished';
                            $statusClass = 'bg-green-50 text-green-600 border-green-200';
                        } else {
                            $label = ucfirst($status);
                            $statusClass = 'bg-gray-50 text-gray-600 border-gray-100';
                        }
                    @endphp
                    <div class="flex flex-col items-center md:items-end">
                        <span class="px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-widest border-2 {{ $statusClass }} shadow-sm">
                            {{ $label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Schedule Bento Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Date Box -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 group hover:shadow-lg transition-all">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-brand-50 flex items-center justify-center text-brand-600 text-3xl shadow-inner group-hover:scale-110 transition-transform">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Scheduled Date</p>
                            <p class="text-xl font-black text-gray-900">{{ $appointment->formatted_date }}</p>
                            <p class="text-xs font-bold text-gray-400 mt-1 italic">Confirmed Schedule</p>
                        </div>
                    </div>
                </div>

                <!-- Time Box -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 group hover:shadow-lg transition-all">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-50 flex items-center justify-center text-indigo-600 text-3xl shadow-inner group-hover:scale-110 transition-transform">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Session Time</p>
                            <p class="text-xl font-black text-gray-900">{{ $appointment->formatted_time }}</p>
                            <p class="text-xs font-bold text-gray-400 mt-1 italic">Assigned Time Slot</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Table -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 px-10 py-6 border-b border-gray-100">
                    <h3 class="font-black text-gray-900 text-lg flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        Additional Information
                    </h3>
                </div>
                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Request Created</p>
                            <p class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-plus-circle text-gray-300"></i>
                                {{ $appointment->created_at->format('M d, Y') }} at {{ $appointment->created_at->format('h:i A') }}
                            </p>
                        </div>
                        @if($appointment->updated_at != $appointment->created_at)
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Last Updated</p>
                            <p class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-pencil-square text-gray-300"></i>
                                {{ $appointment->updated_at->format('M d, Y') }} at {{ $appointment->updated_at->format('h:i A') }}
                            </p>
                        </div>
                        @endif
                    </div>
                    <div class="space-y-6">
                        <div>
                            @php
                                $provider = $appointment->slot?->doctor;
                                $service = \App\Models\Service::where('name', $appointment->service)->first();
                                
                                // Priority for Label:
                                // 1. If a person is assigned, use their role (Doctor/Healthcare Provider)
                                // 2. Fallback to the Service's preferred provider type
                                // 3. Fallback to 'Service Provider'
                                if ($provider) {
                                    $providerLabel = $provider->isDoctor() ? 'Doctor' : ($provider->isMidwife() ? 'Healthcare Provider' : 'Service Provider');
                                } elseif ($service && $service->provider_type && $service->provider_type !== 'Both') {
                                    $serviceProviderMap = [
                                        'Midwife' => 'Healthcare Provider',
                                    ];
                                    $providerLabel = $service->provider_type;
                                    $providerLabel = $serviceProviderMap[$providerLabel] ?? $providerLabel;
                                } else {
                                    $providerLabel = 'Service Provider';
                                }

                                // Helper to get clean name without double prefixes
                                $displayName = $provider ? $provider->full_name : 'Any Available ' . ($providerLabel !== 'Service Provider' ? $providerLabel : 'Clinician');
                                
                                if ($provider) {
                                     $prefix = $provider->isDoctor() ? 'Dr. ' : ($provider->isMidwife() ? 'Healthcare Provider ' : '');
                                     
                                     // Don't add prefix if the name already starts with it or similar titles
                                     $lowerName = strtolower($displayName);
                                     if (str_starts_with($lowerName, 'dr.') || 
                                         str_starts_with($lowerName, 'doctor') || 
                                         str_starts_with($lowerName, 'doc') || 
                                         str_starts_with($lowerName, 'midwife') ||
                                         str_starts_with($lowerName, 'healthcare provider')) {
                                         $prefix = '';
                                     }
                                     $displayName = $prefix . $displayName;
                                 }
                            @endphp
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ $providerLabel }}</p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shadow-inner">
                                    <i class="bi bi-person-badge-fill text-lg"></i>
                                </div>
                                <div class="text-sm font-bold text-gray-900">
                                    {{ $displayName }}
                                </div>
                            </div>
                        </div>
                        @if($appointment->slot)
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Slot Capacity</p>
                            <div class="flex items-center gap-4">
                                <div class="flex-grow bg-gray-100 h-2 rounded-full overflow-hidden">
                                    @php
                                        $slotWidth = ($appointment->slot->allocated / $appointment->slot->capacity) * 100;
                                    @endphp
                                    <div class="bg-brand-500 h-full rounded-full" @style(['width' => $slotWidth . '%'])></div>
                                </div>
                                <p class="text-xs font-black text-gray-600 whitespace-nowrap">
                                    {{ $appointment->slot->allocated }}/{{ $appointment->slot->capacity }} Booked
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
