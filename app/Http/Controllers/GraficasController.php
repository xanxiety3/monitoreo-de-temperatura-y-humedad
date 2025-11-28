<?php

namespace App\Http\Controllers;

use App\Models\RegistroTemperatura;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GraficasController extends Controller
{
    public function graficas()
    {
       // Ordenar todo por fecha y hora
        $registros = RegistroTemperatura::orderBy('created_at')->get();

        if ($registros->isEmpty()) {
            return view('graficas.index', [
                'dias' => [],
                'temp_9' => [], 'temp_11' => [], 'temp_15' => [],
                'hum_9' => [], 'hum_11' => [], 'hum_15' => []
            ]);
        }

        // Agrupar por día
        $diasAgrupados = $registros->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        $dias = [];
        $temp_9 = []; $temp_11 = []; $temp_15 = [];
        $hum_9  = []; $hum_11  = []; $hum_15  = [];

        foreach ($diasAgrupados as $dia => $items) {

            $dias[] = date('d', strtotime($dia)); // solo número del día

            // Reset por día
            $items = $items->values(); // reindexar 0,1,2,3,4,5

            // --- BLOQUE 9 AM ---
            $temp_9[] = $items[0]->tipo === 'temperatura' ? $items[0]->valor_corregido : $items[1]->valor_corregido;
            $hum_9[]  = $items[0]->tipo === 'humedad'     ? $items[0]->valor_corregido : $items[1]->valor_corregido;

            // --- BLOQUE 11 AM ---
            $temp_11[] = $items[2]->tipo === 'temperatura' ? $items[2]->valor_corregido : $items[3]->valor_corregido;
            $hum_11[]  = $items[2]->tipo === 'humedad'     ? $items[2]->valor_corregido : $items[3]->valor_corregido;

            // --- BLOQUE 15 PM ---
            $temp_15[] = $items[4]->tipo === 'temperatura' ? $items[4]->valor_corregido : $items[5]->valor_corregido;
            $hum_15[]  = $items[4]->tipo === 'humedad'     ? $items[4]->valor_corregido : $items[5]->valor_corregido;
        }

        return view('graficas.index', compact(
            'dias',
            'temp_9', 'temp_11', 'temp_15',
            'hum_9', 'hum_11', 'hum_15'
        ));
    
    }

    
}
