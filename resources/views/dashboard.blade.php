<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Panel de trabajo') }}
            </h2>

            <div class="text-right">
                <!-- Reloj -->
                <div id="clock" class="text-pretty text-gray-600"></div>
                <div class="text-xs text-gray-400">Hora local</div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto mt-6 space-y-6">

        {{-- INFO PRINCIPAL --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Registro de Temperatura y Humedad</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Último bloque completado:
                    @if($ultimoCompleto)
                        <span class="font-semibold text-green-700">Bloque {{ $ultimoCompleto }}
                            ({{ $bloques[$ultimoCompleto] }})</span>
                    @else
                        <span class="font-semibold text-yellow-600">Ninguno</span>
                    @endif
                </p>
                <p class="text-sm text-gray-600">
                    Bloque actual pendiente:
                    @if($bloqueActual)
                        <span class="font-semibold">Bloque {{ $bloqueActual }} — {{ $bloques[$bloqueActual] }}</span>
                    @else
                        <span class="font-semibold text-green-700">Día completado — no quedan bloques</span>
                    @endif
                </p>
            </div>

            <div class="text-right">
                <p class="text-sm text-gray-500">Siguientes horarios:</p>
                <div class="flex gap-3 mt-1">
                    <div class="px-3 py-1 rounded bg-blue-50 text-blue-700 text-sm">09:00</div>
                    <div class="px-3 py-1 rounded bg-blue-50 text-blue-700 text-sm">11:00</div>
                    <div class="px-3 py-1 rounded bg-blue-50 text-blue-700 text-sm">15:00</div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Próximo registro: <span id="next-block-text"
                        class="font-medium"></span></p>
            </div>
        </div>

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TARJETAS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-gray-50 rounded-xl shadow p-6 border border-gray-100 md:col-span-2">

                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 2v6m0 8v6m4-12a4 4 0 10-8 0v6a4 4 0 108 0V8z" />
                            </svg>
                            Registro conjunto — Temperatura & Humedad
                        </h4>

                        <p class="text-sm text-gray-500 mt-1">
                            Ambos valores se registran juntos para evitar inconsistencias.
                        </p>
                    </div>

                    {{-- Estado por bloques --}}
                    <div class="text-sm text-gray-700">
                        @foreach($estadoBloques as $num => $est)
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Bloque {{ $num }} ({{ $bloques[$num] }}):</span>
                                @if($est['temperatura'] && $est['humedad'])
                                    <span class="text-green-600 font-semibold">Completado</span>
                                @else
                                    <span class="text-yellow-600 font-medium">Pendiente</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- FORMULARIO UNIFICADO --}}
                <div class="mt-6">

                    @php
                        $enabled = $bloqueActual
                            && (!$estadoBloques[$bloqueActual]['temperatura']
                                || !$estadoBloques[$bloqueActual]['humedad']);

                        $disabled = !$bloqueActual
                            || ($estadoBloques[$bloqueActual]['temperatura']
                                && $estadoBloques[$bloqueActual]['humedad']);
                    @endphp

                    <form action="{{ route('registros.storeAmbos') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Laboratorio -->
                        <input type="hidden" name="laboratorio_id" value="{{ Auth::user()->laboratorio_id }}">


                        <!-- Hora automática -->
                        <input type="hidden" name="hora" id="hora-hidden">

                        <!-- TEMPERATURA -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Temperatura (°C)</label>
                            <input name="temperatura_valor" type="number" step="any" class="w-full rounded-lg px-3 py-2 bg-gray-100 border-2 border-gray-300
               focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-300
               shadow-sm hover:shadow transition" @if($disabled) disabled @endif required>
                        </div>

                        <!-- HUMEDAD -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Humedad (%)</label>
                            <input name="humedad_valor" type="number" step="any" class="w-full rounded-lg px-3 py-2 bg-gray-100 border-2 border-gray-300
               focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-300
               shadow-sm hover:shadow transition" @if($disabled) disabled @endif required>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <div class="text-sm text-gray-500">
                                @if($disabled)
                                    @if(!$bloqueActual)
                                        Día completado — no se permiten más registros.
                                    @else
                                        Este bloque ya está completo.
                                    @endif
                                @else
                                    Registrando para
                                    <span class="font-semibold">
                                        Bloque {{ $bloqueActual }} — {{ $bloques[$bloqueActual] }}
                                    </span>
                                @endif
                            </div>

                            <button type="submit" class="px-6 py-2 rounded-md text-white font-medium transition
                {{ $enabled ? 'bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700'
    : 'bg-gray-300 cursor-not-allowed' }}" @if($disabled) disabled @endif>
                                {{ $enabled ? 'Registrar ambos valores' : 'No disponible' }}
                            </button>
                        </div>
                    </form>


                </div>
            </div>

        </div>

        {{-- HISTORIAL RÁPIDO DEL DÍA --}}
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <h5 class="text-sm font-semibold text-gray-800 mb-3">Resumen del día</h5>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($estadoBloques as $num => $est)
                    <div
                        class="p-3 rounded-lg border {{ $est['temperatura'] && $est['humedad'] ? 'border-green-200 bg-green-50' : 'border-yellow-100 bg-yellow-50' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold">Bloque {{ $num }} — {{ $bloques[$num] }}</div>
                                <div class="text-xs text-gray-600 mt-1">
                                    Temperatura: <span
                                        class="{{ $est['temperatura'] ? 'text-green-700' : 'text-yellow-700' }}">{{ $est['temperatura'] ? 'OK' : 'Pendiente' }}</span>
                                    <br>
                                    Humedad: <span
                                        class="{{ $est['humedad'] ? 'text-green-700' : 'text-yellow-700' }}">{{ $est['humedad'] ? 'OK' : 'Pendiente' }}</span>
                                </div>
                            </div>
                            <div class="text-sm">
                                @if($est['temperatura'] && $est['humedad'])
                                    <span class="text-green-700 font-semibold">Completado</span>
                                @else
                                    <span class="text-yellow-700 font-medium">Incompleto</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ALARMA Y SCRIPT --}}
    <audio  id="alarma" src="{{ asset('alarma.mp3') }}" controls></audio>


    <script>
        // Datos que envía el servidor
        const bloques = @json($bloques);
        const bloqueActual = @json($bloqueActual);
        const ultimoCompleto = @json($ultimoCompleto);
        const estadoBloques = @json($estadoBloques);

        // Reloj
        function updateClock() {
            const now = new Date();
            const hh = now.getHours().toString().padStart(2, '0');
            const mm = now.getMinutes().toString().padStart(2, '0');
            const ss = now.getSeconds().toString().padStart(2, '0');
            document.getElementById('clock').textContent = `${hh}:${mm}:${ss}`;
            updateNextBlockText();
        }

        // Determina siguiente horario pendiente para mostrar texto
        function updateNextBlockText() {
            const now = new Date();
            let text = '';

            if (!bloqueActual) {
                text = 'No hay (día completado)';
            } else {
                const nextTime = bloques[bloqueActual];
                text = `Bloque ${bloqueActual} — ${nextTime}`;
            }

            document.getElementById('next-block-text').textContent = text;
        }

        // Alarma: suena justo en 9:00, 11:00 y 15:00 (si el usuario tiene la pestaña activa)
        function checkAlarm() {
            const now = new Date();
            const hora = now.getHours();
            const minuto = now.getMinutes();

            const alarmTimes = [9, 11, 15];

            if (alarmTimes.includes(hora) && minuto === 0) {
                const a = document.getElementById('alarma');
                // intenta reproducir; algunos navegadores requieren interacción previa
                a.play().catch(() => {
                    // falló la reproducción automática por política de navegador
                    console.log('No se pudo reproducir la alarma automáticamente.');
                });
            }
        }

        // Ejecutar cada segundo para reloj; cada 30s para alarmas
        setInterval(updateClock, 1000);
        setInterval(checkAlarm, 30 * 1000);

        // init
        updateClock();

        // Mensaje de ayuda si el usuario acaba de registrar (leer mensaje flash)
        @if(session('success'))
            (function () {
                const msg = @json(session('success'));
                // Opcional: usar notificación del navegador
                if (Notification && Notification.permission === 'granted') {
                    new Notification('Registro', { body: msg });
                } else if (Notification && Notification.permission !== 'denied') {
                    Notification.requestPermission().then(p => {
                        if (p === 'granted') new Notification('Registro', { body: msg });
                    });
                }
            })();
        @endif
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