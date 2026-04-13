@extends('layouts.app')

@section('title', 'Edit Medicine')

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
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @php
        $today = now()->startOfDay();
        $allBatches = $medicine->supplies ?? collect();
        $activeBatches = $allBatches->whereNull('disposed_at');
        $expiredActiveBatches = $activeBatches->filter(fn($b) => $b->expiration_date && $b->expiration_date->copy()->startOfDay()->lt($today));
        $expiringSoonBatches = $activeBatches->filter(fn($b) => $b->expiration_date && $b->expiration_date->copy()->startOfDay()->gte($today) && $b->expiration_date->copy()->startOfDay()->lte($today->copy()->addDays(30)));
        $disposedBatches = $allBatches->whereNotNull('disposed_at');
    @endphp

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

    <form method="POST" action="{{ route('admin.medicines.update', $medicine) }}" class="medicine-form">
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
                            <p class="text-xs text-gray-500 mt-1">For combination drugs, separate ingredients with <span class="font-semibold">+</span></p>
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

                        {{-- Expiration Date (Batch Managed) --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="bi bi-calendar-x text-blue-500"></i>
                                Expiration Date
                            </label>
                            <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                                <p class="text-sm font-black {{ $medicine->expiration_date ? 'text-blue-800' : 'text-gray-500' }}">
                                    {{ $medicine->expiration_date ? $medicine->expiration_date->format('M d, Y') : 'No active batch' }}
                                </p>
                                <p class="text-[11px] font-bold text-blue-700 mt-1 uppercase tracking-wider">
                                    Managed by supply batches only
                                </p>
                                <p class="text-[11px] text-blue-700/90 mt-2">
                                    To update expiry, edit/add a batch in the Supplies module.
                                </p>
                                <a href="{{ route('admin.medicines.supplies') }}" class="inline-flex items-center mt-3 px-3 py-1.5 rounded-lg bg-white border border-blue-200 text-blue-700 text-[10px] font-black uppercase tracking-widest hover:bg-blue-100 transition">
                                    <i class="bi bi-box-seam mr-1.5"></i>
                                    Open Supplies
                                </a>
                            </div>
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

        <div class="mt-8 bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 md:p-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <i class="bi bi-box-seam text-brand-600"></i>
                        Batch Attention Board
                    </h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Use this to identify which exact batch needs action.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.medicines.supplies', ['medicine_id' => $medicine->id]) }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-black uppercase tracking-wider hover:bg-gray-50 transition">
                        <i class="bi bi-list-ul mr-1.5"></i>
                        Open Supply History
                    </a>
                    <a href="{{ route('admin.medicines.supplies.create') }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-black uppercase tracking-wider hover:bg-brand-700 transition">
                        <i class="bi bi-plus-lg mr-1.5"></i>
                        Add Batch
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-red-600">Expired Active</p>
                    <p class="text-2xl font-black text-red-700">{{ $expiredActiveBatches->count() }}</p>
                </div>
                <div class="rounded-xl border border-orange-200 bg-orange-50 px-4 py-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-orange-600">Expiring 30 Days</p>
                    <p class="text-2xl font-black text-orange-700">{{ $expiringSoonBatches->count() }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Active Batches</p>
                    <p class="text-2xl font-black text-emerald-700">{{ $activeBatches->count() }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Disposed</p>
                    <p class="text-2xl font-black text-gray-700">{{ $disposedBatches->count() }}</p>
                </div>
            </div>

            @if($allBatches->count() > 0)
                <div class="overflow-x-auto rounded-2xl border border-gray-100">
                    <table class="w-full min-w-[760px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Batch</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Received</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Expiry</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($allBatches->take(10) as $batch)
                                @php
                                    $isDisposed = !is_null($batch->disposed_at);
                                    $isExpired = !$isDisposed && $batch->expiration_date && $batch->expiration_date->copy()->startOfDay()->lt($today);
                                    $isExpiringSoon = !$isDisposed && $batch->expiration_date && $batch->expiration_date->copy()->startOfDay()->gte($today) && $batch->expiration_date->copy()->startOfDay()->lte($today->copy()->addDays(30));
                                @endphp
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $batch->batch_number ?: 'NO-BATCH' }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-700">{{ number_format($batch->quantity) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $batch->date_received?->format('M d, Y') ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm font-bold {{ $isExpired ? 'text-red-600' : ($isExpiringSoon ? 'text-orange-600' : 'text-gray-700') }}">{{ $batch->expiration_date?->format('M d, Y') ?: 'No date' }}</td>
                                    <td class="px-4 py-3">
                                        @if($isDisposed)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">Disposed</span>
                                        @elseif($isExpired)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600">Expired</span>
                                        @elseif($isExpiringSoon)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-orange-100 text-orange-600">Expiring Soon</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.medicines.supplies', ['medicine_id' => $medicine->id]) }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($allBatches->count() > 10)
                    <p class="text-[11px] text-gray-500 font-medium mt-3">Showing latest 10 batches. Open Supply History for full list.</p>
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                    <i class="bi bi-box2 text-3xl text-gray-300"></i>
                    <p class="text-sm font-bold text-gray-700 mt-2">No batches yet for this medicine</p>
                    <p class="text-xs text-gray-500 mt-1">Add a supply batch to start batch-level expiry tracking.</p>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
