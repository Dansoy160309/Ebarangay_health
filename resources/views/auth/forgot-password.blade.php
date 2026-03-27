@extends('layouts.app')

@section('title', 'Forgot Password')

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
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-2 tracking-tight">Forgot Password?</h2>
            <p class="text-gray-500 font-medium">No worries! Enter your email and we'll send you a reset link.</p>
        </div>

        @if(session('error'))
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-sm flex items-start gap-3 animate-shake">
                <i class="bi bi-exclamation-triangle-fill text-xl mt-0.5"></i>
                <p class="font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 px-6 py-4 rounded-r-lg shadow-sm flex items-start gap-3">
                <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('debug_reset_link'))
            <div class="mb-8 bg-blue-50 border-l-4 border-blue-500 text-blue-700 px-6 py-4 rounded-r-lg shadow-sm">
                <p class="font-black text-xs uppercase tracking-widest mb-2">Reset Link (Debug)</p>
                <a href="{{ session('debug_reset_link') }}" class="break-all font-bold text-sm text-blue-700 hover:text-blue-900 underline">
                    {{ session('debug_reset_link') }}
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('password.forgot.submit') }}" class="space-y-8">
            @csrf

            <div>
                <label for="email" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Email Address</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                        <i class="bi bi-envelope-fill text-lg"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="block w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold"
                        placeholder="you@example.com">
                </div>
                @error('email') 
                    <p class="mt-2 text-xs font-bold text-red-500 ml-1 flex items-center gap-1">
                        <i class="bi bi-info-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-2xl shadow-lg text-sm font-black uppercase tracking-widest text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 transition-all transform hover:-translate-y-1 active:scale-95">
                Send Reset Link <i class="bi bi-send-fill ml-2"></i>
            </button>

            <div class="text-center">
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
</style>
@endsection
