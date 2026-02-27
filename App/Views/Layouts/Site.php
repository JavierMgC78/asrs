!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CEA – Colegio de Educación Avanzada</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">

  <style>
    /* =========================================
       CSS CUSTOM PROPERTIES — DESIGN TOKENS
    ========================================= */
    :root {
      --cea-navy:       #0D1F35;
      --cea-navy-mid:   #1A3557;
      --cea-gold:       #B8944A;
      --cea-gold-light: #D4B06A;
      --cea-cream:      #F7F4EF;
      --cea-white:      #FFFFFF;
      --cea-gray-100:   #F0ECE6;
      --cea-gray-300:   #C8BFB0;
      --cea-gray-500:   #8A7E70;
      --cea-gray-700:   #4A4138;
      --cea-text:       #1E1811;

      --font-display: 'Cormorant Garamond', Georgia, serif;
      --font-body:    'DM Sans', sans-serif;

      --nav-height: 80px;
      --container:  1200px;
      --radius-sm:  4px;
      --radius-md:  8px;

      --shadow-subtle: 0 1px 3px rgba(13,31,53,.06), 0 4px 12px rgba(13,31,53,.04);
      --shadow-card:   0 2px 8px rgba(13,31,53,.08), 0 12px 32px rgba(13,31,53,.06);
      --shadow-nav:    0 1px 0 rgba(13,31,53,.08);

      --transition: .22s cubic-bezier(.4,0,.2,1);
    }

    /* =========================================
       RESET & BASE
    ========================================= */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html {
      font-size: 16px;
      scroll-behavior: smooth;
      -webkit-font-smoothing: antialiased;
    }

    body {
      font-family: var(--font-body);
      font-weight: 400;
      color: var(--cea-text);
      background: var(--cea-cream);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      line-height: 1.6;
    }

    img { max-width: 100%; display: block; }
    a { color: inherit; text-decoration: none; }

    /* =========================================
       UTILITY
    ========================================= */
    .container {
      width: 100%;
      max-width: var(--container);
      margin: 0 auto;
      padding: 0 clamp(20px, 4vw, 56px);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--font-body);
      font-size: .8125rem;
      font-weight: 500;
      letter-spacing: .08em;
      text-transform: uppercase;
      border-radius: var(--radius-sm);
      padding: 11px 24px;
      cursor: pointer;
      transition: var(--transition);
      border: 1.5px solid transparent;
      white-space: nowrap;
    }

    .btn-primary {
      background: var(--cea-gold);
      color: var(--cea-white);
      border-color: var(--cea-gold);
    }
    .btn-primary:hover {
      background: var(--cea-gold-light);
      border-color: var(--cea-gold-light);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(184,148,74,.28);
    }

    .btn-outline {
      background: transparent;
      color: var(--cea-navy);
      border-color: var(--cea-navy);
    }
    .btn-outline:hover {
      background: var(--cea-navy);
      color: var(--cea-white);
    }

    .btn-outline-white {
      background: transparent;
      color: var(--cea-white);
      border-color: rgba(255,255,255,.5);
    }
    .btn-outline-white:hover {
      background: rgba(255,255,255,.12);
      border-color: var(--cea-white);
    }

    /* =========================================
       HEADER / NAV
    ========================================= */
    #site-header {
      position: sticky;
      top: 0;
      z-index: 100;
      height: var(--nav-height);
      background: var(--cea-white);
      box-shadow: var(--shadow-nav);
      display: flex;
      align-items: center;
    }

    .nav-inner {
      display: flex;
      align-items: center;
      gap: 0;
      width: 100%;
    }

    /* Logo */
    .nav-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
      margin-right: auto;
    }

    .nav-logo-mark {
      width: 42px;
      height: 42px;
      background: var(--cea-navy);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .nav-logo-mark::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 3px;
      background: var(--cea-gold);
    }

    .nav-logo-mark span {
      font-family: var(--font-display);
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--cea-white);
      letter-spacing: .04em;
      position: relative;
      z-index: 1;
    }

    .nav-logo-text {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }

    .nav-logo-text strong {
      font-family: var(--font-display);
      font-size: 1.0625rem;
      font-weight: 600;
      color: var(--cea-navy);
      letter-spacing: .04em;
    }

    .nav-logo-text small {
      font-size: .625rem;
      font-weight: 500;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: var(--cea-gray-500);
    }

    /* Primary nav links */
    .nav-links {
      display: flex;
      align-items: center;
      list-style: none;
      gap: 2px;
      margin: 0 32px;
    }

    .nav-links a {
      font-size: .8125rem;
      font-weight: 400;
      letter-spacing: .02em;
      color: var(--cea-gray-700);
      padding: 6px 14px;
      border-radius: var(--radius-sm);
      transition: var(--transition);
      position: relative;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: 2px;
      left: 14px; right: 14px;
      height: 1.5px;
      background: var(--cea-gold);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform var(--transition);
    }

    .nav-links a:hover,
    .nav-links a.active {
      color: var(--cea-navy);
    }

    .nav-links a:hover::after,
    .nav-links a.active::after {
      transform: scaleX(1);
    }

    /* CTA button in nav */
    .nav-cta {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
    }

    .nav-access-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-size: .75rem;
      font-weight: 500;
      letter-spacing: .07em;
      text-transform: uppercase;
      color: var(--cea-navy);
      background: var(--cea-gray-100);
      border: 1.5px solid var(--cea-gray-300);
      border-radius: var(--radius-sm);
      padding: 9px 16px;
      transition: var(--transition);
    }

    .nav-access-btn svg { width: 14px; height: 14px; flex-shrink: 0; }

    .nav-access-btn:hover {
      background: var(--cea-navy);
      color: var(--cea-white);
      border-color: var(--cea-navy);
    }

    /* Hamburger (mobile) */
    .nav-hamburger {
      display: none;
      flex-direction: column;
      gap: 5px;
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
      margin-left: 12px;
    }
    .nav-hamburger span {
      display: block;
      width: 22px; height: 2px;
      background: var(--cea-navy);
      border-radius: 2px;
      transition: var(--transition);
    }

    /* Thin gold accent bar at very top */
    #site-header::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--cea-navy) 0%, var(--cea-gold) 60%, var(--cea-gold-light) 100%);
    }

    /* =========================================
       MAIN CONTENT AREA
    ========================================= */
    main {
      flex: 1;
      /* Page content goes here */
    }

    /* =========================================
       FOOTER
    ========================================= */
    #site-footer {
      background: var(--cea-navy);
      color: rgba(255,255,255,.75);
      padding: 64px 0 0;
      margin-top: auto;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 1.8fr 1fr 1fr 1fr;
      gap: 40px 48px;
      padding-bottom: 48px;
      border-bottom: 1px solid rgba(255,255,255,.08);
    }

    /* Footer brand column */
    .footer-brand {}

    .footer-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
    }

    .footer-logo-mark {
      width: 38px; height: 38px;
      background: rgba(255,255,255,.08);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(255,255,255,.12);
    }

    .footer-logo-mark span {
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 600;
      color: var(--cea-gold-light);
    }

    .footer-logo-text strong {
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 500;
      color: var(--cea-white);
    }

    .footer-brand p {
      font-size: .875rem;
      line-height: 1.7;
      color: rgba(255,255,255,.55);
      max-width: 280px;
      margin-bottom: 24px;
    }

    .footer-social {
      display: flex;
      gap: 10px;
    }

    .footer-social a {
      width: 34px; height: 34px;
      background: rgba(255,255,255,.07);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
      color: rgba(255,255,255,.6);
    }
    .footer-social a:hover {
      background: var(--cea-gold);
      border-color: var(--cea-gold);
      color: var(--cea-white);
    }
    .footer-social svg { width: 15px; height: 15px; }

    /* Footer columns */
    .footer-col h4 {
      font-family: var(--font-body);
      font-size: .6875rem;
      font-weight: 500;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: var(--cea-gold-light);
      margin-bottom: 18px;
    }

    .footer-col ul {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .footer-col ul a {
      font-size: .875rem;
      color: rgba(255,255,255,.55);
      transition: color var(--transition);
    }
    .footer-col ul a:hover { color: var(--cea-white); }

    .footer-col address {
      font-style: normal;
      font-size: .875rem;
      color: rgba(255,255,255,.55);
      line-height: 1.8;
    }

    .footer-col address strong {
      display: block;
      color: rgba(255,255,255,.8);
      font-weight: 500;
      margin-bottom: 6px;
    }

    /* Footer bottom bar */
    .footer-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 0;
      font-size: .75rem;
      color: rgba(255,255,255,.3);
      flex-wrap: wrap;
      gap: 8px;
    }

    .footer-bottom a {
      color: rgba(255,255,255,.4);
      transition: color var(--transition);
    }
    .footer-bottom a:hover { color: rgba(255,255,255,.8); }

    .footer-bottom-links {
      display: flex;
      gap: 20px;
    }

    /* Gold divider accent */
    .footer-gold-line {
      width: 40px;
      height: 2px;
      background: var(--cea-gold);
      margin-bottom: 28px;
    }

    /* =========================================
       PLACEHOLDER MAIN CONTENT (DEMO)
    ========================================= */
    .placeholder-main {
      padding: 96px 0;
      text-align: center;
    }

    .placeholder-badge {
      display: inline-block;
      font-size: .6875rem;
      font-weight: 500;
      letter-spacing: .16em;
      text-transform: uppercase;
      color: var(--cea-gold);
      border: 1px solid var(--cea-gold);
      border-radius: 20px;
      padding: 5px 16px;
      margin-bottom: 24px;
    }

    .placeholder-main h1 {
      font-family: var(--font-display);
      font-size: clamp(2rem, 4vw, 3rem);
      font-weight: 400;
      color: var(--cea-navy);
      line-height: 1.25;
      margin-bottom: 16px;
    }

    .placeholder-main h1 em {
      font-style: italic;
      color: var(--cea-gold);
    }

    .placeholder-main p {
      font-size: 1.0625rem;
      color: var(--cea-gray-500);
      max-width: 520px;
      margin: 0 auto 36px;
    }

    .placeholder-note {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--cea-white);
      border: 1px dashed var(--cea-gray-300);
      border-radius: var(--radius-md);
      padding: 12px 24px;
      font-size: .8125rem;
      color: var(--cea-gray-500);
      margin-top: 16px;
    }

    /* =========================================
       RESPONSIVE
    ========================================= */
    @media (max-width: 1024px) {
      .footer-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 768px) {
      :root { --nav-height: 68px; }

      .nav-links { display: none; }

      .nav-hamburger { display: flex; }

      /* Mobile menu open state */
      body.nav-open .nav-links {
        display: flex;
        flex-direction: column;
        position: fixed;
        top: var(--nav-height);
        left: 0; right: 0;
        background: var(--cea-white);
        padding: 20px 24px 32px;
        box-shadow: 0 12px 32px rgba(13,31,53,.12);
        gap: 4px;
        z-index: 99;
        animation: slideDown .22s ease;
      }

      body.nav-open .nav-links a {
        padding: 10px 14px;
        font-size: .9375rem;
      }

      body.nav-open .nav-hamburger span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
      body.nav-open .nav-hamburger span:nth-child(2) { opacity: 0; }
      body.nav-open .nav-hamburger span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

      @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
      }

      .nav-logo-text { display: none; }

      .footer-grid {
        grid-template-columns: 1fr;
        gap: 36px;
      }

      .footer-bottom {
        flex-direction: column;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      .nav-cta .btn-primary { display: none; }
    }
  </style>
