@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<h1 class="text-2xl font-bold mb-4">Reset Password</h1>

@if(session('error'))
    <div class="bg-red-100 border border-red-300 text-red-800 p-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('password.reset.submit') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="mb-4">
        <label class="block mb-1">New Password</label>
        <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
        @error('password') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label class="block mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded" required>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Reset Password
    </button>
</form>
@endsection
    