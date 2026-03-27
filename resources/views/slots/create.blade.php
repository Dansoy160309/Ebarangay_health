@extends('layouts.app')

@section('title', 'Add Appointment Slot')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-4">

    {{-- Breadcrumbs --}}
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 text-[9px] font-black uppercase tracking-tighter">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center text-gray-400">
                    <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                    <a href="{{ route('midwife.slots.index') }}" class="hover:text-brand-600 transition-colors">Appointment Slots</a>
                </div>
            </li>
            <li>
                <div class="flex items-center text-gray-400">
                    <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                    <span class="text-brand-600">Add New Slot</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-blue-50 rounded-full blur-2xl -ml-20 -mb-20 opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-3">
                    <i class="bi bi-calendar-plus-fill text-[8px]"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">Scheduling Center</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight leading-tight">
                    Add Appointment <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Slot</span>
                </h1>
                <p class="text-gray-500 font-medium mt-3 text-xs lg:text-sm leading-relaxed">
                    Configure a new time window for patient consultations. Set the service type, provider, and maximum patient capacity.
                </p>
            </div>
            
            <div class="flex flex-col gap-2 w-full lg:w-auto">
                <a href="{{ route('midwife.slots.index') }}" 
                   class="px-4 py-2.5 bg-white text-gray-500 rounded-lg font-black text-[9px] uppercase tracking-tighter border border-gray-200 hover:bg-gray-50 transition-all text-center flex items-center justify-center gap-2">
                    <i class="bi bi-arrow-left text-xs"></i> Back to Slots
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-xs text-red-800 flex items-start gap-3 animate-fade-in-down shadow-sm">
            <i class="bi bi-exclamation-triangle-fill text-lg mt-0.5 shrink-0"></i>
            <div>
                <p class="font-black uppercase tracking-tighter text-[9px] mb-1.5">Correction Required</p>
                <ul class="list-disc list-inside space-y-0.5 font-bold text-[10px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <x-slots.form :action="route('midwife.slots.store')" :buttonText="'Create Slot'" :doctors="$doctors" :services="$services" :availabilities="$availabilities" />
    </div>
</div>
@endsection
