<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\AppointmentController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\DashboardController;

Route::prefix('doctor')
    ->name('doctor.')
    ->middleware(['auth', 'role:doctor'])
    ->group(function () {
        
        // Dashboard
        // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Already handled in web.php

        // Profile (reuses health worker profile layout)
        Route::get('profile', function () {
            $user = auth()->user();
            return view('healthworker.profile.index', compact('user'));
        })->name('profile.index');

        // Appointments
        Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('appointments/{appointment}/consult', [AppointmentController::class, 'consult'])->name('appointments.consult');

        // Patients
        Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

        // Health Records
        Route::resource('health-records', HealthRecordController::class);

        // Availability (Duty Schedule)
        Route::get('availability', [App\Http\Controllers\Doctor\AvailabilityController::class, 'index'])->name('availability.index');
        Route::get('availability/create', [App\Http\Controllers\Doctor\AvailabilityController::class, 'create'])->name('availability.create');
        Route::post('availability', [App\Http\Controllers\Doctor\AvailabilityController::class, 'store'])->name('availability.store');
        Route::delete('availability/{availability}', [App\Http\Controllers\Doctor\AvailabilityController::class, 'destroy'])->name('availability.destroy');

        // Announcements
        Route::get('announcements', [App\Http\Controllers\Doctor\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('announcements/{announcement}', [App\Http\Controllers\Doctor\AnnouncementController::class, 'show'])->name('announcements.show');
    });
