<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;

class IncidenciaController extends Controller
{
    // 2. Creamos la función para listar las incidencias
    public function index()
    {
        // Traemos todas las incidencias de la base de datos
        $incidencias = Incidencia::all();

        // Las devolvemos convertidas en texto JSON
        return response()->json($incidencias);
    }

    public function store(Request $request)
{
    $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string'
    ]);

    $incidencia = new Incidencia();
    $incidencia->titulo = $request->titulo;
    $incidencia->descripcion = $request->descripcion;

    // 🔥 MUY IMPORTANTE
    $incidencia->estado = 'Pendiente';
    $incidencia->user_id = 1; // fijo por ahora

    $incidencia->save();

    return response()->json([
        'mensaje' => 'Incidencia creada',
        'data' => $incidencia
    ], 201);
}
}
