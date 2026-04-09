{{-- resources/views/layouts/sidebar-links.blade.php --}}

@php
    use Illuminate\Support\Facades\Route;

    $user = auth()->user();
    $role = $user ? $user->role : null;

    // Default routes for guests or fallback
    $routes = [
        'dashboard' => auth()->check() ? route('dashboard') : route('login'),
        'announcement' => match($role) {
            'admin' => route('admin.announcements.index'),
            'health_worker' => route('healthworker.announcements.index'),
            'doctor' => route('doctor.announcements.index'),
            'midwife' => route('midwife.announcements.index'),
            'patient' => route('patient.announcements.index'),
            default => '#',
        },
        'appointment' => match($role) {
            'admin' => route('admin.appointments.index'),
            'health_worker' => route('healthworker.appointments.index'),
            'doctor' => route('doctor.appointments.index'),
            'midwife' => route('midwife.appointments.index'),
            'patient' => route('patient.appointments.index'),
            default => '#',
        },
        'profile' => match($role) {
            'admin' => route('admin.profile.index'),
            'health_worker' => route('healthworker.profile.index'),
            'doctor' => route('doctor.profile.index'),
            'midwife' => route('midwife.profile.index'),
            'patient' => route('patient.profile.index'),
            default => '#',
        },
        'reports' => $role === 'admin'
            ? route('admin.reports.index')
            : '#',
        'health_records' => match($role) {
            'admin' => route('admin.health-records.index'),
            'health_worker' => route('healthworker.health-records.index'),
            'doctor' => route('doctor.health-records.index'),
            'midwife' => route('midwife.health-records.index'),
            'patient' => route('patient.health-records.index'),
            default => '#',
        },
        'vaccine_inventory' => match($role) {
            'admin' => route('admin.vaccines.index'),
            default => '#',
        },
        'defaulters' => match($role) {
            'midwife' => route('midwife.appointments.defaulters'),
            default => '#',
        },
        'notifications' => auth()->check() ? route('notifications.index') : '#',
    ];
    
    // Helper for active class
    if (!function_exists('getLinkClasses')) {
        function getLinkClasses($isActive) {
            return $isActive
                ? 'flex items-center px-3 py-3 rounded-lg bg-brand-50 text-brand-700 font-semibold text-base shadow-sm transition-all duration-200 ring-1 ring-brand-200 border-l-4 border-brand-600'
                : 'flex items-center px-3 py-3 rounded-lg text-gray-700 font-medium text-base hover:bg-gray-100 hover:text-gray-900 transition-all duration-200 hover:shadow-sm';
        }
    }
@endphp

<div class="space-y-3">
    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-1">Menu</p>

    {{-- Dashboard --}}
    <a href="{{ $routes['dashboard'] }}"
       @click="sidebarOpen = false"
       class="{{ getLinkClasses(request()->routeIs('dashboard')) }}">
        <i class="bi bi-grid-fill mr-3 text-xl"></i> 
        <span>Dashboard</span>
    </a>

    {{-- Announcements --}}
    <a href="{{ $routes['announcement'] }}"
       @click="sidebarOpen = false"
       class="{{ getLinkClasses(request()->routeIs('*announcements*')) }}">
        <div class="relative">
            <i class="bi bi-megaphone-fill mr-3 text-xl text-brand-600"></i>
            @if(isset($unreadAnnouncementsCount) && $unreadAnnouncementsCount > 0)
                <span class="absolute -top-1 left-3 flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                </span>
            @endif
        </div>
        <span class="flex-1">Announcements</span>
        @if(isset($unreadAnnouncementsCount) && $unreadAnnouncementsCount > 0)
            <span class="bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-[10px] font-black">
                {{ $unreadAnnouncementsCount }}
            </span>
        @endif
    </a>
</div>

