@extends('layouts.panel')
@section('titulo', 'Cursos')
@section('contenido')
<h1>Configuración · Datos generales</h1>
<p class="bajada">Cursos habilitados para el registro.</p>
@include('panel._admin-tabs', ['actual' => 'cursos'])
<div class="tarjeta"><table class="lista"><thead><tr><th>Curso</th><th>Alumnos registrados</th></tr></thead><tbody>
@foreach($cursos as $curso)<tr><td>{{ $curso }}</td><td>{{ $conteos[$curso] ?? 0 }}</td></tr>@endforeach
</tbody></table><p>Los grupos se administran en la pestaña Grupos.</p></div>
@endsection
