<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VotacionController;

// --- RUTAS PÚBLICAS ---
// (Cualquiera puede acceder, necesarias para poder entrar a la app)
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);

// Nota del profe: ¿Seguro que comunidades e incidencias deben ser públicas? 
// De momento te las dejo igual, pero en un entorno real seguramente irían protegidas.
Route::get('/comunidades', [ComunidadController::class, 'index']);
Route::post('/comunidades', [ComunidadController::class, 'store']);
Route::get('/incidencias', [IncidenciaController::class, 'index']);
Route::post('/incidencias', [IncidenciaController::class, 'store']);

// --- RUTAS PROTEGIDAS (Requieren Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Listar votaciones
    Route::get('/votaciones', [VotacionController::class, 'index']);
    
    // Crear nueva votación
    Route::post('/votaciones', [VotacionController::class, 'store']);
    
    // Emitir un voto
    Route::post('/votaciones/votar', [VotacionController::class, 'votar']);
    
});