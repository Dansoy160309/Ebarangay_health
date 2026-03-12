@extends('layouts.app')

@section('title', 'Health Records')

@section('content')
<div class="py-6 space-y-6">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Health Records</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all patient health records including diagnosis, vitals, and status.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
             <div class="relative rounded-md shadow-sm max-w-xs w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
                <input type="text" name="search" id="search" class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-2.5" placeholder="Search patients...">
            </div>
            
            @if(auth()->user()->isAdmin() || auth()->user()->isHealthWorker())
                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.health-records.create' : 'healthworker.health-records.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">
                    <i class="bi bi-plus-lg mr-2"></i> Add Record
                </a>
            @endif
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-bold text-left">Patient</th>
                        <th class="px-6 py-4 font-bold text-left">Details</th>
                        <th class="px-6 py-4 font-bold text-left">Status</th>
                        <th class="px-6 py-4 font-bold text-left">Verification</th>
                        <th class="px-6 py-4 font-bold text-left">Date</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $record)
                        @php
                            $patient = $record->patient;
                            $firstInitial = $patient && $patient->first_name ? substr($patient->first_name, 0, 1) : '';
                            $lastInitial = $patient && $patient->last_name ? substr($patient->last_name, 0, 1) : '';
                            $creatorLastName = $record->creator ? $record->creator->last_name : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold">
                                            {{ $firstInitial }}{{ $lastInitial }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $patient->full_name ?? 'Unknown Patient' }}</div>
                                        <div class="text-sm text-gray-500">{{ $patient->email ?? 'No email available' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm text-gray-900 line-clamp-1 max-w-xs">{{ $record->consultation }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">Dr. {{ $creatorLastName ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($record->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Archived
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($record->is_verified)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="bi bi-patch-check-fill mr-1"></i> Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">
                                {{ $record->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    @php
                                        $viewRoute = auth()->user()->isAdmin() ? route('admin.health-records.show', $record->id) : route('healthworker.health-records.show', $record->id);
                                        $editRoute = auth()->user()->isAdmin() ? route('admin.health-records.edit', $record->id) : route('healthworker.health-records.edit', $record->id);
                                    @endphp
                                    
                                    <a href="{{ $viewRoute }}" class="text-gray-400 hover:text-brand-600 transition" title="View">
                                        <i class="bi bi-eye text-lg"></i>
                                    </a>
                                    
                                    <a href="{{ $editRoute }}" class="text-gray-400 hover:text-blue-600 transition" title="Edit">
                                        <i class="bi bi-pencil-square text-lg"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin() && !$record->is_verified)
                                        <form action="{{ route('admin.health-records.verify', $record->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition" title="Verify">
                                                <i class="bi bi-check-circle text-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                        <i class="bi bi-folder2-open text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">No records found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new health record.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $records->links() }}
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            }, 300);
        });
    }
</script>
@endsection