</head>
<body>

  <!-- ============================
       HEADER / NAV
  ============================= -->
  <header id="site-header" role="banner">
    <div class="container">
      <nav class="nav-inner" aria-label="Navegación principal">

        <!-- Logo -->
        <a href="/" class="nav-logo" aria-label="CEA - Inicio">
          <div class="nav-logo-mark">
            <span>CEA</span>
          </div>
          <div class="nav-logo-text">
            <strong>CEA</strong>
            <small>Formando ciudadanos íntegros</small>
          </div>
        </a>

        <!-- Links -->
        <ul class="nav-links" id="nav-links" role="list">
          <li><a href="/" class="active">Inicio</a></li>
          <li><a href="/niveles">Niveles Educativos</a></li>
          <li><a href="/programa-care">Programa C.A.R.E.</a></li>
          <li><a href="/admisiones">Admisiones</a></li>
          <li><a href="/nosotros">Nosotros</a></li>
        </ul>

        <!-- CTAs -->
        <div class="nav-cta">
          <!-- Community access -->
          <a href="/acceso-comunidad" class="nav-access-btn" aria-label="Acceso Comunidad CEA">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Acceso Comunidad
          </a>

          <!-- Admission CTA -->
          <a href="/admisiones" class="btn btn-primary">
            Iniciar Pre-alta
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </a>
        </div>

        <!-- Hamburger -->
        <button class="nav-hamburger" id="nav-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="nav-links">
          <span></span><span></span><span></span>
        </button>

      </nav>
    </div>
  </header>


  <!-- ============================
       MAIN — Aquí va el contenido
       específico de cada página
  ============================= -->
  <main id="main-content" role="main">
    <?php echo $view_content; ?>
    <!-- 
      ╔══════════════════════════════════════════╗
      ║   ZONA DE CONTENIDO DE LA PÁGINA         ║
      ║                                          ║
      ║   Aquí irá el contenido específico de    ║
      ║   cada vista:                            ║
      ║   • Inicio / Hero                        ║
      ║   • Niveles Educativos                   ║
      ║   • Programa C.A.R.E.                    ║
      ║   • Admisiones                           ║
      ║   • Nosotros                             ║
      ╚══════════════════════════════════════════╝
    -->

    <!-- DEMO PLACEHOLDER -->
    <div class="placeholder-main">
      <div class="container">
        <span class="placeholder-badge">Plantilla Base · CEA 2025–2026</span>
        <h1>Educación, Formación y<br><em>Desarrollo integral</em></h1>
        <p>Aquí irá el contenido específico de cada sección del sitio web.</p>
        <a href="/admisiones" class="btn btn-primary">Conoce el proceso de Admisión</a>
        <br>
        <span class="placeholder-note">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
          El contenido de cada página irá dentro de <code>&lt;main&gt;&lt;/main&gt;</code>
        </span>
      </div>
    </div>

  </main>


  <!-- ============================
       FOOTER
  ============================= -->
  <footer id="site-footer" role="contentinfo">
    <div class="container">

      <div class="footer-grid">

        <!-- Brand column -->
        <div class="footer-brand">
          <div class="footer-logo">
            <div class="footer-logo-mark"><span>CEA</span></div>
            <div class="footer-logo-text">
              <strong>CEA</strong>
            </div>
          </div>
          <div class="footer-gold-line"></div>
          <p>Somos la escuela que conoce a tu hijo por su nombre y te da la tranquilidad de que está seguro y aprendiendo.</p>
          <!-- Social -->
          <div class="footer-social" aria-label="Redes sociales">
            <a href="#" aria-label="Facebook">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="#" aria-label="Instagram">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5" fill="currentColor"/></svg>
            </a>
            <a href="#" aria-label="WhatsApp">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
            </a>
          </div>
        </div>

        <!-- Col 2 -->
        <div class="footer-col">
          <h4>Navegación</h4>
          <ul>
            <li><a href="/inicio">Inicio</a></li>
            <li><a href="/niveles">Niveles Educativos</a></li>
            <li><a href="/programa-care">Programa C.A.R.E.</a></li>
            <li><a href="/admisiones">Admisiones</a></li>
            <li><a href="/nosotros">Nosotros</a></li>
          </ul>
        </div>

        <!-- Col 3 -->
        <div class="footer-col">
          <h4>Niveles</h4>
          <ul>
            <li><a href="/niveles/preescolar">Preescolar</a></li>
            <li><a href="/niveles/primaria">Primaria</a></li>
            <li><a href="/niveles/secundaria">Secundaria</a></li>
            <li><a href="/admisiones">Proceso de Admisión</a></li>
            <li><a href="/acceso-comunidad">Acceso Comunidad</a></li>
          </ul>
        </div>

        <!-- Col 4 -->
        <div class="footer-col">
          <h4>Contacto</h4>
          <address>
            <strong>Visítanos</strong>
            Dirección completa<br>
            Ciudad, Estado, C.P.
            <br><br>
            <strong>Llámanos</strong>
            +52 (000) 000-0000
            <br><br>
            <strong>Escríbenos</strong>
            contacto@colegiocea.edu.mx
          </address>
        </div>

      </div>

      <!-- Bottom bar -->
      <div class="footer-bottom">
        <span>© 2025 CEA · Colegio de Educación Avanzada. Todos los derechos reservados.</span>
        <div class="footer-bottom-links">
          <a href="/privacidad">Aviso de Privacidad</a>
          <a href="/terminos">Términos de Uso</a>
        </div>
      </div>

    </div>
  </footer>


  <!-- ============================
       SCRIPTS
  ============================= -->
  <script>
    // Mobile nav toggle
    const toggle = document.getElementById('nav-toggle');
    const navLinks = document.getElementById('nav-links');

    toggle.addEventListener('click', () => {
      const isOpen = document.body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', isOpen);
      toggle.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
    });

    // Close nav on link click (mobile)
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        document.body.classList.remove('nav-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });

    // Active link highlighting based on current path
    const currentPath = window.location.pathname;
    navLinks.querySelectorAll('a').forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === currentPath) {
        link.classList.add('active');
      }
    });

    // Subtle scroll shadow enhancement on nav
    const header = document.getElementById('site-header');
    window.addEventListener('scroll', () => {
      header.style.boxShadow = window.scrollY > 10
        ? '0 1px 0 rgba(13,31,53,.06), 0 4px 16px rgba(13,31,53,.08)'
        : '0 1px 0 rgba(13,31,53,.08)';
    }, { passive: true });
  </script>

</body>
</html>