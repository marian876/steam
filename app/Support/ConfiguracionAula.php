<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConfiguracionAula
{
    public static function admiteParciales(): bool
    {
        if (! Schema::hasTable('aula_configuracion')) {
            return true;
        }
        $valor = DB::table('aula_configuracion')->where('id', 1)->value('entregas_parciales');

        return $valor === null ? true : (bool) $valor;
    }
}
