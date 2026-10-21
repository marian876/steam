<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['paso', 'titulo', 'archivo', 'nombre_original', 'tamano', 'activo'])]
class Video extends Model
{
    protected $table = 'videos';

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'tamano' => 'integer',
        ];
    }

    /** Carpeta pública donde viven los videos del Formato STEAM. */
    public static function carpeta(): string
    {
        return base_path('ejemplos/formatoSteam/Videos');
    }

    public function rutaEnDisco(): string
    {
        return self::carpeta().'/'.$this->archivo;
    }

    public function url(): string
    {
        return '/ejemplos/formatoSteam/Videos/'.rawurlencode($this->archivo);
    }

    /** Tamaño legible: 12,4 MB */
    public function tamanoLegible(): string
    {
        $b = $this->tamano;
        if ($b <= 0) {
            return '—';
        }
        if ($b < 1024 * 1024) {
            return number_format($b / 1024, 0, ',', '.').' KB';
        }

        return number_format($b / 1024 / 1024, 1, ',', '.').' MB';
    }

    /** Título lindo de la página a la que pertenece este video. */
    public function tituloPagina(): string
    {
        return self::paginas()[$this->paso] ?? $this->titulo ?? $this->paso;
    }

    /**
     * Páginas del Formato STEAM para elegir a cuál se le carga el video.
     * La clave es el data-paso; el valor es el mismo título y número que
     * ve el alumno en la guía (ej. "15 · La carátula del colegio"), en el
     * orden en que aparecen.
     */
    public static function paginas(): array
    {
        return [
            'inicio' => 'Inicio',
            'tamano' => '1 · Tamaño y orientación de la página',
            'margenes' => '2 · Los márgenes',
            'que-es-estilo' => '3 · Qué es un estilo y por qué lo usamos',
            'base-estilos' => '4 · La fuente del trabajo',
            'normal' => '5 · Configurar el estilo Normal',
            't1' => '6 · Configurar el estilo Título 1',
            't2' => '7 · Configurar el estilo Título 2',
            't3' => '8 · Configurar el estilo Título 3',
            'estilos-tabla' => '9 · Configurar el estilo Sin espaciado',
            'aplicar' => '10 · Aplicar los estilos de texto',
            'capitalizacion' => '11 · Capitalización de los títulos',
            'estilo-de-tabla' => '12 · Configurar el estilo de la tabla',
            'aplicar-tablas' => '13 · Aplicar los estilos de tablas',
            'saltos' => '14 · Saltos de sección',
            'portada' => '15 · La carátula del colegio',
            'numeracion' => '16 · Encabezado, pie y numeración',
            'tdc-reserva' => '17 · Insertar la tabla de contenidos',
            'citas' => '18 · Citas textuales',
            'cita-ia' => '19 · Cita de texto generado por IA',
            'cita-tablas' => '20 · Referencia de Tablas',
            'cita-ilustraciones' => '21 · Ilustraciones según APA',
            'listado-optativo' => '22 · Tabla de tablas y de ilustraciones',
            'referencias' => '23 · Insertar la Bibliografía',
            'apendices' => '24 · Los apéndices',
            'actualizar-campos' => '25 · Antes de entregar: actualizá todos los campos',
            'comprobacion' => '26 · Criterios de evaluación',
            'datos-adicionales' => '27 · Uso ético de la IA',
            'glosario' => '★ · Glosario',
        ];
    }
}
