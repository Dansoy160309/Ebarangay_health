@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<h2 class="text-2xl font-bold mb-4">Forgot Password</h2>

@if(session('success'))
<div class="bg-green-100 p-3 rounded mb-4">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('password.forgot.submit') }}">
    @csrf
    <div class="mb-4">
        <label class="block mb-1">Email</label>
        <input type="email" name="email" class="w-full border px-3 py-2 rounded" required>
        @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Send Reset Link</button>
</form>
@endsection
