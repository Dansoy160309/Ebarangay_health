@extends('layouts.app')

@section('title', 'Update Vaccine Details')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <div class="mb-10 flex items-center gap-6">
        <a href="{{ route('admin.vaccines.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-brand-600 hover:border-brand-100 transition-all shadow-sm group">
            <i class="bi bi-arrow-left text-xl group-hover:-translate-x-1 transition-transform"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Update Vaccine</h1>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">Modify details for {{ $vaccine->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-brand-50/50 to-transparent -z-0"></div>
        
        <form action="{{ route('admin.vaccines.update', $vaccine->id) }}" method="POST" class="p-10 relative z-10 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Vaccine Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $vaccine->name) }}"
                           class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-base font-black text-gray-900 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300">
                    @error('name') <p class="text-xs font-bold text-red-500 mt-2 ml-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Manufacturer</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer', $vaccine->manufacturer) }}"
                           class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-base font-black text-gray-900 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Storage Temperature Range</label>
                    <input type="text" name="storage_temp_range" value="{{ old('storage_temp_range', $vaccine->storage_temp_range) }}"
                           class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-base font-black text-gray-900 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Minimum Stock Level <span class="text-red-500">*</span></label>
                    <input type="number" name="min_stock_level" required value="{{ old('min_stock_level', $vaccine->min_stock_level) }}"
                           class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-base font-black text-gray-900 focus:ring-4 focus:ring-brand-500/10 transition-all">
                    <p class="text-[10px] text-gray-400 font-bold mt-2 ml-2 uppercase tracking-widest">Alerts when stock falls below this</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 mb-2">Description / Usage Notes</label>
                    <textarea name="description" rows="3" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-base font-black text-gray-900 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300">{{ old('description', $vaccine->description) }}</textarea>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('admin.vaccines.index') }}" class="flex-1 py-5 bg-gray-100 rounded-2xl text-xs font-black uppercase tracking-[0.2em] text-gray-500 text-center hover:bg-gray-200 transition-all">
                    Cancel
                </a>
                <button type="submit" class="flex-1 py-5 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all active:scale-95">
                    Update Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection