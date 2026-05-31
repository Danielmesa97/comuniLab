<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $fillable = [
        'nombre',
        'email',
        'role',
        'vivienda_id',
        'comunidad_id',
        'estado'
    ];

    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class);
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class);
    }
}
