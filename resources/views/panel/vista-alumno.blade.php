@extends('layouts.panel')
@section('titulo', 'Ver como alumno')
@section('contenido')
<h1>Ver como alumno</h1>
<p class="bajada">Elegí una cuenta para consultar su panel y sus tareas. La vista es de solo lectura: no inicia sesión como esa persona ni permite entregar archivos en su nombre.</p>
<div class="tarjeta"><form id="seleccion-alumno"><div class="campo"><label for="alumno">Alumno</label><select id="alumno" required><option value="">Elegí un alumno…</option>@foreach($alumnos as $a)<option value="{{ $a->id }}">{{ $a->nombreCompleto() }} · {{ $a->curso ?: 'Sin curso' }} · {{ $a->email }}</option>@endforeach</select></div><button class="btn-p" type="submit">Ver su panel</button></form></div>
<script>document.getElementById('seleccion-alumno').addEventListener('submit',function(e){e.preventDefault();var id=document.getElementById('alumno').value;if(/^\d+$/.test(id))location.href='/panel/vista-alumno/'+id;});</script>
@endsection
