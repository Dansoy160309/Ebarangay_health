<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\NotificationLogController as AdminNotificationLogController;
use App\Http\Controllers\Admin\MedicineController as AdminMedicineController;
use App\Http\Controllers\Admin\MessageTemplateController;

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
        Route::resource('medicines', AdminMedicineController::class)->except(['show']);
        Route::get('medicines-distributions', [AdminMedicineController::class, 'distributions'])->name('medicines.distributions');
        Route::get('medicines-reports', [AdminMedicineController::class, 'reports'])->name('medicines.reports');
        Route::get('medicines-reports/export', [AdminMedicineController::class, 'exportExcel'])->name('medicines.reports.export');
        Route::get('medicines-supplies', [AdminMedicineController::class, 'supplies'])->name('medicines.supplies');
        Route::get('medicines-disposals', [AdminMedicineController::class, 'disposals'])->name('medicines.disposals');
        Route::get('medicines-supplies/create', [AdminMedicineController::class, 'createSupply'])->name('medicines.supplies.create');
        Route::post('medicines-supplies', [AdminMedicineController::class, 'storeSupply'])->name('medicines.supplies.store');
        Route::post('medicines-supplies/{supply}/dispose', [AdminMedicineController::class, 'disposeSupply'])
            ->name('medicines.supplies.dispose');
        Route::post('medicines/{medicine}/archive', [AdminMedicineController::class, 'archive'])
            ->name('medicines.archive');
        Route::post('medicines/{medicine}/unarchive', [AdminMedicineController::class, 'unarchive'])
            ->name('medicines.unarchive');

        // ===============================
        // Vaccines
        // ===============================
        Route::resource('vaccines', \App\Http\Controllers\Admin\VaccineController::class);
        Route::get('vaccines/{vaccine}/recent-administrations', [\App\Http\Controllers\Admin\VaccineController::class, 'recentAdministrations'])
            ->name('vaccines.recent-administrations');
        Route::get('vaccines-disposals', [\App\Http\Controllers\Admin\VaccineController::class, 'disposals'])
            ->name('vaccines.disposals');
        Route::post('vaccines/stock-in', [\App\Http\Controllers\Admin\VaccineController::class, 'stockIn'])->name('vaccines.stock-in');
        Route::post('vaccine-batches/{batch}/dispose', [\App\Http\Controllers\Admin\VaccineController::class, 'disposeBatch'])
            ->name('vaccines.batches.dispose');

        // ===============================
        // Profile
        // ===============================
        Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');

        // ===============================
        // Reports
        // ===============================
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [AdminReportController::class, 'exportExcel'])->name('reports.export');
        Route::get('reports/calendar', [AdminReportController::class, 'calendar'])->name('reports.calendar');
        Route::get('reports/fhsis', [AdminReportController::class, 'fhsisSummary'])->name('reports.fhsis');
        Route::get('reports/fhsis/export', [AdminReportController::class, 'fhsisExport'])->name('reports.fhsis.export');
        Route::get('reports/vaccines', [AdminReportController::class, 'vaccineSummary'])->name('reports.vaccines');
        Route::get('reports/vaccines/export', [AdminReportController::class, 'vaccineExport'])->name('reports.vaccines.export');

        // ===============================
        // Notification Logs
        // ===============================
        Route::get('notification-logs', [AdminNotificationLogController::class, 'index'])->name('notifications.index');
        Route::post('notification-logs/{notification}/resend', [AdminNotificationLogController::class, 'resend'])
            ->name('notifications.resend');

        // ===============================
        // SMS Management
        // ===============================
        Route::prefix('sms')->name('sms.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SmsSettingController::class, 'index'])->name('index');
            Route::post('update', [\App\Http\Controllers\Admin\SmsSettingController::class, 'update'])->name('update');
            Route::post('broadcast', [\App\Http\Controllers\Admin\SmsSettingController::class, 'broadcast'])->name('broadcast');
            Route::post('clear-logs', [\App\Http\Controllers\Admin\SmsSettingController::class, 'clearLogs'])->name('clear-logs');
        });

        // ===============================
        // Message Templates
        // ===============================
        Route::resource('message-templates', MessageTemplateController::class)
            ->only(['index', 'edit', 'update']);
    });
