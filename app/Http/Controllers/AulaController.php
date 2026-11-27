<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\User;
use App\Support\ConfiguracionAula;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AulaController extends Controller
{
    private function docente(Request $request): void
    {
        abort_unless($request->user()->esDocente(), 403);
    }

    private function acceder(Request $request, object $tarea, ?User $comoAlumno = null): void
    {
        $usuario = $comoAlumno ?? $request->user();
        if (! $comoAlumno && $usuario->esDocente()) {
            return;
        }
        abort_if(! ConfiguracionAula::admiteParciales() && mb_strtolower(trim($tarea->materia)) !== 'general', 404);
        abort_unless($usuario->esAlumno() && DB::table('aula_integrantes_autorizados')
            ->where('user_id', $usuario->id)->where('grupo_id', $tarea->grupo_id)->exists()
            && DB::table('grupo_user')->where('user_id', $usuario->id)->where('grupo_id', $tarea->grupo_id)->exists(), 404);
    }

    public function index(Request $request)
    {
        return $this->indicePara($request, $request->user());
    }

    public function trabajos(Request $request)
    {
        return $this->indicePara($request, $request->user(), false, true);
    }

    public function seleccionarAlumno(Request $request)
    {
        $this->docente($request);

        return view('panel.vista-alumno', ['alumnos' => User::where('rol', User::ROL_ALUMNO)->orderBy('name')->get()]);
    }

    public function vistaAlumno(Request $request, User $alumno)
    {
        $this->docente($request);
        abort_unless($alumno->esAlumno(), 404);

        return $this->indicePara($request, $alumno, true);
    }

    public function trabajosVistaAlumno(Request $request, User $alumno)
    {
        $this->docente($request);
        abort_unless($alumno->esAlumno(), 404);

        return $this->indicePara($request, $alumno, true, true);
    }

    private function indicePara(Request $request, User $user, bool $vistaAlumno = false, bool $seccionTrabajos = false)
    {
        $mes = $request->query('mes', now('America/Asuncion')->format('Y-m'));
        if (! is_string($mes) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
            $mes = now('America/Asuncion')->format('Y-m');
        }
        $tareas = DB::table('aula_tareas')->orderByRaw('vence_en IS NULL')->orderBy('vence_en');
        if (! $user->esDocente()) {
            $tareas->whereIn('grupo_id', DB::table('aula_integrantes_autorizados')->where('user_id', $user->id)->select('grupo_id'))
                ->whereIn('grupo_id', DB::table('grupo_user')->where('user_id', $user->id)->select('grupo_id'));
            if (! ConfiguracionAula::admiteParciales()) {
                $tareas->whereRaw('LOWER(TRIM(materia)) = ?', ['general']);
            }
        }
        $plazos = DB::table('aula_plazos')->orderBy('vence_en');
        if (! $user->esDocente()) {
            $plazos->where('curso', $user->curso ?? '');
        }

        $listaTareas = $tareas->get();
        $entregasGrupo = DB::table('aula_entregas')->join('users', 'users.id', '=', 'aula_entregas.alumno_id')
            ->whereIn('tarea_id', $listaTareas->pluck('id'))->orderByDesc('aula_entregas.id')
            ->select('aula_entregas.*', 'users.name as alumno_nombre', 'users.apellido as alumno_apellido')->get()->groupBy('tarea_id');
        $devolucionesGrupo = DB::table('aula_devoluciones')->join('users', 'users.id', '=', 'aula_devoluciones.docente_id')
            ->whereIn('entrega_id', $entregasGrupo->flatten(1)->pluck('id'))->orderByDesc('aula_devoluciones.id')
            ->select('aula_devoluciones.*', 'users.name as docente_nombre', 'users.apellido as docente_apellido')->get()->groupBy('entrega_id');

        return view('aula.index', [
            'tareas' => $listaTareas,
            'entregasGrupo' => $entregasGrupo,
            'devolucionesGrupo' => $devolucionesGrupo,
            'grupos' => $user->esDocente() ? Grupo::ordenados()->get() : collect(),
            'usuarios' => $user->esDocente() ? User::where('rol', User::ROL_ALUMNO)->orderBy('name')->get() : collect(),
            'mes' => Carbon::createFromFormat('!Y-m', $mes, 'America/Asuncion'),
            'plazos' => $plazos->get(),
            'vistaAlumno' => $vistaAlumno,
            'alumnoVista' => $vistaAlumno ? $user : null,
            'seccionTrabajos' => $seccionTrabajos,
            'admiteParciales' => ConfiguracionAula::admiteParciales(),
        ]);
    }

    public function configuracion()
    {
        return view('panel.configuracion-entregas', ['hayTabla' => Schema::hasTable('aula_configuracion'), 'admiteParciales' => ConfiguracionAula::admiteParciales()]);
    }

    public function actualizarConfiguracion(Request $request)
    {
        abort_unless(Schema::hasTable('aula_configuracion'), 503, 'Falta importar el SQL de configuración de entregas.');
        $datos = $request->validate(['modalidad' => ['required', Rule::in(['todas', 'solo_finales'])]]);
        DB::table('aula_configuracion')->updateOrInsert(['id' => 1], ['entregas_parciales' => $datos['modalidad'] === 'todas', 'updated_at' => now()]);

        return back()->with('aviso', 'Modalidad de entregas actualizada.');
    }

    public function calendario(Request $request)
    {
        $this->docente($request);

        return view('panel.calendario', ['plazos' => DB::table('aula_plazos')->orderBy('vence_en')->get(), 'cursos' => User::CURSOS]);
    }

    private function validarPlazo(Request $request): array
    {
        return $request->validate([
            'curso' => ['required', Rule::in(User::CURSOS)],
            'materia' => ['required', 'string', 'max:100'],
            'etapa' => ['required', 'string', 'max:180'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'vence_en' => ['required', 'date'],
        ]);
    }

    public function crearPlazo(Request $request)
    {
        $this->docente($request);
        $datos = $this->validarPlazo($request);
        $datos['docente_id'] = $request->user()->id;
        $datos['created_at'] = $datos['updated_at'] = now();
        DB::table('aula_plazos')->insert($datos);

        return redirect('/panel/calendario')->with('aviso', 'Plazo agregado al calendario.');
    }

    public function actualizarPlazo(Request $request, int $plazo)
    {
        $this->docente($request);
        $item = DB::table('aula_plazos')->where('id', $plazo)->first();
        abort_unless($item, 404);
        abort_unless($request->user()->esAdmin() || $item->docente_id === $request->user()->id, 403);
        $datos = $this->validarPlazo($request);
        $datos['updated_at'] = now();
        DB::table('aula_plazos')->where('id', $plazo)->update($datos);

        return back()->with('aviso', 'Plazo actualizado.');
    }

    public function eliminarPlazo(Request $request, int $plazo)
    {
        $this->docente($request);
        $item = DB::table('aula_plazos')->where('id', $plazo)->first();
        abort_unless($item, 404);
        abort_unless($request->user()->esAdmin() || $item->docente_id === $request->user()->id, 403);
        DB::table('aula_plazos')->where('id', $plazo)->delete();

        return back()->with('aviso', 'Plazo eliminado.');
    }

    public function crearTarea(Request $request)
    {
        $this->docente($request);
        if (! $request->has('grupos') && $request->filled('grupo_id')) {
            $request->merge(['grupos' => [$request->input('grupo_id')]]);
        }
        $datos = $request->validate([
            'grupos' => ['required', 'array', 'min:1'],
            'grupos.*' => ['required', 'integer', 'distinct', Rule::exists('grupos', 'id')->where('activo', true)],
            'titulo' => ['required', 'string', 'max:180'],
            'materia' => ['required', 'string', 'max:100'],
            'consigna' => ['required', 'string', 'max:10000'],
            'vence_en' => ['nullable', 'date'],
        ]);
        if (! ConfiguracionAula::admiteParciales() && mb_strtolower(trim($datos['materia'])) !== 'general') {
            throw ValidationException::withMessages(['materia' => 'La configuración permite solamente entregas de la sección General.']);
        }
        $grupos = $datos['grupos'];
        unset($datos['grupos']);
        $datos['docente_id'] = $request->user()->id;
        $datos['created_at'] = $datos['updated_at'] = now();
        DB::transaction(function () use ($grupos, $datos) {
            foreach ($grupos as $grupo) {
                DB::table('aula_tareas')->insert($datos + ['grupo_id' => $grupo]);
            }
        });

        return redirect('/aula')->with('aviso', 'Tarea creada para los grupos seleccionados.');
    }

    public function mostrar(Request $request, int $tarea)
    {
        return $this->mostrarPara($request, $tarea);
    }

    public function tareaVistaAlumno(Request $request, User $alumno, int $tarea)
    {
        $this->docente($request);
        abort_unless($alumno->esAlumno(), 404);

        return $this->mostrarPara($request, $tarea, $alumno);
    }

    private function mostrarPara(Request $request, int $tarea, ?User $alumnoVista = null)
    {
        $actividad = DB::table('aula_tareas')->where('id', $tarea)->first();
        abort_unless($actividad, 404);
        $this->acceder($request, $actividad, $alumnoVista);
        $entregas = DB::table('aula_entregas')->join('users', 'users.id', '=', 'aula_entregas.alumno_id')->where('tarea_id', $tarea)->orderByDesc('aula_entregas.id')->select('aula_entregas.*', 'users.name as alumno_nombre', 'users.apellido as alumno_apellido')->get();
        $devoluciones = DB::table('aula_devoluciones')->join('users', 'users.id', '=', 'aula_devoluciones.docente_id')->whereIn('entrega_id', $entregas->pluck('id'))->orderBy('aula_devoluciones.id')->select('aula_devoluciones.*', 'users.name as docente_nombre', 'users.apellido as docente_apellido')->get()->groupBy('entrega_id');

        return view('aula.tarea', compact('actividad', 'entregas', 'devoluciones', 'alumnoVista'));
    }

    public function entregar(Request $request, int $tarea)
    {
        $actividad = DB::table('aula_tareas')->where('id', $tarea)->first();
        abort_unless($actividad, 404);
        abort_unless($request->user()->esAlumno(), 403);
        $this->acceder($request, $actividad);
        abort_unless(Grupo::where('id', $actividad->grupo_id)->where('activo', true)->exists(), 403);
        $datos = $request->validate([
            'archivo' => ['required', 'file', 'mimes:pdf,docx,xlsx,pptx,png,jpg,jpeg', 'extensions:pdf,docx,xlsx,pptx,png,jpg,jpeg', 'max:20480'],
            'comentario' => ['nullable', 'string', 'max:5000'],
        ]);
        $archivo = null;
        try {
            DB::transaction(function () use ($request, $tarea, $datos, &$archivo) {
                $bloqueada = DB::table('aula_tareas')->where('id', $tarea)->lockForUpdate()->first();
                abort_unless($bloqueada, 404);
                $configuracion = Schema::hasTable('aula_configuracion') ? DB::table('aula_configuracion')->where('id', 1)->lockForUpdate()->first() : null;
                abort_if($configuracion && ! $configuracion->entregas_parciales && mb_strtolower(trim($bloqueada->materia)) !== 'general', 404);
                if (DB::table('aula_entregas')->where('tarea_id', $tarea)->exists() && ! $bloqueada->permite_nueva_entrega) {
                    throw ValidationException::withMessages(['archivo' => 'Este grupo ya entregó un archivo para esta tarea.']);
                }
                $archivo = $request->file('archivo')->store('entregas', 'local');
                abort_unless($archivo, 500);
                DB::table('aula_entregas')->insert([
                    'tarea_id' => $tarea, 'alumno_id' => $request->user()->id,
                    'archivo' => $archivo, 'nombre_original' => basename($request->file('archivo')->getClientOriginalName()),
                    'comentario' => $datos['comentario'] ?? null, 'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('aula_tareas')->where('id', $tarea)->update(['permite_nueva_entrega' => false, 'updated_at' => now()]);
            });
        } catch (\Throwable $error) {
            if ($archivo) {
                Storage::disk('local')->delete($archivo);
            }
            throw $error;
        }

        return back()->with('aviso', 'Entrega del grupo guardada.');
    }

    public function reabrir(Request $request, int $tarea)
    {
        $this->docente($request);
        DB::transaction(function () use ($tarea) {
            $actividad = DB::table('aula_tareas')->where('id', $tarea)->lockForUpdate()->first();
            abort_unless($actividad, 404);
            abort_unless(DB::table('aula_entregas')->where('tarea_id', $tarea)->exists(), 422, 'Todavía no hay una entrega para corregir.');
            abort_unless(Grupo::where('id', $actividad->grupo_id)->where('activo', true)->exists(), 403);
            DB::table('aula_tareas')->where('id', $tarea)->update(['permite_nueva_entrega' => true, 'updated_at' => now()]);
        });

        return back()->with('aviso', 'Se habilitó una nueva versión para el grupo.');
    }

    public function descargar(Request $request, int $entrega)
    {
        $item = DB::table('aula_entregas')->where('id', $entrega)->first();
        abort_unless($item, 404);
        $tarea = DB::table('aula_tareas')->where('id', $item->tarea_id)->first();
        abort_unless($tarea, 404);
        $this->acceder($request, $tarea);
        abort_unless(Storage::disk('local')->exists($item->archivo), 404);

        return Storage::disk('local')->download($item->archivo, $item->nombre_original);
    }

    public function devolver(Request $request, int $entrega)
    {
        $this->docente($request);
        abort_unless(DB::table('aula_entregas')->where('id', $entrega)->exists(), 404);
        $datos = $request->validate([
            'comentario' => ['nullable', 'string', 'max:10000', 'required_without_all:errores,calificacion'],
            'errores' => ['nullable', 'string', 'max:10000'],
            'calificacion' => ['nullable', 'string', 'max:100'],
        ]);
        DB::table('aula_devoluciones')->insert(['entrega_id' => $entrega, 'docente_id' => $request->user()->id, 'comentario' => $datos['comentario'] ?? '', 'errores' => $datos['errores'] ?? null, 'calificacion' => $datos['calificacion'] ?? null, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('aviso', 'Devolución guardada para el grupo.');
    }

    public function autorizar(Request $request)
    {
        $this->docente($request);
        $datos = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('rol', User::ROL_ALUMNO)],
            'grupo_id' => ['required', 'integer', Rule::exists('grupos', 'id')->where('activo', true)],
        ]);
        $alumno = User::findOrFail($datos['user_id']);
        $grupo = Grupo::findOrFail($datos['grupo_id']);
        if ($alumno->curso) {
            $esperado = (str_starts_with($grupo->curso, '2') ? '2do' : (str_starts_with($grupo->curso, '3') ? '3ro' : '')).' '.strtoupper($grupo->especialidad).' '.strtoupper($grupo->seccion);
            abort_unless($esperado === $alumno->curso, 422, 'El grupo no corresponde al curso registrado del alumno.');
        }
        $ids = $alumno->grupos()->pluck('grupos.id')->push($grupo->id)->unique()->all();
        if ($error = User::errorGruposAlumno($ids)) {
            return back()->withErrors(['grupo_id' => $error]);
        }
        DB::transaction(function () use ($alumno, $grupo, $request) {
            $alumno->grupos()->syncWithoutDetaching([$grupo->id]);
            DB::table('aula_integrantes_autorizados')->updateOrInsert(['user_id' => $alumno->id, 'grupo_id' => $grupo->id], ['autorizado_por' => $request->user()->id, 'updated_at' => now(), 'created_at' => now()]);
        });

        return back()->with('aviso', 'Grupo asignado y acceso al aula habilitado.');
    }
}
