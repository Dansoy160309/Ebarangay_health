@extends('layouts.app')

@section('title', 'Vaccine Inventory Management')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8" x-data="{ showBatchModal: false }">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-box-seam text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Immunization Supply</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Vaccine <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Inventory</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Monitor vaccine stock levels, manage lot numbers, and track expiration dates for immunization programs.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <a href="{{ route('admin.vaccines.create') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-white text-brand-600 border border-brand-100 font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-sm hover:bg-brand-50 hover:scale-105 transition-all transform group shrink-0">
                    <i class="bi bi-plus-circle mr-2 group-hover:rotate-12 transition-transform"></i>
                    Register New
                </a>
                <button @click="showBatchModal = true" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-brand-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:scale-105 transition-all transform group shrink-0">
                    <i class="bi bi-box-arrow-in-down mr-2 group-hover:-translate-y-1 transition-transform"></i>
                    RHU Stock In
                </button>
            </div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="bg-white rounded-[2rem] shadow-lg shadow-gray-200/50 border border-gray-100 p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-gray-500">
            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-brand-500">
                <i class="bi bi-info-circle"></i>
            </div>
            <span class="text-xs font-bold">Showing all registered vaccines and antigens</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-widest border border-brand-100">
                {{ $vaccines->count() }} Antigens
            </span>
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
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Vaccine Details</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Stock Level</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Storage & Safety</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Batches</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</th>
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
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $vaccine->manufacturer ?? 'No Manufacturer' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="space-y-1">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg {{ $vaccine->in_stock_quantity <= $vaccine->min_stock_level ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} text-xs font-black uppercase">
                                    {{ $vaccine->in_stock_quantity }} doses
                                </span>
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest ml-1">Min: {{ $vaccine->min_stock_level }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-gray-700"><i class="bi bi-thermometer-half text-brand-500 mr-1"></i> {{ $vaccine->storage_temp_range ?? 'Not set' }}</p>
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-gray-100 text-gray-500 tracking-widest">Active</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex -space-x-2">
                                @php $activeBatches = $vaccine->batches->where('quantity_remaining', '>', 0); @endphp
                                @foreach($activeBatches->take(3) as $batch)
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-600 shadow-sm" title="Batch {{ $batch->batch_number }}">
                                        {{ $loop->iteration }}
                                    </div>
                                @endforeach
                                @if($activeBatches->count() > 3)
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-brand-50 flex items-center justify-center text-[10px] font-black text-brand-600 shadow-sm">
                                        +{{ $activeBatches->count() - 3 }}
                                    </div>
                                @endif
                                @if($activeBatches->count() == 0)
                                    <span class="text-[10px] font-black text-red-400 uppercase tracking-widest">No Stock</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3 transition-opacity">
                                <a href="{{ route('admin.vaccines.edit', $vaccine->id) }}" 
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-brand-600 hover:border-brand-300 hover:bg-brand-50 transition-all shadow-sm group/btn"
                                   title="Edit Vaccine">
                                    <i class="bi bi-pencil-square group-hover/btn:scale-110 transition-transform"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Edit</span>
                                </a>
                                
                                <form action="{{ route('admin.vaccines.destroy', $vaccine->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this vaccine?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition-all shadow-sm group/btn"
                                            title="Delete Vaccine">
                                        <i class="bi bi-trash group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
            {{ $vaccines->links() }}
        </div>
    </div>

    <!-- Stock In Modal -->
    <div x-show="showBatchModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showBatchModal = false"></div>
            <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-brand-600 flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                        <i class="bi bi-box-seam text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900">RHU/MHO Stock In</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Receive new supply from higher office</p>
                    </div>
                </div>
                
                <form action="{{ route('admin.vaccines.stock-in') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Select Vaccine</label>
                        <select name="vaccine_id" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500 transition-all">
                            @foreach($vaccines as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Batch Number</label>
                            <input type="text" name="batch_number" required placeholder="LOT-123" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Expiry Date</label>
                            <input type="date" name="expiry_date" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500 transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Quantity (Doses)</label>
                            <input type="number" name="quantity_received" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Date Received</label>
                            <input type="date" name="received_at" value="{{ date('Y-m-d') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Supplier / Source Office</label>
                        <input type="text" name="supplier" placeholder="e.g. Municipal Health Office (MHO)" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-brand-500 transition-all">
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="showBatchModal = false" class="flex-1 py-4 bg-gray-100 rounded-2xl text-xs font-black uppercase text-gray-500 tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 py-4 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition-all tracking-widest">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection