<?php

namespace App\Http\Controllers;

use App\Models\ParametrosCorrecion;
use App\Models\RegistroTemperatura;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RegistroTemperaturaController extends Controller
{

    public function index()
    {
        $laboratorioId = Auth::user()->laboratorio_id;

        // -------------------------------
        // 1. PARÁMETROS
        // -------------------------------
        $parametrosTemp = ParametrosCorrecion::where('laboratorio_id', $laboratorioId)
            ->where('tipo', 'temperatura')
            ->first();

        $parametrosHum = ParametrosCorrecion::where('laboratorio_id', $laboratorioId)
            ->where('tipo', 'humedad')
            ->first();

        // -------------------------------
        // 2. BLOQUES HORARIOS
        // -------------------------------
        $bloques = [
            1 => '09:00',
            2 => '11:00',
            3 => '15:00',
        ];

        $hoy = Carbon::today();

        // Traer registros del día
        $registrosHoy = RegistroTemperatura::whereDate('created_at', $hoy)
            ->where('laboratorio_id', $laboratorioId)
            ->get();

        // Estructura:
        // ['1' => ['temperatura'=>true, 'humedad'=>true], ...]
        $estadoBloques = [
            1 => ['temperatura' => false, 'humedad' => false],
            2 => ['temperatura' => false, 'humedad' => false],
            3 => ['temperatura' => false, 'humedad' => false],
        ];

        foreach ($registrosHoy as $reg) {
            $hora = Carbon::parse($reg->created_at)->format('H:i');

            // Determinar en qué bloque cayó
            $bloque = null;

            if ($hora >= '00:00' && $hora <= '09:59') $bloque = 1;
            if ($hora >= '10:00' && $hora <= '11:59') $bloque = 2;
            if ($hora >= '12:00' && $hora <= '23:59') $bloque = 3;

            if ($bloque) {
                $estadoBloques[$bloque][$reg->tipo] = true;
            }
        }

        // -------------------------------
        // 3. Determinar bloque actual
        // -------------------------------
        $bloqueActual = null;
        $ultimoCompleto = null;

        foreach ($estadoBloques as $num => $datos) {
            if ($datos['temperatura'] && $datos['humedad']) {
                $ultimoCompleto = $num;
            } else {
                $bloqueActual = $num;
                break;
            }
        }

        // Si los 3 bloques están completos → no permitir más
        if (!$bloqueActual) {
            $bloqueActual = null; // día completo
        }

        return view('dashboard', [
            'parametrosTemp' => $parametrosTemp,
            'parametrosHum'  => $parametrosHum,
            'estadoBloques'  => $estadoBloques,
            'bloqueActual'   => $bloqueActual,
            'ultimoCompleto' => $ultimoCompleto,
            'bloques'        => $bloques,
        ]);
    }


public function storeAmbos(Request $request)
{
    // Validar lo que realmente llega desde el formulario
    $request->validate([
        'temperatura_valor' => 'required|numeric',
        'humedad_valor'     => 'required|numeric',
        'laboratorio_id'    => 'required|exists:laboratorios,id',
        'hora'              => 'required',
    ]);

    $user = Auth::user();
    $laboratorioId = $request->laboratorio_id;

    // Obtener parámetros
    $paramTemp = ParametrosCorrecion::where('laboratorio_id', $laboratorioId)
        ->where('tipo', 'temperatura')
        ->first();

    $paramHum = ParametrosCorrecion::where('laboratorio_id', $laboratorioId)
        ->where('tipo', 'humedad')
        ->first();

    // Validar que existan parámetros
    if (!$paramTemp || !$paramHum) {
        return back()->with('error', 'No existen parámetros de corrección registrados para este laboratorio.');
    }

    // Calcular valores corregidos
    $tempCorregida = $this->corregirValor('temperatura', $request->temperatura_valor, $paramTemp);
    $humCorregida  = $this->corregirValor('humedad', $request->humedad_valor, $paramHum);

    // Construir fecha y hora reales según reloj del usuario
    $fechaHora = now()->format('Y-m-d') . ' ' . $request->hora . ':00';

    // === Guarda TEMPERATURA ===
    RegistroTemperatura::create([
        'laboratorio_id' => $laboratorioId,
        'user_id'        => $user->id,
        'tipo'           => 'temperatura',
        'valor_original' => $request->temperatura_valor,
        'valor_corregido' => $tempCorregida,
        'created_at'     => $fechaHora,
        'updated_at'     => now(),
    ]);

    // === Guarda HUMEDAD ===
    RegistroTemperatura::create([
        'laboratorio_id' => $laboratorioId,
        'user_id'        => $user->id,
        'tipo'           => 'humedad',
        'valor_original' => $request->humedad_valor,
        'valor_corregido' => $humCorregida,
        'created_at'     => $fechaHora,
        'updated_at'     => now(),
    ]);

    return back()->with('success', 'Registros guardados correctamente.');
}



   private function corregirValor($tipo, $valor, $params)
{
    if (!$params) {
        // evita crash
        return $valor;
    }

    // === TEMPERATURA ===
    if ($tipo === 'temperatura') {
        $v1 = $params->valor_1;
        $v2 = $params->valor_2;
        $v3 = $params->valor_3;

        // tramo 1 (17.6 a 21.9)
        if ($valor <= 21.9) {
            return $valor + ($v1 + ($v2 - $v1) * ($valor - 17.6) / (21.9 - 17.6));
        }

        // tramo 2 (21.9 a 25.9)
        return $valor + ($v2 + ($v3 - $v2) * ($valor - 21.9) / (25.9 - 21.9));
    }

    // === HUMEDAD ===
    if ($tipo === 'humedad') {
        $v1 = $params->valor_1;
        $v2 = $params->valor_2;
        $v3 = $params->valor_3;

        // tramo 1 (30 a 44)
        if ($valor <= 44) {
            return $valor + ($v1 + ($v2 - $v1) * ($valor - 30) / (44 - 30));
        }

        // tramo 2 (44 a 60)
        return $valor + ($v2 + ($v3 - $v2) * ($valor - 44) / (60 - 44));
    }

    return $valor;
}

}
