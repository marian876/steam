<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class GrupoController extends Controller
{
    public function index()
    {
        return view('panel.grupos', [
            'grupos' => Grupo::ordenados()->get(),
        ]);
    }

    /** Detalle del grupo: sus alumnos y docentes. */
    public function detalle(Grupo $grupo)
    {
        $integrantes = $grupo->usuarios()->orderBy('name')->get();

        return view('panel.grupo-detalle', [
            'grupo' => $grupo,
            'integrantes' => $integrantes,
            'alumnos' => $integrantes->where('rol', User::ROL_ALUMNO)->count(),
            'docentes' => $integrantes->whereIn('rol', [User::ROL_DOCENTE, User::ROL_ADMIN])->count(),
        ]);
    }

    /** Activar o desactivar el grupo (inactivo = solo lectura). */
    public function toggleActivo(Grupo $grupo)
    {
        $grupo->activo = ! $grupo->activo;
        $grupo->save();

        return redirect()->back()->with('aviso', 'Grupo '.$grupo->nombre().' '
            .($grupo->activo ? 'activado.' : 'desactivado: queda de solo lectura.'));
    }

    public function crear()
    {
        return view('panel.grupo', [
            'grupo' => new Grupo(['anio' => (int) date('Y'), 'activo' => true]),
            'especialidades' => $this->especialidades(),
        ]);
    }

    public function guardar(Request $request)
    {
        $grupo = Grupo::create($this->validar($request, null));

        return redirect('/panel/grupos')->with('aviso', 'Grupo '.$grupo->nombre().' creado.');
    }

    public function editar(Grupo $grupo)
    {
        return view('panel.grupo', [
            'grupo' => $grupo,
            'especialidades' => $this->especialidades(),
        ]);
    }

    public function actualizar(Request $request, Grupo $grupo)
    {
        // Un grupo inactivo es de solo lectura: lo único que se puede hacer es reactivarlo.
        if (! $grupo->activo) {
            $grupo->update(['activo' => $request->boolean('activo')]);

            return redirect('/panel/grupos')->with('aviso', $grupo->activo
                ? 'Grupo '.$grupo->nombre().' reactivado.'
                : 'El grupo '.$grupo->nombre().' está inactivo: es de solo lectura.');
        }

        $grupo->update($this->validar($request, $grupo->id));

        return redirect('/panel/grupos')->with('aviso', 'Grupo '.$grupo->nombre().' actualizado.');
    }

    public function eliminar(Grupo $grupo)
    {
        if (! $grupo->activo) {
            return redirect('/panel/grupos')->with('aviso', 'Un grupo inactivo no se puede eliminar: sus datos se conservan.');
        }

        if (Schema::hasTable('proyectos') && $grupo->proyecto()->exists()) {
            return redirect('/panel/grupos')->with('aviso', 'El grupo '.$grupo->nombre().' tiene un proyecto cargado: borrá primero el proyecto.');
        }

        $nombre = $grupo->nombre();
        $grupo->delete(); // quita también a los alumnos y docentes asignados

        return redirect('/panel/grupos')->with('aviso', 'Grupo '.$nombre.' eliminado.');
    }

    private function validar(Request $request, ?int $ignorarId): array
    {
        $request->merge([
            'curso' => trim((string) $request->input('curso')),
            'seccion' => strtoupper(trim((string) $request->input('seccion'))),
            'especialidad' => strtoupper(trim((string) $request->input('especialidad'))),
        ]);

        $datos = $request->validate([
            'curso' => ['required', 'string', 'max:10'],
            'seccion' => ['required', 'string', 'max:10'],
            'especialidad' => ['required', 'string', 'max:60'],
            'numero' => ['required', 'integer', 'min:1', 'max:99'],
            'anio' => [
                'required', 'integer', 'min:2020', 'max:2100',
                Rule::unique('grupos')->ignore($ignorarId)->where(fn ($q) => $q
                    ->where('curso', $request->input('curso'))
                    ->where('seccion', $request->input('seccion'))
                    ->where('especialidad', $request->input('especialidad'))
                    ->where('numero', $request->input('numero'))),
            ],
        ], [
            'anio.unique' => 'Ese grupo ya existe.',
        ]);

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }

    /** Especialidades ya usadas, para sugerirlas al escribir. */
    private function especialidades(): array
    {
        return Grupo::query()->distinct()->orderBy('especialidad')->pluck('especialidad')->all();
    }
}
