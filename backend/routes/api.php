<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\ViviendaController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\VotacionController;
use App\Http\Controllers\InstalacionController;
use App\Http\Controllers\SuperAdmin\ComunidadAdminController;
use App\Http\Controllers\SuperAdmin\SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\SuperAdminViviendaController;
/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/set-password', [AuthController::class, 'setPassword']);
Route::post('/check-user', [AuthController::class, 'checkUser']);

Route::get('/comunidades', [ComunidadController::class, 'index']);

Route::post('/solicitudes', [SolicitudController::class, 'store']);


/*
|--------------------------------------------------------------------------
| RUTAS AUTENTICADAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | VIVIENDAS
    |--------------------------------------------------------------------------
    */
    Route::get('/viviendas', [ViviendaController::class, 'index']);

    
    /*
    |--------------------------------------------------------------------------
    | ANUNCIOS
    |--------------------------------------------------------------------------
    */

    Route::get('/anuncios', [AnuncioController::class, 'index']);
    Route::post('/anuncios', [AnuncioController::class, 'store']);


    /*
    |--------------------------------------------------------------------------
    | INCIDENCIAS
    |--------------------------------------------------------------------------
    */

    Route::get('/incidencias', [IncidenciaController::class, 'index']);
    Route::post('/incidencias', [IncidenciaController::class, 'store']);
    Route::put('/incidencias/{id}', [IncidenciaController::class, 'update']);


    /*
    |--------------------------------------------------------------------------
    | VOTACIONES
    |--------------------------------------------------------------------------
    */

    Route::get('/votaciones', [VotacionController::class, 'index']);
    Route::post('/votaciones', [VotacionController::class, 'store']);
    Route::post('/votaciones/votar', [VotacionController::class, 'votar']);
    Route::post('/votaciones/delegar', [VotacionController::class, 'delegar']);
    Route::post('/votaciones/ejecutar-delegado', [VotacionController::class, 'ejecutarDelegado']);

     /*
    |--------------------------------------------------------------------------
    | Vivienda
    |--------------------------------------------------------------------------
    */


    Route::post('/viviendas', [ViviendaController::class, 'store']);
    Route::put('/viviendas/{id}', [ViviendaController::class, 'update']);
    Route::delete('/viviendas/{id}', [ViviendaController::class, 'destroy']);
    Route::get('/viviendas', [ViviendaController::class, 'index']);


    /*
    |--------------------------------------------------------------------------
    | INSTALACIONES
    |--------------------------------------------------------------------------
    */

    
    Route::get('/instalaciones', [InstalacionController::class, 'index']);
    Route::get('/instalaciones/{id}/reservas', [InstalacionController::class, 'getReservas']);
    Route::post('/reservas', [InstalacionController::class, 'storeReserva']);
    Route::post(
        '/instalaciones',
        [InstalacionController::class, 'store']
    );

    Route::delete(
        '/instalaciones/{id}',
        [InstalacionController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | SOLICITUDES
    |--------------------------------------------------------------------------
    */

    Route::get('/solicitudes', [SolicitudController::class, 'index']);

    Route::post('/solicitudes/{id}/aceptar', [
        SolicitudController::class,
        'aceptar'
    ]);

    Route::post('/solicitudes/{id}/rechazar', [
        SolicitudController::class,
        'rechazar'
    ]);
});


/*
|--------------------------------------------------------------------------
| SUPERADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    'superadmin'
])->prefix('superadmin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | COMUNIDADES
    |--------------------------------------------------------------------------
    */

    Route::get('/comunidades', [ComunidadAdminController::class, 'index']);
    Route::post('/comunidades', [ComunidadAdminController::class, 'store']);
    Route::put('/comunidades/{id}', [ComunidadAdminController::class, 'update']);
    Route::delete('/comunidades/{id}', [ComunidadAdminController::class, 'destroy']);
    Route::get(
        '/comunidades/{id}',
        [ComunidadAdminController::class, 'show']
    );
    /*
    |--------------------------------------------------------------------------
    | VIVIENDAS
    |--------------------------------------------------------------------------
    */

    // VIVIENDAS
    Route::get(
        '/viviendas',
        [SuperAdminViviendaController::class, 'index']
    );

    Route::post(
        '/viviendas',
        [SuperAdminViviendaController::class, 'store']
    );

    Route::delete(
        '/viviendas/{id}',
        [SuperAdminViviendaController::class, 'destroy']
    );
    /*
|--------------------------------------------------------------------------
| USUARIOS
|--------------------------------------------------------------------------
*/

Route::get(
    '/usuarios',
    [SuperAdminUserController::class, 'index']
);

Route::post(
    '/usuarios',
    [SuperAdminUserController::class, 'store']
);

Route::put(
    '/usuarios/{id}/toggle-activo',
    [SuperAdminUserController::class, 'toggleActivo']
);

});