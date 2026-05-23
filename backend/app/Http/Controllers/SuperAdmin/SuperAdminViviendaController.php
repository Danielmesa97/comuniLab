<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Vivienda;

class SuperAdminViviendaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return response()->json(

            Vivienda::with(
                'comunidad',
                'users'
            )
            ->orderBy('id', 'desc')
            ->get()

        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'nombre' =>
                'required|string|max:255',

            'comunidad_id' =>
                'required|exists:comunidades,id',

        ]);

        $vivienda = Vivienda::create([

            'nombre' =>
                $data['nombre'],

            'comunidad_id' =>
                $data['comunidad_id']

        ]);

        return response()->json(
            $vivienda,
            201
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $vivienda = Vivienda::findOrFail($id);

        $vivienda->delete();

        return response()->json([
            'message' => 'Vivienda eliminada'
        ]);
    }
}