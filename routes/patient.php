<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\Patient\{
    AppointmentController as PatientAppointmentController,
    AnnouncementController as PatientAnnouncementController,
    ProfileController as PatientProfileController
};

// Patient routes group
Route::prefix('patient')
    ->as('patient.')
    ->middleware(['auth', 'force.password.change', 'role:patient'])
    ->group(function () {

        // Profile
        Route::get('profile', [PatientProfileController::class, 'index'])->name('profile.index');
        Route::post('profile/preferences', [PatientProfileController::class, 'updatePreferences'])->name('profile.preferences');

        // Announcements
        Route::resource('announcements', PatientAnnouncementController::class)
            ->only(['index', 'show']);

        // Appointments index page
        Route::get('appointments', [PatientAppointmentController::class, 'index'])
            ->name('appointments.index');

        // Available slots page
        Route::get('appointments/available-slots', [PatientAppointmentController::class, 'availableSlots'])
            ->name('appointments.available-slots');

        // Book a slot dynamically (slot passed via URL)
        Route::post('appointments/book/{slot}', [PatientAppointmentController::class, 'book'])
            ->name('appointments.book.dynamic');

        // Appointment actions
        Route::post('appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])
            ->name('appointments.cancel');
        Route::get('appointments/{appointment}/edit', [PatientAppointmentController::class, 'edit'])
            ->name('appointments.edit');
        Route::post('appointments/{appointment}/reschedule', [PatientAppointmentController::class, 'update'])
            ->name('appointments.reschedule');
        Route::get('appointments/{appointment}', [PatientAppointmentController::class, 'show'])
            ->name('appointments.show');

        // Health Records
        Route::get('health-records', [HealthRecordController::class, 'index'])
            ->name('health-records.index');
        Route::get('health-records/{health_record}', [HealthRecordController::class, 'show'])
            ->name('health-records.show');
    });
