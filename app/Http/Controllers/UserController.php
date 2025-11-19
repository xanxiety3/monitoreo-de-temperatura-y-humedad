<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function index()
    {
        $usuarios = User::with('laboratorio')->get();
        return view('users.index', compact('usuarios'));
    }


    public function create()
    {
        $laboratorios = Laboratorio::all();
        return view('users.create', compact('laboratorios'));
    }


    public function edit(User $usuario)
    {
        $laboratorios = Laboratorio::all();

        return view('users.edit', compact('usuario', 'laboratorios'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'laboratorio_id' => 'nullable|exists:laboratorios,id',
        ]);

        $usuario->update($request->only('name', 'email', 'laboratorio_id'));

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'laboratorio_id' => 'nullable|exists:laboratorios,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'laboratorio_id' => $validated['laboratorio_id'],
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    public function destroy(User $usuario)
    {
        // Evitar que un usuario se auto-elimine (opcional)
        if (auth()->id() === $usuario->id) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        try {
            $usuario->delete();
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error eliminando usuario: ' . $e->getMessage());
            return redirect()->route('usuarios.index')->with('error', 'Ocurrió un error al eliminar el usuario.');
        }
    }
}
