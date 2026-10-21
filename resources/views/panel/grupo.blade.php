@extends('layouts.panel')
@section('titulo', $grupo->exists ? 'Editar grupo' : 'Nuevo grupo')
@section('panel_activo', 'grupos')

@section('contenido')
<h1>{{ $grupo->exists ? 'Editar grupo' : 'Nuevo grupo' }}</h1>
<p class="bajada"><a href="/panel/grupos">← Volver a Grupos</a></p>

<div class="tarjeta" style="max-width:620px">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  @php($soloLectura = $grupo->exists && ! $grupo->activo)
  @if($soloLectura)
  <div class="aviso"><strong>Grupo inactivo · solo lectura.</strong> Ya no se pueden cargar datos con este grupo y no se
    puede modificar. Lo que ya tiene se conserva. Para cambiarlo, primero reactivalo.</div>
  @endif

  <form method="POST" action="{{ $grupo->exists ? '/panel/grupos/'.$grupo->id : '/panel/grupos' }}">
    @csrf
    @if($grupo->exists) @method('PUT') @endif

    <fieldset class="grupo-grid" {{ $soloLectura ? 'disabled' : '' }} style="border:0;padding:0;margin:0;min-width:0">
      <div class="campo">
        <label for="curso">Curso</label>
        <input id="curso" name="curso" type="text" value="{{ old('curso', $grupo->curso) }}" required maxlength="10"
          placeholder="2">
      </div>
      <div class="campo">
        <label for="seccion">Sección</label>
        <input id="seccion" name="seccion" type="text" value="{{ old('seccion', $grupo->seccion) }}" required
          maxlength="10" placeholder="A">
      </div>
      <div class="campo" style="grid-column:span 2">
        <label for="especialidad">Especialidad</label>
        <input id="especialidad" name="especialidad" type="text" list="especialidades"
          value="{{ old('especialidad', $grupo->especialidad) }}" required maxlength="60" placeholder="BATAN">
        <datalist id="especialidades">
          @foreach($especialidades as $esp)<option value="{{ $esp }}">@endforeach
        </datalist>
      </div>
      <div class="campo">
        <label for="numero">Grupo Nº</label>
        <input id="numero" name="numero" type="number" min="1" max="99" value="{{ old('numero', $grupo->numero) }}"
          required placeholder="1">
      </div>
      <div class="campo">
        <label for="anio">Año</label>
        <input id="anio" name="anio" type="number" min="2020" max="2100" value="{{ old('anio', $grupo->anio) }}"
          required>
      </div>
    </fieldset>

    <div class="aviso" style="margin:4px 0 16px">Así se va a ver: <strong id="grupo-vista"></strong></div>

    <div class="campo">
      <label style="display:flex;align-items:center;gap:8px;font-weight:400">
        <input type="checkbox" name="activo" value="1" style="width:auto" @checked(old('activo', $grupo->activo ?? true))>
        Activo
      </label>
      <div class="ayuda">Activo: los alumnos pueden elegirlo y se le pueden asignar personas. Inactivo: queda de solo
        lectura; no se borra nada de lo cargado.</div>
    </div>

    <div class="acciones" style="margin-top:6px">
      <button class="btn-p" type="submit">{{ $soloLectura ? 'Guardar estado' : ($grupo->exists ? 'Guardar cambios' : 'Crear grupo') }}</button>
      <a class="boton btn-s" href="/panel/grupos">Cancelar</a>
    </div>
  </form>
</div>

<style>
  .grupo-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0 14px
  }

  @media (max-width: 560px) {
    .grupo-grid {
      grid-template-columns: 1fr 1fr
    }
  }
</style>
@endsection

@section('scripts')
<script>
  (function () {
    var campos = ['curso', 'seccion', 'especialidad', 'numero', 'anio'].map(function (id) { return document.getElementById(id); });
    var vista = document.getElementById('grupo-vista');
    function pintar() {
      var v = campos.map(function (c) { return c.value.trim(); });
      vista.textContent = (v[0] + v[1].toUpperCase() + ' ' + v[2].toUpperCase() + ' Grupo ' + v[3] + ' ' + v[4]).replace(/\s+/g, ' ').trim();
    }
    campos.forEach(function (c) { c.addEventListener('input', pintar); });
    pintar();
  })();
</script>
@endsection
