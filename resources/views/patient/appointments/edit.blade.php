{{-- resources/views/patient/appointments/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Reschedule Appointment')

@section('content')

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Reschedule Appointment</h2>
        <a href="{{ route('patient.appointments.index') }}" class="text-brand-600 hover:underline">
            ← Back to Appointments
        </a>
    </div>

    {{-- Appointment Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h4 class="font-semibold text-gray-700 mb-2">Current Appointment</h4>
        <p><strong>Service:</strong> {{ $appointment->slot->service ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->slot->date)->format('M d, Y') }}</p>
        <p><strong>Time:</strong> {{ $appointment->slot->time ?? 'N/A' }}</p>
        <p><strong>Status:</strong> 
            <span class="capitalize px-3 py-1 rounded-full text-sm
                @switch($appointment->status)
                    @case('approved') bg-green-100 text-green-700 @break
                    @case('pending') bg-yellow-100 text-yellow-700 @break
                    @case('cancelled') bg-gray-200 text-gray-700 @break
                    @default bg-blue-100 text-blue-700
                @endswitch">
                {{ $appointment->status }}
            </span>
        </p>
    </div>

    {{-- Reschedule Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h4 class="font-semibold text-gray-700 mb-4">Select New Slot</h4>

        <form action="{{ route('patient.appointments.update', $appointment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Available Slot</label>
                    <select name="slot_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-500 focus:border-brand-500">
                        @foreach($availableSlots as $slot)
                            <option value="{{ $slot->id }}">
                                {{ $slot->service }} — {{ \Carbon\Carbon::parse($slot->date)->format('M d, Y') }} ({{ $slot->time }})
                            </option>
                        @endforeach
                    </select>
                    @error('slot_id')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('patient.appointments.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 font-medium">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-medium">
                    Update Appointment
                </button>
            </div>
        </form>
    </div>

@endsection
