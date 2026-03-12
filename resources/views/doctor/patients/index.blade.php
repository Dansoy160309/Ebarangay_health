@extends('layouts.app')

@section('title', 'Patients')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
                    <i class="bi bi-people-fill"></i>
                    Patient List
                </h1>
                <p class="text-brand-100 mt-2 text-lg">
                    Browse all registered patients and open their profiles.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex flex-wrap gap-2 p-1 bg-gray-50 rounded-2xl w-fit">
                <a href="{{ route('doctor.patients.index', ['type' => 'all']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $type === 'all' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                   <i class="bi bi-people mr-1"></i> All
                </a>
                <a href="{{ route('doctor.patients.index', ['type' => 'account_holder']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $type === 'account_holder' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                   <i class="bi bi-person-badge mr-1"></i> Account Holders
                </a>
                <a href="{{ route('doctor.patients.index', ['type' => 'dependent']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $type === 'dependent' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                   <i class="bi bi-person-heart mr-1"></i> Dependents
                </a>
            </div>

            <div class="relative w-full lg:max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="bi bi-search"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Search by name, email, or contact..."
                    autocomplete="off"
                    class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-100 focus:border-brand-400 transition-all text-sm text-gray-700 placeholder-gray-400">
            </div>
        </div>
    </div>

    <div class="lg:hidden space-y-4">
        @forelse ($patients as $patient)
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 relative overflow-hidden hover:shadow-md transition-all duration-300 patient-row">
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

                <div>
                    <a href="{{ route('doctor.patients.show', $patient->id) }}"
                       class="w-full px-5 py-3 rounded-xl bg-brand-600 text-white text-sm font-bold shadow-lg shadow-brand-600/20 hover:bg-brand-700 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-person-lines-fill"></i> View Profile
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2rem] p-12 text-center shadow-sm border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-50">
                    <i class="bi bi-people text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No patients found</h3>
                <p class="text-sm text-gray-500">Try adjusting your filters.</p>
            </div>
        @endforelse
    </div>

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
                        <tr class="group hover:bg-gray-50/50 transition-colors patient-row">
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
                                <a href="{{ route('doctor.patients.show', $patient->id) }}"
                                   class="p-2.5 bg-brand-50 text-brand-600 rounded-xl hover:bg-brand-600 hover:text-white transition-all duration-300"
                                   title="View Profile">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-gray-500">
                                No patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $patients->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.patient-row');

        if (!searchInput) return;

        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@endsection

