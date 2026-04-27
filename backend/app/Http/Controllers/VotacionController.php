<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Votacion; 

class VotacionController extends Controller
{
    public function index()
    {
        
        return response()->json(Votacion::where('estado', 'activa')->get());
    }
}