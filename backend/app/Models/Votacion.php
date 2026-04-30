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
}