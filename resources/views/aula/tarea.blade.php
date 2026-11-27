@extends('layouts.base')
@section('titulo', 'Tarea')
@section('contenido')
@if($alumnoVista)<div class="aviso"><strong>Vista previa de {{ $alumnoVista->nombreCompleto() }}</strong> · Solo lectura. <a href="/panel/vista-alumno/{{ $alumnoVista->id }}">Volver a su panel</a></div>@endif
<h1>{{ $actividad->titulo }}</h1><p class="bajada">{{ $actividad->materia }} · {{ $actividad->vence_en ? \Carbon\Carbon::parse($actividad->vence_en)->format('d/m/Y H:i') : 'Sin fecha' }}</p>
@if(session('aviso')) <div class="aviso">{{ session('aviso') }}</div> @endif
@if($errors->any()) <div class="errores"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
<div class="tarjeta"><h2>Consigna</h2><p style="white-space:pre-wrap">{{ $actividad->consigna }}</p></div>
@if($alumnoVista && $entregas->isEmpty())
<div class="tarjeta"><h2>Entregar por el grupo</h2><p>Una persona entrega el trabajo de todo el grupo. Se aceptan PDF, Word, Excel, PowerPoint e imágenes, hasta 20 MB.</p><div class="campo"><label>Archivo</label><input type="file" disabled></div><div class="campo"><label>Comentario (opcional)</label><textarea disabled></textarea></div><button class="btn-p" disabled>Enviar entrega</button><p>El formulario está desactivado en la vista previa.</p></div>
@elseif(auth()->user()->esAlumno() && $entregas->isEmpty())
<div class="tarjeta"><h2>Entregar por el grupo</h2><p>Una persona entrega el trabajo de todo el grupo. Se aceptan PDF, Word, Excel, PowerPoint e imágenes, hasta 20 MB.</p><form method="POST" action="/aula/tareas/{{ $actividad->id }}/entregas" enctype="multipart/form-data">@csrf<div class="campo"><label>Archivo</label><input type="file" name="archivo" required accept=".pdf,.docx,.xlsx,.pptx,.png,.jpg,.jpeg"></div><div class="campo"><label>Comentario (opcional)</label><textarea name="comentario" maxlength="5000"></textarea></div><button class="btn-p">Enviar entrega</button></form></div>
@elseif(auth()->user()->esAlumno() || $alumnoVista)
<div class="tarjeta"><h2>Trabajo entregado</h2><p>Lo subió <strong>{{ $entregas->first()->alumno_nombre }} {{ $entregas->first()->alumno_apellido }}</strong> para todo el grupo. Consultá al profesor si necesitan corregir la entrega.</p></div>
@endif
<div class="tarjeta"><h2>Historial y devoluciones</h2>@forelse($entregas as $e)<div><p><strong>Entrega de {{ $e->alumno_nombre }} {{ $e->alumno_apellido }}</strong> · {{ $e->created_at }} · <a href="/aula/entregas/{{ $e->id }}/archivo">{{ $e->nombre_original }}</a></p>@if($e->comentario)<p>{{ $e->comentario }}</p>@endif
@foreach($devoluciones->get($e->id, collect()) as $d)<div><p><strong>Devolución de {{ $d->docente_nombre }} {{ $d->docente_apellido }} · {{ $d->created_at }}</strong></p>@if($d->calificacion !== null && $d->calificacion !== '')<p>Calificación: {{ $d->calificacion }}</p>@endif @if($d->errores)<p>Errores o aspectos a corregir: {{ $d->errores }}</p>@endif @if($d->comentario)<p>Observaciones: {{ $d->comentario }}</p>@endif</div>@endforeach
@if(auth()->user()->esDocente() && ! $alumnoVista)<form method="POST" action="/aula/entregas/{{ $e->id }}/devoluciones">@csrf<p>Completá al menos uno de los campos.</p><div class="campo"><label>Calificación (por ejemplo, 8/10)</label><input name="calificacion" maxlength="100"></div><div class="campo"><label>Errores o aspectos a corregir</label><textarea name="errores" maxlength="10000"></textarea></div><div class="campo"><label>Observaciones</label><textarea name="comentario" maxlength="10000"></textarea></div><button class="btn-p">Guardar devolución</button></form>@endif<hr></div>@empty<p>Todavía no hay entregas.</p>@endforelse</div><p><a href="{{ $alumnoVista ? '/panel/vista-alumno/'.$alumnoVista->id : '/aula' }}">← Volver al aula</a></p>
@endsection
