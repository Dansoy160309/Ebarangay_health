<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\Admin\{
    AppointmentController as AdminAppointmentController,
    AnnouncementController as AdminAnnouncementController,
    ProfileController as AdminProfileController,
    ReportController as AdminReportController,
    NotificationLogController as AdminNotificationLogController
};
use App\Http\Controllers\Admin\MedicineController as AdminMedicineController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'force.password.change', 'role:admin'])
    ->group(function () {

        // ===============================
        // Users
        // ===============================
        Route::resource('users', UserController::class);
        Route::get('users/role/patients', [UserController::class, 'patients'])->name('users.patients');
        Route::get('users/role/doctors', [UserController::class, 'doctors'])->name('users.doctors');
        Route::get('users/role/midwives', [UserController::class, 'midwives'])->name('users.midwives');
        Route::get('users/role/health-workers', [UserController::class, 'healthWorkers'])->name('users.health_workers');
        Route::get('users/role/admins', [UserController::class, 'admins'])->name('users.admins');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggleStatus');

        // ===============================
        // Announcements
        // ===============================
        Route::resource('announcements', AdminAnnouncementController::class);

        // ===============================
        // Appointments
        // ===============================
        Route::resource('appointments', AdminAppointmentController::class)
            ->except(['show']); // Show handled separately if needed

        // Additional appointment actions
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('{appointment}/show', [AdminAppointmentController::class, 'show'])
                ->name('show');
            Route::post('{appointment}/approve', [AdminAppointmentController::class, 'approve'])
                ->name('approve');
            Route::post('{appointment}/reject', [AdminAppointmentController::class, 'reject'])
                ->name('reject');
            Route::post('{appointment}/reschedule', [AdminAppointmentController::class, 'reschedule'])
                ->name('reschedule');
        });

        // ===============================
        // Health Records
        // ===============================
        Route::resource('health-records', HealthRecordController::class);
        Route::patch('health-records/{health_record}/verify', [HealthRecordController::class, 'verify'])
            ->name('health-records.verify');

        // ===============================
        // Services
        // ===============================
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);

        // ===============================
        // Medicines
        // ===============================
        Route::resource('medicines', AdminMedicineController::class)->except(['show', 'destroy']);
        Route::get('medicines-distributions', [AdminMedicineController::class, 'distributions'])->name('medicines.distributions');
        Route::get('medicines-reports', [AdminMedicineController::class, 'reports'])->name('medicines.reports');
        Route::get('medicines-supplies', [AdminMedicineController::class, 'supplies'])->name('medicines.supplies');
        Route::get('medicines-supplies/create', [AdminMedicineController::class, 'createSupply'])->name('medicines.supplies.create');
        Route::post('medicines-supplies', [AdminMedicineController::class, 'storeSupply'])->name('medicines.supplies.store');

        // ===============================
        // Vaccines
        // ===============================
        Route::resource('vaccines', \App\Http\Controllers\Admin\VaccineController::class);
        Route::post('vaccines/stock-in', [\App\Http\Controllers\Admin\VaccineController::class, 'stockIn'])->name('vaccines.stock-in');

        // ===============================
        // Profile
        // ===============================
        Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');

        // ===============================
        // Reports
        // ===============================
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/calendar', [AdminReportController::class, 'calendar'])->name('reports.calendar');
        Route::get('reports/fhsis', [AdminReportController::class, 'fhsisSummary'])->name('reports.fhsis');
        Route::get('reports/vaccines', [AdminReportController::class, 'vaccineSummary'])->name('reports.vaccines');

        // ===============================
        // Notification Logs
        // ===============================
        Route::get('notification-logs', [AdminNotificationLogController::class, 'index'])->name('notifications.index');
        Route::post('notification-logs/{notification}/resend', [AdminNotificationLogController::class, 'resend'])
            ->name('notifications.resend');
    });
