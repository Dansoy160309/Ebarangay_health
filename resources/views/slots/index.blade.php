@extends('layouts.app')

@section('title', 'Manage Slots')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-calendar-event text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Operational Management</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Manage <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Appointment Slots</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Configure available <span class="text-gray-900 font-black">time windows</span> and monitor clinical availability for your health center.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('midwife.slots.create') }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-brand-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:scale-105 transition-all transform group">
                    <i class="bi bi-plus-lg mr-2 group-hover:rotate-90 transition-transform"></i>
                    Add New Slot
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 flex items-center gap-3 animate-fade-in-down">
            <i class="bi bi-check-circle-fill text-lg"></i>
            <div class="font-bold">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
        {{-- Today's Slots --}}
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-brand-200 transition-all duration-500">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-full blur-3xl -mr-16 -mt-16 opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i class="bi bi-calendar-check-fill text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Scheduled Today</p>
                    <h3 class="text-4xl font-black text-gray-900 group-hover:text-brand-600 transition-colors">{{ $stats['today_slots'] }}</h3>
                </div>
            </div>
        </div>
        
        {{-- Active Slots --}}
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-indigo-200 transition-all duration-500">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full blur-3xl -mr-16 -mt-16 opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i class="bi bi-lightning-charge-fill text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Active Window</p>
                    <h3 class="text-4xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $stats['active_slots'] }}</h3>
                </div>
            </div>
        </div>

        {{-- Total Capacity --}}
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-all duration-500">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full blur-3xl -mr-16 -mt-16 opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i class="bi bi-people-fill text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total Capacity</p>
                    <h3 class="text-4xl font-black text-gray-900 group-hover:text-emerald-600 transition-colors">{{ number_format($stats['total_capacity']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-[3rem] shadow-xl shadow-brand-500/5 border border-gray-100 p-8 relative z-10 overflow-hidden group/filter transition-all hover:border-brand-200">
        <form method="GET" action="{{ route('midwife.slots.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-8 items-end">
            {{-- Service Filter --}}
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Service Type</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within/input:text-brand-500 transition-colors">
                        <i class="bi bi-bandaid"></i>
                    </div>
                    <select name="service" class="block w-full pl-12 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all appearance-none cursor-pointer">
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->name }}" {{ request('service') === $service->name ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Date Filter --}}
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Scheduled Date</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within/input:text-brand-500 transition-colors">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <input type="date" name="date" value="{{ request('date') }}"
                           class="block w-full pl-12 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all cursor-pointer">
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Current Status</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within/input:text-brand-500 transition-colors">
                        <i class="bi bi-toggle-on"></i>
                    </div>
                    <select name="status" class="block w-full pl-12 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:bg-white transition-all appearance-none cursor-pointer">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            {{-- Quick Presets & Buttons --}}
            <div class="lg:col-span-2 flex items-center gap-4">
                <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-2xl border border-gray-100 flex-grow">
                    <button type="button" id="btnToday" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-500 hover:bg-white hover:text-brand-600 hover:shadow-sm transition-all flex-grow">Today</button>
                    <button type="button" id="btnActive" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-500 hover:bg-white hover:text-indigo-600 hover:shadow-sm transition-all flex-grow">Active</button>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="px-8 py-4 bg-brand-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="bi bi-funnel"></i>
                        Filter
                    </button>
                    <a href="{{ route('midwife.slots.index') }}" class="p-4 bg-gray-100 text-gray-500 rounded-2xl hover:bg-red-50 hover:text-red-600 transition-all border border-transparent hover:border-red-100" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise text-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Slots List Section --}}
    <div class="bg-white rounded-[3.5rem] shadow-soft border border-gray-100 overflow-hidden relative z-10">
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Service Provider</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Schedule Window</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Capacity Tracking</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Health Status</th>
                        <th class="px-10 py-8 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Management</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($slots as $slot)
                        <tr class="hover:bg-gray-50/80 transition-all duration-300 group/row {{ $slot->isExpired() ? 'opacity-60 grayscale-[0.3]' : '' }}">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-50 to-brand-100 text-brand-600 flex items-center justify-center text-xl shadow-inner group-hover/row:scale-110 transition-transform duration-500">
                                        <i class="bi bi-bandaid"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-black text-gray-900 group-hover/row:text-brand-600 transition-colors leading-tight mb-1.5 tracking-tight">{{ $slot->service }}</p>
                                        <div class="flex items-center gap-2">
                                            @if($slot->doctor)
                                                <span class="text-[9px] font-black text-brand-500 uppercase tracking-widest bg-brand-50 px-2 py-0.5 rounded-md border border-brand-100/50">
                                                    {{ $slot->doctor->isDoctor() ? 'Doctor' : 'Midwife' }}
                                                </span>
                                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $slot->doctor->full_name }}</span>
                                            @else
                                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest bg-gray-100 px-2 py-0.5 rounded-md">General</span>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Medical Team</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-calendar3 text-brand-500 text-sm"></i>
                                        <span class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $slot->date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 ml-6">
                                        <i class="bi bi-clock text-gray-400 text-[10px]"></i>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $slot->displayTime() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                @php
                                    $booked = $slot->bookedCount();
                                    $cap = max($slot->capacity, 0);
                                    $pct = $cap > 0 ? round(($booked / $cap) * 100) : 0;
                                @endphp
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest">{{ $booked }} / {{ $cap }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.2em]">Booked</span>
                                        </div>
                                        <span class="text-[10px] font-black {{ $slot->remainingSeats() > 0 ? 'text-emerald-600' : 'text-red-600' }} uppercase tracking-widest">
                                            {{ $slot->remainingSeats() }} Left
                                        </span>
                                    </div>
                                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden border border-gray-50">
                                        @php
                                            $barColor = $pct >= 100 ? 'from-red-500 to-red-400' : 'from-brand-500 to-brand-400';
                                        @endphp
                                        <div class="h-full bg-gradient-to-r {{ $barColor }} rounded-full transition-all duration-700 shadow-sm" style="width:<?php echo $pct; ?>%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                @php
                                    $statusColors = [
                                        'green' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'red' => 'bg-red-50 text-red-700 border-red-100',
                                        'gray' => 'bg-gray-50 text-gray-600 border-gray-100',
                                    ];
                                    $colorClass = $statusColors[$slot->status_color] ?? $statusColors['gray'];
                                @endphp
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl {{ $colorClass }} text-[10px] font-black uppercase tracking-widest border shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current {{ $slot->status_color === 'green' ? 'animate-pulse' : '' }}"></span>
                                    {{ $slot->status_label }}
                                </span>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <div class="flex items-center justify-end gap-3 opacity-40 group-hover/row:opacity-100 transition-opacity">
                                    <a href="{{ route('midwife.slots.edit', $slot->id) }}" 
                                       class="p-3 bg-yellow-50 text-yellow-600 rounded-xl hover:bg-yellow-600 hover:text-white transition-all border border-yellow-100 group/btn" title="Edit Slot">
                                        <i class="bi bi-pencil-square text-lg"></i>
                                    </a>
                                    <form action="{{ route('midwife.slots.destroy', $slot->id) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this slot?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-3 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all border border-red-100 group/btn" title="Delete Slot">
                                            <i class="bi bi-trash3 text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-10 py-24 text-center">
                                <div class="w-24 h-24 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-200 shadow-inner">
                                    <i class="bi bi-calendar-x text-5xl"></i>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 mb-2">No Appointment Slots</h3>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Try adjusting your filters or create a new slot window</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-50">
            @forelse($slots as $slot)
                <div class="p-8 space-y-6 {{ $slot->isExpired() ? 'opacity-60' : '' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shadow-inner">
                                <i class="bi bi-bandaid"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 leading-tight">{{ $slot->service }}</h4>
                                <p class="text-[9px] font-black text-brand-600 uppercase tracking-widest mt-1">
                                    {{ $slot->doctor ? ($slot->doctor->isDoctor() ? 'Dr. ' . $slot->doctor->last_name : 'Midwife ' . $slot->doctor->first_name) : 'Medical Team' }}
                                </p>
                            </div>
                        </div>
                        @php
                            $statusColors = [
                                'green' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'red' => 'bg-red-50 text-red-700 border-red-100',
                                'gray' => 'bg-gray-50 text-gray-600 border-gray-100',
                            ];
                            $colorClass = $statusColors[$slot->status_color] ?? $statusColors['gray'];
                        @endphp
                        <span class="px-3 py-1 rounded-full {{ $colorClass }} text-[8px] font-black uppercase tracking-widest border">
                            {{ $slot->status_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="bi bi-calendar3"></i> Date
                            </p>
                            <p class="text-xs font-black text-gray-900">{{ $slot->date->format('M d, Y') }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="bi bi-clock"></i> Time
                            </p>
                            <p class="text-xs font-black text-gray-900">{{ $slot->displayTime() }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @php
                            $booked = $slot->bookedCount();
                            $cap = max($slot->capacity, 0);
                            $pct = $cap > 0 ? round(($booked / $cap) * 100) : 0;
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black text-gray-900 uppercase tracking-widest">Capacity Tracker</span>
                            <span class="text-[9px] font-black {{ $slot->remainingSeats() > 0 ? 'text-emerald-600' : 'text-red-600' }} uppercase tracking-widest">
                                {{ $booked }}/{{ $cap }} ({{ $slot->remainingSeats() }} Left)
                            </span>
                        </div>
                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-600 rounded-full shadow-sm" style="width:<?php echo $pct; ?>%;"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="{{ route('midwife.slots.edit', $slot->id) }}" class="flex-1 py-3.5 bg-yellow-50 text-yellow-600 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center border border-yellow-100">
                            Edit Slot
                        </a>
                        <form action="{{ route('midwife.slots.destroy', $slot->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-3.5 bg-red-50 text-red-600 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center border border-red-100" onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                    No slots found
                </div>
            @endforelse
        </div>

        @if($slots->hasPages())
            <div class="px-10 py-8 border-t border-gray-50 bg-gray-50/30">
                {{ $slots->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Management Protocols / Legend --}}
    <div class="bg-gray-900 rounded-[3rem] p-10 lg:p-14 text-white relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500 rounded-full blur-[100px] -mr-32 -mt-32 opacity-20 group-hover:opacity-40 transition-opacity duration-700"></div>
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-2xl font-black mb-4 tracking-tight">Scheduling Protocols</h3>
                <p class="text-gray-400 font-medium leading-relaxed mb-8">
                    Slots are automatically deactivated once the date has passed. Ensure that slots are correctly assigned to either a Midwife or a Doctor based on the service requirement.
                </p>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-3 bg-white/5 px-5 py-3 rounded-2xl border border-white/10">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Active & Open</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/5 px-5 py-3 rounded-2xl border border-white/10">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Full / Expired</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <i class="bi bi-shield-check text-brand-400 text-2xl mb-4 block"></i>
                    <h4 class="text-sm font-black uppercase tracking-widest mb-2">Provider Type</h4>
                    <p class="text-[10px] text-gray-500 font-bold leading-relaxed">System enforces provider matching for clinical safety.</p>
                </div>
                <div class="p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <i class="bi bi-clock-history text-indigo-400 text-2xl mb-4 block"></i>
                    <h4 class="text-sm font-black uppercase tracking-widest mb-2">Auto-Archive</h4>
                    <p class="text-[10px] text-gray-500 font-bold leading-relaxed">Past slots are locked to maintain historical accuracy.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Small inline script for quick presets --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const todayBtn = document.getElementById('btnToday');
            const activeBtn = document.getElementById('btnActive');
            const dateInput = document.querySelector('input[name="date"]');
            const statusSelect = document.querySelector('select[name="status"]');

            if (todayBtn && dateInput) {
                todayBtn.addEventListener('click', () => {
                    const d = new Date();
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    dateInput.value = `${yyyy}-${mm}-${dd}`;
                });
            }
            if (activeBtn && statusSelect) {
                activeBtn.addEventListener('click', () => {
                    statusSelect.value = 'active';
                });
            }
        });
    </script>

    <style>
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 3s infinite ease-in-out;
        }
        .shadow-soft {
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.05);
        }
    </style>
</div>
@endsection
