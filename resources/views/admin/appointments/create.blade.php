@extends('layouts.app')

@section('title', 'Add Appointment')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-calendar-plus-fill"></i> Add Appointment
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Schedule a new appointment for a patient.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.appointments.index') }}" 
                   class="inline-flex items-center px-5 py-3 rounded-xl bg-white/10 text-white font-medium hover:bg-white/20 border border-white/20 transition-all">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm animate-fade-in-down">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-check-circle-fill text-green-400 text-xl"></i>
                </div>
                <div class="ml-3 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-exclamation-circle-fill text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.appointments.store') }}" method="POST" class="p-8 space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Patient Select --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="patient_id" class="block text-sm font-bold text-gray-700 mb-2">Select Patient</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-person"></i>
                        </div>
                        <select name="patient_id" id="patient_id" class="block w-full pl-10 pr-10 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm appearance-none" required>
                            <option value="">-- Choose a patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->email ?? 'No email' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                </div>

                {{-- Service Select --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="service" class="block text-sm font-bold text-gray-700 mb-2">Service</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-bandaid"></i>
                        </div>
                        <select name="service" id="service" required
                               class="block w-full pl-10 pr-10 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm appearance-none">
                            <option value="">-- Select Service Type --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->name }}" {{ old('service') == $service->name ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ $service->provider_type }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                </div>

                {{-- Doctor Select --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="doctor_id" class="block text-sm font-bold text-gray-700 mb-2">Assign to Doctor (Optional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <select name="doctor_id" id="doctor_id"
                               class="block w-full pl-10 pr-10 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm appearance-none">
                            <option value="">-- Any Available / Healthcare Provider --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                </div>

                {{-- Date --}}
                <div>
                    <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <input type="date" name="date" id="date" min="{{ date('Y-m-d') }}" value="{{ old('date') }}" required
                            class="block w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    </div>
                </div>

                {{-- Time --}}
                <div>
                    <label for="time" class="block text-sm font-bold text-gray-700 mb-2">Time</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-clock"></i>
                        </div>
                        <input type="time" name="time" id="time" value="{{ old('time') }}" required
                            class="block w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    </div>
                </div>

                {{-- Reason --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="reason" class="block text-sm font-bold text-gray-700 mb-2">Reason (Optional)</label>
                    <textarea name="reason" id="reason" rows="3"
                              class="block w-full px-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                              placeholder="E.g. Follow-up checkup...">{{ old('reason') }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.appointments.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-brand-600 text-white rounded-xl font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-600/20 transform hover:-translate-y-0.5">
                    <i class="bi bi-plus-lg mr-2"></i> Save Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
