<?php

use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// La raíz redirige al login: ninguna página interna sin sesión.
Route::get('/', function () {
    return redirect()->route('login');
});

// Punto de entrada tras iniciar sesión: redirige al panel según el rol.
Route::get('/dashboard', function () {
    return auth()->user()->rol === 'encargado'
        ? redirect()->route('encargado.dashboard')
        : redirect()->route('estudiante.dashboard');
})->middleware('auth')->name('dashboard');

// Rutas del Encargado: gestión completa de equipos.
Route::middleware(['auth', 'rol:encargado'])->prefix('encargado')->group(function () {
    Route::get('/dashboard', [EquipoController::class, 'indexEncargado'])->name('encargado.dashboard');
    Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
    Route::get('/equipos/{equipo}/edit', [EquipoController::class, 'edit'])->name('equipos.edit');
    Route::put('/equipos/{equipo}', [EquipoController::class, 'update'])->name('equipos.update');
    Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
    Route::post('/equipos/{equipo}/prestar', [EquipoController::class, 'prestar'])->name('equipos.prestar');
    Route::post('/equipos/{equipo}/devolver', [EquipoController::class, 'devolver'])->name('equipos.devolver');
});

// Rutas del Estudiante: solo consulta de equipos disponibles.
Route::middleware(['auth', 'rol:estudiante'])->prefix('estudiante')->group(function () {
    Route::get('/dashboard', [EquipoController::class, 'indexEstudiante'])->name('estudiante.dashboard');
});

// Perfil (Breeze).
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
