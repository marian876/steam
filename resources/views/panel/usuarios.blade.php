@extends('layouts.panel')
@section('titulo', 'Usuarios')
@section('panel_activo', 'usuarios')

@section('contenido')
<h1>Administración</h1>
<p class="bajada">Usuarios, roles y grupos del sitio.</p>
@include('panel._admin-tabs', ['actual' => 'usuarios'])

<div class="enc-panel">
  <div>
    <h2 class="titulo-seccion">Usuarios</h2>
    <p class="bajada" style="margin:0">Cuentas del sitio, con su rol y sus grupos.</p>
  </div>
  @if($hayGrupos)
  <a class="boton btn-p" href="/panel/usuarios/nuevo">+ Nuevo usuario</a>
  @endif
</div>

@unless($hayGrupos)
<div class="errores">
  <strong>Falta crear las tablas de grupos en la base de datos.</strong><br>
  Entrá a phpMyAdmin, abrí la base del sitio e importá el archivo <code>crear_grupos.sql</code>.
  No borra nada de lo que ya está cargado.
</div>
@endunless

<div class="tbl-toolbar" data-tabla="usr-tabla">
  <label class="tbl-buscar">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
    <input type="search" placeholder="Buscar por nombre, correo o grupo…" autocomplete="off">
  </label>
  <select data-filtro="rol" aria-label="Filtrar por rol">
    <option value="">Todos los roles</option>
    @foreach($roles as $clave => $nombre)
    <option value="{{ $clave }}">{{ $nombre }}</option>
    @endforeach
  </select>
  @if($hayGrupos)
  <select data-filtro="grupo" aria-label="Filtrar por grupo">
    <option value="">Todos los grupos</option>
    <option value="ninguno">Sin grupo</option>
    @foreach($grupos as $g)
    <option value="{{ $g->id }}">{{ $g->nombre() }}</option>
    @endforeach
  </select>
  @endif
  @if($tieneActivo)
  <select data-filtro="estado" aria-label="Filtrar por estado">
    <option value="">Todos los estados</option>
    <option value="1">Activos</option>
    <option value="0">Inactivos</option>
  </select>
  @endif
  <span class="tbl-cuenta"></span>
</div>
<p class="usr-leyenda"><strong>Rol:</strong> Adm = Administrador · D = Docente · A = Alumno
  @if($tieneActivo) &nbsp;|&nbsp; <strong>Estado:</strong> <span class="est-ini est-a">A</span> Activo · <span class="est-ini est-d">D</span> Desactivado @endif</p>

