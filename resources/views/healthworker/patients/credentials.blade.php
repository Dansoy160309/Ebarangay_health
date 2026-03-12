@extends('layouts.app')

@section('title', 'New Patient Credentials')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 flex items-center justify-center min-h-[calc(100vh-100px)]">
    <div class="w-full">
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden relative">
            
            {{-- Header --}}
            <div class="px-8 pt-8 pb-4 text-center border-b border-gray-50 relative z-10">
                <div class="w-14 h-14 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 shadow-inner mx-auto mb-4">
                    <i class="bi bi-shield-check text-3xl"></i>
                </div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight mb-1">New Patient Credentials</h1>
                <p class="text-xs text-gray-500 font-medium">
                    Please copy or print these credentials for the patient.
                </p>
            </div>

            {{-- Decorative Background --}}
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-brand-50/50 to-transparent -z-0"></div>

            {{-- Body --}}
            <div class="px-8 py-6 space-y-6" id="printableCredentials">
                <div class="flex justify-center gap-2 mb-1 no-print">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-widest">Privacy Protected</span>
                    <span class="px-3 py-1 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-widest">HIPAA Compliant</span>
                </div>

                <div class="bg-gray-50/80 rounded-[2rem] p-6 border border-gray-100 shadow-inner space-y-4 print-box">
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest ml-2">Email Address</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400">
                                <i class="bi bi-envelope-at text-base"></i>
                            </div>
                            <input type="text" readonly value="{{ session('new_patient_credentials')['email'] }}" id="credentialEmail"
                                   class="block w-full pl-12 pr-6 py-3.5 bg-white border-none rounded-2xl text-base font-black text-gray-900 shadow-sm ring-1 ring-gray-100">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest ml-2">Generated Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400">
                                <i class="bi bi-key text-base"></i>
                            </div>
                            <input type="text" readonly value="{{ session('new_patient_credentials')['password'] }}" id="credentialPassword"
                                   class="block w-full pl-12 pr-6 py-3.5 bg-white border-none rounded-2xl text-base font-black text-brand-600 shadow-sm ring-1 ring-gray-100 font-mono tracking-wider">
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 rounded-xl p-3.5 flex gap-3 border border-orange-100">
                    <i class="bi bi-info-circle-fill text-orange-500 text-lg shrink-0"></i>
                    <p class="text-[11px] text-orange-800 font-medium leading-relaxed">
                        <span class="font-black uppercase tracking-wide inline text-orange-600">Security Notice:</span>
                        Advise the patient to change their password after first login.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
                <button onclick="copyCredentials()" 
                        class="flex-1 flex items-center justify-center gap-2.5 px-6 py-4 bg-white border border-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-100 transition-all shadow-sm active:scale-95 group">
                    <i class="bi bi-clipboard2-check text-base group-hover:scale-110 transition-transform"></i> Copy Details
                </button>
                <button onclick="printCredentials()" 
                        class="flex-1 flex items-center justify-center gap-2.5 px-6 py-4 bg-brand-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/20 active:scale-95 group">
                    <i class="bi bi-printer text-base group-hover:scale-110 transition-transform"></i> Print Card
                </button>
            </div>
            
            <div class="px-8 pb-6 text-center">
                <a href="{{ route('midwife.patients.index') }}" class="inline-flex items-center gap-2 text-[10px] font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                    <i class="bi bi-arrow-left"></i> Return to Patient List
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableCredentials, #printableCredentials * { visibility: visible; }
        #printableCredentials { position: absolute; left: 0; top: 0; width: 100%; padding: 40px; }
        .no-print { display: none !important; }
        .print-box { border: 2px solid #e5e7eb !important; background-color: #fff !important; box-shadow: none !important; }
    }
</style>

<script>
    function copyCredentials() {
        const email = document.getElementById('credentialEmail').value;
        const password = document.getElementById('credentialPassword').value;
        const text = `E-Barangay Health Portal\n\nEmail: ${email}\nPassword: ${password}\n\nPlease change your password after logging in.`;
        
        navigator.clipboard.writeText(text).then(() => {
            alert('Credentials copied to clipboard!');
        });
    }

    function printCredentials() {
        window.print();
    }
</script>
@endsection