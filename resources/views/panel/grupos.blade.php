@extends('layouts.panel')
@section('titulo', 'Grupos')
@section('panel_activo', 'grupos')

@section('contenido')
<h1>Administración</h1>
<p class="bajada">Usuarios, roles y grupos del sitio.</p>
@include('panel._admin-tabs', ['actual' => 'grupos'])

<div class="enc-panel">
  <div>
    <h2 class="titulo-seccion">Grupos</h2>
    <p class="bajada" style="margin:0">Formato: Curso Sección Especialidad Grupo Nº Año (ej. <strong>2A BATAN Grupo 1
        2026</strong>). Los alumnos eligen el suyo; a los docentes se les asignan en Usuarios.</p>
  </div>
  <a class="boton btn-p" href="/panel/grupos/nuevo">+ Nuevo grupo</a>
</div>

@if($grupos->isEmpty())
<div class="tarjeta">
  <p style="margin:0;color:var(--tinta2)">Todavía no hay grupos. Creá el primero con <strong>+ Nuevo grupo</strong>.</p>
</div>
@else
<div class="tbl-toolbar" data-tabla="tabla-grupos">
  <label class="tbl-buscar">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
    <input type="search" placeholder="Buscar grupo…" autocomplete="off">
  </label>
  <select data-filtro="anio" aria-label="Filtrar por año">
    <option value="">Todos los años</option>
    @foreach($grupos->pluck('anio')->unique()->sortDesc() as $anio)
    <option value="{{ $anio }}" {{ (int) $anio === (int) date('Y') ? 'selected' : '' }}>{{ $anio }}</option>
    @endforeach
  </select>
  <select data-filtro="curso" aria-label="Filtrar por curso">
    <option value="">Todos los cursos</option>
    @foreach($grupos->map(fn ($g) => $g->curso.$g->seccion)->unique()->sort() as $cs)
    <option value="{{ $cs }}">{{ $cs }}</option>
    @endforeach
  </select>
  <select data-filtro="especialidad" aria-label="Filtrar por especialidad">
    <option value="">Todas las especialidades</option>
    @foreach($grupos->pluck('especialidad')->unique()->sort() as $esp)
    <option value="{{ $esp }}">{{ $esp }}</option>
    @endforeach
  </select>
  <select data-filtro="estado" aria-label="Filtrar por estado">
    <option value="">Todos los estados</option>
    <option value="1">Activos</option>
    <option value="0">Inactivos</option>
  </select>
  <span class="tbl-cuenta"></span>
</div>
<p class="leyenda"><strong>Estado:</strong> <span class="est-ini est-a">A</span> Activo · <span class="est-ini est-d">D</span>
  Desactivado (solo lectura). Tocá la letra para cambiarlo.</p>

<div class="tarjeta" style="padding:22px 14px">
  <div class="tbl-wrap">
    <table class="lista ordenable" id="tabla-grupos">
      <thead>
        <tr>
          <th data-orden="nombre">Grupo</th>
          <th data-orden="especialidad">Especialidad</th>
          <th data-orden="anio" style="width:70px">Año</th>
          <th data-orden="estado" style="width:80px">Estado</th>
          <th style="width:120px;text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($grupos as $g)
        <tr data-nombre="{{ $g->nombre() }}" data-curso="{{ $g->curso.$g->seccion }}"
          data-especialidad="{{ $g->especialidad }}" data-anio="{{ $g->anio }}"
          data-estado="{{ $g->activo ? '1' : '0' }}">
          <td style="white-space:nowrap"><strong>{{ $g->nombre() }}</strong></td>
          <td>{{ $g->especialidad }}</td>
          <td style="font-family:var(--mono)">{{ $g->anio }}</td>
          <td>
            <form method="POST" action="/panel/grupos/{{ $g->id }}/activo" style="margin:0">
              @csrf @method('PUT')
              <button type="submit" class="est-ini {{ $g->activo ? 'est-a' : 'est-d' }}"
                title="{{ $g->activo ? 'Activo · tocá para desactivar (queda de solo lectura)' : 'Desactivado · tocá para activar' }}">{{ $g->activo ? 'A' : 'D' }}</button>
            </form>
          </td>
          <td>
            <div class="ico-acciones">
              <a class="ico-btn" href="/panel/grupos/{{ $g->id }}/detalle" title="Ver detalles" aria-label="Ver detalles">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
              </a>
              @if($g->activo)
              <a class="ico-btn" href="/panel/grupos/{{ $g->id }}" title="Editar grupo" aria-label="Editar grupo">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
              </a>
              <form method="POST" action="/panel/grupos/{{ $g->id }}" style="margin:0"
                onsubmit="return confirm('¿Eliminar el grupo {{ $g->nombre() }}? Los alumnos y docentes quedan sin ese grupo.')">
                @csrf @method('DELETE')
                <button class="ico-btn peligro" type="submit" title="Eliminar grupo" aria-label="Eliminar grupo">
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
@endsection
