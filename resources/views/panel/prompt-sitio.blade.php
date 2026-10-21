@extends('layouts.panel')
@section('titulo', 'Editar prompt')

@section('contenido')
<h1>Editar prompt</h1>
<p class="bajada">{{ $guia->nombre() }} › <a href="/{{ $guia->pagina }}">{{ $prompt['pagina'] }}</a></p>

<div class="tarjeta">
  @if(! $hayTabla)
  <div class="errores">
    <strong>Todavía no se pueden guardar cambios.</strong><br>
    Falta importar <code>crear_prompts_sitio_web.sql</code> en phpMyAdmin.
  </div>
  @endif

  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="/panel/{{ $guia->pagina }}/prompts/{{ $prompt['clave'] }}">
    @csrf @method('PUT')

    <div class="campo">
      <label for="titulo">Título</label>
      <input id="titulo" name="titulo" type="text" value="{{ old('titulo', $prompt['titulo']) }}" required autofocus>
      <div class="ayuda">Es lo que se ve en el botón del prompt, antes de desplegarlo.</div>
    </div>

    <div class="campo">
      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="3" required
        style="font-family:var(--texto);font-size:.92rem">{{ old('descripcion', $prompt['descripcion']) }}</textarea>
      <div class="ayuda">Se ve al desplegar, arriba del prompt.</div>
    </div>

    <div class="campo">
      <label for="texto">Texto del prompt</label>
      <textarea id="texto" name="texto" rows="20" required>{{ old('texto', $prompt['texto']) }}</textarea>
      <div class="ayuda">Es lo que se copia con el botón. Los saltos de línea se respetan tal cual.</div>
      @if($automaticos)
      <div class="ayuda" style="margin-top:8px">
        <strong>No borres ni cambies estos textos entre corchetes:</strong> la página los reemplaza sola con lo que
        completa el alumno.
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px">
          @foreach($automaticos as $a)<code>{{ $a }}</code>@endforeach
        </div>
      </div>
      @endif
    </div>

    <div class="acciones" style="margin-top:6px">
      <button class="btn-p" type="submit" @disabled(! $hayTabla)>Guardar cambios</button>
      <a class="boton btn-s" href="/panel#{{ $guia->pestana() }}">Cancelar</a>
    </div>
  </form>

  @if($prompt['modificado'])
  <form method="POST" action="/panel/{{ $guia->pagina }}/prompts/{{ $prompt['clave'] }}" style="margin-top:18px;padding-top:16px;border-top:1px solid var(--linea)"
    onsubmit="return confirm('¿Volver al texto original de este prompt? Se pierden los cambios hechos desde el panel.')">
    @csrf @method('DELETE')
    <p style="margin:0 0 10px;color:var(--tinta2);font-size:.9rem">Este prompt está modificado desde el panel.</p>
    <button class="boton btn-s" type="submit">Restaurar el original</button>
  </form>
  @endif
</div>
@endsection
