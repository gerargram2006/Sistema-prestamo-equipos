<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $fillable = ['codigo', 'nombre', 'categoria', 'estado', 'estudiante_id'];

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }
}
