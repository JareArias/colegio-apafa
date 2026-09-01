<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ApafaAttendanceController;
use App\Http\Controllers\ApafaMeetingController;
use App\Http\Controllers\ApafaParentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\ParentController;

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
   Route::get('/apafa/asistencia', [ApafaAttendanceController::class, 'index'])->name('apafa.attendances.index');
    Route::post('/apafa/asistencia/dni', [ApafaAttendanceController::class, 'registerByDni'])->name('apafa.attendances.dni');
    Route::post('/apafa/asistencia/qr', [ApafaAttendanceController::class, 'registerByQr'])->name('apafa.attendances.qr');
    Route::get('/apafa/asistencia/{meeting}/pdf', [ApafaAttendanceController::class, 'exportPdf'])->name('apafa.attendances.export.pdf');

    
    // Gestión de Reuniones APAFA
    Route::get('/apafa/reuniones', [ApafaMeetingController::class, 'index'])->name('apafa.meetings.index');
    Route::post('/apafa/reuniones', [ApafaMeetingController::class, 'store'])->name('apafa.meetings.store');
    Route::patch('/apafa/reuniones/{meeting}/toggle', [ApafaMeetingController::class, 'toggleStatus'])->name('apafa.meetings.toggle');

    // Carnet Digital del Padre
    Route::get('/mi-carnet-apafa', [ApafaParentController::class, 'showCarnet'])->name('apafa.parent.carnet');

    //Subir Masivamente estudiantes
    Route::get('/apafa/padres', [ParentController::class, 'index'])->name('apafa.parents.index');
    Route::post('/apafa/padres/importar', [ParentController::class, 'import'])->name('apafa.parents.import');

    //Gestion de Multas
    Route::post('/apafa/reuniones/{meeting}/finalizar', [ApafaMeetingController::class, 'finish'])
        ->name('apafa.meetings.finish');
    Route::get('/apafa/multas', [FineController::class, 'index'])->name('apafa.fines.index');
    Route::post('/apafa/multas/{fine}/pagar', [FineController::class, 'markAsPaid'])->name('apafa.fines.pay');
});

Route::post('/apafa/asistencia/qr', [ApafaAttendanceController::class, 'registerByQr'])->name('apafa.attendance.qr');
Route::get('/apafa/asistencia/exportar-pdf/{meeting}', [ApafaAttendanceController::class, 'exportPdf'])->name('apafa.attendance.export.pdf');

