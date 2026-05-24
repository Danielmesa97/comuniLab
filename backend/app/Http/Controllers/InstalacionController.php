<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instalacion;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class InstalacionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTAR INSTALACIONES
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        try {

            $user = $request->user();

            /*
            |--------------------------------------------------------------------------
            | COMUNIDAD ACTIVA
            |--------------------------------------------------------------------------
            */

            $comunidadId =
                $request->server('HTTP_X_COMUNIDAD_ID');

            /*
            |--------------------------------------------------------------------------
            | SI NO VIENE HEADER
            |--------------------------------------------------------------------------
            */

            if (!$comunidadId) {

                // ADMIN / PRESIDENTE / SUPERADMIN
                if (
                    in_array(
                        $user->role,
                        ['admin', 'presidente', 'superadmin']
                    )
                ) {

                    $comunidadId =
                        $user->comunidades()
                            ->first()?->id;

                }

                // PROPIETARIO / INQUILINO
                else {

                    $comunidadId =
                        $user->vivienda?->comunidad_id;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | INSTALACIONES
            |--------------------------------------------------------------------------
            */

            $instalaciones = Instalacion::where(
                'comunidad_id',
                $comunidadId
            )->get();

            return response()->json(
                $instalaciones,
                200
            );

        } catch (\Exception $e) {

            return response()->json([
                'error_real' => $e->getMessage(),
                'linea_del_error' => $e->getLine(),
                'archivo_del_error' => $e->getFile()
            ], 500);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVAS DE INSTALACIÓN
    |--------------------------------------------------------------------------
    */

    public function getReservas($id)
    {
        $reservas = Reserva::where(
            'instalacion_id',
            $id
        )->get();

        return response()->json(
            $reservas,
            200
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR RESERVA
    |--------------------------------------------------------------------------
    */

    public function storeReserva(Request $request)
    {
        $request->validate([

            'instalacion_id' =>
                'required|exists:instalaciones,id',

            'fecha' =>
                'required|date',

            'franja_id' =>
                'required|integer',

        ]);

        /*
        |--------------------------------------------------------------------------
        | COMPROBAR SI YA EXISTE
        |--------------------------------------------------------------------------
        */

        $existe = Reserva::where(
            'instalacion_id',
            $request->instalacion_id
        )
        ->where(
            'fecha',
            $request->fecha
        )
        ->where(
            'franja_id',
            $request->franja_id
        )
        ->exists();

        if ($existe) {

            return response()->json([
                'message' =>
                    'Esta franja ya ha sido reservada'
            ], 422);

        }

        /*
        |--------------------------------------------------------------------------
        | CREAR RESERVA
        |--------------------------------------------------------------------------
        */

        $reserva = Reserva::create([

            'instalacion_id' =>
                $request->instalacion_id,

            'user_id' =>
                Auth::id(),

            'fecha' =>
                $request->fecha,

            'franja_id' =>
                $request->franja_id,

        ]);

        return response()->json([

            'message' =>
                'Reserva confirmada con éxito.',

            'reserva' =>
                $reserva

        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR INSTALACIÓN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([

            'nombre' =>
                'required|string|max:255',

            'descripcion' =>
                'nullable|string',

            'duracion_franja' =>
                'nullable|integer',

            'aforo_max' =>
                'nullable|integer',

            'icono' =>
                'nullable|string',

        ]);

        /*
        |--------------------------------------------------------------------------
        | COMUNIDAD ACTIVA
        |--------------------------------------------------------------------------
        */

        $data['comunidad_id'] =
            $request->server('HTTP_X_COMUNIDAD_ID')
            ??
            $user->comunidades()
                ->first()?->id;

        /*
        |--------------------------------------------------------------------------
        | CREAR
        |--------------------------------------------------------------------------
        */

        $instalacion = Instalacion::create($data);

        return response()->json(
            $instalacion,
            201
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $comunidadId =
            $request->server('HTTP_X_COMUNIDAD_ID')
            ??
            $user->comunidades()
                ->first()?->id;

        $instalacion = Instalacion::where(
            'comunidad_id',
            $comunidadId
        )->findOrFail($id);

        $instalacion->delete();

        return response()->json([
            'message' => 'Instalación eliminada'
        ]);
    }
}