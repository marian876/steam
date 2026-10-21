{{-- Subventanas de Administración. Uso: @include('panel._admin-tabs', ['actual' => 'usuarios']) --}}
<nav class="sub-tabs" aria-label="Administración">
  <a href="/panel/usuarios" class="{{ $actual === 'usuarios' ? 'activo' : '' }}">Usuarios</a>
  <a href="/panel/roles" class="{{ $actual === 'roles' ? 'activo' : '' }}">Roles</a>
  <a href="/panel/grupos" class="{{ $actual === 'grupos' ? 'activo' : '' }}">Grupos</a>
</nav>
