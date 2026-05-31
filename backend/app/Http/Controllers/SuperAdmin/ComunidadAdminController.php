<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comunidad;


class ComunidadAdminController extends Controller
{
    // LISTAR
    public function index()
    {
        return response()->json(
            Comunidad::orderBy('id', 'desc')->get()
        );
    }

    // CREAR
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255'
        ]);

        $comunidad = Comunidad::create($data);

        return response()->json([
            'message' => 'Comunidad creada',
            'comunidad' => $comunidad
        ], 201);
    }

    // EDITAR
    public function update(Request $request, $id)
    {
        $comunidad = Comunidad::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255'
        ]);

        $comunidad->update($data);

        return response()->json([
            'message' => 'Comunidad actualizada',
            'comunidad' => $comunidad
        ]);
    }
        public function show($id)
{
    $comunidad = Comunidad::findOrFail($id);

    return response()->json([

        'comunidad' => $comunidad,

        'stats' => [

            'viviendas' =>
                $comunidad->viviendas()->count(),

            'usuarios' =>
                \App\Models\User::whereHas(
                    'vivienda',
                    function($q) use ($id) {

                        $q->where(
                            'comunidad_id',
                            $id
                        );

                    }
                )->count(),

            'incidencias' =>
                \App\Models\Incidencia::where(
                    'comunidad_id',
                    $id
                )->count(),

            'votaciones' =>
                \App\Models\Votacion::where(
                    'comunidad_id',
                    $id
                )->count()

        ]

    ]);
}


    // DESACTIVAR
    public function destroy($id)
    {
        $comunidad = Comunidad::findOrFail($id);

        $comunidad->update([
            'activa' => false
        ]);

        return response()->json([
            'message' => 'Comunidad desactivada'
        ]);
    }
}