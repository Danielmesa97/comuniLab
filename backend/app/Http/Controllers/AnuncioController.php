<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anuncio; // 🔥 IMPORTANTE

class AnuncioController extends Controller
{
    // 📢 LISTAR ANUNCIOS
    public function index(Request $request)
    {
        $comunidadId = $request->user()->vivienda->comunidad_id;

        $query = Anuncio::where(
            'comunidad_id',
            $comunidadId
        );

        $user = $request->user();

        // 👥 USUARIOS NORMALES → solo anuncios activos
        if (!$user || !in_array($user->role, ['admin','presidente'])) {
            $today = now();

            $query->where('fecha_inicio', '<=', $today)
                  ->where('fecha_fin', '>=', $today);
        }

        // 🔍 FILTRO ADMIN POR FECHAS
        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->whereBetween('fecha_inicio', [
                $request->desde,
                $request->hasta
            ]);
        }

        $anuncios = $query->orderBy('fecha_inicio', 'desc')->get();

        return response()->json($anuncios);
    }

    // 📝 CREAR ANUNCIO
    public function store(Request $request)
    {
        $user = $request->user();

        // 🔒 SOLO ADMIN / PRESIDENTE
        if (!$user || !in_array($user->role, ['admin','presidente'])) {
            return response()->json([
                'error' => 'No autorizado'
            ], 403);
        }

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo' => 'required|in:noticia,evento,aviso,documento',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $data['comunidad_id'] = $request->user()->vivienda->comunidad_id;

        $anuncio = Anuncio::create($data);

        return response()->json([
            'message' => 'Anuncio creado correctamente',
            'anuncio' => $anuncio
        ], 201);
    }
}