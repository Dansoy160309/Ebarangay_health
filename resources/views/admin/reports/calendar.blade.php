@extends('layouts.app')

@section('title', 'Admin Calendar Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50/50 pb-12">
    {{-- Top Navigation Bar --}}
    <nav class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-100 mb-8">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors">Admin</a>
                        <i class="bi bi-chevron-right text-[10px]"></i>
                        <a href="{{ route('admin.reports.index') }}" class="hover:text-brand-600 transition-colors">Reports & Analytics</a>
                        <i class="bi bi-chevron-right text-[10px]"></i>
                        <span class="text-gray-900">Calendar Dashboard</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.location.reload()" class="p-2.5 bg-white text-gray-600 rounded-xl border border-gray-200 hover:bg-gray-50 transition-all flex items-center gap-2 text-sm font-bold shadow-sm">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Refresh</span>
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="p-2.5 bg-brand-600 text-white rounded-xl hover:bg-brand-700 transition-all flex items-center gap-2 text-sm font-bold shadow-md shadow-brand-200">
                        <i class="bi bi-graph-up"></i>
                        <span>Switch to Analytics</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- Sidebar: Stats & Quick Filters --}}
            <div class="w-full lg:w-80 space-y-6">
                {{-- Calendar Overview Card --}}
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                            <i class="bi bi-calendar-range-fill"></i>
                        </div>
                        Overview
                    </h3>
                    <div class="space-y-4">
                        <div class="p-5 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-100">
                            <p class="text-[10px] font-black text-brand-100 uppercase tracking-widest mb-1">Total Calendar Events</p>
                            <div class="flex items-end gap-2">
                                <p class="text-3xl font-black">{{ count($events) }}</p>
                                <p class="text-xs font-bold text-brand-100 mb-1">active</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-3">
                            <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-0.5">Finished</p>
                                    <p class="text-xl font-black text-emerald-900">
                                        {{ collect($events)->where('extendedProps.status', 'completed')->count() + collect($events)->where('extendedProps.type', 'appointment_group')->filter(function($e) {
                                            return collect($e['extendedProps']['appointments'])->every(fn($a) => $a['status'] === 'completed');
                                        })->count() }}
                                    </p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <i class="bi bi-check2-circle text-lg"></i>
                                </div>
                            </div>
                            <div class="p-4 rounded-2xl bg-red-50/50 border border-red-100 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-0.5">No Show</p>
                                    <p class="text-xl font-black text-red-900">
                                        {{ collect($events)->where('extendedProps.status', 'no_show')->count() + collect($events)->where('extendedProps.type', 'appointment_group')->filter(function($e) {
                                            return collect($e['extendedProps']['appointments'])->every(fn($a) => $a['status'] === 'no_show');
                                        })->count() }}
                                    </p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                    <i class="bi bi-person-x text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Hub --}}
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                            <i class="bi bi-sliders"></i>
                        </div>
                        Filters
                    </h3>
                    <form id="filterForm" action="{{ route('admin.reports.calendar') }}" method="GET">
                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">View Mode</label>
                                <select name="view_type" id="typeFilter" class="w-full border border-gray-100 focus:ring-2 focus:ring-brand-500/20 text-sm font-bold text-gray-700 bg-gray-50 rounded-xl px-4 py-3 appearance-none cursor-pointer" onchange="document.getElementById('filterForm').submit()">
                                    <option value="all_events" {{ $viewType === 'all_events' ? 'selected' : '' }}>All Calendar Events</option>
                                    <option value="slots_only" {{ $viewType === 'slots_only' ? 'selected' : '' }}>Availability Slots Only</option>
                                    <option value="appointments_only" {{ $viewType === 'appointments_only' ? 'selected' : '' }}>Patient Appointments Only</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-3 block">Color Guide</label>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50/50 border border-transparent hover:border-gray-100 transition-all">
                                        <span class="w-3 h-3 rounded-full bg-blue-500 shadow-sm shadow-blue-200"></span>
                                        <span class="text-xs font-bold text-gray-600">Clinic Slots</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50/50 border border-transparent hover:border-gray-100 transition-all">
                                        <span class="w-3 h-3 rounded-full bg-yellow-400 shadow-sm shadow-yellow-200"></span>
                                        <span class="text-xs font-bold text-gray-600">Pending / Mixed</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50/50 border border-transparent hover:border-gray-100 transition-all">
                                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                                        <span class="text-xs font-bold text-gray-600">All Completed</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50/50 border border-transparent hover:border-gray-100 transition-all">
                                        <span class="w-3 h-3 rounded-full bg-red-400 shadow-sm shadow-red-200"></span>
                                        <span class="text-xs font-bold text-gray-600">All No Show</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Main Calendar Area --}}
            <div class="flex-1 min-w-0 space-y-8">
                <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 p-8 lg:p-10 relative overflow-hidden">
                    <div id="calendar" class="min-h-[800px]"></div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Event Details Modal --}}
