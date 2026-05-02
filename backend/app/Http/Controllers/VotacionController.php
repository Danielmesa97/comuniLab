<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Votacion; 
use App\Models\Voto; 

class VotacionController extends Controller
{

    public function store(Request $request)
    {
         // CONTROL DE ROLES
        if (!in_array($request->user()->role, ['presidente','admin','superadmin'])) {
            return response()->json([
                'error' => 'No autorizado'
            ], 403);
    }
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
        // Pedimos a Laravel que cuente los votos totales y solo los "si"
        $query = Votacion::query()
            ->withCount('votos') 
            ->withCount(['votos as votos_si_count' => function ($q) {
                $q->where('opcion', 'si'); 
            }]);

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
        $mis_votos = Voto::where('vivienda_id', $request->user()->vivienda_id)
            ->pluck('votacion_id');

        return response()->json([
            'votaciones' => $votaciones,
            'mis_votos'  => $mis_votos
        ]);
    }

    
    public function votar(Request $request)
    {
        // 1. Validamos los datos de entrada (Añadida la opción)
        $request->validate([
            'votacion_id' => 'required|exists:votaciones,id',
            'opcion'      => 'required|in:si,no' // Aseguramos que el voto sea estrictamente "si" o "no"
        ]);

        // 2. Buscamos la votación en la base de datos
        $votacion = Votacion::findOrFail($request->votacion_id);

        // 3. LA NUEVA BARRERA DE SEGURIDAD
        if ($votacion->fecha_limite && now()->greaterThan($votacion->fecha_limite)) {
            return response()->json([
                'message' => 'Lo sentimos, el plazo para esta votación ha finalizado.'
            ], 403); 
        }

        // comprobar si la vivienda ya votó
        $yaVoto = Voto::where('votacion_id', $votacion->id)
            ->where('vivienda_id', $request->user()->vivienda_id)
            ->exists();

        if ($yaVoto) {
            return response()->json([
                'message' => 'Esta vivienda ya ha votado en esta propuesta.'
            ], 403);
        }

        // crear voto por vivienda
        $voto = Voto::create([
            'votacion_id' => $votacion->id,
            'vivienda_id' => $request->user()->vivienda_id,
            'opcion'      => $request->opcion
        ]);

        // 6. Devolvemos una respuesta de éxito a Vue
        return response()->json([
            'message' => '¡Voto registrado correctamente!',
            'voto'    => $voto
        ], 201); // El código 201 significa "Creado exitosamente"
    }
}