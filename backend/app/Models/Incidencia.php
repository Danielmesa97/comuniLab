<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'foto',
        'estado',
        'user_id',
        'comunidad_id',
    ];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIÓN CON USUARIO
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}