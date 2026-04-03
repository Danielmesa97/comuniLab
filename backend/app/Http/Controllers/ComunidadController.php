<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comunidad;

class ComunidadController extends Controller
{
    public function index()
    {
        return Comunidad::all();
    }

    public function store(Request $request)
    {
        $comunidad = Comunidad::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        return response()->json($comunidad, 201);
    }
}