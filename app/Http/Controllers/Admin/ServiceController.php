<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services',
            'description' => 'nullable|string',
            'provider_type' => ['required', Rule::in(['Doctor', 'Midwife', 'Both'])],
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('services')->ignore($service->id)],
            'description' => 'nullable|string',
            'provider_type' => ['required', Rule::in(['Doctor', 'Midwife', 'Both'])],
        ]);

        $oldName = $service->name;
        $service->update($validated);

        // Update related Slots and Appointments if name changed
        if ($oldName !== $service->name) {
            \App\Models\Slot::where('service', $oldName)->update(['service' => $service->name]);
            \App\Models\Appointment::where('service', $oldName)->update(['service' => $service->name]);
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // Optional: Check if slots exist for this service before deleting?
        // For now, let's just delete. If DB has foreign keys, it might fail or cascade.
        // Slots likely reference service name string based on previous code, but recent changes might have kept it string.
        // The Slot model likely stores service name.
        
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
