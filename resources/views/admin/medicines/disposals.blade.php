@extends('layouts.app')

@section('title', 'Disposed Medicine Batches')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">

    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-60"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-red-50 text-red-600 border border-red-100 mb-3 sm:mb-4">
                    <i class="bi bi-archive-fill text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Archive</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Disposal <span class="text-red-600 underline decoration-red-200 decoration-4 underline-offset-4">Log</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Complete history of disposed expired medicine batches.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.medicines.index') }}"
                   class="inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-white border border-gray-200 text-gray-700 font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-lg shadow-gray-200/50 border border-gray-100 p-5 sm:p-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="pl-4 pr-4 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all text-[10px] font-black uppercase tracking-widest text-gray-700 shadow-inner">
            <span class="text-gray-300 font-black text-[10px]">TO</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="pl-4 pr-4 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all text-[10px] font-black uppercase tracking-widest text-gray-700 shadow-inner">

            <select name="medicine_id"
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
                <a href="{{ route('admin.medicines.disposals') }}" class="p-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all">
                    <i class="bi bi-x-lg text-xs"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Disposed At</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Medicine</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Batch No.</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Quantity</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Expired On</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Disposed By</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($disposals as $row)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6 text-sm font-black text-gray-900">
                                {{ $row->disposed_at?->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-8 py-6 text-sm font-black text-gray-900">
                                {{ $row->medicine?->generic_name ?? 'Unknown medicine' }}
                            </td>
                            <td class="px-8 py-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl bg-gray-50 border border-gray-100 text-[11px] font-black text-gray-600 uppercase tracking-tight">
                                    <i class="bi bi-hash mr-1 text-gray-400"></i>
                                    {{ $row->batch_number ?: 'NO BATCH' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-sm font-black text-gray-900">{{ number_format($row->quantity) }}</td>
                            <td class="px-8 py-6 text-sm font-black text-red-600">{{ $row->expiration_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-8 py-6 text-sm font-bold text-gray-700">{{ $row->disposer?->full_name ?? 'System' }}</td>
                            <td class="px-8 py-6 text-sm text-gray-600">{{ $row->disposal_notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center text-gray-500 font-semibold">No disposed batches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($disposals->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $disposals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
