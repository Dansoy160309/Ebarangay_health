@extends('layouts.app')

@section('title', 'Manage Services')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-[2rem] shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <i class="bi bi-heart-pulse-fill"></i> Manage Services
                </h1>
                <p class="text-brand-100 mt-2 text-lg">Configure the medical services available in the barangay health center.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.services.create') }}" 
                   class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-brand-600 font-bold shadow-sm hover:bg-brand-50 hover:scale-105 transition-all transform group">
                    <div class="w-6 h-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center mr-2 group-hover:bg-brand-200 transition">
                        <i class="bi bi-plus-lg text-sm"></i>
                    </div>
                    Add New Service
                </a>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl flex items-start justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="bi bi-check-lg text-lg"></i>
                </div>
                <div>
                    <h3 class="text-green-800 font-bold">Success</h3>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
            <button @click="show = false" class="text-green-500 hover:text-green-700 transition rounded-lg p-1 hover:bg-green-100">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    {{-- Services Content --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Toolbar --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-gray-500 text-sm">
                <i class="bi bi-info-circle"></i>
                <span>Showing all available services</span>
            </div>
            
            {{-- Search could go here if implemented in controller --}}
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden p-4 space-y-4">
            @forelse($services as $service)
                <div class="bg-white rounded-[1.5rem] p-5 shadow-sm relative overflow-hidden border border-gray-100">
                    {{-- Header: Name & Provider --}}
                    <div class="flex justify-between items-start mb-4 gap-2">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shadow-sm shrink-0">
                                <i class="bi bi-capsule"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ $service->name }}</h3>
                                <p class="text-xs text-gray-500">ID: #{{ $service->id }}</p>
                            </div>
                        </div>
                        @php
                            $colorClass = match($service->provider_type) {
                                'Doctor' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'Midwife' => 'bg-pink-50 text-pink-700 border-pink-100',
                                'Both' => 'bg-purple-50 text-purple-700 border-purple-100',
                                default => 'bg-gray-50 text-gray-700 border-gray-100'
                            };
                            $icon = match($service->provider_type) {
                                'Doctor' => 'bi-person-badge',
                                'Midwife' => 'bi-person-hearts',
                                'Both' => 'bi-people-fill',
                                default => 'bi-question-circle'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $colorClass }}">
                            <i class="bi {{ $icon }}"></i>
                            {{ $service->provider_type }}
                        </span>
                    </div>

                    {{-- Body: Description --}}
                    <div class="mb-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <p class="text-sm text-gray-600">
                            @if($service->description)
                                {{ Str::limit($service->description, 100) }}
                            @else
                                <span class="text-gray-400 italic">No description provided</span>
                            @endif
                        </p>
                    </div>

                    {{-- Footer: Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                        <a href="{{ route('admin.services.edit', $service->id) }}" 
                           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all border border-blue-200 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-pencil-square text-sm"></i>
                            Edit
                        </a>
                        
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this service?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 text-red-700 hover:bg-red-100 transition-all border border-red-200 text-xs font-bold uppercase tracking-wider">
                                <i class="bi bi-trash text-sm"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-[1.5rem] p-8 text-center shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-inbox text-3xl text-gray-300"></i>
                    </div>
                    <p class="text-base font-medium text-gray-900">No services found</p>
                    <p class="text-sm mt-1 text-gray-500">Get started by adding a new service.</p>
                </div>
            @endforelse
        </div>

        {{-- Table (Desktop Only) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Service Name</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Provider Type</th>
                        <th scope="col" class="relative px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($services as $service)
                        <tr class="hover:bg-blue-50/30 transition duration-150 ease-in-out group">
                            {{-- Name --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="bi bi-capsule"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $service->name }}</div>
                                        <div class="text-xs text-gray-500">ID: #{{ $service->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Description --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-md">
                                    @if($service->description)
                                        {{ Str::limit($service->description, 80) }}
                                    @else
                                        <span class="text-gray-400 italic">No description provided</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Provider Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $colorClass = match($service->provider_type) {
                                        'Doctor' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'Midwife' => 'bg-pink-100 text-pink-700 border-pink-200',
                                        'Both' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200'
                                    };
                                    $icon = match($service->provider_type) {
                                        'Doctor' => 'bi-person-badge',
                                        'Midwife' => 'bi-person-hearts',
                                        'Both' => 'bi-people-fill',
                                        default => 'bi-question-circle'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $colorClass }}">
                                    <i class="bi {{ $icon }}"></i>
                                    {{ $service->provider_type }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 transition-opacity">
                                    <a href="{{ route('admin.services.edit', $service->id) }}" 
                                       class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition-all shadow-sm group/btn"
                                       title="Edit Service">
                                        <i class="bi bi-pencil-square group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Edit</span>
                                    </a>
                                    
                                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this service?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition-all shadow-sm group/btn"
                                                title="Delete Service">
                                            <i class="bi bi-trash group-hover/btn:scale-110 transition-transform"></i>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="bi bi-inbox text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-base font-medium text-gray-900">No services found</p>
                                    <p class="text-sm mt-1">Get started by adding a new service.</p>
                                    <a href="{{ route('admin.services.create') }}" class="mt-4 text-brand-600 hover:underline font-medium">
                                        Add your first service
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
