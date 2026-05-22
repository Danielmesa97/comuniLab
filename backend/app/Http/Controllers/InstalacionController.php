<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instalacion;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class InstalacionController extends Controller
{
    // 1. Listar todas las instalaciones de la comunidad del usuario autenticado
    public function index()
    {
        $user = Auth::user();
        
        // Buscamos solo las instalaciones que pertenezcan a la comunidad del vecino
        $instalaciones = Instalacion::where('comunidad_id', $user->comunidad_id)->get();
        
        return response()->json($instalaciones, 200);
    }

    // 2. Obtener las reservas de una instalación específica para pintarlas en el calendario
    public function getReservas($id)
    {
        // Traemos las reservas de esa instalación (puedes filtrar opcionalmente por fechas recientes si quieres)
        $reservas = Reserva::where('instalacion_id', $id)->get();
        
        return response()->json($reservas, 200);
    }

    // 3. Crear una nueva reserva (Guardar en la base de datos)
    public function storeReserva(Request $request)
    {
        $request->validate([
            'instalacion_id' => 'required|exists:instalaciones,id',
            'fecha' => 'required|date',
            'franja_id' => 'required|integer',
        ]);

        // Verificación de seguridad: Comprobar si esa celda exacta ya está reservada
        $existe = Reserva::where('instalacion_id', $request->instalacion_id)
            ->where('fecha', $request->fecha)
            ->where('franja_id', $request->franja_id)
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'Esta franja ya ha sido reservada por otro vecino.'], 422);
        }

        // Si está libre, creamos la reserva asociándola al usuario que hace la petición
        $reserva = Reserva::create([
            'instalacion_id' => $request->instalacion_id,
            'user_id' => Auth::id(),
            'fecha' => $request->fecha,
            'franja_id' => $request->franja_id,
        ]);

        return response()->json([
            'message' => 'Reserva confirmada con éxito.',
            'reserva' => $reserva
        ], 201);
    }
}