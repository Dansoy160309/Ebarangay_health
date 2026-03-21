<?php

use Illuminate\Support\Facades\Route;

// ===============================
// AUTH CONTROLLERS
// ===============================
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\PasswordChangeController;

// ===============================
// COMMON CONTROLLERS
// ===============================
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

// ===============================
// PASSWORD RESET ROUTES
// ===============================
Route::prefix('password')->group(function () {
    Route::get('forgot', [ForgotPasswordController::class, 'showForgotForm'])->name('password.forgot');
    Route::post('forgot', [ForgotPasswordController::class, 'sendResetLink'])->name('password.forgot.submit');
    Route::get('reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset', [ResetPasswordController::class, 'reset'])->name('password.reset.submit');
});

// ===============================
// ROOT REDIRECT
// ===============================
Route::get('/', function () {
    return view('welcome');
});

// ===============================
// AUTH ROUTES
// ===============================
Route::prefix('auth')->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.attempt');
    });
    
    Route::match(['post', 'get'], 'logout', [LoginController::class, 'logout'])->name('logout');
});

// ===============================
// PASSWORD CHANGE (GLOBAL)
// ===============================
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change/notice', [PasswordChangeController::class, 'showNotice'])->name('password.change.notice');
    Route::get('/password/change', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

// ===============================
// AUTHENTICATED ROUTES
// ===============================
Route::get('/offline', function () {
    return view('offline');
});

Route::middleware(['auth', 'force.password.change'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/admin/dashboard', '/dashboard')->name('admin.dashboard');
    Route::redirect('/healthworker/dashboard', '/dashboard')->name('healthworker.dashboard');
    Route::redirect('/doctor/dashboard', '/dashboard')->name('doctor.dashboard');
    Route::redirect('/midwife/dashboard', '/dashboard')->name('midwife.dashboard');
    Route::redirect('/midwife/dashboard', '/dashboard')->name('midwife.dashboard');
    Route::redirect('/patient/dashboard', '/dashboard')->name('patient.dashboard');

    // Notifications (common)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('read/{id}', [NotificationController::class, 'read'])->name('notifications.read');
        Route::get('mark-all', [NotificationController::class, 'markAll'])->name('notifications.markAll');
    });
});

// ===============================
// ROLE-SPECIFIC ROUTES
// ===============================
require __DIR__ . '/admin.php';
require __DIR__ . '/healthworker.php';
require __DIR__ . '/doctor.php';
require __DIR__ . '/midwife.php';
require __DIR__ . '/patient.php';
