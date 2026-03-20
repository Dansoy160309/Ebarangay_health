@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<style>
    @media print {
        /* 1. Reset Page and Margin */
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        /* 2. Hide Web Elements */
        .print\:hidden,
        nav,
        .sticky,
        button,
        a,
        form,
        .absolute.-inset-1 {
            display: none !important;
        }

        /* 3. Layout Fixes */
        body {
            background-color: white !important;
            color: black !important;
            font-size: 10pt !important;
        }

        .min-h-screen {
            min-height: auto !important;
            padding-bottom: 0 !important;
        }

        .max-w-\[1600px\] {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* 4. Force Backgrounds (Important for Charts/Badges) */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 5. Card Adjustments */
        .bg-white {
            border: 1px solid #eee !important;
            box-shadow: none !important;
        }

        .rounded-\[3rem\], .rounded-\[2\.5rem\], .rounded-2xl {
            border-radius: 1rem !important;
        }

        /* 6. Typography */
        h1 { font-size: 24pt !important; margin-bottom: 10pt !important; }
        h3 { font-size: 16pt !important; margin-bottom: 15pt !important; }
        .text-4xl { font-size: 20pt !important; }
        .text-lg { font-size: 11pt !important; }

        /* 7. Grid Layouts for Print */
        .grid {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 15pt !important;
        }

        .lg\:grid-cols-4 > * {
            width: calc(25% - 15pt) !important;
        }

        .lg\:grid-cols-12 .lg\:col-span-4 { width: 33% !important; }
        .lg\:grid-cols-12 .lg\:col-span-8 { width: 65% !important; }
        
        .md\:grid-cols-2 > * {
            width: calc(50% - 15pt) !important;
        }

        .lg\:grid-cols-3 > * {
            width: calc(33.33% - 15pt) !important;
        }

        /* 8. Page Breaks */
        .break-inside-avoid {
            page-break-inside: avoid !important;
        }

        /* 9. Header Branding (Optional: Show only on print) */
        .print-only-header {
            display: block !important;
            text-align: center;
            margin-bottom: 30pt;
            border-bottom: 2pt solid #0ea5e9;
            padding-bottom: 10pt;
        }
        
        .print-only-header h2 { font-weight: 900; color: #0ea5e9; font-size: 18pt; }
    }

    .print-only-header { display: none; }
</style>

<div class="print-only-header">
    <h2>E-BARANGAY HEALTH SYSTEM</h2>
    <p class="text-xs uppercase font-bold tracking-widest text-gray-500">Official Health Analytics Report • {{ now()->format('F d, Y h:i A') }}</p>
</div>

<div class="min-h-screen bg-gray-50/50 pb-12">
    {{-- Top Navigation Bar --}}
    <nav class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-100 mb-8">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors">Admin</a>
                        <i class="bi bi-chevron-right text-[10px]"></i>
                        <span class="text-gray-900">Reports & Analytics</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.calendar') }}" class="p-2.5 bg-brand-50 text-brand-600 rounded-xl hover:bg-brand-100 transition-all flex items-center gap-2 text-sm font-bold border border-brand-100">
                        <i class="bi bi-calendar3"></i>
                        <span>Calendar View</span>
                    </a>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 text-[11px] font-black uppercase tracking-wider border border-brand-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse"></span>
                        Live Analytics
                    </span>
                    <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-2 text-sm font-bold border border-emerald-100" title="Export to Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                        <span class="hidden sm:inline">Export</span>
                    </a>
                    <button onclick="window.print()" class="p-2 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all border border-transparent hover:border-brand-100">
                        <i class="bi bi-printer text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

<div class="flex flex-col gap-6 sm:gap-8 relative">
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-graph-up-arrow text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Analytics Dashboard</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Reports & <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Analytics</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Comprehensive overview of health center performance, patient trends, and operational metrics.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 print:hidden">
                    <div class="flex items-center gap-2 p-1.5 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner">
                        <form id="filterForm" action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-2">
                            <input type="date" name="start_date" id="startDate" value="{{ $startDate }}" 
                                   class="bg-transparent border-none text-[10px] font-black text-gray-700 focus:ring-0 p-2 w-28 uppercase">
                            <span class="text-gray-300 font-black text-[10px]">→</span>
                            <input type="date" name="end_date" id="endDate" value="{{ $endDate }}" 
                                   class="bg-transparent border-none text-[10px] font-black text-gray-700 focus:ring-0 p-2 w-28 uppercase">
                            <button type="submit" class="p-2.5 bg-gray-900 text-white rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                                <i class="bi bi-search text-xs"></i>
                            </button>
                        </form>
                    </div>
                    <div class="h-10 w-[1px] bg-gray-100 mx-1 hidden sm:block"></div>
                    <a href="{{ route('admin.reports.fhsis') }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-purple-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-purple-500/20 hover:bg-purple-700 transition-all transform active:scale-95">
                        <i class="bi bi-file-earmark-medical mr-2"></i>
                        FHSIS
                    </a>
                    <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-emerald-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 hover:bg-emerald-700 transition-all transform active:scale-95">
                        <i class="bi bi-file-earmark-excel mr-2"></i>
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

        {{-- Performance KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @php
                $stats = [
                    ['label' => 'Total Appointments', 'value' => $appointmentStats['total'], 'growth' => $growthStats['total'], 'icon' => 'bi-calendar-check', 'color' => 'blue'],
                    ['label' => 'Completed', 'value' => $appointmentStats['completed'], 'growth' => $growthStats['completed'], 'icon' => 'bi-check-circle', 'color' => 'emerald'],
                    ['label' => 'Pending', 'value' => $appointmentStats['pending'], 'growth' => $growthStats['pending'], 'icon' => 'bi-hourglass-split', 'color' => 'amber'],
                    ['label' => 'Cancelled', 'value' => $appointmentStats['cancelled'], 'growth' => $growthStats['cancelled'], 'icon' => 'bi-x-circle', 'color' => 'rose'],
                ];
            @endphp

            @foreach($stats as $stat)
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-{{ $stat['color'] }}-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 flex items-center justify-center text-2xl shadow-sm ring-1 ring-{{ $stat['color'] }}-100/50">
                            <i class="bi {{ $stat['icon'] }}"></i>
                        </div>
                        <div @class([
                            'flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black tracking-widest uppercase border',
                            'bg-emerald-50 text-emerald-600 border-emerald-100' => $stat['growth'] >= 0,
                            'bg-rose-50 text-rose-600 border-rose-100' => $stat['growth'] < 0,
                        ])>
                            <i class="bi bi-arrow-{{ $stat['growth'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($stat['growth']) }}%
                        </div>
                    </div>
                    
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">{{ $stat['label'] }}</p>
                        <h3 class="text-4xl font-black text-gray-900 tracking-tight">{{ number_format($stat['value']) }}</h3>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Specialized Analytics Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            
            {{-- Left Column: DOH Monitoring & Medical --}}
            <div class="lg:col-span-4 space-y-8">
                {{-- DOH Monitoring Bento Card --}}
                <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="mb-10">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-shield-plus"></i>
                            </span>
                            DOH Monitoring
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div class="p-6 rounded-[2rem] bg-purple-50/50 border border-purple-100 group hover:bg-purple-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest mb-1">Maternal Health</p>
                                    <h5 class="text-2xl font-black text-gray-900 leading-none">{{ $maternalStats['high_risk_count'] }}</h5>
                                </div>
                                <span class="text-[10px] font-black text-red-500 uppercase">High Risk</span>
                            </div>
                            <div class="mt-4 w-full bg-purple-100 rounded-full h-1.5 overflow-hidden">
                                @php
                                    $maternalWidth = $maternalStats['active_pregnancies'] > 0 ? ($maternalStats['high_risk_count'] / $maternalStats['active_pregnancies'] * 100) : 0;
                                @endphp
                                <div class="bg-purple-600 h-1.5 rounded-full" @style(['width' => $maternalWidth . '%'])></div>
                            </div>
                        </div>

                        <div class="p-6 rounded-[2rem] bg-blue-50/50 border border-blue-100 group hover:bg-blue-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Immunization</p>
                                    <h5 class="text-2xl font-black text-gray-900 leading-none">{{ $immunizationStats['fully_immunized'] }}</h5>
                                </div>
                                <span class="text-[10px] font-black text-blue-600 uppercase">FIC Count</span>
                            </div>
                            <p class="text-[10px] font-bold text-gray-400 mt-2">Target population: {{ $immunizationStats['total_children'] }} infants</p>
                        </div>

                        <div class="p-6 rounded-[2rem] bg-amber-50/50 border border-amber-100 group hover:bg-amber-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Inventory</p>
                                    <h5 class="text-2xl font-black text-gray-900 leading-none">{{ $medicineStats['low_stock_count'] }}</h5>
                                </div>
                                <span class="text-[10px] font-black text-amber-600 uppercase">Low Stock</span>
                            </div>
                            <a href="{{ route('admin.medicines.index') }}" class="inline-block mt-3 text-[10px] font-black text-brand-600 uppercase tracking-widest hover:underline">Reorder Items →</a>
                        </div>
                    </div>
                </div>

                {{-- BP Distribution --}}
                <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                    <div class="mb-8">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-heart-pulse"></i>
                            </span>
                            BP Analytics
                        </h3>
                    </div>
                    <div class="h-[250px] mb-8">
                        <canvas id="bpChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['l' => 'Normal', 'v' => $bpStats['Normal'], 'c' => 'emerald'],
                            ['l' => 'Elevated', 'v' => $bpStats['Elevated'], 'c' => 'amber'],
                            ['l' => 'Stage 1', 'v' => $bpStats['High Stage 1'], 'c' => 'orange'],
                            ['l' => 'Stage 2', 'v' => $bpStats['High Stage 2'], 'c' => 'rose'],
                        ] as $bp)
                        <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-50 flex flex-col items-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ $bp['l'] }}</span>
                            <span class="text-xl font-black text-{{ $bp['c'] }}-600">{{ $bp['v'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Column: Detailed Trends & Visuals --}}
            <div class="lg:col-span-8 space-y-8">
                {{-- Appointment Trend Chart --}}
                <div class="bg-white rounded-[3rem] p-8 sm:p-10 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Appointment Trends</h3>
                            <p class="text-sm font-medium text-gray-400 mt-1">Patient volume distribution over time</p>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 rounded-xl border border-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-500">
                            {{ $trendLabel }}
                        </div>
                    </div>
                    <div class="h-[350px]">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Service Distribution --}}
                    <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-pie-chart-fill"></i>
                            </span>
                            Service Usage
                        </h3>
                        <div class="h-[300px]">
                            <canvas id="serviceChart"></canvas>
                        </div>
                    </div>

                    {{-- Vaccine Distribution --}}
                    <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-inner">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            Top Vaccines
                        </h3>
                        <div class="h-[300px]">
                            <canvas id="vaccineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking & Utilization Analytics --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            {{-- Slot Utilization --}}
            <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-inner">
                        <i class="bi bi-calendar-range"></i>
                    </span>
                    Slot Utilization
                </h3>
                <div class="flex flex-col items-center justify-center py-6">
                    <div class="relative w-48 h-48 mb-8">
                        <canvas id="slotUtilizationChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-black text-gray-900">{{ $slotStats['total_slots_created'] > 0 ? round(($slotStats['total_slots_booked'] / $slotStats['total_slots_created']) * 100) : 0 }}%</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Booked</span>
                        </div>
                    </div>
                    <div class="w-full grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-gray-50 text-center">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Total Capacity</p>
                            <p class="text-xl font-black text-gray-900">{{ number_format($slotStats['total_slots_created']) }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 text-center">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Actual Bookings</p>
                            <p class="text-xl font-black text-gray-900">{{ number_format($slotStats['total_slots_booked']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attendance vs No-Show --}}
            <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm shadow-inner">
                        <i class="bi bi-person-check"></i>
                    </span>
                    Attendance vs No-Show
                </h3>
                <div class="h-[250px] mb-8">
                    <canvas id="attendanceChart"></canvas>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-gray-700">Attended (Completed)</span>
                        </div>
                        <span class="text-sm font-black text-emerald-700">{{ number_format($appointmentStats['completed']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-orange-50/50 border border-orange-100">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                            <span class="text-xs font-bold text-gray-700">No Show</span>
                        </div>
                        <span class="text-sm font-black text-orange-700">{{ number_format($appointmentStats['no_show']) }}</span>
                    </div>
                </div>
            </div>

            {{-- Peak Booking Behavior --}}
            <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-inner">
                        <i class="bi bi-clock-history"></i>
                    </span>
                    Peak Booking Times
                </h3>
                <div class="space-y-6">
                    @forelse($peakBookingTimes as $index => $time)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 font-black text-xs">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-900">{{ $time->formatted_hour }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Most Active Hour</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-brand-600">{{ $time->count }} Bookings</p>
                            <div class="w-20 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                @php
                                    $maxPeak = $peakBookingTimes->first()->count;
                                    $peakWidth = $maxPeak > 0 ? ($time->count / $maxPeak) * 100 : 0;
                                @endphp
                                <div class="h-full bg-brand-500" @style(['width' => $peakWidth . '%'])></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="bi bi-clock text-4xl text-gray-200 block mb-3"></i>
                        <p class="text-xs font-bold text-gray-400">No booking data available</p>
                    </div>
                    @endforelse
                </div>
                <div class="mt-8 pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase leading-relaxed text-center">
                        Trends based on historical data for the selected period.
                    </p>
                </div>
            </div>
        </div>

        {{-- Geographics & Demographics --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            {{-- Prenatal Geographic Map --}}
            <div class="lg:col-span-8 bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Prenatal Geographic Distribution</h3>
                        <p class="text-sm font-medium text-gray-400 mt-1">Tracking maternal cases by Purok location</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                </div>
                <div class="h-[350px]">
                    <canvas id="prenatalChart"></canvas>
                </div>
            </div>

            {{-- Common Illnesses --}}
            <div class="lg:col-span-4 bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-10">Top Morbidity</h3>
                <div class="space-y-6">
                    @forelse($diseaseStats as $index => $disease)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-5">
                            <span class="text-2xl font-black text-gray-100 group-hover:text-brand-100 transition-colors">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-black text-gray-900 leading-none mb-1">{{ $disease->diagnosis }}</p>
                                <div class="w-24 h-1 bg-gray-50 rounded-full overflow-hidden">
                                    @php
                                        $maxCount = $diseaseStats->first()->count;
                                        $width = $maxCount > 0 ? ($disease->count / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-brand-500 rounded-full" @style(['width' => $width . '%'])></div>
                                </div>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-gray-50 rounded-lg text-[10px] font-black text-gray-600 uppercase">{{ $disease->count }} Cases</span>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="bi bi-folder-x text-4xl text-gray-200 block mb-3"></i>
                        <p class="text-xs font-bold text-gray-400">No morbidity data recorded</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Demographics Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Patient Demographics --}}
            <div class="bg-white rounded-[3rem] border border-gray-100 shadow-sm p-10 group hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Patient Demographics</h3>
                        <p class="text-sm text-gray-500 font-medium mt-1">Age and Gender distribution</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-2xl">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-12">
                    <div class="flex flex-col items-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8">Age Groups</p>
                        <div class="w-full max-w-[220px]">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8">Gender Split</p>
                        <div class="w-full max-w-[220px]">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Placeholder or additional stat card if needed --}}
            <div class="bg-gradient-to-br from-brand-600 to-purple-700 rounded-[3rem] p-10 shadow-xl shadow-brand-100 text-white relative overflow-hidden group">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <h3 class="text-3xl font-black leading-tight mb-4">Improve Barangay Outreach</h3>
                        <p class="text-brand-100 font-medium text-lg max-w-xs leading-relaxed">Use these analytics to identify Puroks with low service coverage and plan next month's health missions.</p>
                    </div>
                    <div class="mt-10">
                        <button onclick="window.print()" class="px-8 py-4 bg-white text-brand-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-brand-50 transition-all shadow-lg">
                            Export Situationer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
{{-- Hidden JSON Data for Charts --}}
<script id="report-data" type="application/json">
    {{ Js::from([
        'trendStats' => $trendStats,
        'serviceStats' => $serviceStats,
        'ageGroups' => $ageGroups,
        'genderStats' => $genderStats,
        'diseaseStats' => $diseaseStats,
        'prenatalStats' => $prenatalStats,
        'vaccineStats' => $vaccineStats,
        'bpStats' => $bpStats,
        'slotStats' => $slotStats,
        'appointmentStats' => $appointmentStats,
    ]) }}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function setFilter(type) {
        const today = new Date();
        // Use local time, not UTC (toISOString uses UTC)
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        let start, end;
        end = formatDate(today);

        if (type === '7days') {
            const date = new Date();
            date.setDate(date.getDate() - 6);
            start = formatDate(date);
        } else if (type === 'thisMonth') {
            const date = new Date();
            date.setDate(1); // 1st of current month
            start = formatDate(date);
        } else if (type === 'lastMonth') {
            const firstDayPrevMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDayPrevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            start = formatDate(firstDayPrevMonth);
            end = formatDate(lastDayPrevMonth);
        }

        document.getElementById('startDate').value = start;
        document.getElementById('endDate').value = end;
        document.getElementById('filterForm').submit();
    }

    // Highlight active filter button
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const start = urlParams.get('start_date');
        const end = urlParams.get('end_date');

        if (start && end) {
            // Helper to check match
            const checkMatch = (type) => {
                const today = new Date();
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };
                
                let s, e;
                e = formatDate(today);

                if (type === '7days') {
                    const d = new Date();
                    d.setDate(d.getDate() - 6);
                    s = formatDate(d);
                } else if (type === 'thisMonth') {
                    const d = new Date();
                    d.setDate(1);
                    s = formatDate(d);
                } else if (type === 'lastMonth') {
                    const d = new Date();
                    const first = new Date(d.getFullYear(), d.getMonth() - 1, 1);
                    const last = new Date(d.getFullYear(), d.getMonth(), 0);
                    s = formatDate(first);
                    e = formatDate(last);
                }
                return start === s && end === e;
            };

            if (checkMatch('7days')) document.getElementById('btn-7days').classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
            if (checkMatch('thisMonth')) document.getElementById('btn-thisMonth').classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
            if (checkMatch('lastMonth')) document.getElementById('btn-lastMonth').classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
        } else {
            // Default to This Month if no params (assuming controller defaults)
            const thisMonthBtn = document.getElementById('btn-thisMonth');
            if(thisMonthBtn) thisMonthBtn.classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
        }
        
        // Parse Data safely from JSON script tag
        const reportData = JSON.parse(document.getElementById('report-data').textContent);
        const { 
            trendStats, 
            serviceStats, 
            ageGroups, 
            genderStats, 
            diseaseStats, 
            prenatalStats, 
            vaccineStats, 
            bpStats 
        } = reportData;

        // 1. Appointment Trends Chart (Line)
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        const trendsGradient = trendsCtx.createLinearGradient(0, 0, 0, 400);
        trendsGradient.addColorStop(0, 'rgba(14, 165, 233, 0.2)');
        trendsGradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trendStats.map(item => item.label),
                datasets: [{
                    label: 'Appointments',
                    data: trendStats.map(item => item.count),
                    borderColor: '#0ea5e9',
                    backgroundColor: trendsGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0ea5e9',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { stepSize: 1, color: '#64748b', font: { weight: '600' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { weight: '600' } }
                    }
                }
            }
        });

        // 2. Service Usage Chart (Doughnut)
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: serviceStats.map(s => s.service),
                datasets: [{
                    data: serviceStats.map(s => s.count),
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'
                    ],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11, weight: '600' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });

        // 3. Age Distribution Chart (Bar)
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(ageGroups),
                datasets: [{
                    data: Object.values(ageGroups),
                    backgroundColor: ['#60a5fa', '#34d399', '#fcd34d', '#a78bfa'],
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false }, ticks: { display: false } },
                    x: { grid: { display: false }, ticks: { color: '#64748b', font: { weight: '600' } } }
                }
            }
        });

        // 4. Gender Distribution Chart (Doughnut)
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(genderStats),
                datasets: [{
                    data: Object.values(genderStats),
                    backgroundColor: ['#3b82f6', '#ec4899', '#9ca3af'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11, weight: '600' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });

        // 5. Common Illnesses Chart (Horizontal Bar)
        const diseaseCtx = document.getElementById('diseaseChart').getContext('2d');
        new Chart(diseaseCtx, {
            type: 'bar',
            data: {
                labels: diseaseStats.map(s => s.diagnosis),
                datasets: [{
                    label: 'Cases',
                    data: diseaseStats.map(s => s.count),
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { beginAtZero: true, grid: { display: false }, ticks: { stepSize: 1 } },
                    y: { grid: { display: false }, ticks: { color: '#64748b', font: { weight: '600' } } }
                }
            }
        });

        // 6. Prenatal Stats Chart (Bar)
        const prenatalCtx = document.getElementById('prenatalChart').getContext('2d');
        new Chart(prenatalCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(prenatalStats),
                datasets: [{
                    label: 'Patients',
                    data: Object.values(prenatalStats),
                    backgroundColor: '#ec4899',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 7. Vaccine Coverage Chart (Bar)
        const vaccineCtx = document.getElementById('vaccineChart').getContext('2d');
        new Chart(vaccineCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(vaccineStats),
                datasets: [{
                    label: 'Doses',
                    data: Object.values(vaccineStats),
                    backgroundColor: '#10b981',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 8. Blood Pressure Chart (Doughnut)
        const bpCtx = document.getElementById('bpChart').getContext('2d');
        new Chart(bpCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(bpStats),
                datasets: [{
                    data: Object.values(bpStats),
                    backgroundColor: ['#10b981', '#f59e0b', '#f97316', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });

        // 9. Slot Utilization Chart (Doughnut)
        const slotCtx = document.getElementById('slotUtilizationChart').getContext('2d');
        new Chart(slotCtx, {
            type: 'doughnut',
            data: {
                labels: ['Booked', 'Remaining'],
                datasets: [{
                    data: [
                        slotStats.total_slots_booked, 
                        Math.max(0, slotStats.total_slots_created - slotStats.total_slots_booked)
                    ],
                    backgroundColor: ['#3b82f6', '#f3f4f6'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: { legend: { display: false } }
            }
        });

        // 10. Attendance vs No-Show Chart (Pie)
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(attendanceCtx, {
            type: 'pie',
            data: {
                labels: ['Attended', 'No Show'],
                datasets: [{
                    data: [appointmentStats.completed, appointmentStats.no_show],
                    backgroundColor: ['#10b981', '#f97316'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11, weight: '600' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
