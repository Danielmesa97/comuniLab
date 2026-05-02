<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vivienda extends Model
{
    protected $fillable = [
        'nombre',
        'comunidad_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class);
    }
}
