<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\AppointmentController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\HealthWorker\{
    PatientController as HealthWorkerPatientController,
    DependentController as HealthWorkerDependentController
};
use App\Http\Controllers\Midwife\{
    MedicineDistributionController,
    DefaulterController
};

Route::prefix('midwife')
    ->name('midwife.')
    ->middleware(['auth', 'role:midwife'])
    ->group(function () {

        // Profile (reuses health worker profile layout)
        Route::get('profile', function () {
            $user = auth()->user();
            return view('healthworker.profile.index', compact('user'));
        })->name('profile.index');

        Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('appointments/defaulters', [DefaulterController::class, 'index'])->name('appointments.defaulters');
        Route::post('appointments/{appointment}/no-show', [DefaulterController::class, 'markAsNoShow'])->name('appointments.no-show');
        Route::post('appointments/{appointment}/recall-sms', [DefaulterController::class, 'sendRecallSms'])->name('appointments.recall-sms');
        Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('appointments/{appointment}/consult', [AppointmentController::class, 'consult'])->name('appointments.consult');

        Route::resource('health-records', HealthRecordController::class);

        Route::resource('slots', SlotController::class);

        // Patients (transferred from Health Worker)
        Route::prefix('patients')->name('patients.')->group(function () {
            Route::get('/', [HealthWorkerPatientController::class, 'index'])->name('index');
            Route::get('create', [HealthWorkerPatientController::class, 'create'])->name('create');
            Route::post('/', [HealthWorkerPatientController::class, 'store'])->name('store');
            Route::get('credentials', [HealthWorkerPatientController::class, 'showCredentials'])->name('credentials'); // New credentials page
            Route::get('{patient}', [HealthWorkerPatientController::class, 'show'])->name('show');
            Route::get('{patient}/details', [HealthWorkerPatientController::class, 'getDetails'])->name('details'); // AJAX route
            Route::get('{patient}/edit', [HealthWorkerPatientController::class, 'edit'])->name('edit');
            Route::put('{patient}', [HealthWorkerPatientController::class, 'update'])->name('update');
            Route::delete('{patient}', [HealthWorkerPatientController::class, 'destroy'])->name('destroy');
            
            // Dependents Routes
            Route::post('{patient}/dependents', [HealthWorkerDependentController::class, 'store'])->name('dependents.store');
            Route::delete('{patient}/dependents/{dependent}', [HealthWorkerDependentController::class, 'destroy'])->name('dependents.destroy');
        });

        Route::get('announcements', [App\Http\Controllers\Doctor\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('announcements/{announcement}', [App\Http\Controllers\Doctor\AnnouncementController::class, 'show'])->name('announcements.show');

        Route::prefix('medicines')->name('medicines.')->group(function () {
            Route::get('distribute', [MedicineDistributionController::class, 'create'])->name('distribute.create');
            Route::post('distribute', [MedicineDistributionController::class, 'store'])->name('distribute.store');
        });

        // 💉 Vaccine Inventory Routes
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [VaccineInventoryController::class, 'index'])->name('index');
            Route::post('/vaccine', [VaccineInventoryController::class, 'storeVaccine'])->name('vaccine.store');
            Route::post('/batch', [VaccineInventoryController::class, 'store'])->name('batch.store');
        });
    });
