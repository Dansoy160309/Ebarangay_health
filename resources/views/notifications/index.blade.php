@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12 relative overflow-hidden bg-[#fcfcfd] font-sans">
    {{-- Decorative Background Blobs --}}
    <div class="absolute top-0 right-0 w-[30rem] h-[30rem] bg-brand-50/20 rounded-full blur-3xl -mr-60 -mt-60 opacity-30 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[30rem] h-[30rem] bg-blue-50/20 rounded-full blur-3xl -ml-60 -mb-60 opacity-30 pointer-events-none"></div>

    <!-- Header Section -->
    <div class="bg-white rounded-[3.5rem] p-10 lg:p-14 border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-[10px] font-black text-brand-600 uppercase tracking-[0.3em] bg-brand-50 px-4 py-2 rounded-xl border border-brand-100/50">Communication Hub</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tight leading-[1.1]">
                    Your <span class="text-brand-600 underline decoration-brand-200 decoration-8 underline-offset-4">Notifications</span>
                </h1>
                <p class="text-gray-400 font-bold text-sm sm:text-base mt-4 flex items-center gap-2">
                    Stay connected with your health center's latest updates and medical reminders.
                </p>
            </div>

            @if($notifications->whereNull('read_at')->count() > 0)
                <a href="{{ route('notifications.markAll') }}" 
                   class="inline-flex items-center px-8 py-5 bg-white text-gray-500 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] border border-gray-100 hover:bg-gray-50 hover:text-brand-600 hover:border-brand-200 transition-all shadow-sm gap-4 active:scale-95">
                    <i class="bi bi-check2-all text-xl"></i>
                    Mark All Read
                </a>
            @endif
        </div>
    </div>

    <!-- Notifications List -->
    <div class="relative z-10 space-y-6">
        @forelse($notifications as $notification)
            @php
                $type = $notification->type;
                $isAppointment = str_contains($type, 'Appointment') || str_contains($type, 'Reminder');
                $isAnnouncement = str_contains($type, 'Announcement');
                
                $icon = 'bi-info-circle-fill';
                $colorClass = 'bg-blue-50 text-blue-600 border-blue-100';
                $iconColor = 'text-blue-600';
                
                if ($isAppointment) {
                    $icon = 'bi-calendar-event-fill';
                    $colorClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                    $iconColor = 'text-emerald-600';
                } elseif ($isAnnouncement) {
                    $icon = 'bi-megaphone-fill';
                    $colorClass = 'bg-orange-50 text-orange-600 border-orange-100';
                    $iconColor = 'text-orange-600';
                }
                
                $unread = is_null($notification->read_at);
            @endphp

            <div class="group block bg-white rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 border {{ $unread ? 'border-brand-200 bg-brand-50/10' : 'border-gray-100' }} relative overflow-hidden">
                @if($unread)
                    <div class="absolute top-0 right-0 w-2 h-full bg-brand-600 shadow-lg shadow-brand-500/50"></div>
                @endif
                
                <div class="p-8 lg:p-10">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-8 relative z-10">
                        <!-- Icon Container -->
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-2xl {{ $colorClass }} border flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                <i class="bi {{ $icon }}"></i>
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-black text-lg leading-relaxed mb-4 group-hover:text-brand-600 transition-colors duration-500 tracking-tight">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            <div class="flex flex-wrap items-center gap-4">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                                    <i class="bi bi-clock-fill text-gray-300"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                
                                @if(!$unread)
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-100 opacity-50">
                                        <i class="bi bi-check2-circle-fill"></i>
                                        Archived
                                    </span>
                                @else
                                    <span class="text-[9px] font-black text-brand-600 uppercase tracking-widest flex items-center gap-2 px-3 py-1.5 rounded-xl bg-brand-50 border border-brand-100 animate-pulse">
                                        <i class="bi bi-star-fill"></i>
                                        New Update
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Button -->
                        @if($unread)
                            <div class="flex-shrink-0 self-center">
                                <a href="{{ route('notifications.read', $notification->id) }}" 
                                   class="inline-flex items-center px-8 py-4 bg-brand-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all active:scale-95 gap-3">
                                    <i class="bi bi-check2"></i> Mark Read
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[3.5rem] p-24 text-center border-2 border-dashed border-gray-100 shadow-sm">
                <div class="mx-auto h-24 w-24 bg-gray-50 rounded-2xl flex items-center justify-center mb-8 shadow-inner border border-gray-100">
                    <i class="bi bi-bell-slash text-4xl text-gray-200"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Archive Clear</h3>
                <p class="text-gray-400 font-bold mt-2 text-sm uppercase tracking-widest leading-loose">You're all caught up! No new notifications at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
