@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="bi bi-bar-chart-line-fill text-brand-600"></i> Reports & Analytics
            </h1>
            <p class="mt-1 text-sm text-gray-600">Monitor health trends, service performance, and patient demographics.</p>
        </div>
        <div class="flex flex-col items-end gap-2 print:hidden">
            <form id="filterForm" action="{{ route('healthworker.reports.index') }}" method="GET" class="flex items-center gap-2 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
                <input type="date" name="start_date" id="startDate" value="{{ $startDate }}" class="border-0 focus:ring-0 text-sm text-gray-700 rounded-md bg-transparent">
                <span class="text-gray-400">-</span>
                <input type="date" name="end_date" id="endDate" value="{{ $endDate }}" class="border-0 focus:ring-0 text-sm text-gray-700 rounded-md bg-transparent">
                <button type="submit" class="px-3 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-md transition flex items-center gap-2" title="Apply Date Filter">
                    <i class="bi bi-filter"></i> Apply
                </button>
            </form>

            <div class="flex items-center gap-2">
                <div class="flex bg-white rounded-lg border border-gray-200 shadow-sm p-1">
                    <button type="button" id="btn-7days" onclick="setFilter('7days')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-brand-600 rounded transition">Last 7 Days</button>
                    <div class="w-px bg-gray-200 my-1"></div>
                    <button type="button" id="btn-thisMonth" onclick="setFilter('thisMonth')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-brand-600 rounded transition">This Month</button>
                    <div class="w-px bg-gray-200 my-1"></div>
                    <button type="button" id="btn-lastMonth" onclick="setFilter('lastMonth')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-brand-600 rounded transition">Last Month</button>
                </div>
                <button onclick="window.print()" class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 text-sm rounded-lg transition shadow-sm">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>

    {{-- 1. Appointment Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl z-10">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="z-10">
                <p class="text-sm text-gray-500 font-medium">Total Appointments</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $appointmentStats['total'] }}</h3>
                    <span class="text-xs font-medium {{ $growthStats['total'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        <i class="bi bi-arrow-{{ $growthStats['total'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($growthStats['total']) }}%
                    </span>
                </div>
            </div>
        </div>
        {{-- Completed --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl z-10">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="z-10">
                <p class="text-sm text-gray-500 font-medium">Completed</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $appointmentStats['completed'] }}</h3>
                    <span class="text-xs font-medium {{ $growthStats['completed'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        <i class="bi bi-arrow-{{ $growthStats['completed'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($growthStats['completed']) }}%
                    </span>
                </div>
            </div>
        </div>
        {{-- Pending --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl z-10">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="z-10">
                <p class="text-sm text-gray-500 font-medium">Pending</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $appointmentStats['pending'] }}</h3>
                    <span class="text-xs font-medium {{ $growthStats['pending'] <= 0 ? 'text-green-600' : 'text-yellow-600' }}">
                        <i class="bi bi-arrow-{{ $growthStats['pending'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($growthStats['pending']) }}%
                    </span>
                </div>
            </div>
        </div>
        {{-- Cancelled --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xl z-10">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="z-10">
                <p class="text-sm text-gray-500 font-medium">Cancelled</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $appointmentStats['cancelled'] }}</h3>
                    <span class="text-xs font-medium {{ $growthStats['cancelled'] <= 0 ? 'text-green-600' : 'text-red-600' }}">
                        <i class="bi bi-arrow-{{ $growthStats['cancelled'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($growthStats['cancelled']) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Appointment Trends --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-graph-up text-brand-600"></i> Appointment Trends
            </h3>
            <canvas id="trendsChart" height="250"></canvas>
        </div>

        {{-- Service Usage --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-pie-chart text-brand-600"></i> Service Usage
            </h3>
            <canvas id="serviceChart" height="250"></canvas>
        </div>
    </div>

    {{-- 3. Demographics Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Age Distribution --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-people text-brand-600"></i> Patient Age Distribution
            </h3>
            <canvas id="ageChart" height="250"></canvas>
        </div>

        {{-- Gender Distribution --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-gender-ambiguous text-brand-600"></i> Patient Gender Distribution
            </h3>
            <div class="flex items-center justify-center h-full">
                 <canvas id="genderChart" class="max-h-[250px]"></canvas>
            </div>
        </div>
    </div>

    {{-- 4. Health Analytics Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Common Illnesses --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-virus text-brand-600"></i> Common Illnesses
            </h3>
            <canvas id="diseaseChart" height="250"></canvas>
        </div>

        {{-- Prenatal Cases by Area --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-geo-alt text-brand-600"></i> Prenatal Cases by Area (Purok)
            </h3>
            <div class="flex items-center justify-center h-full">
                <canvas id="prenatalChart" class="max-h-[250px]"></canvas>
            </div>
        </div>
    </div>

    {{-- 5. Detailed Health Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Immunization Coverage --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-shield-check text-brand-600"></i> Immunization Coverage (Top Vaccines)
            </h3>
            <canvas id="vaccineChart" height="250"></canvas>
        </div>

        {{-- Blood Pressure Stats --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-heart-pulse text-brand-600"></i> Blood Pressure Monitoring
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center justify-center">
                    <canvas id="bpChart" class="max-h-[200px]"></canvas>
                </div>
                <div class="flex flex-col justify-center gap-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-gray-600">Normal: <span class="font-bold text-gray-900">{{ $bpStats['Normal'] }}</span></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                        <span class="text-gray-600">Elevated: <span class="font-bold text-gray-900">{{ $bpStats['Elevated'] }}</span></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-gray-600">Stage 1 HTN: <span class="font-bold text-gray-900">{{ $bpStats['High Stage 1'] }}</span></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-gray-600">Stage 2 HTN: <span class="font-bold text-gray-900">{{ $bpStats['High Stage 2'] }}</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- Hidden JSON Data for Charts --}}
<script id="report-data" type="application/json">
    {{ Js::from([
        'trendStats' => $trendStats,
        'serviceStats' => $serviceStats,
        'ageGroups' => $ageGroups,
        'genderStats' => $genderStats,
        'diseaseStats' => $diseaseStats,
        'prenatalStats' => $prenatalStats,
        'vaccineStats' => $vaccineStats,
        'bpStats' => $bpStats,
    ]) }}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function setFilter(type) {
        const today = new Date();
        // Use local time, not UTC (toISOString uses UTC)
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        let start, end;
        end = formatDate(today);

        if (type === '7days') {
            const date = new Date();
            date.setDate(date.getDate() - 6);
            start = formatDate(date);
        } else if (type === 'thisMonth') {
            const date = new Date();
            date.setDate(1); // 1st of current month
            start = formatDate(date);
        } else if (type === 'lastMonth') {
            const firstDayPrevMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDayPrevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            start = formatDate(firstDayPrevMonth);
            end = formatDate(lastDayPrevMonth);
        }

        document.getElementById('startDate').value = start;
        document.getElementById('endDate').value = end;
        document.getElementById('filterForm').submit();
    }

    // Highlight active filter button
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const start = urlParams.get('start_date');
        const end = urlParams.get('end_date');

        if (start && end) {
            // Helper to check match
            const checkMatch = (type) => {
                const today = new Date();
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };
                
                let s, e;
                e = formatDate(today);

                if (type === '7days') {
                    const d = new Date();
                    d.setDate(d.getDate() - 6);
                    s = formatDate(d);
                } else if (type === 'thisMonth') {
                    const d = new Date();
                    d.setDate(1);
                    s = formatDate(d);
                } else if (type === 'lastMonth') {
                    const d = new Date();
                    const first = new Date(d.getFullYear(), d.getMonth() - 1, 1);
                    const last = new Date(d.getFullYear(), d.getMonth(), 0);
                    s = formatDate(first);
                    e = formatDate(last);
                }
                return start === s && end === e;
            };

            if (checkMatch('7days')) document.getElementById('btn-7days').classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
            if (checkMatch('thisMonth')) document.getElementById('btn-thisMonth').classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
            if (checkMatch('lastMonth')) document.getElementById('btn-lastMonth').classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
        } else {
            // Default to This Month if no params (assuming controller defaults)
            const thisMonthBtn = document.getElementById('btn-thisMonth');
            if(thisMonthBtn) thisMonthBtn.classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200');
        }
        
        // Parse Data safely from JSON script tag
        const reportData = JSON.parse(document.getElementById('report-data').textContent);
        const { 
            trendStats, 
            serviceStats, 
            ageGroups, 
            genderStats, 
            diseaseStats, 
            prenatalStats, 
            vaccineStats, 
            bpStats 
        } = reportData;

        // 1. Appointment Trends Chart (Line)
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trendStats.map(item => item.label),
                datasets: [{
                    label: 'Appointments',
                    data: trendStats.map(item => item.count),
                    borderColor: '#0ea5e9', // brand-500
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // 2. Service Usage Chart (Bar)
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: serviceStats.map(s => s.service),
                datasets: [{
                    label: 'Appointments',
                    data: serviceStats.map(s => s.count),
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // 3. Age Distribution Chart (Doughnut)
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(ageGroups),
                datasets: [{
                    data: Object.values(ageGroups),
                    backgroundColor: ['#60a5fa', '#34d399', '#fcd34d', '#a78bfa'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // 4. Gender Distribution Chart (Pie)
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(genderStats),
                datasets: [{
                    data: Object.values(genderStats),
                    backgroundColor: ['#3b82f6', '#ec4899', '#9ca3af'], // Blue, Pink, Gray
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // 5. Common Illnesses Chart (Horizontal Bar)
        const diseaseCtx = document.getElementById('diseaseChart').getContext('2d');
        new Chart(diseaseCtx, {
            type: 'bar',
            data: {
                labels: diseaseStats.map(s => s.diagnosis),
                datasets: [{
                    label: 'Cases',
                    data: diseaseStats.map(s => s.count),
                    backgroundColor: '#f59e0b', // amber-500
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bar
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // 6. Prenatal Stats Chart (Bar)
        const prenatalCtx = document.getElementById('prenatalChart').getContext('2d');
        new Chart(prenatalCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(prenatalStats),
                datasets: [{
                    label: 'Pregnant Patients',
                    data: Object.values(prenatalStats),
                    backgroundColor: '#ec4899', // pink-500
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // 7. Vaccine Coverage Chart (Bar)
        const vaccineCtx = document.getElementById('vaccineChart').getContext('2d');
        new Chart(vaccineCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(vaccineStats),
                datasets: [{
                    label: 'Doses Administered',
                    data: Object.values(vaccineStats),
                    backgroundColor: '#10b981', // emerald-500
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // 8. Blood Pressure Chart (Doughnut)
        const bpCtx = document.getElementById('bpChart').getContext('2d');
        new Chart(bpCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(bpStats),
                datasets: [{
                    data: Object.values(bpStats),
                    backgroundColor: ['#22c55e', '#eab308', '#f97316', '#ef4444'], // Green, Yellow, Orange, Red
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endsection
