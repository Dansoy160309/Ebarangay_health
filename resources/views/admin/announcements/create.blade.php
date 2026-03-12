@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-megaphone-fill"></i> Create Announcement
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Share important updates and news with the community.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.announcements.index') }}" 
                   class="inline-flex items-center px-5 py-3 rounded-xl bg-white/10 text-white font-medium hover:bg-white/20 border border-white/20 transition-all">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Back to Announcements
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-exclamation-circle-fill text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.announcements.store') }}" method="POST" class="p-8 space-y-8">
            @csrf

            <div class="space-y-8">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-type-h1"></i>
                        </div>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               class="block w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                               placeholder="e.g. Vaccination Schedule for October">
                    </div>
                </div>

                {{-- Message --}}
                <div>
                    <label for="message" class="block text-sm font-bold text-gray-700 mb-2">Message</label>
                    <div class="relative">
                        <textarea name="message" id="message" rows="6" required
                                  class="block w-full p-4 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                                  placeholder="Write the full content of your announcement here...">{{ old('message') }}</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 text-right">Markdown formatting supported</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-4">Status</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Active Option --}}
                            <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-200 transition-all group">
                                <input type="radio" name="status" value="active" class="peer sr-only" {{ old('status') == 'active' ? 'checked' : '' }} checked>
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl mb-3 peer-checked:bg-green-600 peer-checked:text-white transition-colors">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <span class="font-bold text-gray-900 peer-checked:text-green-700">Active</span>
                                <span class="text-xs text-gray-500 text-center mt-1">Visible to public</span>
                                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-green-500 rounded-xl pointer-events-none"></div>
                            </label>

                            {{-- Archived Option --}}
                            <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 hover:border-gray-200 transition-all group">
                                <input type="radio" name="status" value="archived" class="peer sr-only" {{ old('status') == 'archived' ? 'checked' : '' }}>
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xl mb-3 peer-checked:bg-gray-600 peer-checked:text-white transition-colors">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <span class="font-bold text-gray-900 peer-checked:text-gray-700">Draft</span>
                                <span class="text-xs text-gray-500 text-center mt-1">Hidden from public</span>
                                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-gray-500 rounded-xl pointer-events-none"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Expiry Date --}}
                    <div>
                        <label for="expires_at" class="block text-sm font-bold text-gray-700 mb-2">Expires At <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                                   class="block w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="bi bi-info-circle mr-1"></i> Announcement will be automatically archived after this date.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                <a href="{{ route('admin.announcements.index') }}" class="px-6 py-3 rounded-xl text-gray-600 font-medium hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-brand-600 text-white font-bold shadow-lg shadow-brand-600/30 hover:bg-brand-700 hover:-translate-y-0.5 transition-all transform">
                    Create Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
