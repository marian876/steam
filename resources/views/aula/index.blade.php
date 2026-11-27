@extends('layouts.base')
@section('titulo', 'Aula del proyecto')
@section('contenido')
<h1>Aula del proyecto</h1>
<p class="bajada">Tareas, entregas por grupo y fechas del proyecto.</p>
@if(session('aviso')) <div class="aviso">{{ session('aviso') }}</div> @endif
@if($errors->any()) <div class="errores"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
<div class="tarjeta"><h2>Calendario de entregas</h2><p>Las fechas y horas se muestran como fueron cargadas por el docente (hora de Paraguay).</p>
<p><a href="/aula?mes={{ $mes->copy()->subMonth()->format('Y-m') }}">← Mes anterior</a> · <strong>{{ $mes->copy()->locale('es')->translatedFormat('F Y') }}</strong> · <a href="/aula?mes={{ $mes->copy()->addMonth()->format('Y-m') }}">Mes siguiente →</a></p>
@php($fechas = $tareas->filter(fn ($t) => $t->vence_en && \Carbon\Carbon::parse($t->vence_en)->format('Y-m') === $mes->format('Y-m'))->groupBy(fn ($t) => (int) \Carbon\Carbon::parse($t->vence_en)->format('j')))
<div style="overflow-x:auto"><table class="lista" aria-label="Calendario de entregas"><thead><tr>@foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $dia)<th scope="col">{{ $dia }}</th>@endforeach</tr></thead><tbody>
@for($semana = 0; $semana < 6; $semana++)
<tr>@for($col = 0; $col < 7; $col++) @php($dia = $semana * 7 + $col - ($mes->dayOfWeekIso - 1) + 1)
<td>@if($dia > 0 && $dia <= $mes->daysInMonth)<strong>{{ $dia }}</strong>@foreach($fechas->get($dia, collect()) as $item)<div><a href="/aula/tareas/{{ $item->id }}">{{ $item->titulo }}</a><br>{{ \Carbon\Carbon::parse($item->vence_en)->format('H:i') }}</div>@endforeach @endif</td>@endfor</tr>
@endfor</tbody></table></div>
</div>
<div class="tarjeta"><h2>Tareas</h2>
@forelse($tareas as $t)
<p><a href="/aula/tareas/{{ $t->id }}"><strong>{{ $t->titulo }}</strong></a> · {{ $t->materia }} · {{ $t->vence_en ? \Carbon\Carbon::parse($t->vence_en)->format('d/m/Y H:i') : 'Sin fecha' }}</p>
@empty <p>No hay tareas disponibles. Si sos alumno, un profesor o administrador debe asignarte un grupo.</p> @endforelse
</div>
@if(auth()->user()->esDocente())
<div class="tarjeta"><h2>Crear tarea y fecha de entrega</h2><form method="POST" action="/aula/tareas">@csrf
<div class="campo"><label>Grupo</label><select name="grupo_id" required>@foreach($grupos as $g)<option value="{{ $g->id }}">{{ $g->nombre() }}</option>@endforeach</select></div>
<div class="campo"><label>Título</label><input name="titulo" required maxlength="180"></div><div class="campo"><label>Materia</label><input name="materia" required maxlength="100"></div>
<div class="campo"><label>Consigna</label><textarea name="consigna" required maxlength="10000"></textarea></div><div class="campo"><label>Fecha y hora de entrega (opcional)</label><input type="datetime-local" name="vence_en"></div><button class="btn-p">Crear tarea</button></form></div>
@endif
@if(auth()->user()->esDocente())
<div class="tarjeta"><h2>Asignar alumno a un grupo</h2><p>Comprobá la identidad del alumno y seleccioná un grupo de su curso.</p><form method="POST" action="/aula/autorizaciones">@csrf
<div class="campo"><label>Alumno</label><select name="user_id" required>@foreach($usuarios as $u)<option value="{{ $u->id }}">{{ $u->nombreCompleto() }} · {{ $u->curso ?: 'Curso no registrado' }} · {{ $u->email }}</option>@endforeach</select></div>
<div class="campo"><label>Grupo</label><select name="grupo_id" required>@foreach($grupos as $g)<option value="{{ $g->id }}">{{ $g->nombre() }}</option>@endforeach</select></div><button class="btn-p">Asignar grupo</button></form></div>
@endif
@endsection
