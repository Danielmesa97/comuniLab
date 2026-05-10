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
            'direccion' => 'required|string|max:255'
        ]);

        $comunidad = Comunidad::create($data);

        return response()->json([
            'message' => 'Comunidad creada',
            'comunidad' => $comunidad
        ], 201);
    }
}