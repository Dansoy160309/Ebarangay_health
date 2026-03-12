{{-- resources/views/healthworker/appointments/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Appointment')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <div class="bg-brand-100 p-3 rounded-xl">
            <i class="bi bi-pencil-square text-2xl text-brand-600"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Appointment</h1>
            <p class="text-gray-500 text-sm">Update appointment details and status</p>
        </div>
    </div>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-start gap-3">
            <i class="bi bi-exclamation-circle-fill text-xl mt-0.5"></i>
            <div>
                <h3 class="font-bold text-sm mb-1">Please fix the following errors:</h3>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        {{-- Card Header --}}
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-gray-700 flex items-center gap-2">
                <i class="bi bi-calendar-event text-brand-600"></i>
                Appointment #{{ $appointment->id }}
            </h2>
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Last updated: {{ $appointment->updated_at->diffForHumans() }}</span>
        </div>

        <form action="{{ route('healthworker.appointments.update', $appointment->id) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Patient (Read-only) --}}
                <div class="col-span-full">
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Patient Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-person text-gray-400"></i>
                        </div>
                        <input type="text" value="{{ $appointment->user->full_name ?? 'N/A' }}" disabled
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed text-sm">
                    </div>
                </div>

                {{-- Service --}}
                <div class="col-span-full">
                    <label for="service" class="block mb-2 text-sm font-semibold text-gray-700">Service Type</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-bandaid text-gray-400"></i>
                        </div>
                        <select name="service" id="service" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition text-sm text-gray-800">
                            @foreach($services as $service)
                                <option value="{{ $service->name }}" {{ old('service', $appointment->service) === $service->name ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ $service->provider_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Date --}}
                <div>
                    <label for="date" class="block mb-2 text-sm font-semibold text-gray-700">Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-calendar3 text-gray-400"></i>
                        </div>
                        <input type="date" name="date" id="date" value="{{ old('date', optional($appointment->slot->date)->format('Y-m-d')) }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition text-sm text-gray-800" required>
                    </div>
                </div>

                {{-- Time --}}
                <div>
                    <label for="time" class="block mb-2 text-sm font-semibold text-gray-700">Time</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-clock text-gray-400"></i>
                        </div>
                        <input type="time" name="time" id="time" value="{{ old('time', $appointment->slot->time ?? '') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition text-sm text-gray-800" required>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-span-full">
                    <label for="status" class="block mb-2 text-sm font-semibold text-gray-700">Status</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-check-circle text-gray-400"></i>
                        </div>
                        <select name="status" id="status" class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition text-sm text-gray-800 appearance-none bg-no-repeat bg-[right_1rem_center]">
                            <option value="pending" {{ $appointment->status === 'pending' ? 'selected' : '' }} class="text-yellow-600">Pending</option>
                            <option value="approved" {{ $appointment->status === 'approved' ? 'selected' : '' }} class="text-green-600">Approved</option>
                            <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }} class="text-blue-600">Completed (Attended)</option>
                            <option value="rejected" {{ $appointment->status === 'rejected' ? 'selected' : '' }} class="text-red-600">Rejected</option>
                            <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }} class="text-gray-600">Cancelled</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="bi bi-info-circle mr-1"></i>
                        Changing status to <strong>Approved</strong> will notify the patient.
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('healthworker.appointments.index') }}"
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition flex items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>

                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition flex items-center gap-2">
                    <i class="bi bi-save"></i> Update Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
