<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASRS - Panel de Administración</title>
    <base href="<?= htmlspecialchars($baseUrl ?? '/') ?>">
    <?php foreach (($cssFiles ?? []) as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>
</head>
<body class="asrs-admin-body">
    <div class="asrs-app-container">
        <!-- Header -->
        <header class="asrs-header">
            <div class="header-left">
                <img src="assets/asrsWeb/img/logo_axe_web.png" alt="ASRS Logo" class="asrs-logo">
                <span class="system-name">ASRS</span>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? '') ?></span>
                    <span class="user-role">[<?= htmlspecialchars(implode(', ', $currentUser['roles'] ?? [])) ?>]</span>
                </div>
            </div>
        </header>

        <div class="asrs-main-wrapper">
            <!-- Sidebar Izquierda -->
            <aside class="asrs-sidebar">
                <nav class="sidebar-nav">
                    <a href="asrsadmin/dashboard" class="nav-link active">[ Dashboard ]</a>
                    <a href="admin/routes" class="nav-link">[ Rutas & Caché ]</a>
                    <a href="admin/users" class="nav-link">[ Usuarios & ACL ]</a>
                    <a href="admin/roles" class="nav-link">[ Roles & Permisos ]</a>
                </nav>
                <div class="sidebar-footer">
                    <button id="btn-logout" class="asrs-btn-logout">CERRAR SESIÓN</button>
                </div>
            </aside>

            <!-- Contenido Principal -->
            <main class="asrs-content-area">
                <?= $content ?? '' ?>
            </main>
        </div>

        <!-- Footer -->
        <footer class="asrs-footer">
            <p>Desarrollado por coffee flavored software</p>
        </footer>
    </div>

    <?php foreach (($jsFiles ?? []) as $js): ?>
        <script src="<?= htmlspecialchars($js) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
