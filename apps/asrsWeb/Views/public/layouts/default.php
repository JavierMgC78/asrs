<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'ASRS | Axe Secure Router System', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= htmlspecialchars($baseCss ?? '/assets/asrs/css/asrs-public-layout.css', ENT_QUOTES, 'UTF-8') ?>">
  <?php if (!empty($cssPathView)): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssPathView, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
</head>
<body class="asrs-core-body" data-theme="neon-outline">
  <header class="asrs-topbar" id="asrsTopbar">
    <div class="asrs-shell asrs-topbar__inner">
      <a href="/asrs" class="asrs-logo">
        <span class="asrs-logo__mark" aria-hidden="true"></span>
        <span class="asrs-logo__text"><?= htmlspecialchars($systemName ?? 'ASRS', ENT_QUOTES, 'UTF-8') ?></span>
      </a>

      <button
        type="button"
        class="asrs-burger"
        id="asrsBurger"
        aria-expanded="false"
        aria-controls="asrsNav"
        aria-label="Alternar navegación del sistema"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <nav class="asrs-core-nav" id="asrsNav" aria-label="Navegación ASRS">
        <ul class="asrs-core-nav__list">
          <li class="asrs-core-nav__item"><a href="/asrs" class="asrs-core-nav__link">Inicio</a></li>
          <li class="asrs-core-nav__item"><a href="/asrs/arquitectura" class="asrs-core-nav__link">Arquitectura</a></li>
          <li class="asrs-core-nav__item"><a href="/asrs/estado" class="asrs-core-nav__link">Estado</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="asrs-workspace" id="asrsWorkspace">
    <div class="asrs-shell">
      <?php
        if (!empty($contentFile) && is_file($contentFile)) {
            require $contentFile;
        } else {
            echo '<section class="asrs-panel"><p>Vista de sistema no disponible.</p></section>';
        }
      ?>
    </div>
  </main>

  <footer class="asrs-sys-footer" id="asrsSysFooter">
    <div class="asrs-shell asrs-sys-footer__inner">
      <p class="asrs-sys-footer__copy">ASRS — Axe Secure Router System</p>
      <p class="asrs-sys-footer__status" id="asrsRuntimeStatus">Núcleo activo</p>
    </div>
  </footer>

  <script src="<?= htmlspecialchars($baseJs ?? '/assets/asrs/js/asrs-public-layout.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
  <?php if (!empty($jsPathView)): ?>
    <script src="<?= htmlspecialchars($jsPathView, ENT_QUOTES, 'UTF-8') ?>" defer></script>
  <?php endif; ?>
</body>
</html>