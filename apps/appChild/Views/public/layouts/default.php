<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Centro Educativo América | Educación, Formación, Desarrollo', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= htmlspecialchars($baseCss ?? '/assets/appChild/css/layouts/default.css', ENT_QUOTES, 'UTF-8') ?>">
  <?php if (!empty($contentCss)): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($contentCss, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
</head>
<body class="cea-body" data-institution="CEA">
  <header class="cea-header" id="ceaHeader">
    <div class="cea-container cea-header__inner">
      <a href="/" class="cea-brand" aria-label="Centro Educativo América - Inicio">
        <img
          src="/assets/appChild/img/logo_3.png"
          alt="Logo Centro Educativo América"
          class="cea-brand__logo"
          width="40"
          height="40"
        >
        <span class="cea-brand__text">
          <strong class="cea-brand__name">Centro Educativo América</strong>
          <span class="cea-brand__motto">Educación, Formación, Desarrollo</span>
        </span>
      </a>

      <button
        type="button"
        class="cea-menu-toggle"
        id="ceaMenuToggle"
        aria-expanded="false"
        aria-controls="ceaNav"
        aria-label="Abrir o cerrar menú de navegación"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <nav class="cea-nav" id="ceaNav" aria-label="Navegación institucional">
        <ul class="cea-nav__list">
          <li class="cea-nav__item"><a href="/#inicio" class="cea-nav__link">Inicio</a></li>
          <li class="cea-nav__item"><a href="/#nosotros" class="cea-nav__link">Nosotros</a></li>
          <li class="cea-nav__item"><a href="/#oferta-educativa" class="cea-nav__link">Oferta Educativa</a></li>
          <li class="cea-nav__item"><a href="/#programa-care" class="cea-nav__link">Programa C.A.R.E.</a></li>
          <li class="cea-nav__item"><a href="/#contacto" class="cea-nav__link">Contacto</a></li>
        </ul>

        <div class="cea-nav__actions">
          <a href="/moodle" class="cea-btn cea-btn--ghost">Portal Padres</a>
          <a href="/admisiones" class="cea-btn cea-btn--primary">Admisiones</a>
        </div>
      </nav>
    </div>
    <div class="cea-level-strip" aria-hidden="true">
      <span class="cea-level-strip__seg cea-level-strip__seg--preescolar"></span>
      <span class="cea-level-strip__seg cea-level-strip__seg--primaria"></span>
      <span class="cea-level-strip__seg cea-level-strip__seg--secundaria"></span>
    </div>
  </header>

  <main class="cea-main" id="ceaMain">
    <div class="cea-container">
      <?= $content ?? '' ?>
    </div>
  </main>

  <footer class="cea-footer" id="ceaFooter">
    <div class="cea-container cea-footer__grid">
      <div class="cea-footer__brand">
        <p class="cea-footer__title">Centro Educativo América</p>
        <p class="cea-footer__text">Educación, Formación, Desarrollo · Mérida, Yucatán.</p>
      </div>
      <div class="cea-footer__meta">
        <p class="cea-footer__copy">&copy; <?= date('Y') ?> Centro Educativo América. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  <script src="<?= htmlspecialchars($baseJs ?? '/assets/appChild/js/cea-layout.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
  <?php if (!empty($contentJs)): ?>
    <script src="<?= htmlspecialchars($contentJs, ENT_QUOTES, 'UTF-8') ?>" defer></script>
  <?php endif; ?>
</body>
</html>