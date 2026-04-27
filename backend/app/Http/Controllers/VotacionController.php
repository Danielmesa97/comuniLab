<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Votacion; 
use App\Models\Voto; 

class VotacionController extends Controller
{
    public function index(Request $request)
{
    // 1. Obtenemos las votaciones activas
    $votaciones = Votacion::where('estado', 'activa')->get();

    // 2. Buscamos los IDs de las votaciones donde el usuario logueado ya ha participado
    $misVotosIds = [];
    if ($request->user()) {
        $misVotosIds = \App\Models\Voto::where('user_id', $request->user()->id)
            ->pluck('votacion_id'); // Esto devuelve solo un array de IDs, ej: [1, 5]
    }

    return response()->json([
        'votaciones' => $votaciones,
        'mis_votos' => $misVotosIds
    ]);
}

    
    public function votar(Request $request)
{
    $request->validate([
        'votacion_id' => 'required|exists:votaciones,id',
        'opcion' => 'required|in:si,no',
    ]);

    try {
        Voto::create([
            'user_id' => $request->user()->id,
            'votacion_id' => $request->votacion_id,
            'opcion' => $request->opcion,
        ]);
        return response()->json(['message' => 'Voto registrado con éxito'], 201);
    } catch (\Exception $e) {
        // CAMBIA ESTA LÍNEA para ver el error real en el alert de Vue
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
}