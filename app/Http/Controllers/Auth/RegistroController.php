<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegistroController extends Controller
{
    public function mostrar()
    {
        return view('auth.registro', [
            'cursos' => User::CURSOS,
        ]);
    }

    public function registrar(Request $request)
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'apellido' => ['required', 'string', 'max:120'],
            'curso' => ['required', Rule::in(User::CURSOS)],
            'email' => ['required', 'string', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $rol = User::rolSegunCorreo($datos['email']);

        if ($rol === null) {
            throw ValidationException::withMessages([
                'email' => 'Usá un correo institucional @sil.edu.py o @alumnos.sil.edu.py.',
            ]);
        }

        $usuario = User::create([
            'name' => $datos['name'],
            'apellido' => $datos['apellido'],
            'email' => strtolower($datos['email']),
            'rol' => $rol,
            'curso' => $datos['curso'],
            'password' => $datos['password'],
        ]);

        Auth::login($usuario, remember: true);
        $request->session()->regenerate();

        return redirect('/aula');
    }
}
