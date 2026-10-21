@extends('layouts.panel')
@section('titulo', 'Panel')
@section('panel_activo', 'inicio')

@section('contenido')
@php($ejemplos = $ejemplos ?? collect())
@php($hayTablaEjemplos = $hayTablaEjemplos ?? true)
@php($videos = $videos ?? collect())
@php($hayTablaVideos = $hayTablaVideos ?? true)
@php($paginasVideo = $paginasVideo ?? [])
@php($promptsSitio = $promptsSitio ?? [])
@php($promptsDefensa = $promptsDefensa ?? [])
@php($promptsBatan = $promptsBatan ?? [])
@php($hayTablaPromptsSitio = $hayTablaPromptsSitio ?? true)

<style>
  .bienvenida {
    display: flex;
    align-items: center;
    gap: 20px;
    background: linear-gradient(120deg, var(--azul-suave), var(--superficie));
    border: 1px solid var(--linea);
    border-radius: var(--r-l);
    padding: 24px 26px;
    margin-bottom: 26px;
    overflow: hidden;
  }

  .bienvenida .eyebrow {
    font-family: var(--mono);
    font-size: 11px;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--azul);
    font-weight: 600;
    margin-bottom: 4px;
  }

  .bienvenida h1 {
    margin: 0 0 6px
  }

  .bienvenida p {
    margin: 0;
    color: var(--tinta2);
    max-width: 52ch
  }

  .bienvenida-img {
    margin-left: auto;
    height: 92px;
    width: auto;
    border-radius: var(--r-m);
    object-fit: cover;
    flex: none;
  }

  @media (max-width: 640px) {
    .bienvenida-img {
      display: none
    }
  }

  .panel-panel[hidden] {
    display: none
  }

  .resumen {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 14px;
  }

  .resumen-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
    background: var(--superficie);
    border: 1px solid var(--linea);
    border-radius: var(--r-l);
    box-shadow: var(--sombra);
    padding: 18px 20px;
    text-decoration: none;
    color: var(--tinta);
    transition: border-color .18s;
  }

  .resumen-item:hover {
    border-color: var(--azul)
  }

  .resumen-num {
    font-family: var(--display);
    font-size: 1.9rem;
    font-weight: 800;
    color: var(--azul);
    line-height: 1.1
  }

  .resumen-tit {
    font-weight: 700
  }

  .resumen-sec {
    font-family: var(--mono);
    font-size: 10px;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--gris)
  }
</style>

{{-- ---------------- INICIO (dashboard) ---------------- --}}
<section class="panel-panel" data-panel="inicio" hidden>
  <div class="bienvenida">
    <div>
      <div class="eyebrow">Bienvenida</div>
      <h1>Panel docente</h1>
      <p>Acá gestionás los contenidos que se publican en el sitio del proyecto.</p>
    </div>
    <img class="bienvenida-img" src="/logos/banner-steam.webp" alt="" aria-hidden="true">
  </div>

  <div class="resumen">
    <a class="resumen-item" href="/panel#prompts">
      <span class="resumen-num">{{ $prompts->count() }}</span>
      <span class="resumen-tit">Prompts</span>
      <span class="resumen-sec">Formato STEAM</span>
    </a>
    <a class="resumen-item" href="/panel#videos">
      <span class="resumen-num">{{ $videos->count() }}</span>
      <span class="resumen-tit">Videos</span>
      <span class="resumen-sec">Formato STEAM</span>
    </a>
    <a class="resumen-item" href="/panel#ejemplos">
      <span class="resumen-num">{{ $ejemplos->count() }}</span>
      <span class="resumen-tit">Ejemplos de contenido</span>
      <span class="resumen-sec">Defensa oral</span>
    </a>
    <a class="resumen-item" href="/panel#defensa-oral-prompts">
      <span class="resumen-num">{{ count($promptsDefensa) }}</span>
      <span class="resumen-tit">Prompts</span>
      <span class="resumen-sec">Defensa oral</span>
    </a>
    <a class="resumen-item" href="/panel#sitio-web">
      <span class="resumen-num">{{ count($promptsSitio) }}</span>
      <span class="resumen-tit">Prompts</span>
      <span class="resumen-sec">Sitio web · SilEmprende</span>
    </a>
    <a class="resumen-item" href="/panel#sitio-web-batan-prompts">
      <span class="resumen-num">{{ count($promptsBatan) }}</span>
      <span class="resumen-tit">Prompts</span>
      <span class="resumen-sec">Sitio web · BATAN</span>
    </a>
  </div>
