@extends('layouts.panel')
@section('titulo', $prompt->exists ? 'Editar prompt' : 'Nuevo prompt')

@section('contenido')
<h1>{{ $prompt->exists ? 'Editar prompt' : 'Nuevo prompt' }}</h1>
<p class="bajada">Se muestra en la página <strong>Datos adicionales</strong> del sitio.</p>

<div class="tarjeta">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ $prompt->exists ? '/panel/prompts/'.$prompt->id : '/panel/prompts' }}">
    @csrf
    @if($prompt->exists) @method('PUT') @endif

    <div class="campo">
      <label for="titulo">Título</label>
      <input id="titulo" name="titulo" type="text" value="{{ old('titulo', $prompt->titulo) }}" required autofocus>
      <div class="ayuda">Es lo que se ve cuando la sección está cerrada.</div>
    </div>

    <div class="campo">
      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="3" required
        style="font-family:var(--texto);font-size:.92rem">{{ old('descripcion', $prompt->descripcion) }}</textarea>
      <div class="ayuda">Explica para qué sirve. Se ve al desplegar, arriba del prompt.</div>
    </div>

    <div class="campo">
      <label for="texto">Texto del prompt</label>
      <textarea id="texto" name="texto" rows="18" required>{{ old('texto', $prompt->texto) }}</textarea>
      <div class="ayuda">Es lo que se copia con el botón. Los saltos de línea se respetan tal cual.</div>
    </div>

    <div class="campo" style="display:flex;gap:22px;align-items:flex-end;flex-wrap:wrap">
      <div style="width:110px">
        <label for="orden">Orden</label>
        <input id="orden" name="orden" type="number" min="0" max="999" value="{{ old('orden', $prompt->orden ?? 0) }}"
          required>
      </div>
      <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-bottom:10px">
        <input type="checkbox" name="activo" value="1" style="width:auto" @checked(old('activo', $prompt->activo ??
        true))>
        Visible en el sitio
      </label>
    </div>

    <div class="acciones" style="margin-top:6px">
      <button class="btn-p" type="submit">{{ $prompt->exists ? 'Guardar cambios' : 'Agregar prompt' }}</button>
      <a class="boton btn-s" href="/panel">Cancelar</a>
    </div>
  </form>
</div>
@endsection
