@extends('layouts.app')

@section('title', 'Notification Logs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 text-[11px] font-black uppercase tracking-widest">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-brand-600 transition-colors flex items-center">
                    <i class="bi bi-house-door mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center text-gray-900">
                    <i class="bi bi-chevron-right mx-2 text-[8px]"></i>
                    <span>Notification Logs</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-widest mb-4">
                <i class="bi bi-bell-fill mr-2"></i>
                System Audit
            </div>
            <h1 class="text-5xl font-black text-gray-900 tracking-tight">Notification Logs</h1>
            <p class="text-gray-500 font-medium mt-2">Monitor all in-app notifications and resend supported messages if necessary.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 rounded-3xl border border-green-100 bg-green-50/50 p-4 text-sm text-green-800 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                    <i class="bi bi-list-ul"></i>
                </div>
                <h2 class="font-black text-gray-900 text-sm uppercase tracking-widest">All Notifications</h2>
            </div>
            <div class="text-[11px] font-black text-gray-400 uppercase tracking-widest">
                Showing <span class="text-gray-900">{{ $notifications->firstItem() ?? 0 }}</span> - <span class="text-gray-900">{{ $notifications->lastItem() ?? 0 }}</span> of {{ $notifications->total() }}
            </div>
        </div>

        {{-- Mobile View (Enhanced) --}}
        <div class="md:hidden p-6 space-y-4">
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
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full border {{ $typeBg }}">
                            <i class="bi {{ $typeIcon }} text-[10px]"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-700">{{ $readableType }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $statusClass }}">
                            {{ $status }}
                        </span>
                    </div>

                    <div class="mb-4">
                        @if($recipient)
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-9 w-9 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-black shrink-0 border border-brand-100/50">
                                    {{ substr($recipient->first_name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-black text-gray-900 text-sm">{{ $recipient->full_name ?? $recipient->email ?? 'User #'.$recipient->id }}</div>
                                    <div class="text-[10px] font-bold text-gray-400">{{ $recipient->email ?? '' }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-sm font-medium text-gray-600 line-clamp-2 leading-relaxed">{{ $data['message'] ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $notification->created_at->format('M d, Y') }}</span>
                            <span class="text-[9px] font-bold text-gray-300 uppercase">{{ $notification->created_at->format('h:i A') }}</span>
                        </div>
                        
                        @if(in_array($notification->type, [
                            \App\Notifications\UpcomingAppointmentReminder::class,
                            \App\Notifications\NewAnnouncementNotification::class,
                        ]))
                            <form action="{{ route('admin.notifications.resend', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-black shadow-lg shadow-gray-200 transition-all text-[10px] font-black uppercase tracking-widest">
                                    <i class="bi bi-arrow-clockwise"></i> Resend
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">No notifications found.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table (Enhanced) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Recipient</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Message</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Created</th>
                        <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
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
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($recipient)
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-black shrink-0 border border-brand-100/50">
                                            {{ substr($recipient->first_name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 group-hover:text-brand-600 transition-colors">{{ $recipient->full_name ?? $recipient->email ?? 'User #'.$recipient->id }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ $recipient->email ?? '' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 text-gray-400">
                                        <div class="h-10 w-10 rounded-2xl bg-gray-50 flex items-center justify-center">
                                            <i class="bi bi-person-x"></i>
                                        </div>
                                        <span class="text-xs font-bold uppercase tracking-widest">Unknown User</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div @class([
                                        'h-8 w-8 rounded-xl flex items-center justify-center text-sm',
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
                                    <span class="text-xs font-black text-gray-700 uppercase tracking-widest">{{ $readableType }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 max-w-sm">
                                <div class="text-sm font-medium text-gray-600 line-clamp-2 leading-relaxed" title="{{ $data['message'] ?? '' }}">
                                    {{ $data['message'] ?? '—' }}
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span @class([
                                    'inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border',
                                    'bg-red-50 text-red-600 border-red-100' => $status === 'failed',
                                    'bg-orange-50 text-orange-600 border-orange-100' => $status === 'resent',
                                    'bg-gray-50 text-gray-600 border-gray-200' => $status === 'read',
                                    'bg-emerald-50 text-emerald-600 border-emerald-100' => $status === 'sent',
                                ])>
                                    <span @class([
                                        'w-1 h-1 rounded-full mr-1.5',
                                        'bg-red-500 animate-pulse' => $status === 'failed',
                                        'bg-orange-500' => $status === 'resent',
                                        'bg-gray-400' => $status === 'read',
                                        'bg-emerald-500' => $status === 'sent',
                                    ])></span>
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900">{{ $notification->created_at->format('M d, Y') }}</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="h-1 w-1 rounded-full bg-brand-500"></span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $notification->created_at->format('h:i A') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                @if(in_array($notification->type, [
                                    \App\Notifications\UpcomingAppointmentReminder::class,
                                    \App\Notifications\NewAnnouncementNotification::class,
                                ]))
                                    <form action="{{ route('admin.notifications.resend', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:text-brand-600 hover:border-brand-200 shadow-sm transition-all text-[10px] font-black uppercase tracking-widest group-hover:bg-brand-50 group-hover:border-brand-100">
                                            <i class="bi bi-arrow-clockwise mr-1.5"></i> Resend
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest select-none cursor-not-allowed">No Action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-24 w-24 rounded-[2.5rem] bg-gray-50 flex items-center justify-center text-gray-200 text-4xl mb-6 border border-dashed border-gray-200">
                                        <i class="bi bi-bell-slash"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 tracking-tight">No Notifications Found</h3>
                                    <p class="text-gray-500 max-w-xs mx-auto mt-2 font-medium">There are currently no notification logs available to display.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

