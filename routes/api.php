<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Endpoint para envio de dados de energia (dispositivos IoT)
Route::middleware(['api.token', 'throttle.api:30,1'])->post('/energy-data', [Api\EnergyDataController::class, 'store']);

// Endpoints para aplicações externas
Route::middleware(['api.token', 'throttle.api:100,1'])->prefix('v1')->group(function () {
    Route::get('/devices', [Api\DeviceController::class, 'index']);
    Route::get('/alerts', [Api\AlertController::class, 'index']);
    Route::post('/alerts/rule', [Api\AlertController::class, 'storeRule']);
    Route::put('/alerts/rule/{id}', [Api\AlertController::class, 'updateRule']);
    Route::delete('/alerts/rule/{id}', [Api\AlertController::class, 'destroyRule']);
    Route::get('/reports', [Api\ReportController::class, 'index']);
    Route::post('/reports/generate', [Api\ReportController::class, 'generate']);
    Route::get('/reports/download/{id}', [Api\ReportController::class, 'download']);
    Route::get('/energy-data', [Api\EnergyDataController::class, 'index']);
    Route::get('/energy-data/real-time', [Api\EnergyDataController::class, 'realTime']);
    Route::get('/energy-data/aggregates', [Api\EnergyDataController::class, 'aggregates']);
    Route::get('/dashboard/overview', [Api\DashboardController::class, 'overview']);
    Route::get('/dashboard/consumption', [Api\DashboardController::class, 'consumption']);
    Route::get('/dashboard/alerts', [Api\DashboardController::class, 'alerts']);
    Route::get('/dashboard/devices-status', [Api\DashboardController::class, 'devicesStatus']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
