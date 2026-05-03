<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anuncio extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'fecha_inicio',
        'fecha_fin'
    ];
}
