@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                        <i class="bi bi-megaphone-fill"></i> Announcement Details
                    </h1>
                    @if($announcement->status === 'active')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white border border-white/20 backdrop-blur-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 mr-1.5 animate-pulse"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-white border border-white/10 backdrop-blur-sm">
                            <i class="bi bi-archive-fill mr-1.5 opacity-70"></i>
                            Archived
                        </span>
                    @endif
                </div>
                <p class="text-brand-100 text-lg font-medium leading-relaxed">{{ $announcement->title }}</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.announcements.index') }}" 
                   class="inline-flex items-center px-4 py-2 rounded-xl bg-white/10 text-white font-medium hover:bg-white/20 border border-white/20 transition-all text-sm">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Back
                </a>
                <a href="{{ route('admin.announcements.edit', $announcement->id) }}" 
                   class="inline-flex items-center px-4 py-2 rounded-xl bg-white text-brand-600 font-bold hover:bg-brand-50 shadow-sm transition-all text-sm">
                    <i class="bi bi-pencil-square mr-2"></i>
                    Edit
                </a>
            </div>
        </div>
    </div>

    {{-- Content Card --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        {{-- Meta Bar --}}
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold border border-brand-200">
                        {{ substr($announcement->creator->full_name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $announcement->creator->full_name }}</p>
                        <p class="text-xs text-gray-500 font-medium">Posted by {{ $announcement->creator->role }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-sm">
                <div class="flex items-center text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <i class="bi bi-calendar3 mr-2 text-brand-400"></i>
                    <span class="font-medium">{{ $announcement->created_at->format('F d, Y') }}</span>
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-gray-400">{{ $announcement->created_at->format('h:i A') }}</span>
                </div>
                
                @if($announcement->expires_at)
                    <div class="flex items-center text-orange-600 bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-100 shadow-sm">
                        <i class="bi bi-clock-history mr-2"></i>
                        <span class="font-bold">Expires: {{ $announcement->expires_at->format('M d, Y') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Message Body --}}
        <div class="p-8 sm:p-10">
            <div class="prose max-w-none text-gray-700 leading-relaxed text-lg">
                {!! nl2br(e($announcement->message)) !!}
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-end">
             <button type="button" onclick="if(confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) document.getElementById('delete-form').submit();" 
                    class="text-red-600 hover:text-red-700 font-bold text-sm flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-red-50 transition-colors border border-transparent hover:border-red-100">
                <i class="bi bi-trash"></i> Delete Announcement
            </button>
            <form id="delete-form" action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection
