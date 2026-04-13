@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="w-full max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-2">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-1">Announcement Details</h1>
            <p class="text-sm text-gray-500 font-medium max-w-2xl">Review and manage this published update for the barangay community.</p>
        </div>
        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
            Full-width workspace
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-6 md:p-8 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-3">
                        <h2 class="text-3xl font-black tracking-tight text-white flex items-center gap-3">
                            <i class="bi bi-megaphone-fill"></i> Announcement Details
                        </h2>
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
                    <p class="text-brand-100 text-2xl font-bold leading-relaxed">{{ $announcement->title }}</p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold border border-brand-200">
                            {{ substr($announcement->creator->full_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $announcement->creator->full_name }}</p>
                            <p class="text-xs text-gray-500 font-medium">Posted by {{ $announcement->creator->role }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-sm">
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

                <div class="p-6 md:p-10">
                    <div class="prose max-w-none text-gray-700 leading-relaxed text-lg">
                        {!! nl2br(e($announcement->message)) !!}
                    </div>
                </div>
            </div>
        </div>

        <aside class="space-y-5 xl:sticky xl:top-6">
            <div class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-black text-gray-900 mb-3">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.announcements.index') }}"
                       class="block w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 text-sm font-black hover:bg-gray-100 transition text-center">
                        Back to Announcements
                    </a>
                    <a href="{{ route('admin.announcements.edit', $announcement->id) }}"
                       class="block w-full px-4 py-3 rounded-xl bg-brand-600 text-white text-sm font-black hover:bg-brand-700 transition text-center">
                        Edit Announcement
                    </a>
                    <button type="button" onclick="if(confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) document.getElementById('delete-form').submit();"
                            class="block w-full px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-black hover:bg-red-100 transition">
                        Delete Announcement
                    </button>
                </div>
            </div>

            <div class="rounded-[2rem] border border-gray-100 bg-gray-50 p-6 shadow-sm">
                <h3 class="text-base font-black text-gray-900 mb-2">Publishing Notes</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Use edit to update content or expiry. If an announcement is no longer relevant, archive or delete it to keep the feed clean.</p>
            </div>
        </aside>
    </div>

    <form id="delete-form" action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
