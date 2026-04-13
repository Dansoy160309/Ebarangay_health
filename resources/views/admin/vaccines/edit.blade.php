@extends('layouts.app')

@section('title', 'Update Vaccine Details')

<style>
.vaccine-form input,
.vaccine-form select,
.vaccine-form textarea {
    background-color: #ffffff !important;
    color: #111827 !important;
    color-scheme: light;
}

.vaccine-form input:-webkit-autofill,
.vaccine-form input:-webkit-autofill:hover,
.vaccine-form input:-webkit-autofill:focus,
.vaccine-form select:-webkit-autofill,
.vaccine-form textarea:-webkit-autofill {
    -webkit-text-fill-color: #111827;
    -webkit-box-shadow: 0 0 0px 1000px #ffffff inset;
    transition: background-color 5000s ease-in-out 0s;
}
</style>

@section('content')
<div class="w-full max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.vaccines.index') }}" class="w-11 h-11 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-brand-600 hover:border-brand-100 transition-all shadow-sm group shrink-0">
                <i class="bi bi-arrow-left text-lg group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Update Vaccine Details</h1>
                <p class="text-sm text-gray-500 font-medium max-w-2xl">Modify vaccine details and keep your baseline stock settings aligned with current inventory policy.</p>
            </div>
        </div>
        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Full-width workspace</div>
    </div>

    <div class="bg-white rounded-[2.25rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-5 md:p-6 overflow-hidden relative">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-brand-50/50 -z-0"></div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50/50 p-5 text-sm text-red-800 animate-in fade-in slide-in-from-top-4 duration-300">
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

        <form action="{{ route('admin.vaccines.update', $vaccine->id) }}" method="POST" class="vaccine-form relative z-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                <div class="xl:col-span-2 space-y-6">
                    <div class="space-y-5 rounded-[2rem] border border-gray-100 bg-white p-5 md:p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </div>
                            <h2 class="text-lg font-black text-gray-900">Vaccine Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2 space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Vaccine Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required value="{{ old('name', $vaccine->name) }}"
                                       class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all placeholder:text-gray-300">
                                @error('name') <p class="text-xs font-bold text-red-500 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Manufacturer</label>
                                <input type="text" name="manufacturer" value="{{ old('manufacturer', $vaccine->manufacturer) }}"
                                       class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300">
                            </div>

                            <div class="space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Storage Temperature Range</label>
                                <input type="text" name="storage_temp_range" value="{{ old('storage_temp_range', $vaccine->storage_temp_range) }}"
                                       class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300">
                            </div>

                            <div class="space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Minimum Stock Level <span class="text-red-500">*</span></label>
                                <input type="number" name="min_stock_level" required value="{{ old('min_stock_level', $vaccine->min_stock_level) }}"
                                       class="w-full px-5 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-brand-500/10 transition-all">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest ml-1">Alerts when stock falls below this</p>
                            </div>

                            <div class="md:col-span-2 space-y-2.5">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Description / Usage Notes</label>
                                <textarea name="description" rows="4" class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-300">{{ old('description', $vaccine->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 xl:sticky xl:top-24">
                    <div class="rounded-[1.75rem] border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-9 w-9 rounded-xl bg-white text-brand-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-info-circle text-lg"></i>
                            </div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900">Quick Notes</h3>
                        </div>
                        <div class="space-y-3 text-sm text-gray-600 font-medium leading-6">
                            <p>Change vaccine name carefully because it is used in inventory and administration records.</p>
                            <p>Use stock-in entries for expiry-aware doses and lot tracking from RHU/MHO.</p>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] border border-gray-100 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-9 w-9 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-box-seam text-lg"></i>
                            </div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900">Batch Guidance</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-600 font-medium">
                            <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500 shrink-0"></span><span>Use RHU/MHO Stock In to add lot numbers and expiry dates.</span></li>
                            <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500 shrink-0"></span><span>Track dose updates through batch entries to keep inventory auditable.</span></li>
                            <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500 shrink-0"></span><span>After update, verify stock status on the vaccine list.</span></li>
                        </ul>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-end pt-1">
                        <a href="{{ route('admin.vaccines.index') }}" class="px-5 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                            Cancel
                        </a>
                        <button type="submit" class="px-7 py-3 rounded-xl bg-brand-600 text-white text-xs font-black hover:bg-brand-700 transition-all active:scale-95 shadow-md shadow-brand-200">
                            Update Details
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection