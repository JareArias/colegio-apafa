<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ApafaAttendanceController;
use App\Http\Controllers\ApafaMeetingController;
use App\Http\Controllers\ApafaParentController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas de Asistencia APAFA
    Route::get('/apafa/asistencia', [ApafaAttendanceController::class, 'index'])->name('apafa.attendance.index');
    Route::post('/apafa/asistencia/dni', [ApafaAttendanceController::class, 'registerByDni'])->name('apafa.attendance.dni');

    
    // Gestión de Reuniones APAFA
    Route::get('/apafa/reuniones', [ApafaMeetingController::class, 'index'])->name('apafa.meetings.index');
    Route::post('/apafa/reuniones', [ApafaMeetingController::class, 'store'])->name('apafa.meetings.store');
    Route::patch('/apafa/reuniones/{meeting}/toggle', [ApafaMeetingController::class, 'toggleStatus'])->name('apafa.meetings.toggle');

    // Carnet Digital del Padre
    Route::get('/mi-carnet-apafa', [ApafaParentController::class, 'showCarnet'])->name('apafa.parent.carnet');
});

Route::post('/apafa/asistencia/qr', [ApafaAttendanceController::class, 'registerByQr'])->name('apafa.attendance.qr');
Route::get('/apafa/asistencia/exportar-pdf/{meeting}', [ApafaAttendanceController::class, 'exportPdf'])->name('apafa.attendance.export.pdf');

