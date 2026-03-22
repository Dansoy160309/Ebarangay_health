@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-3">
                    <i class="bi bi-bell-fill text-[8px]"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">Communication Hub</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight leading-tight mb-1 sm:mb-2">
                    Your <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Notifications</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Stay connected with your health center's latest updates and medical reminders.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                @if($notifications->whereNull('read_at')->count() > 0)
                    <a href="{{ route('notifications.markAll') }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-700 font-black text-[9px] sm:text-xs uppercase tracking-tighter shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                        <i class="bi bi-check2-all mr-1.5"></i>
                        Mark All Read
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="relative z-10 space-y-4">
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

            <div class="group block bg-white rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-500 border {{ $unread ? 'border-brand-200 bg-brand-50/10' : 'border-gray-100' }} relative overflow-hidden">
                @if($unread)
                    <div class="absolute top-0 right-0 w-1.5 h-full bg-brand-600 shadow-md shadow-brand-500/50"></div>
                @endif
                
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 relative z-10">
                        <!-- Icon Container -->
                        <div class="flex-shrink-0">
                            <div class="h-11 w-11 rounded-lg {{ $colorClass }} border flex items-center justify-center text-lg shadow-inner group-hover:scale-105 group-hover:rotate-3 transition-all duration-500">
                                <i class="bi {{ $icon }}"></i>
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-black text-sm leading-snug mb-2.5 group-hover:text-brand-600 transition-colors duration-500 tracking-tight">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-tighter flex items-center gap-1.5 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                                    <i class="bi bi-clock-fill text-gray-300 text-[7px]"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                
                                @if(!$unread)
                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-tighter flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 border border-gray-100 opacity-50">
                                        <i class="bi bi-check2-circle-fill text-[7px]"></i>
                                        Archived
                                    </span>
                                @else
                                    <span class="text-[8px] font-black text-brand-600 uppercase tracking-tighter flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-brand-50 border border-brand-100 animate-pulse">
                                        <i class="bi bi-star-fill text-[7px]"></i>
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
