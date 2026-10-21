<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/** El alumno ve sus grupos y elige el de un año nuevo. */
class MisGruposController extends Controller
{
    public function mostrar()
    {
        abort_unless(Schema::hasTable('grupos'), 404);

        $usuario = Auth::user();
        $mios = $usuario->grupos()->ordenados()->get();

        return view('mis-grupos', [
            'usuario' => $usuario,
            'mios' => $mios,
            // Solo se ofrecen grupos activos de años que el alumno todavía no tiene.
            'disponibles' => Grupo::where('activo', true)
                ->whereNotIn('anio', $mios->pluck('anio'))
                ->ordenados()->get(),
        ]);
    }

    public function agregar(Request $request)
    {
        $usuario = Auth::user();

        $datos = $request->validate([
            'grupo' => ['required', 'integer', Rule::exists('grupos', 'id')->where('activo', true)],
        ], [
            'grupo.required' => 'Elegí tu grupo.',
        ]);

        $ids = $usuario->grupos()->pluck('grupos.id')->push((int) $datos['grupo'])->unique()->all();

        if ($usuario->rol === User::ROL_ALUMNO && ($error = User::errorGruposAlumno($ids))) {
            return redirect('/mis-grupos')->withErrors(['grupo' => $error]);
        }

        $usuario->grupos()->syncWithoutDetaching([(int) $datos['grupo']]);

        return redirect('/mis-grupos')->with('aviso', 'Grupo agregado.');
    }
}
