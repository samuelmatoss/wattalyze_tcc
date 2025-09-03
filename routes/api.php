<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\ApiAlertController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiDeviceController;
use App\Http\Controllers\Api\ApiEnergyTariffController;
use App\Http\Controllers\Api\ApiEnvironmentController;
use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Controllers\Api\ApiReportController;
use App\Http\Controllers\Api\ApiSettingsController;
use App\Http\Controllers\Api\ApiSupportController;

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
   
    // Add custom routes as needed
});
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/forgot-password', [ApiAuthController::class, 'sendResetLinkEmail']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::put('/profile', [ApiAuthController::class, 'update']);
    Route::post('/refresh', [ApiAuthController::class, 'refresh']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices', [ApiDeviceController::class, 'index']);
    Route::get('/devices/create', [ApiDeviceController::class, 'create']);
    Route::post('/devices', [ApiDeviceController::class, 'store']);
    Route::get('/devices/{device}', [ApiDeviceController::class, 'show']);
    Route::get('/devices/{device}/edit', [ApiDeviceController::class, 'edit']);
    Route::put('/devices/{device}', [ApiDeviceController::class, 'update']);
    Route::delete('/devices/{device}', [ApiDeviceController::class, 'destroy']);
    Route::get('/devices/{device}/diagnostics', [ApiDeviceController::class, 'diagnostics']);
    Route::get('/devices/{device}/debug', [ApiDeviceController::class, 'debug']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/energy-tariffs', [ApiEnergyTariffController::class, 'index']);
    Route::post('/energy-tariffs', [ApiEnergyTariffController::class, 'store']);
    Route::get('/energy-tariffs/{tariff}', [ApiEnergyTariffController::class, 'show']);
    Route::put('/energy-tariffs/{tariff}', [ApiEnergyTariffController::class, 'update']);
    Route::delete('/energy-tariffs/{tariff}', [ApiEnergyTariffController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/environments', [ApiEnvironmentController::class, 'index']);
    Route::post('/environments', [ApiEnvironmentController::class, 'store']);
    Route::get('/environments/{environment}', [ApiEnvironmentController::class, 'show']);
    Route::put('/environments/{environment}', [ApiEnvironmentController::class, 'update']);
    Route::delete('/environments/{environment}', [ApiEnvironmentController::class, 'destroy']);
    Route::get('/environments/{environment}/consumption', [ApiEnvironmentController::class, 'consumption']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [ApiHomeController::class, 'dashboard']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports', [ApiReportController::class, 'index']);
    Route::get('/reports/generate-form', [ApiReportController::class, 'generateForm']);
    Route::post('/reports/generate', [ApiReportController::class, 'generate']);
    Route::delete('/reports/{report}', [ApiReportController::class, 'destroy']);
    Route::get('/reports/{report}/download', [ApiReportController::class, 'download']);
    Route::get('/reports/{report}/status', [ApiReportController::class, 'status']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/settings/profile', [ApiSettingsController::class, 'profile']);
    Route::put('/settings/profile', [ApiSettingsController::class, 'updateProfile']);
    Route::put('/settings/password', [ApiSettingsController::class, 'updatePassword']);
    Route::get('/settings/notifications', [ApiSettingsController::class, 'notifications']);
    Route::get('/settings/notification-preferences', [ApiSettingsController::class, 'notificationPreferences']);
    Route::put('/settings/notification-preferences', [ApiSettingsController::class, 'updateNotificationPreferences']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/support', [ApiSupportController::class, 'submit']);
    Route::get('/support/contact-info', [ApiSupportController::class, 'contactInfo']);
});