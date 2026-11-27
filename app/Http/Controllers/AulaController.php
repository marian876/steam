<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AulaController extends Controller
{
    private function docente(Request $request): void
    {
        abort_unless($request->user()->esDocente(), 403);
    }

    private function acceder(Request $request, object $tarea): void
    {
        if ($request->user()->esDocente()) {
            return;
        }
        abort_unless($request->user()->esAlumno() && DB::table('aula_integrantes_autorizados')
            ->where('user_id', $request->user()->id)->where('grupo_id', $tarea->grupo_id)->exists()
            && DB::table('grupo_user')->where('user_id', $request->user()->id)->where('grupo_id', $tarea->grupo_id)->exists(), 404);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $mes = $request->query('mes', now('America/Asuncion')->format('Y-m'));
        if (! is_string($mes) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
            $mes = now('America/Asuncion')->format('Y-m');
        }
        $tareas = DB::table('aula_tareas')->orderByRaw('vence_en IS NULL')->orderBy('vence_en');
        if (! $user->esDocente()) {
            $tareas->whereIn('grupo_id', DB::table('aula_integrantes_autorizados')->where('user_id', $user->id)->select('grupo_id'))
                ->whereIn('grupo_id', DB::table('grupo_user')->where('user_id', $user->id)->select('grupo_id'));
        }

        return view('aula.index', [
            'tareas' => $tareas->get(),
            'grupos' => $user->esDocente() ? Grupo::ordenados()->get() : collect(),
            'usuarios' => $user->esDocente() ? User::where('rol', User::ROL_ALUMNO)->orderBy('name')->get() : collect(),
            'mes' => Carbon::createFromFormat('!Y-m', $mes, 'America/Asuncion'),
        ]);
    }

    public function crearTarea(Request $request)
    {
        $this->docente($request);
        $datos = $request->validate([
            'grupo_id' => ['required', 'integer', Rule::exists('grupos', 'id')->where('activo', true)],
            'titulo' => ['required', 'string', 'max:180'],
            'materia' => ['required', 'string', 'max:100'],
            'consigna' => ['required', 'string', 'max:10000'],
            'vence_en' => ['nullable', 'date'],
        ]);
        $datos['docente_id'] = $request->user()->id;
        $datos['created_at'] = $datos['updated_at'] = now();
        DB::table('aula_tareas')->insert($datos);

        return redirect('/aula')->with('aviso', 'Tarea creada.');
    }

    public function mostrar(Request $request, int $tarea)
    {
        $actividad = DB::table('aula_tareas')->where('id', $tarea)->first();
        abort_unless($actividad, 404);
        $this->acceder($request, $actividad);
        $entregas = DB::table('aula_entregas')->where('tarea_id', $tarea)->orderByDesc('id')->get();
        $devoluciones = DB::table('aula_devoluciones')->whereIn('entrega_id', $entregas->pluck('id'))->orderBy('id')->get()->groupBy('entrega_id');

        return view('aula.tarea', compact('actividad', 'entregas', 'devoluciones'));
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
        $archivo = $request->file('archivo')->store('entregas', 'local');
        abort_unless($archivo, 500);
        DB::table('aula_entregas')->insert([
            'tarea_id' => $tarea, 'alumno_id' => $request->user()->id,
            'archivo' => $archivo, 'nombre_original' => basename($request->file('archivo')->getClientOriginalName()),
            'comentario' => $datos['comentario'] ?? null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return back()->with('aviso', 'Entrega del grupo guardada. Se conserva el historial de versiones.');
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
        $datos = $request->validate(['comentario' => ['required', 'string', 'max:10000']]);
        DB::table('aula_devoluciones')->insert(['entrega_id' => $entrega, 'docente_id' => $request->user()->id, 'comentario' => $datos['comentario'], 'created_at' => now(), 'updated_at' => now()]);

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
