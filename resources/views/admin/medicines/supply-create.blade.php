@extends('layouts.app')

@section('title', 'Add Medicine Supply')

@section('content')
<div class="max-w-3xl mx-auto px-3 sm:px-5 lg:px-6 py-6">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-4" aria-label="Breadcrumb">
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
                    <a href="{{ route('admin.medicines.supplies') }}" class="hover:text-brand-600 transition-colors">Supply History</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400 mx-2 text-[10px]"></i>
                    <span class="text-gray-900 font-bold">Add Supply</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-10">
        <h1 class="text-5xl font-black text-gray-900 tracking-tight mb-2">New Supply Entry</h1>
        <p class="text-gray-600 font-semibold text-lg">Add a new record for medicine replenishment and stock updates.</p>
    </div>

    <div class="bg-white rounded-xl shadow-md shadow-gray-200/30 border border-gray-100 p-6 md:p-8 overflow-hidden relative">
        {{-- Decorative background --}}
        <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-brand-50/40 -z-0"></div>

        @if(session('success'))
            <div class="mb-8 rounded-3xl border border-green-100 bg-green-50/50 p-4 text-sm text-green-800 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 rounded-3xl border border-red-100 bg-red-50/50 p-6 text-sm text-red-800 animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="font-black uppercase tracking-widest text-[11px]">Please correct the following:</div>
                </div>
                <ul class="list-disc list-inside space-y-1 font-medium ml-11">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.medicines.supplies.store') }}" class="space-y-7 relative z-10">
            @csrf

            {{-- Medicine Selection Section --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-8 w-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-sm">
                        <i class="bi bi-capsule-pill"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-gray-900 uppercase tracking-wide">Select Medicine</h3>
                </div>
                
                @php
                    $selectedMedicine = $medicines->firstWhere('id', old('medicine_id'));
                    $selectedMedicineName = $selectedMedicine ? $selectedMedicine->generic_name . ($selectedMedicine->brand_name ? ' ('.$selectedMedicine->brand_name.')' : '') : '';
                @endphp
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" id="medicine_search_input" list="medicine_list" value="{{ old('medicine_id') ? $selectedMedicineName : '' }}"
                           placeholder="Search for a medicine..."
                           class="w-full pl-12 pr-5 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-base font-bold transition-all shadow-sm">
                    <datalist id="medicine_list">
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->generic_name }}{{ $medicine->brand_name ? ' ('.$medicine->brand_name.')' : '' }}" data-id="{{ $medicine->id }}"></option>
                        @endforeach
                    </datalist>
                    <input type="hidden" name="medicine_id" id="medicine_id_hidden" value="{{ old('medicine_id') }}">
                </div>
            </div>

            {{-- Batch & Quantity Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-7 w-7 rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center text-xs">
                            <i class="bi bi-hash"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Batch Number</h3>
                    </div>
                    <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                           placeholder="e.g. BATCH-2024-001"
                           class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-base font-bold transition-all shadow-sm placeholder:text-gray-400">
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-7 w-7 rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center text-xs">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Quantity</h3>
                    </div>
                    <div class="relative group">
                        <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                               class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-base font-black transition-all shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Units
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expiration & Supplier Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-7 w-7 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-xs">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Expiration Date</h3>
                    </div>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date') }}"
                           class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-base font-bold transition-all shadow-sm">
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-7 w-7 rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center text-xs">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Supplier Name</h3>
                    </div>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}"
                           placeholder="Enter supplier name..."
                           class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-base font-bold transition-all shadow-sm placeholder:text-gray-400">
                </div>
            </div>

            {{-- Date Received Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-7 w-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Date Received</h3>
                    </div>
                    <input type="date" name="date_received" value="{{ old('date_received', now()->toDateString()) }}"
                           class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-base font-bold transition-all shadow-sm">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5">
                <a href="{{ route('admin.medicines.supplies') }}" class="px-6 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-gray-900 text-white text-xs font-black hover:bg-black transition-all active:scale-95 shadow-md shadow-gray-200">
                    Save Supply Entry
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const medicineInput = document.getElementById('medicine_search_input');
        const hiddenMedicineId = document.getElementById('medicine_id_hidden');
        const medicineOptions = Array.from(document.querySelectorAll('#medicine_list option')).map(opt => ({
            label: opt.value.trim(),
            id: opt.dataset.id
        }));

        const form = document.querySelector('form');

        const setMedicineId = () => {
            const entered = medicineInput.value.trim().toLowerCase();
            const match = medicineOptions.find(item => item.label.toLowerCase() === entered);
            hiddenMedicineId.value = match ? match.id : '';
        };

        medicineInput.addEventListener('input', setMedicineId);

        form.addEventListener('submit', (event) => {
            setMedicineId();
            if (!hiddenMedicineId.value) {
                event.preventDefault();
                alert('Please select a valid medicine from the list before submitting.');
                medicineInput.focus();
            }
        });
    });
</script>

@endsection

