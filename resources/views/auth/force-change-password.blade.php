@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">
        
        {{-- Left Side: Decorative / Info --}}
        <div class="md:w-5/12 bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-white flex flex-col justify-center relative overflow-hidden">
            {{-- Decorative Circles --}}
            <div class="absolute top-0 left-0 w-32 h-32 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-48 h-48 bg-white/10 rounded-full translate-x-1/3 translate-y-1/3"></div>

            <div class="relative z-10 text-center md:text-left">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-6 backdrop-blur-sm">
                    <i class="bi bi-shield-lock-fill text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold mb-4">Secure Your Account</h2>
                <p class="text-brand-100 leading-relaxed mb-6">
                    Protect your personal information and health records. Choose a strong password that you haven't used on other websites.
                </p>
                <ul class="text-sm text-brand-100 space-y-2 text-left hidden md:block">
                    <li class="flex items-center gap-2"><i class="bi bi-check-circle-fill"></i> At least 8 characters long</li>
                    <li class="flex items-center gap-2"><i class="bi bi-check-circle-fill"></i> Mix of letters & numbers</li>
                    <li class="flex items-center gap-2"><i class="bi bi-check-circle-fill"></i> Include special symbols</li>
                </ul>
            </div>
        </div>

        {{-- Right Side: Form --}}
        <div class="md:w-7/12 p-8 md:p-12">
            <div class="text-center md:text-left mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Change Password</h1>
                <p class="text-gray-500 mt-1">Please enter your current password and a new one.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r flex items-start gap-3">
                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                    <div>
                        <p class="font-bold">Success</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('password.change.update') }}" method="POST">
                @csrf

                {{-- Current Password --}}
                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-key text-gray-400"></i>
                        </div>
                        <input type="password" name="current_password" id="current_password"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition sm:text-sm @error('current_password') border-red-500 ring-red-200 @enderror"
                            placeholder="••••••••" required>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition sm:text-sm @error('password') border-red-500 ring-red-200 @enderror"
                            placeholder="New strong password" required>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock-fill text-gray-400"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition sm:text-sm"
                            placeholder="Repeat new password" required>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col-reverse sm:flex-row items-center gap-4">
                    @if(!auth()->user()->must_change_password)
                        <a href="{{ route('dashboard') }}" 
                           class="w-full sm:w-auto px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center transition">
                            Cancel
                        </a>
                    @endif
                    
                    <button type="submit"
                        class="w-full sm:w-auto flex-1 bg-brand-600 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition flex items-center justify-center gap-2">
                        <i class="bi bi-check-lg"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