</section>

{{-- ---------------- PROMPTS ---------------- --}}
<section class="panel-panel" data-panel="prompts" hidden>
  <div class="enc-panel">
    <div>
      <h2 class="titulo-seccion">Prompts</h2>
      <p class="bajada" style="margin:0">Aparecen en <a href="/formato#datos-adicionales">Uso ético de la IA</a>.</p>
    </div>
    <a class="boton btn-p" href="/panel/prompts/nuevo">Agregar prompt</a>
  </div>

  @if($prompts->isNotEmpty())
  <div class="tbl-toolbar" data-tabla="tabla-prompts">
    <label class="tbl-buscar">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
      <input type="search" placeholder="Buscar prompt…" autocomplete="off">
    </label>
    <select data-filtro="estado" aria-label="Filtrar por estado">
      <option value="">Todos los estados</option>
      <option value="visible">Visibles</option>
      <option value="oculto">Ocultos</option>
    </select>
    <span class="tbl-cuenta"></span>
  </div>
  @endif

  <div class="tarjeta" style="padding:22px 14px">
    @if($prompts->isEmpty())
    <p style="margin:10px 12px;color:var(--tinta2)">Todavía no hay prompts cargados.</p>
    @else
    <table class="lista ordenable" id="tabla-prompts">
      <thead>
        <tr>
          <th data-orden="orden" style="width:56px">Orden</th>
          <th data-orden="titulo">Prompt</th>
          <th data-orden="estado" style="width:90px">Estado</th>
          <th style="width:90px;text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($prompts as $prompt)
        <tr data-orden="{{ $prompt->orden }}" data-titulo="{{ $prompt->titulo }}" data-estado="{{ $prompt->activo ? 'visible' : 'oculto' }}">
          <td style="font-family:var(--mono);color:var(--gris)">{{ $prompt->orden }}</td>
          <td>
            <strong>{{ $prompt->titulo }}</strong>
            <div style="color:var(--tinta2);font-size:.85rem;margin-top:3px">
              {{ Str::limit($prompt->descripcion, 120) }}</div>
          </td>
          <td><span class="etq {{ $prompt->activo ? 'si' : 'no' }}">{{ $prompt->activo ? 'Visible' : 'Oculto' }}</span>
          </td>
          <td>
            <div class="ico-acciones">
              <a class="ico-btn" href="/panel/prompts/{{ $prompt->id }}" title="Editar" aria-label="Editar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></a>
              <form method="POST" action="/panel/prompts/{{ $prompt->id }}" style="margin:0"
                onsubmit="return confirm('¿Eliminar este prompt? No se puede deshacer.')">
                @csrf @method('DELETE')
                <button class="ico-btn peligro" type="submit" title="Eliminar" aria-label="Eliminar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
</section>

