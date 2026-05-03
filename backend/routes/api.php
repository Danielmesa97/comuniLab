<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VotacionController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ViviendaController;
use App\Http\Controllers\AnuncioController;

// --- RUTAS PÚBLICAS ---
// (Cualquiera puede acceder, necesarias para poder entrar a la app)
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/comunidades', [ComunidadController::class, 'index']);
Route::post('/comunidades', [ComunidadController::class, 'store']);
Route::post('/solicitudes', [SolicitudController::class, 'store']);
Route::get('/viviendas', [ViviendaController::class, 'index']);
Route::post('/set-password', [AuthController::class, 'setPassword']);
Route::post('/check-user', [AuthController::class, 'checkUser']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/anuncios', [AnuncioController::class, 'index']);
    Route::post('/anuncios', [AnuncioController::class, 'store']);

});

// --- RUTAS PROTEGIDAS (Requieren Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/incidencias', [IncidenciaController::class, 'index']);
    Route::post('/incidencias', [IncidenciaController::class, 'store']);
    Route::put('/incidencias/{id}', [IncidenciaController::class, 'update']);
    // Listar votaciones
    Route::get('/votaciones', [VotacionController::class, 'index']);
    // Crear nueva votación
    Route::post('/votaciones', [VotacionController::class, 'store']);
    // Emitir un voto
    Route::post('/votaciones/votar', [VotacionController::class, 'votar']);
    
    Route::get('/solicitudes', [SolicitudController::class, 'index']);
    Route::post('/solicitudes/{id}/aceptar', [SolicitudController::class, 'aceptar']);
    Route::post('/solicitudes/{id}/rechazar', [SolicitudController::class, 'rechazar']);
});