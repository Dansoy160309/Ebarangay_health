{{-- resources/views/patient/appointments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="container mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Appointment Details</h2>
        <a href="{{ route('patient.appointments.index') }}" class="text-brand-600 hover:underline">
            ← Back to Appointments
        </a>
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h4 class="text-lg font-semibold text-gray-700 mb-4">Service Information</h4>
        <p><strong>Service:</strong> {{ $appointment->slot->service ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->slot->date)->format('M d, Y') }}</p>
        <p><strong>Time:</strong> {{ $appointment->slot->time ?? 'N/A' }}</p>

        <hr class="my-4">

        <h4 class="text-lg font-semibold text-gray-700 mb-4">Status</h4>
        <p>
            <span class="capitalize px-3 py-1 rounded-full text-sm
                @switch($appointment->status)
                    @case('approved') bg-green-100 text-green-700 @break
                    @case('pending') bg-yellow-100 text-yellow-700 @break
                    @case('rejected') bg-red-100 text-red-700 @break
                    @case('cancelled') bg-gray-200 text-gray-700 @break
                    @default bg-blue-100 text-blue-700
                @endswitch">
                {{ $appointment->status }}
            </span>
        </p>

        <hr class="my-4">

        <h4 class="text-lg font-semibold text-gray-700 mb-4">Actions</h4>
        <div class="flex space-x-3">
            @if(!in_array($appointment->status, ['cancelled', 'rejected', 'completed']))
                <form action="{{ route('patient.appointments.cancel', $appointment->id) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                        Cancel Appointment
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500 italic">No actions available for this appointment status.</p>
            @endif
        </div>
    </div>

</div>
@endsection
