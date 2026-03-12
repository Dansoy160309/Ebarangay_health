@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-megaphone-fill"></i> Announcements
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Manage news and updates for the barangay community.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.announcements.create') }}" 
                   class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-brand-600 font-bold shadow-sm hover:bg-brand-50 hover:scale-105 transition-all transform group">
                    <div class="w-6 h-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center mr-2 group-hover:bg-brand-200 transition">
                        <i class="bi bi-plus-lg text-sm"></i>
                    </div>
                    Create Announcement
                </a>
            </div>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl flex items-start justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="bi bi-check-lg text-lg"></i>
                </div>
                <div>
                    <h3 class="text-green-800 font-bold">Success</h3>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
            <button @click="show = false" class="text-green-500 hover:text-green-700 transition rounded-lg p-1 hover:bg-green-100">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    {{-- Content Grid --}}
    @if($announcements->count())
        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($announcements as $announcement)
                <article class="flex flex-col bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-brand-100 transition-all duration-300 group relative hover:-translate-y-0.5">
                    {{-- Status Badge --}}
                    <div class="absolute top-4 right-4 z-10">
                        @if($announcement->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200 shadow-sm">
                                <i class="bi bi-archive-fill mr-1.5 text-gray-500"></i>
                                Archived
                            </span>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-1">
                            <i class="bi bi-calendar3 text-brand-400"></i>
                            {{ $announcement->created_at->format('M d, Y') }}
                        </div>
                        
                        <h3 class="font-bold text-xl text-gray-900 mb-3 group-hover:text-brand-600 transition-colors line-clamp-2">
                            {{ $announcement->title }}
                        </h3>
                        
                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4 flex-1">
                            {{ \Illuminate\Support\Str::limit($announcement->message, 120) }}
                        </p>

                        @if($announcement->expires_at)
                            <div class="mt-auto pt-3 border-t border-gray-50">
                                <p class="text-xs text-orange-500 flex items-center font-medium bg-orange-50 px-2 py-1 rounded-lg w-fit">
                                    <i class="bi bi-clock-history mr-1.5"></i> Expires: {{ $announcement->expires_at->format('M d, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Card Footer --}}
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center">
                        <a href="{{ route('admin.announcements.show', $announcement->id) }}" 
                           class="text-brand-600 font-bold text-sm hover:text-brand-700 flex items-center group/link transition-colors">
                            Read Details <i class="bi bi-arrow-right ml-1 transition-transform group-hover/link:translate-x-1"></i>
                        </a>
                        <div class="flex gap-2">
                             <a href="{{ route('admin.announcements.edit', $announcement->id) }}" 
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all"
                                title="Edit Announcement">
                                <i class="bi bi-pencil-fill text-sm"></i>
                             </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $announcements->links() }}
        </div>
    @else
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-24 h-24 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-6 text-brand-200">
                <i class="bi bi-megaphone-fill text-5xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900">No announcements yet</h3>
            <p class="text-gray-500 mt-2 mb-8 max-w-md mx-auto">Get started by creating your first announcement to share important news and updates with the community.</p>
            <a href="{{ route('admin.announcements.create') }}" 
               class="inline-flex items-center px-6 py-3 rounded-xl bg-brand-600 text-white font-bold shadow-lg shadow-brand-600/30 hover:bg-brand-700 hover:scale-105 transition-all transform">
               <i class="bi bi-plus-lg mr-2"></i> Create First Announcement
            </a>
        </div>
    @endif

</div>
@endsection
