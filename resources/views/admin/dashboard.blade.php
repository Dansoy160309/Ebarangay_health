@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">

    {{-- Welcome Section --}}
    <div class="mb-8 bg-gradient-to-r from-brand-700 to-brand-600 rounded-[2rem] p-6 sm:p-10 text-white shadow-lg shadow-brand-500/20 relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl sm:text-4xl font-bold mb-3 tracking-tight">
                Welcome back, {{ auth()->user()->first_name }}! 👋
            </h1>
            <p class="text-brand-100 text-lg sm:text-xl font-light leading-relaxed">
                Here's what's happening in your barangay health center today.
            </p>
            
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 text-sm font-medium text-white bg-white/20 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/10">
                    <i class="bi bi-calendar2-day"></i>
                    <span>{{ now()->format('l, F j, Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute right-0 top-0 h-full w-full sm:w-1/2 pointer-events-none">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 opacity-10 transform translate-x-1/4">
                <i class="bi bi-activity text-[20rem]"></i>
            </div>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        
        {{-- Total Residents --}}
        <a href="{{ route('admin.users.index', ['role' => 'patient']) }}" class="bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-md transition block relative overflow-hidden group">
            <div class="flex flex-col h-full justify-between relative z-10">
                <div class="flex justify-between items-start mb-4">
                     <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl shadow-sm">
                         <i class="bi bi-people-fill"></i>
                     </div>
                </div>
                <div>
                     <h3 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">{{ $totalResidents }}</h3>
                     <p class="text-sm font-bold text-gray-400 mt-1">Total Patients</p>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 text-blue-50 opacity-50 group-hover:scale-110 transition duration-500">
                <i class="bi bi-people-fill text-9xl"></i>
            </div>
        </a>

        {{-- Scheduled Appointments --}}
        <a href="{{ route('admin.appointments.index', ['status' => 'approved']) }}" class="bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-md transition block relative overflow-hidden group">
             <div class="flex flex-col h-full justify-between relative z-10">
                <div class="flex justify-between items-start mb-4">
                     <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-500 flex items-center justify-center text-2xl shadow-sm">
                         <i class="bi bi-calendar-check-fill"></i>
                     </div>
                </div>
                <div>
                     <h3 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">{{ $approvedAppointments }}</h3>
                     <p class="text-sm font-bold text-gray-400 mt-1">Upcoming</p>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 text-green-50 opacity-50 group-hover:scale-110 transition duration-500">
                <i class="bi bi-calendar-check-fill text-9xl"></i>
            </div>
        </a>

        {{-- Medicine Inventory --}}
        <a href="{{ route('admin.medicines.index') }}" class="bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-md transition block relative overflow-hidden group">
             <div class="flex flex-col h-full justify-between relative z-10">
                <div class="flex justify-between items-start mb-4">
                     <div class="w-12 h-12 rounded-2xl {{ $medicineStats['low_stock_count'] > 0 ? 'bg-red-50 text-red-500' : 'bg-purple-50 text-purple-500' }} flex items-center justify-center text-2xl shadow-sm">
                         <i class="bi bi-capsule"></i>
                     </div>
                </div>
                <div>
                     <h3 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">{{ $medicineStats['total_stock'] }}</h3>
                     <p class="text-sm font-bold text-gray-400 mt-1">Medicine Stock</p>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 text-purple-50 opacity-50 group-hover:scale-110 transition duration-500">
                <i class="bi bi-capsule text-9xl"></i>
            </div>
        </a>

        {{-- Pending Requests --}}
        <a href="{{ route('admin.appointments.index', ['status' => 'pending']) }}" class="bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-md transition block relative overflow-hidden group">
             <div class="flex flex-col h-full justify-between relative z-10">
                <div class="flex justify-between items-start mb-4">
                     <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-500 flex items-center justify-center text-2xl shadow-sm">
                         <i class="bi bi-clock-history"></i>
                     </div>
                </div>
                <div>
                     <h3 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">{{ $pendingAppointments }}</h3>
                     <p class="text-sm font-bold text-gray-400 mt-1">Pending Requests</p>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 text-yellow-50 opacity-50 group-hover:scale-110 transition duration-500">
                <i class="bi bi-clock-history text-9xl"></i>
            </div>
        </a>
    </div>

    {{-- Analytics & Calendar Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Health Analytics (Left 2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- DOH KPI Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Maternal Health KPI --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-20 h-20 -mr-8 -mt-8 bg-purple-50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i class="bi bi-gender-female"></i> Maternal Health
                    </p>
                    <h4 class="text-2xl font-black text-gray-900 mb-1">{{ $maternalStats['high_risk_count'] }}</h4>
                    <p class="text-[10px] font-bold text-red-500 uppercase tracking-tight">High Risk Cases</p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <span class="text-[9px] font-bold text-gray-400">Pregnancies: {{ $maternalStats['active_pregnancies'] }}</span>
                    </div>
                </div>

                {{-- Child Immunization KPI --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-20 h-20 -mr-8 -mt-8 bg-blue-50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i class="bi bi-shield-check"></i> Immunization
                    </p>
                    <h4 class="text-2xl font-black text-gray-900 mb-1">{{ $immunizationStats['fully_immunized'] }}</h4>
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-tight">Fully Immunized (FIC)</p>
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <span class="text-[9px] font-bold text-gray-400">Coverage: {{ $immunizationStats['total_children'] > 0 ? round(($immunizationStats['fully_immunized'] / $immunizationStats['total_children']) * 100) : 0 }}%</span>
                    </div>
                </div>

                {{-- Morbidity Tracking KPI --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-20 h-20 -mr-8 -mt-8 bg-red-50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i class="bi bi-virus"></i> Top Morbidity
                    </p>
                    <h4 class="text-2xl font-black text-gray-900 mb-1">{{ $morbidityStats->first()->case_count ?? 0 }}</h4>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-tight">{{ $morbidityStats->first()->condition_name ?? 'No cases' }}</p>
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <span class="text-[9px] font-bold text-gray-400">Total Records: {{ $morbidityStats->sum('case_count') }}</span>
                    </div>
                </div>
            </div>

            {{-- Charts Grid --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shadow-inner">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        Health Trends
                    </h3>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-widest rounded-full">DOH-Aligned</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="h-64">
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Patient Demographics</p>
                        <canvas id="genderChart"></canvas>
                    </div>
                    <div class="h-64">
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Service Utilization</p>
                        <canvas id="serviceChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Medicine Reports --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                        </div>
                        Medicine & Inventory
                    </h3>
                    <a href="{{ route('admin.medicines.index') }}" class="text-xs font-black text-brand-600 uppercase tracking-widest hover:underline">Full Inventory</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="col-span-1 space-y-4">
                        <div class="p-4 rounded-3xl bg-red-50 border border-red-100">
                            <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">Expiring Soon</p>
                            <p class="text-2xl font-black text-red-700">{{ $medicineStats['expiring_soon']->count() }}</p>
                        </div>
                        <div class="p-4 rounded-3xl bg-amber-50 border border-amber-100">
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Low Stock Items</p>
                            <p class="text-2xl font-black text-amber-700">{{ $medicineStats['low_stock_count'] }}</p>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Fast-Moving Medicines</p>
                        <div class="space-y-3">
                            @foreach($medicineStats['fast_moving'] as $fast)
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50/50 border border-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-brand-500 text-xs">
                                        <i class="bi bi-capsule"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-900">{{ $fast->medicine->generic_name }}</span>
                                </div>
                                <span class="text-[10px] font-black text-brand-600 uppercase">{{ $fast->total_quantity }} units</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Health Advisories (Announcements) --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        Health Advisories
                    </h3>
                    <a href="{{ route('admin.announcements.index') }}" class="text-xs font-black text-brand-600 uppercase tracking-widest hover:underline">Manage</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($healthAdvisories as $advisory)
                    <div class="p-6 rounded-[2rem] bg-gray-50 border border-gray-100 hover:border-amber-200 transition-all duration-300 group">
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">{{ $advisory->created_at->format('M d, Y') }}</p>
                        <h4 class="font-black text-gray-900 mb-2 line-clamp-1">{{ $advisory->title }}</h4>
                        <p class="text-xs text-gray-600 line-clamp-2 mb-4">{{ $advisory->message }}</p>
                        <a href="{{ route('admin.announcements.show', $advisory) }}" class="text-[10px] font-black text-brand-600 uppercase tracking-widest flex items-center gap-1 group-hover:gap-2 transition-all">
                            Read More <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    @empty
                    <div class="col-span-3 text-center py-8 text-gray-400 italic">No active health advisories.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar (Right 1/3) --}}
        <div class="space-y-6">
            
            {{-- Today's Agenda (Replaces Old Calendar) --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900 flex items-center gap-3">
                        <i class="bi bi-calendar-check-fill text-brand-600"></i>
                        Today's Agenda
                    </h3>
                    <span class="px-3 py-1 bg-brand-100 text-brand-700 text-[10px] font-black rounded-lg uppercase tracking-wider">
                        {{ now()->format('M d') }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @php
                            $todayAppts = $upcomingAppointments->filter(fn($a) => $a->scheduled_at && $a->scheduled_at->isToday())->sortBy('scheduled_at');
                        @endphp

                        @forelse($todayAppts as $appt)
                        <div class="flex gap-4 p-4 rounded-2xl bg-gray-50/50 border border-gray-50 hover:border-brand-100 transition-all group">
                            <div class="flex flex-col items-center justify-center border-r border-gray-200 pr-4 min-w-[60px]">
                                <span class="text-xs font-black text-gray-900">{{ $appt->scheduled_at->format('h:i') }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $appt->scheduled_at->format('A') }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-black text-gray-900 line-clamp-1 group-hover:text-brand-600 transition-colors">{{ $appt->display_patient_name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $appt->service }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-12 text-center">
                            <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center text-gray-300 text-2xl mx-auto mb-4">
                                <i class="bi bi-cup-hot"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-400">No appointments for today.</p>
                            <a href="{{ route('admin.reports.calendar') }}" class="text-[10px] font-black text-brand-600 uppercase tracking-widest mt-2 block hover:underline">View Full Calendar</a>
                        </div>
                        @endforelse
                    </div>

                    @if($todayAppts->count() > 0)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.reports.calendar') }}" class="w-full py-3 rounded-xl bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-brand-600 transition-all shadow-lg shadow-gray-200">
                            <i class="bi bi-calendar3"></i>
                            View Full Schedule
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Recent Notifications --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 overflow-hidden">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-3 mb-8">
                    <i class="bi bi-bell-fill text-brand-600"></i>
                    Recent Alerts
                </h3>
                <div class="space-y-6">
                    @forelse($notifications as $notification)
                    <div class="flex gap-4 relative group">
                        <div class="w-2 h-2 rounded-full bg-brand-500 mt-1.5 shrink-0 group-hover:scale-125 transition"></div>
                        <div>
                            <p class="text-xs font-bold text-gray-900 leading-relaxed">{{ $notification->data['message'] ?? 'New notification' }}</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 italic text-center">No new notifications</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- Upcoming Appointments Section (Full Width) --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-black text-gray-900 flex items-center gap-3">
                <i class="bi bi-calendar-event-fill text-brand-600"></i> Upcoming Appointments
            </h3>
            <a href="{{ route('admin.appointments.index') }}" class="text-xs font-black text-brand-600 uppercase tracking-widest hover:underline">View All</a>
        </div>
        
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-8 py-4">Patient</th>
                        <th class="px-8 py-4">Service</th>
                        <th class="px-8 py-4 whitespace-nowrap">Date & Time</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($upcomingAppointments as $appointment)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-sm group-hover:scale-110 transition">
                                    {{ substr($appointment->display_patient_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 leading-none mb-1">{{ $appointment->display_patient_name }}</p>
                                    <p class="text-xs font-bold text-gray-400">{{ $appointment->display_patient_email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                {{ $appointment->service }}
                            </span>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            <p class="text-sm font-black text-gray-900 leading-none mb-1">{{ $appointment->scheduled_at ? $appointment->scheduled_at->format('M d, Y') : '-' }}</p>
                            <p class="text-xs font-bold text-gray-400 tracking-wider">{{ $appointment->scheduled_at ? $appointment->scheduled_at->format('h:i A') : '-' }}</p>
                        </td>
                        <td class="px-8 py-5">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-50 text-amber-600',
                                    'approved' => 'bg-emerald-50 text-emerald-600',
                                    'rejected' => 'bg-rose-50 text-rose-600',
                                    'cancelled' => 'bg-gray-50 text-gray-600',
                                    'rescheduled' => 'bg-indigo-50 text-indigo-600',
                                ];
                                $statusClass = $statusClasses[$appointment->status] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                {{ $appointment->status }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('admin.appointments.show', $appointment->id) }}" 
                               class="w-10 h-10 inline-flex items-center justify-center rounded-2xl bg-gray-50 text-gray-400 hover:bg-brand-50 hover:text-brand-600 transition shadow-sm group-hover:shadow-md">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            No upcoming appointments
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-50">
            @forelse($upcomingAppointments as $appointment)
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-sm">
                                {{ substr($appointment->display_patient_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-900 leading-tight">{{ $appointment->display_patient_name }}</p>
                                <p class="text-[9px] font-black text-brand-600 uppercase tracking-widest mt-0.5">{{ $appointment->service }}</p>
                            </div>
                        </div>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-50 text-amber-600',
                                'approved' => 'bg-emerald-50 text-emerald-600',
                                'rejected' => 'bg-rose-50 text-rose-600',
                                'cancelled' => 'bg-gray-50 text-gray-600',
                                'rescheduled' => 'bg-indigo-50 text-indigo-600',
                            ];
                            $statusClass = $statusClasses[$appointment->status] ?? 'bg-gray-50 text-gray-600';
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $statusClass }}">
                            {{ $appointment->status }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between text-[10px]">
                        <div class="flex items-center gap-2 text-gray-500 font-bold uppercase tracking-widest">
                            <i class="bi bi-calendar3"></i>
                            {{ $appointment->scheduled_at ? $appointment->scheduled_at->format('M d, Y') : '-' }}
                        </div>
                        <div class="flex items-center gap-2 text-gray-500 font-bold uppercase tracking-widest">
                            <i class="bi bi-clock"></i>
                            {{ $appointment->scheduled_at ? $appointment->scheduled_at->format('h:i A') : '-' }}
                        </div>
                    </div>

                    <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="block w-full py-3 bg-gray-50 text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest text-center border border-gray-100 shadow-sm">
                        View Details
                    </a>
                </div>
            @empty
                <div class="p-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                    No upcoming appointments
                </div>
            @endforelse
        </div>
    </div>
    {{-- Data for JS --}}
    <div id="dashboard-data" 
         data-gender-labels='{!! json_encode($healthAnalytics['gender_distribution']->pluck('gender')) !!}'
         data-gender-counts='{!! json_encode($healthAnalytics['gender_distribution']->pluck('count')) !!}'
         data-service-labels='{!! json_encode($serviceStats->pluck('service')) !!}'
         data-service-counts='{!! json_encode($serviceStats->pluck('count')) !!}'
         data-calendar-events='{!! json_encode($calendarEvents) !!}'
         class="hidden">
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataEl = document.getElementById('dashboard-data');
        const genderLabels = JSON.parse(dataEl.dataset.genderLabels);
        const genderCounts = JSON.parse(dataEl.dataset.genderCounts);
        const serviceLabels = JSON.parse(dataEl.dataset.serviceLabels);
        const serviceCounts = JSON.parse(dataEl.dataset.serviceCounts);
        const calendarEvents = JSON.parse(dataEl.dataset.calendarEvents);

        // 📊 Charts
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');

        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderCounts,
                    backgroundColor: ['#0ea5e9', '#f472b6', '#94a3b8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 15, font: { weight: 'bold', size: 10 } }
                    }
                }
            }
        });

        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceCounts,
                    backgroundColor: ['#3b82f6', '#a855f7', '#14b8a6', '#6366f1', '#0ea5e9', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 15, font: { weight: 'bold', size: 10 } }
                    }
                },
                cutout: '70%'
            }
        });

        // 📅 Calendar
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            height: 'auto',
            contentHeight: 'auto',
            events: calendarEvents,
            eventDisplay: 'block',
            displayEventTime: false,
            eventClassNames: 'rounded-lg border-none text-[9px] font-black px-2 py-0.5',
            dayMaxEvents: 2,
            eventClick: function(info) { 
                info.jsEvent.preventDefault(); 
                if(info.event.url) window.location.href = info.event.url; 
            }
        });

        calendar.render();
    });
</script>
<style>
    /* Premium Calendar Styling */
    .fc { font-family: inherit; border: none; }
    .fc-toolbar-title { font-size: 0.9rem !important; font-weight: 900 !important; color: #111827; text-transform: uppercase; letter-spacing: 0.05em; }
    .fc-button { background: #f9fafb !important; border: 1px solid #f3f4f6 !important; color: #6b7280 !important; font-weight: 900 !important; text-transform: uppercase !important; font-size: 0.6rem !important; padding: 0.5rem 0.75rem !important; border-radius: 0.75rem !important; transition: all 0.3s !important; }
    .fc-button:hover { background: #f3f4f6 !important; color: #111827 !important; transform: translateY(-1px); }
    .fc-button-primary:not(:disabled).fc-button-active { background: #0ea5e9 !important; border-color: #0ea5e9 !important; color: #fff !important; }
    .fc-col-header-cell-cushion { font-size: 0.65rem !important; font-weight: 900 !important; color: #9ca3af !important; text-transform: uppercase !important; letter-spacing: 0.1em !important; text-decoration: none !important; }
    .fc-daygrid-day-number { font-size: 0.75rem !important; font-weight: 800 !important; color: #374151 !important; text-decoration: none !important; margin: 4px 8px !important; }
    .fc-day-today { background: rgba(14, 165, 233, 0.03) !important; }
    .fc-day-today .fc-daygrid-day-number { color: #0ea5e9 !important; background: #f0f9ff; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
    .fc-event { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .fc-scrollgrid { border: none !important; }
    .fc-theme-standard td, .fc-theme-standard th { border: 1px solid #f9fafb !important; }
</style>
@endsection
