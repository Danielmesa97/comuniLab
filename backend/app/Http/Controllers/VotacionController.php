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
        $query = Votacion::query();

        // 1. Usamos 'filled' que es la forma nativa y segura de Laravel
        if ($request->filled('buscar')) {
            
            $termino = $request->input('buscar');
            
            // Agrupamos la búsqueda para que el OR no rompa otras consultas SQL
            $query->where(function($q) use ($termino) {
                $q->where('titulo', 'like', '%' . $termino . '%')
                  ->orWhere('descripcion', 'like', '%' . $termino . '%');
            });
            
        } else {
            // 2. Si no hay búsqueda, mostramos las recientes o sin caducidad
            $haceUnMes = now()->subMonth();

            $query->where(function ($q) use ($haceUnMes) {
                $q->where('fecha_limite', '>=', $haceUnMes)
                  ->orWhereNull('fecha_limite');
            });
        }

        // Ejecutamos la consulta
        $votaciones = $query->orderBy('created_at', 'desc')->get();

        // Recuperamos los votos
        $mis_votos = $request->user()->votos()->pluck('votacion_id');

        return response()->json([
            'votaciones' => $votaciones,
            'mis_votos'  => $mis_votos
        ]);
    }

    
    public function votar(Request $request)
    {
        // 1. Validamos los datos de entrada
        $request->validate([
            'votacion_id' => 'required|exists:votaciones,id',
            // ... (otras validaciones que tengas como la opción elegida)
        ]);

        // 2. Buscamos la votación en la base de datos
        $votacion = Votacion::findOrFail($request->votacion_id);

        // 👇 3. LA NUEVA BARRERA DE SEGURIDAD 👇
        // Si la votación tiene fecha límite, y la fecha actual es mayor que esa límite...
        if ($votacion->fecha_limite && now()->greaterThan($votacion->fecha_limite)) {
            return response()->json([
                'message' => 'Lo sentimos, el plazo para esta votación ha finalizado.'
            ], 403); // 403 significa "Prohibido"
        }

        // 4. Comprobamos si el usuario ya había votado antes
        $yaVoto = $request->user()->votos()->where('votacion_id', $votacion->id)->exists();
        if ($yaVoto) {
            return response()->json(['message' => 'Ya has votado en esta propuesta.'], 403);
        }

        // ... (Aquí sigue tu código normal para guardar el voto) ...
    }
}