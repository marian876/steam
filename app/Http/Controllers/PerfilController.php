<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function mostrar()
    {
        $usuario = Auth::user();

        return view('panel.perfil', [
            'usuario' => $usuario,
            'grupos' => Schema::hasTable('grupos') ? $usuario->grupos()->ordenados()->get() : collect(),
        ]);
    }

    public function actualizar(Request $request)
    {
        $usuario = Auth::user();

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'apellido' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:180', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'password.confirmed' => 'Las contraseñas nuevas no coinciden.',
        ]);

        $usuario->name = $datos['name'];
        $usuario->apellido = $datos['apellido'];
        $usuario->email = strtolower($datos['email']);
        if (! empty($datos['password'])) {
            $usuario->password = $datos['password'];
        }
        $usuario->save();

        return redirect('/panel/perfil')->with('aviso', 'Tus datos se guardaron.');
    }
}
