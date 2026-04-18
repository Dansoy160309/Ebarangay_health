@extends('layouts.app')

@section('title', 'Appointment Slots')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Header Section --}}
    <div class="bg-gradient-to-br from-brand-700 via-brand-600 to-brand-500 rounded-[2rem] p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute right-10 top-10 opacity-10">
            <i class="bi bi-calendar-range text-[15rem] leading-none"></i>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-10">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 shadow-inner">
                        <i class="bi bi-calendar2-week-fill text-2xl"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-[0.2em] opacity-80 text-white/90">Clinical Scheduling</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black mb-3 tracking-tight leading-tight">
                    Appointment Slots
                </h1>
                <p class="text-brand-50 text-sm font-medium leading-relaxed opacity-90">
                    Monitor availability and capacity at a glance.
                </p>
            </div>

            <!-- Header Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 w-full lg:w-auto shrink-0">
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-3xl border border-white/20 text-center shadow-lg group hover:bg-white/20 transition-all duration-300">
                    <p class="text-[9px] font-black uppercase tracking-widest opacity-70 mb-2">Today's Slots</p>
                    <p class="text-2xl font-black tracking-tighter">{{ $stats['today_slots'] }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-3xl border border-white/20 text-center shadow-lg group hover:bg-white/20 transition-all duration-300">
                    <p class="text-[9px] font-black uppercase tracking-widest opacity-70 mb-2">Active</p>
                    <p class="text-2xl font-black tracking-tighter text-green-300">{{ $stats['active_slots'] }}</p>
                </div>
                <div class="hidden sm:block bg-white/10 backdrop-blur-md p-4 rounded-3xl border border-white/20 text-center shadow-lg group hover:bg-white/20 transition-all duration-300">
                    <p class="text-[9px] font-black uppercase tracking-widest opacity-70 mb-2">Total Capacity</p>
                    <p class="text-2xl font-black tracking-tighter text-blue-300">{{ $stats['total_capacity'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-[2rem] shadow-lg shadow-gray-200/50 border border-gray-100 p-6">
        <form method="GET" action="{{ route('healthworker.slots.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            {{-- Service Filter --}}
            <div class="space-y-2">
                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Medical Service</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-search"></i>
                    </div>
                    <input 
                        type="text" 
                        name="service" 
                        value="{{ request('service') }}" 
                        placeholder="Search service..." 
                        class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition-all"
                    >
                </div>
            </div>

            {{-- Date Filter --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Scheduled Date</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ request('date') }}" 
                        class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition-all"
                    >
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Visibility Status</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <i class="bi bi-toggle-on"></i>
                    </div>
                    <select name="status" class="block w-full pl-11 pr-10 py-3.5 bg-gray-50 border-gray-100 rounded-2xl text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>
            </div>

            {{-- Filter Actions --}}
            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 bg-brand-600 text-white py-3.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-lg shadow-brand-600/20 active:scale-[0.98] flex items-center justify-center gap-2">
                    <i class="bi bi-funnel"></i> Apply Filter
                </button>
                <a href="{{ route('healthworker.slots.index') }}" class="p-3.5 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 transition-all shadow-sm" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise text-lg"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Slots List --}}
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-3 py-2 text-left text-[8px] font-black text-gray-400 uppercase tracking-[0.18em]">Service</th>
                        <th class="px-3 py-2 text-left text-[8px] font-black text-gray-400 uppercase tracking-[0.18em]">Schedule</th>
                        <th class="px-3 py-2 text-left text-[8px] font-black text-gray-400 uppercase tracking-[0.18em]">Capacity</th>
                        <th class="px-3 py-2 text-left text-[8px] font-black text-gray-400 uppercase tracking-[0.18em]">Status</th>
                        <th class="px-3 py-2 text-right text-[8px] font-black text-gray-400 uppercase tracking-[0.18em]">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse ($slots as $slot)
                        <tr class="hover:bg-gray-50/80 transition-all group">
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-base border border-brand-100 group-hover:scale-105 transition-transform">
                                        <i class="bi bi-bandaid-fill"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 leading-tight mb-0.5">{{ $slot->service }}</div>
                                        <div class="text-[9px] font-bold text-brand-500 uppercase tracking-[0.18em] flex items-center gap-1">
                                            @if($slot->doctor)
                                                Dr. {{ $slot->doctor->full_name }}
                                            @else
                                                Healthcare Provider / Any
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <div class="text-sm font-black text-gray-900">{{ \Carbon\Carbon::parse($slot->date)->format('M d, Y') }}</div>
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-[0.18em] flex items-center gap-1">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                        @if($slot->end_time)
                                            - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $booked = $slot->appointments_count ?? 0;
                                    $percent = $slot->capacity > 0 ? ($booked / $slot->capacity) * 100 : 0;
                                    $barColor = $percent >= 100 ? 'bg-red-500' : ($percent >= 80 ? 'bg-orange-500' : 'bg-brand-500');
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <div class="text-sm font-black text-gray-900">{{ $booked }} / {{ $slot->capacity }}</div>
                                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full transition-all duration-500" style="width: {{ min($percent, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                @if($slot->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-[0.16em] bg-green-50 text-green-600 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2 bg-green-500"></span>
                                        Available
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-[0.16em] bg-gray-50 text-gray-400 border border-gray-100">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2 bg-gray-300"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button 
                                    onclick="openSlotModal(this)"
                                    data-service="{{ $slot->service }}"
                                    data-doctor="{{ $slot->doctor ? 'Dr. ' . $slot->doctor->full_name : 'Healthcare Provider / Any' }}"
                                    data-date="{{ \Carbon\Carbon::parse($slot->date)->format('F j, Y') }}"
                                    data-start-time="{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}"
                                    data-end-time="{{ $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('h:i A') : 'N/A' }}"
                                    data-capacity="{{ $slot->capacity }}"
                                    data-booked="{{ $booked }}"
                                    data-status="{{ $slot->is_active ? 'Active' : 'Inactive' }}"
                                    class="inline-flex items-center justify-center bg-gray-50 text-gray-500 p-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.18em] hover:bg-brand-600 hover:text-white transition-all shadow-sm active:scale-[0.98]">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="max-w-xs mx-auto">
                                    <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 text-gray-200 border-4 border-white shadow-inner">
                                        <i class="bi bi-calendar-x text-5xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-black text-gray-900 mb-2">No Slots Found</h3>
                                    <p class="text-sm font-medium text-gray-500 mb-8">There are no clinical slots available matching your criteria.</p>
                                    @if(request()->anyFilled(['service', 'date', 'status']))
                                        <a href="{{ route('healthworker.slots.index') }}" class="inline-flex items-center px-8 py-4 bg-brand-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-700 shadow-xl shadow-brand-600/20 transition-all">
                                            Reset Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($slots->hasPages())
            <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50 flex justify-center">
                {{ $slots->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modern Clinical Modal --}}
<div id="slotModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-md flex items-center justify-center z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalContent">
        {{-- Modal Header --}}
        <div class="bg-gradient-to-br from-brand-600 to-brand-500 p-6 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-white/10 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 15px 15px;"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30">
                        <i class="bi bi-info-circle-fill text-lg"></i>
                    </div>
                    <button onclick="closeSlotModal()" class="w-9 h-9 rounded-full bg-black/10 flex items-center justify-center hover:bg-black/20 transition-colors">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <h2 class="text-2xl font-black tracking-tight mb-1">Clinical Slot Details</h2>
                <p class="text-brand-100 text-xs font-medium opacity-80 uppercase tracking-wider">Medical Scheduling Information</p>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 space-y-4">
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner">
                <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl border border-white shadow-sm">
                    <i class="bi bi-bandaid-fill"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.18em] mb-1">Assigned Service</p>
                    <p id="modalService" class="text-lg font-black text-gray-900 leading-tight"></p>
                    <p id="modalDoctor" class="text-xs font-bold text-brand-500 mt-1"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                        <i class="bi bi-calendar3 text-brand-500"></i> Date
                    </p>
                    <p id="modalDate" class="text-sm font-black text-gray-900"></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                        <i class="bi bi-people-fill text-brand-500"></i> Capacity
                    </p>
                    <p class="text-sm font-black text-gray-900"><span id="modalBooked"></span> / <span id="modalCapacity"></span> <span class="text-xs text-gray-400 font-bold ml-1">Patients</span></p>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                    <i class="bi bi-clock-fill text-brand-500"></i> Consultation Window
                </p>
                <p id="modalTime" class="text-sm font-black text-gray-900"></p>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
                    <p id="modalStatus" class="text-sm font-black text-gray-900"></p>
                </div>
                <div id="modalStatusBadge"></div>
            </div>
        </div>

        <div class="p-5 bg-gray-50/50 border-t border-gray-100">
            <button onclick="closeSlotModal()" class="w-full py-3 bg-brand-600 text-white rounded-xl text-[11px] font-black uppercase tracking-wider hover:bg-brand-700 transition-all shadow-xl shadow-brand-600/20 active:scale-[0.98]">
                Close Clinical View
            </button>
        </div>
    </div>
</div>

{{-- Modal Script --}}
<script>
    function openSlotModal(button) {
        const modal = document.getElementById('slotModal');
        const content = document.getElementById('modalContent');
        
        const service = button.dataset.service;
        const doctor = button.dataset.doctor;
        const date = button.dataset.date;
        const startTime = button.dataset.startTime;
        const endTime = button.dataset.endTime;
        const capacity = button.dataset.capacity;
        const booked = button.dataset.booked;
        const status = button.dataset.status;

        document.getElementById('modalService').textContent = service;
        document.getElementById('modalDoctor').textContent = doctor;
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalTime').textContent = `${startTime} - ${endTime}`;
        document.getElementById('modalCapacity').textContent = capacity;
        document.getElementById('modalBooked').textContent = booked;
        document.getElementById('modalStatus').textContent = status;
        
        const badgeContainer = document.getElementById('modalStatusBadge');
        if (status === 'Active') {
            badgeContainer.innerHTML = '<span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-600 border border-green-200">Available</span>';
        } else {
            badgeContainer.innerHTML = '<span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-gray-100 text-gray-400 border border-gray-200">Inactive</span>';
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeSlotModal() {
        const modal = document.getElementById('slotModal');
        const content = document.getElementById('modalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Close on backdrop click
    document.getElementById('slotModal').addEventListener('click', function(e) {
        if (e.target === this) closeSlotModal();
    });
</script>

<style>
    /* Custom Scrollbar for the table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>
@endsection
