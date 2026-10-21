@extends('layouts.panel')
@section('titulo', $grupo->nombre())
@section('panel_activo', 'grupos')

@section('contenido')
<p class="bajada" style="margin-bottom:8px"><a href="/panel/grupos">← Volver a Grupos</a></p>

<div class="enc-panel">
  <div>
    <h1 style="margin:0 0 4px">{{ $grupo->nombre() }}</h1>
    <p class="bajada" style="margin:0">
      Curso {{ $grupo->curso }}{{ $grupo->seccion }} · {{ $grupo->especialidad }} · Grupo {{ $grupo->numero }} · {{ $grupo->anio }}
      · <span class="est-ini {{ $grupo->activo ? 'est-a' : 'est-d' }}" title="{{ $grupo->activo ? 'Activo' : 'Desactivado' }}">{{ $grupo->activo ? 'A' : 'D' }}</span>
    </p>
  </div>
  @if($grupo->activo)
  <a class="ico-btn" href="/panel/grupos/{{ $grupo->id }}" title="Editar grupo" aria-label="Editar grupo">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
  </a>
  @endif
</div>

@unless($grupo->activo)
<div class="aviso"><strong>Grupo desactivado · solo lectura.</strong> Se conserva todo lo cargado, pero no se pueden
  sumar personas ni modificarlo.</div>
@endunless

<div class="detalle-cifras">
  <div class="detalle-cifra"><span class="n">{{ $alumnos }}</span><span class="t">Alumnos</span></div>
  <div class="detalle-cifra"><span class="n">{{ $docentes }}</span><span class="t">Docentes</span></div>
</div>

@if($integrantes->isEmpty())
<div class="tarjeta">
  <p style="margin:0;color:var(--tinta2)">Este grupo todavía no tiene integrantes.</p>
</div>
@else
<div class="tbl-toolbar" data-tabla="tabla-integrantes">
  <label class="tbl-buscar">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
    <input type="search" placeholder="Buscar integrante…" autocomplete="off">
  </label>
  <select data-filtro="tipo" aria-label="Filtrar por tipo">
    <option value="">Alumnos y docentes</option>
    <option value="alumno">Solo alumnos</option>
    <option value="docente">Solo docentes</option>
  </select>
  <span class="tbl-cuenta"></span>
</div>

<div class="tarjeta" style="padding:22px 14px">
  <div class="tbl-wrap">
    <table class="lista ordenable" id="tabla-integrantes">
      <thead>
        <tr>
          <th data-orden="nombre">Nombre</th>
          <th data-orden="rol" style="width:70px">Rol</th>
          <th style="width:60px;text-align:right">Ver</th>
        </tr>
      </thead>
      <tbody>
        @foreach($integrantes as $p)
        @php($ini = ['admin' => 'Adm', 'docente' => 'D', 'alumno' => 'A'][$p->rol] ?? '?')
        <tr data-nombre="{{ $p->nombreCompleto() }}" data-rol="{{ $p->rol }}"
          data-tipo="{{ $p->rol === 'alumno' ? 'alumno' : 'docente' }}"
          data-buscar="{{ $p->nombreCompleto() }} {{ $p->email }}">
          <td><strong>{{ $p->nombreCompleto() }}</strong></td>
          <td><span class="rol-ini rol-{{ $p->rol }}" title="{{ $p->etiquetaRol() }}">{{ $ini }}</span></td>
          <td>
            <div class="ico-acciones">
              <a class="ico-btn" href="/panel/usuarios/{{ $p->id }}/editar" title="Ver o editar usuario"
                aria-label="Ver o editar usuario">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M5 20c0-3.5 3.1-5.5 7-5.5s7 2 7 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
              </a>
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
  .detalle-cifras {
    display: flex;
    gap: 14px;
    margin: 0 0 20px;
    flex-wrap: wrap
  }

  .detalle-cifra {
    background: var(--superficie);
    border: 1px solid var(--linea);
    border-radius: var(--r-l);
    box-shadow: var(--sombra);
    padding: 14px 22px;
    min-width: 140px;
    display: flex;
    flex-direction: column
  }

  .detalle-cifra .n {
    font-family: var(--display);
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--azul);
    line-height: 1.1
  }

  .detalle-cifra .t {
    font-family: var(--mono);
    font-size: 10px;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--gris)
  }
</style>
@endsection