{{-- ---------------- VIDEOS ---------------- --}}
<section class="panel-panel" data-panel="videos" hidden>
  <div class="enc-panel">
    <div>
      <h2 class="titulo-seccion">Videos (Formato STEAM)</h2>
      <p class="bajada" style="margin:0">Un video explicativo para cada página del <a href="/formato">Formato
          STEAM</a>. En esa página aparece un botón de video al lado del foquito.</p>
    </div>
    @if($hayTablaVideos)
    <a class="boton btn-p" href="/panel/videos/nuevo">Agregar video</a>
    @endif
  </div>

  @if($hayTablaVideos && $videos->isNotEmpty())
  <div class="tbl-toolbar" data-tabla="tabla-videos">
    <label class="tbl-buscar">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
      <input type="search" placeholder="Buscar página o archivo…" autocomplete="off">
    </label>
    <select data-filtro="estado" aria-label="Filtrar por estado">
      <option value="">Todos los estados</option>
      <option value="visible">Visibles</option>
      <option value="oculto">Ocultos</option>
    </select>
    <span class="tbl-cuenta"></span>
  </div>
  @endif

  <div class="tarjeta" style="padding:22px 14px">
    @if(! $hayTablaVideos)
    <div class="errores" style="margin:4px 12px">
      <strong>Falta crear la tabla de videos en la base de datos.</strong><br>
      Entrá a phpMyAdmin, abrí la base del sitio e importá el archivo <code>crear_videos.sql</code>.
      No borra nada de lo que ya está cargado.
    </div>
    @elseif($videos->isEmpty())
    <p style="margin:10px 12px;color:var(--tinta2)">Todavía no hay videos cargados.</p>
    @else
    <table class="lista ordenable" id="tabla-videos">
      <thead>
        <tr>
          <th data-orden="pagina">Página</th>
          <th data-orden="archivo">Archivo</th>
          <th data-orden="tamano" style="width:90px">Tamaño</th>
          <th data-orden="estado" style="width:90px">Estado</th>
          <th style="width:90px;text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($videos as $video)
        <tr data-pagina="{{ $paginasVideo[$video->paso] ?? $video->titulo ?? $video->paso }}" data-archivo="{{ $video->nombre_original }}" data-tamano="{{ $video->tamano }}" data-estado="{{ $video->activo ? 'visible' : 'oculto' }}">
          <td>
            <strong>{{ $paginasVideo[$video->paso] ?? $video->titulo ?? $video->paso }}</strong>
            <div style="color:var(--tinta2);font-size:.78rem;margin-top:3px;font-family:var(--mono)">
              {{ $video->paso }}</div>
          </td>
          <td>
            @if($video->archivo)
            <a href="{{ $video->url() }}" target="_blank" rel="noopener"
              style="font-family:var(--mono);font-size:.78rem">{{ $video->nombre_original }}</a>
            @else
            <span style="font-family:var(--mono);font-size:.78rem;color:var(--gris)">Sin archivo</span>
            @endif
          </td>
          <td style="font-family:var(--mono);font-size:.8rem;color:var(--gris)">{{ $video->tamanoLegible() }}</td>
          <td><span class="etq {{ $video->activo ? 'si' : 'no' }}">{{ $video->activo ? 'Visible' : 'Oculto' }}</span>
          </td>
          <td>
            <div class="ico-acciones">
              <a class="ico-btn" href="/panel/videos/{{ $video->id }}" title="Editar" aria-label="Editar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></a>
              <form method="POST" action="/panel/videos/{{ $video->id }}" style="margin:0"
                onsubmit="return confirm('¿Eliminar este video? Se borra también el archivo subido.')">
                @csrf @method('DELETE')
                <button class="ico-btn peligro" type="submit" title="Eliminar" aria-label="Eliminar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
</section>

