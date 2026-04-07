@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-50 via-white to-sky-50 relative overflow-hidden">
    
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob"></div>
        <div class="absolute -bottom-[10%] right-[10%] w-[35%] h-[35%] bg-blue-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000"></div>
    </div>

    <div class="max-w-md w-full bg-white/95 rounded-[2rem] shadow-soft border border-gray-200 p-6 sm:p-8 relative z-10 transition-all duration-300 hover:shadow-2xl">
        <div class="text-center mb-7">
            <div class="w-14 h-14 bg-brand-50 rounded-3xl flex items-center justify-center text-brand-600 text-2xl mx-auto mb-4 shadow-inner border border-brand-100">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mb-2 tracking-tight">Forgot Password?</h2>
            <p class="text-sm sm:text-base text-gray-600 font-medium leading-relaxed max-w-xl mx-auto">
                No worries — enter the email on your account and we’ll send a secure reset link right away.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-3xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.forgot.submit') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 uppercase tracking-[0.18em] mb-2">Email address</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                        <i class="bi bi-envelope-fill text-lg"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="block w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-3xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all text-sm"
                        placeholder="you@example.com">
                </div>
                @error('email') 
                    <p class="mt-2 text-sm text-red-600 ml-1 flex items-center gap-2">
                        <i class="bi bi-info-circle-fill"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit"
                class="w-full inline-flex justify-center items-center py-3.5 px-6 rounded-3xl shadow-lg text-sm font-semibold uppercase tracking-[0.24em] text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 transition-transform duration-200 active:scale-[0.98]">
                Send Reset Link <i class="bi bi-send-fill ml-2 text-base"></i>
            </button>

            <p class="text-center text-sm text-gray-500">If this email is registered, you’ll receive a reset link in a few minutes.</p>

            <div class="text-center pt-1">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-brand-600 transition-colors uppercase tracking-[0.24em]">
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
</style>
@endsection
