@extends('layouts.app')

@section('title', 'Manage Slots')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Slots</h1>
            <p class="text-sm text-gray-600 mt-2">Compact appointment availability list.</p>
        </div>
        <a href="{{ route('midwife.slots.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-brand-600 text-brand-600 rounded text-sm font-semibold hover:bg-brand-50 transition-colors">
            <i class="bi bi-plus-lg"></i>Add Slot
        </a>
    </div>

    <form method="GET" action="{{ route('midwife.slots.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-4">
        <div class="space-y-2">
            <label class="text-xs font-semibold text-gray-700">Service</label>
            <select name="service" class="w-full px-3 py-2.5 border border-gray-200 rounded text-sm text-gray-700 bg-white">
                <option value="">All Services</option>
                @foreach($services as $service)
                    <option value="{{ $service->name }}" {{ request('service') === $service->name ? 'selected' : '' }}>{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-2">
            <label class="text-xs font-semibold text-gray-700">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded text-sm text-gray-700 bg-white">
        </div>
        <div class="space-y-2">
            <label class="text-xs font-semibold text-gray-700">Status</label>
            <select name="status" class="w-full px-3 py-2.5 border border-gray-200 rounded text-sm text-gray-700 bg-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="flex-1 px-4 py-2.5 bg-brand-600 text-white rounded text-sm font-semibold uppercase tracking-[0.1em] hover:bg-brand-700 transition-colors">Filter</button>
            <a href="{{ route('midwife.slots.index') }}" class="px-4 py-2.5 border border-gray-200 rounded text-sm text-gray-700 hover:bg-gray-50 transition-colors font-semibold">Reset</a>
        </div>
    </form>

    <div class="bg-white border border-gray-200 rounded-md overflow-visible">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto overflow-y-visible relative">
            <table class="w-full text-left text-xs border-collapse overflow-visible">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 font-bold text-gray-600 uppercase">Service</th>
                        <th class="px-3 py-2 font-bold text-gray-600 uppercase">Provider</th>
                        <th class="px-3 py-2 font-bold text-gray-600 uppercase">Date & Time</th>
                        <th class="px-3 py-2 font-bold text-gray-600 uppercase">Booked</th>
                        <th class="px-3 py-2 font-bold text-gray-600 uppercase">Capacity</th>
                        <th class="px-3 py-2 font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-3 py-2 text-right font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($slots as $slot)
                        @php
                            $bookedAppointments = $slot->appointments()->whereNotIn('status', ['cancelled', 'rejected'])->with('user')->get();
                        @endphp
                        <tr class="group hover:bg-gray-50 transition-colors {{ $slot->isExpired() ? 'opacity-50' : '' }}">
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-bandaid text-brand-500"></i>
                                    <span class="font-semibold text-gray-900 text-sm">{{ $slot->service }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-[11px]">
                                @if($slot->doctor)
                                    <span class="font-bold text-gray-900">{{ $slot->doctor->isDoctor() ? 'Dr. ' : '' }}{{ $slot->doctor->full_name }}</span>
                                @else
                                    <span class="text-gray-500">Medical Team</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-[11px]">
                                <div class="font-semibold text-gray-900">{{ $slot->date->format('M d, Y') }}</div>
                                <div class="text-gray-500">{{ $slot->displayTime() }}</div>
                            </td>
                            <td class="px-3 py-2 text-[11px] font-semibold text-gray-700 relative booked-tooltip-cell" data-patients='@json($bookedAppointments->map(function($appointment){ return [
                                        'name' => $appointment->user->full_name ?? 'Unknown',
                                        'code' => $appointment->user->patient_code ?? 'No code',
                                    ]; }))'>
                                <span>{{ $bookedAppointments->count() }} booked</span>
                            </td>
                            <td class="px-3 py-2 text-[11px]">
                                @php
                                    $booked = $slot->bookedCount();
                                    $cap = max($slot->capacity, 0);
                                    $pct = $cap > 0 ? round(($booked / $cap) * 100) : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900">{{ $booked }}/{{ $cap }}</span>
                                    <div class="w-14 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-brand-500" style="width:{{ $pct }}%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $statusColors = [
                                        'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'red' => 'bg-red-50 text-red-700 border-red-200',
                                        'gray' => 'bg-gray-50 text-gray-600 border-gray-200',
                                    ];
                                    $colorClass = $statusColors[$slot->status_color] ?? $statusColors['gray'];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold border {{ $colorClass }}">
                                    <span class="w-1 h-1 rounded-full bg-current"></span>{{ $slot->status_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-70 hover:opacity-100 transition-opacity">
                                    <a href="{{ route('midwife.slots.edit', $slot->id) }}" class="p-1.5 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-600 hover:text-white transition-colors border border-yellow-200" title="Edit">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>
                                    <form action="{{ route('midwife.slots.destroy', $slot->id) }}" method="POST" onsubmit="return confirm('Delete this slot?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-600 hover:text-white transition-colors border border-red-200" title="Delete">
                                            <i class="bi bi-trash3 text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No slots found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($slots as $slot)
                <div class="p-3 space-y-2 {{ $slot->isExpired() ? 'opacity-50' : '' }}">
                    <div class="flex justify-between items-start gap-2">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <i class="bi bi-bandaid text-brand-600 text-lg shrink-0"></i>
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-900">{{ $slot->service }}</h4>
                                <p class="text-xs text-gray-500">{{ $slot->doctor?->full_name ?? 'Medical Team' }}</p>
                            </div>
                        </div>
                        @php
                            $statusColors = [
                                'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'red' => 'bg-red-50 text-red-700 border-red-200',
                                'gray' => 'bg-gray-50 text-gray-600 border-gray-200',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-bold border whitespace-nowrap {{ $statusColors[$slot->status_color] ?? $statusColors['gray'] }}">
                            {{ $slot->status_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px]">
                        <div class="bg-gray-50 p-2 rounded border border-gray-200">
                            <p class="text-gray-600 font-semibold mb-0.5">Date</p>
                            <p class="font-bold text-gray-900">{{ $slot->date->format('M d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border border-gray-200">
                            <p class="text-gray-600 font-semibold mb-0.5">Time</p>
                            <p class="font-bold text-gray-900">{{ $slot->displayTime() }}</p>
                        </div>
                    </div>

                    @php
                        $booked = $slot->bookedCount();
                        $cap = max($slot->capacity, 0);
                        $pct = $cap > 0 ? round(($booked / $cap) * 100) : 0;
                    @endphp
                    <div class="bg-gray-50 p-2 rounded border border-gray-200 text-xs">
                        <div class="flex justify-between mb-1">
                            <span class="font-bold text-gray-600">Capacity</span>
                            <span class="font-bold text-gray-900">{{ $booked }}/{{ $cap }}</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-500" style="width:{{ $pct }}%;"></div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('midwife.slots.edit', $slot->id) }}" class="flex-1 px-2 py-1.5 bg-yellow-50 text-yellow-700 rounded text-[10px] font-bold text-center border border-yellow-200 hover:bg-yellow-600 hover:text-white hover:border-yellow-600 transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('midwife.slots.destroy', $slot->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this slot?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-2 py-1.5 bg-red-50 text-red-700 rounded text-[10px] font-bold border border-red-200 hover:bg-red-600 hover:text-white hover:border-red-600 transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 text-sm">No slots found</div>
            @endforelse
        </div>
        
        @if($slots->hasPages())
            <div class="px-4 py-4 border-t border-gray-200 bg-gray-50 text-center">
                <button id="loadMoreBtn" class="px-6 py-2.5 bg-brand-600 text-white rounded-lg text-sm font-semibold hover:bg-brand-700 transition-colors" data-page="{{ $slots->currentPage() }}" data-has-more="{{ $slots->hasMorePages() ? 'true' : 'false' }}">
                    <i class="bi bi-download mr-2"></i>Load More Slots
                </button>
                <div id="loadingIndicator" class="hidden mt-2 text-brand-600 text-sm font-semibold">
                    <i class="bi bi-hourglass-split animate-spin mr-2"></i>Loading...
                </div>
            </div>
        @endif
    </div>
</div>

<div id="bookedPatientsTooltip" class="hidden fixed z-50 pointer-events-none">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-[0_20px_50px_rgba(15,23,42,0.18)] text-xs text-gray-700 overflow-hidden w-64 max-w-[16rem]">
        <div class="px-3 py-2 border-b border-gray-100 font-semibold text-gray-900 bg-white">Booked patients</div>
        <div id="bookedPatientsTooltipList" class="max-h-52 overflow-y-auto bg-white"></div>
    </div>
</div>

<script>
    // Load More functionality
    document.getElementById('loadMoreBtn')?.addEventListener('click', function() {
        const btn = this;
        const hasMore = btn.dataset.hasMore === 'true';
        const currentPage = parseInt(btn.dataset.page);
        const nextPage = currentPage + 1;
        const loading = document.getElementById('loadingIndicator');
        
        if (!hasMore) {
            btn.style.display = 'none';
            return;
        }

        btn.style.display = 'none';
        loading.classList.remove('hidden');

        // Build query string with current filters
        const params = new URLSearchParams(window.location.search);
        params.set('page', nextPage);
        params.set('load_more', '1');

        fetch(`{{ route('midwife.slots.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract new table rows
            const newRows = doc.querySelectorAll('table tbody tr');
            const tbody = document.querySelector('table tbody');
            
            if (tbody && newRows.length > 0) {
                newRows.forEach(row => {
                    const clonedRow = row.cloneNode(true);
                    tbody.appendChild(clonedRow);
                    
                    // Re-initialize booked tooltip for new rows
                    const bookedCell = clonedRow.querySelector('.booked-tooltip-cell');
                    if (bookedCell) {
                        initializeBookedTooltip(bookedCell);
                    }
                });
            }

            // Update button state
            const newDoc = doc.body;
            const nextBtn = newDoc.querySelector('#loadMoreBtn');
            if (nextBtn) {
                btn.dataset.page = nextBtn.dataset.page;
                btn.dataset.hasMore = nextBtn.dataset.hasMore;
                
                if (nextBtn.dataset.hasMore === 'false') {
                    btn.style.display = 'none';
                } else {
                    btn.style.display = 'inline-flex';
                }
            }

            loading.classList.add('hidden');
            btn.style.display = btn.dataset.hasMore === 'true' ? 'inline-flex' : 'none';
        })
        .catch(error => {
            console.error('Error loading more slots:', error);
            loading.classList.add('hidden');
            btn.style.display = 'inline-flex';
            alert('Error loading more slots. Please try again.');
        });
    });

    // Booked Tooltip Initialization
    function initializeBookedTooltip(cell) {
        const tooltip = document.getElementById('bookedPatientsTooltip');
        const list = document.getElementById('bookedPatientsTooltipList');

        const escapeHtml = (text) => {
            if (!text) return '';
            return text.replace(/[&<>"']/g, function(match) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[match];
            });
        };

        const patients = cell.dataset.patients ? JSON.parse(cell.dataset.patients) : [];
        if (!patients.length) return;

        const showTooltip = () => {
            list.innerHTML = patients.map(patient => `
                <div class="px-3 py-2 hover:bg-gray-50 transition-colors flex items-start gap-2">
                    <i class="bi bi-person-fill text-gray-400 mt-0.5"></i>
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-gray-900">${escapeHtml(patient.name)}</p>
                        <p class="truncate text-[10px] text-gray-500">${escapeHtml(patient.code)}</p>
                    </div>
                </div>
            `).join('');

            tooltip.classList.remove('hidden');
            const rect = cell.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            let left = rect.left;
            let top = rect.bottom + 8;

            if (left + tooltipRect.width > window.innerWidth - 16) {
                left = window.innerWidth - tooltipRect.width - 16;
            }
            if (left < 16) {
                left = 16;
            }
            if (top + tooltipRect.height > window.innerHeight - 16) {
                top = rect.top - tooltipRect.height - 8;
            }

            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
        };

        cell.addEventListener('mouseenter', showTooltip);
        cell.addEventListener('mouseleave', () => tooltip.classList.add('hidden'));
    }

    // Initialize tooltips for existing rows
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.booked-tooltip-cell').forEach(cell => {
            initializeBookedTooltip(cell);
        });
    });
</script>
@endsection
