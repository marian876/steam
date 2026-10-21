<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('titulo', 'Panel') · Proyectos STEAM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&family=Public+Sans:ital,wght@0,400;0,500;0,700;1,400&family=IBM+Plex+Mono:wght@400;600&display=swap">
  {{-- Evita el parpadeo: aplica el estado colapsado antes de pintar. --}}
  <script>
    try { if (localStorage.getItem('panel-compacto') === '1') document.documentElement.classList.add('pnl-compacto'); } catch (e) { }
  </script>
  <style>
    :root {
      --azul: #00306B;
      --azul-suave: #E9F1FB;
      --turquesa: #0B7D7B;
      --ambar: #B96C12;
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
      --lateral: 236px;
      --lateral-min: 66px;
      --toph: 60px;
    }

    @media (prefers-color-scheme:dark) {
      :root {
        --azul: #86BAF9;
        --azul-suave: #16273B;
        --turquesa: #4CCFCB;
        --ambar: #EFAD63;
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

    /* ---------- Barra superior ---------- */
    .pnl-top {
      position: sticky;
      top: 0;
      z-index: 50;
      height: var(--toph);
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 0 18px;
      background: var(--superficie);
      border-bottom: 1px solid var(--linea);
    }

    .pnl-burger {
      background: transparent;
      border: 1px solid var(--linea);
      border-radius: var(--r-s);
      width: 38px;
      height: 38px;
      display: grid;
      place-items: center;
      color: var(--tinta2);
      cursor: pointer;
      flex: none;
      padding: 0;
    }

    .pnl-burger:hover {
      background: var(--superficie2);
      color: var(--tinta)
    }

    .pnl-marca {
      display: flex;
      align-items: center;
      gap: 10px;
      white-space: nowrap;
    }

    .pnl-marca a {
      display: flex;
      align-items: center;
      text-decoration: none;
      color: var(--tinta);
    }

    .pnl-marca-texto {
      font-family: var(--display);
      font-weight: 800;
      font-size: 1.12rem;
    }

    .pnl-marca img {
      height: 34px;
      width: auto;
      display: block;
      transition: transform .15s ease, opacity .15s ease;
    }

    .pnl-marca-logo:hover img {
      transform: scale(1.06);
      opacity: .9;
    }

    .pnl-top-fin {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 8px
    }

    /* menú de usuario */
    .pnl-usuario {
      position: relative
    }

    .pnl-usuario-btn {
      display: flex;
      align-items: center;
      gap: 9px;
      background: transparent;
      border: 1px solid transparent;
      border-radius: 999px;
      padding: 5px 10px 5px 5px;
      cursor: pointer;
      color: var(--tinta);
    }

    .pnl-usuario-btn:hover {
      background: var(--superficie2);
      border-color: var(--linea)
    }

    .pnl-avatar {
      width: 34px;
      height: 34px;
      border-radius: 999px;
      background: var(--azul);
      color: #fff;
      display: grid;
      place-items: center;
      font-weight: 700;
      font-size: .8rem;
      flex: none;
    }

    @media (prefers-color-scheme:dark) {
      .pnl-avatar {
        color: #0E1018
      }
    }

    .pnl-usuario-nombre {
      font-weight: 600;
      font-size: .9rem;
      white-space: nowrap
    }

    .pnl-usuario-btn .caret {
      color: var(--gris)
    }

    .pnl-menu {
      position: absolute;
      right: 0;
      top: calc(100% + 8px);
      min-width: 210px;
      background: var(--superficie);
      border: 1px solid var(--linea);
      border-radius: var(--r-m);
      box-shadow: var(--sombra);
      padding: 6px;
      display: none;
    }

    .pnl-menu.abierto {
      display: block
    }

    .pnl-menu-cab {
      padding: 8px 10px;
      border-bottom: 1px solid var(--linea);
      margin-bottom: 4px
    }

    .pnl-menu-cab .n {
      font-weight: 700;
      font-size: .9rem
    }

    .pnl-menu-cab .r {
      font-family: var(--mono);
      font-size: 10px;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: var(--gris)
    }

    .pnl-menu a,
    .pnl-menu button {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      text-align: left;
      background: transparent;
      border: 0;
      border-radius: var(--r-s);
      padding: 9px 10px;
      font-family: var(--texto);
      font-size: .9rem;
      font-weight: 500;
      color: var(--tinta);
      text-decoration: none;
      cursor: pointer;
    }

    .pnl-menu a:hover,
    .pnl-menu button:hover {
      background: var(--superficie2)
    }

    .pnl-menu .salir {
      color: var(--rojo)
    }

    .pnl-menu svg {
      width: 17px;
      height: 17px;
      flex: none
    }

    /* ---------- Cuerpo: lateral + main ---------- */
    .pnl-cuerpo {
      display: flex;
      align-items: flex-start;
      min-height: calc(100vh - var(--toph));
    }

    .pnl-lateral {
      position: sticky;
      top: var(--toph);
      align-self: flex-start;
      flex: none;
      width: var(--lateral);
      height: calc(100vh - var(--toph));
      overflow-y: auto;
      background: var(--superficie);
      border-right: 1px solid var(--linea);
      padding: 14px 12px;
      transition: width .18s ease;
    }

    .pnl-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
      border-radius: var(--r-m);
      color: var(--tinta2);
      text-decoration: none;
      font-weight: 600;
      font-size: .95rem;
      white-space: nowrap;
    }

    .pnl-item:hover {
      background: var(--superficie2);
      color: var(--tinta)
    }

    .pnl-item.activo {
      background: var(--azul-suave);
      color: var(--azul)
    }

    .pnl-item svg {
      width: 20px;
      height: 20px;
      flex: none
    }

    .pnl-item .cuenta {
      margin-left: auto;
      font-family: var(--mono);
      font-size: 10px;
      font-weight: 600;
      color: var(--gris);
      background: color-mix(in srgb, var(--gris) 14%, transparent);
      border-radius: 99px;
      padding: 2px 8px;
    }

    .pnl-item.activo .cuenta {
      color: var(--azul);
      background: color-mix(in srgb, var(--azul) 15%, transparent)
    }

    .pnl-grupo-tit {
      font-family: var(--mono);
      font-size: 10px;
      letter-spacing: .09em;
      text-transform: uppercase;
      color: var(--gris);
      font-weight: 600;
      padding: 14px 12px 5px;
    }

    .pnl-sep {
      height: 1px;
      background: var(--linea);
      margin: 10px 6px
    }

    .pnl-main {
      flex: 1;
      min-width: 0;
      max-width: 1060px;
      margin: 0 auto;
      padding: 26px 26px 70px;
      width: 100%;
    }

    /* ----- Colapsado (riel de iconos) en escritorio ----- */
    .pnl-compacto .pnl-lateral {
      width: var(--lateral-min);
      padding: 14px 8px;
    }

    .pnl-compacto .pnl-item span,
    .pnl-compacto .pnl-item .cuenta,
    .pnl-compacto .pnl-grupo-tit {
      display: none
    }

    .pnl-compacto .pnl-item {
      justify-content: center;
      padding: 10px
    }

    /* velo para el modo móvil */
    .pnl-velo {
      display: none;
      position: fixed;
      inset: var(--toph) 0 0;
      background: rgba(20, 23, 43, .45);
      z-index: 39;
    }

    @media (max-width: 860px) {
      .pnl-lateral {
        position: fixed;
        left: 0;
        top: var(--toph);
        z-index: 40;
        transform: translateX(-100%);
        box-shadow: var(--sombra);
        width: var(--lateral);
      }

      .pnl-nav-abierta .pnl-lateral {
        transform: none
      }

      .pnl-nav-abierta .pnl-velo {
        display: block
      }

      /* en móvil el colapsado no aplica: siempre ancho completo al abrir */
      .pnl-compacto .pnl-lateral {
        width: var(--lateral);
        padding: 14px 12px
      }

      .pnl-compacto .pnl-item span,
      .pnl-compacto .pnl-item .cuenta,
      .pnl-compacto .pnl-grupo-tit {
        display: revert
      }

      .pnl-compacto .pnl-item {
        justify-content: flex-start;
        padding: 10px 12px
      }

      .pnl-usuario-nombre {
        display: none
      }
    }

    /* ---------- Componentes reutilizados ---------- */
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
    input[type=search],
    input[type=url],
    select,
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
    select:focus,
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
    /* ---------- Tablas: búsqueda, filtros y orden por columna ---------- */
    .tbl-toolbar {
      display: flex;
      gap: 12px;
      margin-bottom: 14px;
      flex-wrap: wrap;
      align-items: center
    }

    .tbl-buscar {
      flex: 1;
      min-width: 220px;
      position: relative;
      margin: 0;
      font-weight: 400
    }

    .tbl-buscar svg {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 18px;
      height: 18px;
      color: var(--gris);
      pointer-events: none
    }

    .tbl-buscar input {
      padding-left: 38px
    }

    .tbl-toolbar select {
      width: auto;
      min-width: 160px
    }

    .tbl-cuenta {
      font-family: var(--mono);
      font-size: 11px;
      color: var(--gris);
      white-space: nowrap
    }

    .tbl-wrap {
      overflow-x: auto
    }

    .tbl-sort-btn {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: transparent;
      border: 0;
      padding: 0;
      font-family: var(--mono);
      font-size: 10px;
      letter-spacing: .09em;
      text-transform: uppercase;
      color: var(--gris);
      font-weight: 600;
      cursor: pointer;
      white-space: nowrap
    }

    .tbl-sort-btn:hover {
      color: var(--tinta)
    }

    .tbl-sort-btn svg {
      width: 12px;
      height: 16px;
      opacity: .4;
      transition: opacity .15s, transform .15s
    }

    .tbl-sort-btn.asc svg,
    .tbl-sort-btn.desc svg {
      opacity: 1;
      color: var(--azul)
    }

    .tbl-sort-btn.desc svg {
      transform: rotate(180deg)
    }

    tr.tbl-vacio td {
      text-align: center;
      color: var(--gris);
      padding: 26px 12px
    }

    .chip {
      display: inline-block;
      font-size: .74rem;
      font-weight: 600;
      color: var(--azul);
      background: var(--azul-suave);
      border-radius: 99px;
      padding: 2px 9px;
      margin: 2px 4px 2px 0;
      white-space: nowrap
    }

    /* subventanas de Administración */
    .sub-tabs {
      display: flex;
      gap: 4px;
      border-bottom: 1px solid var(--linea);
      margin: 0 0 22px
    }

    .sub-tabs a {
      padding: 9px 14px;
      font-weight: 600;
      font-size: .92rem;
      color: var(--tinta2);
      text-decoration: none;
      border-bottom: 2px solid transparent;
      margin-bottom: -1px;
      white-space: nowrap
    }

    .sub-tabs a:hover {
      color: var(--tinta)
    }

    .sub-tabs a.activo {
      color: var(--azul);
      border-bottom-color: var(--azul)
    }
    /* botones de ícono (editar, ver, eliminar) */
    .ico-acciones {
      display: flex;
      gap: 4px;
      justify-content: flex-end;
      align-items: center
    }

    .ico-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      border-radius: var(--r-s);
      background: transparent;
      border: 1px solid transparent;
      color: var(--tinta2);
      cursor: pointer;
      padding: 0;
      text-decoration: none
    }

    .ico-btn svg {
      width: 17px;
      height: 17px
    }

    .ico-btn:hover {
      background: var(--superficie2);
      border-color: var(--linea);
      color: var(--tinta)
    }

    .ico-btn.peligro:hover {
      background: var(--rojo-suave);
      border-color: var(--rojo);
      color: var(--rojo)
    }
    /* ===== Rol y estado como iniciales ===== */
    .rol-ini, .est-ini {
      display: inline-grid;
      place-items: center;
      min-width: 30px;
      height: 26px;
      padding: 0 7px;
      border-radius: 99px;
      font-family: var(--mono);
      font-size: .76rem;
      font-weight: 700;
      border: 1px solid transparent;
    }
    .rol-admin { color: var(--azul); background: var(--azul-suave); }
    .rol-docente { color: var(--turquesa); background: color-mix(in srgb, var(--turquesa) 14%, transparent); }
    .rol-alumno { color: var(--tinta2); background: color-mix(in srgb, var(--gris) 16%, transparent); }
    .est-a { color: var(--verde); background: color-mix(in srgb, var(--verde) 14%, transparent); }
    .est-d { color: var(--rojo); background: var(--rojo-suave); }
    button.est-ini { cursor: pointer; }
    button.est-ini:hover { border-color: currentColor; }

    .leyenda {
      font-size: .8rem;
      color: var(--gris);
      margin: -4px 0 14px
    }

    .leyenda .est-ini,
    .leyenda .rol-ini {
      min-width: 22px;
      height: 20px;
      font-size: .68rem
    }
  </style>
