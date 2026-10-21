<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegistroController extends Controller
{
    public function mostrar()
    {
        return view('auth.registro', [
            'grupos' => Schema::hasTable('grupos') ? Grupo::where('activo', true)->ordenados()->get() : collect(),
        ]);
    }

    public function registrar(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'apellido' => ['required', 'string', 'max:120'],
            'grupo' => ['nullable', 'integer', Rule::exists('grupos', 'id')->where('activo', true)],
            'email' => ['required', 'string', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $rol = User::rolSegunCorreo($datos['email']);

        if ($rol === null) {
            throw ValidationException::withMessages([
                'email' => 'Solo se puede registrar con un correo del colegio: @sil.edu.py (docentes) o @alumnos.sil.edu.py (alumnos).',
            ]);
        }

        // El alumno elige su grupo (si el administrador ya creó grupos).
        $hayGrupos = Schema::hasTable('grupos') && Grupo::where('activo', true)->exists();
        if ($rol === User::ROL_ALUMNO && $hayGrupos && empty($datos['grupo'])) {
            throw ValidationException::withMessages(['grupo' => 'Elegí tu grupo.']);
        }

        $usuario = User::create([
            'name' => $datos['name'],
            'apellido' => $datos['apellido'],
            'email' => strtolower($datos['email']),
            'rol' => $rol,
            'password' => $datos['password'],
        ]);

        if ($rol === User::ROL_ALUMNO && ! empty($datos['grupo'])) {
            $usuario->grupos()->attach((int) $datos['grupo']);
        }

        Auth::login($usuario, remember: true);
        $request->session()->regenerate();

        return redirect()->intended($usuario->esDocente() ? '/panel' : '/formato');
    }
}
