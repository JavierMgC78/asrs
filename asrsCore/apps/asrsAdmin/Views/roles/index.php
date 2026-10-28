<?php
use asrsCore\apps\asrsAdmin\Controllers\AdminController;

// Control de acceso perimetral exclusivo para superadmin
$adminController = new AdminController();

// Si la petición es POST (alta de rol vía Fetch), delegar al endpoint de guardado
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $postBody = json_decode((string)file_get_contents('php://input'), true);
    if (is_array($postBody) && ($postBody['action'] ?? '') === 'update') {
        $adminController->updateRole();
    }
    if (is_array($postBody) && ($postBody['action'] ?? '') === 'delete') {
        $adminController->deleteRole();
    }
    $adminController->storeRole();
}

$viewData = $adminController->rolesIndex();
$currentUser = $viewData['currentUser'] ?? null;

// Carga de solo lectura del catálogo de roles y conteo de vistas asignadas
$rolesList = [];
try {
    $pdo = \asrsCore\Core\Conection::getInstance();
    $rolesList = $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll(\PDO::FETCH_ASSOC);
    $viewsPerRole = [];
    foreach ($pdo->query("SELECT roles FROM asrs_views WHERE roles IS NOT NULL")->fetchAll(\PDO::FETCH_COLUMN) as $json) {
        $decoded = json_decode((string)$json, true);
        foreach ((is_array($decoded) ? $decoded : []) as $rn) {
            $viewsPerRole[$rn] = ($viewsPerRole[$rn] ?? 0) + 1;
        }
    }
    foreach ($rolesList as &$rl) {
        $rl['views_count'] = $viewsPerRole[$rl['name'] ?? ''] ?? 0;
    }
    unset($rl);
} catch (\Throwable $e) {
    $rolesList = [];
}
?>

