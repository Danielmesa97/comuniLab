<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Votacion; 
use App\Models\Voto; 

class VotacionController extends Controller
{

    public function store(Request $request)
    {
        // 1. Validamos los datos que nos envía Vue
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_limite' => 'nullable|date' 
        ]);

        // 2. Por defecto, una votación recién creada nace como 'activa'
        $validated['estado'] = 'activa';

        // 3. Guardamos en la base de datos (Recuerda que ya añadimos fecha_limite al $fillable)
        $votacion = Votacion::create($validated);

        // 4. Devolvemos la respuesta al frontend con código 201 (Created)
        return response()->json([
            'message' => 'Votación creada con éxito',
            'votacion' => $votacion
        ], 201);
    }
    
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

        // 1. Buscamos la votación
        $votacion = Votacion::findOrFail($request->votacion_id);

        // 2. Comprobamos la fecha límite
        // Usamos now() de Laravel que ya viene con la zona horaria configurada
        if ($votacion->fecha_limite && now()->gt($votacion->fecha_limite)) {
            return response()->json([
                'message' => 'Lo sentimos, el plazo para votar en esta encuesta ha finalizado.'
            ], 403); // 403 Forbidden: Entiendo quién eres, pero no tienes permiso para esto
        }

        // 3. Lógica que ya tenías para evitar duplicados y crear el voto...
        try {
            // ... (Tu código de creación de voto)
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ya has votado en esta encuesta'], 422);
        }
    }
}