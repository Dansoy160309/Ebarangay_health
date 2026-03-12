{{-- resources/views/patient/appointments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
    <h1 class="text-3xl font-extrabold mb-6 text-center text-brand-700">📅 Book an Appointment</h1>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Appointment Form --}}
    <form action="{{ route('patient.appointments.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Service Selection --}}
        <div>
            <label for="service" class="block font-semibold mb-2 text-gray-700">Service</label>
            <select name="service" id="service" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-400 focus:border-brand-400"
                required>
                <option value="">-- Choose a service --</option>
                @foreach($services as $service)
                    <option value="{{ $service }}" {{ old('service', request('service')) == $service ? 'selected' : '' }}>
                        {{ $service }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Appointment Date --}}
        <div>
            <label for="appointment_date" class="block font-semibold mb-2 text-gray-700">Appointment Date</label>
            <input type="date" name="appointment_date" id="appointment_date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-400 focus:border-brand-400"
                value="{{ old('appointment_date', request('date')) }}"
                min="{{ date('Y-m-d') }}" required>
        </div>

        {{-- Slots Container --}}
        <div id="slots-container" class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg min-h-[80px] flex items-center justify-center">
            <p class="text-gray-500 text-center">Please select a date and service to view available slots.</p>
        </div>

        {{-- Submit Button --}}
        <button type="submit" 
            class="w-full bg-brand-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-brand-700 transition transform hover:-translate-y-0.5">
            Confirm Booking
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
{{ file_get_contents(resource_path('views/appointments/partials/load-slots.js')) ?? '' }}
</script>
@endsection
