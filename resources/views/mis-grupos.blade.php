@extends('layouts.base')
@section('titulo', 'Mis grupos')
@section('ancho', 'estrecho')

@section('contenido')
<h1>Mis grupos</h1>
<p class="bajada">Tu grupo de proyecto de cada año.</p>

@if(session('aviso'))
<div class="aviso">{{ session('aviso') }}</div>
@endif

<div class="tarjeta">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  @if($mios->isEmpty())
  <p style="margin:0 0 16px;color:var(--tinta2)">Todavía no elegiste ningún grupo.</p>
  @else
  <ul style="margin:0 0 18px;padding-left:18px">
    @foreach($mios as $g)
    <li><strong>{{ $g->nombre() }}</strong></li>
    @endforeach
  </ul>
  @endif

  @if($usuario->esAlumno())
  <p style="margin:0;color:var(--tinta2);font-size:.9rem">Para agregar o cambiar un grupo, avisale a un administrador.</p>
  @else
  <p style="margin:0;color:var(--tinta2);font-size:.9rem">Tus grupos los asigna el administrador.</p>
  @endif

  <div class="pie-form"><a href="/">← Volver al inicio</a></div>
</div>
@endsection
