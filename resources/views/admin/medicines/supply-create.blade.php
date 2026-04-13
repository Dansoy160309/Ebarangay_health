@extends('layouts.app')

@section('title', 'Add Medicine Supply')

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

    <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-2">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-1">New Supply Entry</h1>
            <p class="text-sm text-gray-500 font-medium max-w-2xl">Add a new record for medicine replenishment and stock updates.</p>
        </div>
        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Full-width workspace</div>
    </div>

    <div class="bg-white rounded-[2.25rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-5 md:p-6 overflow-hidden relative">
        {{-- Decorative background --}}
        <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-brand-50/50 -z-0"></div>

        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-green-100 bg-green-50/50 p-4 text-sm text-green-800 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50/50 p-5 text-sm text-red-800 animate-in fade-in slide-in-from-top-4 duration-300">
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

        <form method="POST" action="{{ route('admin.medicines.supplies.store') }}" class="medicine-form relative z-10">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                <div class="xl:col-span-2 space-y-6">
                    <div class="space-y-5 rounded-[2rem] border border-gray-100 bg-white p-5 md:p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                <i class="bi bi-capsule-pill text-lg"></i>
                            </div>
                            <h2 class="text-lg font-black text-gray-900">Supply Information</h2>
                        </div>

                        @php
                            $selectedMedicine = $medicines->firstWhere('id', old('medicine_id'));
                            $selectedMedicineName = $selectedMedicine ? $selectedMedicine->generic_name . ($selectedMedicine->brand_name ? ' ('.$selectedMedicine->brand_name.')' : '') : '';
                            $selectedMedicineDosage = $selectedMedicine ? trim(($selectedMedicine->dosage_form ?? '') . ($selectedMedicine->strength ? ' • '.$selectedMedicine->strength : '')) : '';
                        @endphp

                        <div class="space-y-2.5 md:col-span-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Medicine</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                    <i class="bi bi-search"></i>
                                </div>
                                <input type="text" id="medicine_search_input" list="medicine_list" value="{{ old('medicine_id') ? $selectedMedicineName : '' }}"
                                       placeholder="Search for a medicine..."
                                       class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                                <datalist id="medicine_list">
                                    @foreach($medicines as $medicine)
                                        <option value="{{ $medicine->generic_name }}{{ $medicine->brand_name ? ' ('.$medicine->brand_name.')' : '' }}" data-id="{{ $medicine->id }}" data-dosage-form="{{ $medicine->dosage_form }}" data-strength="{{ $medicine->strength }}"></option>
                                    @endforeach
                                </datalist>
                                <input type="hidden" name="medicine_id" id="medicine_id_hidden" value="{{ old('medicine_id') }}">
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400 mb-1">Dosage</div>
                                <div id="selected_medicine_dosage" class="text-sm font-black text-gray-900">
                                    {{ $selectedMedicineDosage ?: 'Select a medicine to view dosage' }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Batch Number</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                        <i class="bi bi-hash"></i>
                                    </div>
                                    <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                                           placeholder="e.g. BATCH-2024-001"
                                           class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm placeholder:text-gray-400">
                                </div>
                            </div>

                            <div class="space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Quantity</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                                           class="w-full pl-12 pr-16 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-black transition-all shadow-sm">
                                    <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-[10px] font-black text-gray-400 uppercase tracking-widest">Units</div>
                                </div>
                            </div>

                            <div class="space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Expiration Date</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <input type="date" name="expiration_date" value="{{ old('expiration_date') }}"
                                           class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                                </div>
                            </div>

                            <div class="space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Supplier Name</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}"
                                           placeholder="Enter supplier name..."
                                           class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm placeholder:text-gray-400">
                                </div>
                            </div>

                            <div class="space-y-2.5 md:col-span-2 max-w-md">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Date Received</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <input type="date" name="date_received" value="{{ old('date_received', now()->toDateString()) }}"
                                           class="w-full pl-12 pr-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 xl:sticky xl:top-24">
                    <div class="rounded-[1.75rem] border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-9 w-9 rounded-xl bg-white text-brand-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-info-circle text-lg"></i>
                            </div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900">Quick Notes</h3>
                        </div>
                        <div class="space-y-3 text-sm text-gray-600 font-medium leading-6">
                            <p>Use the exact batch number from the delivery slip so the stock log stays easy to trace.</p>
                            <p>Keep the expiration date tied to the batch. This helps disposal and alert logic stay accurate.</p>
                            <p>After saving, check the supply history to confirm the entry and review the active batch status.</p>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] border border-gray-100 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-9 w-9 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-check2-square text-lg"></i>
                            </div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900">Before Saving</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-600 font-medium">
                            <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500 shrink-0"></span><span>Select the correct medicine from the list.</span></li>
                            <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500 shrink-0"></span><span>Verify batch number and quantity.</span></li>
                            <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500 shrink-0"></span><span>Double-check the expiration date before submitting.</span></li>
                        </ul>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-end pt-1">
                        <a href="{{ route('admin.medicines.supplies') }}" class="px-5 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                            Cancel
                        </a>
                        <button type="submit" class="px-7 py-3 rounded-xl bg-gray-900 text-white text-xs font-black hover:bg-black transition-all active:scale-95 shadow-md shadow-gray-200">
                            Save Supply Entry
                        </button>
                    </div>
                </div>
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
            id: opt.dataset.id,
            dosageForm: opt.dataset.dosageForm || '',
            strength: opt.dataset.strength || ''
        }));
        const dosageDisplay = document.getElementById('selected_medicine_dosage');

        const form = document.querySelector('form');

        const setMedicineId = () => {
            const entered = medicineInput.value.trim().toLowerCase();
            const match = medicineOptions.find(item => item.label.toLowerCase() === entered);
            hiddenMedicineId.value = match ? match.id : '';
            if (dosageDisplay) {
                if (match) {
                    const dosageText = [match.dosageForm, match.strength].filter(Boolean).join(' • ');
                    dosageDisplay.textContent = dosageText || 'Dosage not set';
                } else {
                    dosageDisplay.textContent = 'Select a medicine to view dosage';
                }
            }
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

