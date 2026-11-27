<?php

namespace App\Http\Controllers;

use App\Support\PromptsSitioWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ModuloController extends Controller
{
    public const MODULOS = ['contabilidad', 'marketing', 'business-administration', 'economics'];

    public function mostrar(string $pagina)
    {
        abort_unless(in_array($pagina, self::MODULOS, true), 404);
        $html = PromptsSitioWeb::de($pagina)->html();
        if (Schema::hasTable('archivos_modulos')) {
            $archivos = DB::table('archivos_modulos')->where('modulo', $pagina)->where('activo', true)->orderBy('id')->get();
            if ($archivos->isNotEmpty()) {
                $bloque = '<div class="cuerpo" style="margin-top:26px;max-width:none"><h3>Archivos para descargar</h3><ul>';
                foreach ($archivos as $archivo) {
                    $bloque .= '<li><a href="/materiales/'.(int) $archivo->id.'">'.e($archivo->titulo).'</a></li>';
                }
                $bloque .= '</ul></div>';
                $html = preg_replace('/<\/section>/', $bloque.'</section>', $html, 1);
            }
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function panel(string $pagina)
    {
        $guia = PromptsSitioWeb::de($pagina);
        abort_unless(in_array($pagina, self::MODULOS, true), 404);

        return view('panel.modulo', [
            'guia' => $guia, 'prompts' => $guia->actuales(),
            'hayPrompts' => PromptsSitioWeb::hayTabla(),
            'hayArchivos' => Schema::hasTable('archivos_modulos'),
            'archivos' => Schema::hasTable('archivos_modulos') ? DB::table('archivos_modulos')->where('modulo', $pagina)->orderByDesc('id')->get() : collect(),
        ]);
    }

    public function guardar(Request $request, string $pagina)
    {
        abort_unless(in_array($pagina, self::MODULOS, true), 404);
        abort_unless(Schema::hasTable('archivos_modulos'), 503, 'Falta importar el SQL de módulos.');
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'archivo' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp', 'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp', 'max:20480'],
        ]);
        $archivo = $request->file('archivo')->store('materiales-modulos', 'local');
        abort_unless($archivo, 500);
        DB::table('archivos_modulos')->insert([
            'modulo' => $pagina, 'titulo' => $datos['titulo'], 'archivo' => $archivo,
            'nombre_original' => basename($request->file('archivo')->getClientOriginalName()),
            'activo' => true, 'user_id' => $request->user()->id, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return back()->with('aviso', 'Archivo agregado a la guía.');
    }

    public function estado(string $pagina, int $archivo)
    {
        $item = DB::table('archivos_modulos')->where('modulo', $pagina)->where('id', $archivo)->first();
        abort_unless($item, 404);
        DB::table('archivos_modulos')->where('id', $archivo)->update(['activo' => ! $item->activo, 'updated_at' => now()]);

        return back()->with('aviso', 'Visibilidad del archivo actualizada.');
    }

    public function descargar(int $archivo)
    {
        abort_unless(Schema::hasTable('archivos_modulos'), 404);
        $item = DB::table('archivos_modulos')->where('id', $archivo)->where('activo', true)->first();
        abort_unless($item && Storage::disk('local')->exists($item->archivo), 404);

        return Storage::disk('local')->download($item->archivo, $item->nombre_original);
    }
}
