@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
@php
    $prefix = match(auth()->user()->role) {
        'admin' => 'admin',
        'health_worker' => 'healthworker',
        'doctor' => 'doctor',
        'midwife' => 'midwife',
        default => 'patient',
    };
@endphp
<div class="py-8 space-y-10" x-data="{ search: '' }">

    {{-- Breadcrumbs & Top Meta --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-[0.2em]">
                <li class="inline-flex items-center">
                    <a href="{{ route($prefix . '.dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center gap-2">
                        <i class="bi bi-house-door"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center text-gray-400">
                        <i class="bi bi-chevron-right mx-2 text-[8px] opacity-50"></i>
                        <span class="text-brand-600">Announcements</span>
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

    {{-- Top-Aligned Header & Search Section --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full blur-2xl -mr-16 -mt-16 opacity-30 group-hover:opacity-60 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-2.5">
                    <i class="bi bi-megaphone-fill text-xs"></i>
                    <span class="text-[8px] font-black uppercase tracking-widest">Latest Updates</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-tight mb-1.5 sm:mb-2">
                    Health <span class="text-brand-600">Advisories</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed mb-4 lg:mb-0">
                    Stay informed with real-time community health alerts and vaccination schedules.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:max-w-xl">
                {{-- Search Bar --}}
                <div class="relative flex-grow w-full group/search">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within/search:text-brand-500 transition-colors">
                        <i class="bi bi-search text-xs"></i>
                    </div>
                    <input type="text" x-model="search" 
                        class="block w-full pl-9 pr-3 py-2 bg-gray-50 border-none rounded-lg focus:ring-4 focus:ring-brand-500/10 focus:bg-white text-xs font-bold text-gray-900 placeholder-gray-400 transition-all shadow-inner"
                        placeholder="Search advisories...">
                </div>

                {{-- Quick Stats (Compact) --}}
                <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                    <div class="px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 flex flex-col items-center justify-center min-w-[70px]">
                        <span class="text-[7px] font-black text-gray-400 uppercase tracking-tighter">Active</span>
                        <span class="text-xs font-black text-gray-900">{{ $announcements->total() }}</span>
                    </div>
                    <div class="px-3 py-1.5 rounded-lg bg-red-50 border border-red-100 flex flex-col items-center justify-center min-w-[70px]">
                        <span class="text-[7px] font-black text-red-400 uppercase tracking-tighter">New</span>
                        <span class="text-xs font-black text-red-600">
                            {{ $announcements->filter(fn($a) => $a->created_at->diffInDays() < 3)->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Hub (Removed as it's now integrated above) --}}

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
                    <a href="{{ route($prefix . '.announcements.show', $announcement->id) }}" 
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
                    There are no posted advisories at the moment. Check back later for updates.
                </p>
                <a href="{{ route($prefix . '.dashboard') }}" class="inline-flex items-center gap-3 px-10 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-brand-600 transition-all shadow-xl shadow-gray-200">
                    Return Dashboard <i class="bi bi-house"></i>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
