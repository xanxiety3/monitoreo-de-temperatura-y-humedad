<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use App\Models\ParametrosCorrecion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParametrizacionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->name === 'Admin') {
            // Admin ve todo
            $parametros = ParametrosCorrecion::with('laboratorio')->get();
        } else {
            // Usuario normal ve solo su laboratorio
            $parametros = ParametrosCorrecion::with('laboratorio')
                ->where('laboratorio_id', $user->laboratorio_id)
                ->get();
        }

        return view('parametros.index', [
            'parametros' => $parametros,
            'userLaboratorioId' => $user->laboratorio_id,
            'esAdmin' => $user->name === 'Admin'
        ]);
    }


    public function create()
    {
        $user = auth()->user();

        if ($user->name === 'Admin') {
            $laboratorios = Laboratorio::all();
            $labId = null; // admin puede elegir
        } else {
            $laboratorios = Laboratorio::where('id', $user->laboratorio_id)->get();
            $labId = $user->laboratorio_id;
        }

        // Verificar parámetros existentes en el laboratorio del usuario
        $parametros = ParametrosCorrecion::where('laboratorio_id', $labId ?? $laboratorios->first()->id)->get();

        return view('parametros.create', [
            'laboratorios' => $laboratorios,
            'laboratorioIdUsuario' => $labId,
            'tieneTemperatura' => $parametros->where('tipo', 'temperatura')->count() > 0,
            'tieneHumedad' => $parametros->where('tipo', 'humedad')->count() > 0,
            'parametro' => null
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'laboratorio_id' => [
                'required',
                'exists:laboratorios,id',
            ],
            'tipo' => [
                'required',
                Rule::in(['temperatura', 'humedad']),
                Rule::unique('parametros_correcion', 'tipo')
                    ->where(fn($query) => $query->where('laboratorio_id', $request->laboratorio_id)),
            ],
            'valor_1' => 'required|numeric',
            'valor_2' => 'required|numeric',
            'valor_3' => 'required|numeric',
        ], [
            'laboratorio_id.required' => 'Debe seleccionar un laboratorio.',
            'tipo.required' => 'Debe seleccionar un tipo de parámetro.',
            'tipo.in' => 'El tipo debe ser temperatura o humedad.',
            'tipo.unique' => 'Ya existe un parámetro de este tipo para este laboratorio. Por favor, edítelo si desea cambiar los valores.',
            'valor_1.required' => 'Debe ingresar el valor 1.',
            'valor_2.required' => 'Debe ingresar el valor 2.',
            'valor_3.required' => 'Debe ingresar el valor 3.',
        ]);

        ParametrosCorrecion::create($validated);

        return redirect()
            ->route('parametros.index')
            ->with('success', 'Parámetro creado correctamente.');
    }

    public function edit($id)
    {
        $parametro = ParametrosCorrecion::findOrFail($id);

        $user = auth()->user();

        if ($user->role === 'admin') {
            $laboratorios = Laboratorio::all();
        } else {
            // usuario solo puede editar lo de su lab
            $laboratorios = Laboratorio::where('id', $user->laboratorio_id)->get();
        }

        return view('parametros.edit', [
            'parametro' => $parametro,
            'laboratorios' => $laboratorios,
            'laboratorioIdUsuario' => $user->role === 'admin' ? null : $user->laboratorio_id,
            'tieneTemperatura' => false,
            'tieneHumedad' => false
        ]);
    }



    public function update(Request $request, ParametrosCorrecion $parametro)
    {
        $request->validate([
            'laboratorio_id' => 'required|exists:laboratorios,id',
            'tipo' => 'required|string|max:255',
            'valor_1' => 'nullable|numeric',
            'valor_2' => 'nullable|numeric',
            'valor_3' => 'nullable|numeric',
        ]);

        $parametro->update($request->all());

        return redirect()->route('parametros.index')
            ->with('success', 'Parámetro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $parametro = ParametrosCorrecion::findOrFail($id);
        $parametro->delete();

        return redirect()->route('parametros.index')
            ->with('success', 'Parámetro eliminado correctamente.');
    }
}
