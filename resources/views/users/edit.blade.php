<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Usuario
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto my-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Correo -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Laboratorio -->
                    <div>
                        <label for="laboratorio_id" class="block text-sm font-medium text-gray-700">Laboratorio
                            asignado</label>
                        <select name="laboratorio_id" id="laboratorio_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Seleccione un laboratorio --</option>
                            @foreach ($laboratorios as $lab)
                                <option value="{{ $lab->id }}" {{ $usuario->laboratorio_id == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('laboratorio_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-2">
                        <div class="flex space-x-3">
                            <a href="{{ route('usuarios.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow-sm hover:bg-gray-300 transition-all duration-200">
                                Cancelar
                            </a>

                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                                Guardar cambios
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>