<div id="eventModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-white/20 w-full max-w-xl relative transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
            <div id="modalHeader">
                <h3 id="modalTitle" class="text-2xl font-black text-gray-900"></h3>
            </div>
            <button id="closeModal" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-8 max-h-[75vh] overflow-y-auto" id="modalBody">
            {{-- Content will be injected here --}}
        </div>
    </div>
</div>

{{-- FullCalendar Assets --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const events = @json($events);
        let currentGroupData = null;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: events,
            themeSystem: 'bootstrap5',
            nowIndicator: true,
            slotMinTime: '06:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: true,
            slotEventOverlap: false,
            expandRows: true,
            stickyHeaderDates: true,
            handleWindowResize: true,
            scrollTime: moment().format('HH:00:00'),
            slotLabelInterval: '01:00:00',
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            eventContent: function(arg) {
                const props = arg.event.extendedProps;
                let content = document.createElement('div');
                content.classList.add('flex', 'flex-col', 'gap-0.5', 'overflow-hidden');

                if (props.type === 'slot') {
                    content.innerHTML = `
                        <div class="flex items-center gap-1">
                            <i class="bi bi-clock-history text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">${arg.timeText}</span>
                        </div>
                        <div class="font-black text-[11px] truncate">${arg.event.title.replace(' (Slot)', '')}</div>
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[9px] font-bold opacity-70">${props.booked}/${props.capacity} booked</span>
                            <span class="text-[8px] font-black uppercase opacity-60">${props.doctor.split(' ').pop()}</span>
                        </div>
                    `;
                } else if (props.type === 'appointment_group') {
                    content.innerHTML = `
                        <div class="flex items-center gap-1">
                            <i class="bi bi-people-fill text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">${arg.timeText}</span>
                        </div>
                        <div class="font-black text-[11px] truncate">${props.service}</div>
                        <div class="flex items-center gap-1">
                            <span class="px-1.5 py-0.5 rounded-md bg-black/10 text-[9px] font-black">${props.patient_count} patients</span>
                        </div>
                    `;
                }
                return { domNodes: [content] };
            },
            eventClick: function(info) {
                const props = info.event.extendedProps;
                currentGroupData = props;
                showModalContent(info.event);
            },
            eventDidMount: function(info) {
                const props = info.event.extendedProps;
                let tooltipContent = '';

                if (props.type === 'slot') {
                    tooltipContent = `Slot: ${info.event.title.replace(' (Slot)', '')}\nCapacity: ${props.booked}/${props.capacity}`;
                } else if (props.type === 'appointment_group') {
                    tooltipContent = `${props.patient_count} patients for ${props.service} at ${props.time}.\nClick to see names.`;
                }
                info.el.setAttribute('title', tooltipContent);
            }
        });

        calendar.render();

        function showModalContent(event) {
            const props = event.extendedProps;
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const eventModal = document.getElementById('eventModal');
            const modalContent = document.getElementById('modalContent');

            let bodyContent = '';

            if (props.type === 'slot') {
                modalTitle.innerHTML = `<span class="uppercase tracking-widest text-[10px] block mb-1 text-blue-500 font-black">SLOT DETAILS</span>` + event.title.replace(' (Slot)', '');
                const bookedPercentage = props.capacity > 0 ? (props.booked / props.capacity) * 100 : 0;
                const barColor = bookedPercentage >= 100 ? 'bg-red-500' : (bookedPercentage >= 75 ? 'bg-orange-500' : 'bg-brand-500');

                bodyContent = `
                    <div class="space-y-8">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="p-5 rounded-[2rem] bg-gray-50/50 border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Assigned Service</p>
                                <p class="text-base font-black text-gray-900">${event.title.replace(' (Slot)', '')}</p>
                            </div>
                            <div class="p-5 rounded-[2rem] bg-gray-50/50 border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Medical Provider</p>
                                <p class="text-base font-black text-gray-900">${props.doctor}</p>
                            </div>
                        </div>
                        <div class="p-8 rounded-[2.5rem] bg-gray-900 text-white shadow-xl">
                            <div class="flex justify-between items-end mb-6">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Patient Capacity Status</p>
                                    <p class="text-3xl font-black">${props.booked} <span class="text-gray-500 text-lg font-bold">/ ${props.capacity} booked</span></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-brand-400">${Math.round(bookedPercentage)}%</span>
                                </div>
                            </div>
                            <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full ${barColor} shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: ${bookedPercentage}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (props.type === 'appointment_group') {
                modalTitle.innerHTML = `<span class="uppercase tracking-widest text-[10px] block mb-1 text-brand-500 font-black">PATIENT GROUP</span>${props.service}`;
                
                const statusPills = {
                    'pending': 'bg-yellow-50 text-yellow-700 border-yellow-100',
                    'approved': 'bg-indigo-50 text-indigo-700 border-indigo-100',
                    'completed': 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    'no_show': 'bg-red-50 text-red-700 border-red-100',
                    'rescheduled': 'bg-blue-50 text-blue-700 border-blue-100',
                };

                let appointmentsHtml = props.appointments.map((appt, index) => {
                    let statusLabel = appt.status.replace('_', ' ');
                    if (appt.status === 'approved') {
                        statusLabel = appt.has_vitals ? 'Ready' : 'Confirmed';
                    }
                    return `
                        <li onclick="window.viewAppointmentDetail(${index})" class="group flex items-center justify-between p-4 rounded-2xl bg-gray-50/50 border border-gray-100 hover:bg-white hover:border-brand-200 hover:shadow-md transition-all cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-brand-500 transition-colors">
                                    ${appt.patient_photo 
                                        ? `<img src="${appt.patient_photo}" class="w-full h-full rounded-xl object-cover">`
                                        : `<i class="bi bi-person-fill text-2xl"></i>`}
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 text-sm group-hover:text-brand-600 transition-colors">${appt.patient_name}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Click to view details</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full border ${statusPills[appt.status] || 'bg-gray-100 text-gray-800'}">
                                    ${statusLabel}
                                </span>
                                <i class="bi bi-chevron-right text-gray-300 group-hover:text-brand-400 transition-colors"></i>
                            </div>
                        </li>
                    `;
                }).join('');

                bodyContent = `
                    <div class="space-y-6">
                        <div class="p-5 rounded-3xl bg-gray-50 border border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-brand-500 shadow-sm">
                                    <i class="bi bi-clock-fill"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Scheduled Time</p>
                                    <p class="text-sm font-black text-gray-900">${props.time}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Booked</p>
                                <p class="text-sm font-black text-gray-900">${props.patient_count} Patients</p>
                            </div>
                        </div>
                        <ul class="space-y-3">${appointmentsHtml}</ul>
                    </div>
                `;
            }
            
            modalBody.innerHTML = bodyContent;
            eventModal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 50);
        }

        window.viewAppointmentDetail = function(index) {
            const appt = currentGroupData.appointments[index];
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const modalContent = document.getElementById('modalContent');

            // Animation out
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modalTitle.innerHTML = `
                    <div class="flex items-center gap-3">
                        <button onclick="window.backToGroupList()" class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-all">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <div>
                            <span class="uppercase tracking-widest text-[10px] block mb-0.5 text-brand-500 font-black">APPOINTMENT DETAILS</span>
                            <span class="text-gray-900">${appt.patient_name}</span>
                        </div>
                    </div>
                `;

                const statusPills = {
                    'pending': 'bg-yellow-50 text-yellow-700 border-yellow-100',
                    'approved': 'bg-indigo-50 text-indigo-700 border-indigo-100',
                    'completed': 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    'no_show': 'bg-red-50 text-red-700 border-red-100',
                    'rescheduled': 'bg-blue-50 text-blue-700 border-blue-100',
                };

                let vitalsHtml = '<p class="text-sm font-bold text-gray-400 italic">No vital signs recorded yet.</p>';
                if (appt.vitals) {
                    const v = appt.vitals;
                    vitalsHtml = `
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            ${v.blood_pressure ? `
                                <div class="p-3 rounded-2xl bg-white border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">BP</p>
                                    <p class="text-sm font-black text-gray-900">${v.blood_pressure}</p>
                                </div>` : ''}
                            ${v.temperature ? `
                                <div class="p-3 rounded-2xl bg-white border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Temp</p>
                                    <p class="text-sm font-black text-gray-900">${v.temperature}°C</p>
                                </div>` : ''}
                            ${v.heart_rate ? `
                                <div class="p-3 rounded-2xl bg-white border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">HR</p>
                                    <p class="text-sm font-black text-gray-900">${v.heart_rate} bpm</p>
                                </div>` : ''}
                            ${v.weight ? `
                                <div class="p-3 rounded-2xl bg-white border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Weight</p>
                                    <p class="text-sm font-black text-gray-900">${v.weight} kg</p>
                                </div>` : ''}
                        </div>
                    `;
                }

                modalBody.innerHTML = `
                    <div class="space-y-8">
                        {{-- Patient Header --}}
                        <div class="flex items-center gap-6 p-6 rounded-[2rem] bg-gray-50 border border-gray-100">
                            <div class="w-20 h-20 rounded-[1.5rem] bg-white border-2 border-white shadow-md overflow-hidden">
                                ${appt.patient_photo 
                                    ? `<img src="${appt.patient_photo}" class="w-full h-full object-cover">`
                                    : `<div class="w-full h-full flex items-center justify-center text-3xl text-gray-200"><i class="bi bi-person-fill"></i></div>`}
                            </div>
                            <div>
                                <h4 class="text-xl font-black text-gray-900">${appt.patient_name}</h4>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full border ${statusPills[appt.status] || 'bg-gray-100 text-gray-800'}">
                                        ${appt.status.replace('_', ' ')}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Service Details --}}
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Service Type</p>
                                <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100 font-bold text-gray-900 text-sm">
                                    ${appt.service}
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Medical Provider</p>
                                <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100 font-bold text-gray-900 text-sm">
                                    ${appt.provider}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Scheduled Date & Time</p>
                            <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100 flex items-center gap-3">
                                <i class="bi bi-calendar-event text-brand-500"></i>
                                <span class="font-bold text-gray-900 text-sm">${appt.scheduled_at}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Reason for Visit</p>
                            <div class="p-5 rounded-2xl bg-gray-50/50 border border-gray-100 text-sm text-gray-600 leading-relaxed italic">
                                "${appt.reason}"
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center px-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Vital Signs</p>
                                ${appt.has_vitals ? '<span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Captured</span>' : ''}
                            </div>
                            <div class="p-6 rounded-[2rem] bg-gray-50/50 border border-gray-100">
                                ${vitalsHtml}
                            </div>
                        </div>
                    </div>
                `;

                // Animation in
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 200);
        };

        window.backToGroupList = function() {
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                showModalContent({
                    title: `(${currentGroupData.patient_count}) ${currentGroupData.service}`,
                    extendedProps: currentGroupData
                });
            }, 200);
        };

        // Close modal functionality
        function closeModal() {
            const eventModal = document.getElementById('eventModal');
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                eventModal.classList.add('hidden');
            }, 300);
        }

        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('eventModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    });
</script>

<style>
    /* FullCalendar Custom Styling */
    .fc {
        --fc-border-color: #f8fafc;
        --fc-daygrid-dot-event-hover-bg: #f9fafb;
    }
    .fc-header-toolbar {
        margin-bottom: 2.5rem !important;
        padding: 0 1rem;
    }
    .fc-toolbar-title {
        font-size: 1.75rem !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        letter-spacing: -0.02em;
    }
    .fc-button {
        background-color: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        color: #64748b !important;
        font-weight: 700 !important;
        text-transform: capitalize !important;
        padding: 0.6rem 1.2rem !important;
        border-radius: 1rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
    }
    .fc-button:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
        transform: translateY(-1px);
    }
    .fc-button-active {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border-color: #0f172a !important;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important;
    }
    .fc-day-today {
        background-color: #f1f5f9/30 !important;
    }
    .fc-event {
        border-radius: 10px !important;
        padding: 5px 10px !important;
        font-size: 0.75rem !important;
        font-weight: 800 !important;
        border: none !important;
        margin-bottom: 4px !important;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 45px;
    }
    .fc-daygrid-event {
        white-space: normal !important;
        height: auto !important;
    }
    .fc-event-main {
        width: 100%;
        overflow: hidden;
    }
    .fc-event:hover {
        transform: scale(1.02) translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        z-index: 10;
    }
    .fc-daygrid-event-dot {
        border-width: 4px !important;
    }
    .fc-event-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-family: 'Inter', sans-serif;
    }
    .fc-daygrid-day-top {
        padding: 15px !important;
    }
    .fc-daygrid-day-number {
        font-size: 0.9rem !important;
        font-weight: 900 !important;
        color: #cbd5e1 !important;
        text-decoration: none !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        color: #3b82f6 !important;
        background: #eff6ff;
        padding: 4px 8px;
        border-radius: 8px;
    }
    .fc-col-header-cell {
        background: #f8fafc;
        padding: 15px 0 !important;
        border-bottom: 2px solid #f1f5f9 !important;
    }
    .fc-col-header-cell-cushion {
        font-size: 0.75rem !important;
        font-weight: 900 !important;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8 !important;
        text-decoration: none !important;
    }
    .fc-scrollgrid {
        border-radius: 32px !important;
        overflow: hidden !important;
        border: 1px solid #f1f5f9 !important;
        background: white;
    }

    /* Time Grid (Week/Day) Specific Styling */
    .fc-timegrid-slot {
        height: 5rem !important;
        border-bottom: 1px solid #f8fafc !important;
    }
    .fc-timegrid-slot-label-cushion {
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        color: #94a3b8 !important;
        text-transform: lowercase;
    }
    .fc-timegrid-axis-cushion {
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        color: #94a3b8 !important;
    }
    .fc-now-indicator-line {
        border-color: #ef4444 !important;
        border-width: 2px !important;
    }
    .fc-now-indicator-arrow {
        border-color: #ef4444 !important;
        border-top-color: transparent !important;
        border-bottom-color: transparent !important;
    }
    .fc-timegrid-event {
        border-radius: 12px !important;
        padding: 8px !important;
    }
    .fc-timegrid-event .fc-event-main {
        padding: 0 !important;
    }
    .fc-timegrid-event .fc-event-title {
        font-size: 0.85rem !important;
        font-weight: 900 !important;
        margin-bottom: 2px;
    }
    .fc-timegrid-event .fc-event-time {
        font-size: 0.7rem !important;
        font-weight: 700 !important;
        opacity: 0.8;
    }

    /* Event Colors with gradients */
    .fc-event[style*="background-color: rgb(59, 130, 246)"] { 
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    }
    .fc-event[style*="background-color: rgb(251, 191, 36)"] { 
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
        color: #78350f !important;
    }
    .fc-event[style*="background-color: rgb(16, 185, 129)"] { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    .fc-event[style*="background-color: rgb(248, 113, 113)"] { 
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%) !important;
    }
</style>
@endsection
