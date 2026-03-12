{{-- resources/views/dashboard/default.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8 text-center">

    {{-- Alert messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-left">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-left">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6 text-left">
            {{ session('info') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="w-16 h-16 bg-brand-100 text-brand-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">
            <i class="bi bi-person-badge"></i>
        </div>

        <h3 class="text-2xl font-bold text-gray-800 mb-4">Welcome to E-Barangay Health</h3>
        
        <p class="text-gray-600 mb-6 leading-relaxed">
            Hello, <strong class="text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong>! <br>
            It seems your account does not have a specific dashboard assigned yet.
        </p>

        <div class="bg-gray-50 rounded-xl p-4 mb-8 border border-gray-100">
            <p class="text-sm text-gray-500">
                <i class="bi bi-info-circle mr-1"></i>
                Please contact the system administrator to update your account role or dashboard access.
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors shadow-sm">
            <i class="bi bi-house-door mr-2"></i> Go to Home
        </a>
    </div>
</div>
@endsection
