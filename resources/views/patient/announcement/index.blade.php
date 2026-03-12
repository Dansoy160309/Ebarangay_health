    @extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="space-y-6">

    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="bi bi-megaphone text-brand-600"></i> Announcements
    </h2>

    @if($announcements->count())
        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($announcements as $announcement)
                <div class="border border-gray-100 rounded-xl p-5 shadow-sm bg-white hover:shadow-md transition flex flex-col justify-between hover:scale-[1.02] duration-200">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 mb-2 group-hover:text-brand-600 transition">{{ $announcement->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 leading-relaxed">{{ \Illuminate\Support\Str::limit($announcement->message, 120) }}</p>
                        <div class="flex items-center text-xs text-gray-400 gap-1">
                            <i class="bi bi-clock"></i>
                            <span>Posted: {{ $announcement->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('patient.announcements.show', $announcement->id) }}" 
                       class="mt-5 block w-full bg-brand-50 text-brand-700 hover:bg-brand-600 hover:text-white px-4 py-2.5 rounded-lg text-center text-sm font-semibold transition border border-brand-100">
                        Read More
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 mt-4">No announcements found.</p>
    @endif

</div>
@endsection
