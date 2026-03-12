@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<h2 class="text-2xl font-bold mb-4">Reset Password</h2>

<form method="POST" action="{{ route('password.reset.submit') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    
    <div class="mb-4">
        <label class="block mb-1">Email</label>
        <input type="email" name="email" class="w-full border px-3 py-2 rounded" required>
        @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    
    <div class="mb-4">
        <label class="block mb-1">New Password</label>
        <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="mb-4">
        <label class="block mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded" required>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Reset Password</button>
</form>
@endsection
