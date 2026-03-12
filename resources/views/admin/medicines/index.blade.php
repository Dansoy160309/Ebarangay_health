@extends('layouts.app')

@section('title', 'Medicines')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium text-gray-500">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="bi bi-chevron-right text-gray-400 mx-2 text-[10px]"></i>
                            <span class="text-gray-900">Inventory</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                <span class="p-3 bg-brand-50 rounded-2xl text-brand-600 shadow-sm">
                    <i class="bi bi-capsule-pill"></i>
                </span>
                Medicines
            </h1>
            <p class="mt-2 text-lg text-gray-500 font-medium">Manage medicine inventory and monitor stock levels.</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.medicines.distributions') }}" 
               class="inline-flex items-center px-5 py-2.5 rounded-2xl border border-gray-200 text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
                <i class="bi bi-journal-text me-2 text-brand-500"></i>
                Distribution Logs
            </a>
            <a href="{{ route('admin.medicines.supplies') }}" 
               class="inline-flex items-center px-5 py-2.5 rounded-2xl border border-gray-200 text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
                <i class="bi bi-truck me-2 text-brand-500"></i>
                Supply History
            </a>
            <a href="{{ route('admin.medicines.reports') }}" 
               class="inline-flex items-center px-5 py-2.5 rounded-2xl border border-gray-200 text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
                <i class="bi bi-pie-chart me-2 text-brand-500"></i>
                Reports
            </a>
            <a href="{{ route('admin.medicines.create') }}" 
               class="inline-flex items-center px-6 py-2.5 rounded-2xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-all active:scale-95 shadow-lg shadow-brand-100">
                <i class="bi bi-plus-lg me-2"></i>
                Add Medicine
            </a>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 mb-8">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-grow group">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" autocomplete="off"
                       class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-sm font-medium"
                       placeholder="Search medicines by name, brand, or dosage form...">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.medicines.index') }}" 
                       class="px-6 py-3.5 rounded-2xl bg-gray-100 text-gray-600 text-sm font-bold hover:bg-gray-200 transition-all flex items-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
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
