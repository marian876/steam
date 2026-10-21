<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VideoController extends Controller
{
    /** Formatos de video que se aceptan. */
    private const EXTENSIONES = ['mp4', 'webm', 'mov', 'm4v', 'ogv'];

    /** Tamaño máximo en KB (100 MB). Ojo: el hosting también limita la subida. */
    private const MAX_KB = 102400;

    public function crear()
    {
        return view('panel.video', [
            'video' => new Video(['activo' => true]),
            'paginas' => Video::paginas(),
            'ocupadas' => Video::pluck('paso')->all(),
        ]);
    }

    public function guardar(Request $request)
    {
        $datos = $this->validar($request, null, archivoObligatorio: true);

        $video = new Video($datos);
        $video->titulo = Video::paginas()[$datos['paso']] ?? $datos['paso'];
        $this->guardarArchivo($request, $video);
        $video->save();

        return redirect('/panel#videos')->with('aviso', 'Video agregado.');
    }

    public function editar(Video $video)
    {
        return view('panel.video', [
            'video' => $video,
            'paginas' => Video::paginas(),
            'ocupadas' => Video::where('id', '!=', $video->id)->pluck('paso')->all(),
        ]);
    }

    public function actualizar(Request $request, Video $video)
    {
        $datos = $this->validar($request, $video->id, archivoObligatorio: false);

        $video->fill($datos);
        $video->titulo = Video::paginas()[$datos['paso']] ?? $datos['paso'];

        if ($request->hasFile('archivo')) {
            $this->borrarArchivo($video);
            $this->guardarArchivo($request, $video);
        }

        $video->save();

        return redirect('/panel#videos')->with('aviso', 'Video actualizado.');
    }

    public function eliminar(Video $video)
    {
        $this->borrarArchivo($video);
        $video->delete();

        return redirect('/panel#videos')->with('aviso', 'Video eliminado.');
    }

    private function validar(Request $request, ?int $ignorarId, bool $archivoObligatorio): array
    {
        $reglaArchivo = [
            $archivoObligatorio ? 'required' : 'nullable',
            'file',
            'mimes:'.implode(',', self::EXTENSIONES),
            'max:'.self::MAX_KB,
        ];

        $datos = $request->validate([
            'paso' => [
                'required',
                Rule::in(array_keys(Video::paginas())),
                Rule::unique('videos', 'paso')->ignore($ignorarId),
            ],
            'archivo' => $reglaArchivo,
        ], [
            'paso.in' => 'Elegí una página válida.',
            'paso.unique' => 'Ya hay un video para esa página. Editá el que existe o elegí otra.',
            'archivo.mimes' => 'El video tiene que ser .mp4, .webm, .mov, .m4v u .ogv.',
            'archivo.max' => 'El video no puede pasar de 100 MB.',
        ]);

        unset($datos['archivo']);
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }

    private function guardarArchivo(Request $request, Video $video): void
    {
        $subido = $request->file('archivo');
        $extension = strtolower($subido->getClientOriginalExtension());

        // Nombre propio, nunca el del usuario: evita rutas raras y colisiones.
        $nombre = Str::slug($video->paso).'-'.Str::lower(Str::random(6)).'.'.$extension;

        $carpeta = Video::carpeta();
        if (! is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $subido->move($carpeta, $nombre);

        $video->archivo = $nombre;
        $video->nombre_original = $subido->getClientOriginalName();
        $video->tamano = (int) filesize($carpeta.'/'.$nombre);
    }

    private function borrarArchivo(Video $video): void
    {
        $ruta = $video->rutaEnDisco();
        if ($video->archivo && is_file($ruta)) {
            unlink($ruta);
        }
    }
}
