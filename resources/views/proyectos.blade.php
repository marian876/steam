<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proyectos {{ $titulo }} · SIL</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&family=Public+Sans:ital,wght@0,400;0,500;0,700;1,400&family=IBM+Plex+Mono:wght@400;600&display=swap">
  <style>
    :root {
      --azul: #00306B;
      --azul-vivo: #1668E0;
      --azul-suave: #E9F1FB;
      --turquesa: #0B7D7B;
      --turquesa-suave: #DCF0EF;
      --tinta: #14172B;
      --tinta2: #474D69;
      --gris: #787E9B;
      --linea: #DBDEEC;
      --linea-fuerte: #C0C5DC;
      --fondo: #F2F6FD;
      --superficie: #FFFFFF;
      --sombra: 0 1px 2px rgba(20, 23, 43, .05), 0 10px 28px -16px rgba(20, 23, 43, .30);
      --sombra-alta: 0 2px 4px rgba(20, 23, 43, .06), 0 22px 48px -24px rgba(20, 23, 43, .42);
      --display: "Bricolage Grotesque", system-ui, sans-serif;
      --texto: "Public Sans", system-ui, sans-serif;
      --mono: "IBM Plex Mono", ui-monospace, monospace;
      --r: 14px;
      --r-l: 22px;
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: var(--texto);
      color: var(--tinta);
      background: var(--fondo);
      position: relative;
      overflow-x: hidden;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      background: url("/logos/fondo.jpg") center center / cover no-repeat fixed;
    }

    body.modal-abierto {
      overflow: hidden
    }

    .envoltorio {
      position: relative;
      z-index: 1;
      max-width: 1120px;
      margin: 0 auto;
      padding: clamp(22px, 4vw, 44px) clamp(20px, 4vw, 40px) clamp(30px, 5vw, 54px);
    }

    .barra-sup {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: clamp(26px, 4vw, 40px);
    }

    .marca {
      display: inline-flex;
      align-items: center;
      gap: 13px;
      text-decoration: none;
    }

    .marca img {
      height: 58px;
      width: auto;
      flex: none
    }

    .marca .nombre {
      font-family: var(--display);
      font-weight: 800;
      color: var(--azul);
      line-height: 1.08;
      font-size: .95rem;
      letter-spacing: .01em;
      text-transform: uppercase;
    }

    .marca .nombre small {
      display: block;
      font-weight: 600;
      font-size: .72rem;
      letter-spacing: .22em;
      color: var(--azul);
      opacity: .85;
      margin-top: 3px;
    }

    .volver {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-size: .92rem;
      font-weight: 700;
      text-decoration: none;
      color: var(--azul);
      background: var(--superficie);
      border: 1px solid var(--linea-fuerte);
      border-radius: 999px;
      padding: 9px 18px;
    }

    .volver:hover {
      background: var(--azul-suave);
      border-color: var(--azul)
    }

    .eyebrow {
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: var(--mono);
      font-size: .78rem;
      font-weight: 600;
      letter-spacing: .26em;
      text-transform: uppercase;
      color: var(--azul);
      margin-bottom: 12px;
    }

    .eyebrow::before {
      content: "";
      width: 34px;
      height: 3px;
      border-radius: 2px;
      background: var(--azul-vivo);
      flex: none;
    }

    h1 {
      font-family: var(--display);
      font-weight: 800;
      line-height: 1;
      margin: 0 0 8px;
      font-size: clamp(2.2rem, 6vw, 3.6rem);
      letter-spacing: -.03em;
    }

    .sub {
      color: var(--tinta2);
      margin: 0 0 30px;
      font-size: 1.02rem
    }

    /* ---------- grilla de proyectos ---------- */
    .proyectos {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
    }

    .proyecto {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 6px;
      background: var(--superficie);
      border: 1px solid var(--linea);
      border-radius: var(--r-l);
      padding: 24px 20px 20px;
      box-shadow: var(--sombra);
      cursor: pointer;
      font: inherit;
      color: inherit;
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .proyecto:hover {
      transform: translateY(-4px);
      box-shadow: var(--sombra-alta);
      border-color: var(--linea-fuerte)
    }

    .proyecto:focus-visible {
      outline: 3px solid var(--azul-vivo);
      outline-offset: 3px
    }

    .logo {
      width: 110px;
      height: 110px;
      object-fit: contain;
      border-radius: 18px;
      background: #fff;
      margin-bottom: 8px;
    }

    .logo-ini {
      display: grid;
      place-items: center;
      font-family: var(--display);
      font-weight: 800;
      font-size: 2rem;
      color: var(--azul);
      background: var(--azul-suave);
    }

    .proyecto h2 {
      font-family: var(--display);
      font-weight: 700;
      font-size: 1.2rem;
      line-height: 1.15;
      margin: 0;
    }

    .datos {
      font-family: var(--mono);
      font-size: .78rem;
      color: var(--gris);
      letter-spacing: .04em
    }

    .ver {
      margin-top: 8px;
      font-weight: 700;
      font-size: .88rem;
      color: var(--azul-vivo)
    }

    .vacio {
      background: var(--superficie);
      border: 1px solid var(--linea);
      border-radius: var(--r-l);
      box-shadow: var(--sombra);
      padding: 22px 24px;
      color: var(--tinta2);
      margin: 0
    }


    /* ---------- ventana emergente ---------- */
    .modal-velo {
      position: fixed;
      inset: 0;
      z-index: 50;
      background: rgba(20, 23, 43, .55);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px;
      opacity: 0;
      pointer-events: none;
      transition: opacity .2s ease;
    }

    .modal-velo.abierto {
      opacity: 1;
      pointer-events: auto
    }

    .modal {
      position: relative;
      background: var(--superficie);
      border-radius: var(--r-l);
      box-shadow: var(--sombra-alta);
      width: 100%;
      max-width: 560px;
      max-height: 88vh;
      overflow-y: auto;
      padding: 28px;
      transform: translateY(12px);
      transition: transform .2s ease;
    }

    .modal-velo.abierto .modal {
      transform: none
    }

    .cerrar {
      position: absolute;
      top: 14px;
      right: 14px;
      width: 36px;
      height: 36px;
      border-radius: 999px;
      border: 1px solid var(--linea);
      background: var(--superficie);
      color: var(--tinta2);
      font-size: 1.3rem;
      line-height: 1;
      cursor: pointer;
    }

    .cerrar:hover {
      color: var(--tinta);
      border-color: var(--linea-fuerte)
    }

    .modal-enc {
      display: flex;
      align-items: center;
      gap: 16px;
      margin: 0 40px 16px 0
    }

    .modal-enc .logo {
      width: 76px;
      height: 76px;
      margin: 0;
      border: 1px solid var(--linea);
      flex: none
    }

    .modal-enc .logo-ini {
      font-size: 1.5rem
    }

    .modal-enc h3 {
      font-family: var(--display);
      font-size: 1.45rem;
      margin: 0 0 4px;
      line-height: 1.1
    }

    .modal p.desc {
      color: var(--tinta2);
      line-height: 1.55;
      margin: 0 0 18px
    }

    .modal h4 {
      font-family: var(--mono);
      font-size: .72rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--gris);
      margin: 0 0 8px
    }

    .archivos {
      list-style: none;
      margin: 0 0 20px;
      padding: 0;
      border: 1px solid var(--linea);
      border-radius: var(--r)
    }

    .archivos li+li {
      border-top: 1px solid var(--linea)
    }

    .archivos a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 11px 14px;
      text-decoration: none;
      color: var(--tinta);
      font-size: .93rem
    }

    .archivos a:hover {
      background: var(--fondo)
    }

    .archivos .tipo {
      font-family: var(--mono);
      font-size: .66rem;
      font-weight: 600;
      color: var(--turquesa);
      background: var(--turquesa-suave);
      border-radius: 6px;
      padding: 3px 6px;
      flex: none
    }

    .archivos .nom {
      flex: 1;
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap
    }

    .archivos .tam {
      font-family: var(--mono);
      font-size: .74rem;
      color: var(--gris)
    }

    .sin-archivos {
      color: var(--gris);
      font-size: .9rem;
      margin: 0 0 20px
    }

    .btn-web {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-weight: 700;
      text-decoration: none;
      color: #fff;
      background: var(--azul-vivo);
      border-radius: 999px;
      padding: 11px 20px;
    }

    .btn-web:hover {
      filter: brightness(1.1)
    }

    .proyecto {
      position: relative
    }

    .ciencap {
      display: inline-block;
      font-family: var(--mono);
      font-size: .7rem;
      font-weight: 600;
      letter-spacing: .04em;
      color: #8A5A00;
      background: #FFF1D6;
      border: 1px solid #F5D08A;
      border-radius: 999px;
      padding: 3px 10px;
      margin-top: 6px
    }

    .ciencap-esquina {
      position: absolute;
      top: 12px;
      right: 12px;
      margin: 0
    }

    .logros {
      list-style: none;
      margin: 0 0 18px;
      padding: 0
    }

    .logros li {
      font-size: .93rem;
      color: var(--tinta);
      padding: 4px 0
    }

    .integrantes {
      list-style: none;
      margin: 0 0 18px;
      padding: 0;
      display: flex;
      flex-wrap: wrap;
      gap: 8px
    }

    .integrantes li {
      font-size: .92rem;
      font-weight: 600;
      color: var(--azul);
      background: var(--azul-suave);
      border-radius: 999px;
      padding: 5px 12px
    }

    .botones {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin: 4px 0 18px
    }

    .btn-doc {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-weight: 700;
      text-decoration: none;
      color: var(--turquesa);
      background: var(--turquesa-suave);
      border: 1px solid color-mix(in srgb, var(--turquesa) 35%, transparent);
      border-radius: 999px;
      padding: 11px 20px;
    }

    .btn-doc:hover {
      border-color: var(--turquesa)
    }
  </style>
