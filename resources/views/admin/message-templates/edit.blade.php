@extends('layouts.app')

@section('title', 'Edit Message Template')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-widest text-brand-500">Edit Template</p>
        <h1 class="text-2xl font-black text-gray-900 mt-1">{{ $template->name }}</h1>
        <p class="text-sm text-gray-500 mt-2">Update the content used when staff sends this template.</p>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('admin.message-templates.update', $template) }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name', $template->name) }}" class="template-editor-field w-full rounded-xl border border-gray-200 focus:ring-brand-500 focus:border-brand-500 px-4 py-3" required>
        </div>

        @if($template->type === 'email')
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Subject</label>
            <input id="template-subject" type="text" name="subject" value="{{ old('subject', $template->subject) }}" class="template-editor-field w-full rounded-xl border border-gray-200 focus:ring-brand-500 focus:border-brand-500 px-4 py-3" required>
        </div>
        @endif

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-500">Body</label>
                @if($template->type === 'sms')
                    <span id="sms-char-counter" class="text-xs font-bold text-gray-400">0 chars</span>
                @endif
            </div>
            <textarea id="template-body" name="body" rows="12" class="template-editor-field w-full rounded-xl border border-gray-200 focus:ring-brand-500 focus:border-brand-500 px-4 py-3 font-mono text-sm" required>{{ old('body', $template->body) }}</textarea>
            @if($template->type === 'sms')
                <p class="text-xs text-gray-400 mt-2">Tip: Keep SMS near 160 characters for single-message delivery.</p>
            @endif
        </div>

        <label class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
            <span class="text-sm text-gray-700 font-medium">Active</span>
        </label>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-3 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition">Save Changes</button>
            <a href="{{ route('admin.message-templates.index') }}" class="px-5 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition">Back</a>
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-black text-gray-900 mb-2">Live Sample Preview</h2>
        <p class="text-sm text-gray-500 mb-4">This preview uses sample values so you can check formatting before saving.</p>

        @if($template->type === 'email')
            <div class="mb-3">
                <p class="text-xs font-black uppercase tracking-widest text-gray-500 mb-1">Subject Preview</p>
                <div id="subject-preview" class="template-preview-box rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700"></div>
            </div>
        @endif

        <div>
            <p class="text-xs font-black uppercase tracking-widest text-gray-500 mb-1">Body Preview</p>
            <pre id="body-preview" class="template-preview-box rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap"></pre>
        </div>
    </div>

</div>

<style>
    .template-editor-field {
        background-color: #ffffff !important;
        color: #111827 !important;
        color-scheme: light;
    }

    .template-editor-field:focus,
    .template-editor-field:active {
        background-color: #ffffff !important;
        color: #111827 !important;
    }

    .template-preview-box {
        min-height: 56px;
    }
</style>

<script>
    (function () {
        const sampleData = {
            '{patient_name}': 'Juan Dela Cruz',
            '{patient_first_name}': 'Juan',
            '{patient_age}': '11',
            '{patient_gender}': 'Male',
            '{guardian_name}': 'Maria Dela Cruz',
            '{recipient_label}': 'Maria',
            '{service_type}': 'Immunization',
            '{appointment_date}': 'Apr 11, 2026',
            '{appointment_time}': '08:00 AM',
            '{appointment_version}': '2nd',
            '{health_center_name}': 'E-Barangay Health Center',
            '{contact_number}': '09123456789',
            '{midwife_name}': 'Mae Healthcare Provider'
        };

        const bodyInput = document.getElementById('template-body');
        const subjectInput = document.getElementById('template-subject');
        const bodyPreview = document.getElementById('body-preview');
        const subjectPreview = document.getElementById('subject-preview');
        const smsCounter = document.getElementById('sms-char-counter');

        const renderTemplate = (text) => {
            let output = text || '';
            Object.keys(sampleData).forEach((key) => {
                output = output.split(key).join(sampleData[key]);
            });
            return output;
        };

        const refreshPreview = () => {
            if (bodyInput && bodyPreview) {
                bodyPreview.textContent = renderTemplate(bodyInput.value);
            }

            if (subjectInput && subjectPreview) {
                subjectPreview.textContent = renderTemplate(subjectInput.value);
            }

            if (smsCounter && bodyInput) {
                const length = bodyInput.value.length;
                smsCounter.textContent = `${length} chars`;
                smsCounter.classList.toggle('text-red-500', length > 160);
                smsCounter.classList.toggle('text-gray-400', length <= 160);
            }
        };

        if (bodyInput) bodyInput.addEventListener('input', refreshPreview);
        if (subjectInput) subjectInput.addEventListener('input', refreshPreview);

        refreshPreview();
    })();
</script>
@endsection
