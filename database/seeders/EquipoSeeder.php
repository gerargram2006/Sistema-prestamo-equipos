<?php

namespace Database\Seeders;

use App\Models\Equipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipos = [
            ['LAP-001', 'Laptop Dell Latitude', 'Laptop'],
            ['LAP-002', 'Laptop HP ProBook', 'Laptop'],
            ['PRO-001', 'Proyector Epson X41', 'Proyector'],
            ['PRO-002', 'Proyector BenQ MS550', 'Proyector'],
            ['ARD-001', 'Kit Arduino UNO', 'Electrónica'],
            ['ARD-002', 'Kit Arduino Mega', 'Electrónica'],
            ['MUL-001', 'Multímetro Fluke 117', 'Medición'],
            ['MUL-002', 'Multímetro Uni-T UT61E', 'Medición'],
        ];

        foreach ($equipos as [$codigo, $nombre, $categoria]) {
            Equipo::create(['codigo' => $codigo, 'nombre' => $nombre, 'categoria' => $categoria, 'estado' => 'Disponible']);
        }
    }
}
