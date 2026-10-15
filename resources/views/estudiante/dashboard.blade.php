{{--
    Vista: panel del Estudiante.
    - "Equipos disponibles": permite Solicitar ($equiposDisponibles).
    - "Mis préstamos": permite Devolver ($misPrestamos).
--}}
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

            <!-- ===================== Equipos Disponibles ===================== -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Equipos disponibles</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">Código</th>
                                    <th class="border px-3 py-2 text-left">Nombre</th>
                                    <th class="border px-3 py-2 text-left">Categoría</th>
                                    <th class="border px-3 py-2 text-left">Estado</th>
                                    <th class="border px-3 py-2 text-left">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($equiposDisponibles as $equipo)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $equipo->codigo }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->nombre }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->categoria }}</td>
                                        <td class="border px-3 py-2">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">{{ $equipo->estado }}</span>
                                        </td>
                                        <td class="border px-3 py-2">
                                            <form action="{{ route('equipos.prestar', $equipo) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                                                    Solicitar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="border px-3 py-2 text-center text-gray-500">
                                            No hay equipos disponibles en este momento.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ===================== Mis Préstamos ===================== -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Mis préstamos</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">Código</th>
                                    <th class="border px-3 py-2 text-left">Nombre</th>
                                    <th class="border px-3 py-2 text-left">Categoría</th>
                                    <th class="border px-3 py-2 text-left">Estado</th>
                                    <th class="border px-3 py-2 text-left">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($misPrestamos as $equipo)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $equipo->codigo }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->nombre }}</td>
                                        <td class="border px-3 py-2">{{ $equipo->categoria }}</td>
                                        <td class="border px-3 py-2">
                                            <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">{{ $equipo->estado }}</span>
                                        </td>
                                        <td class="border px-3 py-2">
                                            <form action="{{ route('equipos.devolver', $equipo) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                                    Devolver
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="border px-3 py-2 text-center text-gray-500">
                                            No tienes equipos prestados.
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
