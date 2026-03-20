@extends('layouts.app')

@section('title', 'Medicine Distribution Logs')

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
                    Distribution <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Logs</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Monitor and track how medicines are being dispensed to patients in real-time.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.medicines.index') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-white border border-gray-200 text-gray-700 font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Inventory
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
                <a href="{{ route('admin.medicines.supplies') }}" 
                   class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-600 transition-all whitespace-nowrap">
                    Supplies
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
                @if(request()->hasAny(['date_from', 'date_to', 'medicine_id']))
                    <a href="{{ route('admin.medicines.distributions') }}" class="p-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all">
                        <i class="bi bi-x-lg text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Timestamp</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Patient</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Medicine</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Qty</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Dosage / Schedule</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Dispensed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($distributions as $record)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
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
                            <td class="px-8 py-6">
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
                                    <div class="text-sm font-bold text-gray-700">
                                        {{ $record->healthWorker?->full_name ?? 'System' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-300 mb-4">
                                        <i class="bi bi-search text-4xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">No logs found</h3>
                                    <p class="text-gray-500 max-w-xs mx-auto">Try adjusting your filters to see more results.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($distributions->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
                {{ $distributions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

