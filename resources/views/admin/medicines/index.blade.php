@extends('layouts.app')

@section('title', 'Medicines')

@section('content')
@section('content')
<div class="flex flex-col gap-6 sm:gap-8">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-capsule-pill text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Inventory Control</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Medicine <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Inventory</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Manage medicine stocks, monitor expiration dates, and track distribution logs.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.medicines.create') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-brand-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:scale-105 transition-all transform group">
                    <i class="bi bi-plus-lg mr-2 group-hover:rotate-90 transition-transform"></i>
                    Add Medicine
                </a>
            </div>
        </div>
    </div>

    {{-- Search & Action Bar --}}
    <div class="bg-white rounded-[2rem] shadow-lg shadow-gray-200/50 border border-gray-100 p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            {{-- Quick Links --}}
            <div class="flex items-center p-1 bg-gray-50 rounded-xl border border-gray-100 overflow-x-auto no-scrollbar">
                <a href="{{ route('admin.medicines.distributions') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-600 transition-all whitespace-nowrap">
                    Distributions
                </a>
                <a href="{{ route('admin.medicines.supplies') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-600 transition-all whitespace-nowrap">
                    Supplies
                </a>
                <a href="{{ route('admin.medicines.reports') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-600 transition-all whitespace-nowrap">
                    Reports
                </a>
            </div>

            {{-- Search Input --}}
            <form method="GET" class="relative w-full lg:max-w-md group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, brand, or form..."
                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all text-xs font-bold text-gray-700 placeholder-gray-400 shadow-inner">
            </form>
        </div>
    </div>

    {{-- Inventory Table --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Medicine Details</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Form / Strength</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Stock Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Expiration</th>
                        <th class="px-8 py-5 text-right text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($medicines as $medicine)
                        @php
                            $isLow = in_array($medicine->id, $lowStockIds, true);
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
                                            <span class="text-sm font-bold {{ $isExpiring ? 'text-orange-600' : 'text-gray-700' }}">
                                                {{ $medicine->expiration_date->format('M d, Y') }}
                                            </span>
                                        </div>
                                        @if($isExpiring)
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
                                <a href="{{ route('admin.medicines.edit', $medicine) }}"
                                   class="inline-flex items-center px-5 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:border-gray-300 hover:text-brand-600 transition-all active:scale-95 shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Edit
                                </a>
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
