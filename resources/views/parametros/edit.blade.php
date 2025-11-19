<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold text-primary mb-6">Editar Parámetro</h1>
        <form action="{{ route('parametros.update', $parametro) }}" method="POST">
            @method('PUT')
            @include('parametros._form')
        </form>
    </div>
</x-app-layout>
