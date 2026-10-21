@extends('layouts.panel')
@section('titulo', $ejemplo->exists ? 'Editar ejemplo' : 'Nuevo ejemplo')

@section('contenido')
<h1>{{ $ejemplo->exists ? 'Editar ejemplo de contenido' : 'Nuevo ejemplo de contenido' }}</h1>
<p class="bajada">Se ofrece para descargar en <strong>Defensa oral › Ejemplos de contenido</strong>. Subí uno por nivel
  o sección.</p>

<div class="tarjeta">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ $ejemplo->exists ? '/panel/ejemplos/'.$ejemplo->id : '/panel/ejemplos' }}"
    enctype="multipart/form-data">
    @csrf
    @if($ejemplo->exists) @method('PUT') @endif

    <div class="campo">
      <label for="titulo">Título</label>
      <input id="titulo" name="titulo" type="text" value="{{ old('titulo', $ejemplo->titulo) }}" required autofocus>
      <div class="ayuda">Cómo se llama en la página. Ej: "Ejemplos de contenido STEAM - BATAN".</div>
    </div>

    <div class="campo">
      <label for="descripcion">Descripción (optativa)</label>
      <textarea id="descripcion" name="descripcion" rows="3"
        style="font-family:var(--texto);font-size:.92rem">{{ old('descripcion', $ejemplo->descripcion) }}</textarea>
      <div class="ayuda">Una línea sobre qué muestra el documento.</div>
    </div>

    <div class="campo">
      <label for="archivo">Archivo del ejemplo</label>
      <div class="ayuda" style="margin-top:0;margin-bottom:6px">El documento con los ejemplos de contenido para ese
        nivel o sección.</div>
      @if($ejemplo->exists && $ejemplo->archivo)
      <div class="aviso" style="margin-bottom:10px">
        Archivo actual: <a href="{{ $ejemplo->url() }}"
          download="{{ $ejemplo->nombre_original }}">{{ $ejemplo->nombre_original }}</a>
        ({{ $ejemplo->tamanoLegible() }})<br>
        <span style="color:var(--tinta2)">Si no elegís uno nuevo, se mantiene este.</span>
      </div>
      @endif
      <input id="archivo" name="archivo" type="file" accept=".docx,.doc,.odt,.pdf"
        {{ $ejemplo->archivo ? '' : 'required' }} style="width:100%;font-size:.9rem;padding:9px 0">
      <div class="ayuda">Word, OpenDocument o PDF (.docx, .doc, .odt, .pdf). Hasta 20 MB.</div>
    </div>

    <div class="campo" style="display:flex;gap:22px;align-items:flex-end;flex-wrap:wrap">
      <div style="width:110px">
        <label for="orden">Orden</label>
        <input id="orden" name="orden" type="number" min="0" max="999" value="{{ old('orden', $ejemplo->orden ?? 0) }}"
          required>
      </div>
      <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-bottom:10px">
        <input type="checkbox" name="activo" value="1" style="width:auto" @checked(old('activo', $ejemplo->activo ??
        true))>
        Visible en el sitio
      </label>
    </div>

    <div class="acciones" style="margin-top:6px">
      <button class="btn-p" type="submit">{{ $ejemplo->exists ? 'Guardar cambios' : 'Subir ejemplo' }}</button>
      <a class="boton btn-s" href="/panel">Cancelar</a>
    </div>
  </form>
</div>
@endsection
