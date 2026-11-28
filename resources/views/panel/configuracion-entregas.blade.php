@extends('layouts.panel')
@section('titulo', 'Configuración de entregas')
@section('contenido')
<h1>Configuración · Entregas</h1>
<p class="bajada">Elegí la modalidad de cada curso por separado.</p>
@include('panel._admin-tabs', ['actual' => 'entregas'])
<div class="tarjeta"><h2>Modalidad de entregas</h2>
@unless($hayTabla)<div class="errores">Importá el SQL de configuración por curso para guardar esta opción.</div>@endunless
@if($errors->any())<div class="errores"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="/panel/configuracion/entregas">@csrf @method('PUT')
@foreach($modalidades as $curso => $parciales)
<fieldset style="margin-bottom:18px"><legend><strong>{{ $curso }}</strong></legend>
<p><label><input type="radio" name="modalidades[{{ $curso }}]" value="todas" @checked($parciales)> Entregas parciales por materia y finales en General</label></p>
<p><label><input type="radio" name="modalidades[{{ $curso }}]" value="solo_finales" @checked(! $parciales)> Solo entregas finales en General</label></p>
</fieldset>
@endforeach
<p>En la opción de solo finales, los alumnos no ven ni pueden enviar tareas parciales. Las tareas y devoluciones existentes se conservan para volver a habilitarlas después. Los plazos del calendario no se eliminan.</p>
<button type="submit" class="btn-p" @disabled(! $hayTabla)>Guardar modalidad</button></form></div>
@endsection
