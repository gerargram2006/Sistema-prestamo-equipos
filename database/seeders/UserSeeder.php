<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
