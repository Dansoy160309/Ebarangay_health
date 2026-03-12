@extends('layouts.app')

@section('title', 'Health Record Details')

@section('content')
<div class="py-8 space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            @php
                 $backRoute = auth()->user()->isAdmin()
                    ? route('admin.health-records.index')
                    : (auth()->user()->isHealthWorker()
                        ? route('healthworker.health-records.index')
                        : (auth()->user()->isDoctor() || auth()->user()->isMidwife()
                            ? route('doctor.health-records.index')
                            : route('patient.health-records.index')));
            @endphp
            <a href="{{ $backRoute }}" class="bg-white p-2 rounded-lg shadow-sm text-gray-500 hover:text-brand-600 transition">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Health Record Details</h1>
                <p class="text-gray-500 text-sm">Record ID: #{{ $record->id }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            @if(auth()->user()->isAdmin() && !$record->verified_at)
                <form action="{{ route('admin.health-records.verify', $record->id) }}" method="POST" onsubmit="return confirm('Verify this record?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition shadow-sm">
                        <i class="bi bi-patch-check-fill mr-2"></i> Verify Record
                    </button>
                </form>
            @endif
            @if(auth()->user()->isHealthWorker() || auth()->user()->isAdmin() || auth()->user()->isDoctor() || auth()->user()->isMidwife())
                <a href="{{ auth()->user()->isAdmin()
                    ? route('admin.health-records.edit', $record->id)
                    : (auth()->user()->isHealthWorker()
                        ? route('healthworker.health-records.edit', $record->id)
                        : route('doctor.health-records.edit', $record->id)) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition shadow-sm">
                    <i class="bi bi-pencil-square mr-2"></i> Edit Record
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="md:col-span-2 space-y-6">
            <!-- Vitals Card -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-activity text-brand-600"></i> Vital Signs
                </h3>
                <div class="bg-gray-50 p-4 rounded text-gray-700 whitespace-pre-wrap font-mono text-sm">{{ $record->vital_signs ?: 'No vital signs recorded.' }}</div>
            </div>

            <!-- Consultation Card -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-clipboard2-pulse text-brand-600"></i> Consultation Notes
                </h3>
                <div class="prose max-w-none text-gray-700">
                    <p class="whitespace-pre-wrap">{{ $record->consultation ?: 'No notes available.' }}</p>
                </div>
            </div>

            <!-- Immunizations -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-shield-plus text-brand-600"></i> Immunizations
                </h3>
                @php
                    $immunizations = is_string($record->immunizations) ? json_decode($record->immunizations, true) : ($record->immunizations ?? []);
                @endphp
                
                @if(!empty($immunizations))
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-2 px-3 text-left">Vaccine</th>
                                    <th class="py-2 px-3 text-left">Date Administered</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($immunizations as $imm)
                                    @if(!empty($imm['name']))
                                    <tr>
                                        <td class="py-2 px-3 font-medium">{{ $imm['name'] }}</td>
                                        <td class="py-2 px-3 text-gray-600">{{ $imm['date'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 italic">No immunizations recorded.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Patient Info -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-sm uppercase font-bold text-gray-500 mb-4">Patient Information</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-xl">
                        {{ substr($record->patient->first_name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">{{ $record->patient->full_name }}</p>
                        <p class="text-xs text-gray-500">ID: {{ $record->patient->id }}</p>
                    </div>
                </div>
                <div class="text-sm space-y-2 text-gray-600">
                    <p><span class="font-semibold">Gender:</span> {{ ucfirst($record->patient->gender) }}</p>
                    <p><span class="font-semibold">Age:</span> {{ \Carbon\Carbon::parse($record->patient->dob)->age }} years</p>
                    <p><span class="font-semibold">Address:</span> {{ $record->patient->address }}</p>
                </div>
            </div>

            <!-- Record Meta -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-sm uppercase font-bold text-gray-500 mb-4">Record Status</h3>
                
                <div class="mb-4">
                    <p class="text-xs text-gray-400 uppercase">Status</p>
                    <span class="inline-block mt-1 px-2 py-1 rounded text-xs font-semibold {{ $record->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-400 uppercase">Created By</p>
                    <p class="text-sm font-medium text-gray-800">{{ $record->creator->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $record->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 uppercase">Verification</p>
                    @if($record->verified_at)
                        <div class="flex items-start gap-2 mt-1">
                            <i class="bi bi-check-circle-fill text-green-600 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Verified</p>
                                <p class="text-xs text-gray-500">by {{ $record->verifier->full_name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-400">{{ $record->verified_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 mt-1 text-yellow-600">
                            <i class="bi bi-clock"></i>
                            <span class="text-sm font-medium">Pending Verification</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
