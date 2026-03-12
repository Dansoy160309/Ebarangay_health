@extends('layouts.app')

@section('title', 'Medicine Supply History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 text-[11px] font-black uppercase tracking-widest">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center">
                    <i class="bi bi-house-door mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center text-gray-300">
                    <i class="bi bi-chevron-right mx-2 text-[8px]"></i>
                    <a href="{{ route('admin.medicines.index') }}" class="text-gray-400 hover:text-brand-600 transition-colors">Inventory</a>
                </div>
            </li>
            <li>
                <div class="flex items-center text-gray-900">
                    <i class="bi bi-chevron-right mx-2 text-[8px]"></i>
                    <span>Supply History</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-widest mb-4">
                <i class="bi bi-clock-history mr-2"></i>
                Transaction Logs
            </div>
            <h1 class="text-5xl font-black text-gray-900 tracking-tight">Supply History</h1>
            <p class="text-gray-500 font-medium mt-2">Track and manage medicine replenishment records.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center px-6 py-3.5 rounded-2xl border border-gray-200 text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
                <i class="bi bi-arrow-left mr-2 text-gray-400"></i>
                Inventory
            </a>
            <a href="{{ route('admin.medicines.supplies.create') }}" class="inline-flex items-center px-8 py-3.5 rounded-2xl bg-brand-600 text-white text-sm font-black hover:bg-brand-700 transition-all active:scale-95 shadow-xl shadow-brand-200">
                <i class="bi bi-plus-lg mr-2"></i>
                Add Supply
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 rounded-3xl border border-green-100 bg-green-50/50 p-4 text-sm text-green-800 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-10 w-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                <i class="bi bi-filter-right text-xl"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Filter Records</h2>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">From Date</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-medium transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">To Date</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-medium transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Medicine</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <select name="medicine_id" class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-medium transition-all appearance-none">
                        <option value="">All Medicines</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                                {{ $medicine->generic_name }} {{ $medicine->brand_name ? '('.$medicine->brand_name.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Supplier</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-truck"></i>
                    </div>
                    <input type="text" name="supplier_name" value="{{ request('supplier_name') }}"
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-medium transition-all placeholder:text-gray-400" placeholder="Search supplier...">
                </div>
            </div>

            <div class="md:col-span-4 flex items-center justify-between pt-2">
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-8 py-3 rounded-2xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                        <i class="bi bi-search mr-2"></i>
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['date_from','date_to','medicine_id','supplier_name']))
                        <a href="{{ route('admin.medicines.supplies') }}" class="inline-flex items-center px-6 py-3 rounded-2xl text-sm font-bold text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all">
                            <i class="bi bi-x-lg mr-2"></i>
                            Clear
                        </a>
                    @endif
                </div>
                <div class="text-xs font-bold text-gray-400">
                    Showing <span class="text-gray-900">{{ $supplies->firstItem() ?? 0 }}</span> to <span class="text-gray-900">{{ $supplies->lastItem() ?? 0 }}</span> of <span class="text-gray-900">{{ $supplies->total() }}</span> records
                </div>
            </div>
        </form>
    </div>

    {{-- History Table --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-calendar3"></i>
                                Date Received
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-capsule-pill"></i>
                                Medicine Info
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-hash"></i>
                                Batch No.
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-box-seam"></i>
                                Quantity
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-clock-history"></i>
                                Expiration
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-truck"></i>
                                Supplier & Receiver
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($supplies as $supply)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6 whitespace-nowrap">
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

