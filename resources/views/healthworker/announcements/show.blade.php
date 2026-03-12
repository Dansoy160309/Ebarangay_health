{{-- resources/views/healthworker/announcements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Announcement Details')

@extends('layouts.app')

@section('title', 'Announcement Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-12 py-8">

    {{-- Navigation & Quick Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('healthworker.announcements.index') }}" 
           class="inline-flex items-center gap-3 text-xs font-black text-gray-400 hover:text-brand-600 uppercase tracking-[0.2em] transition-all group">
            <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center shadow-sm group-hover:border-brand-200 group-hover:text-brand-600 transition-all">
                <i class="bi bi-arrow-left"></i> 
            </div>
            Back to All Advisories
        </a>
        
        <div class="flex items-center gap-3">
            <div class="flex -space-x-2">
                <div class="w-8 h-8 rounded-full bg-brand-100 border-2 border-white flex items-center justify-center text-brand-600 text-[10px] font-bold">H</div>
                <div class="w-8 h-8 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-blue-600 text-[9px] font-bold">E</div>
                <div class="w-8 h-8 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-indigo-600 text-[9px] font-bold">L</div>
            </div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Public Advisory</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[3.5rem] shadow-sm border border-gray-100 overflow-hidden relative">
                {{-- Dynamic Accent --}}
                <div class="h-4 bg-gradient-to-r from-brand-500 via-brand-400 to-brand-600"></div>
                
                <div class="p-8 sm:p-12 lg:p-16">
                    {{-- Status Badge --}}
                    @if($announcement->created_at->diffInDays() < 3)
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-red-50 text-red-600 border border-red-100 mb-8 animate-bounce">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em]">New Release</span>
                        </div>
                    @endif

                    <h1 class="text-4xl sm:text-5xl font-black text-gray-900 leading-[1.1] mb-10 tracking-tight">
                        {{ $announcement->title }}
                    </h1>

                    {{-- Metadata Bar --}}
                    <div class="flex flex-wrap items-center gap-8 mb-12 py-8 border-y border-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                                <i class="bi bi-calendar-event-fill text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-0.5">Published</p>
                                <p class="text-sm font-black text-gray-900">{{ $announcement->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                                <i class="bi bi-clock-history text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-0.5">Time</p>
                                <p class="text-sm font-black text-gray-900">{{ $announcement->created_at->format('h:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Message Body --}}
                    <div class="prose prose-blue max-w-none">
                        <div class="text-gray-600 leading-[2.2] font-medium text-lg whitespace-pre-line">
                            {{ $announcement->message }}
                        </div>
                    </div>
                </div>
                
                {{-- Advisory Footer --}}
                <div class="bg-gray-50/80 px-8 sm:px-12 py-10 border-t border-gray-100">
                    <div class="flex items-start gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-brand-500 border border-gray-100 shadow-sm shrink-0">
                            <i class="bi bi-info-circle-fill text-2xl"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Community Health Notice</p>
                            <p class="text-sm font-bold text-gray-700 leading-relaxed">
                                @if($announcement->expires_at)
                                    This advisory remains active and valid until <span class="text-brand-600">{{ \Carbon\Carbon::parse($announcement->expires_at)->format('F d, Y') }}</span>. 
                                @endif
                                Please follow the instructions provided to ensure community safety.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-8">
            {{-- Quick Info Card --}}
            <div class="bg-gray-900 rounded-[3rem] p-8 text-white shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500 rounded-full blur-3xl opacity-20 -mr-16 -mt-16 group-hover:opacity-30 transition-opacity"></div>
                <h4 class="text-lg font-black mb-6 flex items-center gap-3">
                    <i class="bi bi-patch-question-fill text-brand-400"></i>
                    Need Help?
                </h4>
                <p class="text-gray-400 text-sm leading-relaxed mb-8 font-medium">
                    If you have questions regarding this health advisory, please contact the Barangay Health Center immediately.
                </p>
                <a href="{{ route('healthworker.dashboard') }}" class="block w-full py-4 bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl text-center text-xs font-black uppercase tracking-[0.2em] transition-all">
                    Contact Center
                </a>
            </div>

            {{-- Recent Alerts --}}
            @php
                $recent = \App\Models\Announcement::where('status', 'active')
                    ->where('id', '!=', $announcement->id)
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp

            @if($recent->count())
                <div class="space-y-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] ml-4">Recent Alerts</h4>
                    <div class="space-y-4">
                        @foreach($recent as $item)
                            <a href="{{ route('healthworker.announcements.show', $item->id) }}" class="block p-6 bg-white border border-gray-100 rounded-[2rem] shadow-sm hover:shadow-lg hover:border-brand-200 transition-all group">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">{{ $item->created_at->format('M d, Y') }}</span>
                                <h5 class="text-sm font-black text-gray-900 group-hover:text-brand-600 transition-colors line-clamp-2">{{ $item->title }}</h5>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom Nav --}}
    <div class="pt-12 border-t border-gray-100 text-center">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em]">&copy; {{ date('Y') }} E-Barangay Health System</p>
    </div>

</div>
@endsection
