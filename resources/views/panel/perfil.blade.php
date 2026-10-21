@extends('layouts.panel')
@section('titulo', 'Mi perfil')
@section('panel_activo', 'perfil')

@section('contenido')
<h1>Mi perfil</h1>
<p class="bajada">Cambiá tus datos de acceso.</p>

<div class="tarjeta" style="max-width:520px">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="/panel/perfil">
    @csrf @method('PUT')

    <div class="campo">
      <label for="name">Nombre</label>
      <input id="name" name="name" type="text" value="{{ old('name', $usuario->name) }}" required>
    </div>

    <div class="campo">
      <label for="apellido">Apellido</label>
      <input id="apellido" name="apellido" type="text" value="{{ old('apellido', $usuario->apellido) }}" required>
    </div>

    <div class="campo">
      <label for="email">Correo</label>
      <input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}" required>
      <div class="ayuda">Tu rol actual es <strong>{{ $usuario->etiquetaRol() }}</strong>.</div>
    </div>

    <div class="campo">
      <label>Tus grupos</label>
      @forelse($grupos as $g)
      <span class="chip">{{ $g->nombre() }}</span>
      @empty
      <div class="ayuda" style="margin:0">Todavía no tenés grupos asignados.</div>
      @endforelse
      @if($grupos->isNotEmpty())<div class="ayuda">Los grupos los asigna el administrador.</div>@endif
    </div>

    <div class="campo">
      <label for="password">Contraseña nueva</label>
      <input id="password" name="password" type="password" autocomplete="new-password">
      <div class="ayuda">Dejala en blanco si no querés cambiarla. Mínimo 8 caracteres.</div>
    </div>

    <div class="campo">
      <label for="password_confirmation">Repetir contraseña nueva</label>
      <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
    </div>

    <div class="acciones" style="margin-top:6px">
      <button class="btn-p" type="submit">Guardar cambios</button>
      <a class="boton btn-s" href="/panel">Volver al panel</a>
    </div>
  </form>
</div>
@endsection
