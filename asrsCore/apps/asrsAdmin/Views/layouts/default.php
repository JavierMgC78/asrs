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
                <?php
                $userRoles    = (array)($currentUser['roles'] ?? []);
                $isSuperAdmin = in_array('superadmin', $userRoles, true);
                $isAdmin      = $isSuperAdmin || in_array('admin', $userRoles, true);
                $isCashier    = $isSuperAdmin || in_array('cashregister', $userRoles, true);
                $isTeacher    = $isSuperAdmin || in_array('teacher', $userRoles, true);
                $activeUri    = $currentUri ?? '';
                ?>
                <nav class="sidebar-nav">
                    <!-- Dashboard Principal -->
                    <a href="asrsadmin/dashboard" class="nav-link <?= ($activeUri === 'asrsadmin/dashboard' || $activeUri === '') ? 'active' : '' ?>">[ Dashboard ]</a>

                    <!-- Módulo: Núcleo & Sistema (Superadmin) -->
                    <?php if ($isSuperAdmin): ?>
                    <div class="sidebar-nav-group">
                        <button type="button" class="nav-accordion-toggle" aria-expanded="false">
                            <span class="accordion-icon">[+]</span>
                            <span class="accordion-title">[ SISTEMA & NÚCLEO ]</span>
                        </button>
                        <div class="nav-accordion-body">
                            <a href="admin/routes" class="nav-link sub-link <?= ($activeUri === 'admin/routes') ? 'active' : '' ?>">&gt; Rutas & Caché</a>
                            <a href="admin/roles" class="nav-link sub-link <?= ($activeUri === 'admin/roles') ? 'active' : '' ?>">&gt; Roles & Permisos</a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Módulo: Seguridad & Usuarios (Superadmin y Admin) -->
                    <?php if ($isAdmin): ?>
                    <div class="sidebar-nav-group">
                        <button type="button" class="nav-accordion-toggle" aria-expanded="false">
                            <span class="accordion-icon">[+]</span>
                            <span class="accordion-title">[ USUARIOS & ACL ]</span>
                        </button>
                        <div class="nav-accordion-body">
                            <a href="admin/users" class="nav-link sub-link <?= ($activeUri === 'admin/users') ? 'active' : '' ?>">&gt; Gestión de Usuarios</a>
                            <a href="admin/sessions" class="nav-link sub-link <?= ($activeUri === 'admin/sessions') ? 'active' : '' ?>">&gt; Sesiones Perimetrales</a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Módulo: Finanzas & Caja (Superadmin y Cajero) -->
                    <?php if ($isCashier): ?>
                    <div class="sidebar-nav-group">
                        <button type="button" class="nav-accordion-toggle" aria-expanded="false">
                            <span class="accordion-icon">[+]</span>
                            <span class="accordion-title">[ CAJA & FINANZAS ]</span>
                        </button>
                        <div class="nav-accordion-body">
                            <a href="admin/cashregister" class="nav-link sub-link <?= ($activeUri === 'admin/cashregister') ? 'active' : '' ?>">&gt; Terminal de Caja</a>
                            <a href="admin/payments" class="nav-link sub-link <?= ($activeUri === 'admin/payments') ? 'active' : '' ?>">&gt; Registro de Pagos</a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Módulo: Gestión Académica (Superadmin y Docente) -->
                    <?php if ($isTeacher): ?>
                    <div class="sidebar-nav-group">
                        <button type="button" class="nav-accordion-toggle" aria-expanded="false">
                            <span class="accordion-icon">[+]</span>
                            <span class="accordion-title">[ PORTAL ACADÉMICO ]</span>
                        </button>
                        <div class="nav-accordion-body">
                            <a href="admin/academic/groups" class="nav-link sub-link <?= ($activeUri === 'admin/academic/groups') ? 'active' : '' ?>">&gt; Control de Grupos</a>
                            <a href="admin/academic/attendance" class="nav-link sub-link <?= ($activeUri === 'admin/academic/attendance') ? 'active' : '' ?>">&gt; Registro Asistencias</a>
                        </div>
                    </div>
                    <?php endif; ?>
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
