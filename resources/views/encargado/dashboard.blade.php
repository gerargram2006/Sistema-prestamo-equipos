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

            <!-- Inventario de equipos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Inventario de equipos</h3>
                        <a href="{{ route('equipos.create') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded text-sm">
                            Crear equipo
                        </a>
                    </div>

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
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="border px-3 py-2 text-center text-gray-500">
                                            <p class="mb-3">No hay equipos registrados.</p>
                                            <a href="{{ route('equipos.create') }}"
                                               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded text-sm inline-block">
                                                Crear equipo
                                            </a>
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
