<?php

namespace App\Support;

use App\Models\PromptSitioWeb;
use Illuminate\Support\Facades\Schema;

/**
 * Prompts editables desde el panel de las guías "Sitio web de la empresa" (SilEmprende y BATAN) y "Defensa oral".
 *
 * El texto original está escrito en resources/site/<pagina>.html, marcado con
 * data-prompt / data-prompt-titulo / data-prompt-desc. Lo que se edita en el
 * panel se guarda en la tabla prompts_sitio_web y se aplica sobre el HTML al
 * servir la página. Si una clave no tiene fila, se usa el original.
 *
 * En la tabla, las claves del sitio web se guardan tal cual (como siempre) y las
 * de las otras guías con el prefijo "<pagina>:".
 */
class PromptsSitioWeb
{
    /** Guías con prompts editables: página => nombre para el panel. */
    public const PAGINAS = [
        'sitio-web' => 'Sitio web de la empresa',
        'sitio-web-batan' => 'Sitio web · BATAN',
        'defensa-oral' => 'Defensa oral',
    ];

    /** Textos entre corchetes que la página completa sola: no se pueden borrar. */
    public const AUTOMATICOS = [
        'sitio-web' => [
            'contenido-proyecto-1' => ['[PEGÁ AQUÍ TUS RESPUESTAS]'],
            'diseno-1' => ['[NOMBRE DE LA EMPRESA]', '[DESCRIPCIÓN CLARA DEL PROYECTO]'],
            'diseno-2' => ['[DESCRIPCIÓN CLARA DEL PROYECTO]', '[PALETA APROBADA]', '[MAPA Y CONTENIDOS]'],
            'diseno-3' => ['[DESCRIPCIÓN CLARA DEL PROYECTO]', '[MAPA COMPLETO DEL SITIO]', '[PALETA APROBADA]', '[ESTILO VISUAL Y TIPOGRAFÍAS]'],
            'contenido-paginas' => ['[PEGÁ AQUÍ EL TEXTO COPIADO DEL LISTADO DE CONTENIDOS Y PÁGINAS]', '[PEGÁ AQUÍ LA INFORMACIÓN DISPONIBLE]'],
            'canva' => [
                '[IDENTIDAD VISUAL APROBADA]', '[MAPA APROBADO]', '[ESTILO ELEGIDO]', '[TIPO DE MENÚ ELEGIDO]',
                '[COMPORTAMIENTO DEL MENÚ ELEGIDO]', '[ANIMACIONES ELEGIDAS]', '[TRANSICIONES ELEGIDAS]',
                '[COMPOSICIONES ELEGIDAS]', '[IMÁGENES ELEGIDAS]', '[HERRAMIENTAS ELEGIDAS]',
                '[NOMBRE Y APELLIDO]',
            ],
            'refinar' => ['[ANÁLISIS DEL PROTOTIPO]'],
            'datos-reales' => ['[DATOS REALES]'],
        ],
        'sitio-web-batan' => [
            'notebook-contenido' => ['[OBJETIVO DEL SITIO]', '[PÚBLICO]', '[PÁGINAS DEL SITIO]'],
            'gemini-sitio' => ['[OBJETIVO DEL SITIO]', '[PÚBLICO]', '[SECCIONES Y PÁGINAS]', '[INFORMACIÓN DEL SITIO]', '[FUNCIONALIDADES]', '[DISEÑO]', '[NOMBRE Y APELLIDO]'],
            'gemini-paleta' => ['[NOMBRE DE LA EMPRESA]', '[OBJETIVO DEL SITIO]'],
            'gemini-estilo' => ['[PALETA APROBADA]', '[OBJETIVO DEL SITIO]', '[PÚBLICO]'],
            'gemini-tienda' => ['[PRODUCTOS]'],
        ],
        'defensa-oral' => [],
    ];

    private const ATRIBUTOS = '((?:\s+[\w-]+(?:="[^"]*")?)*)';

    public function __construct(public readonly string $pagina) {}

    public static function de(string $pagina): self
    {
        abort_unless(isset(self::PAGINAS[$pagina]), 404);

        return new self($pagina);
    }

    public static function hayTabla(): bool
    {
        return Schema::hasTable('prompts_sitio_web');
    }

    public function nombre(): string
    {
        return self::PAGINAS[$this->pagina];
    }

    /** Pestaña del panel donde se listan estos prompts. */
    public function pestana(): string
    {
        return $this->pagina === 'sitio-web' ? 'sitio-web' : $this->pagina.'-prompts';
    }

    /** @return string[] */
    public function automaticos(string $clave): array
    {
        return self::AUTOMATICOS[$this->pagina][$clave] ?? [];
    }

    /** Clave con la que se guarda en la tabla. */
    public function claveGuardada(string $clave): string
    {
        return $this->pagina === 'sitio-web' ? $clave : $this->pagina.':'.$clave;
    }

    private function archivo(): string
    {
        return resource_path('site/'.$this->pagina.'.html');
    }

