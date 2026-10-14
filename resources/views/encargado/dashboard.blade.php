<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel del Encargado
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Datos del usuario -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Bienvenido, <strong>{{ auth()->user()->name }}</strong> (Encargado)
                </div>
            </div>

            <!-- Mensajes de la sesión -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Errores de validación -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Registrar nuevo equipo -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Registrar nuevo equipo</h3>

                    <form action="{{ route('equipos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        @csrf
                        <input type="text" name="codigo" value="{{ old('codigo') }}" placeholder="Código (ej: LAP-003)"
                               class="border-gray-300 rounded shadow-sm" required>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre (ej: Laptop Lenovo)"
                               class="border-gray-300 rounded shadow-sm" required>
                        <input type="text" name="categoria" value="{{ old('categoria') }}" placeholder="Categoría (ej: Laptop)"
                               class="border-gray-300 rounded shadow-sm" required>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                            Guardar equipo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Inventario de equipos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Inventario de equipos</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">Código</th>
                                    <th class="border px-3 py-2 text-left">Nombre</th>
                                    <th class="border px-3 py-2 text-left">Categoría</th>
                                    <th class="border px-3 py-2 text-left">Estado</th>
                                    <th class="border px-3 py-2 text-left">Prestado a</th>
                                    <th class="border px-3 py-2 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($equipos as $equipo)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $equipo->codigo }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->nombre }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->categoria }}</td>
                                        <td class="border px-3 py-2">
                                            @if ($equipo->estado === 'Disponible')
                                                <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">{{ $equipo->estado }}</span>
                                            @else
                                                <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">{{ $equipo->estado }}</span>
                                            @endif
                                        </td>
                                        <td class="border px-3 py-2">{{ $equipo->estudiante ? $equipo->estudiante->name : '-' }}</td>
                                        <td class="border px-3 py-2">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <!-- Editar -->
                                                <a href="{{ route('equipos.edit', $equipo) }}"
                                                   class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-1 rounded text-xs">
                                                    Editar
                                                </a>

                                                <!-- Eliminar -->
                                                <form action="{{ route('equipos.destroy', $equipo) }}" method="POST"
                                                      onsubmit="return confirm('¿Seguro que deseas eliminar este equipo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                                        Eliminar
                                                    </button>
                                                </form>

                                                @if ($equipo->estado === 'Disponible')
                                                    <!-- Prestar -->
                                                    <form action="{{ route('equipos.prestar', $equipo) }}" method="POST"
                                                          class="flex items-center gap-1">
                                                        @csrf
                                                        <select name="estudiante_id" class="border-gray-300 rounded text-xs" required>
                                                            <option value="">Seleccionar estudiante</option>
                                                            @foreach ($estudiantes as $estudiante)
                                                                <option value="{{ $estudiante->id }}">{{ $estudiante->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                                                            Prestar
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- Devolver -->
                                                    <form action="{{ route('equipos.devolver', $equipo) }}" method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                                            Devolver
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="border px-3 py-2 text-center text-gray-500">
                                            No hay equipos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
