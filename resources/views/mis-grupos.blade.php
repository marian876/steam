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
  @if($disponibles->isEmpty())
  <p style="margin:0;color:var(--tinta2);font-size:.9rem">No hay grupos nuevos para elegir. Si te falta el de este
    año, avisale a tu docente.</p>
  @else
  <form method="POST" action="/mis-grupos">
    @csrf
    <div class="campo">
      <label for="grupo">Agregar el grupo de otro año</label>
      <select id="grupo" name="grupo" required style="width:100%;font-size:.92rem;padding:9px 12px">
        <option value="">Elegí tu grupo…</option>
        @foreach($disponibles as $g)
        <option value="{{ $g->id }}">{{ $g->nombre() }}</option>
        @endforeach
      </select>
      <div class="ayuda">Podés tener un grupo por año.</div>
    </div>
    <button class="btn-p" type="submit">Agregar grupo</button>
  </form>
  @endif
  @else
  <p style="margin:0;color:var(--tinta2);font-size:.9rem">Tus grupos los asigna el administrador.</p>
  @endif

  <div class="pie-form"><a href="/">← Volver al inicio</a></div>
</div>
@endsection
