<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\SupportController;
use App\Http\Controllers\Web\DeviceController;
use App\Http\Controllers\Web\EnvironmentController;
use App\Http\Controllers\Web\AlertController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\EnergyTariffController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
Route::get('/devices/{device}/influx-data', [DeviceController::class, 'influxData'])
    ->name('devices.influxData');

// Cadastro
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


// Authenticated Routes
Route::middleware('auth')->group(
    function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/home', [HomeController::class, 'dashboard'])->name('dashboard.home');


        // Profile Management
        Route::patch('/profile', [AuthController::class, 'update'])->name('profile.update');

        // Environments Routes
        Route::prefix('environments')->name('environments.')->group(function () {
            Route::get('/', [EnvironmentController::class, 'index'])->name('index');
            Route::get('/create', [EnvironmentController::class, 'create'])->name('create');
            Route::post('/', [EnvironmentController::class, 'store'])->name('store');
            Route::get('/{environment}', [EnvironmentController::class, 'show'])->name('show');
            Route::get('/{environment}/edit', [EnvironmentController::class, 'edit'])->name('edit');
            Route::patch('/{environment}', [EnvironmentController::class, 'update'])->name('update');
            Route::delete('/{environment}', [EnvironmentController::class, 'destroy'])->name('destroy');
            Route::get('/hierarchy/view', [EnvironmentController::class, 'hierarchy'])->name('hierarchy');
        });

        // Devices Routes
        Route::prefix('devices')->name('devices.')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])->name('index');
            Route::get('/create', [DeviceController::class, 'create'])->name('create');
            Route::post('/', [DeviceController::class, 'store'])->name('store');
            Route::get('/{device}', [DeviceController::class, 'show'])->name('show');
            Route::get('/{device}/edit', [DeviceController::class, 'edit'])->name('edit');
            Route::patch('/{device}', [DeviceController::class, 'update'])->name('update');
            Route::delete('/{device}', [DeviceController::class, 'destroy'])->name('destroy');
            Route::get('/{device}/diagnostics', [DeviceController::class, 'diagnostics'])->name('diagnostics');
            Route::post('/{device}/restart', [DeviceController::class, 'restart'])->name('restart');
        });

        // Alerts Routes
        Route::prefix('alerts')->name('alerts.')->group(function () {
            // Alert Rules
            Route::get('/rules', [AlertController::class, 'rules'])->name('rules');
            Route::post('/rules', [AlertController::class, 'storeRule'])->name('rules.store');
            Route::get('/rules/{rule}/edit', [AlertController::class, 'editRule'])->name('rules.edit');
            Route::patch('/rules/{rule}', [AlertController::class, 'updateRule'])->name('rules.update');
            Route::delete('/rules/{rule}', [AlertController::class, 'destroyRule'])->name('rules.destroy');
            Route::post('/rules/{rule}/toggle', [AlertController::class, 'toggleRule'])->name('rules.toggle');

            // Active Alerts
            Route::get('/active', [AlertController::class, 'active'])->name('active');
            Route::post('/{alert}/resolve', [AlertController::class, 'markResolved'])->name('resolve');
            Route::post('/{alert}/acknowledge', [AlertController::class, 'acknowledge'])->name('acknowledge');
            Route::post('/bulk-resolve', [AlertController::class, 'bulkResolve'])->name('bulk-resolve');

            // Alert History
            Route::get('/history', [AlertController::class, 'history'])->name('history');

            // Notification Settings
            Route::get('/notifications', [AlertController::class, 'notificationSettings'])->name('notifications');
            Route::post('/notifications', [AlertController::class, 'saveNotificationSettings'])->name('notifications.save');
        });
        Route::prefix('tariffs')->name('tariffs.')->group(function () {
            Route::get('/', [EnergyTariffController::class, 'index'])->name('index');
            Route::get('/create', [EnergyTariffController::class, 'create'])->name('create');
            Route::post('/', [EnergyTariffController::class, 'store'])->name('store');
            Route::get('/{tariff}/edit', [EnergyTariffController::class, 'edit'])->name('edit');
            Route::put('/{tariff}', [EnergyTariffController::class, 'update'])->name('update');
            Route::delete('/{tariff}', [EnergyTariffController::class, 'destroy'])->name('destroy');
        });
        // Reports Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/generate', [ReportController::class, 'generateForm'])->name('generate');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate.store');
            Route::get('/{report}/download', [ReportController::class, 'download'])->name('download');
            Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');

            // Scheduled Reports
            Route::get('/schedule', [ReportController::class, 'schedule'])->name('schedule');
            Route::post('/schedule', [ReportController::class, 'scheduleReport'])->name('schedule.store');
            Route::delete('/schedule/{report}', [ReportController::class, 'cancelSchedule'])->name('schedule.cancel');

            // Report Templates
            Route::get('/templates', [ReportController::class, 'templates'])->name('templates');
            Route::post('/templates', [ReportController::class, 'saveTemplate'])->name('templates.store');
            Route::delete('/templates/{template}', [ReportController::class, 'deleteTemplate'])->name('templates.destroy');
            Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show')->middleware('auth');
        });


        // Settings Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            // Profile Settings
            Route::get('/profile', [SettingsController::class, 'profile'])->name('profile');
            Route::patch('/profile', [SettingsController::class, 'updateProfile'])->name('security.update');


  


            // Notification Settings
            Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
           

            // Security Settings
            Route::get('/security', [SettingsController::class, 'security'])->name('security');
            Route::patch('/security/password', [SettingsController::class, 'updatePassword'])->name('security.password');


            // API Settings
            Route::get('/api', [SettingsController::class, 'api'])->name('api');
            Route::post('/api/tokens', [SettingsController::class, 'createApiToken'])->name('api.tokens.create');
            Route::delete('/api/tokens/{token}', [SettingsController::class, 'revokeApiToken'])->name('api.tokens.revoke');

            // System Preferences
            Route::get('/preferences', [SettingsController::class, 'preferences'])->name('preferences');
            Route::patch('/preferences', [SettingsController::class, 'updatePreferences'])->name('preferences.update');

            // Data Export/Import
            Route::get('/data', [SettingsController::class, 'dataManagement'])->name('data');
            Route::post('/data/export', [SettingsController::class, 'exportData'])->name('data.export');
            Route::post('/data/import', [SettingsController::class, 'importData'])->name('data.import');
            Route::delete('/data/purge', [SettingsController::class, 'purgeData'])->name('data.purge');
        });

        Route::middleware('auth')->group(function () {});

        // Support Routes
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [SupportController::class, 'index'])->name('index');
            Route::post('/ticket', [SupportController::class, 'submit'])->name('ticket.submit');
            Route::get('/faq', [SupportController::class, 'faq'])->name('faq');
            Route::get('/documentation', [SupportController::class, 'documentation'])->name('documentation');
            Route::get('/contact', [SupportController::class, 'contact'])->name('contact');
            Route::post('/contact', [SupportController::class, 'sendMessage'])->name('contact.send');
        });
    }

);
