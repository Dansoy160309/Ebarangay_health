@extends('layouts.app')

@section('title', 'Announcement Details')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <a href="{{ route('patient.announcements.index') }}" class="text-blue-600 underline text-sm">&larr; Back to Announcements</a>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold text-blue-700 mb-4">{{ $announcement->title }}</h2>
        <p class="text-gray-600 mb-4">Posted: {{ $announcement->created_at->format('M d, Y') }}</p>
        <p class="text-gray-700 leading-relaxed">{{ $announcement->message }}</p>
    </div>

</div>
@endsection
