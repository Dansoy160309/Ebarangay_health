@extends('layouts.app')

@section('title', 'Add Medicine')

<style>
.medicine-form input,
.medicine-form select,
.medicine-form textarea {
    background-color: #ffffff !important;
    color: #111827 !important;
    color-scheme: light;
}

.medicine-form input:-webkit-autofill,
.medicine-form input:-webkit-autofill:hover,
.medicine-form input:-webkit-autofill:focus,
.medicine-form select:-webkit-autofill {
    -webkit-text-fill-color: #111827;
    -webkit-box-shadow: 0 0 0px 1000px #ffffff inset;
    transition: background-color 5000s ease-in-out 0s;
}
</style>

@section('content')
<div class="w-full max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium text-gray-500">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors flex items-center">
                    <i class="bi bi-house-door mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400 mx-2 text-[10px]"></i>
                    <a href="{{ route('admin.medicines.index') }}" class="hover:text-brand-600 transition-colors">Inventory</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400 mx-2 text-[10px]"></i>
                    <span class="text-gray-900 font-bold">Add Medicine</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-2">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-1">Register Medicine</h1>
            <p class="text-sm text-gray-500 font-medium max-w-2xl">Add a new medicine to the inventory and configure its base properties.</p>
        </div>
        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
            Full-width workspace
        </div>
    </div>

    <div class="bg-white rounded-[2.25rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-5 md:p-6 overflow-hidden relative">
        {{-- Decorative background --}}
        <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-brand-50/50 -z-0"></div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50/50 p-5 text-sm text-red-800 animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="font-black uppercase tracking-widest text-[11px]">Submission Errors</div>
                </div>
                <ul class="list-disc list-inside space-y-1 font-medium ml-11">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.medicines.store') }}" class="medicine-form space-y-8 relative z-10">
            @csrf

            {{-- Basic Information --}}
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <i class="bi bi-info-circle text-lg"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900">Basic Information</h2>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div class="space-y-2.5">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Generic Name</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-capsule"></i>
                            </div>
                            <input type="text" name="generic_name" value="{{ old('generic_name') }}"
                                   placeholder="e.g. Paracetamol"
                                   class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Brand Name (Optional)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-tag"></i>
                            </div>
                            <input type="text" name="brand_name" value="{{ old('brand_name') }}"
                                   placeholder="e.g. Biogesic"
                                   class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Dosage Form</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-layers"></i>
                            </div>
                            <select name="dosage_form"
                                class="w-full pl-12 pr-10 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm appearance-none">
                                <option value="" disabled {{ old('dosage_form') ? '' : 'selected' }}>Select dosage form</option>
                                <option value="Tablet" {{ old('dosage_form') === 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="Capsule" {{ old('dosage_form') === 'Capsule' ? 'selected' : '' }}>Capsule</option>
                                <option value="Syrup" {{ old('dosage_form') === 'Syrup' ? 'selected' : '' }}>Syrup</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Strength</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <input type="text" name="strength" value="{{ old('strength') }}"
                                   placeholder="e.g. 500mg, 125mg/5mL"
                                class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory Configuration --}}
            <div class="space-y-5 pt-1">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center">
                        <i class="bi bi-box-seam text-lg"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900">Inventory Settings</h2>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div class="space-y-2.5">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Initial Stock</label>
                        <div class="relative group">
                            <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-black transition-all shadow-sm">
                            <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Units
                            </div>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide ml-1">Initial quantity on hand</p>
                    </div>

                    <div class="space-y-2.5">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Reorder Level</label>
                        <div class="relative group">
                            <input type="number" name="reorder_level" min="0" value="{{ old('reorder_level', 0) }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-black transition-all shadow-sm">
                            <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Alert at
                            </div>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide ml-1">Low stock notification threshold</p>
                    </div>

                </div>

            </div>

            <div class="flex items-center justify-end gap-3 pt-8 border-t border-gray-50">
                <a href="{{ route('admin.medicines.index') }}" class="px-8 py-4 rounded-2xl text-sm font-black text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                    Cancel
                </a>
                <button type="submit" class="px-12 py-4 rounded-2xl bg-gray-900 text-white text-sm font-black hover:bg-black transition-all active:scale-95 shadow-xl shadow-gray-200">
                    Register Medicine
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
