@extends('layouts.app')

@section('title', 'Medicine Supply History')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-clock-history text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Transaction Logs</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Supply <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">History</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Track and manage medicine replenishment records and supplier deliveries.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.medicines.index') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-white border border-gray-200 text-gray-700 font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Inventory
                </a>
                <a href="{{ route('admin.medicines.supplies.create') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-brand-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:scale-105 transition-all transform group">
                    <i class="bi bi-plus-lg mr-2 group-hover:rotate-90 transition-transform"></i>
                    Add Supply
                </a>
            </div>
        </div>
    </div>

    {{-- Search & Action Bar --}}
    <div class="bg-white rounded-[2rem] shadow-lg shadow-gray-200/50 border border-gray-100 p-5 sm:p-6">
        <form method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            {{-- Quick Links --}}
            <div class="flex items-center p-1 bg-gray-50 rounded-xl border border-gray-100 overflow-x-auto no-scrollbar">
                <a href="{{ route('admin.medicines.index') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-600 transition-all whitespace-nowrap">
                    Inventory
                </a>
                <a href="{{ route('admin.medicines.distributions') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-600 transition-all whitespace-nowrap">
                    Distributions
                </a>
                <a href="{{ route('admin.medicines.reports') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-600 transition-all whitespace-nowrap">
                    Reports
                </a>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative group">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="pl-4 pr-4 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all text-[10px] font-black uppercase tracking-widest text-gray-700 shadow-inner">
                </div>
                <div class="text-gray-300 font-black text-[10px]">TO</div>
                <div class="relative group">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="pl-4 pr-4 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all text-[10px] font-black uppercase tracking-widest text-gray-700 shadow-inner">
                </div>
                <select name="medicine_id" onchange="this.form.submit()"
                        class="pl-4 pr-10 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all text-[10px] font-black uppercase tracking-widest text-gray-700 shadow-inner appearance-none">
                    <option value="">All Medicines</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                            {{ $medicine->generic_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="p-2.5 bg-gray-900 text-white rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                    <i class="bi bi-search text-xs"></i>
                </button>
                @if(request()->hasAny(['date_from', 'date_to', 'medicine_id', 'supplier_name']))
                    <a href="{{ route('admin.medicines.supplies') }}" class="p-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all">
                        <i class="bi bi-x-lg text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- History Table --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Date Received</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Medicine Info</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Batch No.</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Quantity</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Expiration</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Supplier & Receiver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($supplies as $supply)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <div class="text-sm font-black text-gray-900">
                                        {{ $supply->date_received?->format('M d, Y') ?? $supply->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="h-1 w-1 rounded-full bg-brand-500"></span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                            {{ $supply->created_at->format('h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-black shrink-0 border border-brand-100/50">
                                        {{ substr($supply->medicine?->generic_name ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 group-hover:text-brand-600 transition-colors">
                                            {{ $supply->medicine?->generic_name }}
                                        </div>
                                        @if($supply->medicine?->brand_name)
                                            <div class="text-[11px] font-bold text-gray-400 mt-0.5">
                                                {{ $supply->medicine->brand_name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="inline-flex items-center px-3 py-1 rounded-xl bg-gray-50 border border-gray-100 text-[11px] font-black text-gray-600 uppercase tracking-tight">
                                    <i class="bi bi-hash mr-1 text-gray-400"></i>
                                    {{ $supply->batch_number ?? 'NO BATCH' }}
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900">{{ number_format($supply->quantity) }}</div>
                                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Units</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @php
                                    $isExpired = $supply->expiration_date && $supply->expiration_date->isPast();
                                    $isExpiringSoon = $supply->expiration_date && !$isExpired && $supply->expiration_date->diffInMonths(now()) < 6;
                                @endphp
                                @if($supply->expiration_date)
                                    <div @class([
                                        'text-sm font-black',
                                        'text-red-600' => $isExpired,
                                        'text-orange-600' => $isExpiringSoon,
                                        'text-gray-900' => !$isExpired && !$isExpiringSoon,
                                    ])>
                                        {{ $supply->expiration_date->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        @if($isExpired)
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            <span class="text-[9px] font-black text-red-600 uppercase tracking-widest">Expired</span>
                                        @elseif($isExpiringSoon)
                                            <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                                            <span class="text-[9px] font-black text-orange-600 uppercase tracking-widest">Expiring Soon</span>
                                        @else
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Active Stock</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-gray-400">
                                        <i class="bi bi-dash-circle"></i>
                                        <span class="text-sm font-bold uppercase tracking-widest text-[10px]">No Expiry</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                                        <i class="bi bi-truck text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900">
                                            {{ $supply->supplier_name ?? 'Direct Supply' }}
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Received By:</span>
                                            <span class="text-[10px] font-bold text-brand-600">{{ $supply->receiver?->full_name ?? 'System' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-24 w-24 rounded-[2.5rem] bg-gray-50 flex items-center justify-center text-gray-200 text-4xl mb-6 border border-dashed border-gray-200">
                                        <i class="bi bi-archive"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 tracking-tight">No Records Found</h3>
                                    <p class="text-gray-500 max-w-xs mx-auto mt-2 font-medium">We couldn't find any supply transactions matching your filters.</p>
                                    <div class="mt-8 flex items-center gap-3 justify-center">
                                        <a href="{{ route('admin.medicines.supplies') }}" class="inline-flex items-center px-6 py-3 rounded-2xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                                            <i class="bi bi-x-lg mr-2"></i>
                                            Clear Filters
                                        </a>
                                        <a href="{{ route('admin.medicines.supplies.create') }}" class="inline-flex items-center px-6 py-3 rounded-2xl bg-brand-50 text-brand-600 text-sm font-bold hover:bg-brand-100 transition-all">
                                            <i class="bi bi-plus-lg mr-2"></i>
                                            New Supply
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($supplies->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $supplies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

