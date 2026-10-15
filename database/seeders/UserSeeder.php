<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * UserSeeder — carga los usuarios de prueba.
 *
 * Crea:
 *  - 1 encargado:    encargado@tecsup.edu.pe    / encargado123
 *  - 2 estudiantes:  estudiante1@ y estudiante2@tecsup.edu.pe / estudiante123
 *
 * La contraseña se guarda cifrada automáticamente gracias al cast 'hashed'
 * del modelo User.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['name' => 'Encargado Lab', 'email' => 'encargado@tecsup.edu.pe', 'password' => 'encargado123', 'rol' => 'encargado']);
        User::create(['name' => 'Estudiante Uno', 'email' => 'estudiante1@tecsup.edu.pe', 'password' => 'estudiante123', 'rol' => 'estudiante']);
        User::create(['name' => 'Estudiante Dos', 'email' => 'estudiante2@tecsup.edu.pe', 'password' => 'estudiante123', 'rol' => 'estudiante']);
    }
}
