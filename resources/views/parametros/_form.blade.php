<div class="max-w-3xl mx-auto p-8 bg-white rounded-2xl shadow-md mt-10 border border-gray-100">

    {{-- ENCABEZADO --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <x-heroicon-o-adjustments-horizontal class="w-7 h-7 text-blue-600" />
            <h2 class="text-2xl font-bold text-gray-800">
                {{ isset($parametro) ? 'Editar Parámetro de Corrección' : 'Nuevo Parámetro de Corrección' }}
            </h2>
        </div>
        <a href="{{ route('parametros.index') }}" class="text-sm text-gray-500 hover:text-blue-600 transition">
            ← Volver a la lista
        </a>
    </div>

    {{-- MENSAJES --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <form action="{{ isset($parametro) ? route('parametros.update', $parametro) : route('parametros.store') }}"
          method="POST">

        @csrf
        @if(isset($parametro))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- LABORATORIO --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Laboratorio</label>

                @php
                    $selectedLab = old('laboratorio_id', $parametro->laboratorio_id ?? $laboratorioIdUsuario);
                @endphp

                {{-- Si usuario no es admin --}}
                @if ($laboratorioIdUsuario)
                    <select
                        class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm"
                        disabled>
                        <option>{{ $laboratorios->first()->nombre }}</option>
                    </select>

                    {{-- Campo real oculto --}}
                    <input type="hidden" name="laboratorio_id" value="{{ $laboratorioIdUsuario }}">

                @else
                    {{-- ADMIN --}}
                    <select name="laboratorio_id"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        required>
                        <option value="">Seleccione...</option>
                        @foreach ($laboratorios as $lab)
                            <option value="{{ $lab->id }}"
                                {{ $selectedLab == $lab->id ? 'selected' : '' }}>
                                {{ $lab->nombre }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- TIPO --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo</label>

                @if (isset($parametro))
                    {{-- EN EDICIÓN, NO SE PUEDE CAMBIAR --}}
                    <input
                        class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm"
                        value="{{ ucfirst($parametro->tipo) }}" readonly>

                    <input type="hidden" name="tipo" value="{{ $parametro->tipo }}">
                @else
                    {{-- EN CREACIÓN --}}
                    <select name="tipo"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        required>
                        <option value="">Seleccione tipo...</option>

                        @if (!$tieneTemperatura)
                            <option value="temperatura">Temperatura</option>
                        @endif

                        @if (!$tieneHumedad)
                            <option value="humedad">Humedad</option>
                        @endif
                    </select>
                @endif
            </div>

            {{-- VALOR 1 --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor 1</label>
                <input type="number" step="any" name="valor_1"
                    value="{{ old('valor_1', $parametro->valor_1 ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>

            {{-- VALOR 2 --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor 2</label>
                <input type="number" step="any" name="valor_2"
                    value="{{ old('valor_2', $parametro->valor_2 ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>

            {{-- VALOR 3 --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor 3</label>
                <input type="number" step="any" name="valor_3"
                    value="{{ old('valor_3', $parametro->valor_3 ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>

        </div>

        {{-- BOTONES --}}
        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('parametros.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition">
                Cancelar
            </a>

            <button type="submit"
                class="px-5 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                {{ isset($parametro) ? 'Actualizar' : 'Guardar' }}
            </button>
        </div>

    </form>
</div>
