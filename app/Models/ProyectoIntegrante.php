<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'nombre', 'orden'])]
class ProyectoIntegrante extends Model
{
    protected $table = 'proyecto_integrantes';

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Nombre a mostrar: el actual del usuario si está registrado, si no el escrito. */
    public function nombreMostrar(): string
    {
        return $this->usuario ? $this->usuario->nombreCompleto() : $this->nombre;
    }
}
