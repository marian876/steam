<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['archivo', 'nombre_original', 'tamano'])]
class ProyectoArchivo extends Model
{
    protected $table = 'proyecto_archivos';

    protected function casts(): array
    {
        return [
            'tamano' => 'integer',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function url(): string
    {
        return '/ejemplos/proyectos/'.$this->proyecto_id.'/'.rawurlencode($this->archivo);
    }

    /** Extensión en mayúsculas: PDF, DOCX, PPTX… */
    public function tipo(): string
    {
        return strtoupper(pathinfo($this->nombre_original, PATHINFO_EXTENSION) ?: 'ARCHIVO');
    }

    /** Tamaño legible: 3,4 MB */
    public function tamanoLegible(): string
    {
        $b = $this->tamano;
        if ($b <= 0) {
            return '—';
        }
        if ($b < 1024 * 1024) {
            return number_format(max(1, $b / 1024), 0, ',', '.').' KB';
        }

        return number_format($b / 1024 / 1024, 1, ',', '.').' MB';
    }
}
