<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Prompt de la página Sitio web editado desde el panel (ver App\Support\PromptsSitioWeb). */
#[Fillable(['clave', 'titulo', 'descripcion', 'texto'])]
class PromptSitioWeb extends Model
{
    protected $table = 'prompts_sitio_web';
}
