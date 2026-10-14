<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{
    /**
     * Panel del Encargado: lista todos los equipos y los estudiantes
     * disponibles para el selector de préstamo.
     */
    public function indexEncargado()
    {
        try {
            $equipos = Equipo::with('estudiante')->get();
            $estudiantes = User::where('rol', 'estudiante')->orderBy('name')->get();

            return view('encargado.dashboard', compact('equipos', 'estudiantes'));
        } catch (QueryException $e) {
            report($e);

            // En peticiones GET no usamos back() para evitar bucles de redirección.
            return view('encargado.dashboard', [
                'equipos' => collect(),
                'estudiantes' => collect(),
            ])->with('error', 'No se pudo cargar la información. Intenta nuevamente en unos minutos.');
        }
    }

    /**
     * Registrar un equipo nuevo. Siempre inicia como Disponible.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:equipos,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:255'],
        ], $this->mensajes());

        try {
            Equipo::create([
                'codigo' => $datos['codigo'],
                'nombre' => $datos['nombre'],
                'categoria' => $datos['categoria'],
                'estado' => 'Disponible',
            ]);

            return back()->with('success', 'Equipo registrado con éxito.');
        } catch (QueryException $e) {
            report($e);

            return back()->with('error', 'No se pudo registrar el equipo. Intenta nuevamente en unos minutos.');
        }
    }

    /**
     * Mostrar el formulario de edición de un equipo.
     */
    public function edit(Equipo $equipo)
    {
        return view('encargado.equipos.edit', compact('equipo'));
    }

    /**
     * Actualizar un equipo existente.
     */
    public function update(Request $request, Equipo $equipo)
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('equipos', 'codigo')->ignore($equipo->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:255'],
            'estado' => ['required', Rule::in(['Disponible', 'Prestado'])],
        ], $this->mensajes());

        try {
            $equipo->update($datos);

            return redirect()->route('encargado.dashboard')->with('success', 'Equipo actualizado con éxito.');
        } catch (QueryException $e) {
            report($e);

            return back()->with('error', 'No se pudo actualizar el equipo. Intenta nuevamente en unos minutos.');
        }
    }

    /**
     * Eliminar un equipo. No se permite si está prestado.
     */
    public function destroy(Equipo $equipo)
    {
        if ($equipo->estado === 'Prestado') {
            return back()->with('error', 'No se puede eliminar un equipo que está prestado.');
        }

        try {
            $equipo->delete();

            return back()->with('success', 'Equipo eliminado con éxito.');
        } catch (QueryException $e) {
            report($e);

            return back()->with('error', 'No se pudo eliminar el equipo. Intenta nuevamente en unos minutos.');
        }
    }

    /**
     * Prestar un equipo a un estudiante.
     */
    public function prestar(Request $request, Equipo $equipo)
    {
        $datos = $request->validate([
            'estudiante_id' => ['required', 'integer', Rule::exists('users', 'id')->where('rol', 'estudiante')],
        ], $this->mensajes());

        if ($equipo->estado !== 'Disponible') {
            return back()->with('error', 'El equipo no está disponible para préstamo.');
        }

        try {
            $equipo->update([
                'estado' => 'Prestado',
                'estudiante_id' => $datos['estudiante_id'],
            ]);

            return back()->with('success', 'Equipo prestado correctamente.');
        } catch (QueryException $e) {
            report($e);

            return back()->with('error', 'No se pudo registrar el préstamo. Intenta nuevamente en unos minutos.');
        }
    }

    /**
     * Devolver un equipo prestado (vuelve a Disponible).
     */
    public function devolver(Equipo $equipo)
    {
        if ($equipo->estado !== 'Prestado') {
            return back()->with('error', 'El equipo no está prestado.');
        }

        try {
            $equipo->update([
                'estado' => 'Disponible',
                'estudiante_id' => null,
            ]);

            return back()->with('success', 'Equipo devuelto correctamente.');
        } catch (QueryException $e) {
            report($e);

            return back()->with('error', 'No se pudo registrar la devolución. Intenta nuevamente en unos minutos.');
        }
    }

    /**
     * Panel del Estudiante: solo lectura, únicamente equipos Disponibles.
     */
    public function indexEstudiante()
    {
        try {
            $equipos = Equipo::where('estado', 'Disponible')->orderBy('codigo')->get();

            return view('estudiante.dashboard', compact('equipos'));
        } catch (QueryException $e) {
            report($e);

            return view('estudiante.dashboard', ['equipos' => collect()])
                ->with('error', 'No se pudo cargar la información. Intenta nuevamente en unos minutos.');
        }
    }

    /**
     * Mensajes de validación en español para reutilizar en los formularios.
     */
    private function mensajes(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe un equipo con ese código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'categoria.required' => 'La categoría es obligatoria.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'estudiante_id.required' => 'Debe seleccionar un estudiante.',
            'estudiante_id.exists' => 'El estudiante seleccionado no es válido.',
        ];
    }
}
