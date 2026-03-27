@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 relative overflow-hidden">
    
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob"></div>
        <div class="absolute top-[20%] -right-[10%] w-[35%] h-[35%] bg-blue-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] bg-purple-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-4000"></div>
    </div>

    <div class="max-w-6xl w-full bg-white rounded-[2rem] shadow-2xl overflow-hidden flex min-h-[650px] relative z-10 transition-all duration-300 hover:shadow-3xl">
        
        {{-- Left Panel --}}
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-brand-700 via-brand-600 to-brand-800 p-12 flex-col justify-between relative overflow-hidden text-white">
            
            {{-- Abstract Pattern Overlay --}}
            <div class="absolute inset-0 opacity-10 pattern-grid-lg"></div>
            
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-brand-400/20 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white shadow-inner border border-white/10">
                        <i class="bi bi-heart-pulse-fill text-2xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-wide drop-shadow-sm uppercase">E-Barangay Health</span>
                </div>

                <div class="space-y-6">
                    <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight drop-shadow-md">
                        Your Health,<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-100 to-white">Our Priority.</span>
                    </h1>
                    <p class="text-brand-100 text-xl font-light leading-relaxed max-w-md drop-shadow-sm">
                        Seamlessly manage appointments, health records, and access barangay health services from the comfort of your home.
                    </p>
                </div>
            </div>

            <div class="relative z-10 mt-12">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 flex items-center gap-5 border border-white/20 max-w-sm shadow-lg hover:bg-white/15 transition-colors cursor-default group">
                    <div class="flex -space-x-4">
                        <div class="w-12 h-12 rounded-full bg-blue-400 border-2 border-brand-700 shadow-md group-hover:scale-110 transition-transform z-30"></div>
                        <div class="w-12 h-12 rounded-full bg-teal-400 border-2 border-brand-700 shadow-md group-hover:scale-110 transition-transform z-20"></div>
                        <div class="w-12 h-12 rounded-full bg-purple-400 border-2 border-brand-700 shadow-md group-hover:scale-110 transition-transform z-10"></div>
                    </div>
                    <div>
                        <div class="text-white font-bold text-2xl">500+</div>
                        <div class="text-brand-100 text-sm font-medium uppercase tracking-wider">Active Residents</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-16 flex flex-col justify-center bg-white">
            <div class="max-w-md mx-auto w-full">
                <div class="mb-10">
                    <h2 class="text-4xl font-black text-gray-900 mb-2 tracking-tight">Welcome Back! 👋</h2>
                    <p class="text-gray-500 text-lg font-medium">Please enter your details to sign in.</p>
                </div>

                @if (session('info'))
                    <div class="mb-8 bg-blue-50 border-l-4 border-blue-500 text-blue-700 px-6 py-4 rounded-r-lg shadow-sm flex items-start gap-3 animate-fade-in-down">
                        <i class="bi bi-info-circle-fill text-xl mt-0.5"></i>
                        <div>
                            <p class="font-black text-lg">Session Info</p>
                            <p class="text-sm font-bold mt-1">{{ session('info') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-8 bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-sm flex items-start gap-3 animate-shake">
                        <i class="bi bi-exclamation-triangle-fill text-xl mt-0.5"></i>
                        <div>
                            <p class="font-black text-lg">Login Failed</p>
                            <ul class="list-disc list-inside text-sm mt-1 space-y-1 font-bold">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-7">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Email Address</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                                <i class="bi bi-envelope-fill text-xl"></i>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="block w-full pl-12 pr-4 py-4 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 text-lg text-gray-900 placeholder-gray-400 transition-all shadow-sm hover:border-gray-300 font-bold bg-gray-50 focus:bg-white"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1">Password</label>
                            <a href="{{ route('password.forgot') }}" class="text-xs font-black text-brand-600 hover:text-brand-700 transition-all">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                                <i class="bi bi-lock-fill text-xl"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="block w-full !pl-12 !pr-14 py-4 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 text-lg text-gray-900 placeholder-gray-400 transition-all shadow-sm hover:border-gray-300 font-bold bg-gray-50 focus:bg-white"
                                placeholder="••••••••">
                            <button type="button" id="togglePassword" 
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-brand-600 cursor-pointer focus:outline-none transition-colors">
                                <i class="bi bi-eye-slash-fill text-xl" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center py-2">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 rounded cursor-pointer transition-colors shadow-sm">
                        <label for="remember" class="ml-3 block text-sm text-gray-500 font-bold cursor-pointer select-none uppercase tracking-widest">
                            Stay signed in
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center items-center py-5 px-6 border border-transparent rounded-2xl shadow-xl text-sm font-black uppercase tracking-[0.2em] text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 transition-all transform hover:-translate-y-1 active:scale-95">
                        Sign In <i class="bi bi-arrow-right ml-2 text-xl"></i>
                    </button>
                </form>

                <div class="mt-10 pt-6 border-t border-gray-100 text-center">
                    <p class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} E-Barangay Health System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        if (type === 'text') {
            eyeIcon.classList.remove('bi-eye-slash-fill');
            eyeIcon.classList.add('bi-eye-fill');
        } else {
            eyeIcon.classList.remove('bi-eye-fill');
            eyeIcon.classList.add('bi-eye-slash-fill');
        }
    });
</script>

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
    .animation-delay-4000 {
        animation-delay: 4s;
    }
    .pattern-grid-lg {
        background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 20px 20px;
    }
    
    /* Hide native password reveal button in Edge */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>
@endsection
