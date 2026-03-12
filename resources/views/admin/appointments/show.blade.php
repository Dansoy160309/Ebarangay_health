{{-- resources/views/admin/appointments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.appointments.index') }}" class="hover:text-brand-600 transition-colors">Appointments</a>
                <i class="bi bi-chevron-right text-xs"></i>
                <span>Details</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Appointment #{{ $appointment->id }}</h2>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-3">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-50 text-yellow-600',
                    'approved' => 'bg-green-50 text-green-600',
                    'rejected' => 'bg-red-50 text-red-600',
                    'rescheduled' => 'bg-blue-50 text-blue-600',
                    'cancelled' => 'bg-gray-50 text-gray-600',
                ];
                $statusColor = $statusColors[$appointment->status] ?? 'bg-gray-50 text-gray-600';
            @endphp
            <span class="px-4 py-2 rounded-full text-xs font-extrabold uppercase tracking-wide {{ $statusColor }}">
                {{ ucfirst($appointment->status) }}
            </span>
            <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 font-medium transition-colors shadow-sm text-sm">
                <i class="bi bi-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Appointment Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-calendar-event text-brand-600"></i>
                        Appointment Information
                    </h3>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Service Requested</label>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                                <i class="bi bi-bandaid text-xl"></i>
                            </div>
                            <span class="text-xl font-semibold text-gray-900">
                                {{ $appointment->slot->service ?? $appointment->service ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Date & Time</label>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="bi bi-clock text-xl"></i>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $appointment->slot ? $appointment->slot->date->format('F d, Y') : 'Date N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($appointment->slot)
                                        {{ $appointment->slot->displayTime() }}
                                    @else
                                        Time N/A
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            @if($appointment->status === 'pending' && $appointment->type === 'walk-in')
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-lightning-charge text-yellow-600"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-8">
                    <p class="text-gray-600 mb-6">This walk-in appointment is currently pending. You can approve or reject it below.</p>
                    <div class="flex gap-4">
                        <form action="{{ route('admin.appointments.approve', $appointment->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold transition-all shadow-sm hover:shadow-md">
                                <i class="bi bi-check-lg text-xl"></i> Approve Appointment
                            </button>
                        </form>

                        <form action="{{ route('admin.appointments.reject', $appointment->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-white border-2 border-red-100 text-red-600 rounded-xl hover:bg-red-50 hover:border-red-200 font-bold transition-all">
                                <i class="bi bi-x-lg text-xl"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Patient Info --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <div class="px-6 py-5 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-person text-gray-600"></i>
                        Patient Details
                    </h3>
                </div>
                <div class="p-6 flex flex-col items-center text-center border-b border-gray-50">
                    <div class="w-24 h-24 bg-gradient-to-br from-brand-100 to-brand-200 rounded-full flex items-center justify-center text-brand-700 text-3xl font-bold mb-4 shadow-inner">
                        {{ substr($appointment->user->full_name ?? 'U', 0, 1) }}
                    </div>
                    <h4 class="text-xl font-bold text-gray-900">{{ $appointment->user->full_name ?? 'Unknown User' }}</h4>
                    <p class="text-gray-500 text-sm mt-1">{{ $appointment->user->email ?? 'No email' }}</p>
                </div>
                <div class="p-6 bg-gray-50/50">
                     <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Contact</span>
                            <span class="text-sm font-medium text-gray-700">{{ $appointment->user->contact_no ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Age / Gender</span>
                            <span class="text-sm font-medium text-gray-700">
                                {{ $appointment->user->age ?? '-' }} / {{ ucfirst($appointment->user->gender ?? '-') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Purok</span>
                            <span class="text-sm font-medium text-gray-700">{{ $appointment->user->purok ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Address</span>
                            <p class="text-sm font-medium text-gray-700 leading-relaxed">
                                {{ $appointment->user->address ?? 'No address provided' }}
                            </p>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
