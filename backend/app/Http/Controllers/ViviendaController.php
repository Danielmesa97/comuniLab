<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vivienda;

class ViviendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Vivienda::query();

        if ($request->has('comunidad_id')) {
            $query->where('comunidad_id', $request->comunidad_id);
        }

        return $query->get();
    }
}