</head>

@php($u = auth()->user())
@php($iniciales = collect(explode(' ', $u->nombreCompleto()))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode(''))
@php($activo = trim($__env->yieldContent('panel_activo')))

<body>
  <header class="pnl-top">
    <button class="pnl-burger" id="pnl-burger" type="button" aria-label="Mostrar u ocultar el menú">
      <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
        <path d="M2 4.5h14M2 9h14M2 13.5h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
      </svg>
    </button>
    <div class="pnl-marca">
      <a class="pnl-marca-logo" href="/" title="Ir a la vista principal del sitio" aria-label="Ir a la vista principal del sitio">
        <img src="/logos/logo-colegio-sil.png" alt="Escudo del Colegio San Ignacio de Loyola">
      </a>
      <a class="pnl-marca-texto" href="/panel" title="Ir al panel docente">
        <span>Proyectos STEAM</span>
      </a>
    </div>

    <div class="pnl-top-fin">
      <div class="pnl-usuario" id="pnl-usuario">
        <button class="pnl-usuario-btn" id="pnl-usuario-btn" type="button" aria-haspopup="true"
          aria-expanded="false">
          <span class="pnl-avatar">{{ $iniciales ?: '·' }}</span>
          <span class="pnl-usuario-nombre">{{ $u->nombreCompleto() }}</span>
          <svg class="caret" width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>
        <div class="pnl-menu" id="pnl-menu" role="menu">
          <div class="pnl-menu-cab">
            <div class="n">{{ $u->nombreCompleto() }}</div>
            <div class="r">{{ $u->etiquetaRol() }}</div>
          </div>
          <a href="/panel/perfil" role="menuitem">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7" />
              <path d="M5 20c0-3.5 3.1-5.5 7-5.5s7 2 7 5.5" stroke="currentColor" stroke-width="1.7"
                stroke-linecap="round" />
            </svg>
            Mi perfil
          </a>
          @if($u->esAdmin())
          <a href="/panel/usuarios" role="menuitem">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7" />
              <path
                d="M19.4 13a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V19a2 2 0 0 1-4 0 1.6 1.6 0 0 0-2.7-1.1l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.6 1.6 0 0 0 4.6 13H4.5a2 2 0 0 1 0-4h.1a1.6 1.6 0 0 0 1.1-2.7l-.1-.1A2 2 0 1 1 8.4 3.4l.1.1a1.6 1.6 0 0 0 1.8.3H10.5a1.6 1.6 0 0 0 1-1.5V2a2 2 0 0 1 4 0v.1a1.6 1.6 0 0 0 2.7 1.1l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V8a1.6 1.6 0 0 0 1.5 1H23a2 2 0 0 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1z"
                stroke="currentColor" stroke-width="1.3" />
            </svg>
            Configuración
          </a>
          @endif
          <form method="POST" action="/salir">
            @csrf
            <button class="salir" type="submit" role="menuitem">
              <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M15 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h9" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" />
                <path d="M18 8l4 4-4 4M22 12H10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
              Cerrar sesión
            </button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <div class="pnl-cuerpo">
    <div class="pnl-velo" id="pnl-velo"></div>
    <aside class="pnl-lateral" id="pnl-lateral" aria-label="Menú del panel">
      <a class="pnl-item {{ $activo === 'inicio' ? 'activo' : '' }}" href="/panel#inicio" data-tab="inicio">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M3 10.5 12 3l9 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
            stroke-linejoin="round" />
          <path d="M5 9.5V20h14V9.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
        <span>Panel docente</span>
      </a>

      <div class="pnl-grupo-tit">Formato STEAM</div>
      <a class="pnl-item" href="/panel#prompts" data-tab="prompts">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <rect x="4" y="3" width="16" height="18" rx="2" stroke="currentColor" stroke-width="1.7" />
          <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
        </svg>
        <span>Prompts</span>
      </a>
      <a class="pnl-item" href="/panel#videos" data-tab="videos">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <rect x="3" y="6" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.7" />
          <path d="M15 10l6-3v10l-6-3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
        </svg>
        <span>Videos</span>
      </a>

      <div class="pnl-grupo-tit">Defensa oral</div>
      <a class="pnl-item" href="/panel#ejemplos" data-tab="ejemplos">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z" stroke="currentColor"
            stroke-width="1.7" stroke-linejoin="round" />
          <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
        </svg>
        <span>Ejemplos de contenido</span>
      </a>
      <a class="pnl-item" href="/panel#defensa-oral-prompts" data-tab="defensa-oral-prompts">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <rect x="4" y="3" width="16" height="18" rx="2" stroke="currentColor" stroke-width="1.7" />
          <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
        </svg>
        <span>Prompts</span>
      </a>

      <div class="pnl-grupo-tit">Sitio web de la empresa</div>
      <a class="pnl-item" href="/panel#sitio-web" data-tab="sitio-web">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <rect x="4" y="3" width="16" height="18" rx="2" stroke="currentColor" stroke-width="1.7" />
          <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
        </svg>
        <span>Prompts SilEmprende</span>
      </a>
      <a class="pnl-item" href="/panel#sitio-web-batan-prompts" data-tab="sitio-web-batan-prompts">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <rect x="4" y="3" width="16" height="18" rx="2" stroke="currentColor" stroke-width="1.7" />
          <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
        </svg>
        <span>Prompts BATAN</span>
      </a>

      <div class="pnl-grupo-tit">Proyectos anteriores</div>
      <a class="pnl-item {{ $activo === 'proyectos' ? 'activo' : '' }}" href="/panel/proyectos">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor"
            stroke-width="1.7" stroke-linejoin="round" />
          <path d="M12 11v5M9.5 13.5 12 16l2.5-2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
        <span>Proyectos</span>
      </a>


    </aside>

    <main class="pnl-main">
      @if(session('aviso'))
      <div class="aviso">{{ session('aviso') }}</div>
      @endif
      @yield('contenido')
    </main>
  </div>

  <script>
    (function () {
      var raiz = document.documentElement;
      var burger = document.getElementById('pnl-burger');
      var velo = document.getElementById('pnl-velo');
      var esMovil = function () { return window.matchMedia('(max-width: 860px)').matches; };

      burger.addEventListener('click', function () {
        if (esMovil()) {
          document.body.classList.toggle('pnl-nav-abierta');
        } else {
          var on = raiz.classList.toggle('pnl-compacto');
          try { localStorage.setItem('panel-compacto', on ? '1' : '0'); } catch (e) { }
        }
      });
      velo.addEventListener('click', function () { document.body.classList.remove('pnl-nav-abierta'); });

      // Menú de usuario
      var uBtn = document.getElementById('pnl-usuario-btn');
      var uMenu = document.getElementById('pnl-menu');
      function cerrarMenu() { uMenu.classList.remove('abierto'); uBtn.setAttribute('aria-expanded', 'false'); }
      uBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var ab = uMenu.classList.toggle('abierto');
        uBtn.setAttribute('aria-expanded', ab ? 'true' : 'false');
      });
      document.addEventListener('click', function (e) {
        if (!uMenu.contains(e.target) && e.target !== uBtn) cerrarMenu();
      });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape') cerrarMenu(); });
    })();
  </script>
  <script>
    /* Tablas ordenables con búsqueda y filtros.
       <div class="tbl-toolbar" data-tabla="ID"> con un input[type=search] y
       select[data-filtro="clave"]; la tabla lleva class="ordenable" y
       th[data-orden="clave"]. Cada fila puede traer data-clave (valor para
       ordenar/filtrar; varios valores separados por |) y data-buscar. */
    (function () {
      var ICONO = '<svg viewBox="0 0 12 16" fill="none" aria-hidden="true"><path d="M6 2v12M3 5l3-3 3 3M3 11l3 3 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      var params = new URLSearchParams(location.search);

      document.querySelectorAll('table.ordenable').forEach(function (tabla) {
        var tbody = tabla.tBodies[0];
        if (!tbody) return;
        var filas = [].slice.call(tbody.rows);
        var ths = [].slice.call(tabla.tHead.rows[0].cells);
        var barra = document.querySelector('.tbl-toolbar[data-tabla="' + tabla.id + '"]');
        var buscar = barra && barra.querySelector('input[type=search]');
        var filtros = barra ? [].slice.call(barra.querySelectorAll('select[data-filtro]')) : [];
        var cuenta = barra && barra.querySelector('.tbl-cuenta');

        var vacio = document.createElement('tr');
        vacio.className = 'tbl-vacio';
        vacio.innerHTML = '<td colspan="' + ths.length + '">No hay resultados con esa búsqueda.</td>';
        vacio.hidden = true;
        tbody.appendChild(vacio);

        function valor(fila, clave, idx) {
          var v = fila.getAttribute('data-' + clave);
          if (v === null) v = (fila.cells[idx] ? fila.cells[idx].textContent : '');
          return v.trim().toLowerCase();
        }
        function textoFila(fila) {
          var b = fila.getAttribute('data-buscar');
          if (b !== null) return b.toLowerCase();
          return [].map.call(fila.cells, function (c) {
            return c.querySelector('form, select, button') ? '' : c.textContent;
          }).join(' ').toLowerCase();
        }

        function aplicar() {
          var q = buscar ? buscar.value.trim().toLowerCase() : '';
          var n = 0;
          filas.forEach(function (f) {
            var ok = !q || textoFila(f).indexOf(q) !== -1;
            filtros.forEach(function (sel) {
              if (!ok || !sel.value) return;
              var v = (f.getAttribute('data-' + sel.dataset.filtro) || '').split('|');
              ok = v.indexOf(sel.value) !== -1;
            });
            f.hidden = !ok;
            if (ok) n++;
          });
          vacio.hidden = n > 0 || filas.length === 0;
          if (cuenta) cuenta.textContent = n + ' de ' + filas.length;
        }

        ths.forEach(function (th, idx) {
          var clave = th.getAttribute('data-orden');
          if (!clave) return;
          var btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'tbl-sort-btn';
          btn.innerHTML = th.innerHTML + ICONO;
          th.innerHTML = '';
          th.appendChild(btn);
          btn.addEventListener('click', function () {
            var asc = !btn.classList.contains('asc');
            tabla.querySelectorAll('.tbl-sort-btn').forEach(function (b) { b.classList.remove('asc', 'desc'); });
            btn.classList.add(asc ? 'asc' : 'desc');
            filas.sort(function (a, b) {
              var va = valor(a, clave, idx), vb = valor(b, clave, idx);
              var na = parseFloat(va), nb = parseFloat(vb);
              var cmp = (!isNaN(na) && !isNaN(nb) && String(na) === va && String(nb) === vb)
                ? na - nb : va.localeCompare(vb, 'es', { numeric: true });
              return asc ? cmp : -cmp;
            });
            filas.forEach(function (f) { tbody.appendChild(f); });
            tbody.appendChild(vacio);
          });
        });

        filtros.forEach(function (sel) {
          var inicial = params.get(sel.dataset.filtro);
          if (inicial !== null) sel.value = inicial;
          sel.addEventListener('change', aplicar);
        });
        if (buscar) buscar.addEventListener('input', aplicar);
        aplicar();
      });
    })();
  </script>
  @yield('scripts')
</body>

</html>
