<?php

use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas web del sistema
|--------------------------------------------------------------------------
|
| Organizadas por rol:
|  - '/'            → redirige al login (ninguna página interna sin sesión).
|  - '/dashboard'   → redirige al panel del rol autenticado.
|  - 'encargado/*'  → middleware auth + rol:encargado (panel y CRUD).
|  - 'estudiante/*' → middleware auth + rol:estudiante (panel, prestar, devolver).
|  - '/profile'     → perfil y cuenta (Breeze).
|
*/

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

// Rutas del Encargado: gestión completa de equipos (ver, registrar, editar, eliminar).
Route::middleware(['auth', 'rol:encargado'])->prefix('encargado')->group(function () {
    Route::get('/dashboard', [EquipoController::class, 'indexEncargado'])->name('encargado.dashboard');

    // CRUD de equipos: equipos.create, equipos.store, equipos.edit, equipos.update y equipos.destroy.
    Route::resource('equipos', EquipoController::class)->except(['index', 'show']);
});

// Rutas del Estudiante: consultar disponibles, solicitar y devolver.
Route::middleware(['auth', 'rol:estudiante'])->prefix('estudiante')->group(function () {
    Route::get('/dashboard', [EquipoController::class, 'indexEstudiante'])->name('estudiante.dashboard');
    Route::post('/equipos/{equipo}/prestar', [EquipoController::class, 'prestar'])->name('equipos.prestar');
    Route::post('/equipos/{equipo}/devolver', [EquipoController::class, 'devolver'])->name('equipos.devolver');
});

// Perfil (Breeze).
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
