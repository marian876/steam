@extends('layouts.panel')
@section('titulo', $proyecto->exists ? $proyecto->empresa : 'Nuevo proyecto')
@section('panel_activo', 'proyectos')

@section('contenido')
@php($lectura = $proyecto->exists && $proyecto->soloLectura())
<h1>{{ $proyecto->exists ? ($lectura ? $proyecto->empresa : 'Editar proyecto') : 'Nuevo proyecto' }}</h1>
<p class="bajada"><a href="/panel/proyectos">← Volver a Proyectos</a></p>

<div class="tarjeta" style="max-width:680px">
  @if($errors->any())
  <div class="errores">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  @if($lectura)
  <div class="aviso"><strong>Solo lectura.</strong> El grupo {{ $proyecto->grupo->nombre() }} está inactivo: se conserva
    todo lo cargado pero no se puede modificar. Para cambiarlo, reactivá el grupo en Administración › Grupos.</div>
  @endif

  <form method="POST" enctype="multipart/form-data"
    action="{{ $proyecto->exists ? '/panel/proyectos/'.$proyecto->id : '/panel/proyectos' }}">
    @csrf
    @if($proyecto->exists) @method('PUT') @endif

    <fieldset {{ $lectura ? 'disabled' : '' }} style="border:0;padding:0;margin:0;min-width:0">
      <div class="campo">
        <label for="grupo_id">Grupo</label>
        <select id="grupo_id" name="grupo_id" required>
          <option value="">Elegí el grupo…</option>
          @foreach($grupos as $g)
          <option value="{{ $g->id }}" {{ (int) old('grupo_id', $proyecto->grupo_id) === $g->id ? 'selected' : '' }}>
            {{ $g->nombre() }}</option>
          @endforeach
          @if($lectura)
          <option value="{{ $proyecto->grupo_id }}" selected>{{ $proyecto->grupo->nombre() }}</option>
          @endif
        </select>
        <div class="ayuda">Solo aparecen grupos activos que todavía no tienen proyecto.</div>
      </div>

      <div class="campo">
        <label for="empresa">Nombre de la empresa</label>
        <input id="empresa" name="empresa" type="text" value="{{ old('empresa', $proyecto->empresa) }}" required
          maxlength="150">
      </div>

      <div class="campo">
        <label for="integrante-nuevo">Integrantes</label>
        @unless($hayIntegrantes)
        <div class="errores" style="margin:0 0 8px">Para poder guardar integrantes falta importar
          <code>crear_proyecto_integrantes.sql</code> en phpMyAdmin.</div>
        @endunless
        <div class="int-lista" id="int-lista" aria-live="polite"></div>
        @unless($lectura)
        <div class="int-agregar">
          <input id="integrante-nuevo" type="text" list="int-usuarios" autocomplete="off"
            placeholder="Escribí un nombre o elegí un usuario registrado">
          <button type="button" class="boton btn-s" id="int-agregar">Agregar</button>
        </div>
        <datalist id="int-usuarios">
          @foreach($usuarios as $u)
          <option value="{{ $u['nombre'] }} · {{ $u['detalle'] }}"></option>
          @endforeach
        </datalist>
        <button type="button" class="int-grupo" id="int-del-grupo">+ Agregar a los alumnos registrados del grupo elegido</button>
        <div class="ayuda">Pueden ser usuarios registrados (se eligen de la lista) o personas sin cuenta (se escribe el
          nombre y Enter). Aparecen en la ventana del proyecto, arriba de los botones.</div>
        @endunless
      </div>

      <div class="campo">
        <label for="descripcion">Descripción (optativa)</label>
        <textarea id="descripcion" name="descripcion" rows="3" maxlength="2000"
          style="font-family:var(--texto);font-size:.92rem">{{ old('descripcion', $proyecto->descripcion) }}</textarea>
        <div class="ayuda">Una o dos líneas sobre el producto o servicio de la empresa.</div>
      </div>

      <div class="campo">
        <label for="sitio_web">Sitio web</label>
        <input id="sitio_web" name="sitio_web" type="url" value="{{ old('sitio_web', $proyecto->sitio_web) }}"
          placeholder="https://…" maxlength="500">
      </div>

      @if($hayCiencap)
      <div class="campo">
        <label style="display:flex;align-items:center;gap:8px;font-weight:600">
          <input type="checkbox" name="ciencap" value="1" style="width:auto" @checked(old('ciencap', $proyecto->ciencap))>
          ★ Participó en la Ciencap
        </label>
        <div class="ayuda">En el sitio aparece con la etiqueta <strong>★ Ciencap</strong>.</div>
      </div>

      <div class="campo">
        <label for="logros">Logros obtenidos (optativo)</label>
        <textarea id="logros" name="logros" rows="3" maxlength="2000" style="font-family:var(--texto);font-size:.92rem"
          placeholder="Ej.: 1.er puesto en la categoría Tecnología · Mención especial del jurado">{{ old('logros', $proyecto->logros) }}</textarea>
        <div class="ayuda">Premios, menciones o reconocimientos. Un logro por renglón.</div>
      </div>
      @else
      <div class="errores">Para marcar la Ciencap y cargar los logros obtenidos falta importar
        <code>agregar_ciencap_logros.sql</code> en phpMyAdmin.</div>
      @endif

      <div class="campo">
        <label for="logo">Logo de la empresa</label>
        @if($proyecto->logoUrl())
        <div class="pry-actual">
          <img src="{{ $proyecto->logoUrl() }}" alt="Logo actual" class="pry-logo-grande">
          @unless($lectura)
          <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0">
            <input type="checkbox" name="logo_quitar" value="1" style="width:auto"> Quitar el logo
          </label>
          @endunless
        </div>
        @endif
        @unless($lectura)
        <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp" style="padding:8px 0;background:none;border:0">
        <div class="ayuda">JPG, PNG o WebP, hasta 2 MB. {{ $proyecto->logo ? 'Si subís otro, reemplaza al actual.' : '' }}</div>
        @endunless
      </div>

      <div class="campo">
        <label for="archivos">Archivos del proyecto</label>
        @if($proyecto->exists && $proyecto->archivos->isNotEmpty())
        <ul class="pry-archivos">
          @foreach($proyecto->archivos as $a)
          <li>
            <span class="etq no">{{ $a->tipo() }}</span>
            <a href="{{ $a->url() }}" target="_blank" rel="noopener">{{ $a->nombre_original }}</a>
            <span class="pry-tam">{{ $a->tamanoLegible() }}</span>
            @unless($lectura)
            <label class="pry-quitar"><input type="checkbox" name="archivos_quitar[]" value="{{ $a->id }}"
                style="width:auto"> Quitar</label>
            @endunless
          </li>
          @endforeach
        </ul>
        @endif
        @unless($lectura)
        <input id="archivos" name="archivos[]" type="file" multiple
          accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.zip,.mp4"
          style="padding:8px 0;background:none;border:0">
        <div class="ayuda">Podés elegir varios a la vez: documento escrito, presentación, planilla, fotos… Hasta 20 MB cada
          uno (el hosting puede limitar a menos).</div>
        @endunless
      </div>

      <div class="campo">
        <label style="display:flex;align-items:center;gap:8px;font-weight:400">
          <input type="checkbox" name="activo" value="1" style="width:auto" @checked(old('activo', $proyecto->activo ??
          true))>
          Visible en el sitio
        </label>
      </div>
    </fieldset>

    <div class="acciones" style="margin-top:6px">
      @unless($lectura)
      <button class="btn-p" type="submit">{{ $proyecto->exists ? 'Guardar cambios' : 'Cargar proyecto' }}</button>
      @endunless
      <a class="boton btn-s" href="/panel/proyectos">{{ $lectura ? 'Volver' : 'Cancelar' }}</a>
    </div>
  </form>
