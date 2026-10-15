{{--
    Vista: formulario para editar un equipo existente (equipos.update).
    Precarga los datos del equipo ($equipo) y usa @method('PUT').
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar equipo
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">

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

                    <form action="{{ route('equipos.update', $equipo) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="codigo" class="block font-medium text-sm text-gray-700">Código</label>
                            <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $equipo->codigo) }}"
                                   class="mt-1 block w-full border-gray-300 rounded shadow-sm" required>
                        </div>

                        <div>
                            <label for="nombre" class="block font-medium text-sm text-gray-700">Nombre</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $equipo->nombre) }}"
                                   class="mt-1 block w-full border-gray-300 rounded shadow-sm" required>
                        </div>

                        <div>
                            <label for="categoria" class="block font-medium text-sm text-gray-700">Categoría</label>
                            <input type="text" id="categoria" name="categoria" value="{{ old('categoria', $equipo->categoria) }}"
                                   class="mt-1 block w-full border-gray-300 rounded shadow-sm" required>
                        </div>

                        <div>
                            <label for="estado" class="block font-medium text-sm text-gray-700">Estado</label>
                            <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 rounded shadow-sm" required>
                                <option value="Disponible" @selected(old('estado', $equipo->estado) === 'Disponible')>Disponible</option>
                                <option value="Prestado" @selected(old('estado', $equipo->estado) === 'Prestado')>Prestado</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                Guardar cambios
                            </button>
                            <a href="{{ route('encargado.dashboard') }}"
                               class="text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
