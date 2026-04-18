@extends('layouts.app')

@section('title', 'Add Service')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-plus-circle-fill"></i> Add New Service
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Create a new medical service available for appointments.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.services.index') }}" 
                   class="inline-flex items-center px-5 py-3 rounded-xl bg-white/10 text-white font-medium hover:bg-white/20 border border-white/20 transition-all">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Back to Services
                </a>
            </div>
        </div>
    </div>

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
        <form action="{{ route('admin.services.store') }}" method="POST" class="p-8 space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Service Name --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Service Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-capsule"></i>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="block w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                               placeholder="e.g. General Checkup">
                    </div>
                </div>

                {{-- Description --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <div class="relative">
                        <textarea name="description" id="description" rows="4"
                                  class="block w-full p-4 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                                  placeholder="Briefly describe what this service entails...">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Provider Type --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-4">Provider Type</label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Doctor Option --}}
                        <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all group">
                            <input type="radio" name="provider_type" value="Doctor" class="peer sr-only" {{ old('provider_type') == 'Doctor' ? 'checked' : '' }} required>
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-3 peer-checked:bg-blue-600 peer-checked:text-white transition-colors">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <span class="font-bold text-gray-900 peer-checked:text-blue-700">Doctor</span>
                            <span class="text-xs text-gray-500 text-center mt-1">Requires doctor assignment</span>
                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-blue-500 rounded-xl pointer-events-none"></div>
                        </label>

                        {{-- Healthcare Provider Option --}}
                        <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-pink-50 hover:border-pink-200 transition-all group">
                            <input type="radio" name="provider_type" value="Midwife" class="peer sr-only" {{ old('provider_type') == 'Midwife' ? 'checked' : '' }}>
                            <div class="w-12 h-12 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center text-2xl mb-3 peer-checked:bg-pink-600 peer-checked:text-white transition-colors">
                                <i class="bi bi-person-hearts"></i>
                            </div>
                            <span class="font-bold text-gray-900 peer-checked:text-pink-700">Healthcare Provider</span>
                            <span class="text-xs text-gray-500 text-center mt-1">Managed by healthcare providers</span>
                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-pink-500 rounded-xl pointer-events-none"></div>
                        </label>

                        {{-- Both Option --}}
                        <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-200 transition-all group">
                            <input type="radio" name="provider_type" value="Both" class="peer sr-only" {{ old('provider_type') == 'Both' ? 'checked' : '' }}>
                            <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-2xl mb-3 peer-checked:bg-purple-600 peer-checked:text-white transition-colors">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <span class="font-bold text-gray-900 peer-checked:text-purple-700">Both</span>
                            <span class="text-xs text-gray-500 text-center mt-1">Available to all</span>
                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-purple-500 rounded-xl pointer-events-none"></div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                <a href="{{ route('admin.services.index') }}" class="px-6 py-3 rounded-xl text-gray-600 font-medium hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-brand-600 text-white font-bold shadow-lg shadow-brand-600/30 hover:bg-brand-700 hover:scale-105 transition-all transform">
                    Create Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
