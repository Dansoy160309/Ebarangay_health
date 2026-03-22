@extends('layouts.app')

@section('title', 'Health Records Management')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">
    
    {{-- Top-Aligned Search Section --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full blur-2xl -mr-16 -mt-16 opacity-30 group-hover:opacity-60 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-2.5">
                    <i class="bi bi-folder2-open text-xs"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">Patient Archives</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-tight mb-1 sm:mb-1.5">
                    Health <span class="text-brand-600">Records</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-xs leading-relaxed">
                    Search for a patient to access their clinical history.
                </p>
            </div>

            @php($routePrefix = $routePrefix ?? 'healthworker')
            <form method="GET" action="{{ route($routePrefix . '.health-records.index') }}" class="w-full md:max-w-sm">
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-grow group">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-600 transition-colors text-xs"></i>
                        <input type="text" name="search_patient" value="{{ request('search_patient') }}" 
                            class="w-full pl-9 pr-3 py-2 bg-gray-50 border-none rounded-lg text-xs font-bold focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-400 shadow-inner" 
                            placeholder="Search by name..." autocomplete="off">
                    </div>
                    <div class="flex gap-1.5">
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg font-black text-[8px] uppercase tracking-tighter hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all active:scale-95 shrink-0">
                            Search
                        </button>
                        @if(request('search_patient'))
                            <a href="{{ route($routePrefix . '.health-records.index') }}" class="px-3 py-2 bg-gray-100 text-gray-500 rounded-lg hover:bg-gray-200 transition-all active:scale-95 flex items-center justify-center shadow-sm shrink-0 text-xs">
                                <i class="bi bi-arrow-counterclockwise text-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Step 2: Search Results --}}
    @if(isset($searchResults) && $searchResults->isNotEmpty() && !isset($selectedSubject))
        <div class="relative z-10 space-y-6 animate-fade-in-up">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-6">Identified Patients</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($searchResults as $result)
                    <a href="{{ route($routePrefix . '.health-records.index', ['search_patient' => request('search_patient'), 'subject_id' => $result->id]) }}" 
                       class="group bg-white p-6 rounded-[2.5rem] border border-gray-100 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-500/5 transition-all duration-500 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full blur-2xl -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="relative z-10 flex items-center gap-5">
                            <div class="h-14 w-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-xl shadow-inner group-hover:scale-110 transition-transform duration-500">
                                {{ substr($result->first_name, 0, 1) }}{{ substr($result->last_name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="font-black text-gray-900 truncate tracking-tight">{{ $result->full_name }}</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate mt-1">{{ $result->address }}</p>
                                @if($result->guardian_id && $result->guardian)
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-brand-50 text-brand-600 text-[9px] font-black uppercase tracking-widest mt-2">
                                        <i class="bi bi-person-fill-lock"></i> Dependent
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif(request('search_patient') && !isset($selectedSubject))
        <div class="relative z-10 bg-white rounded-[3rem] p-16 text-center border border-gray-100 shadow-sm">
            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-300 shadow-inner">
                <i class="bi bi-search text-4xl"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">No Patients Found</h3>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Adjust your search terms to locate specific archives.</p>
        </div>
    @endif

    {{-- Step 4: Records View --}}
    @if(isset($selectedSubject))
        <div class="relative z-10 bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up">
            <div class="px-10 py-8 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="h-16 w-16 rounded-[1.5rem] bg-white text-brand-600 flex items-center justify-center font-black text-2xl shadow-sm border border-gray-100">
                        {{ substr($selectedSubject->first_name, 0, 1) }}{{ substr($selectedSubject->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $selectedSubject->full_name }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-white px-2 py-0.5 rounded-md border border-gray-100">{{ $selectedSubject->age }} yrs</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $selectedSubject->gender }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route($routePrefix . '.health-records.create', ['patient_id' => $selectedSubject->id]) }}" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-brand-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-brand-700 shadow-lg shadow-brand-500/20 transition-all active:scale-95 gap-3">
                    <i class="bi bi-plus-lg text-lg"></i> Add New Record
                </a>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-6 p-6">
                @forelse($records as $record)
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-bl-[3rem] -mr-12 -mt-12 opacity-50"></div>
                        
                        <div class="flex justify-between items-center mb-6 relative z-10">
                            <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                                <i class="bi bi-calendar3 text-brand-400"></i>
                                {{ $record->created_at->format('M d, Y') }}
                            </div>
                        </div>

                        <div class="mb-6 relative z-10">
                            <h4 class="text-lg font-black text-gray-900 mb-2 tracking-tight line-clamp-1">{{ $record->diagnosis ?: 'Medical Consultation' }}</h4>
                            <p class="text-xs font-bold text-gray-500 line-clamp-2 leading-relaxed">
                                {{ $record->consultation }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-5 border-t border-gray-50 relative z-10">
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                By: {{ $record->creator->first_name ?? 'Unknown' }}
                            </div>
                            <a href="{{ route($routePrefix . '.health-records.show', $record->id) }}" class="px-6 py-3 rounded-xl bg-brand-600 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-brand-500/10 hover:bg-brand-700 transition-all flex items-center gap-2">
                                View File <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 rounded-[2rem] p-12 text-center border border-dashed border-gray-200">
                        <i class="bi bi-clipboard2-pulse text-3xl text-gray-300 mb-4 block"></i>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Archive Empty</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Consultation Date</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Clinical Diagnosis</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Attended By</th>
                            <th class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($records as $record)
                            <tr class="hover:bg-gray-50/50 transition-all duration-300 group/row">
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 tracking-tight">{{ $record->created_at->format('M d, Y') }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $record->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="text-sm font-black text-gray-900 tracking-tight mb-1">{{ $record->diagnosis ?: 'Medical Consultation' }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate max-w-xs">{{ Str::limit($record->consultation, 50) }}</div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-500 uppercase">
                                            {{ substr($record->creator->first_name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-600">{{ $record->creator->full_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <a href="{{ route($routePrefix . '.health-records.show', $record->id) }}" 
                                       class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:text-brand-600 hover:bg-brand-50 transition-all border border-transparent hover:border-brand-100 shadow-sm active:scale-95">
                                        <i class="bi bi-arrow-right text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-10 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center max-w-xs mx-auto">
                                        <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                            <i class="bi bi-clipboard2-pulse text-4xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-xl font-black text-gray-900 tracking-tight mb-2">No Records Found</h3>
                                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest leading-relaxed">This patient has no recorded medical history in our archives.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($records->hasPages())
                <div class="px-10 py-8 border-t border-gray-100 bg-gray-50/50">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
