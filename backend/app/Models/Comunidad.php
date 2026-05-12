<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunidad extends Model
{
    protected $table = 'comunidades';
    protected $fillable = [
    'nombre',
    'descripcion',
    'activa',
];
public function viviendas()
{
    return $this->hasMany(Vivienda::class);
}
}

