<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-primary">Parámetros de Corrección</h1>
            @php
                $laboratorioId = $esAdmin ? null : $userLaboratorioId;

                $tieneTemperatura = $parametros->where('tipo', 'temperatura')->count() > 0;
                $tieneHumedad = $parametros->where('tipo', 'humedad')->count() > 0;
            @endphp

            @if ($esAdmin || !$tieneTemperatura || !$tieneHumedad)
                <a href="{{ route('parametros.create') }}"
                    class="bg-primary text-black px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Nuevo Parámetro
                </a>
            @endif

        </div>

        @if (session('success'))
            <div
                class="mt-2 mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl shadow-sm flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414L9 14.414 5.293 10.707a1 1 0 011.414-1.414L9 11.586l6.293-6.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>

        @endif

        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Laboratorio</th>
                    <th class="px-4 py-2 text-left">Tipo</th>
                    <th class="px-4 py-2 text-center">Valor 1</th>
                    <th class="px-4 py-2 text-center">Valor 2</th>
                    <th class="px-4 py-2 text-center">Valor 3</th>
                    <th class="px-4 py-2 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($parametros as $parametro)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 uppercase">{{ $parametro->laboratorio->nombre ?? 'No asignado' }}</td>
                        <td class="px-4 py-2 uppercase">{{ $parametro->tipo }}</td>
                        <td class="px-4 py-2 text-center">{{ $parametro->valor_1 }}</td>
                        <td class="px-4 py-2 text-center">{{ $parametro->valor_2 }}</td>
                        <td class="px-4 py-2 text-center">{{ $parametro->valor_3 }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('parametros.edit', $parametro) }}"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
                                <x-heroicon-o-pencil class="w-5 h-5 mr-1" /> Editar
                            </a>
                            <form action="{{ route('parametros.destroy', $parametro) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center text-red-600 hover:text-red-800 transition"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este parámetro?');">
                                    <x-heroicon-o-trash class="w-5 h-5 mr-1" /> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">
                            No hay parámetros registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>