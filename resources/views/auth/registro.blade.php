@extends('layouts.base')
@section('titulo', 'Crear cuenta')
@section('ancho', 'estrecho')

@section('contenido')
<h1>Crear cuenta</h1>
<p class="bajada">Con el correo del colegio. El rol se asigna solo según el dominio.</p>

<div class="tarjeta">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="/registro">
    @csrf
    <div class="campo">
      <label for="name">Nombre</label>
      <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
    </div>

    <div class="campo">
      <label for="apellido">Apellido</label>
      <input id="apellido" name="apellido" type="text" value="{{ old('apellido') }}" required>
    </div>

    <div class="campo">
      <label for="email">Correo del colegio</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required>
      <div class="ayuda">@sil.edu.py entra como docente · @alumnos.sil.edu.py entra como alumno</div>
    </div>

    @if($grupos->isNotEmpty())
    <div class="campo" id="campo-grupo" {{ old('grupo') ? '' : 'hidden' }}>
      <label for="grupo">Tu grupo</label>
      <select id="grupo" name="grupo" style="width:100%;font-size:.92rem;padding:9px 12px">
        <option value="">Elegí tu grupo…</option>
        @foreach($grupos as $g)
        <option value="{{ $g->id }}" {{ (int) old('grupo') === $g->id ? 'selected' : '' }}>{{ $g->nombre() }}</option>
        @endforeach
      </select>
      <div class="ayuda">Si no aparece tu grupo, avisale a tu docente.</div>
    </div>
    @endif

    <div class="campo">
      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" required>
      <div class="ayuda">Mínimo 8 caracteres.</div>
    </div>

    <div class="campo">
      <label for="password_confirmation">Repetir la contraseña</label>
      <input id="password_confirmation" name="password_confirmation" type="password" required>
    </div>

    <button class="btn-p" type="submit">Crear cuenta</button>
  </form>

  <div class="pie-form">¿Ya tenés cuenta? <a href="/ingresar">Ingresar</a></div>
</div>

<script>
  // El grupo solo se pide a los alumnos (correo @alumnos.sil.edu.py).
  (function () {
    var email = document.getElementById('email');
    var campo = document.getElementById('campo-grupo');
    if (!campo) return;
    var grupo = document.getElementById('grupo');
    function revisar() {
      var alumno = /@alumnos\.sil\.edu\.py$/i.test(email.value.trim());
      campo.hidden = !alumno;
      grupo.required = alumno;
    }
    email.addEventListener('input', revisar);
    revisar();
  })();
</script>
@endsection
