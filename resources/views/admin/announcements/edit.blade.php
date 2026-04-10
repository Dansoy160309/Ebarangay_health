@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-pencil-square"></i> Edit Announcement
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Update the details for this announcement.</p>
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
        <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="bi bi-type-h1"></i>
                            </div>
                            <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}" required maxlength="120"
                                class="block w-full pl-10 pr-4 py-3 bg-white text-gray-900 border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                                   placeholder="Short, clear announcement title">
                        </div>
                        <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                            <span>Example: Vaccination Schedule for October</span>
                            <span id="title-count"></span>
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-bold text-gray-700 mb-2">Message</label>
                        <div class="relative">
                            <textarea name="message" id="message" rows="7" required
                                      class="block w-full p-4 bg-white text-gray-900 border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                                      placeholder="Write the full content of your announcement here...">{{ old('message', $announcement->message) }}</textarea>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                            <span>Markdown supported: headings, bold, bullet lists.</span>
                            <span id="message-count"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-4">Status</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-200 transition-all group">
                                    <input type="radio" name="status" value="active" class="peer sr-only" {{ (old('status') ?? $announcement->status) == 'active' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl mb-3 peer-checked:bg-green-600 peer-checked:text-white transition-colors">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                    <span class="font-bold text-gray-900 peer-checked:text-green-700">Active</span>
                                    <span class="text-xs text-gray-500 text-center mt-1">Visible to patients in the app</span>
                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-green-500 rounded-xl pointer-events-none"></div>
                                </label>

                                <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 hover:border-gray-200 transition-all group">
                                    <input type="radio" name="status" value="archived" class="peer sr-only" {{ (old('status') ?? $announcement->status) == 'archived' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xl mb-3 peer-checked:bg-gray-600 peer-checked:text-white transition-colors">
                                        <i class="bi bi-archive"></i>
                                    </div>
                                    <span class="font-bold text-gray-900 peer-checked:text-gray-700">Draft</span>
                                    <span class="text-xs text-gray-500 text-center mt-1">Keeps announcement hidden for now</span>
                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-gray-500 rounded-xl pointer-events-none"></div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-bold text-gray-700 mb-2">Expires At <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                    <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', $announcement->expires_at ? $announcement->expires_at->format('Y-m-d') : '') }}"
                                        class="block w-full pl-10 pr-4 py-3 bg-white text-gray-900 border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="bi bi-info-circle mr-1"></i> Announcement will be archived automatically after this date.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-2xl border border-gray-100 h-full p-5 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-500 tracking-wide">
                                <div class="w-7 h-7 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center">
                                    <i class="bi bi-megaphone-fill text-sm"></i>
                                </div>
                                <span>Announcement Preview</span>
                            </div>
                            <span id="announcement-preview-status-pill" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-800 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                <span id="announcement-preview-status-text">{{ (old('status') ?? $announcement->status) === 'archived' ? 'Draft' : 'Active' }}</span>
                            </span>
                        </div>

                        <div class="text-xs text-gray-400 mb-2 flex items-center gap-1">
                            <i class="bi bi-calendar3 text-brand-400"></i>
                            <span>{{ $announcement->created_at->format('M d, Y') }}</span>
                        </div>

                        <h2 id="announcement-preview-title" class="font-bold text-lg text-gray-900 mb-2 line-clamp-3">
                            {{ old('title', $announcement->title) ?: 'Your announcement title' }}
                        </h2>

                        <p id="announcement-preview-body" class="text-sm text-gray-600 leading-relaxed line-clamp-6 flex-1">
                            {{ old('message', $announcement->message) ?: 'Your announcement message will appear here.' }}
                        </p>

                        @if($announcement->expires_at)
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <p class="text-[11px] text-orange-600 flex items-center font-medium bg-orange-50 px-2 py-1 rounded-lg w-fit">
                                    <i class="bi bi-clock-history mr-1.5"></i> Expires: {{ $announcement->expires_at->format('M d, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                {{-- Delete Action --}}
                 <button type="button" onclick="if(confirm('Are you sure you want to delete this announcement?')) document.getElementById('delete-form').submit();" 
                        class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-red-50 transition">
                    <i class="bi bi-trash"></i> Delete Announcement
                </button>

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.announcements.index') }}" class="px-6 py-3 rounded-xl text-gray-600 font-medium hover:bg-gray-100 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-brand-600 text-white font-bold shadow-lg shadow-brand-600/30 hover:bg-brand-700 hover:-translate-y-0.5 transition-all transform">
                        Update Announcement
                    </button>
                </div>
            </div>
        </form>
        
        <form id="delete-form" action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var titleInput = document.getElementById('title');
    var messageInput = document.getElementById('message');
    var statusInputs = document.querySelectorAll('input[name="status"]');
    var titleCount = document.getElementById('title-count');
    var messageCount = document.getElementById('message-count');
    var previewTitle = document.getElementById('announcement-preview-title');
    var previewBody = document.getElementById('announcement-preview-body');
    var previewStatusText = document.getElementById('announcement-preview-status-text');
    var previewStatusPill = document.getElementById('announcement-preview-status-pill');

    function updateTitle() {
        if (!titleInput) return;
        var value = titleInput.value || '';
        previewTitle.textContent = value.trim() || 'Your announcement title';
        if (titleCount) {
            titleCount.textContent = value.length + ' / 120';
        }
    }

    function updateMessage() {
        if (!messageInput) return;
        var value = messageInput.value || '';
        previewBody.textContent = value.trim() || 'Your announcement message will appear here.';
        if (messageCount) {
            messageCount.textContent = value.length + ' characters';
        }
    }

    function updateStatus() {
        if (!previewStatusText || !previewStatusPill) return;
        var selected = null;
        statusInputs.forEach(function (input) {
            if (input.checked) {
                selected = input.value;
            }
        });
        if (!selected) return;
        if (selected === 'active') {
            previewStatusText.textContent = 'Active';
            previewStatusPill.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-800 border border-green-200';
        } else {
            previewStatusText.textContent = 'Draft';
            previewStatusPill.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-800 border border-gray-200';
        }
    }

    if (titleInput) {
        titleInput.addEventListener('input', updateTitle);
    }
    if (messageInput) {
        messageInput.addEventListener('input', updateMessage);
    }
    statusInputs.forEach(function (input) {
        input.addEventListener('change', updateStatus);
    });

    updateTitle();
    updateMessage();
    updateStatus();
});
</script>
@endsection
