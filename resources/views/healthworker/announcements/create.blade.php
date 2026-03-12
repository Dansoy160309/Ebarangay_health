{{-- resources/views/healthworker/announcements/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('healthworker.announcements.index') }}" class="text-brand-600 hover:text-brand-800 flex items-center">
            &larr; Back to Announcements
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New Announcement</h2>

        <form action="{{ route('healthworker.announcements.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border-gray-300 focus:ring-brand-500 focus:border-brand-500 shadow-sm"
                        placeholder="e.g., Vaccination Schedule for Kids">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" required
                        class="w-full rounded-lg border-gray-300 focus:ring-brand-500 focus:border-brand-500 shadow-sm">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active (Visible)</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived (Hidden)</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Message --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" id="message" rows="5" required
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        placeholder="Write the details of the announcement here...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Expiry Date --}}
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expires On (Optional)</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <p class="text-gray-500 text-xs mt-1">Leave blank if the announcement should not expire automatically.</p>
                    @error('expires_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2 rounded-lg font-medium shadow-sm transition">
                        Publish Announcement
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
