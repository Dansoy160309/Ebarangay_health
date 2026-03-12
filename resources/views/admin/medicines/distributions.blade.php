@extends('layouts.app')

@section('title', 'Medicine Distribution Logs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
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
                    <span class="text-gray-900">Distribution Logs</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Distribution Logs</h1>
            <p class="text-gray-500 font-medium">Monitor and track how medicines are being dispensed to patients.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-2xl border border-gray-200 text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
                <i class="bi bi-arrow-left mr-2"></i>
                Inventory
            </a>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-10 w-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                <i class="bi bi-filter-right text-xl"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Filter Distribution</h2>
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

            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-3 rounded-2xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                    <i class="bi bi-search mr-2"></i>
                    Filter Logs
                </button>
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-clock-history"></i>
                                Timestamp
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-person"></i>
                                Patient
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-capsule-pill"></i>
                                Medicine
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-box-seam"></i>
                                Qty
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-prescription2"></i>
                                Dosage / Schedule
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-person-check"></i>
                                Dispensed By
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($distributions as $record)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ $record->distributed_at?->format('M d, Y') ?? $record->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase mt-0.5 tracking-tight">
                                    {{ $record->distributed_at?->format('h:i A') ?? $record->created_at->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-black">
                                        {{ substr($record->patient?->full_name ?? 'N', 0, 1) }}
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ $record->patient?->full_name ?? 'N/A' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-black text-gray-900 group-hover:text-brand-600 transition-colors">
                                    {{ $record->medicine?->generic_name }}
                                </div>
                                @if($record->medicine?->brand_name)
                                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">
                                        {{ $record->medicine->brand_name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span class="text-sm font-black text-gray-900">{{ number_format($record->quantity) }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Units</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ $record->dosage }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-gray-100 text-gray-600 uppercase tracking-widest">
                                        {{ $record->frequency }}
                                    </span>
                                    <span class="text-gray-300 text-[10px]">•</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">
                                        {{ $record->duration }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                                        <i class="bi bi-person-check text-sm"></i>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ $record->midwife?->full_name ?? 'System' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 rounded-[2rem] bg-gray-50 flex items-center justify-center text-gray-300 text-3xl mb-4">
                                        <i class="bi bi-prescription2"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">No logs found</h3>
                                    <p class="text-gray-500 max-w-xs mx-auto mt-1">No distribution records match your current filter settings.</p>
                                    <a href="{{ route('admin.medicines.distributions') }}" class="mt-6 text-sm font-bold text-brand-600 hover:text-brand-700 underline underline-offset-4">
                                        Reset filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($distributions->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $distributions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

