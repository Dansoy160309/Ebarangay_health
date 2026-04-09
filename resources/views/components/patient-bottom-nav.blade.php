@php
    $route = Route::currentRouteName();

    $items = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'bi-house-door-fill', 'href' => route('dashboard')],
        ['label' => 'Booking', 'route' => 'patient.appointments.index', 'icon' => 'bi-calendar2-check-fill', 'href' => route('patient.appointments.index')],
        ['label' => 'Records', 'route' => 'patient.health-records.index', 'icon' => 'bi-file-earmark-medical', 'href' => route('patient.health-records.index')],
        ['label' => 'Advisories', 'route' => 'patient.announcements.index', 'icon' => 'bi-bell-fill', 'href' => route('patient.announcements.index')],
        ['label' => 'Profile', 'route' => 'patient.profile.index', 'icon' => 'bi-person-circle', 'href' => route('patient.profile.index')],
    ];
@endphp

<nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-gray-200 shadow-2xl shadow-gray-900/10 md:hidden safe-area-inset-bottom">
    <div class="max-w-2xl mx-auto grid grid-cols-5 gap-0 px-1 py-2 text-xs font-bold">
        @foreach($items as $item)
            @php $active = $route === $item['route']; @endphp
            <a href="{{ $item['href'] }}" 
               class="flex flex-col items-center justify-center p-3 rounded-lg transition-all duration-200 active:scale-95 select-none touch-manipulation
                       {{ $active ? 'text-brand-600 bg-brand-50 shadow-md' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50' }}"
               role="tab"
               :aria-selected="{{ $active ? 'true' : 'false' }}">
                <i class="bi {{ $item['icon'] }} text-xl sm:text-2xl mb-1.5 transition-transform {{ $active ? 'scale-110' : '' }}"></i>
                <span class="text-[9px] sm:text-xs font-semibold">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>

{{-- Safe area spacer for mobile --}}
<div class="h-20 md:hidden"></div>

<style>
    @supports(padding: max(0px)) {
        nav.safe-area-inset-bottom {
            padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
        }
        
        .h-20 {
            height: calc(5rem + env(safe-area-inset-bottom));
        }
    }
</style>
