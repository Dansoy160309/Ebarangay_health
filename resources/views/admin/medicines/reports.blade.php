@extends('layouts.app')

@section('title', 'Medicine Reports')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ 
    tab: 'distribution',
    inventoryFilter: 'all',
    setInventoryFilter(filter) {
        this.tab = 'inventory';
        this.inventoryFilter = filter;
    }
}">
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
                            <a href="{{ route('admin.medicines.index') }}" class="hover:text-brand-600 transition-colors">Inventory</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="bi bi-chevron-right text-gray-400 mx-2 text-[10px]"></i>
                            <span class="text-gray-900">Reports</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                <span class="p-3 bg-brand-50 rounded-2xl text-brand-600 shadow-sm">
                    <i class="bi bi-pie-chart-fill"></i>
                </span>
                Medicine Reports
            </h1>
            <p class="mt-2 text-lg text-gray-500 font-medium">Visual insights into distribution, inventory, usage, and supplies.</p>
        </div>
        
        <a href="{{ route('admin.medicines.index') }}" 
           class="inline-flex items-center px-6 py-2.5 rounded-2xl border border-gray-200 text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
            <i class="bi bi-arrow-left me-2 text-brand-500"></i>
            Back to Inventory
        </a>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 mb-10">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-8 items-end">
            <div class="md:col-span-3 space-y-3">
                <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] flex items-center gap-2">
                    <i class="bi bi-layers-fill text-brand-500"></i>
                    Report Type
                </label>
                <div class="flex bg-gray-50 p-1 rounded-xl border border-gray-100">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="daily" class="sr-only peer" @checked($type === 'daily') onchange="this.form.submit()">
                        <div class="py-2.5 text-center text-sm font-bold rounded-lg peer-checked:bg-white peer-checked:text-brand-600 peer-checked:shadow-sm text-gray-500 hover:text-gray-700 transition-all">
                            Daily
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="monthly" class="sr-only peer" @checked($type === 'monthly') onchange="this.form.submit()">
                        <div class="py-2.5 text-center text-sm font-bold rounded-lg peer-checked:bg-white peer-checked:text-brand-600 peer-checked:shadow-sm text-gray-500 hover:text-gray-700 transition-all">
                            Monthly
                        </div>
                    </label>
                </div>
            </div>

            @if($type === 'daily')
                <div class="md:col-span-4 space-y-3">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] flex items-center gap-2">
                        <i class="bi bi-calendar-event text-brand-500"></i>
                        Select Date
                    </label>
                    <input type="date" name="date" value="{{ $selectedDate ?? now()->toDateString() }}"
                           class="w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-sm font-bold text-gray-700">
                </div>
            @else
                <div class="md:col-span-2 space-y-3">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] flex items-center gap-2">
                        <i class="bi bi-calendar-month text-brand-500"></i>
                        Month
                    </label>
                    <select name="month" class="w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-sm font-bold text-gray-700">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(($selectedMonth ?? now()->month) == $m)>
                                {{ \Illuminate\Support\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="md:col-span-2 space-y-3">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] flex items-center gap-2">
                        <i class="bi bi-calendar-check text-brand-500"></i>
                        Year
                    </label>
                    <input type="number" name="year" value="{{ $selectedYear ?? now()->year }}"
                           class="w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-sm font-bold text-gray-700">
                </div>
            @endif

            <div class="md:col-span-5 flex flex-wrap lg:flex-nowrap gap-3">
                <button type="submit" class="flex-grow px-8 py-3 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200 flex items-center justify-center gap-2">
                    <i class="bi bi-funnel"></i>
                    Apply Filters
                </button>
                <div class="flex gap-2 flex-grow lg:flex-grow-0">
                    <button type="button" id="exportPdfBtn" class="flex-1 px-5 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-file-earmark-pdf text-red-500"></i>
                        PDF
                    </button>
                    <a href="{{ route('admin.medicines.reports.export', request()->all()) }}" class="flex-1 px-5 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-file-earmark-excel text-emerald-500"></i>
                        Excel
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">
        <button type="button" @click="setInventoryFilter('all')" class="group text-left bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 flex flex-col gap-4 hover:border-brand-500/30 transition-all active:scale-95">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 text-2xl group-hover:scale-110 transition-transform">
                <i class="bi bi-capsule-pill"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Medicines</div>
                <div class="text-3xl font-black text-gray-900 tracking-tight">{{ number_format($totalMedicines) }}</div>
            </div>
        </button>
        
        <button type="button" @click="setInventoryFilter('all')" class="group text-left bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 flex flex-col gap-4 hover:border-blue-500/30 transition-all active:scale-95">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 text-2xl group-hover:scale-110 transition-transform">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Stock</div>
                <div class="text-3xl font-black text-gray-900 tracking-tight">{{ number_format($totalStock) }}</div>
            </div>
        </button>
        
        <button type="button" @click="tab = 'distribution'" class="group text-left bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 flex flex-col gap-4 hover:border-emerald-500/30 transition-all active:scale-95">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 text-2xl group-hover:scale-110 transition-transform">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                    Distributed
                </div>
                <div class="text-3xl font-black text-gray-900 tracking-tight">{{ number_format($distributedInPeriod) }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">
                    {{ $type === 'daily' ? 'For selected date' : 'For selected month' }}
                </div>
            </div>
        </button>
        
        <button type="button" @click="setInventoryFilter('low_stock')" class="group text-left bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 flex flex-col gap-4 hover:border-red-500/30 transition-all active:scale-95" :class="inventoryFilter === 'low_stock' && tab === 'inventory' ? 'ring-2 ring-red-500 border-transparent' : ''">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600 text-2xl group-hover:scale-110 transition-transform">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Low Stock</div>
                <div class="text-3xl font-black text-red-600 tracking-tight">{{ number_format($lowStockCount) }}</div>
            </div>
        </button>
        
        <button type="button" @click="setInventoryFilter('expiring')" class="group text-left bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 flex flex-col gap-4 hover:border-orange-500/30 transition-all active:scale-95" :class="inventoryFilter === 'expiring' && tab === 'inventory' ? 'ring-2 ring-orange-500 border-transparent' : ''">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 text-2xl group-hover:scale-110 transition-transform">
                <i class="bi bi-alarm"></i>
            </div>
            <div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Expiring Soon</div>
                <div class="text-3xl font-black text-orange-600 tracking-tight">{{ number_format($expiringSoonCount) }}</div>
            </div>
        </button>
    </div>

    {{-- Main Content Tabs --}}
    <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden min-h-[600px]">
        {{-- Tab Navigation --}}
        <div class="px-10 pt-8 border-b border-gray-100 bg-gray-50/30 flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6">
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="tab = 'distribution'"
                        :class="tab === 'distribution' ? 'bg-brand-600 text-white shadow-lg shadow-brand-100' : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'"
                        class="px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2">
                    <i class="bi bi-journal-text"></i>
                    Distributions
                </button>
                <button type="button" @click="setInventoryFilter('all')"
                        :class="tab === 'inventory' ? 'bg-brand-600 text-white shadow-lg shadow-brand-100' : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'"
                        class="px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2">
                    <i class="bi bi-boxes"></i>
                    Inventory
                </button>
                <button type="button" @click="tab = 'usage'"
                        :class="tab === 'usage' ? 'bg-brand-600 text-white shadow-lg shadow-brand-100' : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'"
                        class="px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2">
                    <i class="bi bi-bar-chart"></i>
                    Usage
                </button>
                <button type="button" @click="tab = 'supply'"
                        :class="tab === 'supply' ? 'bg-brand-600 text-white shadow-lg shadow-brand-100' : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'"
                        class="px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2">
                    <i class="bi bi-truck"></i>
                    Supplies
                </button>
            </div>
            
            <div class="relative group max-w-xs w-full">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-500 transition-colors"></i>
                <input id="reportSearchInput" type="text" placeholder="Filter records..."
                       class="w-full pl-12 pr-4 py-3 rounded-2xl border-gray-100 bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-xs font-bold">
            </div>
        </div>

        <div class="p-10">
            {{-- Distributions Tab --}}
            <div x-show="tab === 'distribution'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-brand-600 rounded-full"></span>
                        Distribution Records
                    </h3>
                    <span class="px-4 py-1.5 rounded-full bg-brand-50 text-brand-700 text-[10px] font-black uppercase tracking-widest">
                        {{ $distributions->count() }} Records
                    </span>
                </div>
                
                <div class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm">
                    <table id="distributionTable" data-report-table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Date</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Medicine</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Patient</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($distributions as $record)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-calendar3 text-gray-400 text-xs"></i>
                                            <span class="text-sm font-bold text-gray-700">
                                                {{ $record->distributed_at?->format('M d, Y') ?? $record->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-900 text-sm uppercase tracking-tight">{{ $record->medicine?->generic_name }}</div>
                                        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $record->medicine?->brand_name ?? 'Generic' }}</div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-500 uppercase">
                                                {{ substr($record->patient?->first_name ?? 'N', 0, 1) }}{{ substr($record->patient?->last_name ?? 'A', 0, 1) }}
                                            </div>
                                            <span class="text-sm font-bold text-gray-700">{{ $record->patient?->full_name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-black tracking-tight">
                                            {{ $record->quantity }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
                                                <i class="bi bi-journal-x text-3xl"></i>
                                            </div>
                                            <h4 class="text-lg font-bold text-gray-900">No distribution records</h4>
                                            <p class="text-sm text-gray-500">There are no records found for the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Inventory Tab --}}
            <div x-show="tab === 'inventory'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-blue-600 rounded-full"></span>
                        <span x-text="inventoryFilter === 'low_stock' ? 'Low Stock Inventory' : (inventoryFilter === 'expiring' ? 'Expiring Soon' : 'Full Inventory Summary')"></span>
                    </h3>
                    <button x-show="inventoryFilter !== 'all'" @click="inventoryFilter = 'all'" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                        <i class="bi bi-x-circle"></i> Clear Filter
                    </button>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 mb-10" x-show="inventoryFilter === 'all'">
                    <div class="lg:col-span-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                                <span class="w-1.5 h-8 bg-blue-600 rounded-full"></span>
                                Stock Movement
                            </h3>
                        </div>
                        <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm">
                            @if(collect($stockChartData)->sum() !== 0)
                                <div class="h-[350px]">
                                    <canvas id="stockLineChart"></canvas>
                                </div>
                            @else
                                <div class="h-[350px] flex flex-col items-center justify-center text-gray-400">
                                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
                                        <i class="bi bi-graph-down text-3xl"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-900">No movement data</h4>
                                    <p class="text-sm text-gray-500">No stock changes recorded for this period.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="lg:col-span-4 space-y-6">
                        <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                            <span class="w-1.5 h-8 bg-orange-600 rounded-full"></span>
                            Insights
                        </h3>
                        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-gray-900/10">
                            <h4 class="text-xs font-black text-brand-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <i class="bi bi-info-circle"></i>
                                Legend & Guide
                            </h4>
                            <div class="space-y-6">
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                                        <i class="bi bi-graph-up text-brand-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold mb-1">Stock Volatility</p>
                                        <p class="text-xs text-gray-400 leading-relaxed">The chart displays net stock changes based on daily arrivals and distributions.</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                                        <i class="bi bi-shield-check text-emerald-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold mb-1">Safety Levels</p>
                                        <p class="text-xs text-gray-400 leading-relaxed">Upward trends indicate replenishment, while downward trends show dispensing activity.</p>
                                    </div>
                                </div>
                                <div class="pt-6 border-t border-white/10">
                                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Inventory Health</p>
                                        <div class="flex items-end justify-between">
                                            <span class="text-2xl font-black">{{ $totalMedicines > 0 ? round((($totalMedicines - $lowStockCount) / $totalMedicines) * 100) : 0 }}%</span>
                                            <span class="text-xs text-brand-400 font-bold">Stable Items</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm">
                    <table id="inventoryTable" data-report-table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Medicine</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Stock Level</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Reorder Point</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Expiration Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($inventorySummary as $medicine)
                                @php
                                    $isLowStock = $medicine->stock <= $medicine->reorder_level;
                                    $isExpiringSoon = $medicine->expiration_date && $medicine->expiration_date->lte(now()->addDays(30));
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors group"
                                    x-show="inventoryFilter === 'all' || 
                                           (inventoryFilter === 'low_stock' && {{ $isLowStock ? 'true' : 'false' }}) || 
                                           (inventoryFilter === 'expiring' && {{ $isExpiringSoon ? 'true' : 'false' }})">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-900 text-sm uppercase tracking-tight group-hover:text-brand-600 transition-colors">{{ $medicine->generic_name }}</div>
                                        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $medicine->brand_name ?? 'Generic' }}</div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base font-black {{ $isLowStock ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ $medicine->stock }}
                                            </span>
                                            @if($isLowStock)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-600">
                                                    Low
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-gray-500">{{ $medicine->reorder_level }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if($medicine->expiration_date)
                                            <div class="flex flex-col gap-0.5">
                                                <span class="text-sm font-bold {{ $isExpiringSoon ? 'text-orange-600' : 'text-gray-700' }}">
                                                    {{ $medicine->expiration_date->format('M d, Y') }}
                                                </span>
                                                @if($isExpiringSoon)
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-orange-500">Expiring</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-gray-300 uppercase tracking-widest">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
                                                <i class="bi bi-boxes text-3xl"></i>
                                            </div>
                                            <h4 class="text-lg font-bold text-gray-900">Inventory is empty</h4>
                                            <p class="text-sm text-gray-500">No medicine items found in the current inventory list.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Usage Tab --}}
            <div x-show="tab === 'usage'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-emerald-600 rounded-full"></span>
                        Consumption Analytics
                    </h3>
                </div>

                <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm mb-10">
                    <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] mb-8 flex items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-brand-500"></i>
                        Top Medicines by Usage
                    </h4>
                    @if($usageByMedicine->count() > 0)
                        <div class="h-[400px]">
                            <canvas id="usageBarChart"></canvas>
                        </div>
                    @else
                        <div class="h-[350px] flex flex-col items-center justify-center text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
                                <i class="bi bi-bar-chart-steps text-3xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900">No usage analytics</h4>
                            <p class="text-sm text-gray-500">No medicine distributions found for the selected period.</p>
                        </div>
                    @endif
                </div>

                @if($usageByMedicine->count() > 0)
                    <div class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm">
                        <table id="usageTable" data-report-table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Medicine</th>
                                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Total Units</th>
                                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Patients Served</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($usageByMedicine as $row)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-8 py-5">
                                            <div class="font-bold text-gray-900 text-sm uppercase tracking-tight group-hover:text-brand-600 transition-colors">{{ $row['medicine']?->generic_name ?? 'Unknown' }}</div>
                                            <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $row['medicine']?->brand_name ?? 'Generic' }}</div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-base font-black tracking-tight">
                                                {{ $row['total_quantity'] }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-sm font-bold text-gray-700">{{ $row['total_doses'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Supply Tab --}}
            <div x-show="tab === 'supply'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-purple-600 rounded-full"></span>
                        Supply Activity
                    </h3>
                    <span class="px-4 py-1.5 rounded-full bg-purple-50 text-purple-700 text-[10px] font-black uppercase tracking-widest">
                        Grouped by Item
                    </span>
                </div>

                <div class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm">
                    <table id="supplyTable" data-report-table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Medicine</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Total Qty Received</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] sortable cursor-pointer hover:text-brand-600 transition-colors">Delivery Batches</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($supplyByMedicine as $row)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-900 text-sm uppercase tracking-tight group-hover:text-brand-600 transition-colors">{{ $row['medicine']?->generic_name ?? 'Unknown' }}</div>
                                        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $row['medicine']?->brand_name ?? 'Generic' }}</div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-xl bg-purple-50 text-purple-700 text-base font-black tracking-tight">
                                            {{ $row['total_quantity'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-gray-700">{{ $row['total_deliveries'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
                                                <i class="bi bi-truck text-3xl"></i>
                                            </div>
                                            <h4 class="text-lg font-bold text-gray-900">No supply activity</h4>
                                            <p class="text-sm text-gray-500">No medicine supplies were received during the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- Data for JS --}}
    <div id="report-data" 
         data-usage-labels='{!! json_encode($usageChartLabels) !!}'
         data-usage-data='{!! json_encode($usageChartData) !!}'
         data-stock-labels='{!! json_encode($stockChartLabels) !!}'
         data-stock-data='{!! json_encode($stockChartData) !!}'
         class="hidden">
    </div>
</div>
@endsection

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dataEl = document.getElementById('report-data');
            const usageLabels = JSON.parse(dataEl.dataset.usageLabels);
            const usageData = JSON.parse(dataEl.dataset.usageData);
            const stockLabels = JSON.parse(dataEl.dataset.stockLabels);
            const stockData = JSON.parse(dataEl.dataset.stockData);

            const usageCtx = document.getElementById('usageBarChart');
            if (usageCtx && usageLabels.length > 0) {
                new Chart(usageCtx, {
                    type: 'bar',
                    data: {
                        labels: usageLabels,
                        datasets: [{
                            label: 'Quantity Dispensed',
                            data: usageData,
                            backgroundColor: '#0ea5e9',
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' units';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { autoSkip: false, maxRotation: 60, minRotation: 0 }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }

            const stockCtx = document.getElementById('stockLineChart');
            if (stockCtx && stockLabels.length > 0 && stockData.some(v => v !== 0)) {
                new Chart(stockCtx, {
                    type: 'line',
                    data: {
                        labels: stockLabels,
                        datasets: [{
                            label: 'Net Stock Level',
                            data: stockData,
                            borderColor: '#ffffff',
                            backgroundColor: 'rgba(255,255,255,0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 3,
                            pointBackgroundColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' units (net)';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#e0f2fe' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: '#e0f2fe' },
                                grid: { color: 'rgba(255,255,255,0.15)' }
                            }
                        }
                    }
                });
            }

            const searchInput = document.getElementById('reportSearchInput');
            const exportPdfBtn = document.getElementById('exportPdfBtn');
            const exportExcelBtn = document.getElementById('exportExcelBtn');

            function getActiveTable() {
                const visibleTables = Array.from(document.querySelectorAll('table[data-report-table]'))
                    .filter(table => table.offsetParent !== null);
                return visibleTables[0] || null;
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const term = this.value.toLowerCase();
                    const table = getActiveTable();
                    if (!table) {
                        return;
                    }
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(term) ? '' : 'none';
                    });
                });
            }

            function exportTableToExcel(table, filename) {
                const html = table.outerHTML;
                const blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            }

            function exportTableToPdf(table, title) {
                const printWindow = window.open('', '', 'height=800,width=1000');
                if (!printWindow) {
                    return;
                }
                printWindow.document.write('<html><head><title>' + title + '</title>');
                printWindow.document.write('<link rel="stylesheet" href="' + '{{ asset("css/bootstrap-icons.css") }}' + '">');
                printWindow.document.write('<style>table{width:100%;border-collapse:collapse;font-size:12px;}th,td{border:1px solid #e5e7eb;padding:6px;text-align:left;}thead{background:#f9fafb;font-weight:600;}</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<h2>' + title + '</h2>');
                printWindow.document.write(table.outerHTML);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            }

            if (exportExcelBtn) {
                exportExcelBtn.addEventListener('click', function () {
                    const table = getActiveTable();
                    if (table) {
                        exportTableToExcel(table, 'medicine-report.xls');
                    }
                });
            }

            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', function () {
                    const table = getActiveTable();
                    if (table) {
                        exportTableToPdf(table, 'Medicine Report');
                    }
                });
            }

            document.querySelectorAll('table[data-report-table] thead th.sortable').forEach(function (header, index) {
                header.addEventListener('click', function () {
                    const table = header.closest('table');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const ascending = header.dataset.sort !== 'asc';
                    document.querySelectorAll('th.sortable').forEach(th => th.dataset.sort = '');
                    header.dataset.sort = ascending ? 'asc' : 'desc';
                    rows.sort(function (a, b) {
                        const aText = a.children[index].textContent.trim();
                        const bText = b.children[index].textContent.trim();
                        const aNum = parseFloat(aText.replace(/[^0-9.-]/g, ''));
                        const bNum = parseFloat(bText.replace(/[^0-9.-]/g, ''));
                        const aIsNum = !isNaN(aNum);
                        const bIsNum = !isNaN(bNum);
                        let result;
                        if (aIsNum && bIsNum) {
                            result = aNum - bNum;
                        } else {
                            result = aText.localeCompare(bText);
                        }
                        return ascending ? result : -result;
                    });
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    </script>
@endsection
