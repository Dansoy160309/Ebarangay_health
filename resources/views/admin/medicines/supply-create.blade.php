@extends('layouts.app')

@section('title', 'Add Medicine Supply')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
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
                    <a href="{{ route('admin.medicines.supplies') }}" class="hover:text-brand-600 transition-colors">Supply History</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400 mx-2 text-[10px]"></i>
                    <span class="text-gray-900 font-bold">Add Supply</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-10">
        <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">New Supply Entry</h1>
        <p class="text-gray-500 font-medium">Add a new record for medicine replenishment and stock updates.</p>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 md:p-12 overflow-hidden relative">
        {{-- Decorative background --}}
        <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-brand-50/50 -z-0"></div>

        @if(session('success'))
            <div class="mb-8 rounded-3xl border border-green-100 bg-green-50/50 p-4 text-sm text-green-800 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 rounded-3xl border border-red-100 bg-red-50/50 p-6 text-sm text-red-800 animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="font-black uppercase tracking-widest text-[11px]">Please correct the following:</div>
                </div>
                <ul class="list-disc list-inside space-y-1 font-medium ml-11">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.medicines.supplies.store') }}" class="space-y-10 relative z-10">
            @csrf

            {{-- Medicine Selection Section --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-8 w-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-sm">
                        <i class="bi bi-capsule-pill"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Select Medicine</h3>
                </div>
                
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-search"></i>
                    </div>
                    <select name="medicine_id" class="w-full pl-12 pr-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all appearance-none shadow-sm">
                        <option value="">Search for a medicine...</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" @selected(old('medicine_id') == $medicine->id)>
                                {{ $medicine->generic_name }} {{ $medicine->brand_name ? '('.$medicine->brand_name.')' : '' }}
                                (Current Stock: {{ number_format($medicine->stock) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Batch & Quantity Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-8 w-8 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center text-sm">
                            <i class="bi bi-hash"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Batch Number</h3>
                    </div>
                    <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                           placeholder="e.g. BATCH-2024-001"
                           class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm placeholder:text-gray-400">
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-8 w-8 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center text-sm">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Quantity</h3>
                    </div>
                    <div class="relative group">
                        <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                               class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-black transition-all shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Units
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expiration & Supplier Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-8 w-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Expiration Date</h3>
                    </div>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date') }}"
                           class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-8 w-8 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center text-sm">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Supplier Name</h3>
                    </div>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}"
                           placeholder="Enter supplier name..."
                           class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm placeholder:text-gray-400">
                </div>
            </div>

            {{-- Date Received Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-8 w-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Date Received</h3>
                    </div>
                    <input type="date" name="date_received" value="{{ old('date_received', now()->toDateString()) }}"
                           class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-bold transition-all shadow-sm">
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-6">
                <a href="{{ route('admin.medicines.supplies') }}" class="px-8 py-4 rounded-2xl text-sm font-black text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-4 rounded-2xl bg-gray-900 text-white text-sm font-black hover:bg-black transition-all active:scale-95 shadow-xl shadow-gray-200">
                    Save Supply Entry
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

