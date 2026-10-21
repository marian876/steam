<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Parte pública de "Proyectos anteriores". */
class ProyectosAnterioresController extends Controller
{
    /**
     * Una colección por especialidad y año que tenga grupos creados (ej. BATAN 2026),
     * con la cantidad de proyectos visibles. La home arma una tarjeta por cada una.
     */
    public function colecciones()
    {
        if (! Schema::hasTable('grupos')) {
            return [];
        }

        $hayProyectos = Schema::hasTable('proyectos');

        return DB::table('grupos')
            ->when($hayProyectos, fn ($q) => $q->leftJoin('proyectos', fn ($j) => $j
                ->on('proyectos.grupo_id', '=', 'grupos.id')->where('proyectos.activo', true)))
            ->groupBy('grupos.especialidad', 'grupos.anio')
            ->orderByDesc('grupos.anio')->orderBy('grupos.especialidad')
            ->selectRaw('grupos.especialidad as especialidad, grupos.anio as anio, '
                .($hayProyectos ? 'count(proyectos.id)' : '0').' as cantidad')
            ->get()
            ->map(fn ($c) => [
                'titulo' => $c->especialidad.' '.$c->anio,
                'cantidad' => (int) $c->cantidad,
                'url' => '/proyectos/'.rawurlencode(strtolower($c->especialidad)).'/'.$c->anio,
            ]);
    }

    /** Listado de los proyectos de una especialidad y un año. */
    public function listado(string $especialidad, int $anio)
    {
        abort_unless(Schema::hasTable('grupos'), 404);

        // La colección existe si hay al menos un grupo de esa especialidad y año.
        abort_unless(Grupo::where('anio', $anio)->whereRaw('lower(especialidad) = ?', [strtolower($especialidad)])->exists(), 404);

        $relaciones = ['grupo', 'archivos'];
        if (Schema::hasTable('proyecto_integrantes')) {
            $relaciones[] = 'integrantes.usuario';
        }

        $proyectos = ! Schema::hasTable('proyectos') ? collect() : Proyecto::with($relaciones)
            ->where('activo', true)
            ->whereHas('grupo', fn ($q) => $q->where('anio', $anio)->whereRaw('lower(especialidad) = ?', [strtolower($especialidad)]))
            ->get()
            ->sortBy(fn ($p) => [$p->grupo->curso.$p->grupo->seccion, $p->grupo->numero])
            ->values();

        return view('proyectos', [
            'titulo' => strtoupper($especialidad).' '.$anio,
            'proyectos' => $proyectos,
        ]);
    }
}
