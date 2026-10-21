<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['grupo_id', 'empresa', 'descripcion', 'sitio_web', 'ciencap', 'logros', 'activo'])]
class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'ciencap' => 'boolean',
        ];
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(ProyectoArchivo::class)->orderBy('nombre_original');
    }

    public function integrantes(): HasMany
    {
        return $this->hasMany(ProyectoIntegrante::class)->orderBy('orden');
    }

    /** Carpeta pública del proyecto: ejemplos/proyectos/{id}. */
    public function carpeta(): string
    {
        return base_path('ejemplos/proyectos/'.$this->id);
    }

    public function urlCarpeta(): string
    {
        return '/ejemplos/proyectos/'.$this->id;
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? $this->urlCarpeta().'/'.rawurlencode($this->logo) : null;
    }

    /** Un proyecto de un grupo inactivo es de solo lectura. */
    public function soloLectura(): bool
    {
        return $this->grupo && ! $this->grupo->activo;
    }

    /** Iniciales de la empresa, para cuando no hay logo. */
    public function iniciales(): string
    {
        return collect(preg_split('/\s+/', trim($this->empresa)))->filter()->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    }
}
