@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="py-12">
    
    <!-- Profile Overview Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Cover Design --}}
        <div class="h-32 sm:h-48 bg-gradient-to-r from-brand-600 to-brand-400 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="absolute inset-0 bg-black/5"></div>
        </div>

        {{-- Profile Header Content --}}
        <div class="relative px-6 sm:px-10 pb-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-end -mt-12 sm:-mt-16 gap-6">
                {{-- Avatar --}}
                <div class="relative group">
                    <div class="h-28 w-28 sm:h-36 sm:w-36 rounded-full ring-4 ring-white bg-white flex items-center justify-center text-4xl sm:text-5xl font-bold text-brand-600 shadow-md">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                    </div>
                    <div class="absolute bottom-2 right-2 bg-green-500 h-6 w-6 rounded-full border-4 border-white shadow-sm" title="Active Account"></div>
                </div>

                {{-- Name & Role --}}
                <div class="text-center sm:text-left flex-1 pb-2">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </h1>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-50 text-brand-700 border border-brand-200">
                            <i class="bi bi-person-check mr-1.5"></i> Patient
                        </span>
                        <span class="text-sm text-gray-500 flex items-center">
                            <i class="bi bi-calendar3 mr-1.5 text-gray-400"></i> Joined {{ $user->created_at->format('F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.patient-quick-services')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Contact & Personal Info -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Contact Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="bi bi-person-lines-fill text-xl"></i>
                    </div>
                    Contact Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    <!-- Email -->
                    <div class="group flex items-start gap-4">
                        <div class="mt-1 h-8 w-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-colors">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email Address</p>
                            <p class="text-gray-900 font-medium break-all">{{ $user->email }}</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="group flex items-start gap-4">
                        <div class="mt-1 h-8 w-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-colors">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Phone Number</p>
                            <p class="text-gray-900 font-medium">{{ $user->contact_no ?? 'Not Provided' }}</p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="group flex items-start gap-4 md:col-span-2">
                        <div class="mt-1 h-8 w-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-colors">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Home Address</p>
                            <p class="text-gray-900 font-medium leading-relaxed">{{ $user->address ?? 'Not Provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <i class="bi bi-bell-fill text-xl"></i>
                    </div>
                    Notification Preferences
                </h3>
                
                <form action="{{ route('patient.profile.preferences') }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-orange-500 shadow-sm">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">SMS Notifications</p>
                                <p class="text-xs text-gray-500">Receive appointment updates via SMS</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_notifications" value="1" onchange="this.form.submit()" class="sr-only peer" {{ $user->sms_notifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Dependents Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center">
                        <i class="bi bi-people text-xl"></i>
                    </div>
                    My Dependents
                </h3>

                @if($user->dependents->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($user->dependents as $dependent)
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-200 bg-gray-50 hover:bg-white hover:border-pink-200 hover:shadow-sm transition-all group">
                                <div class="h-14 w-14 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center font-bold text-lg group-hover:scale-105 transition-transform">
                                    {{ substr($dependent->first_name, 0, 1) }}{{ substr($dependent->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $dependent->full_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $dependent->relationship }} • {{ $dependent->age }} yrs</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 border border-dashed border-gray-300 p-8 text-center">
                        <div class="mx-auto h-12 w-12 text-gray-400 mb-3">
                            <i class="bi bi-person-plus text-3xl"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">No Dependents Listed</h3>
                        <p class="mt-1 text-sm text-gray-500">Contact your Barangay Health Worker to add family members to your profile.</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Column: Account Stats -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i class="bi bi-shield-check text-xl"></i>
                    </div>
                    Account Status
                </h3>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600">Verification</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                            <i class="bi bi-check-circle-fill mr-1.5"></i> Verified
                        </span>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">System ID</p>
                        <div class="flex items-center gap-2 font-mono text-gray-900 bg-gray-100 px-3 py-2 rounded-lg">
                            <i class="bi bi-hash text-gray-400"></i>
                            {{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 leading-relaxed text-center">
                            Your account is fully verified and active. You can book appointments and view health records.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions (Visual Only since routes might not exist) -->
             <div class="bg-gradient-to-br from-brand-600 to-brand-700 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                 <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                 <div class="relative z-10">
                     <h3 class="font-bold text-lg mb-2">Need Help?</h3>
                     <p class="text-brand-100 text-sm mb-4">Visit the Barangay Health Center for assistance with updating your profile information.</p>
                     <div class="flex items-center gap-2 text-xs font-medium bg-white/20 p-3 rounded-lg backdrop-blur-sm">
                        <i class="bi bi-info-circle"></i>
                        <span>Profile editing is handled by admin</span>
                     </div>
                 </div>
             </div>

        </div>

    </div>
</div>
@endsection