</head>

<body>
  <div class="envoltorio">
    <div class="barra-sup">
      <a class="marca" href="/">
        <img src="/logos/logo_reducido.png" alt="Escudo del Colegio San Ignacio de Loyola">
        <div class="nombre">San Ignacio<br>de Loyola<small>School</small></div>
      </a>
      <a class="volver" href="/">← Volver al inicio</a>
    </div>

    <div class="eyebrow">Proyectos Presentados</div>
    <h1>{{ $titulo }}</h1>
    <p class="sub">El trabajo de cada grupo. Tocá una empresa para ver sus archivos y su sitio web.</p>

    @if($proyectos->isEmpty())
    <p class="vacio">Todavía no hay proyectos cargados para {{ $titulo }}. Volvé pronto.</p>
    @endif

    @php($hayTablaIntegrantes = \Illuminate\Support\Facades\Schema::hasTable('proyecto_integrantes'))
    <main class="proyectos">
      @foreach($proyectos as $p)
      <button class="proyecto" type="button" data-proyecto="{{ $p->id }}">
        @if($p->ciencap)
        <span class="ciencap ciencap-esquina" title="Participó en la Ciencap">★ Ciencap</span>
        @endif
        @if($p->logoUrl())
        <img class="logo" src="{{ $p->logoUrl() }}" alt="Logo de {{ $p->empresa }}" loading="lazy">
        @else
        <span class="logo logo-ini" aria-hidden="true">{{ $p->iniciales() }}</span>
        @endif
        <h2>{{ $p->empresa }}</h2>
        <span class="datos">{{ $p->grupo->curso }}{{ $p->grupo->seccion }} · Grupo {{ $p->grupo->numero }}</span>
        <span class="ver">Ver proyecto →</span>
      </button>

      {{-- Contenido de la ventana emergente de este proyecto --}}
      <template id="datos-{{ $p->id }}">
        <div class="modal-enc">
          @if($p->logoUrl())
          <img class="logo" src="{{ $p->logoUrl() }}" alt="Logo de {{ $p->empresa }}">
          @else
          <span class="logo logo-ini" aria-hidden="true">{{ $p->iniciales() }}</span>
          @endif
          <div>
            <h3 id="modal-titulo">{{ $p->empresa }}</h3>
            <span class="datos">{{ $p->grupo->nombre() }}</span>
            @if($p->ciencap)
            <div><span class="ciencap" title="Participó en la Ciencap">★ Ciencap</span></div>
            @endif
          </div>
        </div>
        @if($p->descripcion)
        <p class="desc">{{ $p->descripcion }}</p>
        @endif

        @php($logros = collect(preg_split('/\r\n|\r|\n/', (string) $p->logros))->map(fn ($l) => trim($l))->filter())
        @if($logros->isNotEmpty())
        <h4>Logros obtenidos</h4>
        <ul class="logros">
          @foreach($logros as $l)
          <li>🏅 {{ $l }}</li>
          @endforeach
        </ul>
        @endif

        @php($integrantesP = $hayTablaIntegrantes ? $p->integrantes : collect())
        @if($integrantesP->isNotEmpty())
        <h4>Integrantes</h4>
        <ul class="integrantes">
          @foreach($integrantesP as $i)
          <li>{{ $i->nombreMostrar() }}</li>
          @endforeach
        </ul>
        @endif

        @php($doc = $p->archivos->first(fn ($a) => strtolower(pathinfo($a->nombre_original, PATHINFO_EXTENSION)) === 'pdf') ?? $p->archivos->first())
        @php($otros = $p->archivos->reject(fn ($a) => $doc && $a->id === $doc->id))
        @if($doc || $p->sitio_web)
        <div class="botones">
          @if($doc)
          <a class="btn-doc" href="{{ $doc->url() }}" target="_blank" rel="noopener">📄 Documentación</a>
          @endif
          @if($p->sitio_web)
          <a class="btn-web" href="{{ $p->sitio_web }}" target="_blank" rel="noopener noreferrer">Sitio web ↗</a>
          @endif
        </div>
        @else
        <p class="sin-archivos">Este proyecto todavía no tiene documentación ni sitio web cargados.</p>
        @endif

        @if($otros->isNotEmpty())
        <h4>Otros archivos</h4>
        <ul class="archivos">
          @foreach($otros as $a)
          <li>
            <a href="{{ $a->url() }}" target="_blank" rel="noopener">
              <span class="tipo">{{ $a->tipo() }}</span>
              <span class="nom">{{ $a->nombre_original }}</span>
              <span class="tam">{{ $a->tamanoLegible() }}</span>
            </a>
          </li>
          @endforeach
        </ul>
        @endif
      </template>
      @endforeach
    </main>

  </div>

  <div class="modal-velo" id="modal" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
      <button class="cerrar" type="button" id="modal-cerrar" aria-label="Cerrar">×</button>
      <div id="modal-cuerpo"></div>
    </div>
  </div>

  <script>
    (function () {
      var velo = document.getElementById('modal');
      var cuerpo = document.getElementById('modal-cuerpo');
      var cerrarBtn = document.getElementById('modal-cerrar');
      var ultimo = null;

      function abrir(id, origen) {
        var tpl = document.getElementById('datos-' + id);
        if (!tpl) return;
        cuerpo.innerHTML = '';
        cuerpo.appendChild(tpl.content.cloneNode(true));
        ultimo = origen;
        velo.classList.add('abierto');
        velo.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-abierto');
        cerrarBtn.focus();
      }
      function cerrar() {
        velo.classList.remove('abierto');
        velo.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-abierto');
        if (ultimo) ultimo.focus();
      }

      document.querySelectorAll('[data-proyecto]').forEach(function (b) {
        b.addEventListener('click', function () { abrir(b.dataset.proyecto, b); });
      });
      cerrarBtn.addEventListener('click', cerrar);
      velo.addEventListener('click', function (e) { if (e.target === velo) cerrar(); });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && velo.classList.contains('abierto')) cerrar(); });
    })();
  </script>
</body>

</html>
