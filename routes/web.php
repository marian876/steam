<?php

use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\SesionController;
use App\Http\Controllers\EjemploController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MisGruposController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PromptSitioWebController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ProyectosAnterioresController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\EsAdmin;
use App\Http\Middleware\EsDocente;
use App\Models\Ejemplo;
use App\Models\Prompt;
use App\Models\Video;
use App\Support\PromptsSitioWeb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

// Sitio del Formato Steam (SIL). Las páginas del formato son estáticas.
// Se sirven desde resources/site para que funcione en cualquier estructura de despliegue.

// Página inicial de presentación con las dos opciones
Route::get('/', function () {
    return response()->file(resource_path('site/inicio.html'));
});

// Opción 1: solo la información del formato (sin los pasos)
Route::get('/formato', function () {
    return response()->file(resource_path('site/formato-steam.html'));
});

// Visor de la muestra formateada (solo lectura, sin descarga ni copia)
Route::get('/muestra', function () {
    return response()->file(resource_path('site/muestra.html'));
});

// Muestra nueva (versión CFV). La /muestra anterior queda para los de 3.º.
Route::get('/muestra-nueva', function () {
    return response()->file(resource_path('site/muestra-nueva.html'));
});

// Defensa oral: diseño de la presentación.
// El prompt editado desde el panel se aplica sobre el HTML al servirlo.
Route::get('/defensa-oral', function () {
    return response(PromptsSitioWeb::de('defensa-oral')->html())->header('Content-Type', 'text/html; charset=UTF-8');
});

// Sitio web de la empresa · Proyecto STEAM BATAN (NotebookLM + Gemini).
Route::get('/sitio-web-batan', function () {
    return response(PromptsSitioWeb::de('sitio-web-batan')->html())->header('Content-Type', 'text/html; charset=UTF-8');
});

// Sitio web de la empresa: guía de diseño y evaluación.
// Los prompts editados desde el panel se aplican sobre el HTML al servirlo.
Route::get('/sitio-web', function () {
    return response(PromptsSitioWeb::de('sitio-web')->html())->header('Content-Type', 'text/html; charset=UTF-8');
});

/* ---------- Datos que consumen las páginas estáticas ---------- */

// Prompts de la página "Uso ético de la IA" (Formato STEAM)
Route::get('/api/prompts', function () {
    if (! Schema::hasTable('prompts')) {
        return []; // la página muestra los prompts que trae escritos
    }

    return Prompt::where('activo', true)
        ->orderBy('orden')->orderBy('id')
        ->get(['id', 'titulo', 'descripcion', 'texto']);
});

// Documentos de la página "Ejemplos"
Route::get('/api/ejemplos', function () {
    if (! Schema::hasTable('ejemplos')) {
        return []; // la página muestra el ejemplo que trae escrito
    }

    $haySinFormato = Schema::hasColumn('ejemplos', 'archivo_sin');

    return Ejemplo::where('activo', true)
        ->orderBy('orden')->orderBy('id')
        ->get()
        ->map(function ($e) use ($haySinFormato) {
            $fila = [
                'id' => $e->id,
                'titulo' => $e->titulo,
                'descripcion' => $e->descripcion,
                'url' => $e->url(),
                'nombre' => $e->nombre_original,
                'tamano' => $e->tamanoLegible(),
            ];

            if ($haySinFormato && $e->archivo_sin) {
                $fila['url_sin'] = $e->urlSin();
                $fila['nombre_sin'] = $e->nombre_sin;
                $fila['tamano_sin'] = $e->tamanoLegibleSin();
            }

            return $fila;
        });
});

// Videos por página del Formato STEAM. Devuelve { paso: {url, titulo} }.
Route::get('/api/videos', function () {
    if (! Schema::hasTable('videos')) {
        return (object) []; // todavía no se creó la tabla: no hay videos
    }

    return Video::where('activo', true)
        ->get()
        ->mapWithKeys(function ($v) {
            return [$v->paso => ['url' => $v->url(), 'titulo' => $v->tituloPagina()]];
        });
});

// Proyectos anteriores: tarjetas de la home y listado público de cada especialidad/año.
Route::get('/api/proyectos/colecciones', [ProyectosAnterioresController::class, 'colecciones']);
Route::get('/proyectos/{especialidad}/{anio}', [ProyectosAnterioresController::class, 'listado'])
    ->where(['especialidad' => '[A-Za-z0-9_-]+', 'anio' => '[0-9]{4}']);

// Quién está en sesión (para mostrar el enlace de ingreso o el nombre).
// Devuelve también el token CSRF, que la página estática necesita para el botón de salir.
Route::get('/api/yo', function () {
    $u = Auth::user();

    return $u
        ? ['autenticado' => true, 'nombre' => $u->name, 'rol' => $u->rol, 'docente' => $u->esDocente(), 'csrf' => csrf_token()]
        : ['autenticado' => false];
});

/* ---------- Cuentas ---------- */

Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegistroController::class, 'mostrar']);
    Route::post('/registro', [RegistroController::class, 'registrar']);
    Route::get('/ingresar', [SesionController::class, 'mostrar'])->name('login');
    Route::post('/ingresar', [SesionController::class, 'ingresar']);
});

