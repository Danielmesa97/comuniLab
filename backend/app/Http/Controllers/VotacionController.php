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

        $validated['comunidad_id'] = $request->user()->vivienda->comunidad_id;

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
        $user = $request->user();
        $viviendaId = $user->vivienda_id;
        $comunidadId = $user->vivienda->comunidad_id;

        // --- 1. CONSULTA DE VOTACIONES (Tu lógica original) ---
        $query = Votacion::where('comunidad_id', $comunidadId)
            ->withCount('votos')
            ->withCount(['votos as votos_si_count' => function ($q) {
                $q->where('opcion', 'si'); 
            }]);

        // Búsqueda
        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            $query->where(function($q) use ($termino) {
                $q->where('titulo', 'like', '%' . $termino . '%')
                ->orWhere('descripcion', 'like', '%' . $termino . '%');
            });
        } else {
            $haceUnMes = now()->subMonth();
            $query->where(function ($q) use ($haceUnMes) {
                $q->where('fecha_limite', '>=', $haceUnMes)
                ->orWhereNull('fecha_limite');
            });
        }

        $votaciones = $query->orderBy('created_at', 'desc')->get();

        // --- 2. TUS VOTOS PROPIOS ---
        $mis_votos = Voto::where('vivienda_id', $viviendaId)
            ->pluck('votacion_id');

        // --- 3. NUEVO: DELEGACIONES QUE HAS RECIBIDO ---
        // Buscamos en la tabla votos aquellos donde tú eres el delegado
        // y la opción aún es NULL (porque no has votado por ellos todavía)
        $delegaciones_pendientes = Voto::where('vivienda_delegada_id', $viviendaId)
            ->whereNull('opcion')
            ->with('vivienda') // Cargamos la relación para que Vue sepa QUÉ PISO nos delegó
            ->get();

        return response()->json([
            'votaciones' => $votaciones,
            'mis_votos'  => $mis_votos,
            'delegaciones_pendientes' => $delegaciones_pendientes
        ], 200);
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
            'user_id'     => $request->user()->id,
            'opcion'      => $request->opcion
        ]);

        // 6. Devolvemos una respuesta de éxito a Vue
        return response()->json([
            'message' => '¡Voto registrado correctamente!',
            'voto'    => $voto
        ], 201); // El código 201 significa "Creado exitosamente"
    }
    public function delegar(Request $request)
    {
        $request->validate([
            'votacion_id' => 'required|exists:votaciones,id',
            'vivienda_delegada_id' => 'required|exists:viviendas,id',
        ]);

        $user = $request->user();

        // 1. Verificamos que el usuario no haya votado ya
        $votoExistente = Voto::where('votacion_id', $request->votacion_id)
                            ->where('vivienda_id', $user->vivienda_id)
                            ->first();

        if ($votoExistente) {
            return response()->json(['message' => 'Ya has participado en esta votación'], 400);
        }

        // 2. Creamos el registro de delegación
        Voto::create([
            'votacion_id' => $request->votacion_id,
            'vivienda_id' => $user->vivienda_id, // El dueño del voto
            'user_id' => $user->id,
            'vivienda_delegada_id' => $request->vivienda_delegada_id, // El representante
            'opcion' => null, // Queda vacío hasta que el representante vote
        ]);

        return response()->json(['message' => 'Voto delegado correctamente'], 201);
    }

    public function ejecutarDelegado(Request $request)
    {
        // 1. Validamos los datos (Fíjate que ahora pedimos el 'voto_id')
        $request->validate([
            'votacion_id' => 'required|exists:votaciones,id',
            'voto_id'     => 'required|exists:votos,id',
            'opcion'      => 'required|in:si,no'
        ]);

        $user = $request->user();

        // 2. Buscamos ese voto con un triple candado de seguridad:
        // - Que pertenezca a la votación correcta
        // - Que tu vivienda sea exactamente la delegada
        // - Que la opción siga en 'null' (que no se haya votado ya)
        $votoDelegado = Voto::where('id', $request->voto_id)
                            ->where('votacion_id', $request->votacion_id)
                            ->where('vivienda_delegada_id', $user->vivienda_id)
                            ->first();

        if (!$votoDelegado) {
            return response()->json([
                'message' => 'No se encontró la delegación o no tienes permisos para ejecutarla.'
            ], 403);
        }

        if ($votoDelegado->opcion !== null) {
            return response()->json([
                'message' => 'Este voto delegado ya ha sido utilizado.'
            ], 400);
        }

        // 3. Comprobamos la fecha de caducidad (Igual que en un voto normal)
        $votacion = Votacion::find($request->votacion_id);
        if ($votacion->fecha_limite && now()->greaterThan($votacion->fecha_limite)) {
            return response()->json([
                'message' => 'Lo sentimos, el plazo para esta votación ha finalizado.'
            ], 403); 
        }

        // 4. ¡LA MAGIA! Rellenamos el voto en blanco con la opción elegida
        $votoDelegado->opcion = $request->opcion;
        $votoDelegado->save();

        // 5. Devolvemos la respuesta de éxito
        return response()->json([
            'message' => 'Voto del vecino registrado correctamente',
            'voto' => $votoDelegado
        ], 200);
    }
}