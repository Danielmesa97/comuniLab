<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'instalacion_id',
        'user_id',
        'fecha',
        'franja_id'
    ];

    public function instalacion()
    {
        return $this->belongsTo(Instalacion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}