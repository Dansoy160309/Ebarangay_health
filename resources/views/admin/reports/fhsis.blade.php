@extends('layouts.app')

@section('title', 'DOH FHSIS Summary')

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-8 print:p-0">
    {{-- Header --}}
    <div class="flex justify-between items-center bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 print:shadow-none print:border-none">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-3xl shadow-inner print:hidden">
                <i class="bi bi-file-earmark-bar-graph"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">FHSIS Monthly Summary</h1>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Barangay Health Center Reporting</p>
            </div>
        </div>
        <div class="flex items-center gap-4 print:hidden">
            <form action="{{ route('admin.reports.fhsis') }}" method="GET" class="flex items-center gap-3">
                <select name="month" class="px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-black uppercase tracking-widest focus:ring-4 focus:ring-brand-50 transition-all">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
                <select name="year" class="px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-black uppercase tracking-widest focus:ring-4 focus:ring-brand-50 transition-all">
                    @foreach(range(now()->year - 2, now()->year) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-sm">Filter</button>
            </form>
            <button onclick="window.print()" class="px-6 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-sm">
                <i class="bi bi-printer-fill mr-2"></i> Print Report
            </button>
        </div>
    </div>

    {{-- Report Content --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
