@extends('layouts.app')

@section('title', 'Vaccine Details')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    
    {{-- Header --}}
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden relative group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2">
                    <i class="bi bi-box-seam text-[9px]"></i>
                    <span class="text-[9px] font-black uppercase tracking-tight">Antigen Details</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-tight mb-1">
                    {{ $vaccine->name }}
                </h1>
                <p class="text-gray-500 font-medium text-sm leading-relaxed">
                    {{ $vaccine->manufacturer ?? 'Unknown Manufacturer' }} • {{ $vaccine->storage_temp_range ?? 'N/A' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.vaccines.edit', $vaccine) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-white text-brand-600 border border-brand-100 font-black text-[10px] uppercase tracking-widest shadow-sm hover:bg-brand-50 transition-all">
                    <i class="bi bi-pencil-square mr-1.5"></i> Edit
                </a>
                <a href="{{ route('admin.vaccines.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-50 text-gray-600 border border-gray-200 font-black text-[10px] uppercase tracking-widest shadow-sm hover:bg-gray-100 transition-all">
                    <i class="bi bi-arrow-left mr-1.5"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">
        {{-- Summary Stats --}}
        <div class="lg:col-span-1 flex flex-col gap-4 sm:gap-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Stock Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-bold text-gray-500">Current Stock</span>
                        <span class="text-2xl font-black text-gray-900">{{ $vaccine->in_stock_quantity }}</span>
                    </div>
                    <div class="flex justify-between items-end border-t border-gray-50 pt-4">
                        <span class="text-xs font-bold text-gray-500">Min. Stock Level</span>
                        <span class="text-xl font-black text-gray-900">{{ $vaccine->min_stock_level }}</span>
                    </div>
                    <div class="flex justify-between items-end border-t border-gray-50 pt-4">
                        <span class="text-xs font-bold text-gray-500">Active Batches</span>
                        <span class="text-xl font-black text-gray-900">{{ $vaccine->batches->where('quantity_remaining', '>', 0)->count() }}</span>
                    </div>
                    <div class="mt-4">
                        @if($vaccine->in_stock_quantity <= $vaccine->min_stock_level)
                            <div class="bg-red-50 text-red-600 rounded-lg p-3 flex items-center gap-2 text-[10px] font-black uppercase tracking-tight border border-red-100">
                                <i class="bi bi-exclamation-triangle-fill"></i> Low Stock Alert
                            </div>
                        @else
                            <div class="bg-emerald-50 text-emerald-600 rounded-lg p-3 flex items-center gap-2 text-[10px] font-black uppercase tracking-tight border border-emerald-100">
                                <i class="bi bi-check-circle-fill"></i> Stock level healthy
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Storage Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Temperature Range</p>
                        <p class="text-sm font-bold text-gray-900">{{ $vaccine->storage_temp_range ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Description</p>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $vaccine->description ?? 'No description available.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Batches and History --}}
        <div class="lg:col-span-2 flex flex-col gap-4 sm:gap-5">
            {{-- Active Batches --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Active Batches (Inventory)</h3>
                    <span class="text-[10px] font-black text-brand-600 uppercase tracking-widest">{{ $vaccine->batches->where('quantity_remaining', '>', 0)->count() }} active</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batch No.</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Received</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Expiry</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Received Qty</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Quantity</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($vaccine->batches->where('quantity_remaining', '>', 0) as $batch)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 font-black text-xs text-gray-900">{{ $batch->batch_number }}</td>
                                    <td class="px-5 py-3 text-xs font-bold text-gray-600">{{ $batch->received_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs font-bold {{ $batch->expiry_date->isPast() ? 'text-red-500' : ($batch->expiry_date->diffInMonths(now()) < 3 ? 'text-orange-500' : 'text-gray-600') }}">
                                            {{ $batch->expiry_date->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-black text-xs text-gray-900">{{ $batch->quantity_received }}</td>
                                    <td class="px-5 py-3 text-right font-black text-xs text-gray-900">{{ $batch->quantity_remaining }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $batch->isExpired() ? 'bg-red-50 text-red-600' : ($batch->quantity_remaining <= 5 ? 'bg-orange-50 text-orange-600' : 'bg-emerald-50 text-emerald-600') }}">
                                            {{ $batch->isExpired() ? 'Expired' : ($batch->quantity_remaining <= 5 ? 'Low' : 'Active') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-gray-400 font-medium italic text-xs">No active batches in stock.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Batch History --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Batch History</h3>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">All recorded batches</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batch No.</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Received</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Expiry</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Received Qty</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Administered</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Remaining</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Source</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($vaccine->batches->sortByDesc('received_at') as $batch)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 font-black text-xs text-gray-900">{{ $batch->batch_number }}</td>
                                    <td class="px-5 py-3 text-xs font-bold text-gray-600">{{ $batch->received_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-5 py-3 text-xs font-bold {{ $batch->expiry_date->isPast() ? 'text-red-500' : ($batch->expiry_date->diffInMonths(now()) < 3 ? 'text-orange-500' : 'text-gray-600') }}">{{ $batch->expiry_date->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-right font-black text-xs text-gray-900">{{ $batch->quantity_received }}</td>
                                    <td class="px-5 py-3 text-right font-black text-xs text-gray-900">{{ $batch->quantity_administered }}</td>
                                    <td class="px-5 py-3 text-right font-black text-xs text-gray-900">{{ $batch->quantity_remaining }}</td>
                                    <td class="px-5 py-3 text-right text-xs font-bold text-gray-600">{{ $batch->supplier ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-400 font-medium italic text-xs">No batch records available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Administrations --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Recent Administrations</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" data-recent-admin-table data-refresh-url="{{ route('admin.vaccines.recent-administrations', $vaccine) }}">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batch</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Qty</th>
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Administered By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" data-recent-admin-tbody>
                            @include('admin.vaccines.partials.recent-administrations-rows', ['administrations' => $vaccine->administrations])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
(function () {
    const table = document.querySelector('[data-recent-admin-table]');
    const tbody = document.querySelector('[data-recent-admin-tbody]');

    if (!table || !tbody) {
        return;
    }

    const refreshUrl = table.dataset.refreshUrl;

    const refreshRecentAdministrations = async () => {
        try {
            const response = await fetch(refreshUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                cache: 'no-store',
            });

            if (!response.ok) {
                return;
            }

            const html = await response.text();
            tbody.innerHTML = html;
        } catch (error) {
            console.warn('Unable to refresh recent administrations.', error);
        }
    };

    refreshRecentAdministrations();
    setInterval(refreshRecentAdministrations, 15000);
})();
</script>
@endsection
@endsection
