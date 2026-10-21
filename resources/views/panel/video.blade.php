@extends('layouts.panel')
@section('titulo', $video->exists ? 'Editar video' : 'Nuevo video')

@section('contenido')
<h1>{{ $video->exists ? 'Editar video' : 'Nuevo video' }}</h1>
<p class="bajada">El video se muestra en la página elegida del <strong>Formato STEAM</strong>, en un botón al lado del
  foquito. Uno por página.</p>

<div class="tarjeta">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ $video->exists ? '/panel/videos/'.$video->id : '/panel/videos' }}"
    enctype="multipart/form-data">
    @csrf
    @if($video->exists) @method('PUT') @endif

    <div class="campo">
      <label for="paso">Página</label>
      <select id="paso" name="paso" required style="width:100%;font-size:.95rem;padding:9px 8px">
        <option value="" disabled {{ old('paso', $video->paso) ? '' : 'selected' }}>Elegí una página…</option>
        @foreach($paginas as $clave => $nombre)
        @php($ocupada = in_array($clave, $ocupadas))
        <option value="{{ $clave }}" {{ old('paso', $video->paso) === $clave ? 'selected' : '' }} {{ $ocupada ?
          'disabled' : '' }}>
          {{ $nombre }}{{ $ocupada ? ' — ya tiene video' : '' }}
        </option>
        @endforeach
      </select>
      <div class="ayuda">Las páginas que ya tienen un video aparecen deshabilitadas. Para reemplazar uno, editá el que
        existe.</div>
    </div>

    <div class="campo">
      <label for="archivo">Archivo del video</label>
      @if($video->exists && $video->archivo)
      <div class="aviso" style="margin-bottom:10px">
        Archivo actual: <a href="{{ $video->url() }}" target="_blank"
          rel="noopener">{{ $video->nombre_original }}</a>
        ({{ $video->tamanoLegible() }})<br>
        <span style="color:var(--tinta2)">Si no elegís uno nuevo, se mantiene este.</span>
      </div>
      @endif
      <input id="archivo" name="archivo" type="file" accept=".mp4,.webm,.mov,.m4v,.ogv"
        {{ $video->archivo ? '' : 'required' }} style="width:100%;font-size:.9rem;padding:9px 0">
      <div class="ayuda">MP4, WebM, MOV, M4V u OGV. Hasta 100 MB (el hosting puede limitarlo a menos; si falla, subí
        uno más liviano).</div>
    </div>

    <div class="campo">
      <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-bottom:10px">
        <input type="checkbox" name="activo" value="1" style="width:auto" @checked(old('activo', $video->activo ??
        true))>
        Visible en el sitio
      </label>
    </div>

    <div class="acciones" style="margin-top:6px">
      <button class="btn-p" type="submit">{{ $video->exists ? 'Guardar cambios' : 'Subir video' }}</button>
      <a class="boton btn-s" href="/panel">Cancelar</a>
    </div>
  </form>
</div>
@endsection
