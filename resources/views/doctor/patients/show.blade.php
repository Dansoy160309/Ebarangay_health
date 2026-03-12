@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <nav class="mb-6 flex items-center text-sm text-gray-500">
        <a href="{{ route('doctor.dashboard') }}" class="hover:text-brand-600 transition">Dashboard</a>
        <i class="bi bi-chevron-right mx-2 text-xs"></i>
        <a href="{{ route('doctor.patients.index') }}" class="hover:text-brand-600 transition">Patients</a>
        <i class="bi bi-chevron-right mx-2 text-xs"></i>
        <span class="text-gray-900 font-medium">Patient Details</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col md:flex-row gap-8 items-start">
                <div class="flex-shrink-0 flex flex-col items-center md:items-start text-center md:text-left">
                    <div class="w-24 h-24 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 text-3xl font-bold mb-4 border-4 border-white shadow-sm">
                        {{ substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1) }}
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $patient->full_name }}</h1>
                    <div class="mt-1 flex items-center gap-2 text-sm text-gray-500">
                        <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">
                            Code: {{ $patient->patient_code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full font-medium text-xs {{ $patient->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $patient->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 w-full border-t md:border-t-0 md:border-l border-gray-100 pt-6 md:pt-0 md:pl-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="bi bi-person text-brand-500"></i> Personal Information
                        </h3>
                        <dl class="space-y-3 text-sm">
                            @if($patient->guardian_id)
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-gray-500">Dependent of</dt>
                                <dd class="col-span-2 text-gray-900 font-medium">
                                    {{ $patient->guardian->full_name }}
                                    <span class="text-xs text-gray-500 block mt-0.5">{{ $patient->relationship ?? 'Dependent' }}</span>
                                </dd>
                            </div>
                            @endif
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-gray-500">Birth Date</dt>
                                <dd class="col-span-2 text-gray-900 font-medium">
                                    {{ $patient->dob->format('M d, Y') }}
                                    <span class="text-gray-400 font-normal">({{ $patient->dob->age }} yrs)</span>
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-gray-500">Gender</dt>
                                <dd class="col-span-2 text-gray-900 font-medium">{{ ucfirst($patient->gender) }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-gray-500">Contact</dt>
                                <dd class="col-span-2 text-gray-900 font-medium flex items-center gap-2">
                                    <i class="bi bi-telephone text-gray-400"></i> {{ $patient->contact_no }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-gray-500">Email</dt>
                                <dd class="col-span-2 text-gray-900 font-medium truncate" title="{{ $patient->email }}">
                                    {{ $patient->email }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-gray-500">Address</dt>
                                <dd class="col-span-2 text-gray-900 font-medium leading-relaxed">
                                    {{ $patient->address }}, {{ $patient->purok }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="bi bi-heart-pulse text-red-500"></i> Medical Information
                        </h3>
                        @php($profile = $patient->patientProfile)
                        <dl class="space-y-4 text-sm">
                            <div>
                                <dt class="text-gray-500 mb-1">Blood Type</dt>
                                <dd class="text-gray-900 font-medium">
                                    @if($profile && $profile->blood_type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $profile->blood_type }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Not recorded</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">Allergies</dt>
                                <dd class="text-gray-900 font-medium bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    {{ $profile && $profile->allergies ? $profile->allergies : 'None known' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">Medical History</dt>
                                <dd class="text-gray-900 font-medium bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    {{ $profile && $profile->medical_history ? $profile->medical_history : 'None recorded' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">Current Medications</dt>
                                <dd class="text-gray-900 font-medium bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    {{ $profile && $profile->current_medications ? $profile->current_medications : 'None recorded' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

