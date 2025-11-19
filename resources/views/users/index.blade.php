<x-app-layout>
    <div class="p-6">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-primary">Gestión de Usuarios</h1>

            <a href="{{ route('usuarios.create') }}"
               class="inline-flex items-center bg-primary text-black px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <x-heroicon-o-user-plus class="w-5 h-5 mr-2" />
                Nuevo Usuario
            </a>
        </div>

        <!-- Mensaje de éxito -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 flex items-center">
                <x-heroicon-o-check-circle class="w-5 h-5 mr-2 text-green-600" />
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabla de usuarios -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nombre</th>
                        <th class="px-4 py-3 text-left font-semibold">Correo</th>
                        <th class="px-4 py-3 text-left font-semibold">Laboratorio</th>
                        <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ $usuario->name }}</td>
                            <td class="px-4 py-3">{{ $usuario->email }}</td>
                            <td class="px-4 py-3">
                                {{ $usuario->laboratorio?->nombre ?? 'No asignado' }}
                            </td>

                           

                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-3">
                                    <!-- Editar -->
                                    <a href="{{ route('usuarios.edit', $usuario) }}"
                                       class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
                                        <x-heroicon-o-pencil-square class="w-5 h-5 mr-1" />
                                        Editar
                                    </a>

                                    <!-- Eliminar -->
                                    <form action="{{ route('usuarios.destroy', $usuario) }}" 
                                          method="POST" class="inline"
                                          onsubmit="return confirm('¿Desea eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center text-red-600 hover:text-red-800 transition">
                                            <x-heroicon-o-trash class="w-5 h-5 mr-1" />
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                <x-heroicon-o-information-circle class="w-5 h-5 inline-block mr-1 text-gray-400" />
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
