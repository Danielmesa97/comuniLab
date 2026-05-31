<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Comunidad;

class SuperAdminUserController extends Controller
{
    // LISTADO
    public function index()
    {
        return response()->json(
            User::with('vivienda', 'comunidades')
                ->orderBy('id', 'desc')
                ->get()
        );
    }

    // CREAR
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',

            'comunidad_id' => 'nullable|exists:comunidades,id',
            'vivienda_id' => 'nullable|exists:viviendas,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'vivienda_id' => $data['vivienda_id'] ?? null,
            'password' => null,
            'activo' => 1
        ]);

        // SOLO SI TIENE COMUNIDAD
        if (!empty($data['comunidad_id'])) {

            $user->comunidades()->attach(
                $data['comunidad_id'],
                [
                    'role' => $data['role']
                ]
            );

        }

        return response()->json($user, 201);
    }

    // ACTIVAR / DESACTIVAR
    public function toggleActivo($id)
    {
        $user = User::findOrFail($id);

        $user->activo = !$user->activo;

        $user->save();

        return response()->json($user);
    }
}