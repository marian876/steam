@extends('layouts.base')
@section('titulo', 'Aula del proyecto')
@section('contenido')
@php($rutaAula = $vistaAlumno ? '/panel/vista-alumno/'.$alumnoVista->id : '/aula')
@php($rutaTrabajos = $vistaAlumno ? $rutaAula.'/trabajos' : '/aula/trabajos')
@if($vistaAlumno)<div class="aviso"><strong>Vista previa de {{ $alumnoVista->nombreCompleto() }}</strong> · Solo lectura. <a href="/panel/vista-alumno">Elegir otro alumno</a> · <a href="/panel">Volver al panel</a></div>@endif
<h1>{{ $seccionTrabajos ? 'Entrega de trabajos' : ($vistaAlumno || auth()->user()->esAlumno() ? 'Mi panel de alumno' : 'Aula del proyecto') }}</h1>
<p class="bajada">Tareas, entregas por grupo y fechas del proyecto.</p>
<p><a href="{{ $rutaAula }}">Calendario y panel</a> · <a href="{{ $rutaTrabajos }}">Entrega de trabajos</a></p>
@if(session('aviso')) <div class="aviso">{{ session('aviso') }}</div> @endif
@if($errors->any()) <div class="errores"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
@unless($seccionTrabajos)
<div class="tarjeta"><h2>Calendario de entregas</h2><p>Las fechas y horas se muestran como fueron cargadas por el docente (hora de Paraguay).</p>
@if(! $hayPlazos && auth()->user()->esDocente() && ! $vistaAlumno)<p>Falta importar el SQL de plazos para habilitar el calendario de etapas.</p>@endif
<p><a href="{{ $rutaAula }}?mes={{ $mes->copy()->subMonth()->format('Y-m') }}">← Mes anterior</a> · <strong>{{ $mes->copy()->locale('es')->translatedFormat('F Y') }}</strong> · <a href="{{ $rutaAula }}?mes={{ $mes->copy()->addMonth()->format('Y-m') }}">Mes siguiente →</a></p>
@php($fechas = $tareas->filter(fn ($t) => $t->vence_en && \Carbon\Carbon::parse($t->vence_en)->format('Y-m') === $mes->format('Y-m'))->groupBy(fn ($t) => (int) \Carbon\Carbon::parse($t->vence_en)->format('j')))
@php($fechasPlazos = $plazos->filter(fn ($p) => \Carbon\Carbon::parse($p->vence_en)->format('Y-m') === $mes->format('Y-m'))->groupBy(fn ($p) => (int) \Carbon\Carbon::parse($p->vence_en)->format('j')))
<div style="overflow-x:auto"><table class="lista" aria-label="Calendario de entregas"><thead><tr>@foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $dia)<th scope="col">{{ $dia }}</th>@endforeach</tr></thead><tbody>
@for($semana = 0; $semana < 6; $semana++)
<tr>@for($col = 0; $col < 7; $col++) @php($dia = $semana * 7 + $col - ($mes->dayOfWeekIso - 1) + 1)
<td>@if($dia > 0 && $dia <= $mes->daysInMonth)<strong>{{ $dia }}</strong>@foreach($fechas->get($dia, collect()) as $item)<div><a href="{{ $vistaAlumno ? $rutaAula.'/tareas/'.$item->id : '/aula/tareas/'.$item->id }}">{{ $item->titulo }}</a><br>{{ \Carbon\Carbon::parse($item->vence_en)->format('H:i') }}</div>@endforeach @foreach($fechasPlazos->get($dia, collect()) as $plazo)<div><strong>{{ $plazo->materia }}:</strong> {{ $plazo->etapa }}<br>{{ \Carbon\Carbon::parse($plazo->vence_en)->format('H:i') }}@if($plazo->descripcion)<br>{{ $plazo->descripcion }}@endif</div>@endforeach @endif</td>@endfor</tr>
@endfor</tbody></table></div>
</div>
@endunless
<div class="tarjeta"><h2>{{ $seccionTrabajos ? 'Trabajos por materia' : 'Tareas' }}</h2>
@if($seccionTrabajos)<p>En <strong>General</strong> se entregan los documentos finales, como el macro y la presentación. Los docentes crean las tareas y fechas para cada grupo.</p>@if(! $admiteParciales)<p>La configuración actual admite únicamente entregas finales en General. Los trabajos parciales anteriores se conservan.</p>@endif @endif
@if(auth()->user()->esDocente() && ! $vistaAlumno)<p>Seguimiento por grupo: pendiente de entrega, pendiente de corrección, corregido o nueva versión habilitada.</p><div class="campo"><label for="filtrar-materia">Buscar materia, tarea o grupo</label><input id="filtrar-materia" type="search"></div><div class="campo"><label for="filtrar-estado">Estado</label><select id="filtrar-estado"><option value="">Todos</option><option value="pendiente">Pendiente de entrega</option><option value="revision">Pendiente de corrección</option><option value="corregido">Corregido</option><option value="reabierto">Nueva versión habilitada</option></select></div>@endif
@php($tareasPorMateria = $tareas->groupBy(fn ($t) => mb_strtolower(trim($t->materia))) )
@php($categorias = collect(['contabilidad' => 'Contabilidad', 'marketing' => 'Marketing', 'business administration' => 'Business Administration', 'economics' => 'Economics', 'general' => 'General']))
@foreach($tareasPorMateria->keys() as $claveMateria) @if(! $categorias->has($claveMateria)) @php($categorias->put($claveMateria, $tareasPorMateria[$claveMateria]->first()->materia)) @endif @endforeach
@foreach($categorias as $claveMateria => $nombreMateria)
@if(! $seccionTrabajos && ! $tareasPorMateria->has($claveMateria)) @continue @endif
@if($seccionTrabajos && ! $admiteParciales && (! auth()->user()->esDocente() || $vistaAlumno) && $claveMateria !== 'general' && ! $tareasPorMateria->has($claveMateria)) @continue @endif
<h3>{{ $nombreMateria }}</h3>
@if($claveMateria === 'general' && $seccionTrabajos)<p>Macro, presentación y otras entregas finales definidas por los docentes.</p>@endif
@forelse($tareasPorMateria->get($claveMateria, collect()) as $t)
@php($ultimaEntrega = $entregasGrupo->get($t->id, collect())->first())
@php($rutaTarea = $vistaAlumno ? $rutaAula.'/tareas/'.$t->id : '/aula/tareas/'.$t->id)
@php($estadoTarea = ! $ultimaEntrega ? 'pendiente' : ($t->permite_nueva_entrega ? 'reabierto' : ($devolucionesGrupo->get($ultimaEntrega->id, collect())->isEmpty() ? 'revision' : 'corregido')))
@php($nombreGrupo = auth()->user()->esDocente() && ! $vistaAlumno ? (optional($grupos->firstWhere('id', $t->grupo_id))->nombre() ?? 'Grupo no disponible') : '')
<div class="aula-tarea" data-estado="{{ $estadoTarea }}" data-buscar="{{ mb_strtolower($t->titulo.' '.$t->materia.' '.$nombreGrupo) }}" style="padding:12px 0;border-bottom:1px solid var(--linea)"><p><a href="{{ $rutaTarea }}"><strong>{{ $t->titulo }}</strong></a> · {{ $t->materia }} · {{ $t->vence_en ? \Carbon\Carbon::parse($t->vence_en)->format('d/m/Y H:i') : 'Sin fecha' }}</p>
@if(auth()->user()->esDocente() && ! $vistaAlumno)<p>Grupo: {{ optional($grupos->firstWhere('id', $t->grupo_id))->nombre() ?? 'Grupo no disponible' }}</p>@endif
@if($ultimaEntrega)
<p>Entregado por <strong>{{ $ultimaEntrega->alumno_nombre }} {{ $ultimaEntrega->alumno_apellido }}</strong> · <a href="/aula/entregas/{{ $ultimaEntrega->id }}/archivo">{{ $ultimaEntrega->nombre_original }}</a></p>
@if($t->permite_nueva_entrega)<p><strong>Nueva versión habilitada.</strong> <a href="{{ $rutaTarea }}">Ver tarea</a></p>@elseif($devolucionesGrupo->get($ultimaEntrega->id, collect())->isEmpty())<p>Estado: pendiente de corrección.</p>@else<p>Estado: corregido.</p>@endif
@forelse($devolucionesGrupo->get($ultimaEntrega->id, collect()) as $d)
<div><strong>Devolución de {{ $d->docente_nombre }} {{ $d->docente_apellido }}</strong>
@if($d->calificacion !== null && $d->calificacion !== '')<p>Calificación: {{ $d->calificacion }}</p>@endif
@if($d->errores)<p>Errores o aspectos a corregir: {{ $d->errores }}</p>@endif
@if($d->comentario)<p>Observaciones: {{ $d->comentario }}</p>@endif</div>
@empty <p>La entrega todavía no tiene devolución.</p> @endforelse
@else <p>Pendiente de entrega · <a href="{{ $rutaTarea }}">Ver consigna y entregar</a></p> @endif</div>
@empty <p>No hay trabajos en esta sección.</p> @endforelse
@endforeach
@if($tareas->isEmpty())<p>No hay tareas disponibles. Si sos alumno, un profesor o administrador debe asignarte un grupo.</p>@endif
</div>
@if(auth()->user()->esDocente() && ! $vistaAlumno)
<div class="tarjeta"><h2>Crear tarea y fecha de entrega</h2><form method="POST" action="/aula/tareas">@csrf
<fieldset class="campo"><legend>Grupos destinatarios</legend><p>Seleccioná uno o varios grupos. Se creará una tarea independiente para cada uno.</p>@forelse($grupos->where('activo', true) as $g)<label style="display:block"><input type="checkbox" name="grupos[]" value="{{ $g->id }}"> {{ $g->nombre() }}</label>@empty<p>No hay grupos activos.</p>@endforelse</fieldset>
<div class="campo"><label>Título</label><input name="titulo" required maxlength="180" placeholder="Ej.: Macro final"></div><div class="campo"><label for="materia-tarea">Materia o sección</label><input id="materia-tarea" name="materia" list="materias-tarea" required maxlength="100" @if(! $admiteParciales) value="General" readonly @endif><datalist id="materias-tarea"><option value="General"><option value="Contabilidad"><option value="Marketing"><option value="Business Administration"><option value="Economics"></datalist><div class="ayuda">Usá General para macro, presentación y demás entregas finales.</div></div>
<div class="campo"><label>Consigna</label><textarea name="consigna" required maxlength="10000"></textarea></div><div class="campo"><label>Fecha y hora de entrega (opcional)</label><input type="datetime-local" name="vence_en"></div><button class="btn-p">Crear tarea</button></form></div>
@endif
@if(auth()->user()->esDocente() && ! $vistaAlumno && ! $seccionTrabajos)
<div class="tarjeta"><h2>Asignar alumno a un grupo</h2><p>Comprobá la identidad del alumno y seleccioná un grupo de su curso.</p><form method="POST" action="/aula/autorizaciones">@csrf
<div class="campo"><label>Alumno</label><select name="user_id" required>@foreach($usuarios as $u)<option value="{{ $u->id }}">{{ $u->nombreCompleto() }} · {{ $u->curso ?: 'Curso no registrado' }} · {{ $u->email }}</option>@endforeach</select></div>
<div class="campo"><label>Grupo</label><select name="grupo_id" required>@foreach($grupos as $g)<option value="{{ $g->id }}">{{ $g->nombre() }}</option>@endforeach</select></div><button class="btn-p">Asignar grupo</button></form></div>
@endif
@if(auth()->user()->esDocente() && ! $vistaAlumno)
<script>(function(){var texto=document.getElementById('filtrar-materia'),estado=document.getElementById('filtrar-estado');function filtrar(){var q=texto.value.trim().toLocaleLowerCase(),e=estado.value;document.querySelectorAll('.aula-tarea').forEach(function(t){t.hidden=(q&&!t.dataset.buscar.includes(q))||(e&&t.dataset.estado!==e);});}texto.addEventListener('input',filtrar);estado.addEventListener('change',filtrar);})();</script>
@endif
@endsection
