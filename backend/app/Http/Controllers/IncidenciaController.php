<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;

class IncidenciaController extends Controller
{
    // 🔹 LISTAR INCIDENCIAS
    public function index(Request $request)
    {
        $comunidadId = $request->user()->vivienda->comunidad_id;

        $query = Incidencia::where(
            'comunidad_id',
            $comunidadId
);

        // 🔥 OPCIONAL: solo las del usuario
        // $query->where('user_id', $request->user()->id);

        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
    }

    // 🔹 CREAR INCIDENCIA
    public function store(Request $request)
    {
        // Validación
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string'
        ]);

        // DATOS AUTOMÁTICOS
        $data['estado'] = 'pendiente';
        $data['user_id'] = $request->user()->id;
        $data['comunidad_id'] = $request->user()->vivienda->comunidad_id;

        $incidencia = Incidencia::create($data);

        return response()->json($incidencia, 201);
    }
    // 🔹 ACTUALIZAR ESTADO
public function update(Request $request, $id)
{
    $incidencia = Incidencia::findOrFail($id);

    // 🔐 PERMISOS
    if (
        $request->user()->id !== $incidencia->user_id &&
        !in_array($request->user()->role, ['admin','presidente','superadmin'])
    ) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // 🔥 VALIDAR ESTADO
    $request->validate([
        'estado' => 'required|in:pendiente,en_proceso,resuelto'
    ]);

    $incidencia->estado = $request->estado;
    $incidencia->save();

    return response()->json($incidencia);
}
}