<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['curso', 'seccion', 'especialidad', 'numero', 'anio', 'activo'])]
class Grupo extends Model
{
    protected $table = 'grupos';

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'anio' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /** El proyecto (empresa) del grupo. */
    public function proyecto(): HasOne
    {
        return $this->hasOne(Proyecto::class);
    }

    /** Nombre del grupo: "2A BATAN Grupo 1 2026". */
    public function nombre(): string
    {
        return $this->curso.$this->seccion.' '.$this->especialidad.' Grupo '.$this->numero.' '.$this->anio;
    }

    /** Orden natural: año más nuevo primero, después curso, sección, especialidad y número. */
    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderByDesc('anio')->orderBy('curso')->orderBy('seccion')
            ->orderBy('especialidad')->orderBy('numero');
    }
}