</div>

<style>
  .int-lista {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px
  }

  .int-lista:empty::before {
    content: "Todavía no agregaste integrantes.";
    font-size: .82rem;
    color: var(--gris)
  }

  .int-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .86rem;
    font-weight: 600;
    color: var(--tinta);
    background: var(--superficie2);
    border: 1px solid var(--linea);
    border-radius: 999px;
    padding: 4px 6px 4px 12px
  }

  .int-chip.reg {
    color: var(--azul);
    background: var(--azul-suave);
    border-color: transparent
  }

  .int-chip small {
    font-family: var(--mono);
    font-size: 9px;
    letter-spacing: .06em;
    text-transform: uppercase;
    opacity: .75
  }

  .int-chip button {
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
    font-size: 1rem;
    line-height: 1;
    padding: 0 4px
  }

  .int-agregar {
    display: flex;
    gap: 8px
  }

  .int-grupo {
    margin-top: 8px;
    background: transparent;
    border: 0;
    padding: 0;
    color: var(--azul);
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline
  }

  .pry-actual {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 8px
  }

  .pry-logo-grande {
    width: 72px;
    height: 72px;
    object-fit: contain;
    border-radius: 12px;
    border: 1px solid var(--linea);
    background: #fff
  }

  .pry-archivos {
    list-style: none;
    margin: 0 0 10px;
    padding: 0;
    border: 1px solid var(--linea);
    border-radius: var(--r-m)
  }

  .pry-archivos li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    font-size: .9rem
  }

  .pry-archivos li+li {
    border-top: 1px solid var(--linea)
  }

  .pry-archivos a {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap
  }

  .pry-tam {
    font-family: var(--mono);
    font-size: .75rem;
    color: var(--gris)
  }

  .pry-quitar {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 400;
    font-size: .82rem;
    margin: 0;
    color: var(--rojo)
  }
