<?php

use App\Http\Controllers\AlertsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LogConsultaController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Api\BairroController;
use App\Http\Controllers\Api\ReadingGeneratorController;
use App\Http\Controllers\Api\SensorController;
use Illuminate\Support\Facades\Route;

// Auth (guests only)
Route::get('/',  [LoginController::class, 'showLogin'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.attempt');

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Pages
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/map',       [MapController::class,       'index'])->name('map.operational_map');
    Route::get('/alerts',    [AlertsController::class,    'index'])->name('alerts.index');
    Route::get('/history',   [HistoryController::class,   'index'])->name('history.index');
    Route::get('/charts',    [ChartsController::class,    'index'])->name('charts.index');

    Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/clear', [SettingsController::class, 'clear'])->name('settings.clear');

    // Últimos Comandos SQL
    Route::get('/comandos',        [LogConsultaController::class, 'index'])->name('comandos.index');
    Route::post('/comandos/clear', [LogConsultaController::class, 'clear'])->name('comandos.clear');

    // Sensor CRUD — rotas web com sessão para que auth middleware funcione
    Route::post('/api/sensors',           [SensorController::class, 'store']);
    Route::put('/api/sensors/{sensor}',   [SensorController::class, 'update']);
    Route::delete('/api/sensors/{sensor}',[SensorController::class, 'destroy']);

    // Bairros CRUD
    Route::get('/api/bairros',             [BairroController::class, 'index']);
    Route::post('/api/bairros',            [BairroController::class, 'store']);
    Route::put('/api/bairros/{bairro}',    [BairroController::class, 'update']);
    Route::delete('/api/bairros/{bairro}', [BairroController::class, 'destroy']);

    // Geração automática de leituras (chamada pelo JS do browser)
    Route::post('/api/leituras/gerar', [ReadingGeneratorController::class, 'gerar'])->name('leituras.gerar');
});
