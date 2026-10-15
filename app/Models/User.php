<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo User — representa la tabla "users".
 *
 * - #[Fillable]: campos asignables de forma masiva (incluye 'rol').
 * - #[Hidden]: oculta 'password' y 'remember_token' al serializar.
 * - equiposPrestados(): relación "tiene muchos" equipos (los prestados).
 * - casts(): convierte 'password' a hash automáticamente.
 */
#[Fillable(['name', 'email', 'password', 'rol'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relación: equipos que este usuario tiene prestados.
     */
    public function equiposPrestados()
    {
        return $this->hasMany(Equipo::class, 'estudiante_id');
    }

    /**
     * Conversiones de atributos (incluye el hash automático de la contraseña).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
