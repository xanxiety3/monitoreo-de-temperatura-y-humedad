<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold text-primary mb-6">Nuevo Parámetro</h1>
        <form action="{{ route('parametros.store') }}" method="POST">
            @include('parametros._form')
        </form>
    </div>
</x-app-layout>
