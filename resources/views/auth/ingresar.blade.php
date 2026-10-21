@extends('layouts.base')
@section('titulo', 'Ingresar')
@section('ancho', 'estrecho')

@section('contenido')
<h1>Ingresar</h1>
<p class="bajada">Con tu correo del colegio.</p>

<div class="tarjeta">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="/ingresar">
    @csrf
    <div class="campo">
      <label for="email">Correo</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="campo">
      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" required>
    </div>

    <div class="campo">
      <label style="display:flex;align-items:center;gap:8px;font-weight:400">
        <input type="checkbox" name="recordarme" value="1" style="width:auto"> No cerrar la sesión
      </label>
    </div>

    <button class="btn-p" type="submit">Ingresar</button>
  </form>

  <div class="pie-form">¿Todavía no tenés cuenta? <a href="/registro">Crear una</a></div>
</div>
@endsection
