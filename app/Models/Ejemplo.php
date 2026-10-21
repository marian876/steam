<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['titulo', 'descripcion', 'archivo', 'nombre_original', 'tamano', 'orden', 'activo'])]
class Ejemplo extends Model
{
    protected $table = 'ejemplos';

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
            'tamano' => 'integer',
        ];
    }

    /** Carpeta pública donde viven los archivos (ejemplos de contenido de la defensa oral). */
    public static function carpeta(): string
    {
        return base_path('ejemplos/defensaOral');
    }

    public function rutaEnDisco(): string
    {
        return self::carpeta().'/'.$this->archivo;
    }

    public function url(): string
    {
        return '/ejemplos/defensaOral/'.rawurlencode($this->archivo);
    }

    /** Tamaño legible: 3,4 MB */
    public function tamanoLegible(): string
    {
        $b = $this->tamano;
        if ($b <= 0) {
            return '—';
        }
        if ($b < 1024 * 1024) {
            return number_format($b / 1024, 0, ',', '.').' KB';
        }

        return number_format($b / 1024 / 1024, 1, ',', '.').' MB';
    }
}
