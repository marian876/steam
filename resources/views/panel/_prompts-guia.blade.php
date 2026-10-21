{{-- Tabla de prompts editables de una guía (Sitio web o Defensa oral).
     Recibe: $pestana, $pagina, $nombre, $prompts, $hayTabla --}}
<section class="panel-panel" data-panel="{{ $pestana }}" hidden>
  <div class="enc-panel">
    <div>
      <h2 class="titulo-seccion">Prompts ({{ $nombre }})</h2>
      <p class="bajada" style="margin:0">Los prompts de la guía <a href="/{{ $pagina }}">{{ $nombre }}</a>. Están
        fijos en su página: se pueden editar, pero no agregar ni borrar.</p>
    </div>
  </div>

  @if(! $hayTabla)
  <div class="errores" style="margin-bottom:14px">
    <strong>Falta crear la tabla de prompts en la base de datos.</strong><br>
    Podés verlos, pero para guardar cambios entrá a phpMyAdmin, abrí la base del sitio e importá el archivo
    <code>crear_prompts_sitio_web.sql</code>. No borra nada de lo que ya está cargado.
  </div>
  @endif

  <div class="tbl-toolbar" data-tabla="tabla-prompts-{{ $pagina }}">
    <label class="tbl-buscar">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
      <input type="search" placeholder="Buscar página o prompt…" autocomplete="off">
    </label>
    <select data-filtro="estado" aria-label="Filtrar por estado">
      <option value="">Todos</option>
      <option value="original">Originales</option>
      <option value="modificado">Modificados</option>
    </select>
    <span class="tbl-cuenta"></span>
  </div>

  <div class="tarjeta" style="padding:22px 14px">
    <table class="lista ordenable" id="tabla-prompts-{{ $pagina }}">
      <thead>
        <tr>
          <th data-orden="pagina" style="width:190px">Página</th>
          <th data-orden="titulo">Prompt</th>
          <th data-orden="estado" style="width:110px">Estado</th>
          <th style="width:90px;text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($prompts as $p)
        <tr data-pagina="{{ $loop->iteration }}" data-titulo="{{ $p['titulo'] }}" data-estado="{{ $p['modificado'] ? 'modificado' : 'original' }}"
          data-buscar="{{ $p['pagina'] }} {{ $p['titulo'] }} {{ $p['descripcion'] }}">
          <td><strong>{{ $p['pagina'] }}</strong></td>
          <td>
            <strong>{{ $p['titulo'] }}</strong>
            <div style="color:var(--tinta2);font-size:.85rem;margin-top:3px">{{ Str::limit($p['descripcion'], 120) }}</div>
          </td>
          <td><span class="etq {{ $p['modificado'] ? 'si' : 'no' }}">{{ $p['modificado'] ? 'Modificado' : 'Original' }}</span></td>
          <td>
            <div class="ico-acciones">
              <a class="ico-btn" href="/panel/{{ $pagina }}/prompts/{{ $p['clave'] }}" title="Editar" aria-label="Editar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
