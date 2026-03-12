@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                        <i class="bi bi-house-door text-lg"></i>
                        <span class="sr-only">Dashboard</span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-300 flex-shrink-0"></i>
                    <a href="{{ route('admin.users.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Users</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-300 flex-shrink-0"></i>
                    <span class="ml-4 text-sm font-medium text-gray-900" aria-current="page">{{ $user->first_name }} {{ $user->last_name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Identity & Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 text-center">
                <!-- Avatar -->
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-brand-100 to-brand-50 rounded-full flex items-center justify-center text-brand-600 text-4xl font-bold mb-4 shadow-inner ring-4 ring-white">
                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                </div>
                
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</h1>
                <p class="text-sm text-gray-500 mb-6 font-medium">{{ $user->email }}</p>

                <div class="flex items-center justify-center gap-2 mb-8">
                    <!-- Status Badge -->
                    @if($user->status)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-green-50 text-green-600 border border-green-100">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-red-50 text-red-600 border border-red-100">
                            Inactive
                        </span>
                    @endif

                    <!-- Role Badge -->
                    @if($user->role === 'midwife')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-blue-50 text-blue-600 border border-blue-100">
                            Midwife
                        </span>
                    @elseif($user->role === 'health_worker')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-blue-50 text-blue-600 border border-blue-100">
                            Health Worker
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-blue-50 text-blue-600 border border-blue-100">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    @endif
                </div>
                
                <div class="border-t border-gray-100 pt-6 flex flex-col gap-3">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-200 shadow-sm text-sm font-bold rounded-2xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors">
                        <i class="bi bi-pencil mr-2"></i> Edit Profile
                    </a>
                    
                    @if(auth()->id() !== $user->id)
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-bold rounded-2xl text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="bi bi-trash mr-2"></i> Delete User
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Meta Info -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">System Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Member Since</span>
                        <span class="text-sm font-bold text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Last Updated</span>
                        <span class="text-sm font-bold text-gray-900">{{ $user->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">User ID</span>
                        <span class="text-sm font-mono text-gray-900 bg-gray-50 px-2 py-1 rounded-md">#{{ $user->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Personal Information</h2>
                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
                        <i class="bi bi-person text-gray-400"></i>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Full Name</dt>
                        <dd class="text-base text-gray-900">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Date of Birth</dt>
                        <dd class="text-base text-gray-900">{{ $user->dob->format('F d, Y') }} ({{ $user->age }} yrs)</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Gender</dt>
                        <dd class="text-base text-gray-900">{{ $user->gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Civil Status</dt>
                        <dd class="text-base text-gray-900">{{ $user->civil_status ?? 'Not Specified' }}</dd>
                    </div>
                    @if($user->role === 'patient' && $user->guardian)
                    <div class="md:col-span-2 bg-blue-50 rounded-2xl p-4 border border-blue-100">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="bi bi-shield-check text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-blue-800">Dependent Account</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Managed by Guardian: <a href="{{ route('admin.users.show', $user->guardian_id) }}" class="font-bold underline hover:text-blue-900">{{ $user->guardian->first_name }} {{ $user->guardian->last_name }}</a></p>
                                    <p class="mt-1">Relationship: {{ ucfirst($user->relationship) }} • Reason: {{ $user->dependency_reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Contact & Address</h2>
                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
                        <i class="bi bi-geo-alt text-gray-400"></i>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Address</dt>
                        <dd class="text-base text-gray-900 flex items-start gap-2">
                            <i class="bi bi-house text-gray-400 mt-0.5"></i>
                            {{ $user->address }}, {{ $user->purok }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Mobile Number</dt>
                        <dd class="text-base text-gray-900">{{ $user->contact_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Emergency Contact</dt>
                        <dd class="text-base text-gray-900">
                            <p>{{ $user->emergency_no }}</p>
                            @if($user->emergency_contact_name)
                                <p class="text-sm text-gray-500 mt-1">{{ $user->emergency_contact_name }} ({{ $user->emergency_contact_relationship }})</p>
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            @if($user->role === 'patient')
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Health Profile</h2>
                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
                        <i class="bi bi-heart-pulse text-gray-400"></i>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Blood Type</dt>
                        <dd class="text-base text-gray-900">
                            @if(optional($user->patientProfile)->blood_type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-bold bg-red-50 text-red-600 border border-red-100">
                                    {{ $user->patientProfile->blood_type }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">Not specified</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Allergies</dt>
                        <dd class="text-base text-gray-900">
                            @if(optional($user->patientProfile)->allergies)
                                <span class="text-red-600 font-medium">{{ $user->patientProfile->allergies }}</span>
                            @else
                                <span class="text-green-600 flex items-center gap-1"><i class="bi bi-check-circle"></i> No known allergies</span>
                            @endif
                        </dd>
                    </div>
                    @if(optional($user->patientProfile)->medical_history)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Medical History</dt>
                        <dd class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            {{ $user->patientProfile->medical_history }}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Dependents Section -->
            @if($user->dependents->isNotEmpty())
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Family Members / Dependents</h2>
                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
                        <i class="bi bi-people text-gray-400"></i>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($user->dependents as $dependent)
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-sm border border-blue-100">
                                {{ substr($dependent->first_name, 0, 1) }}{{ substr($dependent->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">
                                    <a href="{{ route('admin.users.show', $dependent->id) }}" class="hover:text-brand-600 hover:underline">
                                        {{ $dependent->first_name }} {{ $dependent->last_name }}
                                    </a>
                                </h4>
                                <p class="text-sm text-gray-500">
                                    {{ ucfirst($dependent->relationship) }} • {{ $dependent->age }} yrs old
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($dependent->status)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase tracking-wide bg-green-50 text-green-600 border border-green-100">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase tracking-wide bg-red-50 text-red-600 border border-red-100">
                                    Inactive
                                </span>
                            @endif
                            <a href="{{ route('admin.users.show', $dependent->id) }}" class="p-2 text-gray-400 hover:text-brand-600 transition-colors">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
