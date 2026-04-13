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
<div class="w-full max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @php
        $today = now()->startOfDay();
        $allBatches = $medicine->supplies ?? collect();
        $activeBatches = $allBatches->whereNull('disposed_at');
        $expiredActiveBatches = $activeBatches->filter(fn($b) => $b->expiration_date && $b->expiration_date->copy()->startOfDay()->lt($today));
        $expiringSoonBatches = $activeBatches->filter(fn($b) => $b->expiration_date && $b->expiration_date->copy()->startOfDay()->gte($today) && $b->expiration_date->copy()->startOfDay()->lte($today->copy()->addDays(30)));
        $disposedBatches = $allBatches->whereNotNull('disposed_at');
    @endphp

    <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-2">
        <div>
            <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-brand-600 transition-colors group mb-4">
                <i class="bi bi-arrow-left me-2 group-hover:-translate-x-1 transition-transform"></i>
                Back to Inventory
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-1">Edit Medicine</h1>
            <p class="text-sm text-gray-500 font-medium max-w-2xl">Update core medicine details while keeping batch and expiry flow managed through supplies.</p>
        </div>
        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
            Full-width workspace
        </div>
    </div>

    <form method="POST" action="{{ route('admin.medicines.update', $medicine) }}" class="medicine-form">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
            <div class="xl:col-span-2 space-y-6">
                <div class="space-y-5 rounded-[2rem] border border-gray-100 bg-white p-5 md:p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                            <i class="bi bi-info-circle text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-gray-900">Medicine Information</h2>
                            <p class="text-xs text-gray-500 font-medium">Update details for {{ $medicine->generic_name }}{{ $medicine->brand_name ? ' (' . $medicine->brand_name . ')' : '' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2.5 md:col-span-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Generic Name <span class="text-red-500">*</span></label>
                            <input type="text" name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm"
                                   required placeholder="e.g. Paracetamol">
                            <p class="text-xs text-gray-500 ml-1">For combination drugs, separate ingredients with <span class="font-semibold">+</span></p>
                            @error('generic_name')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1 ml-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-2.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Brand Name</label>
                            <input type="text" name="brand_name" value="{{ old('brand_name', $medicine->brand_name) }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm"
                                   placeholder="e.g. Biogesic">
                            @error('brand_name')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1 ml-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-2.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Dosage Form <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="dosage_form"
                                        class="w-full pl-5 pr-10 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm appearance-none"
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
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1 ml-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-2.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Strength <span class="text-red-500">*</span></label>
                            <input type="text" name="strength" value="{{ old('strength', $medicine->strength) }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm"
                                   required placeholder="e.g. 500mg, 120mg/5ml">
                            @error('strength')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1 ml-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-2.5">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Reorder Level <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="reorder_level" min="0" value="{{ old('reorder_level', $medicine->reorder_level) }}"
                                       class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Alert at
                                </div>
                            </div>
                            @error('reorder_level')
                                <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1 ml-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-5 xl:sticky xl:top-6">
                <div class="rounded-[2rem] bg-gradient-to-br from-blue-600 to-blue-500 text-white p-6 shadow-lg shadow-blue-500/20">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 rounded-2xl bg-white/15 flex items-center justify-center">
                            <i class="bi bi-clipboard-data text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/70">Inventory</p>
                            <h3 class="text-lg font-black">Stock Snapshot</h3>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-black leading-none">{{ $medicine->stock }}</span>
                        <span class="mb-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $medicine->stock <= $medicine->reorder_level ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $medicine->stock <= $medicine->reorder_level ? 'Low Stock' : 'Good' }}
                        </span>
                    </div>
                    <p class="text-xs text-white/85 mt-3">Last updated {{ $medicine->updated_at->diffForHumans() }}</p>
                </div>

                <div class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-black text-gray-900 mb-3">Batch Guidance</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Expiration is managed in supply batches. Add or edit batches in Supplies for accurate expiry tracking and disposal workflow.</p>
                </div>

                <div class="rounded-[2rem] border border-gray-100 bg-gray-50 p-6 shadow-sm">
                    <h3 class="text-base font-black text-gray-900 mb-3">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.medicines.supplies', ['medicine_id' => $medicine->id]) }}"
                           class="block w-full px-4 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 text-sm font-black hover:bg-gray-100 transition">
                            Open Supply History
                        </a>
                        <a href="{{ route('admin.medicines.supplies.create') }}"
                           class="block w-full px-4 py-3 rounded-xl bg-brand-600 text-white text-sm font-black hover:bg-brand-700 transition">
                            + Add Supply Batch
                        </a>
                    </div>
                </div>
            </aside>
        </div>

        <div class="flex items-center justify-end gap-3 pt-8 mt-8 border-t border-gray-100">
            <a href="{{ route('admin.medicines.index') }}"
               class="px-8 py-4 rounded-2xl text-sm font-black text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                Discard Changes
            </a>
            <button type="submit"
                    class="px-12 py-4 rounded-2xl bg-gray-900 text-white text-sm font-black hover:bg-black transition-all active:scale-95 shadow-xl shadow-gray-200">
                Update Medicine
            </button>
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
