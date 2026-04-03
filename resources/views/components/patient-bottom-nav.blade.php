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

<nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg md:hidden">
    <div class="max-w-lg mx-auto grid grid-cols-5 gap-1 px-2 py-1 text-xs font-black">
        @foreach($items as $item)
            @php $active = $route === $item['route']; @endphp
            <a href="{{ $item['href'] }}" class="flex flex-col items-center justify-center py-2 rounded-lg transition-colors {{ $active ? 'text-brand-600 bg-brand-50' : 'text-gray-600 hover:text-brand-600 hover:bg-gray-50' }}">
                <i class="bi {{ $item['icon'] }} text-lg"></i>
                <span class="text-[10px] mt-0.5">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
