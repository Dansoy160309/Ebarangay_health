@extends('layouts.app')

@section('title', 'Edit Appointment Slot')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

    {{-- Breadcrumbs --}}
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-[0.2em]">
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
                    <span class="text-brand-600">Edit Slot</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="bg-white rounded-[3.5rem] p-10 lg:p-14 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-50 rounded-full blur-3xl -mr-48 -mt-48 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-50 rounded-full blur-3xl -ml-32 -mb-32 opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-12">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-2xl bg-brand-50 text-brand-600 border border-brand-100 mb-6">
                    <i class="bi bi-pencil-square text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Clinical Editor</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tight leading-[1.1]">
                    Edit Appointment <span class="text-brand-600 underline decoration-brand-200 decoration-8 underline-offset-4">Slot</span>
                </h1>
                <p class="text-gray-500 font-medium mt-6 text-lg leading-relaxed">
                    Update the details for this appointment slot. Changes will be reflected immediately in the patient booking portal.
                </p>
            </div>
            
            <div class="flex flex-col gap-4 w-full lg:w-auto">
                <a href="{{ route('midwife.slots.index') }}" 
                   class="px-10 py-5 bg-white text-gray-500 rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] border border-gray-200 hover:bg-gray-50 transition-all text-center flex items-center justify-center gap-3">
                    <i class="bi bi-arrow-left"></i> Back to Slots
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-[2.5rem] border border-red-200 bg-red-50 px-8 py-6 text-sm text-red-800 flex items-start gap-4 animate-fade-in-down shadow-sm">
            <i class="bi bi-exclamation-triangle-fill text-2xl mt-0.5"></i>
            <div>
                <p class="font-black uppercase tracking-widest text-[10px] mb-2">Correction Required</p>
                <ul class="list-disc list-inside space-y-1 font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-[3.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <x-slots.form 
            :action="route('midwife.slots.update', $slot->id)" 
            :method="'PUT'" 
            :buttonText="'Update Slot'" 
            :doctors="$doctors" 
            :services="$services" 
            :slotModel="$slot" 
        />
    </div>
</div>
@endsection
