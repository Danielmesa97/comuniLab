<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instalacion extends Model
{
    use HasFactory;

    // Forzamos el nombre de la tabla en español para que Laravel no busque "instalacions"
    protected $table = 'instalaciones'; 

    protected $fillable = [
        'comunidad_id',
        'nombre',
        'descripcion',
        'duracion_franja',
        'aforo_max',
        'icono'
    ];

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}