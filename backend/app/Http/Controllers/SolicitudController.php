<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SolicitudController extends Controller
{
    // 🟢 CREAR SOLICITUD (registro)
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required',
            'email' => 'required|email',
            'role' => 'required|in:inquilino,propietario,presidente',
            'vivienda_id' => 'required|exists:viviendas,id',
            'comunidad_id' => 'required|exists:comunidades,id'
        ]);

        $solicitud = Solicitud::create($data);

        return response()->json([
            'message' => 'Solicitud enviada correctamente',
            'solicitud' => $solicitud
        ]);
    }

    // VER SOLICITUDES (admin/presidente)
    public function index(Request $request)
    {
        if (!in_array($request->user()->role, ['admin','presidente','superadmin'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return Solicitud::with('vivienda')
            ->where('estado', 'pendiente')
            ->get();
    }

    // ACEPTAR SOLICITUD
    public function aceptar($id, Request $request)
    {
        if (!in_array($request->user()->role, ['admin','presidente','superadmin'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $solicitud = Solicitud::findOrFail($id);

        $user = User::create([
            'name' => $solicitud->nombre,
            'email' => $solicitud->email,
            'password' => null, 
            'role' => $solicitud->role,
            'vivienda_id' => $solicitud->vivienda_id
        ]);

        $solicitud->estado = 'aceptada';
        $solicitud->save();
        $user->activo = true;
        $user->save();

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user' => $user
        ]);
    }

    // RECHAZAR SOLICITUD
    public function rechazar($id, Request $request)
    {
        if (!in_array($request->user()->role, ['admin','presidente','superadmin'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $solicitud = Solicitud::findOrFail($id);

        $solicitud->estado = 'rechazada';
        $solicitud->save();

        return response()->json([
            'message' => 'Solicitud rechazada'
        ]);
    }
}