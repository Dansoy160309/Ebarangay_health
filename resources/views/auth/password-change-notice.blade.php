@extends('layouts.app')

@section('title', 'Security Update')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
        {{-- Decorative Top Bar --}}
        <div class="h-2 bg-gradient-to-r from-yellow-400 to-orange-500 w-full"></div>

        <div class="p-8 sm:p-10">
            <div class="flex flex-col items-center text-center">
                
                {{-- Icon --}}
                <div class="mb-6 relative">
                    <div class="absolute inset-0 bg-yellow-100 rounded-full blur-xl opacity-50"></div>
                    <div class="relative bg-yellow-50 rounded-2xl p-4 ring-1 ring-yellow-100 shadow-sm">
                        <i class="bi bi-shield-lock-fill text-5xl text-yellow-600"></i>
                    </div>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 tracking-tight">
                    Password Change Required
                </h1>
                
                <p class="text-gray-500 mb-8 leading-relaxed max-w-sm mx-auto">
                    To ensure the security of your account and patient data, we require you to update your password before proceeding.
                </p>

                @if(session('warning'))
                    <div class="w-full bg-orange-50 border border-orange-100 text-orange-800 px-4 py-3 rounded-xl mb-8 flex items-start gap-3 text-left text-sm">
                        <i class="bi bi-exclamation-triangle-fill text-lg shrink-0 mt-0.5"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                <div class="w-full space-y-3">
                    <a href="{{ route('password.change.form') }}" 
                       class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                        <span>Change Password Now</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3.5 text-gray-500 font-medium hover:text-gray-700 hover:bg-gray-50 rounded-xl transition-colors text-sm">
                            Log out and do this later
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- Footer Info --}}
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">
                <i class="bi bi-lock-fill mr-1"></i> Secure Connection
            </p>
        </div>
    </div>
</div>
@endsection
