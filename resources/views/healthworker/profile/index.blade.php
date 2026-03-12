@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Cover Image --}}
        <div class="h-32 sm:h-48 bg-gradient-to-r from-brand-600 to-brand-400 relative">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>

        {{-- Profile Header --}}
        <div class="relative px-6 sm:px-10 pb-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-end -mt-12 sm:-mt-16 mb-6 gap-6">
                {{-- Avatar --}}
                <div class="relative">
                    <div class="h-24 w-24 sm:h-32 sm:w-32 rounded-full ring-4 ring-white bg-white flex items-center justify-center text-3xl sm:text-4xl font-bold text-brand-600 shadow-md">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                    </div>
                    <div class="absolute bottom-1 right-1 bg-green-500 h-5 w-5 rounded-full border-2 border-white" title="Active"></div>
                </div>

                {{-- Name & Role --}}
                <div class="text-center sm:text-left flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </h1>
                    <div class="flex items-center justify-center sm:justify-start gap-2 mt-1 text-gray-600">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-brand-50 text-brand-700 border border-brand-200">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="bi bi-calendar3"></i> Joined {{ $user->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="mt-4 sm:mt-0">
                    {{-- Placeholder for Edit Profile if we implement it --}}
                    {{-- <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition shadow-sm">
                        <i class="bi bi-pencil mr-2"></i> Edit Profile
                    </button> --}}
                </div>
            </div>

            <hr class="border-gray-100 my-6">

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                
                {{-- Left Column: Contact Information --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        Contact Information
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition">
                            <div class="mt-1 text-gray-400">
                                <i class="bi bi-envelope text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email Address</p>
                                <p class="text-gray-900 font-medium break-all">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition">
                            <div class="mt-1 text-gray-400">
                                <i class="bi bi-telephone text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Phone Number</p>
                                <p class="text-gray-900 font-medium">{{ $user->contact_no ?? 'Not Provided' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition">
                            <div class="mt-1 text-gray-400">
                                <i class="bi bi-geo-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Address</p>
                                <p class="text-gray-900 font-medium">{{ $user->address ?? 'Not Provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Account Details / Stats --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        Account Details
                    </h3>

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">User ID</p>
                                <p class="text-gray-900 font-mono mt-1">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                                    <span class="text-gray-900 font-medium">Active</span>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Updated</p>
                                <p class="text-gray-900 mt-1">{{ $user->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Security Notice --}}
                    <div class="mt-6 flex items-start gap-3 p-4 bg-yellow-50 text-yellow-800 rounded-xl text-sm border border-yellow-100">
                        <i class="bi bi-shield-exclamation text-xl shrink-0"></i>
                        <p>
                            To update your personal information or change your password, please contact the system administrator or use the <a href="{{ route('password.change.form') }}" class="font-bold underline hover:text-yellow-900">Change Password</a> page.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
