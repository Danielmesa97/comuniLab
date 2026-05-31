<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    protected $fillable = [
    'user_id',
    'votacion_id',
    'vivienda_id',
    'vivienda_delegada_id',
    'opcion',
];

    public function vivienda()
{
    // Relación con el dueño original del voto
    return $this->belongsTo(Vivienda::class, 'vivienda_id');
}

    public function votacion()
    {
        return $this->belongsTo(Votacion::class);
    }
}