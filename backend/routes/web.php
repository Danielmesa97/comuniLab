<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\ComunidadController;

Route::get('/comunidades', [ComunidadController::class, 'index']);
Route::post('/comunidades', [ComunidadController::class, 'store']);

//use App\Http\Controllers\IncidenciaController;

//Route::get('/incidencias', [IncidenciaController::class, 'index']);
//Route::post('/incidencias', [IncidenciaController::class, 'store']);