<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function index()
    {
        $hayGrupos = Schema::hasTable('grupos');

        return view('panel.usuarios', [
            'usuarios' => User::orderBy('name')->when($hayGrupos, fn ($q) => $q->with('grupos'))->get(),
            'roles' => User::ROLES,
            'tieneActivo' => Schema::hasColumn('users', 'activo'),
            'hayGrupos' => $hayGrupos,
            'grupos' => $hayGrupos ? Grupo::ordenados()->get() : collect(),
        ]);
    }

    public function crear()
    {
        abort_unless(Schema::hasTable('grupos'), 404);

        return view('panel.usuario', [
            'usuario' => new User(['rol' => User::ROL_ALUMNO, 'activo' => true]),
            'roles' => User::ROLES,
            'grupos' => Grupo::ordenados()->get(),
            'seleccionados' => [],
            'tieneActivo' => Schema::hasColumn('users', 'activo'),
        ]);
    }

    public function guardar(Request $request)
    {
        $datos = $this->validarUsuario($request, null);

        $usuario = User::create([
            'name' => $datos['name'],
            'apellido' => $datos['apellido'],
            'email' => strtolower($datos['email']),
            'rol' => $datos['rol'],
            'password' => $datos['password'],
        ]);
        if (Schema::hasColumn('users', 'activo')) {
            $usuario->activo = $request->boolean('activo');
            $usuario->save();
        }
        $usuario->grupos()->sync($datos['grupos']);

        return redirect('/panel/usuarios')->with('aviso', 'Cuenta creada para '.$usuario->nombreCompleto().'.');
    }

    public function editar(User $usuario)
    {
        abort_unless(Schema::hasTable('grupos'), 404);

        return view('panel.usuario', [
            'usuario' => $usuario,
            'roles' => User::ROLES,
            'grupos' => Grupo::ordenados()->get(),
            'seleccionados' => $usuario->grupos()->pluck('grupos.id')->all(),
            'tieneActivo' => Schema::hasColumn('users', 'activo'),
        ]);
    }

    /** Roles del sitio: qué puede hacer cada uno y cuántas cuentas lo tienen. */
    public function roles()
    {
        return view('panel.roles', [
            'roles' => User::ROLES,
            'conteo' => User::selectRaw('rol, count(*) as total')->groupBy('rol')->pluck('total', 'rol'),
        ]);
    }

    public function actualizarRol(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'rol' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        // Un administrador no puede quitarse a sí mismo el rol de admin
        // (para no dejar el sistema sin ningún administrador por error).
        if ($usuario->id === Auth::id() && $datos['rol'] !== User::ROL_ADMIN) {
            return redirect('/panel/usuarios')
                ->with('aviso', 'No podés quitarte tu propio rol de administrador.');
        }

        $usuario->rol = $datos['rol'];
        $usuario->save();

        return redirect('/panel/usuarios')->with('aviso', 'Rol actualizado para '.$usuario->name.'.');
    }

    /** Actualizar los datos del usuario (nombre, apellido, correo, rol y grupos). */
    public function actualizar(Request $request, User $usuario)
    {
        $datos = $this->validarUsuario($request, $usuario);

        // Un administrador no puede quitarse a sí mismo el rol de admin.
        if ($usuario->id === Auth::id() && $datos['rol'] !== User::ROL_ADMIN) {
            $datos['rol'] = User::ROL_ADMIN;
        }

        $usuario->name = $datos['name'];
        $usuario->apellido = $datos['apellido'];
        $usuario->email = strtolower($datos['email']);
        $usuario->rol = $datos['rol'];
        if (Schema::hasColumn('users', 'activo')) {
            // Nadie puede desactivar su propia cuenta.
            $usuario->activo = $usuario->id === Auth::id() ? true : $request->boolean('activo');
        }
        if (! empty($datos['password'])) {
            $usuario->password = $datos['password'];
        }
        $usuario->save();
        $usuario->grupos()->sync($datos['grupos']);

        return redirect('/panel/usuarios')->with('aviso', 'Datos actualizados para '.$usuario->nombreCompleto().'.');
    }

    /** Cambiar la contraseña de un usuario (solo admin). */
    public function cambiarPassword(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario->password = $datos['password'];
        $usuario->save();

        return redirect('/panel/usuarios')->with('aviso', 'Contraseña actualizada para '.$usuario->name.'.');
    }

    /** Activar o desactivar la cuenta de un usuario. */
    public function toggleActivo(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect('/panel/usuarios')->with('aviso', 'No podés desactivar tu propia cuenta.');
        }

        $usuario->activo = ! $usuario->activo;
        $usuario->save();

        $estado = $usuario->activo ? 'activada' : 'desactivada';

        return redirect('/panel/usuarios')->with('aviso', 'Cuenta '.$estado.' para '.$usuario->name.'.');
    }

    public function eliminar(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect('/panel/usuarios')->with('aviso', 'No podés eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect('/panel/usuarios')->with('aviso', 'Cuenta eliminada.');
    }

    /**
     * Valida el formulario de alta/edición. Un alumno tiene que tener grupo
     * (si ya hay grupos creados) y no puede tener dos del mismo año.
     */
    private function validarUsuario(Request $request, ?User $usuario): array
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'apellido' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:180', Rule::unique('users', 'email')->ignore($usuario?->id)],
            'rol' => ['required', Rule::in(array_keys(User::ROLES))],
            'grupos' => ['nullable', 'array'],
            'grupos.*' => ['integer', Rule::exists('grupos', 'id')],
            'password' => [$usuario ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Los grupos inactivos son de solo lectura: no se suma a nadie nuevo,
        // y los que ya los tenían los conservan (no se pueden quitar).
        $pedidos = array_map('intval', $datos['grupos'] ?? []);
        $activos = Grupo::whereIn('id', $pedidos)->where('activo', true)->pluck('id')->all();
        $inactivosPrevios = $usuario
            ? $usuario->grupos()->where('activo', false)->pluck('grupos.id')->all()
            : [];
        $datos['grupos'] = array_values(array_unique(array_merge($activos, $inactivosPrevios)));

        if ($datos['rol'] === User::ROL_ALUMNO) {
            if (empty($datos['grupos']) && Grupo::where('activo', true)->exists()) {
                throw ValidationException::withMessages(['grupos' => 'Elegí el grupo del alumno.']);
            }
            if ($error = User::errorGruposAlumno($datos['grupos'])) {
                throw ValidationException::withMessages(['grupos' => $error]);
            }
        }

        return $datos;
    }
}
