@extends('layouts.app')

@section('title', 'Medicines')

@section('content')
@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-3">
                    <i class="bi bi-capsule-pill text-[8px]"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">Inventory Control</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight leading-tight mb-1 sm:mb-2">
                    Medicine <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Inventory</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Manage medicine stocks, monitor expiration dates, and track distribution logs.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <a href="{{ route('admin.medicines.create') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-brand-600 text-white font-black text-[9px] sm:text-xs uppercase tracking-tighter shadow-lg shadow-brand-500/15 hover:bg-brand-700 hover:scale-105 transition-all transform group">
                    <i class="bi bi-plus-lg mr-1.5 group-hover:rotate-90 transition-transform text-xs"></i>
                    Add Medicine
                </a>
            </div>
        </div>
    </div>

    {{-- Search & Action Bar --}}
    <div class="bg-white rounded-2xl shadow-md shadow-gray-200/25 border border-gray-100 p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Quick Links --}}
            <div class="flex items-center p-2 bg-gray-50 rounded-xl border border-gray-100 overflow-x-auto no-scrollbar">
                <a href="{{ route('admin.medicines.distributions') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wide text-gray-500 hover:text-brand-600 hover:bg-white transition-all whitespace-nowrap">
                    Distributions
                </a>
                <a href="{{ route('admin.medicines.supplies') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wide text-gray-500 hover:text-brand-600 hover:bg-white transition-all whitespace-nowrap">
                    Supplies
                </a>
                <a href="{{ route('admin.medicines.reports') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wide text-gray-500 hover:text-brand-600 hover:bg-white transition-all whitespace-nowrap">
                    Reports
                </a>
            </div>

            {{-- Search Input --}}
            <form method="GET" class="relative w-full lg:max-w-md group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                    <i class="bi bi-search text-[10px]"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, brand, or form..."
                    class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border-none rounded-lg focus:ring-3 focus:ring-brand-500/10 focus:bg-white transition-all text-[10px] font-bold text-gray-700 placeholder-gray-400 shadow-inner">
            </form>
        </div>
    </div>

    @php
        $lowStockCount = count($lowStockIds ?? []);
        $expiringSoonCount = count($expiringSoonIds ?? []) + count($expiringTodayIds ?? []);
        $totalAlertCount = $lowStockCount + $expiringSoonCount;
    @endphp

    @if($totalAlertCount > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($lowStockCount > 0)
        <div class="bg-red-50 border border-red-100 rounded-xl p-4 sm:p-5 flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center text-red-600 shrink-0 text-lg">
                <i class="bi bi-graph-down-arrow"></i>
            </div>
            <div>
                <h4 class="text-xs font-black text-red-900 uppercase tracking-widest">Low Stock Alert</h4>
                <p class="text-xs font-bold text-red-700 mt-1">{{ $lowStockCount }} medicines are below reorder level.</p>
                <p class="text-[10px] font-black text-red-600 uppercase tracking-wider mt-1">This means replenish stock soon.</p>
            </div>
        </div>

        @endif

        @if($expiringSoonCount > 0)
        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 sm:p-5 flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 shrink-0 text-lg">
                <i class="bi bi-calendar-x"></i>
            </div>
            <div>
                <h4 class="text-xs font-black text-orange-900 uppercase tracking-widest">Expiring Soon Alert</h4>
                <p class="text-xs font-bold text-orange-700 mt-1">{{ $expiringSoonCount }} medicines are near expiry.</p>
                <p class="text-[10px] font-black text-orange-600 uppercase tracking-wider mt-1">This means prioritize use or replace stocks.</p>

                @if(isset($expiringSupplyBatches) && $expiringSupplyBatches->count() > 0)
                    <div class="mt-3 space-y-1.5">
                        <p class="text-[10px] font-black text-orange-800 uppercase tracking-wider">Batches At Risk</p>
                        @foreach($expiringSupplyBatches as $batch)
                            @php
                                $batchExpiresToday = $batch->expiration_date && $batch->expiration_date->isSameDay(today());
                            @endphp
                            <div class="flex items-center justify-between gap-3 text-[11px] font-bold text-orange-800 bg-orange-100/60 border border-orange-200 rounded-lg px-2.5 py-1.5">
                                <span>
                                    {{ $batch->batch_number ?: 'NO-BATCH' }}
                                    •
                                    {{ $batch->medicine?->generic_name ?? 'Unknown medicine' }}
                                </span>
                                <span class="whitespace-nowrap {{ $batchExpiresToday ? 'text-amber-700' : 'text-orange-700' }}">
                                    {{ $batch->expiration_date?->format('M d, Y') }}
                                    ({{ $batchExpiresToday ? 'Today' : 'Soon' }})
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Inventory Table --}}
    <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/30 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Medicine Details</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Form / Strength</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Stock Status</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Expiration</th>
                        <th class="px-5 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-tighter">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($medicines as $medicine)
                        @php
                            $isLow = in_array($medicine->id, $lowStockIds, true);
                            $isExpired = in_array($medicine->id, $expiredIds ?? [], true);
                            $isExpiringToday = in_array($medicine->id, $expiringTodayIds ?? [], true);
                            $isExpiring = in_array($medicine->id, $expiringSoonIds, true);
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 font-bold">
                                        {{ substr($medicine->generic_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-base group-hover:text-brand-600 transition-colors uppercase tracking-tight">{{ $medicine->generic_name }}</div>
                                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $medicine->brand_name ?? 'Generic' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-black uppercase tracking-wider w-fit">
                                        {{ $medicine->dosage_form }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-500">{{ $medicine->strength }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-black text-gray-900">{{ $medicine->stock }}</span>
                                        @if($isLow)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">
                                                Stable
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Reorder at {{ $medicine->reorder_level }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($medicine->expiration_date)
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-calendar3 text-gray-400 text-xs"></i>
                                            <span class="text-sm font-bold {{ $isExpired ? 'text-red-600' : ($isExpiringToday ? 'text-amber-600' : ($isExpiring ? 'text-orange-600' : 'text-gray-700')) }}">
                                                {{ $medicine->expiration_date->format('M d, Y') }}
                                            </span>
                                        </div>
                                        @if($isExpired)
                                            <span class="text-[10px] font-black uppercase tracking-widest text-red-500">
                                                Expired
                                            </span>
                                        @elseif($isExpiringToday)
                                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-500">
                                                Expires Today
                                            </span>
                                        @elseif($isExpiring)
                                            <span class="text-[10px] font-black uppercase tracking-widest text-orange-500">
                                                Expiring Soon
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-gray-300 uppercase tracking-widest">No Date Set</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="inline-flex items-center gap-2 whitespace-nowrap">
                                    <a href="{{ route('admin.medicines.edit', $medicine) }}"
                                       class="inline-flex items-center px-5 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:border-gray-300 hover:text-brand-600 transition-all active:scale-95 shadow-sm">
                                        <i class="bi bi-pencil-square me-2"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.medicines.destroy', $medicine) }}" method="POST" onsubmit="return confirm('Delete this medicine? This action cannot be undone.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-5 py-2 rounded-xl bg-white border border-red-200 text-xs font-bold text-red-600 hover:bg-red-50 hover:border-red-300 transition-all active:scale-95 shadow-sm">
                                            <i class="bi bi-trash me-2"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-300 mb-4">
                                        <i class="bi bi-search text-4xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">No medicines found</h3>
                                    <p class="text-gray-500 max-w-xs mx-auto">Try adjusting your search or add a new medicine to the inventory.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($medicines->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
                {{ $medicines->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
