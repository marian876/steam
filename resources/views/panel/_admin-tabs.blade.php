{{-- Subventanas de Administración. Uso: @include('panel._admin-tabs', ['actual' => 'usuarios']) --}}
<nav class="sub-tabs" aria-label="Datos generales">
  <a href="/panel/usuarios" class="{{ $actual === 'usuarios' ? 'activo' : '' }}">Usuarios</a>
  <a href="/panel/alumnos" class="{{ $actual === 'alumnos' ? 'activo' : '' }}">Alumnos</a>
  <a href="/panel/cursos" class="{{ $actual === 'cursos' ? 'activo' : '' }}">Cursos</a>
  <a href="/panel/roles" class="{{ $actual === 'roles' ? 'activo' : '' }}">Roles</a>
  <a href="/panel/grupos" class="{{ $actual === 'grupos' ? 'activo' : '' }}">Grupos</a>
  <a href="/panel/configuracion/entregas" class="{{ $actual === 'entregas' ? 'activo' : '' }}">Entregas</a>
</nav>
