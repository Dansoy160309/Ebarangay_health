<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\HealthWorker\{
    AppointmentController as HealthWorkerAppointmentController,
    AnnouncementController as HealthWorkerAnnouncementController,
    ProfileController as HealthWorkerProfileController,
    ReportController as HealthWorkerReportController,
    PatientController as HealthWorkerPatientController,
    DependentController as HealthWorkerDependentController
};

Route::prefix('healthworker')
    ->as('healthworker.')
    ->middleware(['auth', 'force.password.change', 'role:health_worker'])
    ->group(function () {

        Route::get('profile', [HealthWorkerProfileController::class, 'index'])->name('profile.index');
        Route::resource('announcements', HealthWorkerAnnouncementController::class)
            ->only(['index', 'show', 'create', 'store']);

        // Patients
        Route::prefix('patients')->name('patients.')->group(function () {
            Route::get('/', [HealthWorkerPatientController::class, 'index'])->name('index');
            Route::get('create', [HealthWorkerPatientController::class, 'create'])->name('create');
            Route::post('/', [HealthWorkerPatientController::class, 'store'])->name('store');
            Route::get('credentials', [HealthWorkerPatientController::class, 'showCredentials'])->name('credentials');
            Route::get('{patient}', [HealthWorkerPatientController::class, 'show'])->name('show');
            Route::get('{patient}/details', [HealthWorkerPatientController::class, 'getDetails'])->name('details');
            Route::get('{patient}/edit', [HealthWorkerPatientController::class, 'edit'])->name('edit');
            Route::put('{patient}', [HealthWorkerPatientController::class, 'update'])->name('update');
            Route::delete('{patient}', [HealthWorkerPatientController::class, 'destroy'])->name('destroy');
            
            // Dependents Routes
            Route::post('{patient}/dependents', [HealthWorkerDependentController::class, 'store'])->name('dependents.store');
            Route::delete('{patient}/dependents/{dependent}', [HealthWorkerDependentController::class, 'destroy'])->name('dependents.destroy');
        });

        // Appointments
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [HealthWorkerAppointmentController::class, 'index'])->name('index');
            Route::get('create', [HealthWorkerAppointmentController::class, 'create'])->name('create');
            Route::post('/', [HealthWorkerAppointmentController::class, 'store'])->name('store');
            Route::get('{appointment}', [HealthWorkerAppointmentController::class, 'show'])->name('show');
            Route::get('{appointment}/edit', [HealthWorkerAppointmentController::class, 'edit'])->name('edit');
            Route::put('{appointment}', [HealthWorkerAppointmentController::class, 'update'])->name('update');
            Route::post('{appointment}/approve', [HealthWorkerAppointmentController::class, 'approve'])->name('approve');
            Route::post('{appointment}/cancel', [HealthWorkerAppointmentController::class, 'cancel'])->name('cancel');
            Route::get('{appointment}/add-vitals', [HealthWorkerAppointmentController::class, 'addVitals'])->name('add-vitals');
            Route::post('{appointment}/store-vitals', [HealthWorkerAppointmentController::class, 'storeVitals'])->name('store-vitals');
        });

        // Health Records
        Route::resource('health-records', HealthRecordController::class)
            ->except(['destroy']);

        // Slots
        Route::prefix('slots')->name('slots.')->group(function () {
            Route::get('/', [SlotController::class, 'index'])->name('index');
            Route::get('{slot}/details', [SlotController::class, 'details'])->name('details');
        });

    });