{{-- Role Specific --}}
<div class="space-y-3 mt-6">
    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Management</p>

    {{-- Patient --}}
    @if($role === 'patient')
        <a href="{{ $routes['appointment'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('patient.appointments*')) }}">
            <i class="bi bi-calendar-check-fill mr-3 text-xl"></i> 
            <span>My Appointments</span>
        </a>

        <a href="{{ $routes['health_records'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('patient.health-records*')) }}">
            <i class="bi bi-file-medical-fill mr-3 text-xl"></i> 
            <span>My Health Records</span>
        </a>
    @endif

    @if($role === 'health_worker')
        <a href="{{ $routes['appointment'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('healthworker.appointments*')) }}">
            <i class="bi bi-calendar-event-fill mr-3 text-xl"></i> 
            <span>Appointments</span>
        </a>

        <a href="{{ route('healthworker.slots.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('healthworker.slots.*')) }}">
            <i class="bi bi-clock-history mr-3 text-xl"></i> 
            <span>Appointment Slots</span>
        </a>

        <a href="{{ route('healthworker.patients.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('healthworker.patients*')) }}">
            <i class="bi bi-person-vcard-fill mr-3 text-xl"></i> 
            <span>Patients</span>
        </a>
    @endif

    {{-- Midwife --}}
    @if($role === 'midwife')
        <a href="{{ $routes['appointment'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('midwife.appointments*') && !request()->routeIs('midwife.appointments.defaulters')) }}">
            <i class="bi bi-calendar-event-fill mr-3 text-xl"></i> 
            <span>Appointments</span>
        </a>

        @if($routes['defaulters'])
        <a href="{{ $routes['defaulters'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('midwife.appointments.defaulters')) }}">
            <i class="bi bi-person-x-fill mr-3 text-xl text-red-600"></i> 
            <span>Defaulters</span>
        </a>
        @endif

        <a href="{{ $routes['health_records'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('midwife.health-records*')) }}">
            <i class="bi bi-file-medical-fill mr-3 text-xl"></i> 
            <span>Health Records</span>
        </a>

        <a href="{{ route('midwife.slots.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('midwife.slots.*')) }}">
            <i class="bi bi-clock-history mr-3 text-xl"></i> 
            <span>Appointment Slots</span>
        </a>

        <a href="{{ route('midwife.medicines.distribute.create') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('midwife.medicines.*')) }}">
            <i class="bi bi-capsule-pill mr-3 text-xl"></i> 
            <span>Medicines</span>
        </a>

        <a href="{{ route('midwife.doctor-presence.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('midwife.doctor-presence.*')) }}">
            <i class="bi bi-person-check-fill mr-3 text-xl"></i> 
            <span>Doctor Presence</span>
        </a>

        <a href="{{ route('midwife.referral-slips.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('midwife.referral-slips.*')) }}">
            <i class="bi bi-file-earmark-medical-fill mr-3 text-xl"></i> 
            <span>Referral Slips</span>
        </a>
    @endif

    @if($role === 'doctor')
        <a href="{{ $routes['appointment'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('doctor.appointments*')) }}">
            <i class="bi bi-calendar-event-fill mr-3 text-xl"></i> 
            <span>Appointments</span>
        </a>

        <a href="{{ route('doctor.availability.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('doctor.availability.*')) }}">
            <i class="bi bi-calendar-check-fill mr-3 text-xl"></i> 
            <span>My Duty Schedule</span>
        </a>

        <a href="{{ $routes['health_records'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('doctor.health-records*') || request()->routeIs('doctor.patients*')) }}">
            <i class="bi bi-file-medical-fill mr-3 text-xl"></i> 
            <span>Patient Health Records</span>
        </a>
    @endif

    {{-- Admin --}}
    @if($role === 'admin')
        <a href="{{ route('admin.users.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('admin.users.*')) }}">
            <i class="bi bi-people-fill mr-3 text-xl"></i> 
            <span>Users</span>
        </a>

        <a href="{{ route('admin.services.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('admin.services.*')) }}">
            <i class="bi bi-list-task mr-3 text-xl"></i> 
            <span>Services</span>
        </a>

        <a href="{{ route('admin.medicines.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('admin.medicines.*')) }}">
            <i class="bi bi-capsule-pill mr-3 text-xl"></i> 
            <span>Medicines</span>
        </a>

        @if($routes['vaccine_inventory'])
        <a href="{{ $routes['vaccine_inventory'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('admin.vaccines*')) }}">
            <i class="bi bi-box-seam-fill mr-3 text-xl text-indigo-600"></i> 
            <span>Vaccine Inventory</span>
        </a>
        @endif

        <a href="{{ $routes['reports'] }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('admin.reports.*')) }}">
            <i class="bi bi-file-earmark-bar-graph-fill mr-3 text-xl text-brand-600"></i> 
            <span class="flex-1">Reports</span>
            <i class="bi bi-chevron-down text-[10px] text-gray-400 group-hover:text-gray-600 transition-colors ml-auto"></i>
        </a>
        
        @if(request()->routeIs('admin.reports.*'))
        <div class="ml-10 space-y-2 mt-1 mb-2">
            <a href="{{ route('admin.reports.index') }}" 
               class="block text-xs font-bold {{ request()->routeIs('admin.reports.index') ? 'text-brand-600' : 'text-gray-500 hover:text-gray-900' }} transition-colors uppercase tracking-widest">
                • General Analytics
            </a>
            <a href="{{ route('admin.reports.fhsis') }}" 
               class="block text-xs font-bold {{ request()->routeIs('admin.reports.fhsis') ? 'text-brand-600' : 'text-gray-500 hover:text-gray-900' }} transition-colors uppercase tracking-widest">
                • FHSIS Summary
            </a>
            <a href="{{ route('admin.reports.vaccines') }}" 
               class="block text-xs font-bold {{ request()->routeIs('admin.reports.vaccines') ? 'text-brand-600' : 'text-gray-500 hover:text-gray-900' }} transition-colors uppercase tracking-widest">
                • Vaccine Usage
            </a>
        </div>
        @endif

        <a href="{{ route('admin.notifications.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('admin.notifications*')) }}">
            <i class="bi bi-bell-fill mr-3 text-xl"></i> 
            <span>Notification Logs</span>
        </a>

        <a href="{{ route('admin.sms.index') }}"
           @click="sidebarOpen = false"
           class="{{ getLinkClasses(request()->routeIs('admin.sms*')) }}">
            <i class="bi bi-chat-left-dots-fill mr-3 text-xl"></i> 
            <span>SMS Management</span>
        </a>
    @endif
</div>

{{-- Settings --}}
<div class="space-y-3 mt-8">
    <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Settings</p>

    <a href="{{ $routes['notifications'] }}"
       @click="sidebarOpen = false"
       class="{{ getLinkClasses(request()->routeIs('notifications.*')) }}">
        <div class="relative">
            <i class="bi bi-bell-fill mr-3 text-xl text-brand-600"></i>
            @if(isset($unreadCount) && $unreadCount > 0)
                <span class="absolute -top-1 left-3 flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                </span>
            @endif
        </div>
        <span class="flex-1">Notifications</span>
        @if(isset($unreadCount) && $unreadCount > 0)
            <span class="bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-sm font-bold">
                {{ $unreadCount }}
            </span>
        @endif
    </a>

    <a href="{{ $routes['profile'] }}"
       @click="sidebarOpen = false"
       class="{{ getLinkClasses(request()->routeIs('*profile*')) }}">
        <i class="bi bi-person-circle mr-3 text-xl text-brand-600"></i> 
        <span>Profile</span>
    </a>
</div>
