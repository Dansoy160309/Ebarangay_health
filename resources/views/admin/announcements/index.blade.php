@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-2xl shadow-lg p-5 sm:p-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <i class="bi bi-megaphone-fill text-lg"></i> Announcements
                </h1>
                <p class="text-brand-100 mt-1.5 text-sm">Manage news and updates for the barangay community.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.announcements.create') }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white text-brand-600 font-bold shadow-sm hover:bg-brand-50 hover:scale-105 transition-all transform group">
                    <div class="w-5 h-5 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center mr-1.5 group-hover:bg-brand-200 transition text-[10px]">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    Create Announcement
                </a>
            </div>
        </div>
    </div>

    {{-- Session alerts are handled globally in the app layout --}}

    {{-- Content Grid --}}
    @if($announcements->count())
        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($announcements as $announcement)
                <article class="flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-brand-100 transition-all duration-300 group relative hover:-translate-y-0.5">
                    {{-- Status Badge --}}
                    <div class="absolute top-3 right-3 z-10">
                        @if($announcement->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-bold bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                <span class="w-1 h-1 rounded-full bg-green-500 mr-1 animate-pulse"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-bold bg-gray-100 text-gray-800 border border-gray-200 shadow-sm">
                                <i class="bi bi-archive-fill mr-1 text-gray-500 text-[7px]"></i>
                                Archived
                            </span>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="text-gray-400 text-[8px] font-bold uppercase tracking-tighter mb-1.5 flex items-center gap-0.5">
                            <i class="bi bi-calendar3 text-brand-400 text-[7px]"></i>
                            {{ $announcement->created_at->format('M d, Y') }}
                        </div>
                        
                        <h3 class="font-bold text-sm text-gray-900 mb-2 group-hover:text-brand-600 transition-colors line-clamp-2">
                            {{ $announcement->title }}
                        </h3>
                        
                        <p class="text-gray-500 text-xs leading-relaxed line-clamp-2 mb-2.5 flex-1">
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
                             <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Delete this announcement? This action cannot be undone.')" class="inline">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" 
                                         class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-red-200 transition-all"
                                         title="Delete Announcement">
                                     <i class="bi bi-trash-fill text-sm"></i>
                                 </button>
                             </form>
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
