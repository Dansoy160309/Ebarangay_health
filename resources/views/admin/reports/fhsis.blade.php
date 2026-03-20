@extends('layouts.app')

@section('title', 'DOH FHSIS Summary')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8 print:p-0">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group print:hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-file-earmark-bar-graph text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">DOH Reporting</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    FHSIS <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Monthly Summary</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Barangay Health Center monthly reporting summary for maternal and child health indicators.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <form action="{{ route('admin.reports.fhsis') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="month" class="px-4 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none shadow-inner">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="px-4 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none shadow-inner">
                        @foreach(range(now()->year - 2, now()->year) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="p-3 bg-gray-900 text-white rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                        <i class="bi bi-filter text-xs"></i>
                    </button>
                </form>
                <div class="h-10 w-[1px] bg-gray-100 mx-1 hidden sm:block"></div>
                <a href="{{ route('admin.reports.fhsis.export', ['month' => $month, 'year' => $year]) }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-emerald-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 hover:bg-emerald-700 transition-all transform active:scale-95">
                    <i class="bi bi-file-earmark-excel-fill mr-2"></i>
                    Export
                </a>
                <button onclick="window.print()" 
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-brand-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all transform active:scale-95">
                    <i class="bi bi-printer-fill mr-2"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    {{-- Report Content --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 print:block">
        {{-- Maternal Health --}}
        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-purple-600 uppercase tracking-widest flex items-center gap-4 mb-10">
                <span class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-sm shadow-inner">
                    <i class="bi bi-gender-female"></i>
                </span>
                Maternal Health Indicators
            </h3>
            <div class="space-y-6">
                <div class="flex justify-between items-center p-4 rounded-2xl bg-gray-50/50">
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">Total Prenatal Visits</span>
                    <span class="text-xl font-black text-gray-900">{{ $maternal['total_prenatal_visits'] }}</span>
                </div>
                <div class="flex justify-between items-center p-4 rounded-2xl bg-red-50/50">
                    <span class="text-xs font-bold text-red-600 uppercase tracking-wide">High-Risk Pregnancies</span>
                    <span class="text-xl font-black text-red-700">{{ $maternal['high_risk_pregnancies'] }}</span>
                </div>
                <div class="flex justify-between items-center p-4 rounded-2xl bg-gray-50/50">
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">Postpartum Compliance</span>
                    <span class="text-xl font-black text-gray-900">{{ $maternal['postpartum_checkups'] }}</span>
                </div>
            </div>
        </div>

        {{-- Child Health --}}
        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-blue-600 uppercase tracking-widest flex items-center gap-4 mb-10">
                <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-sm shadow-inner">
                    <i class="bi bi-shield-check"></i>
                </span>
                Child Health (FIC)
            </h3>
            <div class="space-y-6">
                <div class="flex justify-between items-center p-4 rounded-2xl bg-emerald-50/50">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wide">Fully Immunized Children</span>
                    <span class="text-xl font-black text-emerald-700">{{ $immunization['fic_count'] }}</span>
                </div>
                <div class="flex justify-between items-center p-4 rounded-2xl bg-gray-50/50">
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">Target Infants (Month)</span>
                    <span class="text-xl font-black text-gray-900">{{ $immunization['total_infants'] }}</span>
                </div>
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-50">
                                Coverage Progress
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black inline-block text-blue-600">
                                {{ $immunization['total_infants'] > 0 ? round(($immunization['fic_count'] / $immunization['total_infants']) * 100) : 0 }}%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-blue-50">
                        @php
                            $ficWidth = $immunization['total_infants'] > 0 ? ($immunization['fic_count'] / $immunization['total_infants']) * 100 : 0;
                        @endphp
                        <div @style(['width' => $ficWidth . '%']) class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Morbidity --}}
        <div class="md:col-span-2 bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-red-600 uppercase tracking-widest flex items-center gap-4 mb-10">
                <span class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-sm shadow-inner">
                    <i class="bi bi-virus"></i>
                </span>
                Monthly Morbidity (Top Diseases)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                @forelse($morbidity as $index => $item)
                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <span class="text-lg font-black text-gray-200 group-hover:text-red-100 transition-colors">{{ $index + 1 }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $item->diagnosis }}</span>
                    </div>
                    <span class="px-4 py-1 rounded-full bg-gray-50 text-[10px] font-black text-gray-600 uppercase tracking-widest">{{ $item->case_count }} Cases</span>
                </div>
                @empty
                <p class="text-xs text-gray-400 italic">No morbidity data recorded for this month.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Footer/Signature --}}
    <div class="mt-20 pt-10 border-t border-gray-100 hidden print:block">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-10">Prepared by:</p>
                <div class="w-48 border-b-2 border-gray-900 mb-2"></div>
                <p class="text-sm font-black text-gray-900">{{ auth()->user()->full_name }}</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Administrator</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-10">Verified by:</p>
                <div class="w-48 border-b-2 border-gray-900 mb-2"></div>
                <p class="text-sm font-black text-gray-900">Health Officer / Midwife</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Barangay Health Center</p>
            </div>
        </div>
    </div>
</div>
@endsection
