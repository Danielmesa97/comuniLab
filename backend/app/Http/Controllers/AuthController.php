<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // Para encriptar contraseñas

class AuthController extends Controller
{
    // 1. FUNCIÓN DE REGISTRO
    public function registro(Request $request)
    {
        // Validamos que nos envíen los datos correctos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users', // unique asegura que no haya 2 iguales
            'password' => 'required|string|min:6'
        ]);

        // Creamos el usuario en la base de datos
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
        ]);

        // Le fabricamos su Token VIP
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'mensaje' => 'Usuario registrado con éxito',
            'usuario' => $user,
            'token' => $token
        ], 201);
    }

    // 2. FUNCIÓN DE LOGIN
    public function login(Request $request)
    {
        // Validamos que nos pasen email y contraseña
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Buscamos al usuario en la base de datos
        $user = User::where('email', $request->email)->first();

        // Si no existe o la contraseña no coincide, le echamos
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'mensaje' => 'Credenciales incorrectas'
            ], 401); // 401 significa "No Autorizado"
        }

        // Si todo está bien, le fabricamos un Token VIP nuevo
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'mensaje' => '¡Hola de nuevo!',
            'usuario' => $user,
            'token' => $token
        ]);
    }
}