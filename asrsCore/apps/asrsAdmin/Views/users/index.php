<?php
use asrsCore\apps\asrsAdmin\Controllers\AdminController;
use asrsCore\Core\Conection;

// Control de acceso y carga perimetral para superadmin
$adminController = new AdminController();

// Si la petición hacia esta vista es POST, delegar al endpoint de aprovisionamiento
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $adminController->storeUser();
}

$viewData = $adminController->usersIndex();
$currentUser = $viewData['currentUser'] ?? null;

// Carga del catálogo de identidades activas
$usersList = [];
try {
    $pdo = Conection::getInstance();
    $stmtUsers = $pdo->query("
        SELECT u.id, u.name, u.email, u.status, 
               COALESCE(GROUP_CONCAT(r.name SEPARATOR ', '), 'sin_rol') AS roles,
               u.created_at
        FROM asrs_users u
        LEFT JOIN asrs_user_roles ur ON u.id = ur.user_id
        LEFT JOIN roles r ON ur.role_id = r.id
        GROUP BY u.id
        ORDER BY u.id ASC
    ");
    if ($stmtUsers) {
        $usersList = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (\Throwable $e) {
    $usersList = [];
}
?>

<div class="asrs-users-management-container">
    <!-- 1. Cabecera de Sección -->
    <div class="view-header">
        <div class="header-titles">
            <h1 class="view-title">[ GESTOR DE USUARIOS ]</h1>
            <p class="view-subtitle">Consola centralizada para aprovisionamiento, auditoría y control perimetral de identidades institucionales.</p>
        </div>
        <div class="header-security-badge">
            <span class="security-indicator active"></span>
            <span class="security-label">[ CONTROL EXCLUSIVO: SUPERADMIN ]</span>
        </div>
    </div>

    <!-- Banner de notificación interactiva tipo consola -->
    <div id="users-console-alert" class="console-alert-banner success" style="display: none;">
        <span id="users-console-alert-icon" class="console-icon">[OK]</span>
        <span id="users-console-alert-msg" class="console-msg"></span>
    </div>

    <!-- 2. Grid de las 4 Tarjetas Wireframe / Monospaced (Alineadas en una sola fila) -->
    <div class="users-cards-grid">
        <!-- Tarjeta 1: Ver lista de usuarios (Accionable para desplegar catálogo) -->
        <div class="user-action-card card-cyan">
            <div class="card-top">
                <span class="card-code">[ USR-01 // LIST ]</span>
                <span class="card-status active">● ONLINE</span>
            </div>
            <div class="card-content">
                <h2 class="card-title">Ver lista de usuarios</h2>
                <p class="card-description">
                    Exploración integral del catálogo de identidades activas en el núcleo ASRS. Visualización de metadatos, correos institucionales, fechas de creación y asignación de roles perimetrales en tiempo real.
                </p>
            </div>
            <div class="card-bottom">
                <div class="card-meta">
                    <span class="meta-item">Modo: LECTURA_O(1)</span>
                    <span class="meta-item">Filtro: MULTI-TENANT</span>
                </div>
                <button type="button" id="btn-toggle-users-catalog" class="asrs-btn-card btn-cyan btn-catalog-toggle" aria-expanded="false" aria-controls="users-catalog-drawer">
                    <span class="btn-catalog-text">[ CONSULTAR CATÁLOGO ]</span>
                </button>
            </div>
        </div>

        <!-- Tarjeta 2: Añadir usuario -->
        <div class="user-action-card card-cyan">
            <div class="card-top">
                <span class="card-code">[ USR-02 // PROVISION ]</span>
                <span class="card-status active">● READY</span>
            </div>
            <div class="card-content">
                <h2 class="card-title">Añadir usuario</h2>
                <p class="card-description">
                    Aprovisionamiento perimetral de nuevas cuentas de acceso. Asignación granular de roles institucionales (superadmin, admin, cashregister, teacher, student) y generación de hash seguro bcrypt sin terceros.
                </p>
            </div>
            <div class="card-bottom">
                <div class="card-meta">
                    <span class="meta-item">Vector: INSERT_AUTH</span>
                    <span class="meta-item">Cifrado: BCRYPT_SALT</span>
                </div>
                <button type="button" id="btn-open-create-user-modal" class="asrs-btn-card btn-cyan btn-provision-toggle" aria-haspopup="dialog" aria-expanded="false" aria-controls="modal-create-user">
                    [ APROVISIONAR CUENTA ]
                </button>
            </div>
        </div>

        <!-- Tarjeta 3: Modificar datos de usuario -->
        <div class="user-action-card card-amber">
            <div class="card-top">
                <span class="card-code">[ USR-03 // MUTATION ]</span>
                <span class="card-status warning">▲ AUDIT</span>
            </div>
            <div class="card-content">
                <h2 class="card-title">Modificar datos de usuario</h2>
                <p class="card-description">
                    Actualización de identidades, edición de nombres y direcciones de correo electrónico corporativo, reconfiguración de permisos perimetrales y rotación programada de credenciales de seguridad.
                </p>
            </div>
            <div class="card-bottom">
                <div class="card-meta">
                    <span class="meta-item">Auditoría: ACTIVADA</span>
                    <span class="meta-item">Mutación: ACL_UPDATE</span>
                </div>
                <button type="button" class="asrs-btn-card btn-amber disabled" disabled aria-disabled="true">
                    [ EDITAR REGISTRO ]
                </button>
            </div>
        </div>

        <!-- Tarjeta 4: Eliminar/desactivar usuario -->
        <div class="user-action-card card-red">
            <div class="card-top">
                <span class="card-code">[ USR-04 // REVOCATION ]</span>
                <span class="card-status danger">■ CRITICAL</span>
            </div>
            <div class="card-content">
                <h2 class="card-title">Eliminar/desactivar usuario</h2>
                <p class="card-description">
                    Revocación inmediata de privilegios perimetrales, anulación forzada de Split Tokens activos (Parte A en BD y Parte B en cliente) y baja lógica/física para mitigación de vectores de ataque.
                </p>
            </div>
            <div class="card-bottom">
                <div class="card-meta">
                    <span class="meta-item">Riesgo: CRÍTICO</span>
                    <span class="meta-item">Purga: SPLIT_TOKEN</span>
                </div>
                <button type="button" class="asrs-btn-card btn-red disabled" disabled aria-disabled="true">
                    [ REVOCAR ACCESO ]
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Contenedor Dinámico Desplegable: Catálogo de Usuarios (Despliegue fluido) -->
    <section id="users-catalog-drawer" class="users-catalog-drawer" aria-hidden="true">
        <div class="catalog-drawer-header">
            <div class="drawer-title-group">
                <span class="drawer-icon">[≡]</span>
                <h2 class="drawer-title">CATÁLOGO ACTIVO DE IDENTIDADES // REPOSITORIO_DB</h2>
                <span class="drawer-count-badge">[ <?= count($usersList) ?> REGISTRO(S) ]</span>
            </div>
            <div class="drawer-controls">
                <div class="filter-box-group">
                    <span class="filter-icon">🔍</span>
                    <input type="text" id="users-filter-input" class="asrs-wireframe-input" placeholder="Filtrar por ID, nombre, correo o rol..." autocomplete="off" />
                </div>
                <button type="button" id="btn-close-users-catalog" class="btn-drawer-close" title="Cerrar catálogo de usuarios">
                    [ ✕ CERRAR ]
                </button>
            </div>
        </div>

        <div class="catalog-table-wrapper">
            <table class="asrs-wireframe-table" id="users-catalog-table">
                <thead>
                    <tr>
                        <th class="th-id">ID</th>
                        <th class="th-name">NOMBRE COMPLETO</th>
                        <th class="th-email">CORREO INSTITUCIONAL</th>
                        <th class="th-role">ROLES PERIMETRALES</th>
                        <th class="th-status">ESTADO</th>
                        <th class="th-created">ALTA EN SISTEMA</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <?php if (empty($usersList)): ?>
                        <tr class="empty-row">
                            <td colspan="6" class="text-center-muted">[ NO SE ENCONTRARON IDENTIDADES EN LA BASE DE DATOS ]</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usersList as $u): ?>
                            <?php
                            $isActive = ((int)($u['status'] ?? 0)) === 1;
                            $rolesRaw = explode(',', (string)($u['roles'] ?? ''));
                            ?>
                            <tr class="user-row-item">
                                <td class="col-id">
                                    <span class="id-badge">#<?= str_pad((string)$u['id'], 2, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="col-name">
                                    <span class="user-name-text"><?= htmlspecialchars($u['name'] ?? 'Identidad sin nombre') ?></span>
                                </td>
                                <td class="col-email">
                                    <span class="user-email-text"><?= htmlspecialchars($u['email'] ?? '-') ?></span>
                                </td>
                                <td class="col-role">
                                    <div class="roles-tag-group">
                                        <?php foreach ($rolesRaw as $rName): ?>
                                            <?php
                                            $cleanRole = trim($rName);
                                            $isSuper = ($cleanRole === 'superadmin');
                                            ?>
                                            <span class="role-pill <?= $isSuper ? 'role-super' : 'role-standard' ?>">
                                                [ <?= htmlspecialchars($cleanRole) ?> ]
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="col-status">
                                    <span class="status-pill <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                                        <?= $isActive ? '● ACTIVO' : '■ INACTIVO' ?>
                                    </span>
                                </td>
                                <td class="col-created">
                                    <span class="date-text"><?= htmlspecialchars($u['created_at'] ?? 'N/D') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 4. Terminal de Información de Estado -->
    <div class="security-notice-panel">
        <div class="notice-header">
            <span class="notice-icon">[!]</span>
            <span class="notice-title">AVISO DE ARQUITECTURA PERIMETRAL</span>
        </div>
        <p class="notice-body">
            Este módulo se encuentra configurado en modo estático para verificación de interfaz y modelado visual. La tarjeta USR-01 permite consultar el catálogo de identidades activas en tiempo real. Los accionadores de mutación, inserción y revocación permanecen en espera de protocolo según el ciclo de aislamiento estricto.
        </p>
    </div>
</div>

<!-- ==========================================================================
     Modal Flotante de Aprovisionamiento de Usuarios (USR-02)
     Estética Neon Dark & Wireframe Outline UI
     ========================================================================== -->
<div id="modal-create-user" class="asrs-modal-backdrop" style="display: none;" aria-hidden="true" role="dialog" aria-labelledby="modal-user-title">
    <div class="asrs-modal-container">
        <!-- Cabecera del Modal -->
        <div class="modal-header">
            <div class="modal-title-group">
                <span class="modal-icon">[+]</span>
                <h2 id="modal-user-title" class="modal-title">APROVISIONAMIENTO DE NUEVO USUARIO // REGISTRO_AUTH</h2>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeCreateUserModal()" aria-label="Cerrar ventana modal">[ ✕ ]</button>
        </div>

        <!-- Banner de estado / info en modal -->
        <div class="modal-wireframe-info">
            <span class="info-tag">[ VECTOR DE ENTRADA ]</span>
            <span>Aprovisionamiento perimetral con cifrado nativo bcrypt y asignación granular de rol.</span>
        </div>

        <!-- Banner de alerta dinámica dentro del modal -->
        <div id="modal-user-alert-banner" class="modal-alert" style="display: none;"></div>

        <!-- Formulario de Aprovisionamiento -->
        <form id="form-create-user" onsubmit="handleUserFormSubmit(event)">
            <div class="modal-body">
                <!-- Fila 1: Nombre completo -->
                <div class="form-group">
                    <label for="user-input-name" class="form-label">
                        <span class="label-prefix">&gt;</span> NOMBRE COMPLETO <span class="required">*</span>
                    </label>
                    <input type="text" id="user-input-name" name="name" placeholder="Ej. Carlos Mendoza López" required autocomplete="name" class="asrs-form-input">
                    <span class="form-hint">Nombre e iniciales para visualización institucional.</span>
                </div>

                <!-- Fila 2: Correo institucional y Rol -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="user-input-email" class="form-label">
                            <span class="label-prefix">&gt;</span> CORREO INSTITUCIONAL <span class="required">*</span>
                        </label>
                        <input type="email" id="user-input-email" name="email" placeholder="usuario@asrs.edu.mx" required autocomplete="email" class="asrs-form-input">
                        <span class="form-hint">Identificador perimetral único para autenticación.</span>
                    </div>

                    <div class="form-group">
                        <label for="user-input-role" class="form-label">
                            <span class="label-prefix">&gt;</span> ROL INSTITUCIONAL <span class="required">*</span>
                        </label>
                        <select id="user-input-role" name="role" class="asrs-form-select" required>
                            <option value="student" selected>student (Estudiante)</option>
                            <option value="teacher">teacher (Docente)</option>
                            <option value="cashregister">cashregister (Cajero / Finanzas)</option>
                            <option value="admin">admin (Administrador General)</option>
                            <option value="superadmin">superadmin (Superadministrador Núcleo)</option>
                        </select>
                        <span class="form-hint">Privilegios y permisos en matriz ACL.</span>
                    </div>
                </div>

                <!-- Fila 3: Contraseña bcrypt -->
                <div class="form-group">
                    <label for="user-input-password" class="form-label">
                        <span class="label-prefix">&gt;</span> CONTRASEÑA DE ACCESO (BCRYPT) <span class="required">*</span>
                    </label>
                    <input type="password" id="user-input-password" name="password" placeholder="••••••••••••" required autocomplete="new-password" class="asrs-form-input font-mono">
                    <span class="form-hint">Generación perimetral de hash seguro con algoritmo bcrypt nativo (cost 12).</span>
                </div>
            </div>

            <!-- Footer con Botones de Acción -->
            <div class="modal-footer">
                <button type="button" class="asrs-btn-ghost" onclick="closeCreateUserModal()">[ CANCELAR ]</button>
                <button type="submit" id="btn-submit-user" class="asrs-btn-card btn-cyan btn-save-user">[ GUARDAR ]</button>
            </div>
        </form>
    </div>
</div>

<!-- Estilos Dedicados para el Gestor de Usuarios (Neon Dark & Wireframe) -->
<style>
.asrs-users-management-container {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

/* Header */
.view-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px dashed var(--border-wire);
    padding-bottom: 1.25rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.view-title {
    font-size: 1.4rem;
    color: var(--neon-cyan);
    letter-spacing: 2px;
    font-weight: bold;
    text-shadow: 0 0 10px rgba(0, 240, 255, 0.4);
    margin-bottom: 0.35rem;
}

.view-subtitle {
    color: var(--text-muted);
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.header-security-badge {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    border: 1px solid var(--border-wire);
    background: rgba(0, 240, 255, 0.04);
    padding: 0.45rem 0.85rem;
    font-size: 0.78rem;
    letter-spacing: 1px;
}

.security-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--neon-cyan);
    box-shadow: 0 0 8px var(--neon-cyan);
}

.security-label {
    color: var(--neon-cyan);
    font-weight: bold;
}

/* Grid de Tarjetas (Alineación en una sola fila de 4 columnas) */
.users-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.15rem;
    align-items: stretch;
}

.user-action-card {
    background-color: var(--panel-bg);
    border: 1px solid var(--border-wire);
    padding: 1.25rem 1.05rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 1.1rem;
    transition: all 0.25s ease;
    position: relative;
    min-width: 0; /* Previene overflow en grid 1fr */
}

.user-action-card:hover {
    border-color: var(--border-hover);
    box-shadow: 0 0 14px rgba(0, 240, 255, 0.2);
}

.user-action-card.card-cyan {
    border-color: rgba(0, 240, 255, 0.35);
}

.user-action-card.card-cyan:hover {
    box-shadow: 0 0 15px rgba(0, 240, 255, 0.25);
    border-color: var(--neon-cyan);
}

.user-action-card.card-amber {
    border-color: rgba(255, 183, 3, 0.35);
}

.user-action-card.card-amber:hover {
    box-shadow: 0 0 15px rgba(255, 183, 3, 0.25);
    border-color: var(--neon-amber);
}

.user-action-card.card-red {
    border-color: rgba(255, 51, 102, 0.35);
}

.user-action-card.card-red:hover {
    box-shadow: 0 0 15px rgba(255, 51, 102, 0.25);
    border-color: var(--neon-red);
}

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
    padding-bottom: 0.55rem;
    gap: 0.4rem;
}

.card-code {
    font-size: 0.72rem;
    letter-spacing: 1px;
    font-weight: bold;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-status {
    font-size: 0.72rem;
    letter-spacing: 1px;
    font-weight: bold;
    white-space: nowrap;
}

.card-status.active {
    color: var(--neon-cyan);
    text-shadow: 0 0 6px rgba(0, 240, 255, 0.5);
}

.card-status.warning {
    color: var(--neon-amber);
    text-shadow: 0 0 6px rgba(255, 183, 3, 0.5);
}

.card-status.danger {
    color: var(--neon-red);
    text-shadow: 0 0 6px rgba(255, 51, 102, 0.5);
}

.card-content {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.card-title {
    font-size: 1.05rem;
    font-weight: bold;
    letter-spacing: 0.8px;
    color: var(--text-main);
    line-height: 1.3;
    margin: 0;
}

.card-cyan .card-title {
    color: var(--neon-cyan);
}

.card-amber .card-title {
    color: var(--neon-amber);
}

.card-red .card-title {
    color: var(--neon-red);
}

.card-description {
    font-size: 0.8rem;
    color: var(--text-muted);
    line-height: 1.45;
    letter-spacing: 0.2px;
    margin: 0;
}

.card-bottom {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    border-top: 1px dashed rgba(255, 255, 255, 0.08);
    padding-top: 0.75rem;
}

.card-meta {
    display: flex;
    justify-content: space-between;
    gap: 0.35rem;
    font-size: 0.68rem;
    color: var(--text-muted);
    letter-spacing: 0.3px;
    flex-wrap: wrap;
}

.meta-item {
    background: rgba(0, 0, 0, 0.35);
    padding: 0.2rem 0.35rem;
    border: 1px solid rgba(255, 255, 255, 0.05);
    white-space: nowrap;
}

.asrs-btn-card {
    width: 100%;
    padding: 0.6rem 0.4rem;
    background: transparent;
    font-family: inherit;
    font-size: 0.75rem;
    font-weight: bold;
    letter-spacing: 1px;
    text-align: center;
    border: 1px solid transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Botón interactivo para tarjeta USR-01 */
.asrs-btn-card.btn-catalog-toggle {
    cursor: pointer;
    background: rgba(0, 240, 255, 0.08);
    border-color: var(--neon-cyan);
    color: var(--neon-cyan);
    box-shadow: 0 0 8px rgba(0, 240, 255, 0.2);
}

.asrs-btn-card.btn-catalog-toggle:hover {
    background: var(--neon-cyan);
    color: var(--bg-dark);
    box-shadow: 0 0 15px rgba(0, 240, 255, 0.5);
    transform: translateY(-1px);
}

.asrs-btn-card.btn-catalog-toggle.is-open {
    background: rgba(255, 183, 3, 0.15);
    border-color: var(--neon-amber);
    color: var(--neon-amber);
    box-shadow: 0 0 10px rgba(255, 183, 3, 0.3);
}

.asrs-btn-card.btn-catalog-toggle.is-open:hover {
    background: var(--neon-amber);
    color: var(--bg-dark);
    box-shadow: 0 0 15px rgba(255, 183, 3, 0.6);
}

/* Botón interactivo para tarjeta USR-02 */
.asrs-btn-card.btn-provision-toggle {
    cursor: pointer;
    background: rgba(0, 240, 255, 0.08);
    border-color: var(--neon-cyan);
    color: var(--neon-cyan);
    box-shadow: 0 0 8px rgba(0, 240, 255, 0.2);
}

.asrs-btn-card.btn-provision-toggle:hover {
    background: var(--neon-cyan);
    color: var(--bg-dark);
    box-shadow: 0 0 15px rgba(0, 240, 255, 0.5);
    transform: translateY(-1px);
}

/* Botones desactivados para tarjetas 3 y 4 */
.asrs-btn-card.disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

.asrs-btn-card.btn-cyan.disabled {
    border-color: rgba(0, 240, 255, 0.4);
    color: var(--neon-cyan);
}

.asrs-btn-card.btn-amber.disabled {
    border-color: rgba(255, 183, 3, 0.4);
    color: var(--neon-amber);
}

.asrs-btn-card.btn-red.disabled {
    border-color: rgba(255, 51, 102, 0.4);
    color: var(--neon-red);
}

.asrs-btn-card.disabled:hover {
    opacity: 0.85;
}

/* ==========================================================================
   Contenedor Desplegable Dinámico: Catálogo de Usuarios
   ========================================================================== */
.users-catalog-drawer {
    border: 1px solid transparent;
    background: var(--panel-bg);
    display: flex;
    flex-direction: column;
    gap: 1.15rem;
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    padding: 0 1.25rem;
    transform: translateY(-8px);
    transition: max-height 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.35s ease,
                transform 0.35s ease,
                padding 0.35s ease,
                border-color 0.35s ease,
                box-shadow 0.35s ease;
    margin-top: 0;
    position: relative;
}

.users-catalog-drawer.open {
    max-height: 1400px;
    opacity: 1;
    transform: translateY(0);
    padding: 1.4rem;
    border-color: var(--border-wire);
    box-shadow: 0 0 25px rgba(0, 240, 255, 0.12);
    margin-top: 0.5rem;
}

/* Cabecera del Drawer */
.catalog-drawer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px dashed var(--border-wire);
    padding-bottom: 0.9rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.drawer-title-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.drawer-icon {
    color: var(--neon-cyan);
    font-weight: bold;
    font-size: 1.1rem;
}

.drawer-title {
    font-size: 1.05rem;
    color: var(--neon-cyan);
    font-weight: bold;
    letter-spacing: 1.5px;
    margin: 0;
}

.drawer-count-badge {
    font-size: 0.75rem;
    color: var(--neon-amber);
    border: 1px solid rgba(255, 183, 3, 0.4);
    background: rgba(255, 183, 3, 0.08);
    padding: 0.2rem 0.55rem;
    letter-spacing: 1px;
}

.drawer-controls {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    flex-wrap: wrap;
}

.filter-box-group {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid var(--border-wire);
    padding: 0.35rem 0.75rem;
}

.filter-icon {
    font-size: 0.8rem;
    opacity: 0.7;
}

.asrs-wireframe-input {
    background: transparent;
    border: none;
    outline: none;
    color: var(--text-main);
    font-family: inherit;
    font-size: 0.8rem;
    width: 240px;
    letter-spacing: 0.5px;
}

.asrs-wireframe-input::placeholder {
    color: var(--text-muted);
    font-size: 0.75rem;
}

.btn-drawer-close {
    background: transparent;
    color: var(--neon-red);
    border: 1px solid rgba(255, 51, 102, 0.5);
    padding: 0.4rem 0.85rem;
    font-family: inherit;
    font-size: 0.75rem;
    font-weight: bold;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-drawer-close:hover {
    background: var(--neon-red);
    color: var(--bg-dark);
    box-shadow: 0 0 10px rgba(255, 51, 102, 0.6);
}

/* Tabla Wireframe de Usuarios */
.catalog-table-wrapper {
    overflow-x: auto;
    border: 1px solid rgba(0, 240, 255, 0.15);
    background: rgba(0, 0, 0, 0.25);
}

.asrs-wireframe-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
    text-align: left;
}

.asrs-wireframe-table thead th {
    background-color: #0b0f19;
    color: var(--neon-cyan);
    padding: 0.8rem 1rem;
    font-weight: bold;
    letter-spacing: 1.2px;
    border-bottom: 2px solid var(--border-wire);
    white-space: nowrap;
}

.asrs-wireframe-table tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
    color: var(--text-main);
    vertical-align: middle;
}

.asrs-wireframe-table tbody tr.user-row-item:hover {
    background: rgba(0, 240, 255, 0.04);
}

.id-badge {
    color: var(--neon-cyan);
    font-weight: bold;
    letter-spacing: 1px;
}

.user-name-text {
    font-weight: bold;
    color: var(--text-main);
    letter-spacing: 0.5px;
}

.user-email-text {
    color: var(--text-muted);
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.roles-tag-group {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.role-pill {
    font-size: 0.72rem;
    padding: 0.2rem 0.5rem;
    border: 1px solid transparent;
    letter-spacing: 0.8px;
    font-weight: bold;
}

.role-pill.role-super {
    color: var(--neon-amber);
    border-color: rgba(255, 183, 3, 0.4);
    background: rgba(255, 183, 3, 0.08);
}

.role-pill.role-standard {
    color: var(--neon-cyan);
    border-color: rgba(0, 240, 255, 0.35);
    background: rgba(0, 240, 255, 0.05);
}

.status-pill {
    display: inline-block;
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    font-weight: bold;
    letter-spacing: 1px;
    border: 1px solid transparent;
}

.status-pill.status-active {
    color: var(--neon-cyan);
    border-color: rgba(0, 240, 255, 0.4);
    background: rgba(0, 240, 255, 0.08);
    text-shadow: 0 0 5px rgba(0, 240, 255, 0.4);
}

.status-pill.status-inactive {
    color: var(--neon-red);
    border-color: rgba(255, 51, 102, 0.4);
    background: rgba(255, 51, 102, 0.08);
}

.date-text {
    color: var(--text-muted);
    font-size: 0.78rem;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.text-center-muted {
    text-align: center;
    color: var(--text-muted);
    padding: 1.75rem 1rem !important;
    font-size: 0.82rem;
    letter-spacing: 1px;
}

/* Responsividad para pantallas reducidas */
@media (max-width: 1200px) {
    .users-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 680px) {
    .users-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .catalog-drawer-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .asrs-wireframe-input {
        width: 100%;
    }
}

/* Panel de Aviso */
.security-notice-panel {
    border: 1px solid var(--border-wire);
    background: rgba(0, 240, 255, 0.02);
    padding: 1.1rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.notice-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.notice-icon {
    color: var(--neon-amber);
    font-weight: bold;
}

.notice-title {
    font-size: 0.85rem;
    color: var(--neon-amber);
    font-weight: bold;
    letter-spacing: 1px;
}

.notice-body {
    font-size: 0.8rem;
    color: var(--text-muted);
    line-height: 1.5;
    letter-spacing: 0.5px;
    margin: 0;
}
/* ==========================================================================
   Estilos de Modal Flotante de Aprovisionamiento (Neon Dark & Wireframe)
   ========================================================================== */
.asrs-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(11, 15, 25, 0.88);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1.5rem;
    animation: fadeInModal 0.2s ease-out;
}

@keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
}

.asrs-modal-container {
    background-color: var(--panel-bg);
    border: 1px solid var(--neon-cyan);
    box-shadow: 0 0 25px rgba(0, 240, 255, 0.3), inset 0 0 15px rgba(0, 240, 255, 0.05);
    width: 100%;
    max-width: 650px;
    display: flex;
    flex-direction: column;
    animation: slideDownModal 0.25s ease-out;
}

@keyframes slideDownModal {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border-wire);
    background: rgba(0, 240, 255, 0.03);
}

.modal-title-group {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.modal-icon {
    color: var(--neon-cyan);
    font-weight: bold;
    font-size: 1.1rem;
}

.modal-title {
    font-size: 1rem;
    color: var(--neon-cyan);
    letter-spacing: 1.5px;
    font-weight: bold;
    text-shadow: 0 0 8px rgba(0, 240, 255, 0.4);
    margin: 0;
}

.btn-modal-close {
    background: transparent;
    border: 1px solid transparent;
    color: var(--neon-red);
    font-family: inherit;
    font-size: 0.95rem;
    font-weight: bold;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    transition: all 0.2s;
}

.btn-modal-close:hover {
    border-color: var(--neon-red);
    background: rgba(255, 51, 102, 0.1);
    box-shadow: 0 0 8px rgba(255, 51, 102, 0.4);
}

.modal-wireframe-info {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    background: rgba(0, 240, 255, 0.03);
    border-bottom: 1px dashed rgba(0, 240, 255, 0.15);
    padding: 0.65rem 1.25rem;
    font-size: 0.75rem;
    color: var(--text-muted);
}

.info-tag {
    color: var(--neon-cyan);
    font-weight: bold;
    white-space: nowrap;
}

.modal-alert {
    margin: 1rem 1.25rem 0 1.25rem;
    padding: 0.65rem 0.85rem;
    font-size: 0.85rem;
    border: 1px solid transparent;
    letter-spacing: 0.5px;
}

.modal-alert.error {
    border-color: var(--neon-red);
    background: rgba(255, 51, 102, 0.1);
    color: var(--neon-red);
}

.modal-alert.success {
    border-color: var(--neon-cyan);
    background: rgba(0, 240, 255, 0.1);
    color: var(--neon-cyan);
}

.modal-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    max-height: 75vh;
    overflow-y: auto;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 600px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.form-label {
    font-size: 0.78rem;
    color: var(--text-main);
    letter-spacing: 1px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.label-prefix {
    color: var(--neon-cyan);
}

.required {
    color: var(--neon-red);
}

.asrs-form-input, .asrs-form-select {
    width: 100%;
    background: rgba(0, 0, 0, 0.45);
    border: 1px solid var(--border-wire);
    color: var(--text-main);
    font-family: inherit;
    font-size: 0.85rem;
    padding: 0.6rem 0.8rem;
    outline: none;
    transition: all 0.2s;
    box-sizing: border-box;
}

.asrs-form-input:focus, .asrs-form-select:focus {
    border-color: var(--neon-cyan);
    box-shadow: 0 0 8px rgba(0, 240, 255, 0.35);
}

.asrs-form-select option {
    background-color: var(--panel-bg);
    color: var(--text-main);
}

.form-hint {
    font-size: 0.72rem;
    color: var(--text-muted);
    letter-spacing: 0.4px;
}

.font-mono {
    font-family: monospace;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-top: 1px dashed var(--border-wire);
    background: rgba(0, 240, 255, 0.02);
}

.asrs-btn-ghost {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--text-muted);
    padding: 0.65rem 1rem;
    cursor: pointer;
    font-family: inherit;
    font-size: 0.8rem;
    font-weight: bold;
    letter-spacing: 1px;
    transition: all 0.2s;
}

.asrs-btn-ghost:hover {
    border-color: var(--text-main);
    color: var(--text-main);
    background: rgba(255, 255, 255, 0.05);
}

.btn-save-user {
    width: auto !important;
    padding: 0.65rem 1.4rem !important;
    font-size: 0.8rem !important;
    cursor: pointer !important;
    opacity: 1 !important;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.25);
}

.btn-save-user:hover {
    background: var(--neon-cyan) !important;
    color: var(--bg-dark) !important;
    box-shadow: 0 0 15px rgba(0, 240, 255, 0.5) !important;
}

/* Banner de notificación interactiva tipo consola en página principal */
.console-alert-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1.25rem;
    border: 1px solid var(--neon-cyan);
    background: rgba(0, 240, 255, 0.05);
    color: var(--neon-cyan);
    font-size: 0.85rem;
    letter-spacing: 0.8px;
    box-shadow: 0 0 15px rgba(0, 240, 255, 0.15);
    animation: fadeInModal 0.25s ease-out;
}

.console-alert-banner .console-icon {
    font-weight: bold;
}

.row-highlight-new {
    animation: highlightRowCyan 3s ease-out;
}

@keyframes highlightRowCyan {
    0% { background: rgba(0, 240, 255, 0.3); }
    100% { background: transparent; }
}
</style>

<!-- Lógica de Interacción JavaScript (Despliegue dinámico, modal USR-02, Fetch asíncrono y filtro) -->
<script>
/* Funciones globales de apertura y cierre del modal de aprovisionamiento */
function openCreateUserModal() {
    const modal = document.getElementById('modal-create-user');
    const alertBanner = document.getElementById('modal-user-alert-banner');
    if (modal) {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        if (alertBanner) {
            alertBanner.style.display = 'none';
        }
        setTimeout(() => {
            const nameInput = document.getElementById('user-input-name');
            if (nameInput) nameInput.focus();
        }, 100);
    }
}

function closeCreateUserModal() {
    const modal = document.getElementById('modal-create-user');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

function showConsoleBanner(msg) {
    const alertBox = document.getElementById('users-console-alert');
    const alertMsg = document.getElementById('users-console-alert-msg');
    const alertIcon = document.getElementById('users-console-alert-icon');
    if (alertBox && alertMsg) {
        alertBox.className = 'console-alert-banner success';
        if (alertIcon) alertIcon.textContent = '[OK]';
        alertMsg.textContent = msg;
        alertBox.style.display = 'flex';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 5500);
    }
}

/**
 * Inserta un nuevo usuario al inicio de la tabla del catálogo con animación wireframe.
 */
function insertUserTableRow(user) {
    if (!user) return;
    const tbody = document.getElementById('users-table-body');
    if (!tbody) return;

    // Quitar mensaje de tabla vacía si existe
    const emptyRow = tbody.querySelector('.empty-row');
    if (emptyRow) {
        emptyRow.remove();
    }

    const isSuper = (user.role === 'superadmin');
    const roleClass = isSuper ? 'role-super' : 'role-standard';
    const isActive = (user.status === 1 || user.status === '1');
    const statusClass = isActive ? 'status-active' : 'status-inactive';
    const statusText = isActive ? '● ACTIVO' : '■ INACTIVO';
    const idFormatted = '#' + String(user.id || '+').padStart(2, '0');

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    const newTr = document.createElement('tr');
    newTr.className = 'user-row-item row-highlight-new';
    newTr.innerHTML = `
        <td class="col-id">
            <span class="id-badge">${escapeHtml(idFormatted)}</span>
        </td>
        <td class="col-name">
            <span class="user-name-text">${escapeHtml(user.name)}</span>
        </td>
        <td class="col-email">
            <span class="user-email-text">${escapeHtml(user.email)}</span>
        </td>
        <td class="col-role">
            <div class="roles-tag-group">
                <span class="role-pill ${roleClass}">
                    [ ${escapeHtml(user.role)} ]
                </span>
            </div>
        </td>
        <td class="col-status">
            <span class="status-pill ${statusClass}">
                ${statusText}
            </span>
        </td>
        <td class="col-created">
            <span class="date-text">${escapeHtml(user.created_at || 'Justo ahora')}</span>
        </td>
    `;

    tbody.prepend(newTr);

    setTimeout(() => {
        newTr.classList.remove('row-highlight-new');
    }, 3000);
}

/**
 * Envío asíncrono del formulario mediante Fetch para aprovisionar un nuevo usuario
 * y actualizar la tabla en tiempo real sin recargar la página.
 */
async function handleUserFormSubmit(event) {
    if (event) event.preventDefault();

    const submitBtn = document.getElementById('btn-submit-user');
    const alertBanner = document.getElementById('modal-user-alert-banner');

    const name = document.getElementById('user-input-name')?.value.trim() || '';
    const email = document.getElementById('user-input-email')?.value.trim() || '';
    const role = document.getElementById('user-input-role')?.value || 'student';
    const password = document.getElementById('user-input-password')?.value || '';

    // Validaciones locales
    if (!name || !email || !password) {
        if (alertBanner) {
            alertBanner.className = 'modal-alert error';
            alertBanner.style.display = 'block';
            alertBanner.textContent = '[!] Todos los campos obligatorios deben ser completados.';
        }
        return;
    }

    if (password.length < 6) {
        if (alertBanner) {
            alertBanner.className = 'modal-alert error';
            alertBanner.style.display = 'block';
            alertBanner.textContent = '[!] La contraseña debe contener al menos 6 caracteres.';
        }
        return;
    }

    // Estado visual de carga
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="btn-indicator">⚡</span> [ APROVISIONANDO... ]';
    }

    const payload = {
        name: name,
        email: email,
        role: role,
        password: password
    };

    const tokenB = sessionStorage.getItem('asrs_validator') || localStorage.getItem('asrs_token_b');
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };
    if (tokenB) headers['Authorization'] = `Bearer ${tokenB}`;

    try {
        const response = await fetch('admin/users', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (response.ok && data.ok) {
            // 1. Cerrar el modal
            closeCreateUserModal();

            // 2. Limpiar el formulario
            const form = document.getElementById('form-create-user');
            if (form) form.reset();

            // 3. Notificación tipo consola en la página principal
            showConsoleBanner(`[OK] ${data.message || 'Usuario aprovisionado exitosamente.'}`);

            // 4. Actualizar tabla en tiempo real sin recargar página
            insertUserTableRow(data.user);

            // 5. Actualizar contador de usuarios
            if (data.usersCount !== undefined) {
                const countBadges = document.querySelectorAll('.drawer-count-badge');
                countBadges.forEach(badge => {
                    badge.textContent = `[ ${data.usersCount} REGISTRO(S) ]`;
                });
            }

            // 6. Abrir drawer de catálogo si está cerrado para visualizar el nuevo registro
            const drawer = document.getElementById('users-catalog-drawer');
            const btnToggle = document.getElementById('btn-toggle-users-catalog');
            if (drawer && !drawer.classList.contains('open')) {
                drawer.classList.add('open');
                drawer.setAttribute('aria-hidden', 'false');
                if (btnToggle) {
                    btnToggle.setAttribute('aria-expanded', 'true');
                    btnToggle.classList.add('is-open');
                    const btnText = btnToggle.querySelector('.btn-catalog-text');
                    if (btnText) btnText.textContent = '[ OCULTAR CATÁLOGO ▲ ]';
                }
            }
        } else {
            if (alertBanner) {
                alertBanner.className = 'modal-alert error';
                alertBanner.style.display = 'block';
                alertBanner.textContent = `[ERROR] ${data.error || 'Fallo al aprovisionar el usuario.'}`;
            }
        }
    } catch (error) {
        if (alertBanner) {
            alertBanner.className = 'modal-alert error';
            alertBanner.style.display = 'block';
            alertBanner.textContent = `[ERROR DE RED] No se pudo establecer conexión con el núcleo ASRS: ${error.message}`;
        }
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = '[ GUARDAR ]';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // 1. Control del Catálogo Desplegable (USR-01)
    const btnToggle = document.getElementById('btn-toggle-users-catalog');
    const btnClose = document.getElementById('btn-close-users-catalog');
    const drawer = document.getElementById('users-catalog-drawer');
    const filterInput = document.getElementById('users-filter-input');
    const tableBody = document.getElementById('users-table-body');

    if (btnToggle && drawer) {
        const btnText = btnToggle.querySelector('.btn-catalog-text');

        function openCatalog() {
            drawer.classList.add('open');
            drawer.setAttribute('aria-hidden', 'false');
            btnToggle.setAttribute('aria-expanded', 'true');
            btnToggle.classList.add('is-open');
            if (btnText) {
                btnText.textContent = '[ OCULTAR CATÁLOGO ▲ ]';
            }
            setTimeout(function () {
                drawer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                if (filterInput) {
                    filterInput.focus();
                }
            }, 150);
        }

        function closeCatalog() {
            drawer.classList.remove('open');
            drawer.setAttribute('aria-hidden', 'true');
            btnToggle.setAttribute('aria-expanded', 'false');
            btnToggle.classList.remove('is-open');
            if (btnText) {
                btnText.textContent = '[ CONSULTAR CATÁLOGO ▼ ]';
            }
            btnToggle.focus();
        }

        btnToggle.addEventListener('click', function (e) {
            e.preventDefault();
            if (drawer.classList.contains('open')) {
                closeCatalog();
            } else {
                openCatalog();
            }
        });

        if (btnClose) {
            btnClose.addEventListener('click', function (e) {
                e.preventDefault();
                closeCatalog();
            });
        }
    }

    // 2. Control del Modal de Aprovisionamiento (USR-02)
    const btnOpenUserModal = document.getElementById('btn-open-create-user-modal');
    if (btnOpenUserModal) {
        btnOpenUserModal.addEventListener('click', function (e) {
            e.preventDefault();
            openCreateUserModal();
        });
    }

    // Cierre con Escape o clic fuera del modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCreateUserModal();
        }
    });

    document.addEventListener('click', function (e) {
        const modal = document.getElementById('modal-create-user');
        if (modal && e.target === modal) {
            closeCreateUserModal();
        }
    });

    // 3. Filtrado en tiempo real dentro del catálogo
    if (filterInput && tableBody) {
        filterInput.addEventListener('input', function () {
            const query = filterInput.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('tr.user-row-item');

            let visibleCount = 0;
            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            let emptyMsgRow = document.getElementById('filter-empty-row');
            if (visibleCount === 0) {
                if (!emptyMsgRow) {
                    emptyMsgRow = document.createElement('tr');
                    emptyMsgRow.id = 'filter-empty-row';
                    emptyMsgRow.innerHTML = '<td colspan="6" class="text-center-muted">[ NINGÚN USUARIO COINCIDE CON EL FILTRO INGRESADO ]</td>';
                    tableBody.appendChild(emptyMsgRow);
                }
            } else if (emptyMsgRow) {
                emptyMsgRow.remove();
            }
        });
    }
});
</script>
