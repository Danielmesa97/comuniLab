<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\IncidenciaController;

Route::get('/comunidades', [ComunidadController::class, 'index']);
Route::post('/comunidades', [ComunidadController::class, 'store']);
Route::get('/incidencias', [IncidenciaController::class, 'index']);
Route::post('/incidencias', [IncidenciaController::class, 'store']);
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);