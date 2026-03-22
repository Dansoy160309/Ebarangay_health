@extends('layouts.app')

@section('title', 'My Duty Schedule')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-calendar-check text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Duty Management</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    My <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Duty Schedule</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Set your availability blocks so midwives can coordinate patient appointments effectively.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('doctor.availability.create') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-brand-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:scale-105 transition-all transform group">
                    <i class="bi bi-plus-lg mr-2 group-hover:rotate-90 transition-transform"></i>
                    Add Duty Block
                </a>
            </div>
        </div>
    </div>

    {{-- Duty Blocks Table --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Date</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Time Window</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Type</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Notes</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($availabilities as $block)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ $block->date->format('M d, Y') }}
                                </div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase mt-0.5 tracking-tight">
                                    {{ $block->date->format('l') }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-black">
                                        {{ \Carbon\Carbon::parse($block->start_time)->format('h:i A') }}
                                    </span>
                                    <span class="text-gray-300">→</span>
                                    <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-black">
                                        {{ \Carbon\Carbon::parse($block->end_time)->format('h:i A') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($block->is_recurring)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-widest border border-purple-100">
                                        <i class="bi bi-repeat"></i> Recurring
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest border border-blue-100">
                                        <i class="bi bi-calendar-event"></i> One-time
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $statusClasses = [
                                        'scheduled' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        'arrived' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'delayed' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'absent' => 'bg-red-50 text-red-600 border-red-100',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full {{ $statusClasses[$block->status] }} text-[10px] font-black uppercase tracking-widest border">
                                    {{ $block->status }}
                                </span>
                                @if($block->actual_arrival_time)
                                    <div class="text-[9px] font-bold text-gray-400 mt-1 uppercase">
                                        At {{ $block->actual_arrival_time->format('h:i A') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm text-gray-500 line-clamp-1 max-w-[200px]">{{ $block->notes ?? '—' }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <form action="{{ route('doctor.availability.destroy', $block) }}" method="POST" onsubmit="return confirm('Remove this duty block?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-300 mb-4">
                                        <i class="bi bi-calendar-x text-4xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">No duty blocks found</h3>
                                    <p class="text-gray-500 max-w-xs mx-auto">Start by adding your availability blocks so midwives can schedule appointments.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($availabilities->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $availabilities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
