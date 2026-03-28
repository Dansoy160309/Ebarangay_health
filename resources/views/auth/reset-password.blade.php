@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 relative overflow-hidden" 
     x-data="{ showPassword: false, showConfirmPassword: false }">
    
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob"></div>
        <div class="absolute -bottom-[10%] right-[10%] w-[35%] h-[35%] bg-blue-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000"></div>
    </div>

    {{-- Adjusted size: max-w-sm (smaller) and reduced padding --}}
    <div class="max-w-sm w-full bg-white rounded-[2rem] shadow-2xl p-6 md:p-8 relative z-10 transition-all duration-300 hover:shadow-3xl border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 text-2xl mx-auto mb-4 shadow-inner border border-brand-100">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-900 mb-1 tracking-tight">Reset Password</h2>
            <p class="text-xs text-gray-500 font-medium">Almost there! Choose a new secure password for your account.</p>
        </div>

        {{-- Error alert removed as requested --}}

        <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="password" class="block text-[10px] font-black text-gray-700 uppercase tracking-widest ml-1 mb-1.5">New Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                        <i class="bi bi-key-fill text-base"></i>
                    </div>
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required autofocus
                        class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold text-sm"
                        placeholder="••••••••">
                    
                    {{-- Show/Hide Toggle --}}
                    <button type="button" @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-brand-600 transition-colors">
                        <i class="bi" :class="showPassword ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                    </button>
                </div>
                @error('password') 
                    <p class="mt-1.5 text-[10px] font-bold text-red-500 ml-1 flex items-center gap-1">
                        <i class="bi bi-info-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-[10px] font-black text-gray-700 uppercase tracking-widest ml-1 mb-1.5">Confirm Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                        <i class="bi bi-shield-check-fill text-base"></i>
                    </div>
                    <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                        class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold text-sm"
                        placeholder="••••••••">
                    
                    {{-- Show/Hide Toggle --}}
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" 
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-brand-600 transition-colors">
                        <i class="bi" :class="showConfirmPassword ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center py-3.5 px-6 border border-transparent rounded-2xl shadow-lg text-xs font-black uppercase tracking-widest text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 transition-all transform hover:-translate-y-1 active:scale-95">
                Update Password <i class="bi bi-arrow-right-short text-xl ml-1"></i>
            </button>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[10px] font-black text-gray-400 hover:text-brand-600 transition-colors uppercase tracking-widest">
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
