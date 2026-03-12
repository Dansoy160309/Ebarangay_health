@extends('layouts.app')

@section('title','Announcements')

@section('content')
<h1 class="text-xl font-bold mb-4">Announcements</h1>
<a href="{{ route('announcements.create') }}" class="bg-yellow-500 text-white px-3 py-1 rounded mb-4 inline-block">New Announcement</a>

<ul class="bg-white rounded shadow divide-y">
  @foreach($announcements as $a)
    <li class="p-4">
      <h3 class="font-semibold">{{ $a->title }}</h3>
      <p class="text-sm text-gray-600">{{ Str::limit($a->message, 200) }}</p>
      <div class="text-xs mt-2 text-gray-500">By {{ $a->creator->full_name ?? 'System' }} | {{ $a->created_at->format('M d, Y') }}</div>
    </li>
  @endforeach
</ul>

<div class="mt-4">{{ $announcements->links() }}</div>
@endsection
