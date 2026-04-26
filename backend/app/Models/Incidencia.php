<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    use HasFactory;

    // Estos son los únicos campos que permitiremos rellenar cuando alguien envíe el formulario
    protected $fillable = [
        'titulo',
        'descripcion',
        'foto',
        'estado',
        'user_id',
        // 'comunidad_id', 
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}