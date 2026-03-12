@extends('layouts.app')

@section('title', 'Edit Appointment')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-pencil-square"></i> Edit Appointment
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Update appointment details and status for <span class="font-bold text-white">{{ $appointment->user->full_name }}</span>.</p>
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

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-exclamation-circle-fill text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
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
        {{-- Card Header --}}
        <div class="bg-gray-50/50 px-8 py-6 border-b border-gray-100 flex justify-between items-center">
             <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-calendar-event text-brand-600"></i>
                Appointment Details
            </h2>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider bg-white px-3 py-1 rounded-full border border-gray-100">
                Last updated: {{ $appointment->updated_at->diffForHumans() }}
            </span>
        </div>

        <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Patient (Read-only) --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Patient Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-person"></i>
                        </div>
                        <input type="text" value="{{ $appointment->user->full_name ?? 'N/A' }}" disabled
                               class="block w-full pl-10 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-gray-500 cursor-not-allowed sm:text-sm">
                    </div>
                </div>

                {{-- Service --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="service" class="block text-sm font-bold text-gray-700 mb-2">Service Type</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-bandaid"></i>
                        </div>
                         <select name="service" id="service" required
                               class="block w-full pl-10 pr-10 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm appearance-none">
                            @foreach($services as $service)
                                <option value="{{ $service->name }}" {{ old('service', $appointment->service) === $service->name ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ $service->provider_type }})
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
                        <input type="date" name="date" id="date" value="{{ old('date', optional($appointment->slot)->date ? $appointment->slot->date->format('Y-m-d') : '') }}"
                               class="block w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm" required>
                    </div>
                </div>

                {{-- Time --}}
                <div>
                    <label for="time" class="block text-sm font-bold text-gray-700 mb-2">Time</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-clock"></i>
                        </div>
                        <input type="time" name="time" id="time" value="{{ old('time', $appointment->slot->time ?? '') }}"
                               class="block w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm" required>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <select name="status" id="status" class="block w-full pl-10 pr-10 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm appearance-none">
                            <option value="pending" {{ $appointment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $appointment->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }}>Completed (Attended)</option>
                            <option value="rejected" {{ $appointment->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                         <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                     <p class="mt-2 text-xs text-gray-500">
                        <i class="bi bi-info-circle mr-1"></i>
                        Changing status to <strong>Approved</strong> will notify the patient.
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.appointments.index') }}"
                   class="px-6 py-3 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition flex items-center gap-2">
                    Cancel
                </a>

                <button type="submit"
                        class="px-8 py-3 rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="bi bi-save"></i> Update Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
