@extends('layouts.app')

@section('title', 'Vaccine Inventory')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showVaccineModal: false, showBatchModal: false }">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Vaccine Inventory</h1>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">Stock Management & Expiry Tracking</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showVaccineModal = true" class="px-6 py-3.5 bg-white border border-gray-200 rounded-2xl text-xs font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all shadow-sm active:scale-95 flex items-center gap-2">
                <i class="bi bi-plus-circle text-lg"></i> Register Vaccine
            </button>
            <button @click="showBatchModal = true" class="px-6 py-3.5 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/20 active:scale-95 flex items-center gap-2">
                <i class="bi bi-box-seam text-lg"></i> Stock In (New Batch)
            </button>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($nearExpiryBatches->count() > 0 || $lowStockVaccines->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        @if($nearExpiryBatches->count() > 0)
        <div class="bg-orange-50 border border-orange-100 rounded-[2rem] p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600 shrink-0">
                <i class="bi bi-calendar-x text-2xl"></i>
            </div>
            <div>
                <h4 class="text-sm font-black text-orange-900 uppercase tracking-wider">Near Expiry Alert</h4>
                <p class="text-xs font-bold text-orange-700 mt-1">{{ $nearExpiryBatches->count() }} batches are expiring within 3 months.</p>
            </div>
        </div>
        @endif

        @if($lowStockVaccines->count() > 0)
        <div class="bg-red-50 border border-red-100 rounded-[2rem] p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                <i class="bi bi-graph-down-arrow text-2xl"></i>
            </div>
            <div>
                <h4 class="text-sm font-black text-red-900 uppercase tracking-wider">Low Stock Alert</h4>
                <p class="text-xs font-bold text-red-700 mt-1">{{ $lowStockVaccines->count() }} vaccines are below minimum stock level.</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Inventory Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Vaccine Type</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Current Stock</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Active Batches</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Storage Temp</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($vaccines as $vaccine)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 font-black shadow-inner">
                                    {{ substr($vaccine->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900">{{ $vaccine->name }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $vaccine->manufacturer }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl {{ $vaccine->in_stock_quantity <= $vaccine->min_stock_level ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} text-xs font-black">
                                {{ $vaccine->in_stock_quantity }} doses
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex -space-x-2">
                                @foreach($vaccine->batches->where('quantity_remaining', '>', 0)->take(3) as $batch)
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-600" title="Batch {{ $batch->batch_number }}">
                                        {{ $loop->iteration }}
                                    </div>
                                @endforeach
                                @if($vaccine->batches->where('quantity_remaining', '>', 0)->count() > 3)
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-brand-50 flex items-center justify-center text-[10px] font-black text-brand-600">
                                        +{{ $vaccine->batches->where('quantity_remaining', '>', 0)->count() - 3 }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-gray-600">{{ $vaccine->storage_temp_range ?? 'N/A' }}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $vaccine->is_active ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20' }}">
                                {{ $vaccine->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Register Vaccine Modal -->
    <div x-show="showVaccineModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showVaccineModal = false"></div>
            <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md p-10">
                <h3 class="text-2xl font-black text-gray-900 mb-8">Register Vaccine Type</h3>
                <form action="{{ route('midwife.inventory.vaccine.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Vaccine Name</label>
                        <input type="text" name="name" required placeholder="e.g. BCG, HepB" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Manufacturer</label>
                        <input type="text" name="manufacturer" placeholder="e.g. GSK, Sanofi" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Min Stock Level</label>
                            <input type="number" name="min_stock_level" value="10" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Storage Temp</label>
                            <input type="text" name="storage_temp_range" placeholder="2-8°C" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="showVaccineModal = false" class="flex-1 py-4 bg-gray-100 rounded-2xl text-xs font-black uppercase text-gray-500">Cancel</button>
                        <button type="submit" class="flex-1 py-4 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase shadow-lg shadow-brand-500/20">Save Vaccine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock In Modal -->
    <div x-show="showBatchModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showBatchModal = false"></div>
            <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg p-10">
                <h3 class="text-2xl font-black text-gray-900 mb-8">Stock In New Batch</h3>
                <form action="{{ route('midwife.inventory.batch.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Select Vaccine</label>
                        <select name="vaccine_id" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                            @foreach($vaccines as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Batch Number</label>
                            <input type="text" name="batch_number" required placeholder="LOT-123" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Expiry Date</label>
                            <input type="date" name="expiry_date" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Quantity (Doses)</label>
                            <input type="number" name="quantity_received" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Date Received</label>
                            <input type="date" name="received_at" value="{{ date('Y-m-d') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="showBatchModal = false" class="flex-1 py-4 bg-gray-100 rounded-2xl text-xs font-black uppercase text-gray-500">Cancel</button>
                        <button type="submit" class="flex-1 py-4 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase shadow-lg shadow-brand-500/20">Add to Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection