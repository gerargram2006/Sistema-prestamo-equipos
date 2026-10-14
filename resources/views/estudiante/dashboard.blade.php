<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel del Estudiante
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Datos del usuario -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Bienvenido, <strong>{{ auth()->user()->name }}</strong> (Estudiante)
                </div>
            </div>

            <!-- Mensaje de error de la sesión -->
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Equipos disponibles (solo consulta) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Equipos disponibles para préstamo</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">Código</th>
                                    <th class="border px-3 py-2 text-left">Nombre</th>
                                    <th class="border px-3 py-2 text-left">Categoría</th>
                                    <th class="border px-3 py-2 text-left">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($equipos as $equipo)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $equipo->codigo }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->nombre }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->categoria }}</td>
                                        <td class="border px-3 py-2">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">{{ $equipo->estado }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="border px-3 py-2 text-center text-gray-500">
                                            No hay equipos disponibles en este momento.
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
