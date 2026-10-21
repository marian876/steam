<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('titulo', 'Formato Steam') · SIL</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&family=Public+Sans:ital,wght@0,400;0,500;0,700;1,400&family=IBM+Plex+Mono:wght@400;600&display=swap">
  <style>
    :root {
      --azul: #00306B;
      --azul-suave: #E9F1FB;
      --turquesa: #0B7D7B;
      --ambar: #B96C12;
      --ambar-suave: #FBEEDA;
      --rojo: #C0392A;
      --rojo-suave: #FBE5E1;
      --verde: #1B8049;
      --tinta: #14172B;
      --tinta2: #474D69;
      --gris: #787E9B;
      --linea: #DBDEEC;
      --linea-fuerte: #C0C5DC;
      --fondo: #F0F1F8;
      --superficie: #FFFFFF;
      --superficie2: #F7F8FD;
      --sombra: 0 1px 2px rgba(20, 23, 43, .05), 0 10px 28px -16px rgba(20, 23, 43, .30);
      --display: "Bricolage Grotesque", "Trebuchet MS", system-ui, sans-serif;
      --texto: "Public Sans", "Segoe UI", system-ui, -apple-system, sans-serif;
      --mono: "IBM Plex Mono", ui-monospace, "SFMono-Regular", Consolas, monospace;
      --r-s: 6px;
      --r-m: 12px;
      --r-l: 20px;
    }

    @media (prefers-color-scheme:dark) {
      :root {
        --azul: #86BAF9;
        --azul-suave: #16273B;
        --turquesa: #4CCFCB;
        --ambar: #EFAD63;
        --ambar-suave: #33230E;
        --rojo: #FF8E7A;
        --rojo-suave: #331612;
        --verde: #5BD48D;
        --tinta: #E9EBF6;
        --tinta2: #AFB5D0;
        --gris: #858BA9;
        --linea: #2A2F48;
        --linea-fuerte: #3B4265;
        --fondo: #0E1018;
        --superficie: #161926;
        --superficie2: #1C2030;
        --sombra: 0 1px 2px rgba(0, 0, 0, .4), 0 10px 28px -16px rgba(0, 0, 0, .8);
      }
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      background: var(--fondo);
      color: var(--tinta);
      font-family: var(--texto);
      font-size: 15px;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased
    }

    a {
      color: var(--azul)
    }

    .topbar {
      background: var(--superficie);
      border-bottom: 1px solid var(--linea)
    }

    .tb {
      max-width: 1060px;
      margin: 0 auto;
      padding: 11px 22px;
      display: flex;
      align-items: center;
      gap: 16px
    }

    .tb-marca {
      font-family: var(--display);
      font-weight: 700;
      font-size: .95rem;
      color: var(--tinta);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 9px;
      white-space: nowrap
    }

    .tb-sep {
      flex: 1
    }

    .tb-usuario {
      font-size: .82rem;
      color: var(--tinta2);
      display: flex;
      align-items: center;
      gap: 10px
    }

    .envoltorio {
      max-width: 1060px;
      margin: 0 auto;
      padding: 30px 22px 70px
    }

    .estrecho {
      max-width: 430px
    }

    h1 {
      font-family: var(--display);
      font-size: 1.6rem;
      margin: 0 0 6px
    }

    .bajada {
      color: var(--tinta2);
      margin: 0 0 24px
    }

    .tarjeta {
      background: var(--superficie);
      border: 1px solid var(--linea);
      border-radius: var(--r-l);
      box-shadow: var(--sombra);
      padding: 26px
    }

    label {
      display: block;
      font-weight: 600;
      font-size: .85rem;
      margin: 0 0 5px
    }

    .campo {
      margin-bottom: 16px
    }

    input[type=text],
    input[type=email],
    input[type=password],
    input[type=number],
    textarea {
      width: 100%;
      font-family: var(--texto);
      font-size: .92rem;
      color: var(--tinta);
      background: var(--superficie2);
      border: 1px solid var(--linea-fuerte);
      border-radius: var(--r-s);
      padding: 9px 12px
    }

    textarea {
      font-family: var(--mono);
      font-size: .8rem;
      line-height: 1.6;
      resize: vertical
    }

    input:focus,
    textarea:focus {
      outline: 2px solid var(--azul);
      outline-offset: 1px
    }

    .ayuda {
      font-size: .78rem;
      color: var(--gris);
      margin-top: 5px
    }

    button,
    .boton {
      font-family: var(--texto);
      cursor: pointer;
      border-radius: var(--r-s);
      font-size: .85rem;
      font-weight: 600;
      padding: 9px 15px;
      border: 1px solid transparent;
      transition: .18s;
      text-decoration: none;
      display: inline-block
    }

    .btn-p {
      background: var(--azul);
      color: #fff
    }

    .btn-p:hover {
      filter: brightness(1.15)
    }

    .btn-s {
      background: var(--superficie);
      color: var(--tinta);
      border-color: var(--linea-fuerte)
    }

    .btn-s:hover {
      border-color: var(--tinta2)
    }

    .btn-rojo {
      background: var(--rojo-suave);
      color: var(--rojo);
      border-color: var(--rojo)
    }

    @media (prefers-color-scheme:dark) {
      .btn-p {
        color: #0E1018
      }
    }

    .errores {
      background: var(--rojo-suave);
      border-left: 3px solid var(--rojo);
      border-radius: var(--r-s);
      padding: 11px 14px;
      margin-bottom: 18px;
      font-size: .86rem
    }

    .errores ul {
      margin: 0;
      padding-left: 18px
    }

    .aviso {
      background: var(--azul-suave);
      border-left: 3px solid var(--azul);
      border-radius: var(--r-s);
      padding: 11px 14px;
      margin-bottom: 18px;
      font-size: .88rem
    }

    .pie-form {
      margin-top: 20px;
      font-size: .85rem;
      color: var(--tinta2)
    }

    table.lista {
      width: 100%;
      border-collapse: collapse;
      font-size: .88rem
    }

    table.lista th {
      text-align: left;
      font-family: var(--mono);
      font-size: 10px;
      letter-spacing: .09em;
      text-transform: uppercase;
      color: var(--gris);
      padding: 0 12px 8px;
      font-weight: 600
    }

    table.lista td {
      padding: 12px;
      border-top: 1px solid var(--linea);
      vertical-align: top
    }

    .etq {
      font-family: var(--mono);
      font-size: 9.5px;
      font-weight: 600;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: 3px 8px;
      border-radius: 99px;
      white-space: nowrap
    }

    .etq.si {
      color: var(--verde);
      background: color-mix(in srgb, var(--verde) 14%, transparent)
    }

    .etq.no {
      color: var(--gris);
      background: color-mix(in srgb, var(--gris) 14%, transparent)
    }

    .acciones {
      display: flex;
      gap: 8px;
      flex-wrap: wrap
    }

    .enc-panel {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 20px
    }

    .titulo-seccion {
      font-family: var(--display);
      font-size: 1.15rem;
      margin: 0 0 4px
    }
  </style>
</head>

<body>
  <div class="topbar">
    <div class="tb">
      <a class="tb-marca" href="/formato">Formato Steam</a>
      <div class="tb-sep"></div>
      @auth
      <div class="tb-usuario">
        <span>{{ auth()->user()->name }}</span>
        @if(auth()->user()->esDocente())
        <a class="boton btn-s" href="/panel">Panel</a>
        @endif
        <form method="POST" action="/salir" style="margin:0">@csrf<button class="btn-s" type="submit">Salir</button>
        </form>
      </div>
      @else
      <div class="tb-usuario"><a class="boton btn-s" href="/ingresar">Ingresar</a></div>
      @endauth
    </div>
  </div>

  <div class="envoltorio @yield('ancho')">
    @yield('contenido')
  </div>
</body>

</html>
