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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12 relative overflow-hidden bg-[#fcfcfd] font-sans">
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 right-0 w-[30rem] h-[30rem] bg-brand-50/20 rounded-full blur-3xl -mr-60 -mt-60 opacity-30 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[30rem] h-[30rem] bg-blue-50/20 rounded-full blur-3xl -ml-60 -mb-60 opacity-30 pointer-events-none"></div>

    <!-- Family Selection Hero -->
    <div class="bg-white rounded-[3.5rem] shadow-sm border border-gray-100 p-10 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full -mr-32 -mt-32 blur-[80px] opacity-40"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-3xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-inner border border-brand-100/50 transform hover:rotate-6 transition-transform duration-500">
                        <i class="bi bi-person-badge text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Medical Profiles</h2>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mt-1.5 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-400 animate-pulse"></span>
                            Select a profile to view history
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-gray-50 px-6 py-3 rounded-2xl border border-gray-100 shadow-inner self-start">
                    <div class="flex -space-x-2.5">
                        <div class="w-9 h-9 rounded-xl border-2 border-white bg-brand-400 flex items-center justify-center text-[10px] font-black text-white shadow-sm">
                            {{ substr(auth()->user()->first_name, 0, 1) }}
                        </div>
                        @foreach($dependents->take(3) as $dep)
                            <div class="w-9 h-9 rounded-xl border-2 border-white bg-blue-400 flex items-center justify-center text-[10px] font-black text-white shadow-sm">
                                {{ substr($dep->first_name, 0, 1) }}
                            </div>
                        @endforeach
                    </div>
                    <div class="h-6 w-px bg-gray-200"></div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        {{ $dependents->count() + 1 }} Profiles
                    </span>
                </div>
            </div>
            
            <div class="flex flex-nowrap overflow-x-auto pb-6 gap-6 scrollbar-hide -mx-2 px-2">
                {{-- Account Holder Card --}}
                <a href="{{ route('patient.health-records.index', array_merge(request()->except(['page', 'subject_id']), ['subject_id' => auth()->id()])) }}" 
                   class="flex-shrink-0 relative group min-w-[280px] transition-all duration-500">
                    <div class="relative p-8 rounded-[2.5rem] border-2 transition-all duration-500 {{ $selectedSubject->id == auth()->id() ? 'bg-brand-50 border-brand-200 shadow-xl shadow-brand-500/5 -translate-y-1' : 'bg-white border-gray-50 hover:border-brand-100 hover:shadow-lg' }}">
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div class="h-16 w-16 rounded-2xl {{ $selectedSubject->id == auth()->id() ? 'bg-brand-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center font-black text-2xl shadow-sm transition-all duration-500 group-hover:scale-105">
                                    {{ substr(auth()->user()->first_name, 0, 1) }}
                                </div>
                                @if($selectedSubject->id == auth()->id())
                                    <div class="absolute -top-2 -right-2 w-7 h-7 bg-white rounded-full flex items-center justify-center shadow-md border border-brand-100 animate-bounce-subtle">
                                        <i class="bi bi-patch-check-fill text-brand-600 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-gray-900 text-lg leading-tight group-hover:text-brand-600 transition-colors">My Records</p>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Main Profile</p>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Dependents Cards --}}
                @foreach($dependents as $dependent)
                    <a href="{{ route('patient.health-records.index', array_merge(request()->except(['page', 'subject_id']), ['subject_id' => $dependent->id])) }}" 
                       class="flex-shrink-0 relative group min-w-[280px] transition-all duration-500">
                        <div class="relative p-8 rounded-[2.5rem] border-2 transition-all duration-500 {{ $selectedSubject->id == $dependent->id ? 'bg-blue-50 border-blue-200 shadow-xl shadow-blue-500/5 -translate-y-1' : 'bg-white border-gray-50 hover:border-blue-100 hover:shadow-lg' }}">
                            <div class="flex items-center gap-5">
                                <div class="relative">
                                    <div class="h-16 w-16 rounded-2xl {{ $selectedSubject->id == $dependent->id ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center font-black text-2xl shadow-sm transition-all duration-500 group-hover:scale-105">
                                        {{ substr($dependent->first_name, 0, 1) }}
                                    </div>
                                    @if($selectedSubject->id == $dependent->id)
                                        <div class="absolute -top-2 -right-2 w-7 h-7 bg-white rounded-full flex items-center justify-center shadow-md border border-blue-100 animate-bounce-subtle">
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

    @php
        $profile = $selectedSubject->patientProfile;
        $prenatalRecords = $records->filter(fn($r) => str_contains(strtolower($r->service->name ?? ''), 'prenatal'))->sortBy('created_at');
        $hasActivePregnancy = $profile && $profile->edd && \Carbon\Carbon::parse($profile->edd)->isFuture();
    @endphp

    @if($prenatalRecords->count() > 0 || $hasActivePregnancy)
        <!-- Digital MCHR / Pregnancy Timeline -->
        <div class="bg-white rounded-[3.5rem] p-10 lg:p-14 border border-brand-100 shadow-xl shadow-brand-500/5 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-3xl bg-brand-600 flex items-center justify-center text-white shadow-xl shadow-brand-500/20">
                            <i class="bi bi-fetal-fill text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-gray-900 tracking-tight leading-tight">Pregnancy Journey</h2>
                            <p class="text-xs font-bold text-brand-600 uppercase tracking-[0.2em] mt-1.5 flex items-center gap-2">
                                <i class="bi bi-patch-check-fill"></i> Digital Maternal & Child Health Record
                            </p>
                        </div>
                    </div>

                    @if($profile && $profile->edd)
                        <div class="flex items-center gap-6 bg-brand-50/50 px-8 py-4 rounded-[2rem] border border-brand-100 shadow-inner">
                            <div class="text-center">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Estimated Due Date</p>
                                <p class="text-lg font-black text-brand-600">{{ \Carbon\Carbon::parse($profile->edd)->format('M d, Y') }}</p>
                            </div>
                            <div class="h-10 w-px bg-brand-200"></div>
                            <div class="text-center">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Current Progress</p>
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
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 pt-10">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="text-[10px] font-black text-brand-600 uppercase tracking-[0.3em] bg-brand-50 px-3 py-1.5 rounded-xl border border-brand-100/50">Clinical History</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                {{ $selectedSubject->id == auth()->id() ? 'My Personal Health' : $selectedSubject->first_name . "'s Records" }}
            </h2>
            <p class="text-gray-400 font-bold text-sm sm:text-base mt-2 flex items-center gap-2">
                Viewing all verified medical entries for <span class="text-gray-800 font-black underline decoration-brand-200 decoration-4 underline-offset-4">{{ $selectedSubject->full_name }}</span>
            </p>
        </div>
        
        <form method="GET" class="w-full lg:w-[480px]">
            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
            <div class="flex gap-3 bg-white p-2.5 rounded-[2.5rem] shadow-xl shadow-brand-500/5 border border-gray-100 hover:border-brand-200 transition-colors group">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400 group-hover:text-brand-500 transition-colors"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" 
                           class="block w-full pl-12 pr-4 py-4 border-none rounded-2xl bg-transparent placeholder-gray-400 focus:ring-0 font-bold text-sm" 
                           placeholder="Search diagnosis or staff...">
                </div>
                <button type="submit" class="inline-flex items-center px-8 py-4 border border-transparent text-xs font-black rounded-2xl shadow-xl shadow-brand-500/20 text-white bg-brand-600 hover:bg-brand-700 hover:-translate-y-0.5 transition-all duration-300 uppercase tracking-widest">
                    Search
                </button>
            </div>
        </form>
    </div>

    @if($records->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pb-10">
            @foreach($records as $record)
            <a href="{{ route('patient.health-records.show', $record->id) }}" class="group block bg-white rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-gray-100 hover:border-brand-200 relative overflow-hidden">
                <!-- Top Status Accent -->
                <div class="h-2 bg-gradient-to-r {{ $record->is_verified ? 'from-emerald-400 to-emerald-500' : 'from-orange-400 to-orange-500' }}"></div>
                
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl {{ $record->is_verified ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }} flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform duration-500">
                                <i class="bi {{ $record->is_verified ? 'bi-patch-check-fill' : 'bi-clock-history' }}"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 group-hover:text-brand-600 transition line-clamp-1 leading-tight">
                                    {{ $record->diagnosis ?: 'Medical Consultation' }}
                                </h3>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <i class="bi bi-calendar3 text-[10px] text-gray-400"></i>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">
                                        {{ $record->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-600 group-hover:text-white transition-all duration-500 shadow-sm">
                            <i class="bi bi-arrow-right text-xl"></i>
                        </div>
                    </div>

                    @if($record->consultation)
                        <div class="bg-gray-50/50 rounded-3xl p-6 mb-8 border border-gray-100/50 group-hover:bg-white group-hover:border-brand-100 transition-colors relative">
                            <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed font-bold">
                                {{ $record->consultation }}
                            </p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 text-xs shadow-sm">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Medical Staff</p>
                                <span class="text-[10px] font-black text-gray-700">
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

                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                            <span class="w-1.5 h-1.5 rounded-full {{ $record->is_verified ? 'bg-emerald-500' : 'bg-orange-500' }}"></span>
                            <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">{{ $record->is_verified ? 'Verified' : 'Pending' }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $records->links() }}
        </div>
    @else
        <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-gray-100 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-gray-300 border border-gray-100 shadow-inner">
                <i class="bi bi-clipboard2-pulse text-5xl"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">No Health Records Found</h3>
            <p class="text-gray-400 font-bold text-sm max-w-sm mx-auto uppercase tracking-widest leading-loose">
                You don't have any medical entries recorded for this profile yet.
            </p>
        </div>
    @endif

</div>
@endsection
