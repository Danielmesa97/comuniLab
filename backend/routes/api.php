<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComunidadController;

Route::get('/comunidades', [ComunidadController::class, 'index']);
Route::post('/comunidades', [ComunidadController::class, 'store']);