{{-- ---------------- EJEMPLOS ---------------- --}}
<section class="panel-panel" data-panel="ejemplos" hidden>
  <div class="enc-panel">
    <div>
      <h2 class="titulo-seccion">Ejemplos de contenido (Defensa oral)</h2>
      <p class="bajada" style="margin:0">Aparecen para descargar en <a href="/defensa-oral#contenido-ejemplos">Defensa
          oral › Ejemplos de contenido</a>. Uno por nivel o sección (ej. BATAN, EEB3).</p>
    </div>
    @if($hayTablaEjemplos)
    <a class="boton btn-p" href="/panel/ejemplos/nuevo">Agregar ejemplo</a>
    @endif
  </div>

  @if($hayTablaEjemplos && $ejemplos->isNotEmpty())
  <div class="tbl-toolbar" data-tabla="tabla-ejemplos">
    <label class="tbl-buscar">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
      <input type="search" placeholder="Buscar documento…" autocomplete="off">
    </label>
    <select data-filtro="estado" aria-label="Filtrar por estado">
      <option value="">Todos los estados</option>
      <option value="visible">Visibles</option>
      <option value="oculto">Ocultos</option>
    </select>
    <span class="tbl-cuenta"></span>
  </div>
  @endif

  <div class="tarjeta" style="padding:22px 14px">
    @if(! $hayTablaEjemplos)
    <div class="errores" style="margin:4px 12px">
      <strong>Falta crear la tabla de ejemplos en la base de datos.</strong><br>
      Entrá a phpMyAdmin, abrí la base del sitio e importá el archivo <code>actualizar_ejemplos.sql</code>.
      No borra nada de lo que ya está cargado.
    </div>
    @elseif($ejemplos->isEmpty())
    <p style="margin:10px 12px;color:var(--tinta2)">Todavía no hay documentos cargados.</p>
    @else
    <table class="lista ordenable" id="tabla-ejemplos">
      <thead>
        <tr>
          <th data-orden="orden" style="width:56px">Orden</th>
          <th data-orden="titulo">Documento</th>
          <th data-orden="tamano" style="width:90px">Tamaño</th>
          <th data-orden="estado" style="width:90px">Estado</th>
          <th style="width:90px;text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($ejemplos as $ejemplo)
        <tr data-orden="{{ $ejemplo->orden }}" data-titulo="{{ $ejemplo->titulo }}" data-tamano="{{ $ejemplo->tamano }}" data-estado="{{ $ejemplo->activo ? 'visible' : 'oculto' }}">
          <td style="font-family:var(--mono);color:var(--gris)">{{ $ejemplo->orden }}</td>
          <td>
            <strong>{{ $ejemplo->titulo }}</strong>
            @if($ejemplo->descripcion)
            <div style="color:var(--tinta2);font-size:.85rem;margin-top:3px">
              {{ Str::limit($ejemplo->descripcion, 120) }}</div>
            @endif
            <div style="margin-top:5px">
              @if($ejemplo->archivo)
              <span style="font-size:.72rem;color:var(--gris)">Archivo:</span>
              <a href="{{ $ejemplo->url() }}" download="{{ $ejemplo->nombre_original }}"
                style="font-family:var(--mono);font-size:.78rem">{{ $ejemplo->nombre_original }}</a>
              @else
              <span style="font-family:var(--mono);font-size:.78rem;color:var(--gris)">Sin archivo</span>
              @endif
            </div>
          </td>
          <td style="font-family:var(--mono);font-size:.8rem;color:var(--gris)">{{ $ejemplo->tamanoLegible() }}</td>
          <td><span class="etq {{ $ejemplo->activo ? 'si' : 'no' }}">{{ $ejemplo->activo ? 'Visible' : 'Oculto' }}</span>
          </td>
          <td>
            <div class="ico-acciones">
              <a class="ico-btn" href="/panel/ejemplos/{{ $ejemplo->id }}" title="Editar" aria-label="Editar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></a>
              <form method="POST" action="/panel/ejemplos/{{ $ejemplo->id }}" style="margin:0"
                onsubmit="return confirm('¿Eliminar este ejemplo? Se borra también el archivo subido.')">
                @csrf @method('DELETE')
                <button class="ico-btn peligro" type="submit" title="Eliminar" aria-label="Eliminar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
</section>

{{-- ---------------- PROMPTS DE DEFENSA ORAL ---------------- --}}
@include('panel._prompts-guia', ['pestana' => 'defensa-oral-prompts', 'pagina' => 'defensa-oral', 'nombre' => 'Defensa oral', 'prompts' => $promptsDefensa, 'hayTabla' => $hayTablaPromptsSitio])

{{-- ---------------- SITIO WEB ---------------- --}}
@include('panel._prompts-guia', ['pestana' => 'sitio-web', 'pagina' => 'sitio-web', 'nombre' => 'Sitio web · SilEmprende', 'prompts' => $promptsSitio, 'hayTabla' => $hayTablaPromptsSitio])
@include('panel._prompts-guia', ['pestana' => 'sitio-web-batan-prompts', 'pagina' => 'sitio-web-batan', 'nombre' => 'Sitio web · BATAN', 'prompts' => $promptsBatan, 'hayTabla' => $hayTablaPromptsSitio])
@endsection

@section('scripts')
<script>
  (function () {
    var tabs = [].slice.call(document.querySelectorAll('.pnl-item[data-tab]'));
    var paneles = [].slice.call(document.querySelectorAll('.panel-panel'));
    var validos = tabs.map(function (t) { return t.dataset.tab; });

    function activar(id) {
      if (validos.indexOf(id) < 0) id = validos[0];
      tabs.forEach(function (t) { t.classList.toggle('activo', t.dataset.tab === id); });
      paneles.forEach(function (p) { p.hidden = p.dataset.panel !== id; });
      if (('#' + id) !== location.hash) history.replaceState(null, '', '#' + id);
      // en móvil, cerrar el menú al elegir una sección
      document.body.classList.remove('pnl-nav-abierta');
    }

    window.addEventListener('hashchange', function () { activar((location.hash || '').slice(1)); });
    activar((location.hash || '').slice(1));
  })();
</script>
@endsection
