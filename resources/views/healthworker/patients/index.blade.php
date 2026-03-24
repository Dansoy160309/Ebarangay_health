{{-- resources/views/healthworker/patients/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Patients Management')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-3">
                    <i class="bi bi-people-fill text-xs"></i>
                    <span class="text-xs font-black uppercase tracking-tighter">Master Database</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-tight mb-1 sm:mb-2">
                    Patient <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Management</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs lg:text-sm leading-relaxed">
                    Manage patient records, linked dependent accounts, and comprehensive medical histories.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
                <a href="{{ route('midwife.patients.create') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-brand-600 text-white font-black text-[9px] sm:text-xs uppercase tracking-tighter shadow-lg shadow-brand-500/15 hover:bg-brand-700 hover:scale-105 transition-all transform group shrink-0">
                    <i class="bi bi-person-plus-fill mr-1.5 group-hover:rotate-12 transition-transform text-xs"></i>
                    Add New Patient
                </a>
            </div>
        </div>
    </div>

    {{-- Filter & Search Section (More Compact) --}}
    <div class="bg-white rounded-2xl shadow-md shadow-gray-200/30 border border-gray-100 p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Filter Tabs --}}
            <div class="flex items-center p-1 bg-gray-50 rounded-lg border border-gray-100 overflow-x-auto no-scrollbar">
                <a href="{{ route('midwife.patients.index', ['type' => 'all']) }}"
                   class="px-3.5 py-1.5 rounded-md text-xs font-black uppercase tracking-tighter transition-all whitespace-nowrap {{ $type === 'all' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-600' }}">
                   All
                </a>
                <a href="{{ route('midwife.patients.index', ['type' => 'account_holder']) }}"
                   class="px-3.5 py-1.5 rounded-md text-[9px] font-black uppercase tracking-tighter transition-all whitespace-nowrap {{ $type === 'account_holder' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-600' }}">
                   Account Holders
                </a>
                <a href="{{ route('midwife.patients.index', ['type' => 'dependent']) }}"
                   class="px-3.5 py-1.5 rounded-md text-[9px] font-black uppercase tracking-tighter transition-all whitespace-nowrap {{ $type === 'dependent' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-600' }}">
                   Dependents
                </a>
            </div>

            {{-- Search Input --}}
            <form action="{{ route('midwife.patients.index') }}" method="GET" class="relative w-full lg:max-w-md group">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                    <i class="bi bi-search text-[10px]"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, or family no..."
                    class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border-none rounded-lg focus:ring-3 focus:ring-brand-500/10 focus:bg-white transition-all text-sm font-bold text-gray-700 placeholder-gray-400 shadow-inner">
            </form>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="lg:hidden space-y-4">
        @forelse ($patients as $patient)
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative overflow-hidden hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-xl shrink-0 border border-brand-100">
                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-gray-900 truncate">
                            {{ $patient->last_name }}, {{ $patient->first_name }}
                        </h3>
                        <div class="flex items-center gap-2 text-xs text-gray-500 font-bold uppercase tracking-wider mt-1">
                             <span class="bg-gray-100 px-2 py-0.5 rounded-lg">{{ $patient->age }} yrs</span>
                             <span class="bg-gray-100 px-2 py-0.5 rounded-lg">{{ $patient->gender }}</span>
                             <span class="bg-brand-50 text-brand-600 px-2 py-0.5 rounded-lg">{{ $patient->family_no ?? 'No Family No.' }}</span>
                        </div>
                    </div>
                    <div class="shrink-0">
                         <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide border {{ $patient->status ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                            {{ $patient->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3 mb-5">
                     @if($patient->isDependent())
                        <div class="bg-pink-50/50 text-pink-600 px-4 py-2.5 rounded-2xl text-xs font-bold border border-pink-100 flex items-center gap-3">
                            <i class="bi bi-person-heart text-lg"></i>
                            <div class="min-w-0">
                                <p class="text-[10px] text-pink-400 uppercase tracking-wider">Guardian</p>
                                <p class="truncate">{{ $patient->guardian->full_name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                     @endif

                     <div class="bg-gray-50/80 p-4 rounded-2xl border border-gray-100 space-y-2.5">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i class="bi bi-envelope text-brand-400"></i>
                            <span class="truncate font-medium">
                                @if($patient->isDependent() && $patient->guardian)
                                    {{ $patient->guardian->email }}
                                @elseif(Str::startsWith($patient->email, 'walkin_'))
                                    <span class="text-gray-400 italic">Walk-in Patient</span>
                                @else
                                    {{ $patient->email }}
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i class="bi bi-telephone text-brand-400"></i>
                            <span class="font-medium">{{ $patient->contact_no ?? 'No contact info' }}</span>
                        </div>
                     </div>
                </div>

                <div class="flex gap-2">
                    @if(!$patient->isDependent())
                        <a href="{{ route('midwife.patients.show', ['patient' => $patient->id, 'add_dependent' => 1]) }}" 
                           class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-colors border border-purple-100"
                           title="Add Dependent">
                            <i class="bi bi-person-plus-fill"></i>
                        </a>
                    @endif
                    <a href="{{ route('midwife.patients.show', $patient->id) }}" 
                       class="flex-1 px-5 py-3 rounded-xl bg-brand-600 text-white text-sm font-bold shadow-lg shadow-brand-600/20 hover:bg-brand-700 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-person-lines-fill"></i> View Profile
                    </a>
                    <a href="{{ route('midwife.patients.edit', $patient->id) }}" 
                       class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition-colors border border-amber-100">
                        <i class="bi bi-pencil-fill"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2rem] p-12 text-center shadow-sm border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-50">
                    <i class="bi bi-people text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No patients found</h3>
                <p class="text-sm text-gray-500">Try adjusting your search terms.</p>
            </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden lg:block bg-white overflow-hidden shadow-sm rounded-[2rem] border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Patient Info</th>
                        <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contact Details</th>
                        <th scope="col" class="px-8 py-5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-8 py-5 text-right">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-50" id="patientsTable">
                    @forelse ($patients as $patient)
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 text-lg font-bold border border-brand-100 group-hover:scale-110 transition-transform">
                                            {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4 min-w-0">
                                        <div class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                            {{ $patient->last_name }}, {{ $patient->first_name }}
                                            @if($patient->middle_name) {{ substr($patient->middle_name, 0, 1) }}. @endif
                                            
                                            @if($patient->isDependent())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-pink-50 text-pink-600 border border-pink-100 tracking-wide uppercase">
                                                    Dependent
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 font-medium">
                                            {{ $patient->gender }} • {{ $patient->age }} yrs
                                            @if($patient->family_no)
                                                <span class="text-gray-300 mx-1">|</span>
                                                <span class="text-brand-600 font-bold uppercase tracking-widest text-[10px]">
                                                    <i class="bi bi-hash"></i> {{ $patient->family_no }}
                                                </span>
                                            @endif
                                            @if($patient->isDependent() && $patient->guardian)
                                                <span class="text-gray-300 mx-1">|</span>
                                                <span class="text-brand-600 font-bold">
                                                    <i class="bi bi-person-heart"></i> {{ $patient->guardian->full_name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-sm text-gray-700 font-medium">
                                        <i class="bi bi-envelope text-brand-400"></i>
                                        @if($patient->isDependent() && $patient->guardian)
                                            <span title="Guardian's Email">{{ $patient->guardian->email }}</span>
                                        @elseif(Str::startsWith($patient->email, 'walkin_'))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                <i class="bi bi-person-walking mr-1 text-sm"></i> Walk-in
                                            </span>
                                        @else
                                            {{ $patient->email }}
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                                        <i class="bi bi-telephone text-gray-400"></i>
                                        {{ $patient->contact_no ?? '—' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                                    {{ $patient->status 
                                        ? 'bg-green-50 text-green-700 border-green-200' 
                                        : 'bg-red-50 text-red-700 border-red-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $patient->status ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $patient->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td class="px-8 py-5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$patient->isDependent())
                                        <a href="{{ route('midwife.patients.show', ['patient' => $patient->id, 'add_dependent' => 1]) }}"
                                           class="p-2.5 bg-purple-50 text-purple-600 rounded-xl hover:bg-purple-600 hover:text-white transition-all duration-300"
                                           title="Add Dependent">
                                            <i class="bi bi-person-plus-fill"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('midwife.patients.show', $patient->id) }}"
                                       class="p-2.5 bg-brand-50 text-brand-600 rounded-xl hover:bg-brand-600 hover:text-white transition-all duration-300"
                                       title="View Profile">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('midwife.patients.edit', $patient->id) }}"
                                       class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition-all duration-300"
                                       title="Edit Details">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-6 text-gray-300 border border-gray-100 shadow-inner">
                                        <i class="bi bi-people text-5xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No patients found</h3>
                                    <p class="text-sm text-gray-500 leading-relaxed mb-8 text-center">
                                        We couldn't find any patients matching your search. Try adjusting your filters or search terms.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50/50 px-8 py-5 border-t border-gray-100">
            {{ $patients->links() }}
        </div>
    </div>
</div>

{{-- No live search needed, using server-side search --}}
@endsection

@push('modals')
    @if(session('new_patient_credentials'))
        <div x-data="{ open: true }" 
             x-show="open" 
             x-init="$nextTick(() => { console.log('Modal Initialized'); })"
             class="fixed inset-0 z-[100] overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div x-show="open" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                     aria-hidden="true" 
                     @click="open = false"></div>

                {{-- Modal panel --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-middle bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100">
                    
                    {{-- Header --}}
                    <div class="px-8 pt-8 pb-4 flex items-center justify-between border-b border-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                                <i class="bi bi-shield-check text-xl"></i>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight" id="modal-title">New Patient Credentials</h3>
                        </div>
                        <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-x-lg text-lg"></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-8 py-8 space-y-6" id="printableCredentials">
                        <div class="flex items-center gap-2 mb-4 no-print">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-widest">Privacy Protected</span>
                            <span class="px-3 py-1 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-widest">HIPAA Compliant</span>
                        </div>

                        <p class="text-sm font-bold text-gray-500 leading-relaxed no-print">
                            Please copy these credentials and give them to the patient. This dialog will close when you leave this page.
                        </p>

                        <div class="bg-gray-50/80 rounded-[2rem] p-8 border border-gray-100 shadow-inner space-y-6 print-box">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Email Address</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400">
                                        <i class="bi bi-envelope-at text-lg"></i>
                                    </div>
                                    <input type="text" readonly value="{{ session('new_patient_credentials')['email'] }}" id="credentialEmail"
                                           class="block w-full pl-14 pr-6 py-4 bg-white border-none rounded-2xl text-base font-black text-gray-900 shadow-sm ring-1 ring-gray-100">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Generated Password</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400">
                                        <i class="bi bi-key text-lg"></i>
                                    </div>
                                    <input type="text" readonly value="{{ session('new_patient_credentials')['password'] }}" id="credentialPassword"
                                           class="block w-full pl-14 pr-6 py-4 bg-white border-none rounded-2xl text-base font-black text-brand-600 shadow-sm ring-1 ring-gray-100 font-mono tracking-wider">
                                </div>
                            </div>
                        </div>

                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">
                            <i class="bi bi-info-circle mr-1"></i> For security reasons, please advise the patient to change their password after first login.
                        </p>
                    </div>

                    {{-- Footer --}}
                    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 grid grid-cols-2 gap-4">
                        <button onclick="copyCredentials()" 
                                class="flex items-center justify-center gap-2 px-6 py-4 bg-white border border-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-100 transition-all shadow-sm active:scale-95">
                            <i class="bi bi-clipboard2-check"></i> Copy Credentials
                        </button>
                        <button onclick="printCredentials()" 
                                class="flex items-center justify-center gap-2 px-6 py-4 bg-brand-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/20 active:scale-95">
                            <i class="bi bi-printer"></i> Print Credentials
                        </button>
                        <button @click="open = false" 
                                class="col-span-2 flex items-center justify-center gap-2 px-6 py-4 bg-gray-200 text-gray-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-300 transition-all shadow-sm active:scale-95">
                            Close
                        </button>
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
                .print-box { border: 2px solid #e5e7eb !important; background-color: #fff !important; }
            }
        </style>

        <script>
            function copyCredentials() {
                const email = document.getElementById('credentialEmail').value;
                const password = document.getElementById('credentialPassword').value;
                const text = `Email: ${email}\nPassword: ${password}`;
                
                navigator.clipboard.writeText(text).then(() => {
                    alert('Credentials copied to clipboard!');
                });
            }

            function printCredentials() {
                window.print();
            }
        </script>
    @endif
@endpush
