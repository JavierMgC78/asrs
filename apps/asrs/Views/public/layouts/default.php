<?php
declare(strict_types=1);

$pageTitle = isset($pageTitle) && is_string($pageTitle) && $pageTitle !== ''
    ? $pageTitle
    : 'ASRS · Axe Secure Router System';

$projectRoot = realpath(__DIR__ . '/../../../../') ?: dirname(__DIR__, 4);
$publicRoot = $projectRoot . DIRECTORY_SEPARATOR . 'public';
$scopeFolder = 'public';

$rawContentName = (string)($route['content_name'] ?? $route['content'] ?? $contentName ?? $content_name ?? $viewContent ?? '');
if ($rawContentName === '' && !empty($contentViewPath)) {
    $rawContentName = pathinfo((string)$contentViewPath, PATHINFO_FILENAME);
}
$safeContentName = basename(trim($rawContentName));
if (str_ends_with(strtolower($safeContentName), '.php')) {
    $safeContentName = substr($safeContentName, 0, -4);
}
$safeContentName = (string)preg_replace('/[^a-zA-Z0-9_-]/', '', $safeContentName);

if (defined('APP_CHILD_IS_DEFAULT')) {
    $appFolder = ((int)constant('APP_CHILD_IS_DEFAULT') === 1) ? 'appChild' : 'asrs';
} else {
    $cfgFile = $projectRoot . DIRECTORY_SEPARATOR . 'asrsCore' . DIRECTORY_SEPARATOR . 'config.php';
    $cfg = is_file($cfgFile) ? (require $cfgFile) : [];
    $appFolder = (is_array($cfg) && (int)($cfg['APP_CHILD_IS_DEFAULT'] ?? 0) === 1) ? 'appChild' : 'asrs';
}

$resolvedCssUrl = isset($contentCssUrl) && is_string($contentCssUrl) && $contentCssUrl !== '' ? $contentCssUrl : null;
$resolvedJsUrl  = isset($contentJsUrl) && is_string($contentJsUrl) && $contentJsUrl !== '' ? $contentJsUrl : null;

if ($safeContentName !== '') {
    if ($resolvedCssUrl === null) {
        $cssDisk = $publicRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $appFolder . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $scopeFolder . DIRECTORY_SEPARATOR . 'contents' . DIRECTORY_SEPARATOR . $safeContentName . '.css';
        if (is_file($cssDisk)) {
            $resolvedCssUrl = '/assets/' . $appFolder . '/css/' . $scopeFolder . '/contents/' . $safeContentName . '.css';
        }
    }
    if ($resolvedJsUrl === null) {
        $jsDisk = $publicRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $appFolder . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $scopeFolder . DIRECTORY_SEPARATOR . 'contents' . DIRECTORY_SEPARATOR . $safeContentName . '.js';
        if (is_file($jsDisk)) {
            $resolvedJsUrl = '/assets/' . $appFolder . '/js/' . $scopeFolder . '/contents/' . $safeContentName . '.js';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Activos base fijos del layout público ASRS -->
  <link rel="stylesheet" href="assets/asrs/css/public/layouts/default.css">

  <!-- CSS inferido por vista (solo si existe en disco) -->
  <?php if ($resolvedCssUrl !== null): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($resolvedCssUrl, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
</head>
<body class="asrs-layout-body">
  <a class="asrs-skip-link" href="#asrsMainContent">Saltar al contenido principal</a>

  <header class="asrs-header" id="asrsHeader">
    <div class="asrs-header__inner">
      <a href="/" class="asrs-brand" aria-label="ASRS Inicio">
        <span class="asrs-brand__mark" aria-hidden="true">
          <svg viewBox="0 0 32 32" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M16 2.5L27 7.2v8.6c0 7.1-4.6 12.6-11 15.7-6.4-3.1-11-8.6-11-15.7V7.2L16 2.5z" />
            <line x1="16" y1="7" x2="16" y2="25" />
            <path d="M10.5 11.5c2 1.2 3.8 1.2 5.5 0M21.5 11.5c-2 1.2-3.8 1.2-5.5 0" />
            <path d="M10.5 20.5c2-1.2 3.8-1.2 5.5 0M21.5 20.5c-2-1.2-3.8-1.2-5.5 0" />
          </svg>
        </span>
        <span class="asrs-brand__text">
          <strong>ASRS</strong>
          <small>AXE SECURE ROUTER</small>
        </span>
      </a>

      <button
        type="button"
        class="asrs-nav-toggle"
        id="asrsNavToggle"
        aria-expanded="false"
        aria-controls="asrsNav"
        aria-label="Abrir o cerrar menú de navegación"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <nav class="asrs-nav" id="asrsNav" aria-label="Navegación principal del sistema">
        <ul class="asrs-nav__list">
          <li><a href="/#inicio" class="asrs-nav__link">Inicio</a></li>
          <li><a href="/#arquitectura" class="asrs-nav__link">Arquitectura</a></li>
          <li><a href="/#estado-operativo" class="asrs-nav__link">Estado Operativo</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="asrs-main" id="asrsMainContent">
    <div class="asrs-container">
      <?php
      if (isset($contentViewPath) && is_string($contentViewPath) && is_file($contentViewPath)) {
          require $contentViewPath;
      } else {
          $fallbackContent = __DIR__ . '/../contents/' .$contentName . '.php';
          if (is_file($fallbackContent)) {
              require $fallbackContent;
          }
      }
      ?>
    </div>
  </main>

  <footer class="asrs-footer" id="asrsFooter">
    <div class="asrs-container asrs-footer__inner">
      <div class="asrs-footer__meta">
        <span class="asrs-footer__tag">ASRS · KERNEL v4</span>
        <p>Motor de enrutamiento y seguridad perimetral · PHP &ge; 8.1 · PSR-4 · PDO</p>
      </div>
      <div class="asrs-footer__status">
        <span class="asrs-status-dot" aria-hidden="true"></span>
        <span>PERIMETER ONLINE</span>
      </div>
    </div>
  </footer>

  <!-- JS base fijo del layout público ASRS -->
  <script src="/assets/asrs/js/layouts/default.js" defer></script>

  <!-- JS inferido por vista (solo si existe en disco) -->
  <?php if ($resolvedJsUrl !== null): ?>
    <script src="<?= htmlspecialchars($resolvedJsUrl, ENT_QUOTES, 'UTF-8') ?>" defer></script>
  <?php endif; ?>
</body>
</html>