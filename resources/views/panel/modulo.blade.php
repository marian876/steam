@extends('layouts.panel')
@section('titulo', $guia->nombre())
@section('contenido')
<h1>{{ $guia->nombre() }}</h1><p class="bajada">Editá los prompts y administrá los archivos disponibles en esta guía. <a href="/{{ $guia->pagina }}">Ver guía</a></p>
@if($errors->any())<div class="errores"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="tarjeta"><h2>Prompts</h2>@unless($hayPrompts)<p>Falta importar el SQL de módulos para guardar cambios.</p>@endunless
<table class="lista"><thead><tr><th>Paso</th><th>Prompt</th><th>Estado</th><th></th></tr></thead><tbody>@foreach($prompts as $p)<tr><td>{{ $p['pagina'] }}</td><td>{{ $p['titulo'] }}</td><td>{{ $p['modificado'] ? 'Modificado' : 'Original' }}</td><td><a href="/panel/{{ $guia->pagina }}/prompts/{{ $p['clave'] }}">Editar</a></td></tr>@endforeach</tbody></table></div>
<div class="tarjeta"><h2>Archivos para descargar</h2><p>Los archivos activos aparecerán en el inicio de esta guía. Los archivos ocultos se conservan y dejan de estar disponibles para descargar.</p>
@unless($hayArchivos)<p>Falta importar el SQL de módulos para subir archivos.</p>@else
<form method="POST" action="/panel/modulos/{{ $guia->pagina }}/archivos" enctype="multipart/form-data">@csrf<div class="campo"><label for="titulo">Título</label><input id="titulo" name="titulo" required maxlength="180"></div><div class="campo"><label for="archivo">Archivo (máximo 20 MB)</label><input id="archivo" type="file" name="archivo" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg,.webp"></div><button class="btn-p">Subir archivo</button></form>
@forelse($archivos as $a)<p><strong>{{ $a->titulo }}</strong> · {{ $a->nombre_original }} · {{ $a->activo ? 'Visible' : 'Oculto' }} @if($a->activo)<a href="/materiales/{{ $a->id }}">Descargar</a>@endif</p><form method="POST" action="/panel/modulos/{{ $guia->pagina }}/archivos/{{ $a->id }}">@csrf @method('PUT')<button class="btn-s" type="submit">{{ $a->activo ? 'Ocultar' : 'Mostrar' }}</button></form>@empty<p>No hay archivos cargados desde el panel.</p>@endforelse
@endunless</div>
@endsection
