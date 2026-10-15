<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Equipo — representa la tabla "equipos".
 *
 * - $fillable: campos que se pueden asignar de forma masiva (protege contra
 *   el "mass assignment").
 * - estudiante(): relación "pertenece a" un usuario (el estudiante que lo tiene
 *   prestado). Si estudiante_id es null, el equipo está libre.
 */
class Equipo extends Model
{
    protected $fillable = ['codigo', 'nombre', 'categoria', 'estado', 'estudiante_id'];

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }
}
