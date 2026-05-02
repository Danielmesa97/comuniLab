<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // usuario no existe
        if (!$user) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        //  NUEVO → no tiene contraseña
        if (!$user->password) {
            return response()->json([
                'message' => 'Debes crear tu contraseña primero',
                'needs_password' => true,
                'email' => $user->email
            ], 403);
        }

        // contraseña incorrecta
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // LOGIN OK
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '¡Hola de nuevo!',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function checkUser(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => 'not_found'
        ]);
    }

    if (!$user->password) {
        return response()->json([
            'status' => 'needs_password',
            'email' => $user->email
        ]);
    }

    return response()->json([
        'status' => 'ok'
    ]);
}

    // 2. SET PASSWORD (NUEVO)
    public function setPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        // ya tiene contraseña
        if ($user->password) {
            return response()->json([
                'message' => 'Este usuario ya tiene contraseña'
            ], 400);
        }

        // guardar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Contraseña creada correctamente'
        ]);
    }

}