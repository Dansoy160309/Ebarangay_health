@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto px-4 sm:px-6 py-8">
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header & Toolbar -->
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <span class="bg-brand-100 text-brand-600 p-2.5 rounded-2xl shadow-sm">
                        <i class="bi bi-people-fill"></i>
                    </span>
                    <span class="flex flex-col">
                        <span>User Management</span>
                        <span class="text-xs font-normal text-gray-400">
                            @php
                                $roleNames = [
                                    'all' => 'All Users',
                                    'admin' => 'Admins',
                                    'doctor' => 'Doctors',
                                    'midwife' => 'Midwives',
                                    'health_worker' => 'Health Workers',
                                    'patient' => 'Patients',
                                ];
                                $roleTitle = $roleNames[$currentRole ?? 'all'] ?? $roleNames['all'];
                            @endphp
                            {{ $roleTitle }} View
                        </span>
                    </span>
                </h1>
                <p class="text-sm text-gray-500 mt-3 ml-1">
                    @php
                        $labelMap = [
                            'all' => 'Manage all users and roles.',
                            'admin' => 'Manage admin accounts.',
                            'doctor' => 'Manage doctor accounts.',
                            'midwife' => 'Manage midwife accounts.',
                            'health_worker' => 'Manage health worker accounts.',
                            'patient' => 'Manage patient accounts.',
                        ];
                        $roleLabel = $labelMap[$currentRole ?? 'all'] ?? $labelMap['all'];
                    @endphp
                    {{ $roleLabel }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <div class="flex flex-wrap gap-2 bg-gray-50 px-2 py-2 rounded-2xl border border-gray-200 sm:max-w-xl">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ ($currentRole ?? 'all') === 'all' ? 'bg-brand-600 text-white border-brand-600 shadow-sm' : 'bg-white text-gray-600 border-transparent hover:bg-gray-100' }}">
                        <i class="bi bi-grid-1x2-fill mr-1.5 text-[11px]"></i>
                        <span>All</span>
                    </a>
                    <a href="{{ route('admin.users.patients') }}"
                       class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ ($currentRole ?? '') === 'patient' ? 'bg-gray-800 text-white border-gray-800 shadow-sm' : 'bg-white text-gray-600 border-transparent hover:bg-gray-100' }}">
                        <i class="bi bi-person-hearts mr-1.5 text-[11px]"></i>
                        <span>Patients</span>
                    </a>
                    <a href="{{ route('admin.users.doctors') }}"
                       class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ ($currentRole ?? '') === 'doctor' ? 'bg-cyan-600 text-white border-cyan-600 shadow-sm' : 'bg-white text-gray-600 border-transparent hover:bg-gray-100' }}">
                        <i class="bi bi-stethoscope mr-1.5 text-[11px]"></i>
                        <span>Doctors</span>
                    </a>
                    <a href="{{ route('admin.users.midwives') }}"
                       class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ ($currentRole ?? '') === 'midwife' ? 'bg-pink-600 text-white border-pink-600 shadow-sm' : 'bg-white text-gray-600 border-transparent hover:bg-gray-100' }}">
                        <i class="bi bi-gender-female mr-1.5 text-[11px]"></i>
                        <span>Midwives</span>
                    </a>
                    <a href="{{ route('admin.users.health_workers') }}"
                       class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ ($currentRole ?? '') === 'health_worker' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-gray-600 border-transparent hover:bg-gray-100' }}">
                        <i class="bi bi-bandaid-fill mr-1.5 text-[11px]"></i>
                        <span>Health Workers</span>
                    </a>
                </div>

                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            autocomplete="off"
                            placeholder="Search name or email..."
                            class="w-full sm:w-56 lg:w-64 bg-white border border-gray-200 text-gray-700 py-2.5 pl-10 pr-3 rounded-2xl shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-sm"
                        >
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-gray-400">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl text-xs font-semibold border border-brand-200 bg-white text-brand-600 hover:bg-brand-50 hover:border-brand-400 shadow-sm"
                    >
                        <i class="bi bi-search"></i>
                        <span>Search</span>
                    </button>
                </form>

                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-600 text-white font-medium text-sm rounded-xl hover:bg-brand-700 active:bg-brand-800 transition-colors shadow-lg shadow-brand-500/30">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add User</span>
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mx-6 mt-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-xl flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill text-xl"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Mobile View: Cards -->
        <div class="md:hidden p-4 space-y-4 bg-gray-50/50">
            @forelse($users as $user)
                <div class="bg-white rounded-[1.5rem] p-5 shadow-sm relative overflow-hidden border border-gray-100">
                    {{-- Header: Role & Status --}}
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wide bg-purple-50 text-purple-700 border border-purple-100">
                                    <i class="bi bi-shield-lock-fill"></i> Admin
                                </span>
                            @elseif($user->role === 'doctor')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wide bg-cyan-50 text-cyan-700 border border-cyan-100">
                                    <i class="bi bi-heart-pulse-fill"></i> Doctor
                                </span>
                            @elseif($user->role === 'midwife')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wide bg-pink-50 text-pink-700 border border-pink-100">
                                    <i class="bi bi-gender-female"></i> Midwife
                                </span>
                            @elseif($user->role === 'health_worker')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-100">
                                    <i class="bi bi-bandaid-fill"></i> Health Worker
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wide bg-gray-50 text-gray-600 border border-gray-100">
                                    <i class="bi bi-person-fill"></i> Patient
                                </span>
                            @endif
                        </div>
                        @if($user->status)
                            <span class="w-2 h-2 rounded-full bg-green-500 shadow-sm shadow-green-200" title="Active"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-red-500 shadow-sm shadow-red-200" title="Inactive"></span>
                        @endif
                    </div>

                    {{-- Body: User Info --}}
                    <div class="flex items-center gap-4 mb-5">
                         <div class="w-14 h-14 rounded-[1rem] bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-xl shrink-0 shadow-sm">
                             {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                         </div>
                         <div class="overflow-hidden">
                             <h4 class="text-lg font-bold text-gray-900 leading-tight truncate">
                                 {{ $user->first_name }} {{ $user->last_name }}
                                 @if(auth()->id() === $user->id)
                                     <span class="ml-1 text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded border border-gray-200 align-middle">YOU</span>
                                 @endif
                             </h4>
                             <p class="text-xs font-medium text-gray-500 mt-1 truncate">{{ $user->email }}</p>
                         </div>
                    </div>

                    {{-- Footer: Actions --}}
                    <div class="flex justify-between items-center pt-5 border-t border-gray-100">
                         <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                             Joined {{ $user->created_at->format('M d, Y') }}
                         </div>
                         <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-brand-600 hover:text-white transition shadow-sm border border-gray-100">
                                <i class="bi bi-eye-fill text-xs"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">View</span>
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-yellow-50 text-yellow-600 hover:bg-yellow-600 hover:text-white transition shadow-sm border border-yellow-100">
                                <i class="bi bi-pencil-fill text-xs"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Edit</span>
                            </a>
                         </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-[1.5rem] p-8 text-center text-gray-500 shadow-sm border border-gray-100">
                    <div class="mb-3 bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                        <i class="bi bi-people text-2xl text-gray-400"></i>
                    </div>
                    <p class="font-medium text-sm">No users found.</p>
                </div>
            @endforelse
            
            {{-- Mobile Pagination --}}
            <div class="mt-4">
                 {{ $users->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Desktop View: Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-bold text-left">User Details</th>
                        <th class="px-6 py-4 font-bold text-left">Role</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                        <th class="px-6 py-4 font-bold text-left">Joined Date</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="group hover:bg-gray-50/50 transition-colors duration-200">
                            <!-- User Details -->
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-sm">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800 flex items-center gap-2">
                                            <span class="truncate max-w-[180px] sm:max-w-[250px]" title="{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}">
                                                {{ $user->first_name }} {{ $user->middle_name ? $user->middle_name[0] . '.' : '' }} {{ $user->last_name }}
                                            </span>
                                            @if(auth()->id() === $user->id)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">YOU</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-[200px]" title="{{ $user->email }}">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-6 py-5">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wide bg-purple-50 text-purple-700 border border-purple-100">
                                        <i class="bi bi-shield-lock-fill"></i> Admin
                                    </span>
                                @elseif($user->role === 'doctor')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wide bg-cyan-50 text-cyan-700 border border-cyan-100">
                                        <i class="bi bi-heart-pulse-fill"></i> Doctor
                                    </span>
                                @elseif($user->role === 'midwife')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wide bg-pink-50 text-pink-700 border border-pink-100">
                                        <i class="bi bi-gender-female"></i> Midwife
                                    </span>
                                @elseif($user->role === 'health_worker')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-100">
                                        <i class="bi bi-bandaid-fill"></i> Health Worker
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wide bg-gray-50 text-gray-600 border border-gray-100">
                                        <i class="bi bi-person-fill"></i> Patient
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-5 text-center">
                                @if($user->status)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-green-50 text-green-700 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide bg-red-50 text-red-700 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-5">
                                <div class="text-sm text-gray-600 font-medium">
                                    {{ $user->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $user->created_at->format('h:i A') }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.show', $user->id) }}" 
                                       class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-brand-600 hover:border-brand-300 hover:bg-brand-50 transition-all shadow-sm group/btn"
                                       title="View Details">
                                        <i class="bi bi-eye-fill group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">View</span>
                                    </a>

                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-yellow-600 hover:border-yellow-300 hover:bg-yellow-50 transition-all shadow-sm group/btn"
                                       title="Edit User">
                                        <i class="bi bi-pencil-square group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Edit</span>
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition-all shadow-sm group/btn"
                                                    title="{{ $user->status ? 'Deactivate User' : 'Activate User' }}">
                                                <i class="bi {{ $user->status ? 'bi-toggle-on text-green-600' : 'bi-toggle-off text-gray-400' }} text-lg group-hover/btn:scale-110 transition-transform"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $user->status ? 'On' : 'Off' }}</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition-all shadow-sm group/btn"
                                                    title="Delete User">
                                                <i class="bi bi-trash3-fill group-hover/btn:scale-110 transition-transform"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        @php
                            $emptyTitles = [
                                'admin' => 'No admin accounts yet',
                                'doctor' => 'No doctor accounts yet',
                                'midwife' => 'No midwife accounts yet',
                                'health_worker' => 'No health workers yet',
                                'patient' => 'No patient accounts yet',
                                'all' => 'No users found',
                            ];
                            $emptyTitle = $emptyTitles[$currentRole ?? 'all'] ?? $emptyTitles['all'];
                        @endphp
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 shadow-sm">
                                        <i class="bi bi-people text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $emptyTitle }}</h3>
                                    <p class="text-sm mt-1 text-gray-500">
                                        @if(request('search'))
                                            No results for “{{ request('search') }}”. Try a different name or email.
                                        @else
                                            Click “Add User” to create the first account for this role.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="hidden md:block px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
