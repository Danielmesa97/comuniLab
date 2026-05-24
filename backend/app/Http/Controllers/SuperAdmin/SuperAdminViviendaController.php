<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Vivienda;

class SuperAdminViviendaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN → VE TODO
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'superadmin') {

            $viviendas = Vivienda::with(
                'comunidad',
                'users'
            )
            ->orderBy('id', 'desc')
            ->get();

        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / PRESIDENTE
        |--------------------------------------------------------------------------
        */

        else {

            $comunidadIds =
                $user->comunidades
                    ->pluck('id');

            $viviendas = Vivienda::with(
                'comunidad',
                'users'
            )
            ->whereIn(
                'comunidad_id',
                $comunidadIds
            )
            ->orderBy('id', 'desc')
            ->get();

        }

        return response()->json(
            $viviendas
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([

            'nombre' =>
                'required|string|max:255',

            'comunidad_id' =>
                'required|exists:comunidades,id',

        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR COMUNIDAD DEL ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role !== 'superadmin') {

            $permitido =
                $user->comunidades()
                    ->where(
                        'comunidad_id',
                        $data['comunidad_id']
                    )
                    ->exists();

            if (!$permitido) {

                return response()->json([
                    'message' => 'No autorizado'
                ], 403);
            }
        }

        $vivienda = Vivienda::create([

            'nombre' =>
                $data['nombre'],

            'comunidad_id' =>
                $data['comunidad_id']

        ]);

        return response()->json(
            $vivienda,
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

        $vivienda = Vivienda::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role !== 'superadmin') {

            $permitido =
                $user->comunidades()
                    ->where(
                        'comunidad_id',
                        $vivienda->comunidad_id
                    )
                    ->exists();

            if (!$permitido) {

                return response()->json([
                    'message' => 'No autorizado'
                ], 403);
            }
        }

        $vivienda->delete();

        return response()->json([
            'message' => 'Vivienda eliminada'
        ]);
    }
}