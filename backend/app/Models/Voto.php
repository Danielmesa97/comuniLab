<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    protected $fillable = [
        'votacion_id',
        'vivienda_id',
        'user_id',
        'opcion'
    ];

    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class);
    }

    public function votacion()
    {
        return $this->belongsTo(Votacion::class);
    }
}