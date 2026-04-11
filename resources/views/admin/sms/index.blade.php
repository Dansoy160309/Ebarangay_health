@extends('layouts.app')

@section('title', 'SMS Management')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    
    {{-- Top-Aligned Compact Header --}}
    <div class="relative z-10 bg-white rounded-2xl sm:rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-48 h-48 bg-brand-50 rounded-full blur-2xl -mr-24 -mt-24 opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-600 border border-brand-100 mb-2 sm:mb-3">
                    <i class="bi bi-chat-left-dots-fill text-[8px]"></i>
                    <span class="text-[8px] font-black uppercase tracking-tighter">Communications</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight leading-tight mb-1 sm:mb-2">
                    SMS & <span class="text-brand-600 underline decoration-brand-200 decoration-2 underline-offset-2">Notifications</span>
                </h1>
                <p class="text-gray-500 font-medium text-xs sm:text-sm leading-relaxed">
                    Control system alerts, broadcasts, and monitor delivery status in real-time.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <div class="bg-brand-50/50 backdrop-blur-md px-4 py-2.5 rounded-lg border border-brand-100">
                    <p class="text-[8px] font-black uppercase tracking-tighter text-brand-400 mb-0.5">PhilSMS Balance</p>
                    <p class="text-lg font-black text-brand-600">{{ $balance }} <span class="text-[8px] font-bold text-brand-400">Credits</span></p>
                </div>
                <a href="{{ route('admin.message-templates.index') }}" class="bg-white px-4 py-2.5 rounded-lg border border-gray-100 text-gray-700 hover:text-brand-600 hover:border-brand-200 transition-colors text-xs font-bold">
                    Message Templates
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-100 bg-green-50/50 p-3 text-[10px] text-green-800 flex items-center gap-2 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="h-6 w-6 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0 text-xs">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        {{-- Left Column: Settings --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- SMS Toggles --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 flex items-center gap-1.5 text-sm">
                        <i class="bi bi-sliders text-brand-500 text-xs"></i> Delivery Settings
                    </h3>
                </div>
                <form action="{{ route('admin.sms.update') }}" method="POST" class="p-4 space-y-4">
                    @csrf
                    
                    <div class="flex items-center justify-between p-3 bg-brand-50/50 rounded-lg border border-brand-100">
                        <div>
                            <p class="font-bold text-brand-900 text-xs">Global SMS</p>
                            <p class="text-[8px] text-brand-600 font-medium uppercase tracking-tighter">Master switch</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_enabled" value="1" class="sr-only peer" {{ ($settings['sms_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>

                    <div class="space-y-3 pt-1.5">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter px-2">Service Specific</p>
                        
                        @php
                            $toggles = [
                                'sms_appointment_reminders' => 'Reminders',
                                'sms_defaulter_recall' => 'Defaulter Recall',
                                'sms_auto_defaulter_first_reminder' => 'Auto First Defaulter',
                            ];
                        @endphp

                        @foreach($toggles as $key => $label)
                        <div class="flex items-center justify-between px-2">
                            <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer" {{ ($settings[$key] ?? '0') == '1' ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-500"></div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <button type="submit" class="w-full py-3 bg-brand-600 text-white rounded-xl font-bold text-sm hover:bg-brand-700 transition shadow-lg shadow-brand-200">
                        Save Changes
                    </button>
                </form>
            </div>

            {{-- Manual Broadcast --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-megaphone text-brand-500"></i> SMS Broadcast
                    </h3>
                </div>
                <form action="{{ route('admin.sms.broadcast') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Message</label>
                        <textarea name="message" rows="3" maxlength="160" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-brand-500 focus:border-brand-500"
                            placeholder="Type your emergency announcement..."></textarea>
                        <p class="text-[10px] text-gray-400 mt-1 text-right">Max 160 characters</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Target Audience</label>
                        <select name="target" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-brand-500 focus:border-brand-500">
                            <option value="all">All Registered Users</option>
                            <option value="patients">Patients Only</option>
                            <option value="health_workers">Staff Only (Doctors/HWs)</option>
                        </select>
                    </div>

                    <button type="submit" onclick="return confirm('Send this SMS to all selected users? Costs will apply.')" 
                        class="w-full py-3 bg-orange-500 text-white rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-lg shadow-orange-200">
                        Send Broadcast
                    </button>
                </form>
            </div>
        </div>

        {{-- Right Column: Logs --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden h-full">
                <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-clock-history text-brand-500"></i> Delivery Logs
                    </h3>
                    <form action="{{ route('admin.sms.clear-logs') }}" method="POST" onsubmit="return confirm('Clear all logs?')">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-600 uppercase tracking-widest">
                            Clear Logs
                        </button>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/30">
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Recipient</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Message</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Sent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <p class="text-sm font-bold text-gray-900">{{ $log->user->full_name ?? 'Guest' }}</p>
                                    <p class="text-[10px] text-gray-500 font-medium">{{ $log->recipient }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-600 line-clamp-1" title="{{ $log->message }}">{{ $log->message }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $log->status === 'sent' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right whitespace-nowrap">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $log->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic text-sm">No delivery records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-8 py-4 bg-gray-50/30 border-t border-gray-50">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
