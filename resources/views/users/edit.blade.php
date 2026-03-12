@extends('layouts.app')

@section('title', 'Edit User - ' . $user->full_name)

@section('content')
@php
    $adminPrefix = auth()->user()->isAdmin() ? 'admin.' : '';
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8 bg-[#f8fafc]">
    <!-- Header: Professional & Clean -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-3xl border border-slate-200/60 shadow-sm shadow-slate-200/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50/30 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
        
        <div class="flex items-center gap-6 relative z-10">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white flex items-center justify-center text-3xl font-bold shadow-lg shadow-brand-500/20">
                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Edit User Account</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider border border-slate-200">
                        UID: {{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="text-sm font-medium text-slate-500 flex items-center gap-1.5">
                        <i class="bi bi-person-check text-brand-500"></i>
                        {{ $user->full_name }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="bi bi-arrow-left text-lg"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="p-6 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-start gap-4">
            <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-1">Please correct the following:</h4>
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        @csrf
        @method('PUT')

        <!-- Left Sidebar: Account Identity & Quick Controls (Col-Span-4) -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Role & Status Card -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-sm space-y-8">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                    <i class="bi bi-gear-wide-connected text-brand-500"></i>
                    Account Controls
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">System Role</label>
                        <select name="role" id="role" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all @error('role') ring-2 ring-red-500 @enderror" required>
                            <option value="patient" {{ old('role', $user->role) == 'patient' ? 'selected' : '' }}>🧍 Patient</option>
                            <option value="health_worker" {{ old('role', $user->role) == 'health_worker' ? 'selected' : '' }}>🏥 Health Worker</option>
                            <option value="midwife" {{ old('role', $user->role) == 'midwife' ? 'selected' : '' }}>🩺 Midwife</option>
                            <option value="doctor" {{ old('role', $user->role) == 'doctor' ? 'selected' : '' }}>👨‍⚕️ Doctor</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>👑 Admin</option>
                        </select>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between group cursor-pointer hover:bg-white hover:border-brand-200 transition-all">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700 uppercase">Account Status</span>
                            <span class="text-[10px] font-medium text-slate-400 uppercase">Enable or disable access</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="status" value="1" class="sr-only peer" {{ old('status', $user->status) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                        </label>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100">
                    <button type="submit" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] hover:bg-brand-700 shadow-lg shadow-brand-500/20 transition-all active:scale-95 flex items-center justify-center gap-3">
                        <i class="bi bi-check-circle-fill text-lg"></i>
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="w-full mt-3 py-4 bg-white text-slate-500 border border-slate-200 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                        Cancel
                    </a>
                </div>
            </div>

            <!-- Profile Summary Card -->
            <div class="bg-slate-800 rounded-3xl p-8 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-400/10 rounded-full blur-2xl -mr-16 -mt-16 group-hover:bg-brand-400/20 transition-all"></div>
                <h3 class="text-[10px] font-black text-brand-400 uppercase tracking-[0.3em] mb-6 relative z-10">System Identity</h3>
                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between items-center py-2 border-b border-white/10">
                        <span class="text-xs text-slate-400 font-medium">Username</span>
                        <span class="text-sm font-bold text-slate-100">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/10">
                        <span class="text-xs text-slate-400 font-medium">Joined On</span>
                        <span class="text-sm font-bold text-slate-100">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-slate-400 font-medium">Last Updated</span>
                        <span class="text-sm font-bold text-slate-100">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: Profile Details (Col-Span-8) -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Personal Info Section -->
            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-10 space-y-10">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-brand-500 rounded-full"></span>
                    Personal Information
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all @error('first_name') ring-2 ring-red-500 @enderror" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" placeholder="Optional">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all @error('last_name') ring-2 ring-red-500 @enderror" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all @error('dob') ring-2 ring-red-500 @enderror" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Civil Status</label>
                        <select name="civil_status" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                            <option value="" disabled {{ old('civil_status', $user->civil_status) ? '' : 'selected' }}>-- Select --</option>
                            @foreach(['Single', 'Married', 'Widowed', 'Separated'] as $status)
                                <option value="{{ $status }}" {{ old('civil_status', $user->civil_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Gender</label>
                        <select name="gender" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" required>
                            <option value="" disabled>-- Select --</option>
                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact & Emergency Info Section -->
            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-10 space-y-10">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                    Contact & Residency
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Home Address</label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Purok</label>
                        <input type="text" name="purok" value="{{ old('purok', $user->purok) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Mobile Number</label>
                        <input type="text" name="contact_no" value="{{ old('contact_no', $user->contact_no) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" required>
                    </div>
                </div>

                <div class="p-8 bg-blue-50/30 rounded-3xl border border-blue-100/50 space-y-6">
                    <h5 class="text-[10px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-shield-plus"></i> Emergency Contact Information
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-2">Contact Name</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" 
                                   class="w-full px-4 py-3 bg-white border border-blue-100 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 text-sm font-bold text-gray-900 transition-all" placeholder="Full Name">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-2">Relationship</label>
                            <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $user->emergency_contact_relationship) }}" 
                                   class="w-full px-4 py-3 bg-white border border-blue-100 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 text-sm font-bold text-gray-900 transition-all" placeholder="e.g. Spouse">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-2">Contact No.</label>
                            <input type="text" name="emergency_no" value="{{ old('emergency_no', $user->emergency_no) }}" 
                                   class="w-full px-4 py-3 bg-white border border-blue-100 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 text-sm font-bold text-gray-900 transition-all" placeholder="0912...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Information Section (Conditional) -->
            <div id="medical-info" class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-10 space-y-10 transition-all">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-red-500 rounded-full"></span>
                    Medical Background
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-1 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Blood Type</label>
                        <select name="blood_type" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all">
                            @php($bloodType = old('blood_type', optional($user->patientProfile)->blood_type))
                            <option value="" {{ $bloodType ? '' : 'selected' }}>Unknown</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                <option value="{{ $type }}" {{ $bloodType == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Allergies</label>
                        <input type="text" name="allergies" value="{{ old('allergies', optional($user->patientProfile)->allergies) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" placeholder="List any known allergies...">
                    </div>
                    <div class="md:col-span-4 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Medical Conditions / History</label>
                        <textarea name="medical_history" rows="3" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all resize-none" placeholder="e.g. Hypertension, Diabetes, Asthma...">{{ old('medical_history', optional($user->patientProfile)->medical_history) }}</textarea>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Current Medications</label>
                        <textarea name="current_medications" rows="3" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all resize-none" placeholder="List current maintenance medicines...">{{ old('current_medications', optional($user->patientProfile)->current_medications) }}</textarea>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Family Medical History</label>
                        <textarea name="family_history" rows="3" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all resize-none" placeholder="e.g. History of heart disease...">{{ old('family_history', optional($user->patientProfile)->family_history) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Account Security Section -->
            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-10 space-y-10">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-[0.3em] flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-slate-800 rounded-full"></span>
                    Account Security
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all @error('email') ring-2 ring-red-500 @enderror" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Update Password</label>
                        <input type="password" name="password" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" placeholder="••••••••">
                        <p class="text-[10px] text-slate-400 ml-2 italic">Leave blank to keep current</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-brand-50 focus:bg-white text-sm font-bold text-gray-900 shadow-inner transition-all" placeholder="••••••••">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }

    .tracking-tight { letter-spacing: -0.02em; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const medicalSection = document.getElementById('medical-info');
        
        function toggleSections() {
            const role = roleSelect.value;
            // Show medical section only if role is 'patient'
            if (role === 'patient') {
                medicalSection.style.display = 'block';
                // Add a small animation effect
                setTimeout(() => medicalSection.style.opacity = '1', 50);
            } else {
                medicalSection.style.opacity = '0';
                medicalSection.style.display = 'none';
            }
        }

        // Run on load to set initial state
        toggleSections();

        // Run on change
        roleSelect.addEventListener('change', toggleSections);
    });
</script>
@endsection
