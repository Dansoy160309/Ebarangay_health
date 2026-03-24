{{-- resources/views/layouts/sidebar.blade.php --}}

{{-- Desktop Sidebar --}}
<aside class="w-64 bg-white border-r shadow-sm hidden md:flex flex-col justify-between h-screen fixed inset-y-0 left-0 z-30 print:hidden">
    
    <div class="h-16 flex items-center justify-between border-b bg-gradient-to-r from-brand-600 to-brand-700 px-4">
        <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm">
                <i class="bi bi-heart-pulse-fill"></i>
            </span>
            <span class="tracking-wide">E-Barangay Health</span>
        </a>
    </div>

    {{-- Navigation --}}
    <div class="flex-1 overflow-y-auto py-3 px-2 space-y-1.5 custom-scrollbar">
        @include('layouts.sidebar-links', [
            'unreadCount' => $unreadCount ?? 0,
            'unreadAnnouncementsCount' => $unreadAnnouncementsCount ?? 0
        ])
    </div>
</aside>

{{-- Mobile Sidebar Backdrop --}}
<div x-show="sidebarOpen"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 md:hidden print:hidden"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"
     x-cloak>
</div>

{{-- Mobile Sidebar --}}
<aside x-show="sidebarOpen"
       class="fixed inset-y-0 left-0 w-72 bg-white border-r shadow-2xl z-50 md:hidden flex flex-col print:hidden"
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       style="display: none;"
       x-cloak>

    <div class="h-16 flex items-center justify-between px-4 border-b bg-gradient-to-r from-brand-600 to-brand-500">
        <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm">
                <i class="bi bi-heart-pulse-fill"></i>
            </span>
            <span class="tracking-wide">E-Barangay Health</span>
        </a>
        <button @click="sidebarOpen = false" class="p-2 text-white hover:bg-white/10 rounded-lg transition focus:outline-none">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    {{-- Sidebar Links --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        @include('layouts.sidebar-links', [
            'unreadCount' => $unreadCount ?? 0,
            'unreadAnnouncementsCount' => $unreadAnnouncementsCount ?? 0
        ])
    </nav>
</aside>