</style>
@endsection

@section('scripts')
<script>
  (function () {
    var USUARIOS = @json($usuarios);
    var INICIALES = @json(array_values((array) old('integrantes', $integrantes)));
    var soloLectura = {{ $lectura ? 'true' : 'false' }};
    var lista = document.getElementById('int-lista');
    var input = document.getElementById('integrante-nuevo');

    function usuarioPorId(id) { return USUARIOS.find(function (u) { return u.id === id; }); }

    // Convierte lo escrito en un integrante: si coincide con un usuario registrado lo vincula.
    function resolver(texto) {
      texto = texto.trim();
      if (!texto) return null;
      var u = USUARIOS.find(function (x) { return x.nombre + ' · ' + x.detalle === texto; });
      if (!u) {
        var mismos = USUARIOS.filter(function (x) { return x.nombre.toLowerCase() === texto.toLowerCase(); });
        if (mismos.length === 1) u = mismos[0];
      }
      return u ? { valor: 'u:' + u.id, nombre: u.nombre, reg: true } : { valor: texto, nombre: texto, reg: false };
    }

    function agregar(item) {
      if (!item) return;
      var ya = [].some.call(lista.querySelectorAll('input'), function (i) { return i.value === item.valor; });
      if (ya) return;
      var chip = document.createElement('span');
      chip.className = 'int-chip' + (item.reg ? ' reg' : '');
      var nom = document.createElement('span'); nom.textContent = item.nombre;
      chip.appendChild(nom);
      if (item.reg) { var s = document.createElement('small'); s.textContent = 'registrado'; chip.appendChild(s); }
      var oculto = document.createElement('input');
      oculto.type = 'hidden'; oculto.name = 'integrantes[]'; oculto.value = item.valor;
      chip.appendChild(oculto);
      if (!soloLectura) {
        var x = document.createElement('button');
        x.type = 'button'; x.textContent = '×'; x.title = 'Quitar'; x.setAttribute('aria-label', 'Quitar ' + item.nombre);
        x.addEventListener('click', function () { chip.remove(); });
        chip.appendChild(x);
      }
      lista.appendChild(chip);
    }

    INICIALES.forEach(function (v) {
      var m = /^u:(\d+)$/.exec(v);
      var u = m && usuarioPorId(parseInt(m[1], 10));
      agregar(u ? { valor: v, nombre: u.nombre, reg: true } : (m ? null : { valor: v, nombre: v, reg: false }));
    });

    if (soloLectura || !input) return;

    function desdeInput() { agregar(resolver(input.value)); input.value = ''; input.focus(); }
    document.getElementById('int-agregar').addEventListener('click', desdeInput);
    // Si quedó un nombre escrito sin apretar "Agregar", se suma al guardar.
    input.form.addEventListener('submit', function () { if (input.value.trim()) agregar(resolver(input.value)); });
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); desdeInput(); } });
    // Al elegir una opción de la lista, se agrega sola.
    input.addEventListener('input', function () {
      var v = input.value;
      if (USUARIOS.some(function (x) { return x.nombre + ' · ' + x.detalle === v; })) desdeInput();
    });

    document.getElementById('int-del-grupo').addEventListener('click', function () {
      var gid = parseInt(document.getElementById('grupo_id').value, 10);
      if (!gid) { alert('Primero elegí el grupo.'); return; }
      var alumnos = USUARIOS.filter(function (u) { return u.rol === 'alumno' && u.grupos.indexOf(gid) !== -1; });
      if (!alumnos.length) { alert('Ese grupo todavía no tiene alumnos registrados. Podés escribir los nombres.'); return; }
      alumnos.forEach(function (u) { agregar({ valor: 'u:' + u.id, nombre: u.nombre, reg: true }); });
    });
  })();
</script>
@endsection
