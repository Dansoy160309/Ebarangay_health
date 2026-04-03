<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-8">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-black text-gray-700 uppercase tracking-[0.2em]">Quick Actions</h3>
        <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Tap any card</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-2 gap-3">
        <a href="{{ route('patient.appointments.index') }}" class="rounded-xl bg-blue-50 border border-blue-100 p-3 text-center transition hover:bg-blue-100">
            <div class="mb-2 text-brand-600 text-lg">📅</div>
            <p class="text-xs font-black text-gray-900 uppercase tracking-wider">Book Appointment</p>
        </a>
        <a href="{{ route('patient.health-records.index') }}" class="rounded-xl bg-purple-50 border border-purple-100 p-3 text-center transition hover:bg-purple-100">
            <div class="mb-2 text-purple-600 text-lg">📄</div>
            <p class="text-xs font-black text-gray-900 uppercase tracking-wider">Health Records</p>
        </a>
        <a href="{{ route('patient.announcements.index') }}" class="rounded-xl bg-amber-50 border border-amber-100 p-3 text-center transition hover:bg-amber-100">
            <div class="mb-2 text-amber-600 text-lg">📢</div>
            <p class="text-xs font-black text-gray-900 uppercase tracking-wider">Health Advisories</p>
        </a>
        <a href="{{ route('patient.profile.index') }}" class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center transition hover:bg-emerald-100">
            <div class="mb-2 text-emerald-600 text-lg">👤</div>
            <p class="text-xs font-black text-gray-900 uppercase tracking-wider">Profile</p>
        </a>
    </div>
</div>
