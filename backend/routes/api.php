<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VotacionController;

Route::get('/comunidades', [ComunidadController::class, 'index']);
Route::post('/comunidades', [ComunidadController::class, 'store']);
Route::get('/incidencias', [IncidenciaController::class, 'index']);
Route::post('/incidencias', [IncidenciaController::class, 'store']);
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/votaciones', [VotacionController::class, 'index']);
Route::middleware('auth:sanctum')->post('/votaciones/votar', [VotacionController::class, 'votar']);