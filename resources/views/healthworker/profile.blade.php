@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto p-6 max-w-md bg-white rounded shadow">

    <h1 class="text-2xl font-bold mb-4">My Profile</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('healthworker.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- First Name -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" 
                   class="w-full p-2 border rounded" required>
        </div>

        <!-- Middle Name -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Middle Name (Optional)</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" 
                   class="w-full p-2 border rounded">
        </div>

        <!-- Last Name -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" 
                   class="w-full p-2 border rounded" required>
        </div>

        <!-- DOB -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Date of Birth</label>
            <input type="date" name="dob" value="{{ old('dob', $user->dob) }}" class="w-full p-2 border rounded" required>
        </div>

        <!-- Gender -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Gender</label>
            <select name="gender" class="w-full p-2 border rounded" required>
                <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <!-- Address -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Address</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="w-full p-2 border rounded" required>
        </div>

        <!-- Purok -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Purok</label>
            <input type="text" name="purok" value="{{ old('purok', $user->purok) }}" class="w-full p-2 border rounded" required>
        </div>

        <!-- Contact Number -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Contact Number</label>
            <input type="text" name="contact_no" value="{{ old('contact_no', $user->contact_no) }}" class="w-full p-2 border rounded" required>
        </div>

        <!-- Emergency Number -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Emergency Number (Optional)</label>
            <input type="text" name="emergency_no" value="{{ old('emergency_no', $user->emergency_no) }}" class="w-full p-2 border rounded">
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Password <span class="text-gray-500 text-sm">(leave blank to keep current)</span></label>
            <input type="password" name="password" class="w-full p-2 border rounded">
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full p-2 border rounded">
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Update Profile
            </button>
        </div>
    </form>
</div>
@endsection
