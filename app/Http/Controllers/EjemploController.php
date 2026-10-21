<?php

namespace App\Http\Controllers;

use App\Models\Ejemplo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EjemploController extends Controller
{
    /** Extensiones que se aceptan como documento de ejemplo. */
    private const EXTENSIONES = ['docx', 'doc', 'odt', 'pdf'];

    public function crear()
    {
        return view('panel.ejemplo', [
            'ejemplo' => new Ejemplo(['orden' => (Ejemplo::max('orden') ?? 0) + 1, 'activo' => true]),
        ]);
    }

    public function guardar(Request $request)
    {
        $datos = $this->validar($request, archivoObligatorio: true);

        $ejemplo = new Ejemplo($datos);
        $this->guardarArchivo($request, $ejemplo);
        $ejemplo->save();

        return redirect('/panel#ejemplos')->with('aviso', 'Ejemplo agregado.');
    }

    public function editar(Ejemplo $ejemplo)
    {
        return view('panel.ejemplo', ['ejemplo' => $ejemplo]);
    }

    public function actualizar(Request $request, Ejemplo $ejemplo)
    {
        $datos = $this->validar($request, archivoObligatorio: false);

        $ejemplo->fill($datos);

        if ($request->hasFile('archivo')) {
            $this->borrarArchivo($ejemplo);
            $this->guardarArchivo($request, $ejemplo);
        }

        $ejemplo->save();

        return redirect('/panel#ejemplos')->with('aviso', 'Ejemplo actualizado.');
    }

    public function eliminar(Ejemplo $ejemplo)
    {
        $this->borrarArchivo($ejemplo);
        $ejemplo->delete();

        return redirect('/panel#ejemplos')->with('aviso', 'Ejemplo eliminado.');
    }

    private function validar(Request $request, bool $archivoObligatorio): array
    {
        $reglaArchivo = [
            $archivoObligatorio ? 'required' : 'nullable',
            'file',
            'mimes:'.implode(',', self::EXTENSIONES),
            'max:20480', // 20 MB
        ];

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'orden' => ['required', 'integer', 'min:0', 'max:999'],
            'archivo' => $reglaArchivo,
        ], [
            'archivo.mimes' => 'El archivo tiene que ser .docx, .doc, .odt o .pdf.',
            'archivo.max' => 'El archivo no puede pasar de 20 MB.',
        ]);

        unset($datos['archivo']);
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }

    private function guardarArchivo(Request $request, Ejemplo $ejemplo): void
    {
        $subido = $request->file('archivo');
        $extension = strtolower($subido->getClientOriginalExtension());

        // Nombre propio, nunca el del usuario: evita rutas raras y colisiones.
        $nombre = Str::slug(Str::limit($ejemplo->titulo, 60, '')).'-'.Str::lower(Str::random(6)).'.'.$extension;

        $carpeta = Ejemplo::carpeta();
        if (! is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $subido->move($carpeta, $nombre);

        $ejemplo->archivo = $nombre;
        $ejemplo->nombre_original = $subido->getClientOriginalName();
        $ejemplo->tamano = (int) filesize($carpeta.'/'.$nombre);
    }

    private function borrarArchivo(Ejemplo $ejemplo): void
    {
        $ruta = $ejemplo->rutaEnDisco();
        if ($ejemplo->archivo && is_file($ruta)) {
            unlink($ruta);
        }
    }
}
