@extends('layouts.base')
@section('titulo', 'Tarea')
@section('contenido')
<h1>{{ $actividad->titulo }}</h1><p class="bajada">{{ $actividad->materia }} · {{ $actividad->vence_en ? \Carbon\Carbon::parse($actividad->vence_en)->format('d/m/Y H:i') : 'Sin fecha' }}</p>
@if(session('aviso')) <div class="aviso">{{ session('aviso') }}</div> @endif
@if($errors->any()) <div class="errores"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
<div class="tarjeta"><h2>Consigna</h2><p style="white-space:pre-wrap">{{ $actividad->consigna }}</p></div>
@if(auth()->user()->esAlumno())
<div class="tarjeta"><h2>Entregar por el grupo</h2><p>Una persona entrega el trabajo de todo el grupo. Una nueva entrega conserva las versiones anteriores. Se aceptan PDF, Word, Excel, PowerPoint e imágenes, hasta 20 MB.</p><form method="POST" action="/aula/tareas/{{ $actividad->id }}/entregas" enctype="multipart/form-data">@csrf<div class="campo"><label>Archivo</label><input type="file" name="archivo" required accept=".pdf,.docx,.xlsx,.pptx,.png,.jpg,.jpeg"></div><div class="campo"><label>Comentario (opcional)</label><textarea name="comentario" maxlength="5000"></textarea></div><button class="btn-p">Enviar entrega</button></form></div>
@endif
<div class="tarjeta"><h2>Historial y devoluciones</h2>@forelse($entregas as $e)<div><p><strong>Entrega {{ $e->id }}</strong> · {{ $e->created_at }} · <a href="/aula/entregas/{{ $e->id }}/archivo">{{ $e->nombre_original }}</a></p>@if($e->comentario)<p>{{ $e->comentario }}</p>@endif
@foreach($devoluciones->get($e->id, collect()) as $d)<p><strong>Devolución docente · {{ $d->created_at }}:</strong> {{ $d->comentario }}</p>@endforeach
@if(auth()->user()->esDocente())<form method="POST" action="/aula/entregas/{{ $e->id }}/devoluciones">@csrf<div class="campo"><label>Devolución para el grupo</label><textarea name="comentario" required maxlength="10000"></textarea></div><button class="btn-p">Guardar devolución</button></form>@endif<hr></div>@empty<p>Todavía no hay entregas.</p>@endforelse</div><p><a href="/aula">← Volver al aula</a></p>
@endsection
