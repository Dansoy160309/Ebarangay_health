@extends('layouts.app')

@section('title', 'Message Templates')

@section('content')
<div class="flex flex-col gap-4 sm:gap-5">
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        @php
            $emailCount = $templates->where('type', 'email')->count();
            $smsCount = $templates->where('type', 'sms')->count();
            $activeCount = $templates->where('is_active', true)->count();
        @endphp

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-500">Template Management</p>
                <h1 class="text-2xl font-black text-gray-900">Message Templates</h1>
                <p class="text-sm text-gray-500 max-w-3xl">
                    Manage editable email and SMS content used for patient follow-up messages.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-indigo-50 text-indigo-700">Email: {{ $emailCount }}</span>
                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-blue-50 text-blue-700">SMS: {{ $smsCount }}</span>
                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700">Active: {{ $activeCount }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-100 bg-green-50/50 p-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
            <h2 class="font-bold text-gray-900">Templates</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($templates as $template)
                <div class="p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 hover:bg-gray-50/40 transition-colors">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $template->type === 'email' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $template->type }}
                            </span>
                            @if($template->is_default)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-700">Default</span>
                            @endif
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $template->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $template->is_active ? 'Active' : 'Disabled' }}</span>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 truncate">{{ $template->name }}</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-1">Preview</p>
                        <p class="text-sm text-gray-600 mt-1 template-preview">
                            {{ $template->type === 'email' ? $template->subject : $template->body }}
                        </p>
                        <p class="text-xs text-gray-400 mt-2">Updated {{ $template->updated_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('admin.message-templates.edit', $template) }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition self-start lg:self-auto">
                        Edit
                    </a>
                </div>
            @empty
                <div class="p-10 text-center">
                    <p class="text-gray-500 font-semibold">No templates found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .template-preview {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }
</style>
@endsection