<div class="asrs-roles-management-container">
    <!-- 1. Cabecera de Sección -->
    <div class="roles-view-header">
        <div class="header-titles">
            <h1 class="roles-view-title">[ GESTOR DE ROLES ]</h1>
            <p class="roles-view-subtitle">Consola centralizada para definición, mutación y baja controlada de roles institucionales de la matriz ACL.</p>
        </div>
        <div class="roles-security-badge">
            <span class="roles-security-indicator"></span>
            <span class="roles-security-label">[ CONTROL EXCLUSIVO: SUPERADMIN ]</span>
        </div>
    </div>

    <!-- 2. Grid de 3 Tarjetas (vista estática) -->
    <div class="roles-cards-grid">

        <!-- Tarjeta 0: Listado de roles (despliega catálogo) -->
        <div class="role-action-card card-cyan" id="card-role-list">
            <div class="roles-card-top">
                <span class="roles-card-code">[ ROL-00 // LIST ]</span>
                <span class="roles-card-status active">● ONLINE</span>
            </div>
            <div class="roles-card-content">
                <h2 class="roles-card-title">Listado de roles</h2>
                <p class="roles-card-description">Consulta y auditoría de los roles institucionales registrados: nombre, nombre a mostrar, descripción, estatus y vistas asignadas.</p>
            </div>
            <div class="roles-card-bottom">
                <div class="roles-card-meta">
                    <span class="roles-meta-item">Modo: LECTURA</span>
                    <span class="roles-meta-item" id="roles-meta-count">Roles: <?= count($rolesList) ?></span>
                </div>
                <button type="button" id="btn-toggle-roles-catalog" class="roles-btn-card btn-cyan" aria-expanded="false" aria-controls="roles-catalog-drawer">
                    <span class="btn-catalog-text">[ CONSULTAR CATÁLOGO ]</span>
                </button>
            </div>
        </div>

        <!-- Tarjeta 1: Nuevo rol (disparador de modal) -->
        <div class="role-action-card card-cyan" id="card-role-create">
            <div class="roles-card-top">
                <span class="roles-card-code">[ ROL-01 // PROVISION ]</span>
                <span class="roles-card-status active">● READY</span>
            </div>
            <div class="roles-card-content">
                <h2 class="roles-card-title">Nuevo rol</h2>
                <p class="roles-card-description">
                    Alta y definición de nuevos roles institucionales en la matriz de control de acceso perimetral (ACL). Configuración de identificador interno, nombre a mostrar y estatus operativo.
                </p>
            </div>
            <div class="roles-card-bottom">
                <div class="roles-card-meta">
                    <span class="roles-meta-item">Vector: INSERT_ACL</span>
                    <span class="roles-meta-item">Modo: ASYNC_FETCH</span>
                </div>
                <button type="button" id="btn-open-create-role-modal" class="roles-btn-card btn-cyan btn-provision-toggle" aria-haspopup="dialog" aria-expanded="false" aria-controls="modal-create-role">
                    [ CREAR NUEVO ROL ]
                </button>
            </div>
        </div>

        <!-- Tarjeta 2: Modificar datos del rol -->
        <div class="role-action-card card-amber" id="card-role-edit">
            <div class="roles-card-top">
                <span class="roles-card-code">[ ROL-02 // MUTATION ]</span>
                <span class="roles-card-status warning">▲ AUDIT</span>
            </div>
            <div class="roles-card-content">
                <h2 class="roles-card-title">Modificar datos del rol</h2>
                <p class="roles-card-description">Actualización de nombre, nombre a mostrar, descripción y estatus de un rol existente.</p>

                <form id="form-role-edit" class="roles-form" autocomplete="off" novalidate>
                    <div class="roles-form-group">
                        <label for="role-edit-select" class="roles-form-label"><span class="label-prefix">&gt;</span> ROL A MODIFICAR <span class="required">*</span></label>
                        <select id="role-edit-select" name="role_id" class="roles-form-input roles-form-select">
                            <option value="" selected>-- Seleccione un rol --</option>
                            <?php foreach ($rolesList as $ro): ?>
                                <option value="<?= (int)$ro['id'] ?>"
                                    data-name="<?= htmlspecialchars((string)($ro['name'] ?? ''), ENT_QUOTES) ?>"
                                    data-display="<?= htmlspecialchars((string)($ro['display_name'] ?? ''), ENT_QUOTES) ?>"
                                    data-description="<?= htmlspecialchars((string)($ro['description'] ?? ''), ENT_QUOTES) ?>"
                                    data-status="<?= (int)($ro['status'] ?? 1) ?>"><?= htmlspecialchars((string)($ro['name'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="roles-form-group">
                        <label for="role-edit-name" class="roles-form-label"><span class="label-prefix">&gt;</span> NOMBRE (name)</label>
                        <input type="text" id="role-edit-name" name="name" class="roles-form-input roles-mono" placeholder="Identificador interno" disabled>
                    </div>
                    <div class="roles-form-group">
                        <label for="role-edit-display" class="roles-form-label"><span class="label-prefix">&gt;</span> NOMBRE A MOSTRAR (display_name)</label>
                        <input type="text" id="role-edit-display" name="display_name" class="roles-form-input" placeholder="Nombre visible" disabled>
                    </div>
                    <div class="roles-form-group">
                        <label for="role-edit-description" class="roles-form-label"><span class="label-prefix">&gt;</span> DESCRIPCIÓN</label>
                        <textarea id="role-edit-description" name="description" rows="3" class="roles-form-input roles-form-textarea" placeholder="Descripción del rol" disabled></textarea>
                    </div>
                    <div class="roles-form-group">
                        <label for="role-edit-status" class="roles-form-label"><span class="label-prefix">&gt;</span> ESTATUS</label>
                        <select id="role-edit-status" name="status" class="roles-form-input roles-form-select" disabled>
                            <option value="1">● ACTIVO</option>
                            <option value="0">■ INACTIVO</option>
                        </select>
                    </div>
                    <div id="role-edit-feedback" class="roles-form-hint" role="status" aria-live="polite"></div>
                    <button type="submit" id="btn-role-edit" class="roles-btn-card btn-amber" disabled>[ ACTUALIZAR ROL ]</button>
                </form>
            </div>
            <div class="roles-card-bottom">
                <div class="roles-card-meta">
                    <span class="roles-meta-item">Mutación: UPDATE_ROLE</span>
                    <span class="roles-meta-item">Modo: ESTÁTICO</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Eliminar rol -->
        <div class="role-action-card card-red" id="card-role-delete">
            <div class="roles-card-top">
                <span class="roles-card-code">[ ROL-03 // REVOCATION ]</span>
                <span class="roles-card-status danger">■ CRITICAL</span>
            </div>
            <div class="roles-card-content">
                <h2 class="roles-card-title">Eliminar rol</h2>
                <p class="roles-card-description">Baja de un rol institucional de la matriz ACL.</p>

                <div class="roles-restriction-notice" role="note">
                    <span class="notice-tag">[ RESTRICCIÓN ]</span>
                    <span>No se podrá eliminar un rol que tenga vistas asignadas. Primero debe retirar sus asignaciones en el catálogo de rutas.</span>
                </div>

                <form id="form-role-delete" class="roles-form" autocomplete="off" novalidate>
                    <div class="roles-form-group">
                        <label for="role-delete-select" class="roles-form-label"><span class="label-prefix">&gt;</span> ROL A ELIMINAR <span class="required">*</span></label>
                        <select id="role-delete-select" name="role_id" class="roles-form-input roles-form-select">
                            <option value="" selected>-- Seleccione un rol --</option>
                            <?php foreach ($rolesList as $rd): ?>
                                <option value="<?= (int)$rd['id'] ?>" data-name="<?= htmlspecialchars((string)($rd['name'] ?? ''), ENT_QUOTES) ?>" data-views="<?= (int)$rd['views_count'] ?>"><?= htmlspecialchars((string)($rd['name'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="roles-assigned-views" id="role-delete-views-check">
                        <span class="assigned-label">VISTAS ASIGNADAS:</span>
                        <span class="assigned-value" id="role-delete-views-count">— (pendiente de validación)</span>
                    </div>
                    <div id="role-delete-feedback" class="roles-form-hint" role="status" aria-live="polite"></div>
                    <button type="submit" id="btn-role-delete" class="roles-btn-card btn-red" disabled>[ ELIMINAR ROL ]</button>
                </form>
            </div>
            <div class="roles-card-bottom">
                <div class="roles-card-meta">
                    <span class="roles-meta-item">Riesgo: CRÍTICO</span>
                    <span class="roles-meta-item">Validación: VISTAS_ASIGNADAS</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor desplegable: Catálogo de roles (oculto por defecto) -->
    <section id="roles-catalog-drawer" class="roles-catalog-drawer" aria-hidden="true">
        <div class="roles-drawer-header">
            <h2 class="roles-drawer-title">[≡] CATÁLOGO DE ROLES // MATRIZ_ACL</h2>
            <div class="roles-drawer-controls">
                <span class="roles-drawer-count" id="roles-drawer-count">[ <?= count($rolesList) ?> REGISTRO(S) ]</span>
                <button type="button" id="btn-close-roles-catalog" class="roles-btn-drawer-close">[ ✕ CERRAR ]</button>
            </div>
        </div>
        <div class="roles-table-wrapper">
            <table class="roles-wireframe-table" id="roles-catalog-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>NOMBRE A MOSTRAR</th>
                        <th>DESCRIPCIÓN</th>
                        <th>ESTATUS</th>
                        <th>VISTAS ASIGNADAS</th>
                    </tr>
                </thead>
                <tbody id="roles-table-body">
                    <?php if (empty($rolesList)): ?>
                        <tr><td colspan="6" class="roles-empty">[ NO SE ENCONTRARON ROLES REGISTRADOS ]</td></tr>
                    <?php else: foreach ($rolesList as $r): $active = ((int)($r['status'] ?? 1)) === 1; ?>
                        <tr data-role-id="<?= (int)$r['id'] ?>">
                            <td><span class="roles-id-badge">#<?= str_pad((string)$r['id'], 2, '0', STR_PAD_LEFT) ?></span></td>
                            <td class="roles-mono"><?= htmlspecialchars((string)($r['name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($r['display_name'] ?? '')) ?></td>
                            <td class="roles-desc-cell"><?= htmlspecialchars((string)($r['description'] ?? '—')) ?></td>
                            <td><span class="roles-status-pill <?= $active ? 'is-active' : 'is-inactive' ?>"><?= $active ? '● ACTIVO' : '■ INACTIVO' ?></span></td>
                            <td><span class="roles-views-badge"><?= (int)$r['views_count'] ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 3. Aviso de estado -->
    <div class="roles-notice-panel">
        <div class="notice-header">
            <span class="notice-icon">[!]</span>
            <span class="notice-title">AVISO DE ARQUITECTURA PERIMETRAL</span>
        </div>
        <p class="notice-body">
            Este módulo se encuentra configurado para administración perimetral de roles institucionales. La tarjeta ROL-01 permite aprovisionar nuevos roles mediante modal interactivo con sincronización asíncrona de catálogo y selectores.
        </p>
    </div>
</div>

<!-- ==========================================================================
     Modal Flotante de Alta de Rol (ROL-01)
     Estética Neon Dark & Wireframe Outline UI
     ========================================================================== -->
<div id="modal-create-role" class="asrs-modal-backdrop" style="display: none;" aria-hidden="true" role="dialog" aria-labelledby="modal-role-title">
    <div class="asrs-modal-container">
        <!-- Cabecera del Modal -->
        <div class="modal-header">
            <div class="modal-title-group">
                <span class="modal-icon">[+]</span>
                <h2 id="modal-role-title" class="modal-title">ALTA DE NUEVO ROL // MATRIZ_ACL</h2>
            </div>
            <button type="button" id="btn-close-role-modal-x" class="btn-modal-close" aria-label="Cerrar ventana modal">[ ✕ ]</button>
        </div>

        <!-- Banner de estado / info en modal -->
        <div class="modal-wireframe-info">
            <span class="info-tag">[ VECTOR DE ENTRADA ]</span>
            <span>Aprovisionamiento perimetral de rol institucional con sincronización en tiempo real.</span>
        </div>

        <!-- Banner de alerta dinámica dentro del modal -->
        <div id="modal-role-alert-banner" class="modal-alert" style="display: none;"></div>

        <!-- Formulario de Aprovisionamiento -->
        <form id="form-role-create" class="roles-form" autocomplete="off" novalidate>
            <div class="modal-body">
                <!-- Fila 1: Nombre (identificador interno) -->
                <div class="roles-form-group">
                    <label for="role-create-name" class="roles-form-label">
                        <span class="label-prefix">&gt;</span> NOMBRE (name) <span class="required">*</span>
                    </label>
                    <input type="text" id="role-create-name" name="name" class="roles-form-input roles-mono" placeholder="Ej. coordinator" required autocomplete="off">
                    <span class="roles-form-hint">Identificador interno único, en minúsculas y sin espacios.</span>
                </div>

                <!-- Fila 2: Nombre a mostrar y Estatus -->
                <div class="form-row">
                    <div class="roles-form-group">
                        <label for="role-create-display" class="roles-form-label">
                            <span class="label-prefix">&gt;</span> NOMBRE A MOSTRAR (display_name) <span class="required">*</span>
                        </label>
                        <input type="text" id="role-create-display" name="display_name" class="roles-form-input" placeholder="Ej. Coordinador Académico" required autocomplete="off">
                        <span class="roles-form-hint">Etiqueta institucional visible.</span>
                    </div>

                    <div class="roles-form-group">
                        <label for="role-create-status" class="roles-form-label">
                            <span class="label-prefix">&gt;</span> ESTATUS <span class="required">*</span>
                        </label>
                        <select id="role-create-status" name="status" class="roles-form-input roles-form-select" required>
                            <option value="1" selected>● ACTIVO</option>
                            <option value="0">■ INACTIVO</option>
                        </select>
                        <span class="roles-form-hint">Estado operativo del rol.</span>
                    </div>
                </div>

                <!-- Fila 3: Descripción -->
                <div class="roles-form-group">
                    <label for="role-create-description" class="roles-form-label">
                        <span class="label-prefix">&gt;</span> DESCRIPCIÓN
                    </label>
                    <textarea id="role-create-description" name="description" rows="3" class="roles-form-input roles-form-textarea" placeholder="Alcance y responsabilidades del rol..."></textarea>
                    <span class="roles-form-hint">Alcance y responsabilidades en la matriz ACL.</span>
                </div>

                <div id="role-create-feedback" class="roles-form-hint" role="status" aria-live="polite"></div>
            </div>

            <!-- Footer con Botones de Acción -->
            <div class="modal-footer">
                <button type="button" id="btn-close-role-modal" class="roles-btn-ghost">[ CANCELAR ]</button>
                <button type="submit" id="btn-role-create" class="roles-btn-card btn-cyan">[ GUARDAR ROL ]</button>
            </div>
        </form>
    </div>
</div>

<!-- Estilos dedicados para el modal de Roles (Neon Dark & Wireframe) -->
<style>
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
    background-color: var(--panel-bg, #0b0f19);
    border: 1px solid var(--neon-cyan, #00f0ff);
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
    border-bottom: 1px solid var(--border-wire, rgba(0, 240, 255, 0.2));
    background: rgba(0, 240, 255, 0.03);
}

.modal-title-group {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.modal-icon {
    color: var(--neon-cyan, #00f0ff);
    font-weight: bold;
    font-size: 1.1rem;
}

.modal-title {
    font-size: 1rem;
    color: var(--neon-cyan, #00f0ff);
    letter-spacing: 1.5px;
    font-weight: bold;
    text-shadow: 0 0 8px rgba(0, 240, 255, 0.4);
    margin: 0;
}

.btn-modal-close {
    background: transparent;
    border: 1px solid transparent;
    color: var(--neon-red, #ff3366);
    font-family: inherit;
    font-size: 0.95rem;
    font-weight: bold;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    transition: all 0.2s;
}

.btn-modal-close:hover {
    border-color: var(--neon-red, #ff3366);
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
    color: var(--text-muted, #8892b0);
}

.info-tag {
    color: var(--neon-cyan, #00f0ff);
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
    border-color: var(--neon-red, #ff3366);
    background: rgba(255, 51, 102, 0.1);
    color: var(--neon-red, #ff3366);
}

.modal-alert.success {
    border-color: var(--neon-cyan, #00f0ff);
    background: rgba(0, 240, 255, 0.1);
    color: var(--neon-cyan, #00f0ff);
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

.modal-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-top: 1px dashed var(--border-wire, rgba(0, 240, 255, 0.2));
    background: rgba(0, 240, 255, 0.02);
}

.roles-btn-ghost {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--text-muted, #8892b0);
    padding: 0.65rem 1rem;
    cursor: pointer;
    font-family: inherit;
    font-size: 0.8rem;
    font-weight: bold;
    letter-spacing: 1px;
    transition: all 0.2s;
}

.roles-btn-ghost:hover {
    border-color: var(--text-main, #ccd6f6);
    color: var(--text-main, #ccd6f6);
}

.btn-provision-toggle {
    cursor: pointer;
}
</style>
