@extends('layouts.panel')
@section('titulo', 'Proyectos')
@section('panel_activo', 'proyectos')

@section('contenido')
<div class="enc-panel">
  <div>
    <h1 style="margin:0 0 4px">Proyectos anteriores</h1>
    <p class="bajada" style="margin:0">El trabajo de cada grupo: empresa, logo, sitio web y archivos. Se muestran en la
      página de inicio, agrupados por especialidad y año.</p>
  </div>
  @if($hayTabla)
  <a class="boton btn-p" href="/panel/proyectos/nuevo">+ Nuevo proyecto</a>
  @endif
</div>

@if($hayTabla && ! $hayIntegrantes)
<div class="errores">
  <strong>Falta crear la tabla de integrantes.</strong> Hasta que importes <code>crear_proyecto_integrantes.sql</code>
  en phpMyAdmin, los integrantes no se pueden guardar ni se muestran en el sitio.
</div>
@endif

@if(! $hayTabla)
<div class="errores">
  <strong>Falta crear las tablas de proyectos en la base de datos.</strong><br>
  Entrá a phpMyAdmin, abrí la base del sitio e importá el archivo <code>crear_proyectos.sql</code>
  (después de <code>crear_grupos.sql</code>). No borra nada de lo que ya está cargado.
</div>
@elseif($proyectos->isEmpty())
<div class="tarjeta">
  <p style="margin:0;color:var(--tinta2)">Todavía no hay proyectos. Cargá el primero con <strong>+ Nuevo
      proyecto</strong>.</p>
</div>
@else
<div class="tbl-toolbar" data-tabla="tabla-proyectos">
  <label class="tbl-buscar">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
    <input type="search" placeholder="Buscar empresa o grupo…" autocomplete="off">
  </label>
  <select data-filtro="anio" aria-label="Filtrar por año">
    <option value="">Todos los años</option>
    @foreach($proyectos->pluck('grupo.anio')->unique()->sortDesc() as $anio)
    <option value="{{ $anio }}">{{ $anio }}</option>
    @endforeach
  </select>
  <select data-filtro="especialidad" aria-label="Filtrar por especialidad">
    <option value="">Todas las especialidades</option>
    @foreach($proyectos->pluck('grupo.especialidad')->unique()->sort() as $esp)
    <option value="{{ $esp }}">{{ $esp }}</option>
    @endforeach
  </select>
  @if($hayCiencap)
  <select data-filtro="ciencap" aria-label="Filtrar por Ciencap">
    <option value="">Con y sin Ciencap</option>
    <option value="1">★ Solo Ciencap</option>
    <option value="0">Sin Ciencap</option>
  </select>
  @endif
  <select data-filtro="estado" aria-label="Filtrar por estado">
    <option value="">Todos los estados</option>
    <option value="1">Visibles</option>
    <option value="0">Ocultos</option>
  </select>
  <span class="tbl-cuenta"></span>
</div>
<p class="leyenda"><strong>Estado:</strong> <span class="est-ini est-a">A</span> Visible en el sitio · <span
    class="est-ini est-d">D</span> Oculto. Tocá la letra para cambiarlo.</p>

<div class="tarjeta" style="padding:22px 14px">
  <div class="tbl-wrap">
    <table class="lista ordenable" id="tabla-proyectos">
      <thead>
        <tr>
          <th style="width:56px">Logo</th>
          <th data-orden="empresa">Empresa</th>
          <th data-orden="grupo">Grupo</th>
          @if($hayIntegrantes)
          <th data-orden="integrantes" style="width:100px">Integrantes</th>
          @endif
          <th data-orden="archivos" style="width:85px">Archivos</th>
          <th data-orden="estado" style="width:80px">Estado</th>
          <th style="width:120px;text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($proyectos as $p)
        @php($lectura = $p->soloLectura())
        <tr data-empresa="{{ $p->empresa }}" data-grupo="{{ $p->grupo->nombre() }}" data-archivos="{{ $p->archivos_count }}" data-integrantes="{{ $p->integrantes_count ?? 0 }}"
          data-anio="{{ $p->grupo->anio }}" data-especialidad="{{ $p->grupo->especialidad }}"
          data-estado="{{ $p->activo ? '1' : '0' }}" data-ciencap="{{ $hayCiencap && $p->ciencap ? '1' : '0' }}"
          data-buscar="{{ $p->empresa }} {{ $p->grupo->nombre() }} {{ $hayCiencap && $p->ciencap ? 'ciencap' : '' }}">
          <td>
            @if($p->logoUrl())
            <img class="pry-logo" src="{{ $p->logoUrl() }}" alt="Logo de {{ $p->empresa }}">
            @else
            <span class="pry-logo pry-ini">{{ $p->iniciales() }}</span>
            @endif
          </td>
          <td><strong>{{ $p->empresa }}</strong>
            @if($hayCiencap && $p->ciencap)<span class="ciencap-tag" title="Participó en la Ciencap">★ Ciencap</span>@endif
          </td>
          <td style="white-space:nowrap">{{ $p->grupo->nombre() }}
            @if($lectura)<span class="etq no" title="Grupo inactivo">Solo lectura</span>@endif</td>
          @if($hayIntegrantes)
          <td style="font-family:var(--mono)">{{ $p->integrantes_count }}</td>
          @endif
          <td style="font-family:var(--mono)">{{ $p->archivos_count }}</td>
          <td>
            @if($lectura)
            <span class="est-ini {{ $p->activo ? 'est-a' : 'est-d' }}" title="Solo lectura">{{ $p->activo ? 'A' : 'D' }}</span>
            @else
            <form method="POST" action="/panel/proyectos/{{ $p->id }}/activo" style="margin:0">
              @csrf @method('PUT')
              <button type="submit" class="est-ini {{ $p->activo ? 'est-a' : 'est-d' }}"
                title="{{ $p->activo ? 'Visible · tocá para ocultarlo' : 'Oculto · tocá para mostrarlo' }}">{{ $p->activo ? 'A' : 'D' }}</button>
            </form>
            @endif
          </td>
          <td>
            <div class="ico-acciones">
              @if($lectura)
              <a class="ico-btn" href="/panel/proyectos/{{ $p->id }}" title="Ver (solo lectura)" aria-label="Ver proyecto">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
              </a>
              @else
              <a class="ico-btn" href="/panel/proyectos/{{ $p->id }}" title="Editar proyecto" aria-label="Editar proyecto">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
              </a>
              <form method="POST" action="/panel/proyectos/{{ $p->id }}" style="margin:0"
                onsubmit="return confirm('¿Eliminar el proyecto {{ $p->empresa }}? Se borran también su logo y sus archivos.')">
                @csrf @method('DELETE')
                <button class="ico-btn peligro" type="submit" title="Eliminar proyecto" aria-label="Eliminar proyecto">
                  <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

<style>
  .pry-logo {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    object-fit: contain;
    background: #fff;
    border: 1px solid var(--linea);
    display: block
  }

  .ciencap-tag {
    display: inline-block;
    margin-left: 6px;
    font-size: .72rem;
    font-weight: 700;
    color: #8A5A00;
    background: #FFF1D6;
    border-radius: 99px;
    padding: 2px 8px;
    white-space: nowrap
  }

  .pry-ini {
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: .8rem;
    color: var(--azul);
    background: var(--azul-suave)
  }
</style>
@endsection
