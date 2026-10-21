<?php

namespace App\Http\Controllers;

use App\Models\Ejemplo;
use App\Models\Prompt;
use App\Models\Video;
use App\Support\PromptsSitioWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PanelController extends Controller
{
    public function index()
    {
        // En Hostinger las tablas se crean a mano desde phpMyAdmin:
        // si todavía no se importó la de ejemplos, el panel avisa en vez de romperse.
        $hayTablaEjemplos = Schema::hasTable('ejemplos');
        $hayTablaVideos = Schema::hasTable('videos');

        return view('panel.index', [
            'prompts' => Prompt::orderBy('orden')->orderBy('id')->get(),
            'ejemplos' => $hayTablaEjemplos ? Ejemplo::orderBy('orden')->orderBy('id')->get() : collect(),
            'hayTablaEjemplos' => $hayTablaEjemplos,
            'videos' => $hayTablaVideos ? Video::orderBy('id')->get() : collect(),
            'hayTablaVideos' => $hayTablaVideos,
            'paginasVideo' => Video::paginas(),
            'promptsSitio' => PromptsSitioWeb::de('sitio-web')->actuales(),
            'promptsDefensa' => PromptsSitioWeb::de('defensa-oral')->actuales(),
            'promptsBatan' => PromptsSitioWeb::de('sitio-web-batan')->actuales(),
            'hayTablaPromptsSitio' => PromptsSitioWeb::hayTabla(),
        ]);
    }

    public function crear()
    {
        return view('panel.prompt', [
            'prompt' => new Prompt(['orden' => (Prompt::max('orden') ?? 0) + 1, 'activo' => true]),
        ]);
    }

    public function guardar(Request $request)
    {
        Prompt::create($this->validar($request));

        return redirect('/panel#prompts')->with('aviso', 'Prompt agregado.');
    }

    public function editar(Prompt $prompt)
    {
        return view('panel.prompt', ['prompt' => $prompt]);
    }

    public function actualizar(Request $request, Prompt $prompt)
    {
        $prompt->update($this->validar($request));

        return redirect('/panel#prompts')->with('aviso', 'Prompt actualizado.');
    }

    public function eliminar(Prompt $prompt)
    {
        $prompt->delete();

        return redirect('/panel#prompts')->with('aviso', 'Prompt eliminado.');
    }

    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['required', 'string', 'max:1000'],
            'texto' => ['required', 'string'],
            'orden' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
