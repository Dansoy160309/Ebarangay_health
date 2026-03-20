@extends('layouts.app')

@section('title', 'Dispense Medicine')

@section('content')
@php
    $patientsData = $patients->map(function($p) {
        return ['id' => $p->id, 'label' => $p->full_name];
    })->values();
    $medicinesData = $medicines->map(function($m) {
        $label = trim($m->generic_name . ($m->brand_name ? ' (' . $m->brand_name . ')' : ''));
        return [
            'id' => $m->id,
            'stock' => $m->stock,
            'label' => $label,
            'display' => $label . ' • Stock: ' . $m->stock,
        ];
    })->values();
@endphp
<div class="flex flex-col gap-6 sm:gap-8">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-capsule text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Pharmacy Management</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Dispense <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Medicine</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Record medicine distribution for patients. Use quick search to find patients and available medicine stocks.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <div class="bg-gray-50/80 backdrop-blur-sm px-6 py-4 rounded-2xl border border-gray-100 shadow-inner flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-brand-600 shadow-sm border border-brand-50">
                        <i class="bi bi-shield-check text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Inventory Status</p>
                        <p class="text-sm font-black text-gray-900 tracking-tight">Active & Verified</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="data-container" 
         data-patients="{{ json_encode($patientsData) }}" 
         data-medicines="{{ json_encode($medicinesData) }}" 
         class="hidden"></div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 lg:p-12 relative overflow-hidden">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-8 rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 flex items-center gap-3 animate-fade-in-down">
                <i class="bi bi-check-circle-fill text-lg"></i>
                <div class="font-bold">{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 rounded-3xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-800 flex items-start gap-3 animate-fade-in-down">
                <i class="bi bi-exclamation-triangle-fill text-lg mt-0.5"></i>
                <div>
                    <p class="font-black uppercase tracking-widest text-[10px] mb-2">Correction Required</p>
                    <ul class="list-disc list-inside space-y-1 font-bold">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('midwife.medicines.distribute.store') }}" class="space-y-10" id="dispense-form">
            @csrf

            {{-- Section 1: Core Selection --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Recipient Patient</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-person-bounding-box text-xl"></i>
                        </div>
                        <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                        <input id="patient_search" list="patient_options"
                               class="block w-full pl-16 pr-8 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 placeholder-gray-400 transition-all @error('patient_id') ring-2 ring-red-500 @enderror"
                               placeholder="Type to search patient..."
                               autocomplete="off">
                        <datalist id="patient_options"></datalist>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <i class="bi bi-info-circle text-brand-400"></i>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Type patient name to see suggestions</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Medicine to Dispense</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-capsule-pill text-xl"></i>
                        </div>
                        <input type="hidden" name="medicine_id" id="medicine_id" value="{{ old('medicine_id') }}">
                        <input id="medicine_search" list="medicine_options"
                               class="block w-full pl-16 pr-8 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 placeholder-gray-400 transition-all @error('medicine_id') ring-2 ring-red-500 @enderror"
                               placeholder="Search medicine stock..."
                               autocomplete="off">
                        <datalist id="medicine_options"></datalist>
                    </div>
                    <div class="flex items-center justify-between px-4">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-info-circle text-brand-400"></i>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Displays available inventory only</p>
                        </div>
                        <div class="hidden" id="medicine_meta">
                            <span id="stockBadge" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100 animate-pulse"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Dosage & Schedule --}}
            <div class="bg-gray-50/50 rounded-[3rem] p-8 lg:p-10 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Quantity</label>
                        <div class="relative">
                            <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                                   class="block w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-brand-50 text-base font-bold text-gray-900 shadow-sm transition-all" placeholder="1">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2">
                                <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Max: <span id="qtyMax">—</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Dosage</label>
                        <input type="text" name="dosage" value="{{ old('dosage') }}"
                               class="block w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-brand-50 text-base font-bold text-gray-900 shadow-sm transition-all" placeholder="e.g. 500mg">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Frequency</label>
                        <input type="text" id="frequency" name="frequency" value="{{ old('frequency') }}"
                               class="block w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-brand-50 text-base font-bold text-gray-900 shadow-sm transition-all mb-3" placeholder="e.g. 3x a day">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" data-fill="1x a day" class="preset px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-gray-500 hover:bg-brand-600 hover:text-white border border-gray-100 shadow-sm transition-all">1x/day</button>
                            <button type="button" data-fill="2x a day" class="preset px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-gray-500 hover:bg-brand-600 hover:text-white border border-gray-100 shadow-sm transition-all">2x/day</button>
                            <button type="button" data-fill="3x a day" class="preset px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-gray-500 hover:bg-brand-600 hover:text-white border border-gray-100 shadow-sm transition-all">3x/day</button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Duration</label>
                        <input type="text" id="duration" name="duration" value="{{ old('duration') }}"
                               class="block w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-brand-50 text-base font-bold text-gray-900 shadow-sm transition-all mb-3" placeholder="e.g. 7 days">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" data-fill="3 days" class="preset px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-gray-500 hover:bg-brand-600 hover:text-white border border-gray-100 shadow-sm transition-all">3d</button>
                            <button type="button" data-fill="5 days" class="preset px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-gray-500 hover:bg-brand-600 hover:text-white border border-gray-100 shadow-sm transition-all">5d</button>
                            <button type="button" data-fill="7 days" class="preset px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-gray-500 hover:bg-brand-600 hover:text-white border border-gray-100 shadow-sm transition-all">7d</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Notes --}}
            <div class="space-y-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Additional Instructions (Optional)</label>
                <textarea name="notes" rows="4"
                          class="block w-full px-8 py-6 bg-gray-50 border-none rounded-[2.5rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all"
                          placeholder="Record any specific instructions or remarks here...">{{ old('notes') }}</textarea>
            </div>

            {{-- Form Actions --}}
            <div class="pt-10 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3 bg-blue-50/50 px-5 py-3 rounded-2xl border border-blue-100/50">
                    <i class="bi bi-info-circle-fill text-blue-500"></i>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Inventory will be automatically updated upon submission</p>
                </div>
                
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <a href="{{ route('dashboard') }}" 
                       class="flex-1 sm:flex-none px-10 py-5 bg-white text-gray-500 rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] border border-gray-200 hover:bg-gray-50 transition-all text-center">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex-1 sm:flex-none px-10 py-5 bg-brand-600 text-white rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all active:scale-95 flex items-center justify-center gap-3">
                        Dispense Medicine <i class="bi bi-check-circle-fill"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('data-container');
        const patients = JSON.parse(container.dataset.patients || '[]');
        const medicines = JSON.parse(container.dataset.medicines || '[]');

        function initCombo({inputId, hiddenId, datalistId, items, initialId}) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);
            const list = document.getElementById(datalistId);

            function renderOptions(prefix = '') {
                const q = (prefix || '').toString().trim().toLowerCase();
                const filtered = q.length
                    ? items.filter(it => it.label.toLowerCase().startsWith(q))
                    : items;
                list.innerHTML = filtered
                    .slice(0, 100) // cap suggestions for performance
                    .map(it => `<option value="${(it.display ?? it.label).replace(/"/g, '&quot;')}" data-id="${it.id}" data-stock="${it.stock ?? ''}"></option>`)
                    .join('');
            }

            function setFromLabel(label) {
                const opt = list.querySelector(`option[value="${CSS.escape(label)}"]`);
                if (opt && opt.dataset.id) {
                    hidden.value = opt.dataset.id;
                    if (opt.dataset.stock !== undefined) {
                        const stock = Number(opt.dataset.stock || 0);
                        const stockBadge = document.getElementById('stockBadge');
                        const meta = document.getElementById('medicine_meta');
                        const qty = document.getElementById('quantity');
                        const qtyMax = document.getElementById('qtyMax');
                        if (meta && stockBadge && qty && qtyMax) {
                            meta.classList.remove('hidden');
                            stockBadge.textContent = `In Stock: ${stock}`;
                            qty.max = stock > 0 ? stock : 1;
                            qtyMax.textContent = stock > 0 ? stock : '—';
                        }
                    }
                } else {
                    hidden.value = '';
                    const meta = document.getElementById('medicine_meta');
                    const qty = document.getElementById('quantity');
                    const qtyMax = document.getElementById('qtyMax');
                    if (meta) meta.classList.add('hidden');
                    if (qty) qty.removeAttribute('max');
                    if (qtyMax) qtyMax.textContent = '—';
                }
            }

            // Initialize with all items and prefill from old value
            renderOptions('');
            if (initialId) {
                const found = items.find(it => String(it.id) === String(initialId));
                if (found) {
                    input.value = found.display ?? found.label;
                    // seed stock badge/max if available
                    const qty = document.getElementById('quantity');
                    const qtyMax = document.getElementById('qtyMax');
                    const stockBadge = document.getElementById('stockBadge');
                    const meta = document.getElementById('medicine_meta');
                    if (found.stock !== undefined && qty && qtyMax && stockBadge && meta) {
                        meta.classList.remove('hidden');
                        stockBadge.textContent = `In Stock: ${found.stock}`;
                        qty.max = found.stock > 0 ? found.stock : 1;
                        qtyMax.textContent = found.stock > 0 ? found.stock : '—';
                    }
                }
            }

            input.addEventListener('input', () => {
                renderOptions(input.value);
                // If the user typed an exact option, set hidden id
                setFromLabel(input.value);
            });
            input.addEventListener('change', () => setFromLabel(input.value));
            input.addEventListener('blur', () => setFromLabel(input.value));
        }

        initCombo({
            inputId: 'patient_search',
            hiddenId: 'patient_id',
            datalistId: 'patient_options',
            items: patients,
            initialId: document.getElementById('patient_id').value
        });

        initCombo({
            inputId: 'medicine_search',
            hiddenId: 'medicine_id',
            datalistId: 'medicine_options',
            items: medicines,
            initialId: document.getElementById('medicine_id').value
        });

        // Guard submit: ensure hidden IDs are set
        document.getElementById('dispense-form').addEventListener('submit', function (e) {
            const pid = document.getElementById('patient_id').value;
            const mid = document.getElementById('medicine_id').value;
            const qty = document.getElementById('quantity');
            const max = Number(qty.getAttribute('max') || 0);
            if (!pid || !mid) {
                e.preventDefault();
                alert('Please select a valid Patient and Medicine from the suggestions list.');
                return;
            }
            if (max && Number(qty.value) > max) {
                e.preventDefault();
                alert(`Quantity exceeds available stock (${max}). Please adjust.`);
                qty.focus();
                return;
            }
        });

        // Presets
        document.querySelectorAll('button.preset').forEach(btn => {
            btn.addEventListener('click', () => {
                const fill = btn.getAttribute('data-fill');
                if (fill.includes('day') && !fill.includes('x')) {
                    document.getElementById('duration').value = fill;
                } else {
                    document.getElementById('frequency').value = fill;
                }
            });
        });
    });
</script>
@endsection
