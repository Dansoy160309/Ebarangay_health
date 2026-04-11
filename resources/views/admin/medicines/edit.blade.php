@extends('layouts.app')

@section('title', 'Edit Medicine')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-brand-600 transition-colors group mb-4">
                <i class="bi bi-arrow-left me-2 group-hover:-translate-x-1 transition-transform"></i>
                Back to Inventory
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <span class="p-3 bg-brand-50 rounded-2xl text-brand-600 shadow-sm">
                    <i class="bi bi-capsule"></i>
                </span>
                Edit Medicine Details
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-400">Last updated:</span>
            <span class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-md">{{ $medicine->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.medicines.update', $medicine) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3">
                {{-- Left Panel: Basic Info --}}
                <div class="md:col-span-2 p-10">
                    <div class="bg-gradient-to-r from-brand-600 to-brand-500 px-8 py-6 rounded-3xl mb-8">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="bi bi-info-circle-fill"></i>
                            Medicine Information
                        </h3>
                        <p class="text-brand-100 text-sm">Update the details for {{ $medicine->generic_name }} ({{ $medicine->brand_name }})</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        {{-- Generic Name --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="bi bi-tag-fill text-brand-500"></i>
                                Generic Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}"
                                   class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300 shadow-sm"
                                   required placeholder="e.g. Paracetamol">
                            @error('generic_name')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Brand Name --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="bi bi-bookmark-fill text-brand-500"></i>
                                Brand Name
                            </label>
                            <input type="text" name="brand_name" value="{{ old('brand_name', $medicine->brand_name) }}"
                                   class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300 shadow-sm"
                                   placeholder="e.g. Biogesic">
                            @error('brand_name')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Dosage Form --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="bi bi-box-fill text-brand-500"></i>
                                Dosage Form <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="dosage_form"
                                        class="w-full px-4 pr-10 py-3 rounded-xl border-gray-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all shadow-sm appearance-none"
                                        required>
                                    <option value="Tablet" {{ old('dosage_form', $medicine->dosage_form) === 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                    <option value="Capsule" {{ old('dosage_form', $medicine->dosage_form) === 'Capsule' ? 'selected' : '' }}>Capsule</option>
                                    <option value="Syrup" {{ old('dosage_form', $medicine->dosage_form) === 'Syrup' ? 'selected' : '' }}>Syrup</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="bi bi-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('dosage_form')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Strength --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="bi bi-lightning-fill text-brand-500"></i>
                                Strength <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="strength" value="{{ old('strength', $medicine->strength) }}"
                                   class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300 shadow-sm"
                                   required placeholder="e.g. 500mg, 120mg/5ml">
                            @error('strength')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Right Panel: Inventory & Settings --}}
                <div class="bg-gray-50/70 p-10 border-l border-gray-100">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-8 py-6 rounded-3xl mb-8">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="bi bi-clipboard-data-fill"></i>
                            Inventory Settings
                        </h3>
                        <p class="text-blue-100 text-sm">Manage stock levels and expiration.</p>
                    </div>

                    <div class="space-y-8">
                        {{-- Current Stock (Read Only) --}}
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block">Current Stock</label>
                            <div class="flex items-center gap-3">
                                <span class="text-3xl font-black text-gray-900">{{ $medicine->stock }}</span>
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $medicine->stock <= $medicine->reorder_level ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                                    {{ $medicine->stock <= $medicine->reorder_level ? 'Low Stock' : 'Good' }}
                                </span>
                            </div>
                        </div>

                        {{-- Reorder Level --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="bi bi-graph-down text-blue-500"></i>
                                Reorder Level <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="reorder_level" min="0" value="{{ old('reorder_level', $medicine->reorder_level) }}"
                                   class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm"
                                   required>
                            @error('reorder_level')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Expiration Date --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="bi bi-calendar-x text-blue-500"></i>
                                Expiration Date
                            </label>
                            <div class="relative">
                                <input type="date" name="expiration_date" value="{{ old('expiration_date', optional($medicine->expiration_date)->format('Y-m-d')) }}"
                                       class="w-full px-4 pr-12 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm">
                                <span onclick="const input=this.previousElementSibling; if(input?.showPicker){input.showPicker();} else {input?.focus();}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-blue-500 cursor-pointer">
                                    <i class="bi bi-calendar-event text-base"></i>
                                </span>
                            </div>
                            @error('expiration_date')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 p-10 border-t border-gray-100">
                <a href="{{ route('admin.medicines.index') }}" 
                   class="px-8 py-3 bg-white text-gray-600 border border-gray-200 rounded-2xl font-bold hover:bg-gray-50 hover:text-gray-900 transition-all active:scale-95 shadow-sm">
                    Discard Changes
                </a>
                <button type="submit" 
                        class="px-10 py-3 bg-brand-600 text-white rounded-2xl font-bold hover:bg-brand-700 shadow-lg shadow-brand-200 transition-all active:scale-95 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    Update Medicine
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
