<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Comunidad;

class ComunidadAdminController extends Controller
{
    public function index()
    {
        return response()->json(
            Comunidad::orderBy('id', 'desc')->get()
        );
    }
}