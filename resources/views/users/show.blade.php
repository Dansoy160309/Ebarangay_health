@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="flex flex-col gap-6 sm:gap-8">
    <!-- Header Section with Breadcrumb and Actions -->
    <div class="bg-gradient-to-br from-brand-600 via-brand-500 to-indigo-600 rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 text-white shadow-xl shadow-brand-500/20 relative overflow-hidden">
        <div class="relative z-10">
            <nav class="flex mb-4 sm:mb-6" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 sm:space-x-4 text-[10px] sm:text-xs font-black uppercase tracking-widest opacity-80">
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                    <li><i class="bi bi-chevron-right text-[8px] sm:text-[10px]"></i></li>
                    <li><a href="{{ route('admin.users.index') }}" class="ml-2 sm:ml-4 hover:text-white transition-colors">Users</a></li>
                    <li><i class="bi bi-chevron-right text-[8px] sm:text-[10px]"></i></li>
                    <li class="ml-2 sm:ml-4 text-white">Details</li>
                </ol>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="w-16 h-16 sm:w-24 sm:h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white text-2xl sm:text-4xl font-black shadow-inner border border-white/30 ring-4 ring-white/10 shrink-0">
                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-4xl font-black mb-1 sm:mb-2 tracking-tight leading-tight">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest border border-white/20">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                            @if($user->status)
                                <span class="px-3 py-1 rounded-full bg-emerald-400/20 backdrop-blur-md text-emerald-100 text-[10px] font-black uppercase tracking-widest border border-emerald-400/30">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-red-400/20 backdrop-blur-md text-red-100 text-[10px] font-black uppercase tracking-widest border border-red-400/30">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 rounded-2xl bg-white text-brand-600 font-black text-xs sm:text-sm uppercase tracking-widest shadow-xl hover:bg-brand-50 hover:scale-105 transition-all transform group">
                        <i class="bi bi-pencil-fill mr-2 group-hover:rotate-12 transition-transform"></i>
                        Edit Profile
                    </a>
                    @if(auth()->id() !== $user->id)
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="flex-1 sm:flex-none">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 rounded-2xl bg-red-500 text-white font-black text-xs sm:text-sm uppercase tracking-widest shadow-xl hover:bg-red-600 hover:scale-105 transition-all transform">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute -right-10 -bottom-10 sm:-right-16 sm:-bottom-16 opacity-10 pointer-events-none">
            <i class="bi bi-person-badge-fill text-[12rem] sm:text-[18rem]"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
        
        <!-- Left Sidebar: Meta & Fast Actions -->
        <div class="lg:col-span-4 space-y-6 sm:gap-8">
            
            <!-- Quick Stats -->
            <div class="bg-white rounded-[2rem] p-6 sm:p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
                <h3 class="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="bi bi-info-circle text-brand-500"></i> System Info
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50/50 border border-gray-100 transition-colors group-hover:bg-brand-50/30 group-hover:border-brand-100">
                        <span class="text-xs sm:text-sm font-bold text-gray-500">Member Since</span>
                        <span class="text-xs sm:text-sm font-black text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50/50 border border-gray-100 transition-colors group-hover:bg-brand-50/30 group-hover:border-brand-100">
                        <span class="text-xs sm:text-sm font-bold text-gray-500">Last Updated</span>
                        <span class="text-xs sm:text-sm font-black text-gray-900">{{ $user->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50/50 border border-gray-100 transition-colors group-hover:bg-brand-50/30 group-hover:border-brand-100">
                        <span class="text-xs sm:text-sm font-bold text-gray-500">User ID</span>
                        <span class="text-xs sm:text-sm font-black text-brand-600 px-3 py-1 bg-brand-50 rounded-lg">#{{ $user->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Profile Overview (Compact for Mobile) -->
            <div class="bg-white rounded-[2rem] p-6 sm:p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                <h3 class="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="bi bi-shield-lock text-brand-500"></i> Account Details
                </h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email Address</p>
                        <p class="text-sm font-bold text-gray-900 break-all">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Mobile Number</p>
                        <p class="text-sm font-bold text-gray-900">{{ $user->contact_no }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Emergency No.</p>
                        <p class="text-sm font-bold text-red-600">{{ $user->emergency_no }}</p>
                        @if($user->emergency_contact_name)
                            <p class="text-[10px] font-bold text-gray-500 mt-1">{{ $user->emergency_contact_name }} ({{ $user->emergency_contact_relationship }})</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-8 space-y-6 sm:gap-8">
            
            <!-- Bento Grid for Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                
                <!-- Personal Info Card -->
                <div class="bg-white rounded-[2rem] p-6 sm:p-8 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-sm">
                            <i class="bi bi-person-vcard text-xl"></i>
                        </div>
                        <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Personal</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Gender</p>
                                <p class="text-sm font-bold text-gray-900">{{ $user->gender }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Civil Status</p>
                                <p class="text-sm font-bold text-gray-900">{{ $user->civil_status ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Date of Birth</p>
                            <p class="text-sm font-bold text-gray-900">{{ $user->dob->format('F d, Y') }} ({{ $user->age }} yrs)</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Address</p>
                            <p class="text-sm font-bold text-gray-900 leading-relaxed">{{ $user->address }}, {{ $user->purok }}</p>
                        </div>
                    </div>
                </div>

                <!-- Health Profile Card -->
                @if($user->role === 'patient')
                <div class="bg-white rounded-[2rem] p-6 sm:p-8 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shadow-sm">
                            <i class="bi bi-heart-pulse-fill text-xl"></i>
                        </div>
                        <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Health Profile</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Blood Type</p>
                                @if(optional($user->patientProfile)->blood_type)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black bg-red-50 text-red-600 border border-red-100">
                                        {{ $user->patientProfile->blood_type }}
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-gray-400 italic">Not set</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Allergies</p>
                                @if(optional($user->patientProfile)->allergies)
                                    <span class="text-xs font-bold text-red-600">{{ $user->patientProfile->allergies }}</span>
                                @else
                                    <span class="text-xs font-bold text-emerald-600">None</span>
                                @endif
                            </div>
                        </div>
                        @if(optional($user->patientProfile)->medical_history)
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Medical History</p>
                            <p class="text-xs font-bold text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100 line-clamp-3">
                                {{ $user->patientProfile->medical_history }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Dependent Info Card -->
                @if($user->role === 'patient' && $user->guardian)
                <div class="md:col-span-2 bg-gradient-to-br from-brand-50 to-blue-50 rounded-[2rem] p-6 sm:p-8 border border-brand-100 shadow-sm relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-white text-brand-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-shield-check text-xl"></i>
                            </div>
                            <h2 class="text-lg font-black text-brand-900 tracking-tight uppercase">Guardian Info</h2>
                        </div>
                        <p class="text-sm font-bold text-brand-800 leading-relaxed mb-4">
                            This is a dependent account managed by <br class="hidden sm:block">
                            <a href="{{ route('admin.users.show', $user->guardian_id) }}" class="text-brand-600 underline decoration-2 underline-offset-4 hover:text-brand-800 transition-colors">
                                {{ $user->guardian->first_name }} {{ $user->guardian->last_name }}
                            </a>
                        </p>
                        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-brand-500">
                            <span class="px-3 py-1 bg-white rounded-full shadow-sm border border-brand-100">Relationship: {{ $user->relationship }}</span>
                            <span class="px-3 py-1 bg-white rounded-full shadow-sm border border-brand-100">Reason: {{ $user->dependency_reason }}</span>
                        </div>
                    </div>
                    <i class="bi bi-shield-check-fill absolute -right-4 -bottom-4 text-8xl text-brand-100/50 rotate-12"></i>
                </div>
                @endif
            </div>

            <!-- Family/Dependents Section -->
            @if($user->dependents->isNotEmpty())
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 sm:px-8 sm:py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                            <i class="bi bi-people-fill text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Family Members</h2>
                            <p class="text-[8px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Linked accounts and dependents</p>
                        </div>
                    </div>
                    <span class="px-4 py-1.5 rounded-full bg-white border border-gray-200 text-[10px] font-black text-gray-500 uppercase tracking-widest shadow-sm">
                        {{ $user->dependents->count() }} Members
                    </span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($user->dependents as $dependent)
                    <div class="p-6 sm:p-8 flex items-center justify-between hover:bg-gray-50/50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-lg border border-indigo-100 group-hover:scale-110 transition-transform">
                                {{ substr($dependent->first_name, 0, 1) }}{{ substr($dependent->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-base font-black text-gray-900 group-hover:text-brand-600 transition-colors">
                                    <a href="{{ route('admin.users.show', $dependent->id) }}">
                                        {{ $dependent->first_name }} {{ $dependent->last_name }}
                                    </a>
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">{{ $dependent->relationship }}</span>
                                    <span class="text-[10px] font-bold text-gray-300">•</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $dependent->age }} yrs old</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $dependent->id) }}" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-600 transition-all">
                            <i class="bi bi-chevron-right text-lg"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
