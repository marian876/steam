<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConfiguracionAula
{
    public static function admiteParciales(?string $curso = null): bool
    {
        if ($curso && Schema::hasTable('aula_configuracion_cursos')) {
            $especifico = DB::table('aula_configuracion_cursos')->where('curso', $curso)->value('entregas_parciales');
            if ($especifico !== null) {
                return (bool) $especifico;
            }
        }
        if (! Schema::hasTable('aula_configuracion')) {
            return true;
        }
        $valor = DB::table('aula_configuracion')->where('id', 1)->value('entregas_parciales');

        return $valor === null ? true : (bool) $valor;
    }

    public static function cursoGrupo(object $grupo): ?string
    {
        $nivel = str_starts_with((string) $grupo->curso, '2') ? '2do' : (str_starts_with((string) $grupo->curso, '3') ? '3ro' : null);

        return $nivel ? $nivel.' '.strtoupper(trim($grupo->especialidad)).' '.strtoupper(trim($grupo->seccion)) : null;
    }

    public static function permiteTarea(object $tarea): bool
    {
        if (mb_strtolower(trim($tarea->materia)) === 'general') {
            return true;
        }
        $grupo = DB::table('grupos')->where('id', $tarea->grupo_id)->first();

        return $grupo && self::admiteParciales(self::cursoGrupo($grupo));
    }
}
