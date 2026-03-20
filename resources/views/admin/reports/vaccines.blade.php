@extends('layouts.app')

@section('title', 'Monthly Vaccine Supply & Usage Report')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8 print:p-0">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 border border-gray-100 shadow-sm overflow-hidden group print:hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-3 sm:mb-4">
                    <i class="bi bi-shield-check text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Immunization Control</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight leading-tight mb-2 sm:mb-3">
                    Vaccine <span class="text-brand-600 underline decoration-brand-200 decoration-4 underline-offset-4">Usage Summary</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    DOH-standard monthly supply and usage report for tracking vaccine inventory and administration.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <form action="{{ route('admin.reports.vaccines') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="month" class="px-4 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none shadow-inner">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="px-4 py-3 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none shadow-inner">
                        @for($y=date('Y'); $y>=2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="p-3 bg-gray-900 text-white rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                        <i class="bi bi-filter text-xs"></i>
                    </button>
                </form>
                <div class="h-10 w-[1px] bg-gray-100 mx-1 hidden sm:block"></div>
                <a href="{{ route('admin.reports.vaccines.export', ['month' => $month, 'year' => $year]) }}" 
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-emerald-600 text-white font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 hover:bg-emerald-700 transition-all transform active:scale-95">
                    <i class="bi bi-file-earmark-excel mr-2"></i>
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

    <!-- Printable Header -->
    <div class="hidden print:block text-center mb-10">
        <h2 class="text-xl font-black uppercase">Department of Health</h2>
        <h3 class="text-lg font-bold uppercase">Barangay Health Center - Vaccine Supply & Usage</h3>
        <p class="text-sm font-bold text-gray-500 uppercase mt-2">Report Period: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>

    <!-- Summary Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Vaccine Antigen</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Beg. Balance</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Received</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Administered</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Wastage</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Ending Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($summary as $row)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-gray-900">{{ $row['name'] }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $row['manufacturer'] ?? 'No Manufacturer' }}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-xs font-black text-gray-600">{{ $row['beginning_balance'] }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-xs font-black text-brand-600">+{{ $row['received_this_month'] }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-xs font-black text-indigo-600">-{{ $row['administered'] }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-xs font-black text-orange-600">{{ $row['wastage'] }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1.5 rounded-lg {{ $row['remaining'] <= 5 ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} text-xs font-black">
                                {{ $row['remaining'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">No vaccine data found for this period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Signatures (Print Only) -->
    <div class="hidden print:grid grid-cols-2 gap-20 mt-20 px-10">
        <div class="text-center">
            <div class="border-b-2 border-black mb-2"></div>
            <p class="text-xs font-black uppercase">Prepared By (Health Worker/Admin)</p>
        </div>
        <div class="text-center">
            <div class="border-b-2 border-black mb-2"></div>
            <p class="text-xs font-black uppercase">Verified By (Municipal Health Officer)</p>
        </div>
    </div>

</div>

<style>
    @media print {
        body { background: white !important; }
        .max-w-7xl { max-width: 100% !important; padding: 0 !important; }
        .bg-white { border: none !important; box-shadow: none !important; }
        table { border: 1px solid #e5e7eb !important; }
        th { 
            background-color: #f9fafb !important; 
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endsection