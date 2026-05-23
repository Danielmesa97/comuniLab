<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;

class IncidenciaController extends Controller
{
    // 🔹 LISTAR INCIDENCIAS
    public function index(Request $request)
    {

        // 🔥 SUPERADMIN ENTRANDO A COMUNIDAD

        if ($request->user()->role === 'superadmin') {

            $comunidadId = $request->header(
                'X-Comunidad-Id'
            );

        }

        // 🔹 USUARIO NORMAL

        else {

            $comunidadId =
                $request->user()
                ->vivienda
                ->comunidad_id;

        }

        $query = Incidencia::where(
            'comunidad_id',
            $comunidadId
        );

        return response()->json(
            $query
                ->orderBy('created_at', 'desc')
                ->get()
        );

    }

    // 🔹 CREAR INCIDENCIA
    public function store(Request $request)
    {

        $data = $request->validate([

            'titulo' =>
                'required|string|max:255',

            'descripcion' =>
                'required|string'

        ]);

        // 🔥 SUPERADMIN EN MODO COMUNIDAD

        if ($request->user()->role === 'superadmin') {

            $comunidadId = $request->header(
                'X-Comunidad-Id'
            );

        }

        // 🔹 USUARIO NORMAL

        else {

            $comunidadId =
                $request->user()
                ->vivienda
                ->comunidad_id;

        }

        $data['estado'] = 'pendiente';

        $data['user_id'] =
            $request->user()->id;

        $data['comunidad_id'] =
            $comunidadId;

        $incidencia =
            Incidencia::create($data);

        return response()->json(
            $incidencia,
            201
        );

    }

    // 🔹 ACTUALIZAR ESTADO
    public function update(
        Request $request,
        $id
    )
    {

        $incidencia =
            Incidencia::findOrFail($id);

        // 🔐 PERMISOS

        if (

            $request->user()->id !==
            $incidencia->user_id

            &&

            !in_array(
                $request->user()->role,
                [
                    'admin',
                    'presidente',
                    'superadmin'
                ]
            )

        ) {

            return response()->json([
                'error' => 'No autorizado'
            ], 403);

        }

        $request->validate([

            'estado' =>
                'required|in:pendiente,en_proceso,resuelto'

        ]);

        $incidencia->estado =
            $request->estado;

        $incidencia->save();

        return response()->json(
            $incidencia
        );

    }
}