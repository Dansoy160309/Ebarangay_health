<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $templates = MessageTemplate::orderBy('type')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('admin.message-templates.index', [
            'templates' => $templates,
        ]);
    }

    public function edit(MessageTemplate $messageTemplate)
    {
        return view('admin.message-templates.edit', [
            'template' => $messageTemplate,
        ]);
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($messageTemplate->type === 'email') {
            $rules['subject'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $messageTemplate->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'] ?? null,
            'body' => $validated['body'],
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.message-templates.index')
            ->with('success', 'Message template updated successfully.');
    }
}
