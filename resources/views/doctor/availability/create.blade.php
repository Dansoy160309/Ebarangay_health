@extends('layouts.app')

@section('title', 'Add Duty Block')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-calendar-plus text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Duty Management</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Add <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Duty Block</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Define a time window when you will be present at the health center.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('doctor.availability.index') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-white border border-gray-200 text-gray-700 font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Back to Schedule
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-3xl" x-data="{ isRecurring: {{ old('is_recurring') ? 'true' : 'false' }} }">
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 sm:p-12 relative overflow-hidden">
            <form action="{{ route('doctor.availability.store') }}" method="POST" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Date --}}
                    <div class="space-y-2">
                        <label for="date" class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Duty Date</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <input type="date" name="date" id="date" required min="{{ date('Y-m-d') }}"
                                   class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-50/10 focus:border-brand-500 text-sm font-medium transition-all @error('date') border-red-500 @enderror"
                                   value="{{ old('date', date('Y-m-d')) }}">
                        </div>
                        @error('date') <p class="text-xs text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Recurring Option --}}
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Type</label>
                        <div class="flex flex-col gap-4">
                            <label class="relative flex items-center cursor-pointer group">
                                <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600 shadow-inner"></div>
                                <span class="ml-3 text-sm font-bold text-gray-700">Make Recurring</span>
                            </label>

                            {{-- Recurring Day Dropdown --}}
                            <div x-show="isRecurring" x-transition class="relative group mt-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                    <i class="bi bi-arrow-repeat"></i>
                                </div>
                                <select name="recurring_day" 
                                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-50/10 focus:border-brand-500 text-sm font-medium transition-all appearance-none @error('recurring_day') border-red-500 @enderror">
                                    <option value="">-- Repeat Every --</option>
                                    <option value="1" {{ old('recurring_day') == 1 ? 'selected' : '' }}>Monday</option>
                                    <option value="2" {{ old('recurring_day') == 2 ? 'selected' : '' }}>Tuesday</option>
                                    <option value="3" {{ old('recurring_day') == 3 ? 'selected' : '' }}>Wednesday</option>
                                    <option value="4" {{ old('recurring_day') == 4 ? 'selected' : '' }}>Thursday</option>
                                    <option value="5" {{ old('recurring_day') == 5 ? 'selected' : '' }}>Friday</option>
                                    <option value="6" {{ old('recurring_day') == 6 ? 'selected' : '' }}>Saturday</option>
                                    <option value="0" {{ old('recurring_day') == 0 ? 'selected' : '' }}>Sunday</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                    <i class="bi bi-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Start Time --}}
                    <div class="space-y-2">
                        <label for="start_time" class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Start Time</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-clock"></i>
                            </div>
                            <input type="time" name="start_time" id="start_time" required
                                   class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-medium transition-all @error('start_time') border-red-500 @enderror"
                                   value="{{ old('start_time', '08:00') }}">
                        </div>
                        @error('start_time') <p class="text-xs text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- End Time --}}
                    <div class="space-y-2">
                        <label for="end_time" class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">End Time</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <input type="time" name="end_time" id="end_time" required
                                   class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-medium transition-all @error('end_time') border-red-500 @enderror"
                                   value="{{ old('end_time', '12:00') }}">
                        </div>
                        @error('end_time') <p class="text-xs text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div class="space-y-2">
                    <label for="notes" class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Duty Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 text-sm font-medium transition-all placeholder:text-gray-400"
                              placeholder="e.g. Bringing specialized equipment for maternal checkups...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-brand-600 text-white text-sm font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:scale-[1.02] transition-all transform active:scale-95">
                        <i class="bi bi-check-circle mr-2"></i>
                        Confirm Duty Block
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
