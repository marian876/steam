@extends('layouts.panel')
@section('titulo', 'Roles')
@section('panel_activo', 'roles')

@section('contenido')
<h1>Administración</h1>
<p class="bajada">Usuarios, roles y grupos del sitio.</p>
@include('panel._admin-tabs', ['actual' => 'roles'])

<div class="enc-panel">
  <div>
    <h2 class="titulo-seccion">Roles</h2>
    <p class="bajada" style="margin:0">Qué puede hacer cada rol. El rol de cada persona se cambia en <a
        href="/panel/usuarios">Usuarios</a>.</p>
  </div>
</div>

@php($info = [
  'admin' => ['Gestiona usuarios, roles y grupos. Tiene además todo lo del docente.', true, true],
  'docente' => ['Entra al panel y administra prompts, videos y ejemplos. Tiene grupos a cargo.', true, false],
  'alumno' => ['Usa el sitio (Formato STEAM, defensa oral, sitio web) y elige su grupo.', false, false],
])

<div class="tbl-toolbar" data-tabla="tabla-roles">
  <label class="tbl-buscar">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
    <input type="search" placeholder="Buscar rol…" autocomplete="off">
  </label>
  <select data-filtro="panel" aria-label="Filtrar por acceso al panel">
    <option value="">Con y sin acceso al panel</option>
    <option value="si">Entra al panel</option>
    <option value="no">No entra al panel</option>
  </select>
  <span class="tbl-cuenta"></span>
</div>

<div class="tarjeta" style="padding:22px 14px">
  <div class="tbl-wrap">
    <table class="lista ordenable" id="tabla-roles">
      <thead>
        <tr>
          <th data-orden="nombre" style="width:150px">Rol</th>
          <th>Qué puede hacer</th>
          <th data-orden="panel" style="width:90px">Panel</th>
          <th data-orden="admin" style="width:130px">Administración</th>
          <th data-orden="total" style="width:100px">Cuentas</th>
        </tr>
      </thead>
      <tbody>
        @foreach($roles as $clave => $nombre)
        @php([$desc, $panel, $admin] = $info[$clave] ?? ['', false, false])
        <tr data-nombre="{{ $nombre }}" data-panel="{{ $panel ? 'si' : 'no' }}" data-admin="{{ $admin ? 'si' : 'no' }}"
          data-total="{{ $conteo[$clave] ?? 0 }}">
          <td><strong>{{ $nombre }}</strong></td>
          <td style="color:var(--tinta2)">{{ $desc }}</td>
          <td><span class="etq {{ $panel ? 'si' : 'no' }}">{{ $panel ? 'Sí' : 'No' }}</span></td>
          <td><span class="etq {{ $admin ? 'si' : 'no' }}">{{ $admin ? 'Sí' : 'No' }}</span></td>
          <td><a href="/panel/usuarios?rol={{ $clave }}">{{ $conteo[$clave] ?? 0 }}</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
