@extends('layouts.app')

@section('title', 'My Health History')

@section('content')
<style>
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-bounce-subtle {
        animation: bounce-subtle 2s infinite ease-in-out;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 relative overflow-hidden bg-[#fcfcfd] font-sans">
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 right-0 w-[30rem] h-[30rem] bg-brand-50/20 rounded-full blur-3xl -mr-60 -mt-60 opacity-30 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[30rem] h-[30rem] bg-blue-50/20 rounded-full blur-3xl -ml-60 -mb-60 opacity-30 pointer-events-none"></div>

    <!-- Family Selection Hero -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full -mr-16 -mt-16 blur-[60px] opacity-30"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-inner border border-brand-100/50">
                        <i class="bi bi-person-badge text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Medical Profiles</h2>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1 flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-brand-400 animate-pulse"></span>
                            Select a profile to view history
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 shadow-inner self-start">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-lg border-2 border-white bg-brand-400 flex items-center justify-center text-[9px] font-black text-white shadow-sm">
                            {{ substr(auth()->user()->first_name, 0, 1) }}
                        </div>
                        @foreach($dependents->take(3) as $dep)
                            <div class="w-8 h-8 rounded-lg border-2 border-white bg-blue-400 flex items-center justify-center text-[9px] font-black text-white shadow-sm">
                                {{ substr($dep->first_name, 0, 1) }}
                            </div>
                        @endforeach
                    </div>
                    <div class="h-4 w-px bg-gray-200"></div>
                    <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">
                        {{ $dependents->count() + 1 }} Profiles
                    </span>
                </div>
            </div>
            
            <div class="flex flex-nowrap overflow-x-auto pb-4 gap-4 scrollbar-hide -mx-2 px-2">
                {{-- Account Holder Card --}}
                <a href="{{ route('patient.health-records.index', array_merge(request()->except(['page', 'subject_id']), ['subject_id' => auth()->id()])) }}" 
                   class="flex-shrink-0 relative group min-w-[240px] transition-all duration-500">
                    <div class="relative p-5 rounded-xl border-2 transition-all duration-500 {{ $selectedSubject->id == auth()->id() ? 'bg-brand-50 border-brand-200 shadow-lg shadow-brand-500/5' : 'bg-white border-gray-50 hover:border-brand-100' }}">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="h-12 w-12 rounded-lg {{ $selectedSubject->id == auth()->id() ? 'bg-brand-600 text-white' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center font-black text-xl shadow-sm transition-all duration-500">
                                    {{ substr(auth()->user()->first_name, 0, 1) }}
                                </div>
                                @if($selectedSubject->id == auth()->id())
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-md border border-brand-100">
                                        <i class="bi bi-patch-check-fill text-brand-600 text-xs"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-gray-900 text-base leading-tight">My Records</p>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Main Profile</p>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Dependents Cards --}}
                @foreach($dependents as $dependent)
                    <a href="{{ route('patient.health-records.index', array_merge(request()->except(['page', 'subject_id']), ['subject_id' => $dependent->id])) }}" 
                       class="flex-shrink-0 relative group min-w-[240px] transition-all duration-500">
                        <div class="relative p-5 rounded-xl border-2 transition-all duration-500 {{ $selectedSubject->id == $dependent->id ? 'bg-blue-50 border-blue-200 shadow-lg shadow-blue-500/5' : 'bg-white border-gray-50 hover:border-blue-100' }}">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-lg {{ $selectedSubject->id == $dependent->id ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center font-black text-xl shadow-sm transition-all duration-500">
                                        {{ substr($dependent->first_name, 0, 1) }}
                                    </div>
                                    @if($selectedSubject->id == $dependent->id)
                                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-md border border-blue-100">
                                            <i class="bi bi-heart-fill text-blue-600 text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-black text-gray-900 text-lg leading-tight group-hover:text-blue-600 transition-colors">{{ $dependent->first_name }}</p>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Dependent</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @include('components.patient-quick-services')

    @php
        $profile = $selectedSubject->patientProfile;
        $prenatalRecords = $records->filter(fn($r) => str_contains(strtolower($r->service->name ?? ''), 'prenatal'))->sortBy('created_at');
        $hasActivePregnancy = $profile && $profile->edd && \Carbon\Carbon::parse($profile->edd)->isFuture();
    @endphp

    @if($prenatalRecords->count() > 0 || $hasActivePregnancy)
        <!-- Digital MCHR / Pregnancy Timeline -->
        <div class="bg-white rounded-2xl p-5 lg:p-6 border border-brand-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full blur-2xl -mr-16 -mt-16 opacity-30"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-brand-600 flex items-center justify-center text-white shadow-md">
                            <i class="bi bi-fetal-fill text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight leading-tight">Pregnancy Journey</h2>
                            <p class="text-[8px] font-bold text-brand-600 uppercase tracking-[0.2em] mt-0.5 flex items-center gap-2">
                                <i class="bi bi-patch-check-fill"></i> Digital Maternal & Child Health Record
                            </p>
                        </div>
                    </div>

                    @if($profile && $profile->edd)
                        <div class="flex items-center gap-4 bg-brand-50 px-5 py-3 rounded-lg border border-brand-100 shadow-inner">
                            <div class="text-center">
                                <p class="text-[7px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Estimated Due Date</p>
                                <p class="text-sm font-black text-brand-600">{{ \Carbon\Carbon::parse($profile->edd)->format('M d, Y') }}</p>
                            </div>
                            <div class="h-8 w-px bg-brand-200"></div>
                            <div class="text-center">
                                <p class="text-[7px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Current Progress</p>
                                <p class="text-lg font-black text-gray-900">
                                    {{ floor(\Carbon\Carbon::parse($profile->lmp)->diffInWeeks(now())) }} Weeks
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Timeline Progress Bar -->
                <div class="relative mb-12 sm:mb-16 pt-8">
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-100 rounded-full -translate-y-1/2"></div>
                    @if($profile && $profile->edd)
                        @php
                            $totalWeeks = 40;
                            $currentWeek = min(40, \Carbon\Carbon::parse($profile->lmp)->diffInWeeks(now()));
                            $progress = ($currentWeek / $totalWeeks) * 100;
                        @endphp
                        <div class="absolute top-1/2 left-0 h-1.5 bg-brand-500 rounded-full -translate-y-1/2 transition-all duration-1000 shadow-lg" style="width: {{ $progress }}%">
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-white border-4 border-brand-600 rounded-full shadow-md"></div>
                        </div>
                    @endif

                    <div class="relative flex justify-between gap-2">
                        @for($i = 1; $i <= 4; $i++)
                            @php
                                $weekTarget = [1 => 12, 2 => 24, 3 => 32, 4 => 38][$i];
                                $visit = $prenatalRecords->get($i - 1);
                                $isCompleted = $visit !== null;
                            @endphp
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl {{ $isCompleted ? 'bg-brand-600 text-white shadow-lg' : 'bg-white text-gray-300 border border-gray-100 shadow-sm' }} flex items-center justify-center z-10 transition-all duration-500 hover:scale-110">
                                    <i class="bi {{ $isCompleted ? 'bi-check-lg' : 'bi-circle' }} text-[10px] sm:text-sm"></i>
                                </div>
                                <div class="mt-3 text-center">
                                    <p class="text-[7px] sm:text-[9px] font-black uppercase tracking-widest {{ $isCompleted ? 'text-brand-600' : 'text-gray-400' }}">Visit #{{ $i }}</p>
                                    <p class="text-[8px] sm:text-[10px] font-bold text-gray-500 mt-0.5">Wk {{ $weekTarget }}</p>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Latest Visit Insights -->
                @if($latestPrenatal = $prenatalRecords->last())
                    @php $meta = $latestPrenatal->metadata ?? []; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 bg-gray-50/50 rounded-3xl border border-gray-100 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-brand-500 shadow-sm">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Fetal Heart Tone</p>
                                <p class="text-sm font-black text-gray-900">{{ $meta['fetal_heartbeat'] ?? '--' }} bpm</p>
                            </div>
                        </div>
                        <div class="p-6 bg-gray-50/50 rounded-3xl border border-gray-100 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-brand-500 shadow-sm">
                                <i class="bi bi-rulers"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Fundal Height</p>
                                <p class="text-sm font-black text-gray-900">{{ $meta['fundal_height'] ?? '--' }} cm</p>
                            </div>
                        </div>
                        <div class="p-6 bg-gray-50/50 rounded-3xl border border-gray-100 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-brand-500 shadow-sm">
                                <i class="bi bi-capsule-pill"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Supplements</p>
                                <p class="text-sm font-black text-gray-900 line-clamp-1">
                                    {{ isset($meta['supplements']) ? implode(', ', (array)$meta['supplements']) : 'None' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Records Section Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 pt-8">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[8px] font-black text-brand-600 uppercase tracking-[0.3em] bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-100/50">Clinical History</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                {{ $selectedSubject->id == auth()->id() ? 'My Personal Health' : $selectedSubject->first_name . "'s Records" }}
            </h2>
            <p class="text-gray-400 font-bold text-xs sm:text-sm mt-1.5 flex items-center gap-2">
                Viewing all verified medical entries for <span class="text-gray-800 font-black">{{ $selectedSubject->full_name }}</span>
            </p>
        </div>
        
        <form method="GET" class="w-full lg:w-[420px]">
            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
            <div class="flex gap-2 bg-white p-2 rounded-lg shadow-sm border border-gray-100 hover:border-brand-200 transition-colors group">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" 
                           class="block w-full pl-10 pr-3 py-3 border-none rounded-md bg-transparent placeholder-gray-400 focus:ring-0 font-bold text-xs" 
                           placeholder="Search diagnosis or staff...">
                </div>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-xs font-black rounded-md shadow-md text-white bg-brand-600 hover:bg-brand-700 transition-all duration-300 uppercase tracking-wider">
                    Search
                </button>
            </div>
        </form>
    </div>

    @if($records->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-10">
            @foreach($records as $record)
            <a href="{{ route('patient.health-records.show', $record->id) }}" class="group block bg-white rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-500 border border-gray-100 hover:border-brand-200 relative overflow-hidden">
                <!-- Top Status Accent -->
                <div class="h-1.5 bg-gradient-to-r {{ $record->is_verified ? 'from-emerald-400 to-emerald-500' : 'from-orange-400 to-orange-500' }}"></div>
                
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-lg {{ $record->is_verified ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }} flex items-center justify-center text-lg shadow-inner">
                                <i class="bi {{ $record->is_verified ? 'bi-patch-check-fill' : 'bi-clock-history' }}"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-gray-900 line-clamp-1 leading-tight">
                                    {{ $record->diagnosis ?: 'Medical Consultation' }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <i class="bi bi-calendar3 text-[8px] text-gray-400"></i>
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.05em]">
                                        {{ $record->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-600 group-hover:text-white transition-all duration-500 shadow-sm flex-shrink-0">
                            <i class="bi bi-arrow-right text-sm"></i>
                        </div>
                    </div>

                    @if($record->consultation)
                        <div class="bg-gray-50/50 rounded-lg p-4 mb-4 border border-gray-100/50 group-hover:bg-white group-hover:border-brand-100 transition-colors">
                            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed font-bold">
                                {{ $record->consultation }}
                            </p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 text-[10px] shadow-sm">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <div>
                                <p class="text-[7px] font-black text-gray-400 uppercase tracking-widest">Staff</p>
                                <span class="text-[9px] font-black text-gray-700">
                                    @if($record->verifier)
                                        Dr. {{ $record->verifier->last_name }}
                                    @elseif($record->creator)
                                        {{ $record->creator->full_name }}
                                    @else
                                        Barangay Staff
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                            <span class="w-1 h-1 rounded-full {{ $record->is_verified ? 'bg-emerald-500' : 'bg-orange-500' }}"></span>
                            <span class="text-[7px] font-black text-gray-500 uppercase tracking-widest">{{ $record->is_verified ? 'Verified' : 'Pending' }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $records->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-100 shadow-sm">
            <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center mx-auto mb-4 text-gray-300 border border-gray-100 shadow-inner">
                <i class="bi bi-clipboard2-pulse text-3xl"></i>
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-1">No Health Records Found</h3>
            <p class="text-gray-400 font-bold text-xs max-w-sm mx-auto uppercase tracking-widest leading-loose">
                You don't have any medical entries recorded for this profile yet.
            </p>
        </div>
    @endif

</div>
@endsection
