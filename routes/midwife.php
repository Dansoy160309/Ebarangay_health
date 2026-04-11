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
    DefaulterController,
    VaccineInventoryController,
    ReferralSlipController
};

Route::prefix('midwife')
    ->name('midwife.')
    ->middleware(['auth', 'role:midwife'])
    ->group(function () {

        // Referral Slips
        Route::prefix('referral-slips')->name('referral-slips.')->group(function () {
            Route::get('/', [ReferralSlipController::class, 'index'])->name('index');
            Route::get('create', [ReferralSlipController::class, 'create'])->name('create');
            Route::post('/', [ReferralSlipController::class, 'store'])->name('store');
            Route::get('{referralSlip}', [ReferralSlipController::class, 'show'])->name('show');
            Route::get('{referralSlip}/print', [ReferralSlipController::class, 'print'])->name('print');
            Route::delete('{referralSlip}', [ReferralSlipController::class, 'destroy'])->name('destroy');
        });

        // Profile (reuses health worker profile layout)
        Route::get('profile', function () {
            $user = auth()->user();
            return view('healthworker.profile.index', compact('user'));
        })->name('profile.index');

        Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('appointments/{appointment}/send-reminder-sms', [AppointmentController::class, 'sendReminderSms'])->name('appointments.send-reminder-sms');
        Route::post('appointments/{appointment}/send-reminder-email', [AppointmentController::class, 'sendReminderEmail'])->name('appointments.send-reminder-email');
        Route::get('appointments/defaulters', [DefaulterController::class, 'index'])->name('appointments.defaulters');
        Route::post('appointments/{appointment}/send-email-template', [DefaulterController::class, 'sendEmailTemplate'])->name('appointments.send-email-template');
        Route::post('appointments/{appointment}/send-sms-template', [DefaulterController::class, 'sendSmsTemplate'])->name('appointments.send-sms-template');
        Route::post('appointments/{appointment}/no-show', [DefaulterController::class, 'markAsNoShow'])->name('appointments.no-show');
        Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('appointments/{appointment}/consult', [AppointmentController::class, 'consult'])->name('appointments.consult');
        Route::post('appointments/{appointment}/sign-complete', [AppointmentController::class, 'signAndComplete'])->name('appointments.sign-complete');

        // Patient info access for dashboard alerts (view-only in UI)
        Route::prefix('patients')->name('patients.')->group(function () {
            Route::get('/', [HealthWorkerPatientController::class, 'index'])->name('index');
            Route::get('{patient}', [HealthWorkerPatientController::class, 'show'])->name('show');
        });

        Route::resource('health-records', HealthRecordController::class);

        Route::resource('slots', SlotController::class);

        // Doctor Presence & Tracking
        Route::prefix('doctor-presence')->name('doctor-presence.')->group(function () {
            Route::get('/', [App\Http\Controllers\Midwife\DoctorPresenceController::class, 'index'])->name('index');
            Route::post('{availability}/arrived', [App\Http\Controllers\Midwife\DoctorPresenceController::class, 'markArrived'])->name('arrived');
            Route::post('{availability}/absent', [App\Http\Controllers\Midwife\DoctorPresenceController::class, 'markAbsent'])->name('absent');
            Route::post('{availability}/delayed', [App\Http\Controllers\Midwife\DoctorPresenceController::class, 'markDelayed'])->name('delayed');
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
