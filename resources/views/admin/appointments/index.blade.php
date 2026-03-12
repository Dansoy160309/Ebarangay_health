@extends('layouts.app')

@section('title', 'Monitor Appointments')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-calendar-week"></i> Scheduled Appointments
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Monitor and manage all patient appointments in the system.</p>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.appointments.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Search Patient</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" 
                           class="w-full pl-10 pr-4 py-3 text-sm border-gray-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 bg-gray-50/50" 
                           placeholder="Patient name or email...">
                </div>
            </div>
            
            <div class="md:col-span-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status Filter</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="bi bi-funnel"></i>
                    </div>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full pl-10 pr-10 py-3 text-sm border-gray-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 bg-gray-50/50 appearance-none">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div class="md:col-span-3">
                 <a href="{{ route('admin.appointments.index') }}" 
                    class="w-full px-6 py-3 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all flex items-center justify-center gap-2 border border-transparent">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
                 </a>
            </div>
        </form>
    </div>

    @include('components.status-legend')

    {{-- Mobile Card View --}}
    <div class="md:hidden space-y-4">
        @forelse($appointments as $appointment)
            @php
                $slot = $appointment->slot;
                $status = $appointment->status;
                $statusColors = [
                    'pending' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                    'approved' => 'bg-green-50 text-green-600 border-green-100',
                    'completed' => 'bg-brand-50 text-brand-600 border-brand-100',
                    'rejected' => 'bg-red-50 text-red-600 border-red-100',
                    'cancelled' => 'bg-gray-50 text-gray-600 border-gray-100',
                ];
                $statusClass = $statusColors[$status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
            @endphp
            <div class="bg-white rounded-[2rem] p-6 shadow-sm relative overflow-hidden border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2 text-gray-500 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
                        <i class="bi bi-clock-fill text-brand-400"></i>
                        {{ $appointment->formatted_date }} • {{ $appointment->formatted_time }}
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide border {{ $statusClass }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                <div class="mb-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-12 w-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 text-lg font-bold shrink-0 border border-brand-100">
                            {{ substr($appointment->display_patient_name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 line-clamp-1">{{ $appointment->display_patient_name }}</h3>
                            <p class="text-xs text-gray-500">{{ $appointment->display_patient_email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-700 bg-gray-50/80 p-3 rounded-2xl border border-gray-100">
                        <i class="bi bi-bandaid text-brand-500 text-lg"></i>
                        <span class="font-bold text-gray-800">{{ $slot?->service ?? $appointment->service }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                    <div class="text-xs text-gray-400 font-medium">
                        ID: #{{ $appointment->id }}
                    </div>
                    <a href="{{ route('admin.appointments.show', $appointment->id) }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-brand-50 text-brand-600 font-bold hover:bg-brand-600 hover:text-white transition-all duration-300">
                        Details <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2rem] p-12 text-center shadow-sm border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-50">
                    <i class="bi bi-calendar-x text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No appointments found</h3>
                <p class="text-sm text-gray-500">Try adjusting your search terms or filters.</p>
            </div>
        @endforelse
        
        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
    </div>

    {{-- Table Container (Desktop Only) --}}
    <div class="hidden md:block bg-white overflow-hidden shadow-sm rounded-[2rem] border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Service</th>
                        <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Schedule</th>
                        <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-8 py-5 text-right">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($appointments as $appointment)
                        @php
                            $slot = $appointment->slot;
                            $status = $appointment->status;
                        @endphp
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11">
                                        <div class="h-11 w-11 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 text-base font-bold border border-brand-100 group-hover:scale-110 transition-transform">
                                            {{ substr($appointment->display_patient_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $appointment->display_patient_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $appointment->display_patient_email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-800">{{ $slot?->service ?? $appointment->service }}</span>
                                    @if($slot?->doctor)
                                        <span class="text-xs text-brand-600 font-medium">
                                            <i class="bi bi-person-badge mr-1"></i>Dr. {{ $slot->doctor->full_name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <i class="bi bi-calendar3 text-brand-400"></i>
                                    {{ $appointment->formatted_date }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                    <i class="bi bi-clock text-gray-400"></i>
                                    {{ $appointment->formatted_time }}
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                        'approved' => 'bg-green-50 text-green-700 border-green-100',
                                        'no_show' => 'bg-orange-50 text-orange-700 border-orange-100',
                                        'archived' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'completed' => 'bg-brand-50 text-brand-700 border-brand-100',
                                        'rejected' => 'bg-red-50 text-red-700 border-red-100',
                                        'cancelled' => 'bg-gray-50 text-gray-700 border-gray-100',
                                    ];
                                    $statusClass = $statusColors[$status] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                                @endphp
                                @php
                                    $label = $status === 'no_show' ? 'No Show' : ($status === 'archived' ? 'Archived' : ucfirst($status));
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-2 
                                        @if($status === 'pending') bg-yellow-500
                                        @elseif($status === 'approved') bg-green-500
                                        @elseif($status === 'completed') bg-brand-500
                                        @elseif($status === 'no_show') bg-orange-500
                                        @elseif($status === 'rejected' || $status === 'cancelled') bg-red-500
                                        @else bg-gray-500 @endif"></span>
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-right">
                                <a href="{{ route('admin.appointments.show', $appointment->id) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-50 text-brand-600 font-bold hover:bg-brand-600 hover:text-white transition-all duration-300">
                                    View <i class="bi bi-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-6 text-gray-300 border border-gray-100 shadow-inner">
                                        <i class="bi bi-calendar-x text-5xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No appointments found</h3>
                                    <p class="text-sm text-gray-500 leading-relaxed mb-8 text-center">
                                        We couldn't find any appointments matching your criteria. Try adjusting your search or filters.
                                    </p>
                                    
                                    @if(request('search') || request('status'))
                                    <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-200 rounded-xl font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                                        <i class="bi bi-x-lg mr-2"></i> Clear All Filters
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="bg-gray-50/50 px-8 py-5 border-t border-gray-100">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection
