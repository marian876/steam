@extends('layouts.panel')
@section('titulo', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')
@section('panel_activo', 'usuarios')

@section('contenido')
<h1>{{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
<p class="bajada"><a href="/panel/usuarios">← Volver a Usuarios</a></p>

<div class="tarjeta" style="max-width:640px">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  @if($usuario->exists)
  <p class="ayuda" style="margin:0 0 16px">Cuenta creada el {{ $usuario->created_at?->format('d/m/Y') ?? '—' }}
    @if($usuario->updated_at) · última modificación {{ $usuario->updated_at->format('d/m/Y') }}@endif</p>
  @endif

  <form method="POST" action="{{ $usuario->exists ? '/panel/usuarios/'.$usuario->id : '/panel/usuarios' }}">
    @csrf
    @if($usuario->exists) @method('PUT') @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px">
      <div class="campo">
        <label for="name">Nombre</label>
        <input id="name" name="name" type="text" value="{{ old('name', $usuario->name) }}" required maxlength="120">
      </div>
      <div class="campo">
        <label for="apellido">Apellido</label>
        <input id="apellido" name="apellido" type="text" value="{{ old('apellido', $usuario->apellido) }}" required
          maxlength="120">
      </div>
    </div>

    <div class="campo">
      <label for="email">Correo</label>
      <input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}" required maxlength="180">
      <div class="ayuda">@sil.edu.py para docentes · @alumnos.sil.edu.py para alumnos.</div>
    </div>

    @php($esYo = $usuario->exists && $usuario->id === auth()->id())
    <div class="campo">
      <label for="rol">Rol</label>
      <select id="rol" name="rol" {{ $esYo ? 'disabled' : '' }}>
        @foreach($roles as $clave => $nombre)
        <option value="{{ $clave }}" {{ old('rol', $usuario->rol) === $clave ? 'selected' : '' }}>{{ $nombre }}</option>
        @endforeach
      </select>
      @if($esYo)
      <input type="hidden" name="rol" value="{{ $usuario->rol }}">
      <div class="ayuda">No podés cambiar tu propio rol.</div>
      @endif
    </div>

    @if($tieneActivo)
    <div class="campo">
      <label>Estado</label>
      <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin:0">
        <input type="checkbox" name="activo" value="1" style="width:auto" {{ $esYo ? 'checked disabled' : '' }}
          @checked(old('activo', $usuario->activo ?? true))>
        Cuenta activa (puede iniciar sesión)
      </label>
      @if($esYo)<div class="ayuda">No podés desactivar tu propia cuenta.</div>@endif
    </div>
    @endif

    <div class="campo">
      <label>Grupos</label>
      <div class="ayuda" id="grupos-ayuda" style="margin:0 0 8px"></div>
      @if($grupos->isEmpty())
      <div class="aviso" style="margin:0">Todavía no hay grupos. Crealos en <a href="/panel/grupos">Administración ›
          Grupos</a>.</div>
      @else
      <input type="search" id="grupos-buscar" placeholder="Buscar grupo…" autocomplete="off" style="margin-bottom:8px">
      @php($marcados = array_map('intval', old('grupos', $seleccionados)))
      <div class="grupos-lista">
        @foreach($grupos->groupBy('anio') as $anio => $delAnio)
        <div class="grupos-anio">
          <div class="grupos-anio-tit">{{ $anio }}</div>
          @foreach($delAnio as $g)
          <label class="grupo-opcion" data-texto="{{ strtolower($g->nombre()) }}">
            <input type="checkbox" name="grupos[]" value="{{ $g->id }}" data-anio="{{ $g->anio }}"
              {{ in_array($g->id, $marcados, true) ? 'checked' : '' }} {{ $g->activo ? '' : 'disabled' }}>
            <span>{{ $g->nombre() }}</span>
            @unless($g->activo)<span class="etq no" title="Grupo inactivo: no se puede sumar ni quitar gente">Inactivo · solo lectura</span>@endunless
          </label>
          @endforeach
        </div>
        @endforeach
      </div>
      <div class="ayuda" id="grupos-aviso" style="color:var(--rojo)" hidden>Un alumno solo puede tener un grupo por
        año.</div>
      @endif
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px">
      <div class="campo">
        <label for="password">{{ $usuario->exists ? 'Contraseña nueva' : 'Contraseña' }}</label>
        <input id="password" name="password" type="password" minlength="8" autocomplete="new-password"
          {{ $usuario->exists ? '' : 'required' }}>
        <div class="ayuda">{{ $usuario->exists ? 'Dejala en blanco para no cambiarla.' : 'Mínimo 8 caracteres.' }}</div>
      </div>
      <div class="campo">
        <label for="password_confirmation">Repetir contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
          autocomplete="new-password" {{ $usuario->exists ? '' : 'required' }}>
      </div>
    </div>

    <div class="acciones" style="margin-top:6px">
      <button class="btn-p" type="submit">{{ $usuario->exists ? 'Guardar cambios' : 'Crear usuario' }}</button>
      <a class="boton btn-s" href="/panel/usuarios">Cancelar</a>
    </div>
  </form>
</div>

<style>
  .grupos-lista {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--linea);
    border-radius: var(--r-m);
    padding: 6px 10px
  }

  .grupos-anio-tit {
    font-family: var(--mono);
    font-size: 10px;
    letter-spacing: .09em;
    color: var(--gris);
    font-weight: 600;
    padding: 8px 2px 4px
  }

  .grupo-opcion {
    display: flex;
    align-items: center;
    gap: 9px;
    font-weight: 500;
    font-size: .9rem;
    padding: 5px 2px;
    margin: 0;
    cursor: pointer
  }

  .grupo-opcion input {
    width: auto
  }

  .grupo-opcion[hidden] {
    display: none
  }

  @media (max-width: 560px) {
    form>div[style*="grid-template-columns"] {
      grid-template-columns: 1fr !important
    }
  }
</style>
@endsection

@section('scripts')
<script>
  (function () {
    var rol = document.getElementById('rol');
    var email = document.getElementById('email');
    var ayuda = document.getElementById('grupos-ayuda');
    var aviso = document.getElementById('grupos-aviso');
    var buscar = document.getElementById('grupos-buscar');
    var checks = [].slice.call(document.querySelectorAll('input[name="grupos[]"]'));
    var rolTocado = {{ $usuario->exists ? 'true' : 'false' }};

    var TEXTOS = {
      alumno: 'Elegí el grupo del alumno. Puede tener más de uno solo si son de años distintos.',
      docente: 'Marcá los grupos que tiene a cargo (pueden ser varios).',
      admin: 'Opcional: grupos a cargo, si también da clases.'
    };

    function revisar() {
      ayuda.textContent = TEXTOS[rol.value] || '';
      if (!aviso) return;
      var anios = checks.filter(function (c) { return c.checked; }).map(function (c) { return c.dataset.anio; });
      var repetido = anios.some(function (a, i) { return anios.indexOf(a) !== i; });
      aviso.hidden = !(rol.value === 'alumno' && repetido);
    }

    // En un alta, el rol se sugiere según el dominio del correo.
    email.addEventListener('input', function () {
      if (rolTocado || rol.disabled) return;
      var v = email.value.toLowerCase();
      if (/@alumnos\.sil\.edu\.py$/.test(v)) rol.value = 'alumno';
      else if (/@sil\.edu\.py$/.test(v)) rol.value = 'docente';
      revisar();
    });
    rol.addEventListener('change', function () { rolTocado = true; revisar(); });
    checks.forEach(function (c) { c.addEventListener('change', revisar); });

    if (buscar) buscar.addEventListener('input', function () {
      var q = buscar.value.trim().toLowerCase();
      document.querySelectorAll('.grupo-opcion').forEach(function (l) {
        l.hidden = q && l.dataset.texto.indexOf(q) === -1;
      });
      document.querySelectorAll('.grupos-anio').forEach(function (b) {
        b.hidden = ![].some.call(b.querySelectorAll('.grupo-opcion'), function (l) { return !l.hidden; });
      });
    });

    revisar();
  })();
</script>
@endsection
