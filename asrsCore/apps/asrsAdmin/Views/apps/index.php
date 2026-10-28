<?php
use asrsCore\apps\asrsAdmin\Controllers\AdminController;

// Control de acceso perimetral exclusivo para superadmin
$adminController = new AdminController();

// Si la petición es POST (conmutador vía Fetch), delegar al endpoint de guardado
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $adminController->setDefaultApp();
}

$viewData    = $adminController->appsIndex();
$currentUser = $viewData['currentUser'] ?? null;
$appsList    = $viewData['apps'] ?? [];

$totalApps    = count($appsList);
$activeApps   = count(array_filter($appsList, fn($a) => (int)($a['status'] ?? 0) === 1));
$defaultName  = '—';
foreach ($appsList as $a) {
    if ((int)($a['is_default'] ?? 0) === 1) {
        $defaultName = (string)($a['name'] ?? '—');
        break;
    }
}

/** Enmascara la app_key: solo se muestran los primeros 4 caracteres. */
$maskKey = static function (string $key): string {
    return mb_substr($key, 0, 4) . str_repeat('•', 8);
};
?>

<div class="asrs-apps-management-container">
    <!-- 1. Cabecera de Sección -->
    <div class="apps-view-header">
        <div class="header-titles">
            <h1 class="apps-view-title">[ GESTOR DE APPS ]</h1>
            <p class="apps-view-subtitle">Consola centralizada para administrar las aplicaciones del ecosistema ASRS y definir la aplicación predeterminada del sitio.</p>
        </div>
        <div class="apps-security-badge">
            <span class="apps-security-indicator"></span>
            <span class="apps-security-label">[ CONTROL EXCLUSIVO: SUPERADMIN ]</span>
        </div>
    </div>

    <!-- 2. Resumen -->
    <div class="apps-summary">
        <span class="apps-summary-item">Apps registradas: <strong id="apps-total-count"><?= $totalApps ?></strong></span>
        <span class="apps-summary-item">Activas: <strong><?= $activeApps ?></strong></span>
        <span class="apps-summary-item">Predeterminada: <strong id="apps-default-name"><?= htmlspecialchars($defaultName) ?></strong></span>
    </div>

    <!-- Banner de retroalimentación global -->
    <div id="apps-feedback-banner" class="apps-feedback" role="status" aria-live="polite" style="display: none;"></div>

    <!-- 3. Rejilla de tarjetas por aplicación -->
    <div class="apps-cards-grid" id="apps-cards-grid">
        <?php if (empty($appsList)): ?>
            <div class="apps-empty">[ NO SE ENCONTRARON APLICACIONES REGISTRADAS ]</div>
        <?php else: foreach ($appsList as $app):
            $id         = (int)$app['id'];
            $isActive   = (int)($app['status'] ?? 0) === 1;
            $isDefault  = (int)($app['is_default'] ?? 0) === 1;
            $cardColor  = $isActive ? 'card-cyan' : 'card-red';
            $inputId    = 'app-default-switch-' . $id;
        ?>
            <div class="app-card <?= $cardColor ?><?= $isDefault ? ' is-default' : '' ?>" data-app-id="<?= $id ?>" data-app-name="<?= htmlspecialchars((string)$app['name'], ENT_QUOTES) ?>">
                <div class="apps-card-top">
                    <span class="apps-card-code">[ APP-<?= str_pad((string)$id, 2, '0', STR_PAD_LEFT) ?> // <?= $isActive ? 'ACTIVE' : 'DISABLED' ?> ]</span>
                    <span class="apps-card-status <?= $isActive ? 'active' : 'danger' ?>"><?= $isActive ? '● ONLINE' : '■ OFFLINE' ?></span>
                </div>

                <div class="apps-card-content">
                    <h2 class="apps-card-title"><?= htmlspecialchars((string)$app['name']) ?></h2>
                    <p class="apps-card-description"><?= htmlspecialchars((string)($app['description'] ?? '')) ?: '—' ?></p>
                </div>

                <div class="apps-card-meta">
                    <span class="apps-meta-item apps-mono">Key: <?= htmlspecialchars($maskKey((string)($app['app_key'] ?? ''))) ?></span>
                    <span class="apps-meta-item">Alta: <?= htmlspecialchars(substr((string)($app['created_at'] ?? ''), 0, 10)) ?></span>
                </div>

                <div class="apps-card-bottom">
                    <span class="apps-default-badge" data-default-badge <?= $isDefault ? '' : 'hidden' ?>>★ PREDETERMINADA</span>
                    <label class="apps-switch-wrap<?= $isActive ? '' : ' is-disabled' ?>" for="<?= $inputId ?>">
                        <span class="apps-switch-label">Predeterminada</span>
                        <span class="apps-switch">
                            <input type="checkbox"
                                   id="<?= $inputId ?>"
                                   class="apps-switch-input"
                                   data-app-id="<?= $id ?>"
                                   role="switch"
                                   <?= $isDefault ? 'checked' : '' ?>
                                   <?= $isActive ? '' : 'disabled' ?>>
                            <span class="apps-switch-slider"></span>
                        </span>
                    </label>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- 4. Aviso -->
    <div class="apps-notice-panel">
        <div class="notice-header">
            <span class="notice-icon">[!]</span>
            <span class="notice-title">AVISO DE ARQUITECTURA PERIMETRAL</span>
        </div>
        <p class="notice-body">
            Solo una aplicación puede ser la predeterminada y debe estar activa. Al accionar el interruptor se actualiza <code>asrs_apps.is_default</code> y se recompilan las cachés <code>apps.php</code> y <code>routes.php</code> de forma inmediata.
        </p>
    </div>
</div>

<script src="assets/js/asrs-apps.js"></script>
