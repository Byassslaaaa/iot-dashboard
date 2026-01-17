<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Protected routes - require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/live-monitoring', [DashboardController::class, 'liveMonitoring'])->name('live-monitoring');
    Route::get('/system-logs', [DashboardController::class, 'systemLogs'])->name('system-logs');
    Route::get('/alerts', [DashboardController::class, 'alerts'])->name('alerts');
    Route::post('/alerts/mark-all-read', [DashboardController::class, 'markAllAlertsAsRead'])->name('alerts.mark-all-read');
    Route::post('/alerts/{alert}/mark-read', [DashboardController::class, 'markAlertAsRead'])->name('alerts.mark-read');
    Route::post('/alerts/{alert}/resolve', [DashboardController::class, 'resolveAlert'])->name('alerts.resolve');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
    Route::get('/about-device', [DashboardController::class, 'aboutDevice'])->name('about-device');

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
