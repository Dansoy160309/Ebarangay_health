@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="container mx-auto px-6 py-8 max-w-3xl">
    <div class="bg-white rounded-[2rem] shadow-lg p-8 border border-gray-100">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                <i class="bi bi-person-plus text-xl"></i>
            </div>
            Add New User
        </h1>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 text-sm font-bold rounded-full hover:bg-gray-100 transition-colors">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Role & Status (Moved to Top) -->
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">⚙️ Role & Status</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Select Role</label>
                    <select name="role" id="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('role') border-red-500 @enderror" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Select Role --</option>
                        <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>🧍 Patient</option>
                        <option value="health_worker" {{ old('role') == 'health_worker' ? 'selected' : '' }}>🏥 Health Worker</option>
                        <option value="midwife" {{ old('role') == 'midwife' ? 'selected' : '' }}>🩺 Midwife</option>
                        <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>👨‍⚕️ Doctor</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>👑 Admin</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="inline-flex items-center gap-2 mt-6">
                        <input type="checkbox" name="status" value="1" class="rounded text-brand-600" checked>
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">👤 Personal Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('first_name') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Middle Name <span class="text-xs text-gray-400">(Optional)</span></label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-300 focus:outline-none @error('middle_name') border-red-500 @enderror">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('last_name') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('dob') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Civil Status</label>
                    <select name="civil_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('civil_status') border-red-500 @enderror">
                        <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>-- Select Status --</option>
                        <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('gender') border-red-500 @enderror" required>
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>-- Select Gender --</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">📞 Contact Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('address') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Purok</label>
                    <input type="text" name="purok" value="{{ old('purok') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('purok') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="contact_no" value="{{ old('contact_no') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('contact_no') border-red-500 @enderror" required>
                </div>
                
                <!-- Emergency Contact Group -->
                <div class="sm:col-span-2 pt-4 border-t border-gray-100 mt-2">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Emergency Contact</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Name</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Full Name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-300 focus:outline-none">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Relationship</label>
                            <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" placeholder="e.g. Spouse, Parent"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-300 focus:outline-none">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Mobile No.</label>
                            <input type="text" name="emergency_no" value="{{ old('emergency_no') }}" placeholder="0912..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-300 focus:outline-none @error('emergency_no') border-red-500 @enderror">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div id="medical-info">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">🏥 Medical Background</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Blood Type <span class="text-xs text-gray-400">(Optional)</span></label>
                    <select name="blood_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none">
                        <option value="" {{ old('blood_type') ? '' : 'selected' }}>-- Optional --</option>
                        <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Allergies <span class="text-xs text-gray-400">(Optional)</span></label>
                    <textarea name="allergies" rows="2" placeholder="List any known allergies..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none">{{ old('allergies') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Existing Medical Conditions / History <span class="text-xs text-gray-400">(Optional)</span></label>
                    <textarea name="medical_history" rows="2" placeholder="e.g. Hypertension, Diabetes, Asthma..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none">{{ old('medical_history') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Current Medications <span class="text-xs text-gray-400">(Optional)</span></label>
                    <textarea name="current_medications" rows="2" placeholder="List current maintenance medicines..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none">{{ old('current_medications') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Family Medical History <span class="text-xs text-gray-400">(Optional)</span></label>
                    <textarea name="family_history" rows="2" placeholder="e.g. Father had heart disease..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none">{{ old('family_history') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Account Security -->
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">🔐 Account Security</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block mb-1 font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('email') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Password</label>
                    <input type="password" name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('password') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-400 focus:outline-none @error('password_confirmation') border-red-500 @enderror" required>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-8">
            <a href="{{ route('admin.users.index') }}" 
               class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
                Add User
            </button>
        </div>

    </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const medicalSection = document.getElementById('medical-info');
        
        function toggleSections() {
            const role = roleSelect.value;
            // Show medical section only if role is 'patient' or no role selected yet (optional, but better to hide if unsure)
            // Actually, if 'patient' is not selected, we hide it.
            if (role === 'patient') {
                medicalSection.style.display = 'block';
            } else {
                medicalSection.style.display = 'none';
            }
        }

        // Run on load to set initial state (e.g. if coming back from validation error)
        toggleSections();

        // Run on change
        roleSelect.addEventListener('change', toggleSections);
    });
</script>
@endsection
