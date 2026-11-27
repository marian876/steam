@extends('layouts.base')
@section('titulo', 'Crear cuenta')
@section('ancho', 'estrecho')

@section('contenido')
<h1>Crear cuenta</h1>
<p class="bajada">Registrate como alumno con tu correo del colegio. Un profesor o administrador asignará tu grupo.</p>

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
      <div class="ayuda">Se aceptan @sil.edu.py y @alumnos.sil.edu.py.</div>
    </div>

    <div class="campo">
      <label for="curso">Curso</label>
      <select id="curso" name="curso" required style="width:100%;font-size:.92rem;padding:9px 12px">
        <option value="">Elegí tu curso…</option>
        @foreach($cursos as $curso)
        <option value="{{ $curso }}" {{ old('curso') === $curso ? 'selected' : '' }}>{{ $curso }}</option>
        @endforeach
      </select>
      <div class="ayuda">Tu profesor o administrador asignará tu grupo después del registro.</div>
    </div>

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

@endsection
