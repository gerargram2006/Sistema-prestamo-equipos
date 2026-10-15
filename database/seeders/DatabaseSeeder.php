<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder — seeder principal.
 *
 * Se ejecuta con `php artisan db:seed` o `php artisan migrate:fresh --seed`
 * y llama a los seeders de usuarios y de equipos, en ese orden.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            EquipoSeeder::class,
        ]);
    }
}
