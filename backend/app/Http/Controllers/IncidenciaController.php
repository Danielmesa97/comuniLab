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
        // 1. Añadimos el user_id a las reglas de validación
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'user_id' => 'required|integer' // De momento lo pedimos por Postman
        ]);

        $incidencia = new Incidencia();
        $incidencia->titulo = $request->titulo;
        $incidencia->descripcion = $request->descripcion;
        
        // 2. Asignamos un estado por defecto al crearla
        $incidencia->estado = 'Pendiente'; 
        
        // 3. Le asignamos el ID del usuario que nos llega en la petición
        $incidencia->user_id = $request->user_id; 
        
        $incidencia->save();

        return response()->json([
            'mensaje' => 'Incidencia creada con éxito',
            'data' => $incidencia
        ], 201);
    }
}