<div class="tarjeta" style="padding:22px 14px">
  @if($usuarios->isEmpty())
  <p style="margin:10px 12px;color:var(--tinta2)">Todavía no hay usuarios.</p>
  @else
  <div class="tbl-wrap">
    <table class="lista ordenable usr-tabla" id="usr-tabla">
      <thead>
        <tr>
          <th data-orden="nombre">Usuario</th>
          <th data-orden="rol" style="width:70px">Rol</th>
          @if($hayGrupos)
          <th data-orden="gruposnombre">Grupos</th>
          @endif
          @if($tieneActivo)
          <th data-orden="estado" style="width:80px">Estado</th>
          @endif
          <th style="width:140px;text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($usuarios as $usuario)
        @php($esYo = $usuario->id === auth()->id())
        @php($misGrupos = $hayGrupos ? $usuario->grupos : collect())
        <tr data-nombre="{{ $usuario->nombreCompleto() }}"
            data-email="{{ $usuario->email }}"
            data-rol="{{ $usuario->rol }}"
            data-grupo="{{ $misGrupos->isEmpty() ? 'ninguno' : $misGrupos->pluck('id')->implode('|') }}"
            data-gruposnombre="{{ $misGrupos->map->nombre()->implode(', ') }}"
            data-estado="{{ $tieneActivo ? ($usuario->activo ? '1' : '0') : '1' }}"
            data-buscar="{{ $usuario->nombreCompleto() }} {{ $usuario->email }} {{ $usuario->etiquetaRol() }} {{ $misGrupos->map->nombre()->implode(' ') }}">
          <td>
            <div class="usr-nombre-wrap">
              <strong>{{ $usuario->nombreCompleto() }}</strong>
              @if($esYo) <span class="etq si">Vos</span>@endif
            </div>
          </td>
          <td>
            @php($ini = ['admin' => 'Adm', 'docente' => 'D', 'alumno' => 'A'][$usuario->rol] ?? '?')
            <span class="rol-ini rol-{{ $usuario->rol }}" title="{{ $usuario->etiquetaRol() }}">{{ $ini }}</span>
          </td>
          @if($hayGrupos)
          <td>
            @forelse($misGrupos as $g)
            <span class="chip">{{ $g->nombre() }}</span>
            @empty
            <span style="color:var(--gris);font-size:.82rem">—</span>
            @endforelse
          </td>
          @endif
          @if($tieneActivo)
          <td>
            @if($esYo)
            <span class="est-ini est-a" title="Activo">A</span>
            @else
            <form method="POST" action="/panel/usuarios/{{ $usuario->id }}/activo" style="margin:0">
              @csrf @method('PUT')
              <button type="submit" class="est-ini {{ $usuario->activo ? 'est-a' : 'est-d' }}"
                title="{{ $usuario->activo ? 'Activo · tocá para desactivar' : 'Desactivado · tocá para activar' }}">{{ $usuario->activo ? 'A' : 'D' }}</button>
            </form>
            @endif
          </td>
          @endif
          <td>
            <div class="usr-acciones">
              @unless($esYo)
              {{-- Editar --}}
              <a class="usr-icon-btn" title="Editar usuario" href="/panel/usuarios/{{ $usuario->id }}/editar">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
              </a>
              {{-- Cambiar contraseña --}}
              <button type="button" class="usr-icon-btn" title="Cambiar contraseña"
                      onclick="abrirModalPassword({{ $usuario->id }}, '{{ addslashes($usuario->name) }}')">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
              </button>
              {{-- Eliminar --}}
              <form method="POST" action="/panel/usuarios/{{ $usuario->id }}" style="margin:0;display:inline"
                    onsubmit="return confirm('¿Eliminar la cuenta de {{ $usuario->name }}? No se puede deshacer.')">
                @csrf @method('DELETE')
                <button type="submit" class="usr-icon-btn usr-icon-danger" title="Eliminar usuario">
                  <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
              </form>
              @else
              <span style="color:var(--gris);font-size:.82rem">—</span>
              @endunless
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- Modal Cambiar Contraseña --}}
<div class="usr-modal-overlay" id="modal-password" aria-hidden="true">
  <div class="usr-modal">
    <div class="usr-modal-header">
      <h3>Cambiar contraseña</h3>
      <button type="button" class="usr-modal-close" onclick="cerrarModal('modal-password')" aria-label="Cerrar">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
      </button>
    </div>
    <form method="POST" id="form-password">
      @csrf @method('PUT')
      <div class="usr-modal-body">
        <p class="usr-modal-info" id="password-info"></p>
        <div class="campo">
          <label for="new-password">Nueva contraseña</label>
          <input type="password" id="new-password" name="password" required minlength="8">
          <p class="ayuda">Mínimo 8 caracteres.</p>
        </div>
        <div class="campo">
          <label for="confirm-password">Confirmar contraseña</label>
          <input type="password" id="confirm-password" name="password_confirmation" required minlength="8">
        </div>
      </div>
      <div class="usr-modal-footer">
        <button type="button" class="boton btn-s" onclick="cerrarModal('modal-password')">Cancelar</button>
        <button type="submit" class="boton btn-p">Cambiar contraseña</button>
      </div>
    </form>
  </div>
</div>

@if($errors->any())
<div class="errores" style="margin-top:18px">
  <ul>
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<style>
/* ===== Email column ===== */
.usr-email {
  color: var(--tinta2);
  font-size: .82rem;
  font-family: var(--mono);
  word-break: break-all;
}

/* ===== Nombre wrap ===== */
.usr-nombre-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.usr-leyenda {
  font-size: .8rem;
  color: var(--gris);
  margin: -4px 0 14px;
}
.usr-leyenda .est-ini { min-width: 22px; height: 20px; font-size: .68rem; }

/* ===== Rol select compacto ===== */
.usr-rol-form {
  margin: 0;
}
.usr-rol-select {
  width: auto;
  min-width: 110px;
  padding: 6px 10px;
  font-size: .82rem;
}

