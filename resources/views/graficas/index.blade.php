<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gráficas de Temperatura y Humedad') }}
        </h2>
    </x-slot>

    <title>Gráficas y Reporte de Temperatura y Humedad</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


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

        h2,
        h3 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .chart-box {
            width: 500px;
            height: 450px;
            margin: 20px auto;
            padding: 15px;
        }

        .padre {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        th {
            background: #f0f0f0;
        }
    </style>

    <body>

        <!-- ========================= -->
        <!--   TABLA DE REGISTROS      -->
        <!-- ========================= -->
        <div class="card">
            <h3>Reporte de Registros</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Valor Original</th>
                        <th>Valor Corregido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registros as $reg)
                        <tr>
                            <td>{{ $reg->created_at->format('Y-m-d') }}</td>
                            <td>{{ $reg->created_at->format('H:i') }}</td>
                            <td>{{ ucfirst($reg->tipo) }}</td>
                            <td>{{ $reg->valor_original }}</td>
                            <td>{{ $reg->valor_corregido }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Links de paginación -->
            <div style="margin-top:15px;">
                {{ $registros->links() }}
            </div>
        </div>

        <!-- ========================= -->
        <!--   GRÁFICAS TEMPERATURA Y HUMEDAD -->
        <!-- ========================= -->
        <div style="text-align:center; margin-bottom:20px;">
            <button id="btnDescargarPDF" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Descargar Reporte PDF
            </button>
        </div>

        <div class="padre">
            <div class="card chart-box">
                <h3>Temperatura (°C)</h3>
                <canvas id="graficaTemperatura"></canvas>
            </div>

            <div class="card chart-box">
                <h3>Humedad Relativa (%)</h3>
                <canvas id="graficaHumedad"></canvas>
            </div>
        </div>

        <script>
            const opciones = {
                responsive: true,
                maintainAspectRatio: false
            };

            const dias = @json($dias);

            const t9 = @json($temp_9);
            const t11 = @json($temp_11);
            const t15 = @json($temp_15);

            const h9 = @json($hum_9);
            const h11 = @json($hum_11);
            const h15 = @json($hum_15);

            const tempInferior = Array(dias.length).fill(19);
            const tempOptima = Array(dias.length).fill(22);
            const tempSuperior = Array(dias.length).fill(24);

            const humInferior = Array(dias.length).fill(30);
            const humOptima = Array(dias.length).fill(45);
            const humSuperior = Array(dias.length).fill(60);

            // Temperatura
            const ctx1 = document.getElementById('graficaTemperatura').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: dias,
                    datasets: [
                        { label: "9:00 AM", data: t9, borderColor: "green", borderWidth: 2 },
                        { label: "11:00 AM", data: t11, borderColor: "orange", borderWidth: 2 },
                        { label: "3:00 PM", data: t15, borderColor: "purple", borderWidth: 2 },
                        { label: "Límite inferior (19°C)", data: tempInferior, borderColor: "red", borderWidth: 2, borderDash: [5, 5] },
                        { label: "Temperatura óptima (22°C)", data: tempOptima, borderColor: "blue", borderWidth: 2, borderDash: [5, 5] },
                        { label: "Límite superior (24°C)", data: tempSuperior, borderColor: "red", borderWidth: 2, borderDash: [5, 5] }
                    ]
                },
                options: opciones
            });

            // Humedad
            const ctx2 = document.getElementById('graficaHumedad').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: dias,
                    datasets: [
                        { label: "9:00 AM", data: h9, borderColor: "green", borderWidth: 2 },
                        { label: "11:00 AM", data: h11, borderColor: "orange", borderWidth: 2 },
                        { label: "3:00 PM", data: h15, borderColor: "purple", borderWidth: 2 },
                        { label: "Límite inferior (30%)", data: humInferior, borderColor: "red", borderWidth: 2, borderDash: [5, 5] },
                        { label: "Humedad óptima (45%)", data: humOptima, borderColor: "blue", borderWidth: 2, borderDash: [5, 5] },
                        { label: "Límite superior (60%)", data: humSuperior, borderColor: "red", borderWidth: 2, borderDash: [5, 5] }
                    ]
                },
                options: opciones
            });

            document.getElementById('btnDescargarPDF').addEventListener('click', async function () {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');

                // Capturamos la tabla
                const tabla = document.querySelector('table');
                const canvasTabla = await html2canvas(tabla, { scale: 2 });
                const imgTabla = canvasTabla.toDataURL('image/png');

                // Añadimos la tabla al PDF
                doc.addImage(imgTabla, 'PNG', 10, 10, 190, canvasTabla.height * 190 / canvasTabla.width);

                // Saltamos para gráficas
                let yOffset = 10 + (canvasTabla.height * 190 / canvasTabla.width) + 10;

                // Capturamos las gráficas
                const graficaTemp = document.getElementById('graficaTemperatura');
                const graficaHum = document.getElementById('graficaHumedad');

                const canvasTemp = await html2canvas(graficaTemp.parentNode, { scale: 2 });
                const imgTemp = canvasTemp.toDataURL('image/png');

                const canvasHum = await html2canvas(graficaHum.parentNode, { scale: 2 });
                const imgHum = canvasHum.toDataURL('image/png');

                // Añadimos la gráfica de temperatura
                doc.addPage();
                doc.addImage(imgTemp, 'PNG', 10, 10, 190, canvasTemp.height * 190 / canvasTemp.width);

                // Añadimos la gráfica de humedad
                doc.addPage();
                doc.addImage(imgHum, 'PNG', 10, 10, 190, canvasHum.height * 190 / canvasHum.width);

                // Descargamos el PDF
                doc.save('reporte_temperatura_humedad.pdf');
            });

        </script>

</x-app-layout>