@props(['slotModel' => null, 'action', 'method' => 'POST', 'buttonText', 'doctors' => [], 'services' => [], 'availabilities' => []])

<form action="{{ $action }}" method="POST" class="p-6 sm:p-8 lg:p-10 space-y-10" id="slot-form" x-data="{ 
    selectedDoctor: '{{ old('doctor_id', $slotModel->doctor_id ?? '') }}',
    selectedDate: '{{ old('date', isset($slotModel) ? $slotModel->date->format('Y-m-d') : '') }}',
    availabilities: {{ Js::from($availabilities) }},
    calendarMonth: new Date().getMonth(),
    calendarYear: new Date().getFullYear(),
    calendarDays: [],
    init() {
        this.buildCalendar();
        this.$watch('calendarMonth', () => this.buildCalendar());
        this.$watch('calendarYear', () => this.buildCalendar());
        this.$watch('selectedDoctor', () => this.buildCalendar());
    },
    monthLabel() {
        return new Date(this.calendarYear, this.calendarMonth, 1).toLocaleString('en-US', { month: 'long' });
    },
    prevMonth() {
        if (this.calendarMonth === 0) {
            this.calendarMonth = 11;
            this.calendarYear--;
            return;
        }
        this.calendarMonth--;
    },
    nextMonth() {
        if (this.calendarMonth === 11) {
            this.calendarMonth = 0;
            this.calendarYear++;
            return;
        }
        this.calendarMonth++;
    },
    goToday() {
        const d = new Date();
        this.calendarMonth = d.getMonth();
        this.calendarYear = d.getFullYear();
    },
    buildCalendar() {
        const first = new Date(this.calendarYear, this.calendarMonth, 1);
        const firstDow = first.getDay();
        const daysInMonth = new Date(this.calendarYear, this.calendarMonth + 1, 0).getDate();
        const out = [];
        for (let i = 0; i < firstDow; i++) out.push({ day: '', dateStr: '' });
        for (let day = 1; day <= daysInMonth; day++) {
            const mm = String(this.calendarMonth + 1).padStart(2, '0');
            const dd = String(day).padStart(2, '0');
            const dateStr = `${this.calendarYear}-${mm}-${dd}`;
            out.push({ day, dateStr });
        }
        this.calendarDays = out;
    },
    isAvailableDate(dateStr) {
        if (!dateStr || !this.selectedDoctor) return false;
        const d = new Date(dateStr);
        const weekday = d.getDay();
        return (this.availabilities || []).some(a => {
            if (String(a.doctor_id) !== String(this.selectedDoctor)) return false;
            if (a.is_recurring) return Number(a.recurring_day) === weekday;
            if (!a.date) return false;
            return String(a.date).substring(0, 10) === dateStr;
        });
    },
    windowsForDate(dateStr) {
        if (!dateStr || !this.selectedDoctor) return [];
        const d = new Date(dateStr);
        const weekday = d.getDay();
        const list = (this.availabilities || []).filter(a => String(a.doctor_id) === String(this.selectedDoctor));
        const match = list.filter(a => {
            if (a.is_recurring) return Number(a.recurring_day) === weekday;
            if (!a.date) return false;
            return String(a.date).substring(0, 10) === dateStr;
        });
        return match.map((a, idx) => ({
            key: `${dateStr}-${idx}`,
            start: a.start_time,
            end: a.end_time
        }));
    },
    formatTime(t) {
        if (!t) return '';
        const time = String(t).length === 5 ? `${t}:00` : String(t);
        return new Date(`1970-01-01T${time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    },
    providerRole() {
        const sel = document.getElementById('doctor-select');
        const opt = sel && sel.selectedOptions ? sel.selectedOptions[0] : null;
        return opt ? (opt.getAttribute('data-role') || '') : '';
    },
    selectCalendarDate(dateStr) {
        if (!dateStr) return;
        this.selectedDate = dateStr;
    }
}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        {{-- Section 1: Service & Provider --}}
        <div class="col-span-1 md:col-span-2 space-y-8">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner border border-brand-100">
                    <i class="bi bi-briefcase text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Service Details</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Provider & Type Selection</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-4">
                    <label for="service-select" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Medical Service</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-bandaid text-xl"></i>
                        </div>
                        <select name="service" id="service-select" required
                            class="block w-full pl-16 pr-10 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 transition-all appearance-none">
                            <option value="">-- Select Service --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->name }}" 
                                    data-provider-type="{{ $service->provider_type }}"
                                    {{ old('service', $slotModel->service ?? '') === $service->name ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ $service->provider_type }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-6 pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-4" id="doctor-container">
                    <label for="doctor-select" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">
                        Assigned Provider <span id="doctor-required-label" class="text-red-500 hidden">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-person-badge text-xl"></i>
                        </div>
                        <select name="doctor_id" id="doctor-select" x-model="selectedDoctor"
                            class="block w-full pl-16 pr-10 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 transition-all appearance-none">
                            <option value="" data-role="none">-- No Doctor / Midwife Only --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" 
                                    data-role="{{ $doctor->role }}"
                                    {{ old('doctor_id', $slotModel->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->isDoctor() ? 'Dr.' : '' }} {{ $doctor->full_name }} {{ $doctor->isMidwife() ? '(Midwife)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-6 pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest ml-4" id="doctor-help-text">Select a doctor if required by the service</p>

                    {{-- Doctor Schedule Summary (Visible when doctor is selected) --}}
                    <div x-show="selectedDoctor && providerRole() === 'doctor'" class="mt-6 space-y-4 animate-in fade-in slide-in-from-top-2 duration-500">
                        <div class="px-6 py-5 rounded-[2rem] bg-white border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                    <i class="bi bi-calendar3 text-sm"></i>
                                </div>
                                <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Doctor's Clinical Schedule</h4>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest" x-text="`${monthLabel()} ${calendarYear}`"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="prevMonth()" class="w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-100 transition flex items-center justify-center">
                                            <i class="bi bi-chevron-left text-[10px]"></i>
                                        </button>
                                        <button type="button" @click="goToday()" class="px-3 h-8 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition text-[9px] font-black uppercase tracking-widest">
                                            Today
                                        </button>
                                        <button type="button" @click="nextMonth()" class="w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-100 transition flex items-center justify-center">
                                            <i class="bi bi-chevron-right text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="grid grid-cols-7 gap-1 mb-1">
                                        <template x-for="(d, i) in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']" :key="i">
                                            <div class="text-center text-[8px] font-black uppercase tracking-widest"
                                                 :class="i === 0 ? 'text-red-400' : 'text-gray-300'" x-text="d"></div>
                                        </template>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="(dayObj, idx) in calendarDays" :key="idx">
                                            <button type="button"
                                                class="aspect-square rounded-xl flex items-center justify-center relative transition border border-transparent"
                                                :disabled="!dayObj.dateStr"
                                                @click="selectCalendarDate(dayObj.dateStr)"
                                                :class="{
                                                    'opacity-0 pointer-events-none': !dayObj.dateStr,
                                                    'bg-brand-50 border-brand-100': dayObj.dateStr && isAvailableDate(dayObj.dateStr),
                                                    'bg-white hover:bg-gray-50 border-gray-50': dayObj.dateStr && !isAvailableDate(dayObj.dateStr),
                                                    'ring-4 ring-brand-500/20 border-brand-500': dayObj.dateStr && selectedDate && dayObj.dateStr === selectedDate
                                                }">
                                                <span class="text-[10px] font-black"
                                                      :class="{
                                                          'text-brand-700': dayObj.dateStr && isAvailableDate(dayObj.dateStr),
                                                          'text-red-500': dayObj.dateStr && new Date(dayObj.dateStr).getDay() === 0 && !isAvailableDate(dayObj.dateStr),
                                                          'text-gray-700': dayObj.dateStr && new Date(dayObj.dateStr).getDay() !== 0 && !isAvailableDate(dayObj.dateStr)
                                                      }"
                                                      x-text="dayObj.day"></span>
                                                <template x-if="dayObj.dateStr && isAvailableDate(dayObj.dateStr)">
                                                    <div class="absolute bottom-1 w-1 h-1 rounded-full bg-brand-500"></div>
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-50 space-y-2">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="font-black text-gray-700 uppercase" x-text="selectedDate ? new Date(selectedDate).toLocaleDateString([], {month: 'short', day: 'numeric', year: 'numeric'}) : 'Select a date'"></span>
                                        <span class="font-bold" :class="selectedDate && isAvailableDate(selectedDate) ? 'text-emerald-600' : 'text-gray-400'" x-text="selectedDate ? (isAvailableDate(selectedDate) ? 'Available' : 'No duty') : ''"></span>
                                    </div>

                                    <template x-for="w in windowsForDate(selectedDate)" :key="w.key">
                                        <div class="flex items-center justify-between text-[10px]">
                                            <span class="font-black text-brand-600 uppercase tracking-tighter">On Duty</span>
                                            <span class="font-bold text-gray-500" x-text="`${formatTime(w.start)} - ${formatTime(w.end)}`"></span>
                                        </div>
                                    </template>

                                    <template x-if="selectedDate && windowsForDate(selectedDate).length === 0">
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">No schedule for this date</p>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Specific Date Availability Indicator --}}
                        <div x-show="selectedDate" class="px-6 py-4 rounded-2xl border transition-all"
                             @php
                                $isAvailableJS = "availabilities.some(a => a.doctor_id == selectedDoctor && (
                                    (!a.is_recurring && a.date && a.date.substring(0, 10) === selectedDate) || 
                                    (a.is_recurring && a.recurring_day == new Date(selectedDate).getDay())
                                ))";
                             @endphp
                             :class="{{ $isAvailableJS }} ? 'border-emerald-100 bg-emerald-50/50' : 'border-amber-100 bg-amber-50/50'">
                            <template x-if="{{ $isAvailableJS }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Valid for selected date</p>
                                        <p class="text-[9px] font-bold text-emerald-800 uppercase tracking-tighter opacity-75">The doctor is on duty during this window</p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!{{ $isAvailableJS }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shadow-sm">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Conflict Detected</p>
                                        <p class="text-[9px] font-bold text-amber-800 uppercase tracking-tighter opacity-75">Doctor has no registered duty block for this specific date</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    {{-- Guidance Message --}}
                    <div x-show="providerRole() === 'doctor' && !selectedDoctor" class="mt-4 px-6 py-3 rounded-xl border border-blue-50 bg-blue-50/30 flex items-center gap-3 text-blue-400">
                        <i class="bi bi-info-circle"></i>
                        <p class="text-[9px] font-bold uppercase tracking-widest">Select a doctor to view their weekly clinical schedule</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-1 md:col-span-2 h-px bg-gray-50"></div>

        {{-- Section 2: Date, Time & Capacity --}}
        <div class="col-span-1 md:col-span-2 space-y-8">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner border border-brand-100">
                    <i class="bi bi-clock-history text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Schedule & Capacity</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Time Window & Patient Limits</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <label for="date" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Consultation Date</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-calendar-event text-xl"></i>
                        </div>
                        <input type="date" name="date" id="date" min="{{ now()->toDateString() }}" value="{{ old('date', isset($slotModel) ? $slotModel->date->format('Y-m-d') : '') }}" required x-model="selectedDate"
                            class="block w-full pl-16 pr-6 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>
                </div>

                <div class="space-y-4">
                    <label for="capacity" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Max Patients</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-people text-xl"></i>
                        </div>
                        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $slotModel->capacity ?? 1) }}" min="1" required
                            class="block w-full pl-16 pr-6 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>
                </div>

                <div class="space-y-4">
                    <label for="start_time" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Opening Time</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-clock text-xl"></i>
                        </div>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', isset($slotModel) ? \Carbon\Carbon::parse($slotModel->start_time)->format('H:i') : '08:00') }}" required
                            class="block w-full pl-16 pr-6 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>
                </div>

                <div class="space-y-4">
                    <label for="end_time" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Closing Time</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-clock-fill text-xl"></i>
                        </div>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', isset($slotModel) ? \Carbon\Carbon::parse($slotModel->end_time)->format('H:i') : '17:00') }}"
                            class="block w-full pl-16 pr-6 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 shadow-inner transition-all">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-1 md:col-span-2 h-px bg-gray-50"></div>

        {{-- Section 3: Status --}}
        <div class="col-span-1 md:col-span-2 space-y-8">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner border border-brand-100">
                    <i class="bi bi-toggle-on text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Visibility Settings</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Publication & Booking Status</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <label class="relative flex items-center p-6 bg-white border-2 rounded-[2rem] cursor-pointer hover:bg-emerald-50/30 hover:border-emerald-200 transition-all group w-full sm:w-auto"
                    :class="'{{ old('is_active', $slotModel->is_active ?? true) ? 'true' : 'false' }}' === 'true' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-100'">
                    <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ old('is_active', $slotModel->is_active ?? true) ? 'checked' : '' }}>
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center text-2xl mr-5 peer-checked:bg-emerald-100 peer-checked:text-emerald-600 transition-colors shadow-inner">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <span class="font-black text-gray-900 block tracking-tight">Active & Published</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Available for patient booking</span>
                    </div>
                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-emerald-500 rounded-[2rem] pointer-events-none transition-all"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="pt-10 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-3 bg-blue-50/50 px-5 py-3 rounded-2xl border border-blue-100/50">
            <i class="bi bi-info-circle-fill text-blue-500"></i>
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">This slot will appear in the patient's booking portal immediately</p>
        </div>
        
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <a href="{{ route('midwife.slots.index') }}" 
               class="flex-1 sm:flex-none px-10 py-5 bg-white text-gray-500 rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] border border-gray-200 hover:bg-gray-50 transition-all text-center">
                Cancel
            </a>
            <button type="submit" 
                    class="flex-1 sm:flex-none px-10 py-5 bg-brand-600 text-white rounded-[1.75rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-500/20 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all active:scale-95 flex items-center justify-center gap-3">
                {{ $buttonText }} <i class="bi {{ isset($slotModel) ? 'bi-save' : 'bi-plus-circle-fill' }}"></i>
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serviceSelect = document.getElementById('service-select');
        const doctorSelect = document.getElementById('doctor-select');
        const doctorRequiredLabel = document.getElementById('doctor-required-label');
        const doctorHelpText = document.getElementById('doctor-help-text');
        const allDoctorOptions = Array.from(doctorSelect.options);

        function updateDoctorVisibility() {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const providerType = selectedOption ? selectedOption.getAttribute('data-provider-type') : '';
            const currentValue = doctorSelect.value;

            // Clear current options
            doctorSelect.innerHTML = '';

            // Filter and re-add options
            allDoctorOptions.forEach(option => {
                const role = option.getAttribute('data-role');
                let shouldShow = false;

                if (providerType === 'Doctor') {
                    shouldShow = (role === 'doctor');
                } else if (providerType === 'Midwife') {
                    shouldShow = (role === 'midwife');
                } else if (providerType === 'Both') {
                    shouldShow = (role === 'doctor' || role === 'midwife' || role === 'none');
                } else {
                    shouldShow = true;
                }

                if (shouldShow) {
                    doctorSelect.appendChild(option);
                }
            });

            // Restore value if still present in filtered list
            doctorSelect.value = currentValue;
            
            // CRITICAL: Manually update Alpine state
            const alpineData = document.querySelector('[x-data]').__x?.$data || null;
            if (alpineData) {
                alpineData.selectedDoctor = doctorSelect.value;
            } else {
                // For older versions of Alpine or if __x is not available
                doctorSelect.dispatchEvent(new Event('input', { bubbles: true }));
                doctorSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Update labels and requirements
            if (providerType === 'Doctor') {
                doctorRequiredLabel.classList.remove('hidden');
                doctorSelect.required = true;
                doctorHelpText.textContent = 'A Doctor is required for this service';
                doctorHelpText.className = 'text-[10px] text-red-400 font-bold uppercase tracking-widest ml-4';
            } else if (providerType === 'Midwife') {
                doctorRequiredLabel.classList.remove('hidden');
                doctorSelect.required = true;
                doctorHelpText.textContent = 'A Midwife is required for this service';
                doctorHelpText.className = 'text-[10px] text-brand-600 font-bold uppercase tracking-widest ml-4';
            } else {
                doctorRequiredLabel.classList.add('hidden');
                doctorSelect.required = false;
                doctorHelpText.textContent = 'Select an available provider for this service';
                doctorHelpText.className = 'text-[10px] text-gray-400 font-bold uppercase tracking-widest ml-4';
            }
        }

        serviceSelect.addEventListener('change', updateDoctorVisibility);
        updateDoctorVisibility(); // Initial check
    });
</script>
