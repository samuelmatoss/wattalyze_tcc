<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\EnergyTariffController;
use App\Http\Controllers\Api\EnvironmentController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SupportController;

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
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('alerts', AlertController::class);
    // Add custom routes as needed
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'update']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::get('/devices/create', [DeviceController::class, 'create']);
    Route::post('/devices', [DeviceController::class, 'store']);
    Route::get('/devices/{device}', [DeviceController::class, 'show']);
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit']);
    Route::put('/devices/{device}', [DeviceController::class, 'update']);
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy']);
    Route::get('/devices/{device}/diagnostics', [DeviceController::class, 'diagnostics']);
    Route::get('/devices/{device}/debug', [DeviceController::class, 'debug']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/energy-tariffs', [EnergyTariffController::class, 'index']);
    Route::post('/energy-tariffs', [EnergyTariffController::class, 'store']);
    Route::get('/energy-tariffs/{tariff}', [EnergyTariffController::class, 'show']);
    Route::put('/energy-tariffs/{tariff}', [EnergyTariffController::class, 'update']);
    Route::delete('/energy-tariffs/{tariff}', [EnergyTariffController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/environments', [EnvironmentController::class, 'index']);
    Route::post('/environments', [EnvironmentController::class, 'store']);
    Route::get('/environments/{environment}', [EnvironmentController::class, 'show']);
    Route::put('/environments/{environment}', [EnvironmentController::class, 'update']);
    Route::delete('/environments/{environment}', [EnvironmentController::class, 'destroy']);
    Route::get('/environments/{environment}/consumption', [EnvironmentController::class, 'consumption']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/generate-form', [ReportController::class, 'generateForm']);
    Route::post('/reports/generate', [ReportController::class, 'generate']);
    Route::delete('/reports/{report}', [ReportController::class, 'destroy']);
    Route::get('/reports/{report}/download', [ReportController::class, 'download']);
    Route::get('/reports/{report}/status', [ReportController::class, 'status']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/settings/profile', [SettingsController::class, 'profile']);
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('/settings/password', [SettingsController::class, 'updatePassword']);
    Route::get('/settings/notifications', [SettingsController::class, 'notifications']);
    Route::get('/settings/notification-preferences', [SettingsController::class, 'notificationPreferences']);
    Route::put('/settings/notification-preferences', [SettingsController::class, 'updateNotificationPreferences']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/support', [SupportController::class, 'submit']);
    Route::get('/support/contact-info', [SupportController::class, 'contactInfo']);
});