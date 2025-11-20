<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Panel de Registro Ambiental') }}
            </h2>

            <div class="text-right">
                <div id="clock" class="text-2xl font-mono text-gray-700 font-bold"></div>
                <div class="text-xs text-gray-400">Hora local</div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-8 space-y-8 px-4 sm:px-6 lg:px-8">

        {{-- 1. MENSAJES DE ALERTA Y ÉXITO --}}
        <div class="space-y-3">
            @if(session('success'))
                <div class="flex items-center p-4 rounded-lg bg-green-50 border border-green-300 text-green-800 shadow-sm">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="flex items-start p-4 rounded-lg bg-red-50 border border-red-300 text-red-700 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- 2. CONTENIDO PRINCIPAL: GRID 2/3 (FORMULARIO) + 1/3 (RESUMEN) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- 2.1. COLUMNA PRINCIPAL (2/3): FORMULARIO DE REGISTRO --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 border border-gray-100">

                <div class="pb-4 mb-4 border-b">
                    <h4 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v6m0 8v6m4-12a4 4 0 10-8 0v6a4 4 0 108 0V8z"></path></svg>
                        Registro Conjunto: Temperatura & Humedad
                    </h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Ingrese ambos valores para el bloque actual.
                    </p>
                </div>

                @php
                    $isBlockComplete = $bloqueActual && $estadoBloques[$bloqueActual]['temperatura'] && $estadoBloques[$bloqueActual]['humedad'];
                    $disabled = !$bloqueActual || $isBlockComplete;
                    $enabled = !$disabled;
                @endphp

                <form action="{{ route('registros.storeAmbos') }}" method="POST" class="space-y-6">
                    @csrf

                    <input type="hidden" name="laboratorio_id" value="{{ Auth::user()->laboratorio_id }}">
                    <input type="hidden" name="hora" id="hora-hidden">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative">
                            <label for="temperatura_valor" class="block text-sm font-semibold text-gray-700 mb-1">
                                Temperatura (°C)
                            </label>
                            <input name="temperatura_valor" id="temperatura_valor" type="number" step="0.1"
                                class="w-full rounded-lg pl-4 pr-10 py-2.5 text-lg bg-gray-50 border-2 border-gray-300
                                focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 shadow-sm transition"
                                @if($disabled) disabled @endif required placeholder="Ej: 21.5">
                            <span class="absolute right-3 top-1/2 transform translate-y-2 text-gray-500 font-medium text-sm">°C</span>
                        </div>

                        <div class="relative">
                            <label for="humedad_valor" class="block text-sm font-semibold text-gray-700 mb-1">
                                Humedad (%)
                            </label>
                            <input name="humedad_valor" id="humedad_valor" type="number" step="0.1"
                                class="w-full rounded-lg pl-4 pr-10 py-2.5 text-lg bg-gray-50 border-2 border-gray-300
                                focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-100 shadow-sm transition"
                                @if($disabled) disabled @endif required placeholder="Ej: 55.0">
                            <span class="absolute right-3 top-1/2 transform translate-y-2 text-gray-500 font-medium text-sm">%</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            @if($disabled)
                                @if(!$bloqueActual)
                                    <span class="text-red-600 font-bold">Día Completado 🛑</span>
                                @else
                                    <span class="text-yellow-600 font-bold">Bloque {{ $bloqueActual }} Completo ✅</span>
                                @endif
                            @else
                                Registrando para el bloque:
                                <span class="font-bold text-blue-600">
                                    Bloque {{ $bloqueActual }} ({{ $bloques[$bloqueActual] }})
                                </span>
                            @endif
                        </div>

                        <button type="submit" class="px-8 py-2.5 rounded-md text-white font-bold text-base transition shadow-lg
                            {{ $enabled ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed' }}"
                            @if($disabled) disabled @endif>
                            {{ $enabled ? 'Registrar Valores' : 'No Disponible' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2.2. COLUMNA SECUNDARIA (1/3): ESTADO Y HORARIOS --}}
            <div class="space-y-6">
                
                {{-- Resumen del Estado General --}}
                <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <h5 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-9 0V3h2m-2 2h2m-8 0v2"></path></svg>
                        Resumen de la Jornada
                    </h5>
                    
                    <div class="space-y-3 text-sm">
                        <p class="text-gray-600 border-l-4 pl-3 @if($ultimoCompleto) border-green-500 @else border-yellow-500 @endif">
                            Último Completado:
                            @if($ultimoCompleto)
                                <span class="font-bold text-green-700">B. {{ $ultimoCompleto }} ({{ $bloques[$ultimoCompleto] }})</span>
                            @else
                                <span class="font-bold text-yellow-600">Ninguno</span>
                            @endif
                        </p>
                        <p class="text-gray-600 border-l-4 pl-3 @if(!$bloqueActual) border-green-500 @else border-blue-500 @endif">
                            Bloque Actual:
                            @if($bloqueActual)
                                <span class="font-bold text-blue-700">B. {{ $bloqueActual }} ({{ $bloques[$bloqueActual] }})</span>
                            @else
                                <span class="font-bold text-green-700">Día Completado 🎉</span>
                            @endif
                        </p>
                        <p class="text-gray-600 border-l-4 pl-3 border-gray-300">
                            Próximo Registro: 
                            <span id="next-block-text" class="font-bold text-gray-700"></span>
                        </p>
                    </div>
                </div>

                {{-- Horarios Nominales (Pequeña tarjeta para referencia) --}}
                <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <h5 class="text-lg font-bold text-gray-700 mb-3">Horarios Nominales</h5>
                    <div class="flex flex-col gap-2 text-sm">
                        @foreach($bloques as $num => $time)
                            <div class="flex items-center justify-between p-2 rounded border
                                {{ $bloqueActual == $num ? 'bg-blue-100 border-blue-400 font-semibold' : 'bg-gray-50 border-gray-200' }}">
                                <span>Bloque {{ $num }}</span>
                                <span class="{{ $bloqueActual == $num ? 'text-blue-700' : 'text-gray-600' }}">{{ $time }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. HISTORIAL RÁPIDO DEL DÍA --}}
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-lg mt-8">
            <h3 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-2">
                Resumen de Cumplimiento por Bloque
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($estadoBloques as $num => $est)
                    @php
                        $isCompleted = $est['temperatura'] && $est['humedad'];
                        $isCurrent = $bloqueActual == $num;
                        $borderColor = $isCompleted ? 'border-green-500' : ($isCurrent ? 'border-blue-500' : 'border-yellow-500');
                        $bgColor = $isCompleted ? 'bg-green-50' : ($isCurrent ? 'bg-blue-50' : 'bg-yellow-50');
                        $statusText = $isCompleted ? 'Completado' : ($isCurrent ? 'Actual Pendiente' : 'Pendiente');
                        $textColor = $isCompleted ? 'text-green-700' : ($isCurrent ? 'text-blue-700' : 'text-yellow-700');
                    @endphp

                    <div class="p-4 rounded-lg border-l-4 {{ $borderColor }} {{ $bgColor }} shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex-grow">
                                <div class="text-base font-extrabold text-gray-800">Bloque {{ $num }} ({{ $bloques[$num] }})</div>
                                <div class="text-sm font-semibold {{ $textColor }} mt-1">{{ $statusText }}</div>
                            </div>
                            <div class="text-right text-xs pt-1">
                                <p>T°: <span class="{{ $est['temperatura'] ? 'text-green-600 font-bold' : 'text-yellow-600 font-medium' }}">{{ $est['temperatura'] ? 'OK' : 'Falta' }}</span></p>
                                <p>H°: <span class="{{ $est['humedad'] ? 'text-green-600 font-bold' : 'text-yellow-600 font-medium' }}">{{ $est['humedad'] ? 'OK' : 'Falta' }}</span></p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ALARMA Y SCRIPT --}}
    <audio id="alarma" src="{{ asset('alarma.mp3') }}" hidden></audio>


    <script>

        const bloques = @json($bloques);
        const bloqueActual = @json($bloqueActual);
        const estadoBloques = @json($estadoBloques);

        function updateClock() {
            const now = new Date();
            const hh = now.getHours().toString().padStart(2, '0');
            const mm = now.getMinutes().toString().padStart(2, '0');
            const ss = now.getSeconds().toString().padStart(2, '0');
            // Se actualiza el reloj en el encabezado
            document.getElementById('clock').textContent = `${hh}:${mm}:${ss}`;
            updateNextBlockText();
        }

        function updateNextBlockText() {
            let text = 'No hay (día completado)';
            if (bloqueActual) {
                const nextTime = bloques[bloqueActual];
                text = `Bloque ${bloqueActual} — ${nextTime}`;
            }
            document.getElementById('next-block-text').textContent = text;
        }

        function checkAlarm() {
            const now = new Date();
            const hora = now.getHours();
            const minuto = now.getMinutes();

            const alarmTimes = [9, 11, 15];

            if (alarmTimes.includes(hora) && minuto === 0) {
                const a = document.getElementById('alarma');
                a.play().catch(() => {
                    console.log('No se pudo reproducir la alarma automáticamente.');
                });
            }
        }

        // Ejecutar cada segundo para reloj; cada 30s para alarmas
        setInterval(updateClock, 1000);
        setInterval(checkAlarm, 30 * 1000);

        // init
        updateClock();

        // Guardar hora exacta al enviar formulario
        document.querySelector('form[action="{{ route('registros.storeAmbos') }}"]')
            .addEventListener('submit', function () {
                const now = new Date();
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                document.getElementById('hora-hidden').value = `${hh}:${mm}`;
            });
    </script>
</x-app-layout>