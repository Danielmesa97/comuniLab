<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vivienda; // Asegúrate de importar el modelo

class ViviendaController extends Controller
{
    public function index()
    {
        // Extraemos todas las viviendas de la base de datos
        $viviendas = Vivienda::all();

        // Las devolvemos en formato JSON para que Vue las entienda
        return response()->json([
            'viviendas' => $viviendas
        ]);
    }
}