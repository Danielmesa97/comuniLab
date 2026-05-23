<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vivienda;

class ViviendaController extends Controller
{
    // LISTADO
    public function index(Request $request)
    {
        $query = Vivienda::with('users');

        // FILTRO POR COMUNIDAD
        if ($request->has('comunidad_id')) {
            $query->where(
                'comunidad_id',
                $request->comunidad_id
            );
        }

        return response()->json(
            $query
                ->orderBy('nombre')
                ->get()
        );
    }

    // CREAR
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // comunidad automática
        $data['comunidad_id'] =
            $user->comunidades
                ->first()
                ->id;

        $vivienda = Vivienda::create($data);

        return response()->json(
            $vivienda,
            201
        );
    }

    // EDITAR
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $vivienda = Vivienda::where(
            'comunidad_id',
            $user->comunidades
                ->first()
                ->id
        )->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $vivienda->update($data);

        return response()->json($vivienda);
    }

    // ELIMINAR
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $vivienda = Vivienda::where(
            'comunidad_id',
            $user->comunidades
                ->first()
                ->id
        )->findOrFail($id);

        $vivienda->delete();

        return response()->json([
            'message' => 'Vivienda eliminada'
        ]);
    }
}