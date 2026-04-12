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
}
