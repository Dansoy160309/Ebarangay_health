@extends('layouts.app')

@section('title', 'Health Advisories')

@section('content')
<div class="py-8 space-y-10" x-data="{ search: '' }">

    {{-- Breadcrumbs & Top Meta --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-[0.2em]">
                <li class="inline-flex items-center">
                    <a href="{{ route('patient.dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center gap-2">
                        <i class="bi bi-house-door"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center text-gray-400">
                        <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                        <span class="text-brand-600">Health Advisories</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex items-center gap-4 bg-white px-5 py-2.5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex -space-x-2">
                <div class="w-7 h-7 rounded-full bg-brand-100 border-2 border-white flex items-center justify-center text-brand-600 text-[9px] font-bold">H</div>
                <div class="w-7 h-7 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-blue-600 text-[9px] font-bold">E</div>
                <div class="w-7 h-7 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-indigo-600 text-[9px] font-bold">L</div>
            </div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Public Safety Updates</span>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="bg-white rounded-2xl p-5 lg:p-6 border border-gray-100 shadow-sm relative overflow-hidden">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-full blur-3xl -mr-20 -mt-20 opacity-30"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-blue-50 rounded-full blur-3xl -ml-16 -mb-16 opacity-20"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-md">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-brand-50 text-brand-600 border border-brand-100 mb-4">
                    <i class="bi bi-megaphone-fill text-sm"></i>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Latest Updates</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight leading-tight">
                    Health <span class="text-brand-600 underline decoration-brand-200 decoration-1 underline-offset-2">Advisories</span>
                </h1>
                <p class="text-gray-500 font-medium mt-4 text-sm leading-relaxed">
                    Stay informed with real-time community health alerts, vaccination schedules, and medical missions organized by your barangay.
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full lg:w-auto">
                <div class="bg-gray-50/80 backdrop-blur-sm p-5 rounded-2xl border border-gray-100 shadow-inner flex flex-col justify-center">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                        <i class="bi bi-collection"></i> Total Active
                    </span>
                    <span class="text-2xl font-black text-gray-900 tracking-tight">{{ $announcements->total() }}</span>
                </div>
                <div class="bg-red-50/80 backdrop-blur-sm p-5 rounded-2xl border border-red-100 shadow-inner flex flex-col justify-center">
                    <span class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                        <i class="bi bi-lightning-charge-fill"></i> New Release
                    </span>
                    <span class="text-2xl font-black text-red-600 tracking-tight">
                        {{ $announcements->filter(fn($a) => $a->created_at->diffInDays() < 3)->count() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Hub --}}
    <div class="bg-white rounded-[2.5rem] p-4 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-center group/search transition-all duration-500 hover:shadow-lg focus-within:ring-4 focus-within:ring-brand-50">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-7 flex items-center pointer-events-none text-gray-400 group-focus-within/search:text-brand-500 transition-colors">
                <i class="bi bi-search text-lg"></i>
            </div>
            <input type="text" x-model="search" 
                class="block w-full pl-16 pr-8 py-5 bg-gray-50 border-none rounded-[1.75rem] focus:ring-0 focus:bg-white text-base font-bold text-gray-900 placeholder-gray-400 transition-all"
                placeholder="Search advisories by title or keyword...">
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
            <button class="w-full md:w-auto px-10 py-5 bg-brand-600 text-white rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all active:scale-95 flex items-center justify-center gap-3">
                Search <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>

    @include('components.patient-quick-services')

    @if($announcements->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($announcements as $announcement)
                <div x-show="search === '' || 
                            '{{ addslashes(strtolower($announcement->title)) }}'.includes(search.toLowerCase()) || 
                            '{{ addslashes(strtolower(Str::limit($announcement->message, 100))) }}'.includes(search.toLowerCase())"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="h-full">
                    <a href="{{ route('patient.announcements.show', $announcement) }}" 
                       class="group block bg-white rounded-[3.5rem] border border-gray-100 p-10 shadow-sm hover:shadow-2xl hover:border-brand-200 transition-all duration-500 relative overflow-hidden flex flex-col h-full transform hover:-translate-y-3">
                        
                        {{-- New Badge Pulse --}}
                        @if($announcement->created_at->diffInDays() < 3)
                            <div class="absolute top-10 right-10 flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                <span class="text-[9px] font-black text-red-600 uppercase tracking-widest">New</span>
                            </div>
                        @endif

                        <div class="mb-10">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="text-[9px] font-black text-brand-600 uppercase tracking-[0.2em] bg-brand-50 px-4 py-1.5 rounded-full border border-brand-100">
                                    <i class="bi bi-shield-check mr-1.5"></i> Health Alert
                                </span>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                                    <i class="bi bi-calendar3"></i> {{ $announcement->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 leading-tight group-hover:text-brand-600 transition-colors line-clamp-2 mb-5 tracking-tight">
                                {{ $announcement->title }}
                            </h3>
                            <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 font-medium">
                                {{ $announcement->message }}
                            </p>
                        </div>

                        <div class="mt-auto pt-8 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-all shadow-inner border border-gray-100/50">
                                    <i class="bi bi-clock-fill text-xs"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-gray-600">{{ $announcement->created_at->format('h:i A') }}</span>
                            </div>
                            <span class="text-[10px] font-black text-brand-600 uppercase tracking-[0.2em] flex items-center gap-2 group-hover:gap-4 transition-all">
                                Read More <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        
        <div class="mt-20 pb-16">
            {{ $announcements->links() }}
        </div>
    @else
        <div class="text-center py-40 bg-white rounded-[5rem] border-2 border-dashed border-gray-100 shadow-sm overflow-hidden relative">
            <div class="absolute top-0 right-0 w-80 h-80 bg-gray-50 rounded-full blur-3xl -mr-40 -mt-40"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-brand-50 rounded-full blur-3xl -ml-40 -mb-40"></div>
            
            <div class="relative z-10 max-w-md mx-auto px-6">
                <div class="w-28 h-28 bg-gray-50 rounded-[2.5rem] flex items-center justify-center text-gray-300 mx-auto mb-10 shadow-inner border border-gray-100 transform -rotate-6">
                    <i class="bi bi-megaphone text-6xl"></i>
                </div>
                <h3 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">No Active Advisories</h3>
                <p class="text-gray-400 font-medium leading-relaxed text-lg mb-10">
                    We'll notify you as soon as new health alerts or community updates are posted by the barangay health workers.
                </p>
                <a href="{{ route('patient.dashboard') }}" class="inline-flex items-center gap-3 px-10 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-brand-600 transition-all shadow-xl shadow-gray-200">
                    Return Dashboard <i class="bi bi-house"></i>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
