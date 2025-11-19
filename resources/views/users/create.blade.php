<x-app-layout>
    <x-slot name="header">
        {{ __('Crear usuario') }}
    </x-slot>

    <div class="max-w-4xl mx-auto my-4 bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" value="Nombre" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="email" value="Correo electrónico" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="password" value="Contraseña" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="laboratorio_id" value="Laboratorio" />
                    <select id="laboratorio_id" name="laboratorio_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione un laboratorio</option>
                        @foreach ($laboratorios as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 text-right">
                <x-primary-button>Guardar</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
