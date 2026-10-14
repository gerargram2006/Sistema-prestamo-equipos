<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_encargado_puede_ver_el_formulario_de_creacion(): void
    {
        $encargado = User::factory()->create(['rol' => 'encargado']);

        $response = $this->actingAs($encargado)->get(route('equipos.create'));

        $response->assertOk();
        $response->assertSee('Registrar nuevo equipo');
    }

    public function test_encargado_puede_registrar_un_equipo_disponible(): void
    {
        $encargado = User::factory()->create(['rol' => 'encargado']);

        $response = $this->actingAs($encargado)->post(route('equipos.store'), [
            'codigo' => 'LAP-099',
            'nombre' => 'Laptop de prueba',
            'categoria' => 'Laptop',
            'estado' => 'Disponible',
        ]);

        $response->assertRedirect(route('encargado.dashboard'));
        $this->assertDatabaseHas('equipos', [
            'codigo' => 'LAP-099',
            'estado' => 'Disponible',
        ]);
    }

    public function test_encargado_puede_registrar_un_equipo_prestado(): void
    {
        $encargado = User::factory()->create(['rol' => 'encargado']);

        $response = $this->actingAs($encargado)->post(route('equipos.store'), [
            'codigo' => 'LAP-098',
            'nombre' => 'Laptop prestada',
            'categoria' => 'Laptop',
            'estado' => 'Prestado',
        ]);

        $response->assertRedirect(route('encargado.dashboard'));
        $this->assertDatabaseHas('equipos', [
            'codigo' => 'LAP-098',
            'estado' => 'Prestado',
        ]);
    }

    public function test_no_se_puede_registrar_un_codigo_duplicado(): void
    {
        $encargado = User::factory()->create(['rol' => 'encargado']);
        Equipo::create([
            'codigo' => 'LAP-001',
            'nombre' => 'Existente',
            'categoria' => 'Laptop',
            'estado' => 'Disponible',
        ]);

        $response = $this->actingAs($encargado)->post(route('equipos.store'), [
            'codigo' => 'LAP-001',
            'nombre' => 'Duplicado',
            'categoria' => 'Laptop',
            'estado' => 'Disponible',
        ]);

        $response->assertSessionHasErrors('codigo');
        $this->assertDatabaseCount('equipos', 1);
    }

    public function test_estudiante_no_puede_entrar_al_formulario_de_creacion(): void
    {
        $estudiante = User::factory()->create(['rol' => 'estudiante']);

        $response = $this->actingAs($estudiante)->get(route('equipos.create'));

        $response->assertRedirect(route('estudiante.dashboard'));
    }
}
