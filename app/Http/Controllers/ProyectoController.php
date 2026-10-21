<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProyectoController extends Controller
{
    /** Tipos de archivo que se pueden subir al proyecto. */
    private const EXTENSIONES = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'mp4'];

    public function index()
    {
        $hayTabla = Schema::hasTable('proyectos');

        return view('panel.proyectos', [
            'hayTabla' => $hayTabla,
            'hayIntegrantes' => Schema::hasTable('proyecto_integrantes'),
            'hayCiencap' => $hayTabla && Schema::hasColumn('proyectos', 'ciencap'),
            'proyectos' => $hayTabla
                ? Proyecto::with('grupo')->withCount(Schema::hasTable('proyecto_integrantes') ? ['archivos', 'integrantes'] : ['archivos'])->get()
                    ->sortBy(fn ($p) => [-$p->grupo->anio, $p->grupo->nombre()])->values()
                : collect(),
        ]);
    }

    public function crear()
    {
        abort_unless(Schema::hasTable('proyectos'), 404);

        return view('panel.proyecto', [
            'proyecto' => new Proyecto(['activo' => true]),
            'grupos' => $this->gruposDisponibles(null),
            'usuarios' => $this->usuariosParaIntegrantes(),
            'integrantes' => [],
            'hayIntegrantes' => Schema::hasTable('proyecto_integrantes'),
            'hayCiencap' => Schema::hasColumn('proyectos', 'ciencap'),
        ]);
    }

    public function guardar(Request $request)
    {
        $datos = $this->validar($request, null);

        $proyecto = Proyecto::create($datos);
        $this->guardarArchivos($request, $proyecto);
        $this->guardarIntegrantes($request, $proyecto);

        return redirect('/panel/proyectos')->with('aviso', 'Proyecto '.$proyecto->empresa.' cargado.'.$this->avisoIntegrantes($request));
    }

    public function editar(Proyecto $proyecto)
    {
        $hayIntegrantes = Schema::hasTable('proyecto_integrantes');

        return view('panel.proyecto', [
            'proyecto' => $proyecto->load('grupo', 'archivos'),
            'grupos' => $this->gruposDisponibles($proyecto),
            'usuarios' => $this->usuariosParaIntegrantes(),
            // Formato del formulario: "u:ID" si es un usuario registrado, o el nombre escrito.
            'integrantes' => $hayIntegrantes
                ? $proyecto->integrantes->map(fn ($i) => $i->user_id ? 'u:'.$i->user_id : $i->nombre)->all()
                : [],
            'hayIntegrantes' => $hayIntegrantes,
            'hayCiencap' => Schema::hasColumn('proyectos', 'ciencap'),
        ]);
    }

    public function actualizar(Request $request, Proyecto $proyecto)
    {
        if ($proyecto->soloLectura()) {
            return $this->avisoSoloLectura($proyecto);
        }

        $proyecto->update($this->validar($request, $proyecto));

        // Archivos marcados para quitar.
        $quitar = array_map('intval', (array) $request->input('archivos_quitar', []));
        foreach ($proyecto->archivos()->whereIn('id', $quitar)->get() as $archivo) {
            File::delete($proyecto->carpeta().'/'.$archivo->archivo);
            $archivo->delete();
        }
        if ($request->boolean('logo_quitar') && $proyecto->logo) {
            File::delete($proyecto->carpeta().'/'.$proyecto->logo);
            $proyecto->logo = null;
            $proyecto->save();
        }

        $this->guardarArchivos($request, $proyecto);
        $this->guardarIntegrantes($request, $proyecto);

        return redirect('/panel/proyectos')->with('aviso', 'Proyecto '.$proyecto->empresa.' actualizado.'.$this->avisoIntegrantes($request));
    }

    public function toggleActivo(Proyecto $proyecto)
    {
        if ($proyecto->soloLectura()) {
            return $this->avisoSoloLectura($proyecto);
        }

        $proyecto->activo = ! $proyecto->activo;
        $proyecto->save();

        return redirect('/panel/proyectos')->with('aviso', 'Proyecto '.$proyecto->empresa.' '
            .($proyecto->activo ? 'visible en el sitio.' : 'oculto en el sitio.'));
    }

    public function eliminar(Proyecto $proyecto)
    {
        if ($proyecto->soloLectura()) {
            return $this->avisoSoloLectura($proyecto);
        }

        $empresa = $proyecto->empresa;
        File::deleteDirectory($proyecto->carpeta());
        $proyecto->delete(); // borra también la lista de archivos

        return redirect('/panel/proyectos')->with('aviso', 'Proyecto '.$empresa.' eliminado.');
    }

    private function validar(Request $request, ?Proyecto $proyecto): array
    {
        $datos = $request->validate([
            'grupo_id' => [
                'required', 'integer',
                Rule::exists('grupos', 'id')->where('activo', true),
                Rule::unique('proyectos', 'grupo_id')->ignore($proyecto?->id),
            ],
            'empresa' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'sitio_web' => ['nullable', 'url:http,https', 'max:500'],
            'logros' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'integrantes' => ['nullable', 'array', 'max:30'],
            'integrantes.*' => ['string', 'max:150'],
            'archivos' => ['nullable', 'array'],
            'archivos.*' => ['file', 'mimes:'.implode(',', self::EXTENSIONES), 'max:20480'],
        ], [
            'grupo_id.exists' => 'Elegí un grupo activo.',
            'grupo_id.unique' => 'Ese grupo ya tiene un proyecto cargado.',
            'sitio_web.url' => 'El sitio web tiene que ser un enlace completo (https://…).',
            'logo.image' => 'El logo tiene que ser una imagen (JPG, PNG o WebP).',
            'logo.max' => 'El logo no puede pasar de 2 MB.',
            'archivos.*.mimes' => 'Tipo de archivo no permitido. Se aceptan PDF, Word, PowerPoint, Excel, imágenes, ZIP y MP4.',
            'archivos.*.max' => 'Cada archivo puede pesar hasta 20 MB.',
        ]);

        unset($datos['logo'], $datos['archivos'], $datos['integrantes']);
        $datos['activo'] = $request->boolean('activo');

        // Ciencap y logros solo se guardan si ya se importó agregar_ciencap_logros.sql.
        if (Schema::hasColumn('proyectos', 'ciencap')) {
            $datos['ciencap'] = $request->boolean('ciencap');
        } else {
            unset($datos['logros']);
        }

        return $datos;
    }

    /** Guarda el logo y los archivos nuevos en ejemplos/proyectos/{id}. */
    private function guardarArchivos(Request $request, Proyecto $proyecto): void
    {
        $carpeta = $proyecto->carpeta();
        File::ensureDirectoryExists($carpeta, 0755);

        if ($request->hasFile('logo')) {
            if ($proyecto->logo) {
                File::delete($carpeta.'/'.$proyecto->logo);
            }
            $proyecto->logo = $this->mover($request->file('logo'), $carpeta, 'logo');
            $proyecto->save();
        }

        foreach ((array) $request->file('archivos', []) as $subido) {
            $nombre = $this->mover($subido, $carpeta, pathinfo($subido->getClientOriginalName(), PATHINFO_FILENAME));
            $proyecto->archivos()->create([
                'archivo' => $nombre,
                'nombre_original' => $subido->getClientOriginalName(),
                'tamano' => (int) filesize($carpeta.'/'.$nombre),
            ]);
        }
    }

    /** Nombre propio (nunca el del usuario tal cual) para evitar rutas raras y choques. */
    private function mover(UploadedFile $subido, string $carpeta, string $base): string
    {
        $extension = strtolower($subido->getClientOriginalExtension());
        $nombre = Str::slug(Str::limit($base, 50, '')) ?: 'archivo';
        $nombre .= '-'.Str::lower(Str::random(6)).'.'.$extension;
        $subido->move($carpeta, $nombre);

        return $nombre;
    }

    /**
     * Guarda la lista de integrantes en el orden en que se cargaron.
     * Cada valor es "u:ID" (usuario registrado) o un nombre escrito a mano.
     */
    private function guardarIntegrantes(Request $request, Proyecto $proyecto): void
    {
        if (! Schema::hasTable('proyecto_integrantes')) {
            return;
        }

        $proyecto->integrantes()->delete();

        $orden = 0;
        $vistos = [];
        foreach ((array) $request->input('integrantes', []) as $valor) {
            $valor = trim((string) $valor);
            if ($valor === '' || isset($vistos[$valor])) {
                continue;
            }
            $vistos[$valor] = true;

            if (preg_match('/^u:(\d+)$/', $valor, $m) && ($usuario = User::find((int) $m[1]))) {
                $proyecto->integrantes()->create([
                    'user_id' => $usuario->id,
                    'nombre' => $usuario->nombreCompleto(),
                    'orden' => $orden++,
                ]);
            } elseif (! str_starts_with($valor, 'u:')) {
                $proyecto->integrantes()->create(['nombre' => $valor, 'orden' => $orden++]);
            }
        }
    }

    /** Si se cargaron integrantes pero falta la tabla, se avisa en vez de perderlos en silencio. */
    private function avisoIntegrantes(Request $request): string
    {
        if (! Schema::hasTable('proyecto_integrantes') && array_filter((array) $request->input('integrantes', []))) {
            return ' ATENCIÓN: los integrantes NO se guardaron porque falta importar crear_proyecto_integrantes.sql en phpMyAdmin.';
        }

        return '';
    }

    /** Usuarios que se ofrecen para elegir como integrantes (primero los alumnos). */
    private function usuariosParaIntegrantes(): array
    {
        $conGrupos = Schema::hasTable('grupos');

        return User::orderByRaw("rol = 'alumno' desc")->orderBy('name')
            ->when($conGrupos, fn ($q) => $q->with('grupos'))
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'nombre' => $u->nombreCompleto(),
                'rol' => $u->rol,
                'grupos' => $conGrupos ? $u->grupos->pluck('id')->all() : [],
                'detalle' => $conGrupos && $u->grupos->isNotEmpty()
                    ? $u->grupos->map->nombre()->implode(', ')
                    : $u->etiquetaRol(),
            ])->all();
    }

    /** Grupos activos que todavía no tienen proyecto (más el del propio proyecto al editar). */
    private function gruposDisponibles(?Proyecto $proyecto)
    {
        $ocupados = Proyecto::when($proyecto, fn ($q) => $q->where('id', '!=', $proyecto->id))->pluck('grupo_id');

        return Grupo::where(function ($q) use ($proyecto) {
            $q->where('activo', true);
            if ($proyecto) {
                $q->orWhere('id', $proyecto->grupo_id);
            }
        })->whereNotIn('id', $ocupados)->ordenados()->get();
    }

    private function avisoSoloLectura(Proyecto $proyecto)
    {
        return redirect('/panel/proyectos')->with('aviso', 'El proyecto '.$proyecto->empresa
            .' es de un grupo inactivo: es de solo lectura.');
    }
}
