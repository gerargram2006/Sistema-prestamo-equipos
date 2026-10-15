<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PrestamoEquipoTest — pruebas del flujo de préstamos.
 *
 * Verifica: solicitar un equipo, devolverlo, que no se pueda devolver el de
 * otro estudiante ni solicitar uno ya prestado, y que un encargado no pueda
 * solicitar equipos.
 */
class PrestamoEquipoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crea un equipo disponible para las pruebas.
     */
    private function equipoDisponible(): Equipo
    {
        return Equipo::create([
            'codigo' => 'LAP-001',
            'nombre' => 'Laptop Dell Latitude',
            'categoria' => 'Laptop',
            'estado' => 'Disponible',
        ]);
    }

    public function test_estudiante_puede_solicitar_un_equipo_disponible(): void
    {
        $estudiante = User::factory()->create(['rol' => 'estudiante']);
        $equipo = $this->equipoDisponible();

        $response = $this->actingAs($estudiante)
            ->post(route('equipos.prestar', $equipo));

        $response->assertRedirect();
        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'estado' => 'Prestado',
            'estudiante_id' => $estudiante->id,
        ]);
    }

    public function test_estudiante_puede_devolver_su_propio_equipo(): void
    {
        $estudiante = User::factory()->create(['rol' => 'estudiante']);
        $equipo = $this->equipoDisponible();
        $equipo->update(['estado' => 'Prestado', 'estudiante_id' => $estudiante->id]);

        $response = $this->actingAs($estudiante)
            ->post(route('equipos.devolver', $equipo));

        $response->assertRedirect();
        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'estado' => 'Disponible',
            'estudiante_id' => null,
        ]);
    }

    public function test_estudiante_no_puede_devolver_el_equipo_de_otro(): void
    {
        $propietario = User::factory()->create(['rol' => 'estudiante']);
        $otro = User::factory()->create(['rol' => 'estudiante']);
        $equipo = $this->equipoDisponible();
        $equipo->update(['estado' => 'Prestado', 'estudiante_id' => $propietario->id]);

        $response = $this->actingAs($otro)
            ->post(route('equipos.devolver', $equipo));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'estado' => 'Prestado',
            'estudiante_id' => $propietario->id,
        ]);
    }

    public function test_estudiante_no_puede_solicitar_un_equipo_ya_prestado(): void
    {
        $propietario = User::factory()->create(['rol' => 'estudiante']);
        $otro = User::factory()->create(['rol' => 'estudiante']);
        $equipo = $this->equipoDisponible();
        $equipo->update(['estado' => 'Prestado', 'estudiante_id' => $propietario->id]);

        $response = $this->actingAs($otro)
            ->post(route('equipos.prestar', $equipo));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'estudiante_id' => $propietario->id,
        ]);
    }

    public function test_encargado_no_puede_solicitar_equipos(): void
    {
        $encargado = User::factory()->create(['rol' => 'encargado']);
        $equipo = $this->equipoDisponible();

        // El middleware de rol lo devuelve a su propio panel.
        $response = $this->actingAs($encargado)
            ->post(route('equipos.prestar', $equipo));

        $response->assertRedirect(route('encargado.dashboard'));
        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'estado' => 'Disponible',
            'estudiante_id' => null,
        ]);
    }
}
