<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gráficas de Temperatura y Humedad') }}
        </h2>
    </x-slot>
    <title>Gráficas de Temperatura y Humedad</title>

    <!-- CDN Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: #f5f6fa;
            font-family: Arial, Helvetica, sans-serif;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 40px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .chart-box {
            width: 500px;
            /* Ancho fijo */
            height: 450px;
            /* Alto fijo (cuadrada) */
            margin: 20px auto;
            /* Centrada */
            padding: 15px;
        }
        .padre {
            display: flex;
            justify-content: center;
            gap: 40px; /* Espacio entre las gráficas */
        }
    </style>

    </head>

    <body>

        <!-- ========================= -->
        <!--   GRÁFICA TEMPERATURA     -->
        <!-- ========================= -->

<div class="padre">      <div class="card chart-box">
            <h3 class="text-center mb-2">Temperatura (°C)</h3>
            <canvas id="graficaTemperatura"></canvas>
        </div>

        <div class="card chart-box">
            <h3 class="text-center mb-2">Humedad Relativa (%)</h3>
            <canvas id="graficaHumedad"></canvas>
        </div>
</div>
  

        <script>
            // ==========================================
            // DATOS DESDE LARAVEL
            // ==========================================
            const opciones = {
                responsive: true,
                maintainAspectRatio: false,   // ❗ Permite usar tamaño cuadrado definido en CSS
            };

            const dias = @json($dias);

            // Temperaturas
            const t9 = @json($temp_9);
            const t11 = @json($temp_11);
            const t15 = @json($temp_15);

            // Humedad
            const h9 = @json($hum_9);
            const h11 = @json($hum_11);
            const h15 = @json($hum_15);

            // Límites TEMPERATURA
            const tempInferior = Array(dias.length).fill(19);
            const tempOptima = Array(dias.length).fill(22);
            const tempSuperior = Array(dias.length).fill(24);

            // Límites HUMEDAD
            const humInferior = Array(dias.length).fill(30);
            const humOptima = Array(dias.length).fill(45);
            const humSuperior = Array(dias.length).fill(60);

            // ==================================================
            //               GRAFICA TEMPERATURA
            // ==================================================
            const ctx1 = document.getElementById('graficaTemperatura').getContext('2d');

            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: dias,
                    datasets: [
                        {
                            label: "9:00 AM",
                            data: t9,
                            borderColor: "green",
                            borderWidth: 2
                        },
                        {
                            label: "11:00 AM",
                            data: t11,
                            borderColor: "orange",
                            borderWidth: 2
                        },
                        {
                            label: "3:00 PM",
                            data: t15,
                            borderColor: "purple",
                            borderWidth: 2
                        },
                        // Límites
                        {
                            label: "Límite inferior (19°C)",
                            data: tempInferior,
                            borderColor: "red",
                            borderWidth: 2,
                            borderDash: [5, 5]
                        },
                        {
                            label: "Temperatura óptima (22°C)",
                            data: tempOptima,
                            borderColor: "blue",
                            borderWidth: 2,
                            borderDash: [5, 5]
                        },
                        {
                            label: "Límite superior (24°C)",
                            data: tempSuperior,
                            borderColor: "red",
                            borderWidth: 2,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: opciones
            });

            // ==================================================
            //                 GRAFICA HUMEDAD
            // ==================================================
            const ctx2 = document.getElementById('graficaHumedad').getContext('2d');

            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: dias,
                    datasets: [
                        {
                            label: "9:00 AM",
                            data: h9,
                            borderColor: "green",
                            borderWidth: 2
                        },
                        {
                            label: "11:00 AM",
                            data: h11,
                            borderColor: "orange",
                            borderWidth: 2
                        },
                        {
                            label: "3:00 PM",
                            data: h15,
                            borderColor: "purple",
                            borderWidth: 2
                        },
                        // Límites
                        {
                            label: "Límite inferior (30%)",
                            data: humInferior,
                            borderColor: "red",
                            borderWidth: 2,
                            borderDash: [5, 5]
                        },
                        {
                            label: "Humedad óptima (45%)",
                            data: humOptima,
                            borderColor: "blue",
                            borderWidth: 2,
                            borderDash: [5, 5]
                        },
                        {
                            label: "Límite superior (60%)",
                            data: humSuperior,
                            borderColor: "red",
                            borderWidth: 2,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: opciones
            });

        </script>


</x-app-layout>