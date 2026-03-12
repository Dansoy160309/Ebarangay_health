@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob"></div>
        <div class="absolute top-[20%] -right-[10%] w-[35%] h-[35%] bg-blue-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] bg-purple-200/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-4000"></div>
    </div>

    <div class="max-w-4xl w-full space-y-8 bg-white rounded-[2rem] shadow-xl p-10 relative z-10 transition-all duration-300 hover:shadow-2xl">
        
        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-brand-50 mb-6 shadow-inner border border-brand-100">
                <i class="bi bi-person-plus-fill text-3xl text-brand-600"></i>
            </div>
            <h2 class="text-4xl font-black text-gray-900 tracking-tight">Create your account</h2>
            <p class="mt-3 text-gray-500 font-medium">
                Join the E-Barangay Health community today.
            </p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-sm flex items-start gap-3 animate-shake">
                <i class="bi bi-exclamation-triangle-fill text-xl mt-0.5"></i>
                <div>
                    <p class="font-black text-lg">Registration Failed</p>
                    <ul class="list-disc list-inside text-sm mt-1 space-y-1 font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-8" action="{{ route('register.submit') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- First Name --}}
                <div class="col-span-1">
                    <label for="first_name" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required 
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Middle Name --}}
                <div class="col-span-1">
                    <label for="middle_name" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Middle Name <span class="text-gray-400 text-[10px]">(Optional)</span></label>
                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}"
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Last Name --}}
                <div class="col-span-1">
                    <label for="last_name" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Date of Birth --}}
                <div class="col-span-1">
                    <label for="dob" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="{{ old('dob') }}" required
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 transition-all font-bold">
                </div>

                {{-- Gender --}}
                <div class="col-span-1">
                    <label for="gender" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Gender</label>
                    <select name="gender" id="gender" required
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 transition-all font-bold appearance-none">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                {{-- Purok --}}
                <div class="col-span-1">
                    <label for="purok" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Purok</label>
                    <input type="text" name="purok" id="purok" value="{{ old('purok') }}" required placeholder="e.g. Purok 1"
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Address --}}
                <div class="col-span-1 md:col-span-3">
                    <label for="address" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Full Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" required placeholder="Street, Barangay, City/Municipality"
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Contact No --}}
                <div class="col-span-1 md:col-span-1">
                    <label for="contact_no" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Contact Number</label>
                    <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}" required placeholder="09123456789"
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Emergency No --}}
                <div class="col-span-1 md:col-span-1">
                    <label for="emergency_no" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Emergency Contact</label>
                    <input type="text" name="emergency_no" id="emergency_no" value="{{ old('emergency_no') }}" required placeholder="09123456789"
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>
                 
                {{-- Email --}}
                <div class="col-span-1 md:col-span-1">
                    <label for="email" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Password --}}
                <div class="col-span-1 md:col-span-1.5">
                    <label for="password" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

                {{-- Confirm Password --}}
                <div class="col-span-1 md:col-span-1.5">
                    <label for="password_confirmation" class="block text-sm font-black text-gray-700 uppercase tracking-widest ml-1 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="block w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 focus:bg-white text-gray-900 placeholder-gray-400 transition-all font-bold">
                </div>

            </div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-6 border-t border-gray-100">
                <a href="{{ route('login') }}" class="text-sm font-black text-gray-400 hover:text-brand-600 uppercase tracking-widest transition-all">
                    <i class="bi bi-arrow-left"></i> Already registered?
                </a>

                <button type="submit" class="w-full md:w-auto flex justify-center items-center py-5 px-12 border border-transparent rounded-2xl shadow-xl text-sm font-black uppercase tracking-[0.2em] text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 transition-all transform hover:-translate-y-1 active:scale-95">
                    Register Now <i class="bi bi-check-lg ml-2 text-xl"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

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
</style>
