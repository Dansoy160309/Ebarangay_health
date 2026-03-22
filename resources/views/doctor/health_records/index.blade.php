@extends('layouts.app')

@section('title', 'Patient Health Records')

@section('content')
@php
    $user = auth()->user();
    $prefix = $user->isMidwife() ? 'midwife' : ($user->isDoctor() ? 'doctor' : 'healthworker');
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 relative overflow-hidden">
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-brand-50/50 rounded-full blur-3xl -mr-48 -mt-48 opacity-20 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-50/50 rounded-full blur-3xl -ml-48 -mb-48 opacity-20 pointer-events-none"></div>

    {{-- Page Header --}}
    <div class="relative z-10 bg-white rounded-2xl p-5 lg:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full blur-2xl -mr-16 -mt-16 opacity-30 group-hover:opacity-60 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2.5">
                    <i class="bi bi-file-earmark-medical-fill text-xs"></i>
                    <span class="text-[8px] font-black uppercase tracking-[0.15em]">Medical Archives</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight leading-[1.1]">
                    Health <span class="text-brand-600">Records</span>
                </h1>
                <p class="text-gray-500 font-medium mt-2 text-xs leading-relaxed">
                    Access and manage complete patient medical histories and diagnostic records.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="bg-gray-50 px-5 py-3 rounded-lg border border-gray-100 shadow-inner">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Total Records</p>
                    <p class="text-2xl font-black text-gray-900">{{ $records->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="relative z-10 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-brand-600"></div>
        <form action="{{ route($prefix . '.health-records.index') }}" method="GET" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <!-- Search -->
                <div class="md:col-span-4 space-y-2">
                    <label for="search" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Search Patient or Diagnosis</label>
                    <div class="relative group">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-600 transition-colors"></i>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                            class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-400" 
                            placeholder="Enter keywords..." autocomplete="off">
                    </div>
                </div>

                <!-- Service Filter -->
                <div class="md:col-span-3 space-y-2">
                    <label for="service_id" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Service Category</label>
                    <div class="relative group">
                        <i class="bi bi-filter-circle absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-600 transition-colors"></i>
                        <select name="service_id" id="service_id" class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer">
                            <option value="">All Health Services</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="md:col-span-3 space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Timeline Range</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="date" name="date_start" value="{{ request('date_start') }}" 
                            class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-brand-500/10 transition-all">
                        <input type="date" name="date_end" value="{{ request('date_end') }}" 
                            class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-brand-500/10 transition-all">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="flex-1 py-4 bg-brand-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-brand-700 shadow-lg shadow-brand-500/20 transition-all active:scale-95">
                        Filter
                    </button>
                    <a href="{{ route($prefix . '.health-records.index') }}" class="px-5 py-4 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 transition-all active:scale-95 flex items-center justify-center shadow-sm" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise text-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- List --}}
    <div class="relative z-10 space-y-6">
        {{-- Desktop View: Table --}}
        <div class="hidden md:block bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden relative group/table">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Consultation Date</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Patient Details</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Medical Service</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Clinical Diagnosis</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50/50 transition-all duration-300 group/row">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 tracking-tight">{{ $record->created_at->format('M d, Y') }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $record->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black shadow-inner group-hover/row:scale-110 transition-transform">
                                        {{ substr($record->patient->first_name, 0, 1) }}{{ substr($record->patient->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 tracking-tight">{{ $record->patient->full_name }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                            {{ $record->patient->age }} yrs • {{ $record->patient->gender }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $serviceName = $record->service->name ?? 'Consultation';
                                    $badgeColor = match(true) {
                                        str_contains(strtolower($serviceName), 'prenatal') => 'bg-pink-50 text-pink-700 border-pink-100',
                                        str_contains(strtolower($serviceName), 'immunization') => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        default => 'bg-indigo-50 text-indigo-700 border-indigo-100'
                                    };
                                    $icon = match(true) {
                                        str_contains(strtolower($serviceName), 'prenatal') => 'bi-heart-pulse-fill',
                                        str_contains(strtolower($serviceName), 'immunization') => 'bi-shield-plus',
                                        default => 'bi-file-medical-fill'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $badgeColor }}">
                                    <i class="bi {{ $icon }} text-sm"></i>
                                    {{ $serviceName }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                @if($record->diagnosis)
                                    <span class="text-sm font-bold text-gray-600 line-clamp-1 max-w-xs" title="{{ $record->diagnosis }}">
                                        {{ $record->diagnosis }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Pending Entry</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route($prefix . '.health-records.show', $record->id) }}" 
                                   class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:text-brand-600 hover:bg-brand-50 transition-all border border-transparent hover:border-brand-100 shadow-sm active:scale-95">
                                    <i class="bi bi-arrow-right text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center max-w-xs mx-auto">
                                    <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                        <i class="bi bi-folder-x text-4xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 tracking-tight mb-2">No Records Found</h3>
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest leading-relaxed">Try adjusting your filters or search terms to find specific archives.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile View: Cards --}}
        <div class="md:hidden space-y-6">
            @forelse($records as $record)
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-bl-[3rem] -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                    
                    {{-- Header: Date & Service --}}
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-8 relative z-10">
                        <div class="flex items-center gap-3 text-gray-400 text-[10px] font-black uppercase tracking-widest bg-gray-50 px-4 py-2 rounded-2xl border border-gray-100">
                            <i class="bi bi-calendar3 text-brand-400"></i>
                            {{ $record->created_at->format('M d, Y') }}
                        </div>
                        
                        @php
                            $serviceName = $record->service->name ?? 'Consultation';
                            $badgeColor = match(true) {
                                str_contains(strtolower($serviceName), 'prenatal') => 'bg-pink-50 text-pink-700 border-pink-100',
                                str_contains(strtolower($serviceName), 'immunization') => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                default => 'bg-indigo-50 text-indigo-700 border-indigo-100'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $badgeColor }}">
                            {{ $serviceName }}
                        </span>
                    </div>

                    {{-- Body: Patient --}}
                    <div class="flex items-center gap-5 mb-8 relative z-10">
                         <div class="w-16 h-16 rounded-[1.5rem] bg-brand-50 flex items-center justify-center text-brand-600 font-black text-xl shadow-inner border border-brand-100">
                            {{ substr($record->patient->first_name, 0, 1) }}{{ substr($record->patient->last_name, 0, 1) }}
                         </div>
                         <div>
                             <h4 class="text-xl font-black text-gray-900 tracking-tight leading-tight">{{ $record->patient->full_name }}</h4>
                             <p class="text-[10px] font-black text-gray-400 mt-1 uppercase tracking-[0.2em]">
                                 {{ $record->patient->age }} yrs • {{ $record->patient->gender }}
                             </p>
                         </div>
                    </div>

                    {{-- Diagnosis --}}
                    <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100 mb-8 relative z-10">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Clinical Diagnosis</p>
                        @if($record->diagnosis)
                            <p class="text-sm font-bold text-gray-700 leading-relaxed">{{ $record->diagnosis }}</p>
                        @else
                            <p class="text-xs font-black text-gray-300 uppercase tracking-widest italic">Pending Medical Entry</p>
                        @endif
                    </div>

                    <a href="{{ route($prefix . '.health-records.show', $record->id) }}" class="flex items-center justify-center gap-3 w-full py-5 bg-brand-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition-all active:scale-95 relative z-10">
                        View Full Medical File <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @empty
                <div class="bg-white rounded-[3rem] border-2 border-dashed border-gray-100 p-16 text-center shadow-sm">
                    <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-300 border border-gray-50 shadow-inner transform -rotate-6">
                        <i class="bi bi-folder-x text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">Archive Empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">No matching medical records found.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($records->hasPages())
        <div class="pt-10 border-t border-gray-100">
            {{ $records->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
