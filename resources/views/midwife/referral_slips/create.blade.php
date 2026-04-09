@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4" x-data="referralForm()">
    {{-- Breadcrumbs/Header --}}
    <div class="mb-5">
        <a href="{{ route('midwife.referral-slips.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-brand-600 font-bold text-xs uppercase tracking-widest transition-colors mb-3">
            <i class="bi bi-arrow-left"></i> Back to list
        </a>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Create Referral Slip</h1>
        <p class="text-gray-500 font-medium mt-1">Fill out the clinical referral details below.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border-2 border-red-100 rounded-[2rem] p-5 animate-shake shadow-sm shadow-red-50">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 text-xl flex-shrink-0">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <p class="text-red-800 font-black text-sm uppercase tracking-wider mb-2">Please correct the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-red-600 text-xs font-bold">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('midwife.referral-slips.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- 1. Patient Selection & Basic Info --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-500/5 p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-brand-500"></div>
            
            <h2 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs">01</span>
                Patient & Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Patient Select --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Select Patient</label>
                    <div class="relative">
                        <input type="text" list="patients_list" name="patient_name_search" x-model="patientSearch" @input="handlePatientSearch()"
                               placeholder="Type name to search..."
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none">
                        <datalist id="patients_list">
                            @foreach($patients as $patient)
                                <option value="{{ $patient->full_name }}" data-id="{{ $patient->id }}">
                            @endforeach
                        </datalist>
                        <input type="hidden" name="patient_id" x-model="patientId">
                    </div>
                    @error('patient_id') <p class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                </div>

                {{-- Date --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Referral Date</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" 
                           class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none">
                    @error('date') <p class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                </div>

                {{-- Auto-filled fields --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Address</label>
                    <input type="text" x-model="patientAddress" readonly 
                           class="w-full px-6 py-4 bg-gray-100 border-none rounded-2xl font-bold text-gray-500 cursor-not-allowed outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Age</label>
                        <input type="text" name="patient_age" x-model="patientAge" readonly 
                               class="w-full px-6 py-4 bg-gray-100 border-none rounded-2xl font-bold text-gray-500 cursor-not-allowed outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Family No.</label>
                        <input type="text" name="family_no" x-model="familyNo" 
                               placeholder="Optional"
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none">
                    </div>
                </div>
            </div>
            <input type="hidden" name="patient_address" x-model="patientAddress">
        </div>

        {{-- 2. Referral Routing --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-500/5 p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-brand-500"></div>
            
            <h2 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs">02</span>
                Referral Routing
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Referred From --}}
                <div class="space-y-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1 mb-4 border-b border-gray-50 pb-2">Referred From</label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['RHM', 'PHN', 'PHD', 'SI', 'CHO'] as $from)
                            @php($isOld = is_array(old('referred_from')) && in_array($from, old('referred_from')))
                            <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-brand-50 transition-colors group">
                                <input type="checkbox" name="referred_from[]" value="{{ $from }}" {{ $isOld || (old('referred_from') === null && $from === 'RHM') ? 'checked' : '' }}
                                       class="w-5 h-5 rounded-lg border-gray-200 text-brand-600 focus:ring-brand-500/20">
                                <span class="text-sm font-black text-gray-700 group-hover:text-brand-600 transition-colors">{{ $from }}</span>
                            </label>
                        @endforeach
                        <div class="col-span-2 space-y-3">
                            @php($isOldOther = is_array(old('referred_from')) && in_array('Others', old('referred_from')))
                            <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-brand-50 transition-colors group">
                                <input type="checkbox" name="referred_from[]" value="Others" x-model="fromOthers"
                                       class="w-5 h-5 rounded-lg border-gray-200 text-brand-600 focus:ring-brand-500/20">
                                <span class="text-sm font-black text-gray-700 group-hover:text-brand-600 transition-colors">Others</span>
                            </label>
                            <input type="text" name="referred_from_other" x-show="fromOthers" x-cloak
                                   value="{{ old('referred_from_other') }}"
                                   placeholder="Specify other source..."
                                   class="w-full px-6 py-4 bg-gray-50 border border-brand-100 rounded-2xl font-bold text-gray-900 outline-none">
                        </div>
                    </div>
                    @error('referred_from') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase">{{ $message }}</p> @enderror
                </div>

                {{-- Referred To --}}
                <div class="space-y-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1 mb-4 border-b border-gray-50 pb-2">Referred To</label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['RHM', 'PHN', 'PHD', 'SI', 'CHO'] as $to)
                            @php($isOldTo = is_array(old('referred_to')) && in_array($to, old('referred_to')))
                            <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-brand-50 transition-colors group">
                                <input type="checkbox" name="referred_to[]" value="{{ $to }}" {{ $isOldTo ? 'checked' : '' }}
                                       class="w-5 h-5 rounded-lg border-gray-200 text-brand-600 focus:ring-brand-500/20">
                                <span class="text-sm font-black text-gray-700 group-hover:text-brand-600 transition-colors">{{ $to }}</span>
                            </label>
                        @endforeach
                        <div class="col-span-2 space-y-3">
                            <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-brand-50 transition-colors group">
                                <input type="checkbox" name="referred_to[]" value="Others" x-model="toOthers"
                                       class="w-5 h-5 rounded-lg border-gray-200 text-brand-600 focus:ring-brand-500/20">
                                <span class="text-sm font-black text-gray-700 group-hover:text-brand-600 transition-colors">Others</span>
                            </label>
                            <input type="text" name="referred_to_other" x-show="toOthers" x-cloak
                                   value="{{ old('referred_to_other') }}"
                                   placeholder="Specify destination facility/role..."
                                   class="w-full px-6 py-4 bg-gray-50 border border-brand-100 rounded-2xl font-bold text-gray-900 outline-none">
                        </div>
                    </div>
                    @error('referred_to') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- 3. Clinical Findings --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-500/5 p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-brand-500"></div>
            
            <h2 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs">03</span>
                Clinical Findings & Reason
            </h2>

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Pertinent Findings</label>
                    <textarea name="pertinent_findings" rows="3" placeholder="Symptoms, vitals (BP, Weight), physical findings..."
                              class="w-full px-6 py-4 bg-gray-50 border-none rounded-[2rem] font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none resize-none">{{ old('pertinent_findings') }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Reason for Referral</label>
                    <textarea name="reason_for_referral" rows="3" placeholder="e.g., For further management, specialized equipment needed..."
                              class="w-full px-6 py-4 bg-gray-50 border-none rounded-[2rem] font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none resize-none">{{ old('reason_for_referral') }}</textarea>
                </div>
            </div>
        </div>

        {{-- 4. Instructions & Actions --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-500/5 p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-brand-500"></div>
            
            <h2 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs">04</span>
                Instructions & Feedback
            </h2>

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1 italic text-brand-600">Instruction to Referring Level</label>
                    <textarea name="instruction_to_referring_level" rows="2" placeholder="Initial management instructions..."
                              class="w-full px-6 py-4 bg-brand-50/30 border border-brand-100 rounded-[2rem] font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none resize-none">{{ old('instruction_to_referring_level') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-3 border-t border-gray-50">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Actions Taken by Referred Level</label>
                        <textarea name="actions_taken_by_referred_level" rows="3" placeholder="Leave blank if unknown yet..."
                                  class="w-full px-6 py-4 bg-gray-50 border-none rounded-[2rem] font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none resize-none">{{ old('actions_taken_by_referred_level') }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Final Instructions back to Referring Level</label>
                        <textarea name="instructions_to_referring_level_final" rows="3" placeholder="Feedback/Follow-up care instructions..."
                                  class="w-full px-6 py-4 bg-gray-50 border-none rounded-[2rem] font-bold text-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all outline-none resize-none">{{ old('instructions_to_referring_level_final') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-4 pb-8">
            <button type="reset" class="px-6 py-3.5 text-gray-400 font-black text-xs uppercase tracking-widest hover:text-gray-600 transition-colors">
                Clear Form
            </button>
            <button type="submit" class="px-8 py-3.5 bg-brand-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all active:scale-95 flex items-center gap-2">
                Generate Referral Slip <i class="bi bi-file-earmark-pdf"></i>
            </button>
        </div>
    </form>
</div>

<script>
    function referralForm() {
        return {
            patientId: '{{ old("patient_id", $selectedPatient ? $selectedPatient->id : "") }}',
            patientSearch: '{{ old("patient_name_search", $selectedPatient ? $selectedPatient->full_name : "") }}',
            patientAddress: '{{ old("patient_address", $selectedPatient ? $selectedPatient->address . ", " . $selectedPatient->purok : "") }}',
            patientAge: '{{ old("patient_age", $selectedPatient ? $selectedPatient->age : "") }}',
            familyNo: '{{ old("family_no", $selectedPatient ? $selectedPatient->family_no : "") }}',
            fromOthers: '{{ (old("referred_from") && in_array("Others", (array)old("referred_from"))) ? "true" : "false" }}' === 'true',
            toOthers: '{{ (old("referred_to") && in_array("Others", (array)old("referred_to"))) ? "true" : "false" }}' === 'true',
            
            handlePatientSearch() {
                const list = document.getElementById('patients_list');
                const options = list.options;
                let found = false;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === this.patientSearch) {
                        this.patientId = options[i].getAttribute('data-id');
                        this.updatePatientInfo();
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    this.patientId = '';
                    this.patientAddress = '';
                    this.patientAge = '';
                    this.familyNo = '';
                }
            },
            
            async updatePatientInfo() {
                if (!this.patientId) return;
                
                try {
                    const response = await fetch(`/healthworker/patients/${this.patientId}/details`);
                    const data = await response.json();
                    this.patientAddress = data.patient.address;
                    this.patientAge = data.patient.age;
                    this.familyNo = data.patient.family_no || '';
                } catch (error) {
                    console.error('Error fetching patient details:', error);
                }
            }
        };
    }
</script>
@endsection
