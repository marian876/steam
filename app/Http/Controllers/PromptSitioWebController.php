<?php

namespace App\Http\Controllers;

use App\Models\PromptSitioWeb;
use App\Support\PromptsSitioWeb;
use Illuminate\Http\Request;

/** Prompts de las guías Sitio web y Defensa oral, editables desde el panel. */
class PromptSitioWebController extends Controller
{
    public function editar(string $pagina, string $clave)
    {
        $guia = PromptsSitioWeb::de($pagina);
        $prompts = $guia->actuales();
        abort_unless(isset($prompts[$clave]), 404);

        return view('panel.prompt-sitio', [
            'guia' => $guia,
            'prompt' => $prompts[$clave],
            'automaticos' => $guia->automaticos($clave),
            'hayTabla' => PromptsSitioWeb::hayTabla(),
        ]);
    }

    public function actualizar(Request $request, string $pagina, string $clave)
    {
        $guia = PromptsSitioWeb::de($pagina);
        $originales = $guia->originales();
        abort_unless(isset($originales[$clave]), 404);

        if (! PromptsSitioWeb::hayTabla()) {
            return back()->withInput()->withErrors(['texto' => 'Falta crear la tabla en la base de datos: importá crear_prompts_sitio_web.sql en phpMyAdmin.']);
        }

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['required', 'string', 'max:1000'],
            'texto' => ['required', 'string', 'max:60000'],
        ]);
        $datos['texto'] = str_replace("\r\n", "\n", $datos['texto']);

        // La página reemplaza estos textos entre corchetes con las respuestas del alumno
        $faltan = array_filter($guia->automaticos($clave), fn ($c) => ! str_contains($datos['texto'], $c));
        if ($faltan) {
            return back()->withInput()->withErrors(['texto' => 'El texto tiene que seguir incluyendo, tal cual: '.implode(', ', $faltan)]);
        }

        $claveGuardada = $guia->claveGuardada($clave);
        if ($datos === array_intersect_key($originales[$clave], $datos)) {
            // Quedó igual al original: no hace falta guardarlo
            PromptSitioWeb::where('clave', $claveGuardada)->delete();
        } else {
            PromptSitioWeb::updateOrCreate(['clave' => $claveGuardada], $datos);
        }

        return redirect('/panel#'.$guia->pestana())->with('aviso', 'Prompt actualizado.');
    }

    public function restaurar(string $pagina, string $clave)
    {
        $guia = PromptsSitioWeb::de($pagina);
        if (PromptsSitioWeb::hayTabla()) {
            PromptSitioWeb::where('clave', $guia->claveGuardada($clave))->delete();
        }

        return redirect('/panel#'.$guia->pestana())->with('aviso', 'Se restauró el prompt original.');
    }
}