    /**
     * Prompts tal como están escritos en la página, en orden.
     *
     * @return array<string, array{clave: string, pagina: string, titulo: string, descripcion: string, texto: string}>
     */
    public function originales(): array
    {
        $html = file_get_contents($this->archivo());
        $paginas = self::paginas($html);
        $prompts = [];

        preg_match_all('/<pre'.self::ATRIBUTOS.'\s*>(.*?)<\/pre>/s', $html, $pres, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($pres as $pre) {
            $atributos = $pre[1][0];
            if (! preg_match('/\sdata-prompt="([^"]+)"/', $atributos, $m)) {
                continue;
            }
            $clave = $m[1];

            // La sección que contiene el prompt
            preg_match_all('/<section class="pantalla[^"]*" id="([^"]+)"/', substr($html, 0, $pre[0][1]), $secciones);
            $seccion = end($secciones[1]) ?: '';

            $texto = preg_match('/\sdata-plantilla="([^"]*)"/', $atributos, $p) ? $p[1] : $pre[2][0];

            $prompts[$clave] = [
                'clave' => $clave,
                'pagina' => $paginas[$seccion] ?? $seccion,
                'titulo' => self::textoPlano(self::interior($html, 'span', 'data-prompt-titulo', $clave)),
                'descripcion' => self::textoPlano(self::interior($html, 'p', 'data-prompt-desc', $clave)),
                'texto' => self::decodificar($texto),
            ];
        }

        return $prompts;
    }

    /**
     * Filas editadas en el panel para esta guía, por clave de la página.
     *
     * @return array<string, PromptSitioWeb>
     */
    private function cambios(): array
    {
        if (! self::hayTabla()) {
            return [];
        }

        $claves = array_keys($this->originales());
        $filas = PromptSitioWeb::whereIn('clave', array_map(fn ($c) => $this->claveGuardada($c), $claves))
            ->get()->keyBy('clave');

        $cambios = [];
        foreach ($claves as $clave) {
            if ($fila = $filas->get($this->claveGuardada($clave))) {
                $cambios[$clave] = $fila;
            }
        }

        return $cambios;
    }

    /**
     * Originales con lo editado en el panel encima.
     *
     * @return array<string, array{clave: string, pagina: string, titulo: string, descripcion: string, texto: string, modificado: bool}>
     */
    public function actuales(): array
    {
        $cambios = $this->cambios();

        return array_map(function (array $original) use ($cambios) {
            $cambio = $cambios[$original['clave']] ?? null;

            return $cambio
                ? array_merge($original, $cambio->only(['titulo', 'descripcion', 'texto']), ['modificado' => true])
                : $original + ['modificado' => false];
        }, $this->originales());
    }

    /** HTML de la página con los prompts editados en el panel. */
    public function html(): string
    {
        $html = file_get_contents($this->archivo());

        foreach ($this->cambios() as $clave => $cambio) {
            $html = self::aplicar($html, $clave, $cambio);
        }

        return $html;
    }

    private static function aplicar(string $html, string $clave, PromptSitioWeb $cambio): string
    {
        $claveRx = preg_quote($clave, '/');

        $html = preg_replace_callback(
            '/<pre'.self::ATRIBUTOS.'\s*>(.*?)<\/pre>/s',
            function ($m) use ($clave, $cambio) {
                if (! str_contains($m[1], ' data-prompt="'.$clave.'"')) {
                    return $m[0];
                }

                // Los prompts que la página completa sola guardan el texto en data-plantilla
                if (str_contains($m[1], ' data-plantilla="')) {
                    $atributos = preg_replace_callback('/\sdata-plantilla="[^"]*"/', fn () => ' data-plantilla="'.e($cambio->texto).'"', $m[1]);

                    return '<pre'.$atributos.'></pre>';
                }

                return '<pre'.$m[1].'>'.htmlspecialchars($cambio->texto, ENT_NOQUOTES, 'UTF-8').'</pre>';
            },
            $html
        );

        $html = preg_replace_callback(
            '/(<span[^>]*\sdata-prompt-titulo="'.$claveRx.'"[^>]*>).*?(<\/span>)/s',
            fn ($m) => $m[1].e($cambio->titulo).$m[2],
            $html
        );

        return preg_replace_callback(
            '/(<p[^>]*\sdata-prompt-desc="'.$claveRx.'"[^>]*>).*?(<\/p>)/s',
            fn ($m) => $m[1].e($cambio->descripcion).$m[2],
            $html
        );
    }

    /** Nombre de cada página de la guía con su número, como en el índice. */
    private static function paginas(string $html): array
    {
        preg_match_all('/<section class="pantalla[^"]*" id="([^"]+)"([^>]*)>(.*?)<\/section>/s', $html, $secciones, PREG_SET_ORDER);
        preg_match("/var ordenEtapas = \[([^\]]*)\]/", $html, $orden);
        $orden = isset($orden[1]) ? array_map(fn ($s) => trim($s, " '\""), explode(',', $orden[1])) : [];

        $secciones = array_values(array_filter($secciones, fn ($s) => $s[1] !== 'inicio'));
        if ($orden) {
            usort($secciones, fn ($a, $b) => array_search($a[1], $orden) <=> array_search($b[1], $orden));
        }

        $paginas = [];
        $numero = 0;
        foreach ($secciones as [, $id, $atributos, $contenido]) {
            $titulo = preg_match('/data-titulo="([^"]*)"/', $atributos, $t)
                ? self::decodificar($t[1])
                : (preg_match('/<h2[^>]*>(.*?)<\/h2>/s', $contenido, $h) ? self::textoPlano($h[1]) : $id);
            // Las adicionales no llevan número (su título ya dice "Adicional: …")
            $paginas[$id] = str_contains($atributos, 'data-adicional="true"') ? $titulo : sprintf('%02d', ++$numero).' · '.$titulo;
        }

        return $paginas;
    }

    private static function interior(string $html, string $etiqueta, string $atributo, string $clave): string
    {
        $patron = '/<'.$etiqueta.'[^>]*\s'.$atributo.'="'.preg_quote($clave, '/').'"[^>]*>(.*?)<\/'.$etiqueta.'>/s';

        return preg_match($patron, $html, $m) ? $m[1] : '';
    }

    private static function textoPlano(string $html): string
    {
        return trim(preg_replace('/\s+/u', ' ', self::decodificar(strip_tags($html))));
    }

    private static function decodificar(string $texto): string
    {
        return html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
