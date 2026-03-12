@props(['slotModel' => null, 'action', 'method' => 'POST', 'buttonText', 'doctors' => [], 'services' => []])

<style>
    /* Improve visibility of AM/PM in Chrome/Edge */
    input[type="time"]::-webkit-datetime-edit-ampm-field {
        background-color: #f0f9ff;
        border-radius: 4px;
        padding: 2px 4px;
        color: #0369a1;
        font-weight: 900;
    }
</style>

<form action="{{ $action }}" method="POST" class="p-10 lg:p-14 space-y-12" id="slot-form">
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
                        <select name="doctor_id" id="doctor-select"
                            class="block w-full pl-16 pr-10 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-base font-bold text-gray-900 transition-all appearance-none">
                            <option value="">-- No Doctor / Midwife Only --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $slotModel->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->isDoctor() ? 'Dr.' : '' }} {{ $doctor->full_name }} {{ $doctor->isMidwife() ? '(Midwife)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-6 pointer-events-none text-gray-400">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest ml-4" id="doctor-help-text">Select a doctor if required by the service</p>
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
                        <input type="date" name="date" id="date" value="{{ old('date', isset($slotModel) ? $slotModel->date->format('Y-m-d') : '') }}" required
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
                            class="block w-full pl-16 pr-12 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-xl font-black text-gray-900 shadow-inner transition-all">
                    </div>
                </div>

                <div class="space-y-4">
                    <label for="end_time" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Closing Time</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <i class="bi bi-clock-fill text-xl"></i>
                        </div>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', isset($slotModel) ? \Carbon\Carbon::parse($slotModel->end_time)->format('H:i') : '17:00') }}"
                            class="block w-full pl-16 pr-12 py-5 bg-gray-50 border-none rounded-[2rem] focus:ring-4 focus:ring-brand-50 focus:bg-white text-xl font-black text-gray-900 shadow-inner transition-all">
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
        const doctorContainer = document.getElementById('doctor-container');
        const doctorSelect = document.getElementById('doctor-select');
        const doctorRequiredLabel = document.getElementById('doctor-required-label');
        const doctorHelpText = document.getElementById('doctor-help-text');

        function updateDoctorVisibility() {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const providerType = selectedOption ? selectedOption.getAttribute('data-provider-type') : '';

            if (providerType === 'Doctor') {
                doctorRequiredLabel.classList.remove('hidden');
                doctorSelect.required = true;
                doctorHelpText.textContent = 'Please select a qualified doctor for this medical service';
                doctorHelpText.className = 'text-[10px] text-red-400 font-bold uppercase tracking-widest ml-4';
            } else {
                doctorRequiredLabel.classList.add('hidden');
                doctorSelect.required = false;
                doctorHelpText.textContent = 'Select a doctor if required by the service';
                doctorHelpText.className = 'text-[10px] text-gray-400 font-bold uppercase tracking-widest ml-4';
            }
        }

        serviceSelect.addEventListener('change', updateDoctorVisibility);
        updateDoctorVisibility(); // Initial check
    });
</script>