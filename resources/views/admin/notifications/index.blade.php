@extends('layouts.app')

@section('title', 'Notification Logs')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-3">
                    <i class="bi bi-bell-fill text-[8px]"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">System Audit</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight leading-tight mb-1 sm:mb-2">
                    Notification <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Logs</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Monitor all in-app notifications and system alerts sent to patients and staff.
                </p>
            </div>
        </div>
    </div>

    {{-- Notification List --}}
    <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/30 border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-6 w-6 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-[9px]">
                    <i class="bi bi-list-ul"></i>
                </div>
                <h2 class="font-black text-gray-900 text-xs uppercase tracking-tighter">History Log</h2>
            </div>
            <div class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">
                Showing <span class="text-gray-900">{{ $notifications->firstItem() ?? 0 }}</span> - <span class="text-gray-900">{{ $notifications->lastItem() ?? 0 }}</span> of {{ $notifications->total() }}
            </div>
        </div>

        {{-- Mobile View (Enhanced) --}}
        <div class="md:hidden p-4 space-y-3">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $status = $data['status'] ?? 'sent';
                    $fullType = $notification->type;
                    $readableType = \Illuminate\Support\Str::headline(\Illuminate\Support\Str::afterLast($fullType, '\\'));
                    $recipient = $notification->notifiable;

                    $statusColors = [
                        'sent' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                        'failed' => 'bg-red-50 text-red-600 border-red-100',
                        'resent' => 'bg-orange-50 text-orange-600 border-orange-100',
                        'read' => 'bg-gray-50 text-gray-600 border-gray-100',
                    ];
                    $statusClass = $statusColors[$status] ?? 'bg-gray-50 text-gray-600 border-gray-100';

                    $typeIcon = match(true) {
                        str_contains($readableType, 'Announcement') => 'bi-megaphone-fill text-purple-600',
                        str_contains($readableType, 'Appointment') => 'bi-calendar-check-fill text-blue-600',
                        default => 'bi-bell-fill text-gray-600'
                    };
                    $typeBg = match(true) {
                        str_contains($readableType, 'Announcement') => 'bg-purple-50 border-purple-100',
                        str_contains($readableType, 'Appointment') => 'bg-blue-50 border-blue-100',
                        default => 'bg-gray-50 border-gray-100'
                    };
                @endphp
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border {{ $typeBg }}">
                            <i class="bi {{ $typeIcon }} text-[8px]"></i>
                            <span class="text-[8px] font-black uppercase tracking-tighter text-gray-700">{{ $readableType }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-tighter border {{ $statusClass }}">
                            {{ $status }}
                        </span>
                    </div>

                    <div class="mb-3">
                        @if($recipient)
                            <div class="flex items-center gap-2.5 mb-2.5">
                                <div class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-[8px] font-black shrink-0 border border-brand-100/50">
                                    {{ substr($recipient->first_name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-black text-gray-900 text-xs">{{ $recipient->full_name ?? $recipient->email ?? 'User #'.$recipient->id }}</div>
                                    <div class="text-[8px] font-bold text-gray-400">{{ $recipient->email ?? '' }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                            <p class="text-xs font-medium text-gray-600 line-clamp-2 leading-relaxed">{{ $data['message'] ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">{{ $notification->created_at->format('M d, Y') }}</span>
                            <span class="text-[8px] font-bold text-gray-300 uppercase">{{ $notification->created_at->format('h:i A') }}</span>
                        </div>
                        
                        @if(in_array($notification->type, [
                            \App\Notifications\UpcomingAppointmentReminder::class,
                            \App\Notifications\NewAnnouncementNotification::class,
                        ]))
                            <form action="{{ route('admin.notifications.resend', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-black shadow-md shadow-gray-200 transition-all text-[8px] font-black uppercase tracking-tighter">
                                    <i class="bi bi-arrow-clockwise text-xs"></i> Resend
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">No notifications found.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table (Enhanced) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Recipient</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Type</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Message</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Status</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Created</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-tighter text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($notifications as $notification)
                        @php
                            $data = $notification->data ?? [];
                            $status = $data['status'] ?? 'sent';
                            $fullType = $notification->type;
                            $readableType = \Illuminate\Support\Str::headline(\Illuminate\Support\Str::afterLast($fullType, '\\'));
                            $recipient = $notification->notifiable;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($recipient)
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-[8px] font-black shrink-0 border border-brand-100/50">
                                            {{ substr($recipient->first_name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-black text-gray-900 group-hover:text-brand-600 transition-colors">{{ $recipient->full_name ?? $recipient->email ?? 'User #'.$recipient->id }}</div>
                                            <div class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">{{ $recipient->email ?? '' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-gray-400">
                                        <div class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center text-xs">
                                            <i class="bi bi-person-x"></i>
                                        </div>
                                        <span class="text-[8px] font-bold uppercase tracking-tighter">Unknown User</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div @class([
                                        'h-7 w-7 rounded-lg flex items-center justify-center text-xs',
                                        'bg-purple-50 text-purple-600' => str_contains($readableType, 'Announcement'),
                                        'bg-blue-50 text-blue-600' => str_contains($readableType, 'Appointment'),
                                        'bg-gray-50 text-gray-600' => !str_contains($readableType, 'Announcement') && !str_contains($readableType, 'Appointment'),
                                    ])>
                                        @if(str_contains($readableType, 'Announcement'))
                                            <i class="bi bi-megaphone-fill"></i>
                                        @elseif(str_contains($readableType, 'Appointment'))
                                            <i class="bi bi-calendar-check-fill"></i>
                                        @else
                                            <i class="bi bi-bell-fill"></i>
                                        @endif
                                    </div>
                                    <span class="text-[8px] font-black text-gray-700 uppercase tracking-tighter">{{ $readableType }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 max-w-sm">
                                <div class="text-xs font-medium text-gray-600 line-clamp-2 leading-relaxed" title="{{ $data['message'] ?? '' }}">
                                    {{ $data['message'] ?? '—' }}
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-tighter border',
                                    'bg-red-50 text-red-600 border-red-100' => $status === 'failed',
                                    'bg-orange-50 text-orange-600 border-orange-100' => $status === 'resent',
                                    'bg-gray-50 text-gray-600 border-gray-200' => $status === 'read',
                                    'bg-emerald-50 text-emerald-600 border-emerald-100' => $status === 'sent',
                                ])>
                                    <span @class([
                                        'w-1 h-1 rounded-full mr-1',
                                        'bg-red-500 animate-pulse' => $status === 'failed',
                                        'bg-orange-500' => $status === 'resent',
                                        'bg-gray-400' => $status === 'read',
                                        'bg-emerald-500' => $status === 'sent',
                                    ])></span>
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-gray-900">{{ $notification->created_at->format('M d, Y') }}</span>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span class="h-0.5 w-0.5 rounded-full bg-brand-500"></span>
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">{{ $notification->created_at->format('h:i A') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                @if(in_array($notification->type, [
                                    \App\Notifications\UpcomingAppointmentReminder::class,
                                    \App\Notifications\NewAnnouncementNotification::class,
                                ]))
                                    <form action="{{ route('admin.notifications.resend', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 hover:text-brand-600 hover:border-brand-200 shadow-sm transition-all text-[8px] font-black uppercase tracking-tighter group-hover:bg-brand-50 group-hover:border-brand-100">
                                            <i class="bi bi-arrow-clockwise mr-1 text-xs"></i> Resend
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-tighter select-none cursor-not-allowed">No Action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-200 text-2xl mb-4 border border-dashed border-gray-200">
                                        <i class="bi bi-bell-slash"></i>
                                    </div>
                                    <h3 class="text-lg font-black text-gray-900 tracking-tight">No Notifications Found</h3>
                                    <p class="text-gray-500 max-w-xs mx-auto mt-1.5 font-medium text-xs">There are currently no notification logs available to display.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="px-5 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

