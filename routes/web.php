<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/live-monitoring', [DashboardController::class, 'liveMonitoring'])->name('live-monitoring');
Route::get('/system-logs', [DashboardController::class, 'systemLogs'])->name('system-logs');
Route::get('/alerts', [DashboardController::class, 'alerts'])->name('alerts');
Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
Route::get('/about-device', [DashboardController::class, 'aboutDevice'])->name('about-device');