Route::post('/salir', [SesionController::class, 'salir'])->middleware('auth');

// Mis grupos: el alumno ve sus grupos y elige el de un año nuevo.
Route::middleware('auth')->group(function () {
    Route::get('/mis-grupos', [MisGruposController::class, 'mostrar']);
    Route::post('/mis-grupos', [MisGruposController::class, 'agregar']);
});

/* ---------- Panel docente ---------- */

Route::middleware(['auth', EsDocente::class])->prefix('panel')->group(function () {
    Route::get('/', [PanelController::class, 'index']);
    Route::get('/prompts/nuevo', [PanelController::class, 'crear']);
    Route::post('/prompts', [PanelController::class, 'guardar']);
    Route::get('/prompts/{prompt}', [PanelController::class, 'editar']);
    Route::put('/prompts/{prompt}', [PanelController::class, 'actualizar']);
    Route::delete('/prompts/{prompt}', [PanelController::class, 'eliminar']);

    Route::get('/{pagina}/prompts/{clave}', [PromptSitioWebController::class, 'editar'])->whereIn('pagina', ['sitio-web', 'sitio-web-batan', 'defensa-oral']);
    Route::put('/{pagina}/prompts/{clave}', [PromptSitioWebController::class, 'actualizar'])->whereIn('pagina', ['sitio-web', 'sitio-web-batan', 'defensa-oral']);
    Route::delete('/{pagina}/prompts/{clave}', [PromptSitioWebController::class, 'restaurar'])->whereIn('pagina', ['sitio-web', 'sitio-web-batan', 'defensa-oral']);

    Route::get('/ejemplos/nuevo', [EjemploController::class, 'crear']);
    Route::post('/ejemplos', [EjemploController::class, 'guardar']);
    Route::get('/ejemplos/{ejemplo}', [EjemploController::class, 'editar']);
    Route::put('/ejemplos/{ejemplo}', [EjemploController::class, 'actualizar']);
    Route::delete('/ejemplos/{ejemplo}', [EjemploController::class, 'eliminar']);

    Route::get('/videos/nuevo', [VideoController::class, 'crear']);
    Route::post('/videos', [VideoController::class, 'guardar']);
    Route::get('/videos/{video}', [VideoController::class, 'editar']);
    Route::put('/videos/{video}', [VideoController::class, 'actualizar']);
    Route::delete('/videos/{video}', [VideoController::class, 'eliminar']);

    // Proyectos anteriores (empresa, logo, sitio web y archivos de cada grupo).
    Route::get('/proyectos', [ProyectoController::class, 'index']);
    Route::get('/proyectos/nuevo', [ProyectoController::class, 'crear']);
    Route::post('/proyectos', [ProyectoController::class, 'guardar']);
    Route::get('/proyectos/{proyecto}', [ProyectoController::class, 'editar']);
    Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'actualizar']);
    Route::put('/proyectos/{proyecto}/activo', [ProyectoController::class, 'toggleActivo']);
    Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'eliminar']);

    // Mi perfil: cualquiera que entre al panel puede cambiar sus datos.
    Route::get('/perfil', [PerfilController::class, 'mostrar']);
    Route::put('/perfil', [PerfilController::class, 'actualizar']);

    // Configuración › Usuarios y roles: solo administradores.
    Route::middleware(EsAdmin::class)->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::get('/usuarios/nuevo', [UsuarioController::class, 'crear']);
        Route::post('/usuarios', [UsuarioController::class, 'guardar']);
        Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'editar']);
        Route::put('/usuarios/{usuario}/rol', [UsuarioController::class, 'actualizarRol']);
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'actualizar']);
        Route::put('/usuarios/{usuario}/password', [UsuarioController::class, 'cambiarPassword']);
        Route::put('/usuarios/{usuario}/activo', [UsuarioController::class, 'toggleActivo']);
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'eliminar']);

        Route::get('/roles', [UsuarioController::class, 'roles']);

        Route::get('/grupos', [GrupoController::class, 'index']);
        Route::get('/grupos/nuevo', [GrupoController::class, 'crear']);
        Route::post('/grupos', [GrupoController::class, 'guardar']);
        Route::get('/grupos/{grupo}', [GrupoController::class, 'editar']);
        Route::get('/grupos/{grupo}/detalle', [GrupoController::class, 'detalle']);
        Route::put('/grupos/{grupo}/activo', [GrupoController::class, 'toggleActivo']);
        Route::put('/grupos/{grupo}', [GrupoController::class, 'actualizar']);
        Route::delete('/grupos/{grupo}', [GrupoController::class, 'eliminar']);
    });
});

/* ---------- Plantilla de Word (solo docentes) ---------- */
// El archivo vive en storage/ (bloqueado por .htaccess); solo se entrega
// a un usuario autenticado con rol docente.
Route::get('/plantilla-docente', function () {
    $ruta = storage_path('app/plantillas/Plantilla_Formato_STEAM.docx');
    abort_unless(is_file($ruta), 404);

    return response()->download($ruta, 'Plantilla_Formato_STEAM.docx');
})->middleware(['auth', EsDocente::class]);
