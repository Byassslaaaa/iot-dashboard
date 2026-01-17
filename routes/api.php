<?php

use App\Http\Controllers\Api\SensorController;
use Illuminate\Support\Facades\Route;

// API untuk ESP32
Route::prefix('sensor')->group(function () {
    Route::post('/data', [SensorController::class, 'store']);
    Route::get('/status', [SensorController::class, 'status']);
    Route::get('/readings', [SensorController::class, 'readings']);
});
