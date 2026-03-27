@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 relative overflow-hidden">
    
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob"></div>
        <div class="absolute -bottom-[10%] right-[10%] w-[35%] h-[35%] bg-blue-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000"></div>
    </div>

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-12 relative z-10 transition-all duration-300 hover:shadow-3xl border border-gray-100">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 text-3xl mx-auto mb-6 shadow-inner border border-brand-100">
                <i class="bi bi-shield-check-fill"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-2 tracking-tight">Reset Password</h2>
            <p class="text-gray-500 font-medium">Almost there! Choose a new secure password for your account.</p>
        </div>

        @if(session('error'))
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-sm flex items-start gap-3 animate-shake">
                <i class="bi bi-exclamation-triangle-fill text-xl mt-0.5"></i>
                <p class="font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-6">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="password" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">New Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                        <i class="bi bi-key-fill text-lg"></i>
                    </div>
                    <input type="password" name="password" id="password" required autofocus
                        class="block w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold"
                        placeholder="••••••••">
                </div>
                @error('password') 
                    <p class="mt-2 text-xs font-bold text-red-500 ml-1 flex items-center gap-1">
                        <i class="bi bi-info-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Confirm Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                        <i class="bi bi-shield-lock-fill text-lg"></i>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="block w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-2xl shadow-lg text-sm font-black uppercase tracking-widest text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 transition-all transform hover:-translate-y-1 active:scale-95">
                Update Password <i class="bi bi-arrow-right-short text-2xl ml-1"></i>
            </button>

            <div class="text-center pt-4">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-black text-gray-400 hover:text-brand-600 transition-colors uppercase tracking-widest">
                    <i class="bi bi-arrow-left"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>
@endsection