/* ===== Toggle activo/inactivo ===== */
.usr-toggle-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  border: 1px solid transparent;
  border-radius: var(--r-s);
  padding: 4px 10px 4px 6px;
  font-size: .78rem;
  font-weight: 600;
  cursor: pointer;
  transition: .15s;
  white-space: nowrap;
}
.usr-toggle-btn svg {
  width: 28px;
  height: 18px;
  flex: none;
}
.usr-toggle-btn.activo {
  color: var(--verde);
}
.usr-toggle-btn.activo:hover {
  background: color-mix(in srgb, var(--verde) 8%, transparent);
}
.usr-toggle-btn.inactivo {
  color: var(--gris);
}
.usr-toggle-btn.inactivo:hover {
  background: color-mix(in srgb, var(--gris) 10%, transparent);
}

/* ===== Icon buttons ===== */
.usr-acciones {
  display: flex;
  gap: 4px;
  justify-content: flex-end;
  align-items: center;
}
.usr-icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: var(--r-s);
  background: transparent;
  border: 1px solid transparent;
  color: var(--tinta2);
  cursor: pointer;
  padding: 0;
  transition: .15s;
}
.usr-icon-btn svg {
  width: 17px;
  height: 17px;
}
.usr-icon-btn:hover {
  background: var(--superficie2);
  border-color: var(--linea);
  color: var(--tinta);
}
.usr-icon-btn.usr-icon-danger:hover {
  background: var(--rojo-suave);
  border-color: var(--rojo);
  color: var(--rojo);
}

/* ===== Modal ===== */
.usr-modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 100;
  background: rgba(20, 23, 43, .5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity .2s;
}
.usr-modal-overlay.abierto {
  opacity: 1;
  pointer-events: auto;
}
@media (prefers-color-scheme:dark) {
  .usr-modal-overlay {
    background: rgba(0, 0, 0, .6);
  }
}
.usr-modal {
  background: var(--superficie);
  border: 1px solid var(--linea);
  border-radius: var(--r-l);
  box-shadow: var(--sombra);
  width: 100%;
  max-width: 440px;
  margin: 16px;
  transform: translateY(12px);
  transition: transform .2s;
}
.usr-modal-overlay.abierto .usr-modal {
  transform: translateY(0);
}
.usr-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 22px;
  border-bottom: 1px solid var(--linea);
}
.usr-modal-header h3 {
  margin: 0;
  font-family: var(--display);
  font-size: 1.05rem;
}
.usr-modal-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: var(--r-s);
  background: transparent;
  border: 0;
  color: var(--gris);
  cursor: pointer;
  padding: 0;
}
.usr-modal-close svg {
  width: 18px;
  height: 18px;
}
.usr-modal-close:hover {
  background: var(--superficie2);
  color: var(--tinta);
}
.usr-modal-body {
  padding: 22px;
}
.usr-modal-info {
  font-size: .88rem;
  color: var(--tinta2);
  margin: 0 0 16px;
}
.usr-modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 14px 22px;
  border-top: 1px solid var(--linea);
}

/* ===== Responsive ===== */
@media (max-width: 700px) {
}
</style>
@endsection

@section('scripts')
<script>
(function() {
  // ---------- Modales ----------
  window.abrirModalPassword = function(id, nombre) {
    var modal = document.getElementById('modal-password');
    var form = document.getElementById('form-password');
    form.action = '/panel/usuarios/' + id + '/password';
    document.getElementById('password-info').textContent = 'Cambiar contraseña de ' + nombre;
    document.getElementById('new-password').value = '';
    document.getElementById('confirm-password').value = '';
    modal.classList.add('abierto');
    modal.setAttribute('aria-hidden', 'false');
    document.getElementById('new-password').focus();
  };

  window.cerrarModal = function(id) {
    var modal = document.getElementById(id);
    modal.classList.remove('abierto');
    modal.setAttribute('aria-hidden', 'true');
  };

  // Cerrar con Escape o click en overlay
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.usr-modal-overlay.abierto').forEach(function(m) {
        m.classList.remove('abierto');
        m.setAttribute('aria-hidden', 'true');
      });
    }
  });

  document.querySelectorAll('.usr-modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) {
        overlay.classList.remove('abierto');
        overlay.setAttribute('aria-hidden', 'true');
      }
    });
  });
})();
</script>
@endsection
