@extends('layouts.app')

@section('title', 'Health Records Management')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">

    {{-- Step 1: Search Section --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="bi bi-folder2-open text-brand-600"></i> Health Records (Read-Only)
        </h1>
        <p class="text-gray-500 mb-6">Search for a patient to view their health records.</p>

        <form method="GET" action="{{ route('admin.health-records.index') }}" class="relative max-w-2xl">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search_patient" value="{{ request('search_patient') }}" 
                        class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition" 
                        placeholder="Search by name (e.g. Juan Cruz)..." autocomplete="off">
                </div>
                <button type="submit" class="bg-brand-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-brand-700 transition shadow-sm">
                    Search
                </button>
                @if(request('search_patient'))
                    <a href="{{ route('admin.health-records.index') }}" class="bg-gray-100 text-gray-600 px-4 py-3 rounded-xl font-medium hover:bg-gray-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Step 2: Search Results --}}
    @if(isset($searchResults) && $searchResults->isNotEmpty() && !isset($selectedSubject))
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 animate-fade-in-up">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Search Results</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($searchResults as $result)
                    <a href="{{ route('admin.health-records.index', ['search_patient' => request('search_patient'), 'subject_id' => $result->id]) }}" 
                       class="flex items-center gap-4 p-4 rounded-[1.5rem] border border-gray-200 hover:border-brand-500 hover:shadow-md transition cursor-pointer bg-white">
                        <div class="h-12 w-12 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-lg flex-shrink-0">
                            {{ substr($result->first_name, 0, 1) }}{{ substr($result->last_name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-bold text-gray-900 truncate">{{ $result->full_name }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $result->address }}</p>
                            @if($result->guardian_id && $result->guardian)
                                <p class="text-xs text-brand-600 mt-1 truncate">
                                    <i class="bi bi-person-fill-lock"></i> Dependent of {{ $result->guardian->full_name }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif(request('search_patient') && !isset($selectedSubject))
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="bi bi-search text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No patients found</h3>
            <p class="text-gray-500">Try adjusting your search terms.</p>
        </div>
    @endif

    {{-- Step 4: Records View --}}
    @if(isset($selectedSubject))
        <div class="bg-white rounded-[2rem] shadow-lg border border-gray-200 overflow-hidden animate-fade-in-up">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Medical History: {{ $selectedSubject->full_name }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ $selectedSubject->age }} yrs old • {{ $selectedSubject->gender }}
                        @if($selectedSubject->guardian_id && $selectedSubject->guardian)
                            • Dependent of {{ $selectedSubject->guardian->full_name }}
                        @endif
                    </p>
                </div>
                {{-- Add New Record Button REMOVED for Admin --}}
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden p-4 space-y-4">
                @forelse($records as $record)
                    <div class="bg-white rounded-[1.5rem] p-5 shadow-sm relative overflow-hidden border border-gray-100">
                        {{-- Header: Date & Attendant --}}
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center gap-2 text-gray-500 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
                                <i class="bi bi-calendar-event text-brand-400"></i>
                                {{ $record->created_at->format('M d, Y') }}
                            </div>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                {{ $record->creator->full_name ?? 'Unknown' }}
                            </span>
                        </div>

                        {{-- Body: Diagnosis --}}
                        <div class="mb-4">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                    <i class="bi bi-clipboard2-pulse-fill text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $record->diagnosis ?: 'Medical Consultation' }}</h3>
                                    <p class="text-xs text-gray-500 line-clamp-2">{{ Str::limit($record->consultation, 100) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer: Actions --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                            <div class="text-xs text-gray-400 font-medium">
                                {{ $record->created_at->format('h:i A') }}
                            </div>
                            <a href="{{ route('admin.health-records.show', $record->id) }}" class="flex items-center justify-center w-8 h-8 rounded-full bg-brand-50 text-brand-600 hover:bg-brand-100 transition-colors">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 text-gray-300">
                            <i class="bi bi-clipboard2-x text-3xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-900">No records found</p>
                        <p class="text-xs text-gray-500">This patient has no medical history yet.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Date</th>
                            <th class="px-6 py-4 font-semibold">Diagnosis</th>
                            <th class="px-6 py-4 font-semibold">Attended By</th>
                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($records as $record)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $record->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $record->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium">{{ $record->diagnosis ?: 'Medical Consultation' }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($record->consultation, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->creator->full_name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.health-records.show', $record->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-clipboard2-pulse text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-base font-medium text-gray-900">No records found</p>
                                        <p class="text-sm">This patient has no medical history yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($records->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
