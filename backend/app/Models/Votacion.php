<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Votacion extends Model
{
    use HasFactory;

    
    protected $table = 'votaciones'; 

    protected $fillable = ['titulo', 'descripcion', 'estado', 'fecha_limite'];

    //contar votos
    public function votos()
    {
        // Una votación "tiene muchos" votos
        return $this->hasMany(Voto::class);
    }
    public function viviendas()
    {
    return $this->hasManyThrough(
        Vivienda::class,
        Voto::class,
        'votacion_id',   // FK en votos
        'id',            // PK en viviendas
        'id',            // PK en votaciones
        'vivienda_id'    // FK en votos hacia viviendas
    );
    }
}