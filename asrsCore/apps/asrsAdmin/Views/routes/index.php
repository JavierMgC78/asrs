<?php
use asrsCore\apps\asrsAdmin\Controllers\AdminController;

$adminController = new AdminController();

// Si la petición hacia esta ruta es POST (ej. inserción desde el modal), delegar al endpoint
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $adminController->storeRoute();
}

$viewData = $adminController->routesIndex();

$routes = $viewData['routes'] ?? [];
$cacheMetrics = $viewData['cacheMetrics'] ?? [];
$totalRoutes = count($routes);
?>

<div class="asrs-routes-management-container">
    <!-- 1. Cabecera de la Sección -->
    <div class="view-header">
        <div class="header-titles">
            <h1 class="view-title">[ GESTIÓN DE RUTAS & ARTEFACTOS DE CACHÉ ]</h1>
            <p class="view-subtitle">Catálogo unificado de resolución O(1), análisis de artefactos en disco y directivas ACL por rol.</p>
        </div>
        <div class="header-actions">
            <button id="btn-open-create-modal" class="asrs-btn-neon-cyan" type="button" onclick="openCreateRouteModal()">
                <span class="btn-indicator">[+]</span> [ + NUEVA RUTA ]
            </button>
            <button id="btn-purge-cache" class="asrs-btn-outline" type="button" onclick="triggerCachePurge()">
                <span class="btn-indicator">⚡</span> [ REGENERAR CACHÉS ]
            </button>
        </div>
    </div>

    <!-- Feedback Banner / Notificación de Consola -->
    <div id="console-alert" class="console-alert-banner" style="display: none;">
        <span id="console-alert-icon">[INFO]</span>
        <span id="console-alert-msg"></span>
    </div>

    <!-- 2. Grid de Métricas de Artefactos de Caché -->
    <div class="metrics-grid">
        <?php foreach ($cacheMetrics as $key => $metric): ?>
            <div class="metric-card">
                <div class="card-header">
                    <span class="card-label">[ <?= htmlspecialchars($metric['title']) ?> ]</span>
                    <span class="status-indicator <?= $metric['exists'] ? 'active' : 'inactive' ?>"></span>
                </div>
                <div class="card-body">
                    <div class="metric-value-row">
                        <span class="metric-value" id="count-<?= htmlspecialchars($key) ?>">
                            <?= str_pad((string)$metric['count'], 2, '0', STR_PAD_LEFT) ?>
                        </span>
                        <span class="metric-unit">ítems</span>
                    </div>
                    <span class="metric-desc"><?= htmlspecialchars($metric['description']) ?></span>
                </div>
                <div class="card-footer">
                    <span class="tech-tag"><?= htmlspecialchars($metric['file']) ?> (<?= number_format($metric['size'] / 1024, 2) ?> KB)</span>
                    <span class="time-tag"><?= $metric['mtime'] ? date('H:i:s d/m/Y', $metric['mtime']) : 'N/A' ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 3. Barra de Búsqueda y Filtros de Rutas -->
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-prefix">&gt;</span>
            <input type="text" id="route-filter-input" placeholder="Filtrar por URI, App, Tipo o Rol..." class="asrs-search-input" onkeyup="filterRoutesTable()">
        </div>
        <div class="filter-actions">
            <div class="filter-group">
                <button type="button" class="btn-filter active" onclick="applyTypeFilter('ALL', this)">[ TODOS ]</button>
                <button type="button" class="btn-filter" onclick="applyTypeFilter('PUBLIC', this)">[ PUBLIC ]</button>
                <button type="button" class="btn-filter" onclick="applyTypeFilter('PRIVATE', this)">[ PRIVATE ]</button>
            </div>
            <span class="routes-counter" id="visible-routes-counter">Total: <?= $totalRoutes ?> rutas</span>
        </div>
    </div>

    <!-- 4. Tabla de Catálogo de Rutas (Wireframe & Monospace) -->
    <div class="table-wireframe-wrapper">
        <table class="asrs-wireframe-table" id="routes-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#ID</th>
                    <th style="width: 220px;">URI SISTEMA</th>
                    <th style="width: 120px;">APP</th>
                    <th style="width: 240px;">CONTROLADOR / DISPATCH</th>
                    <th style="width: 100px;">TIPO</th>
                    <th>ROLES AUTORIZADOS (JSON)</th>
                    <th style="width: 110px; text-align: center;">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($routes)): ?>
                    <tr>
                        <td colspan="7" class="empty-table-msg">[!] No se detectaron rutas registradas en el catálogo.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($routes as $item): ?>
                        <?php
                        $isPrivate = ($item['type'] ?? 'public') === 'private';
                        $rolesArr = $item['roles_array'] ?? [];
                        $rolesJson = !empty($rolesArr) ? json_encode($rolesArr, JSON_UNESCAPED_SLASHES) : '[]';
                        $controllerDisplay = !empty($item['controller'])
                            ? htmlspecialchars($item['controller']) . (!empty($item['method']) ? '::' . htmlspecialchars($item['method']) : '')
                            : '<span class="text-subtle">[Kernel / ' . htmlspecialchars($item['content'] ?? 'vista') . ']</span>';
                        $uriDisplay = ($item['uri'] === '') ? '<span class="text-subtle">/ (raíz)</span>' : htmlspecialchars($item['uri']);
                        ?>
                        <tr class="route-row" data-type="<?= strtoupper($item['type'] ?? 'PUBLIC') ?>">
                            <td class="col-id"><?= htmlspecialchars((string)($item['id'] ?? '-')) ?></td>
                            <td class="col-uri">
                                <span class="uri-badge">[ <?= $uriDisplay ?> ]</span>
                            </td>
                            <td class="col-app">
                                <span class="app-tag app-<?= strtolower($item['app'] ?? '') ?>"><?= htmlspecialchars($item['app'] ?? '-') ?></span>
                            </td>
                            <td class="col-controller">
                                <?= $controllerDisplay ?>
                            </td>
                            <td class="col-type">
                                <span class="type-badge <?= $isPrivate ? 'type-private' : 'type-public' ?>">
                                    <?= strtoupper(htmlspecialchars($item['type'] ?? 'PUBLIC')) ?>
                                </span>
                            </td>
                            <td class="col-roles">
                                <?php if (!empty($rolesArr)): ?>
                                    <code class="roles-code-block"><?= htmlspecialchars($rolesJson) ?></code>
                                <?php else: ?>
                                    <span class="roles-public-tag">[*] PÚBLICO_ABIERTO</span>
                                <?php endif; ?>
                            </td>
                            <td class="col-actions">
                                <a href="<?= htmlspecialchars($item['uri']) ?>" target="_blank" class="asrs-btn-mini" title="Probar ruta en nueva pestaña">
                                    [ TEST ↗ ]
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==========================================================================
     Modal Flotante de Creación de Rutas (Neon Dark & Wireframe Outline UI)
     ========================================================================== -->
<div id="modal-create-route" class="asrs-modal-backdrop" style="display: none;" aria-hidden="true" role="dialog" aria-labelledby="modal-route-title">
    <div class="asrs-modal-container">
        <!-- Cabecera del Modal -->
        <div class="modal-header">
            <div class="modal-title-group">
                <span class="modal-icon">[+]</span>
                <h2 id="modal-route-title" class="modal-title">REGISTRO DE NUEVA RUTA / VECTOR HTTP</h2>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeCreateRouteModal()" aria-label="Cerrar ventana modal">[ ✕ ]</button>
        </div>

        <!-- Banner de Estado interno del Modal -->
        <div id="modal-alert-banner" class="modal-alert" style="display: none;"></div>

        <!-- Formulario de Creación -->
        <form id="form-create-route" onsubmit="handleRouteFormSubmit(event)">
            <div class="modal-body">
                <!-- Fila 1: URI y App -->
                <div class="form-row">
                    <div class="form-group col-uri">
                        <label for="route-input-uri" class="form-label">
                            <span class="label-prefix">&gt;</span> URI DEL SISTEMA <span class="required">*</span>
                        </label>
                        <div class="input-prefix-wrapper">
                            <span class="input-addon">/</span>
                            <input type="text" id="route-input-uri" name="uri" placeholder="ej. admin/auditoria" required autocomplete="off" class="asrs-form-input">
                        </div>
                        <span class="form-hint">Ruta relativa del Front Controller (O(1)).</span>
                    </div>

                    <div class="form-group col-app">
                        <label for="route-input-app" class="form-label">
                            <span class="label-prefix">&gt;</span> APLICACIÓN DESTINO <span class="required">*</span>
                        </label>
                        <select id="route-input-app" name="app" class="asrs-form-select" required>
                            <option value="asrsAdmin" selected>asrsAdmin (Panel Administrativo)</option>
                            <option value="ceaWeb">ceaWeb (Portal Institucional)</option>
                            <option value="asrsWeb">asrsWeb (Portal Core / Landing)</option>
                        </select>
                        <span class="form-hint">Ecosistema modular donde reside la vista.</span>
                    </div>
                </div>

                <!-- Fila 2: Tipo de Ruta y Contenido/Vista -->
                <div class="form-row">
                    <div class="form-group col-type">
                        <label for="route-input-type" class="form-label">
                            <span class="label-prefix">&gt;</span> TIPO DE ACCESO <span class="required">*</span>
                        </label>
                        <select id="route-input-type" name="type" class="asrs-form-select" onchange="handleTypeChange(this.value)">
                            <option value="private" selected>PRIVATE (Blindaje Split Token + Roles)</option>
                            <option value="public">PUBLIC (Acceso Libre Perimetral)</option>
                        </select>
                        <span class="form-hint">Nivel de seguridad verificado por el Kernel.</span>
                    </div>

                    <div class="form-group col-content">
                        <label for="route-input-content" class="form-label">
                            <span class="label-prefix">&gt;</span> VISTA / CONTENIDO (PHP) <span class="required">*</span>
                        </label>
                        <input type="text" id="route-input-content" name="content" placeholder="ej. auditoria o reportes/index" required class="asrs-form-input">
                        <span class="form-hint">Archivo relativo a Views/contents/ (sin .php).</span>
                    </div>
                </div>

                <!-- Fila 3: Controlador y Método (Opcionales) -->
                <div class="form-row">
                    <div class="form-group col-controller">
                        <label for="route-input-controller" class="form-label">
                            <span class="label-prefix">&gt;</span> CONTROLADOR (OPCIONAL)
                        </label>
                        <input type="text" id="route-input-controller" name="controller" placeholder="ej. AdminController" class="asrs-form-input">
                        <span class="form-hint">Clase controladora a despachar.</span>
                    </div>

                    <div class="form-group col-method">
                        <label for="route-input-method" class="form-label">
                            <span class="label-prefix">&gt;</span> MÉTODO / ACCIÓN (OPCIONAL)
                        </label>
                        <input type="text" id="route-input-method" name="method" placeholder="ej. index o listar" class="asrs-form-input">
                        <span class="form-hint">Función interna ejecutada por el Kernel.</span>
                    </div>
                </div>

                <!-- Fila 4: Roles Autorizados (JSON) -->
                <div class="form-group" id="group-roles-input">
                    <label for="route-input-roles" class="form-label">
                        <span class="label-prefix">&gt;</span> ROLES AUTORIZADOS (FORMATO JSON) <span class="required">*</span>
                    </label>
                    <input type="text" id="route-input-roles" name="roles" value='["superadmin"]' class="asrs-form-input font-mono" placeholder='["superadmin"]'>
                    <div class="roles-quick-tags">
                        <span class="quick-tag-label">Asignación rápida:</span>
                        <button type="button" class="btn-quick-role" onclick="setRoleTag('[\"superadmin\"]')">[ superadmin ]</button>
                        <button type="button" class="btn-quick-role" onclick="setRoleTag('[\"superadmin\", \"admin\"]')">[ superadmin + admin ]</button>
                        <button type="button" class="btn-quick-role" onclick="setRoleTag('[\"cashregister\"]')">[ cashregister ]</button>
                        <button type="button" class="btn-quick-role" onclick="setRoleTag('[\"teacher\"]')">[ teacher ]</button>
                        <button type="button" class="btn-quick-role" onclick="setRoleTag('[]')">[ ninguno / abierto ]</button>
                    </div>
                    <span class="form-hint">Matriz de roles con acceso perimetral autorizado.</span>
                </div>
            </div>

            <!-- Footer del Modal con Acciones -->
            <div class="modal-footer">
                <button type="button" class="asrs-btn-ghost" onclick="closeCreateRouteModal()">[ CANCELAR ]</button>
                <button type="submit" id="btn-submit-route" class="asrs-btn-outline btn-save-route">
                    <span class="btn-indicator">⚡</span> [ GUARDAR & COMPILAR RUTA ]
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Estilos Dedicados para la Vista de Rutas y Modal (Neon Dark & Wireframe) -->
<style>
.asrs-routes-management-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
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
    font-size: 1.35rem;
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

.header-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.asrs-btn-neon-cyan {
    background-color: transparent;
    color: var(--neon-cyan);
    border: 1px solid var(--neon-cyan);
    padding: 0.65rem 1rem;
    cursor: pointer;
    font-family: inherit;
    font-weight: bold;
    letter-spacing: 1px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.25);
}

.asrs-btn-neon-cyan:hover {
    background-color: var(--neon-cyan);
    color: var(--bg-dark);
    box-shadow: 0 0 15px rgba(0, 240, 255, 0.6);
}

/* Banner de Alerta / Consola */
.console-alert-banner {
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
    letter-spacing: 1px;
    border: 1px solid var(--border-wire);
    background: rgba(0, 240, 255, 0.05);
    display: flex;
    gap: 0.75rem;
    align-items: center;
    transition: all 0.3s ease;
}

.console-alert-banner.success {
    border-color: var(--neon-cyan);
    color: var(--neon-cyan);
    background: rgba(0, 240, 255, 0.08);
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.2);
}

.console-alert-banner.error {
    border-color: var(--neon-red);
    color: var(--neon-red);
    background: rgba(255, 51, 102, 0.08);
}

/* Grid de Métricas */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.25rem;
}

.metric-card {
    background-color: var(--panel-bg);
    border: 1px solid var(--border-wire);
    padding: 1.2rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 1rem;
    transition: all 0.25s ease;
}

.metric-card:hover {
    border-color: var(--border-hover);
    box-shadow: 0 0 12px rgba(0, 240, 255, 0.2);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-label {
    font-size: 0.8rem;
    letter-spacing: 1.5px;
    color: var(--neon-cyan);
    font-weight: bold;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.status-indicator.active {
    background-color: var(--neon-cyan);
    box-shadow: 0 0 8px var(--neon-cyan);
}

.status-indicator.inactive {
    background-color: var(--neon-red);
    box-shadow: 0 0 8px var(--neon-red);
}

.metric-value-row {
    display: flex;
    align-items: baseline;
    gap: 0.4rem;
}

.metric-value {
    font-size: 2.2rem;
    font-weight: bold;
    color: var(--text-main);
    letter-spacing: 2px;
}

.metric-unit {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.metric-desc {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.card-footer {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    border-top: 1px dashed rgba(0, 240, 255, 0.15);
    padding-top: 0.6rem;
    color: var(--text-muted);
}

.tech-tag {
    color: var(--neon-amber);
}

/* Barra de Herramientas */
.table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 0.8rem 1rem;
    background: var(--panel-bg);
    border: 1px solid var(--border-wire);
}

.search-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
    min-width: 250px;
}

.search-prefix {
    color: var(--neon-cyan);
    font-weight: bold;
}

.asrs-search-input {
    width: 100%;
    background: transparent;
    border: none;
    color: var(--text-main);
    font-family: inherit;
    font-size: 0.9rem;
    outline: none;
}

.asrs-search-input::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

.filter-actions {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.filter-group {
    display: flex;
    gap: 0.4rem;
}

.btn-filter {
    background: transparent;
    color: var(--text-muted);
    border: 1px solid rgba(0, 240, 255, 0.2);
    padding: 0.35rem 0.65rem;
    font-family: inherit;
    font-size: 0.8rem;
    cursor: pointer;
    letter-spacing: 1px;
    transition: all 0.2s;
}

.btn-filter:hover, .btn-filter.active {
    color: var(--neon-cyan);
    border-color: var(--neon-cyan);
    background: rgba(0, 240, 255, 0.08);
}

.routes-counter {
    font-size: 0.8rem;
    color: var(--neon-amber);
    font-weight: bold;
    letter-spacing: 1px;
}

/* Tabla Wireframe */
.table-wireframe-wrapper {
    background: var(--panel-bg);
    border: 1px solid var(--border-wire);
    overflow-x: auto;
}

.asrs-wireframe-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
    text-align: left;
}

.asrs-wireframe-table th {
    background: rgba(0, 240, 255, 0.04);
    color: var(--neon-cyan);
    padding: 0.85rem 1rem;
    font-weight: bold;
    letter-spacing: 1px;
    border-bottom: 1px solid var(--border-wire);
    border-right: 1px solid rgba(0, 240, 255, 0.1);
}

.asrs-wireframe-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid rgba(0, 240, 255, 0.08);
    border-right: 1px solid rgba(0, 240, 255, 0.05);
    color: var(--text-main);
    vertical-align: middle;
}

.route-row:hover {
    background: rgba(0, 240, 255, 0.04);
}

.row-highlight-new {
    animation: neonFlash 2s ease-out;
}

@keyframes neonFlash {
    0% { background-color: rgba(0, 240, 255, 0.35); }
    100% { background-color: transparent; }
}

.col-id {
    color: var(--text-muted);
    font-weight: bold;
}

.uri-badge {
    color: var(--neon-cyan);
    font-weight: bold;
    letter-spacing: 0.5px;
}

.app-tag {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border: 1px solid rgba(0, 240, 255, 0.25);
    background: rgba(0, 240, 255, 0.02);
    letter-spacing: 1px;
}

.app-asrsadmin {
    border-color: var(--neon-amber);
    color: var(--neon-amber);
}

.type-badge {
    font-size: 0.75rem;
    font-weight: bold;
    padding: 0.2rem 0.5rem;
    letter-spacing: 1px;
    display: inline-block;
}

.type-public {
    color: var(--neon-cyan);
    border: 1px solid var(--neon-cyan);
    background: rgba(0, 240, 255, 0.05);
}

.type-private {
    color: var(--neon-red);
    border: 1px solid var(--neon-red);
    background: rgba(255, 51, 102, 0.08);
    box-shadow: 0 0 6px rgba(255, 51, 102, 0.2);
}

.roles-code-block {
    background: rgba(0, 0, 0, 0.35);
    border: 1px dashed rgba(255, 183, 3, 0.4);
    color: var(--neon-amber);
    padding: 0.2rem 0.5rem;
    font-family: inherit;
    font-size: 0.8rem;
    display: inline-block;
}

.roles-public-tag {
    color: var(--text-muted);
    font-size: 0.8rem;
}

.text-subtle {
    color: var(--text-muted);
    font-size: 0.8rem;
}

.asrs-btn-mini {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    border: 1px solid var(--border-wire);
    color: var(--neon-cyan);
    text-decoration: none;
    font-size: 0.75rem;
    letter-spacing: 1px;
    transition: all 0.2s;
}

.asrs-btn-mini:hover {
    border-color: var(--neon-cyan);
    background: var(--neon-cyan);
    color: var(--bg-dark);
    box-shadow: 0 0 8px rgba(0, 240, 255, 0.5);
}

.asrs-btn-outline {
    background: transparent;
    color: var(--neon-cyan);
    border: 1px solid var(--neon-cyan);
    padding: 0.65rem 1rem;
    cursor: pointer;
    font-family: inherit;
    font-weight: bold;
    letter-spacing: 1px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.asrs-btn-outline:hover {
    background: var(--neon-cyan);
    color: var(--bg-dark);
    box-shadow: 0 0 12px rgba(0, 240, 255, 0.4);
}

.btn-indicator {
    color: var(--neon-amber);
}

.empty-table-msg {
    text-align: center;
    padding: 2rem !important;
    color: var(--neon-amber);
    letter-spacing: 1px;
}

/* ==========================================================================
   Estilos de la Ventana Modal Flotante (Neon Dark & Wireframe)
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
    max-width: 680px;
    display: flex;
    flex-direction: column;
    animation: slideDownModal 0.25s ease-out;
}

@keyframes slideDownModal {
    from { transform: translateY(-25px); opacity: 0; }
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
    font-size: 1.05rem;
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
    gap: 1rem;
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
    font-size: 0.8rem;
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

.input-prefix-wrapper {
    display: flex;
    align-items: center;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid var(--border-wire);
}

.input-addon {
    padding: 0 0.6rem;
    color: var(--neon-cyan);
    font-weight: bold;
}

.input-prefix-wrapper .asrs-form-input {
    border: none !important;
    background: transparent !important;
    padding-left: 0;
}

.asrs-form-input, .asrs-form-select {
    width: 100%;
    background: rgba(0, 0, 0, 0.45);
    border: 1px solid var(--border-wire);
    color: var(--text-main);
    font-family: inherit;
    font-size: 0.88rem;
    padding: 0.6rem 0.8rem;
    outline: none;
    transition: all 0.2s;
    box-sizing: border-box;
}

.asrs-form-input:focus, .asrs-form-select:focus, .input-prefix-wrapper:focus-within {
    border-color: var(--neon-cyan);
    box-shadow: 0 0 8px rgba(0, 240, 255, 0.35);
}

.asrs-form-select option {
    background-color: var(--panel-bg);
    color: var(--text-main);
}

.form-hint {
    font-size: 0.73rem;
    color: var(--text-muted);
    letter-spacing: 0.5px;
}

.font-mono {
    font-family: monospace;
}

.roles-quick-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    align-items: center;
    margin-top: 0.25rem;
}

.quick-tag-label {
    font-size: 0.72rem;
    color: var(--text-muted);
}

.btn-quick-role {
    background: transparent;
    color: var(--neon-amber);
    border: 1px solid rgba(255, 183, 3, 0.35);
    font-family: inherit;
    font-size: 0.72rem;
    padding: 0.2rem 0.45rem;
    cursor: pointer;
    letter-spacing: 0.5px;
    transition: all 0.2s;
}

.btn-quick-role:hover {
    background: rgba(255, 183, 3, 0.12);
    border-color: var(--neon-amber);
    box-shadow: 0 0 6px rgba(255, 183, 3, 0.3);
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
    font-weight: bold;
    letter-spacing: 1px;
    transition: all 0.2s;
}

.asrs-btn-ghost:hover {
    border-color: var(--text-main);
    color: var(--text-main);
    background: rgba(255, 255, 255, 0.05);
}

.btn-save-route {
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.25);
}
</style>

<!-- Lógica Interactiva: Modal, Filtrado y Comunicación Asíncrona -->
<script>
let currentTypeFilter = 'ALL';

/* ==========================================================================
   Control del Modal de Creación de Rutas
   ========================================================================== */
function openCreateRouteModal() {
    const modal = document.getElementById('modal-create-route');
    const alertBanner = document.getElementById('modal-alert-banner');
    if (modal) {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        if (alertBanner) alertBanner.style.display = 'none';
        
        // Auto-focus en el primer campo
        setTimeout(() => {
            const uriInput = document.getElementById('route-input-uri');
            if (uriInput) uriInput.focus();
        }, 100);
    }
}

function closeCreateRouteModal() {
    const modal = document.getElementById('modal-create-route');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

// Cierre del modal con tecla Escape o clic en el backdrop
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeCreateRouteModal();
});

document.addEventListener('click', (e) => {
    const modal = document.getElementById('modal-create-route');
    if (modal && e.target === modal) {
        closeCreateRouteModal();
    }
});

function handleTypeChange(val) {
    const rolesGroup = document.getElementById('group-roles-input');
    const rolesInput = document.getElementById('route-input-roles');
    if (val === 'public') {
        if (rolesInput && rolesInput.value === '["superadmin"]') {
            rolesInput.value = '[]';
        }
    } else {
        if (rolesInput && (rolesInput.value === '[]' || !rolesInput.value)) {
            rolesInput.value = '["superadmin"]';
        }
    }
}

function setRoleTag(val) {
    const input = document.getElementById('route-input-roles');
    if (input) input.value = val;
}

/* ==========================================================================
   Envío Asíncrono del Formulario mediante Fetch
   ========================================================================== */
async function handleRouteFormSubmit(event) {
    event.preventDefault();

    const submitBtn = document.getElementById('btn-submit-route');
    const alertBanner = document.getElementById('modal-alert-banner');

    const uri = document.getElementById('route-input-uri').value.trim();
    const app = document.getElementById('route-input-app').value.trim();
    const type = document.getElementById('route-input-type').value;
    const content = document.getElementById('route-input-content').value.trim();
    const controller = document.getElementById('route-input-controller').value.trim();
    const method = document.getElementById('route-input-method').value.trim();
    const rolesRaw = document.getElementById('route-input-roles').value.trim();

    // Validación básica de roles JSON
    let rolesArray = [];
    if (rolesRaw) {
        try {
            rolesArray = JSON.parse(rolesRaw);
            if (!Array.isArray(rolesArray)) {
                throw new Error('El valor de roles debe ser un array JSON (ej. ["superadmin"])');
            }
        } catch (err) {
            showAlertModal(alertBanner, '[!] Formato de roles inválido: debe ser un array JSON válido, ej: ["superadmin"]');
            return;
        }
    }

    // Estado visual de carga
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="btn-indicator">⚡</span> [ GUARDANDO EN DISCO... ]';
    }

    const payload = {
        uri: uri,
        app: app,
        type: type,
        content: content,
        controller: controller,
        method: method,
        layout: 'default',
        roles: rolesArray
    };

    const tokenB = sessionStorage.getItem('asrs_validator') || localStorage.getItem('asrs_token_b');
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };
    if (tokenB) headers['Authorization'] = `Bearer ${tokenB}`;

    try {
        const response = await fetch('admin/routes', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (response.ok && data.ok) {
            // 1. Cerrar el modal
            closeCreateRouteModal();

            // 2. Limpiar el formulario
            document.getElementById('form-create-route').reset();
            setRoleTag('["superadmin"]');

            // 3. Notificación tipo consola en la página principal
            showConsoleBanner(`[OK] Ruta '/${data.route.uri}' persistida en BD y caché routes.php recompilada con éxito.`);

            // 4. Actualizar tabla en tiempo real sin recargar página
            insertOrUpdateTableRow(data.route);

            // 5. Actualizar contadores
            if (data.routesCount !== undefined) {
                const countBadge = document.getElementById('count-routes');
                if (countBadge) {
                    countBadge.textContent = String(data.routesCount).padStart(2, '0');
                }
            }
        } else {
            showAlertModal(alertBanner, `[ERROR] ${data.error || 'Fallo al guardar la ruta'}`);
        }
    } catch (err) {
        showAlertModal(alertBanner, '[ERROR] Fallo de conexión perimetral al procesar la ruta.');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="btn-indicator">⚡</span> [ GUARDAR & COMPILAR RUTA ]';
        }
    }
}

function showAlertModal(bannerEl, msg) {
    if (!bannerEl) return;
    bannerEl.className = 'modal-alert error';
    bannerEl.textContent = msg;
    bannerEl.style.display = 'block';
}

function showConsoleBanner(msg) {
    const alertBox = document.getElementById('console-alert');
    const alertMsg = document.getElementById('console-alert-msg');
    const alertIcon = document.getElementById('console-alert-icon');
    if (alertBox && alertMsg) {
        alertBox.className = 'console-alert-banner success';
        if (alertIcon) alertIcon.textContent = '[OK]';
        alertMsg.textContent = msg;
        alertBox.style.display = 'flex';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 5000);
    }
}

function insertOrUpdateTableRow(route) {
    const tbody = document.querySelector('#routes-table tbody');
    if (!tbody) return;

    // Si la tabla contenía el mensaje de vacía, removerlo
    const emptyMsg = tbody.querySelector('.empty-table-msg');
    if (emptyMsg) {
        emptyMsg.closest('tr').remove();
    }

    const isPrivate = (route.type || 'public') === 'private';
    const rolesArr = route.roles_array || [];
    const rolesJson = rolesArr.length > 0 ? JSON.stringify(rolesArr) : '[]';
    const uriDisplay = (route.uri === '') ? '<span class="text-subtle">/ (raíz)</span>' : escapeHtml(route.uri);
    const controllerDisplay = route.controller
        ? escapeHtml(route.controller) + (route.method ? '::' + escapeHtml(route.method) : '')
        : `<span class="text-subtle">[Kernel / ${escapeHtml(route.content || 'vista')}]</span>`;

    // Buscar si ya existe una fila con esa URI y App
    const existingRows = tbody.querySelectorAll('tr.route-row');
    let matchedRow = null;
    existingRows.forEach(r => {
        const uText = r.querySelector('.col-uri')?.textContent.replace(/\[|\]/g, '').trim();
        const aText = r.querySelector('.col-app')?.textContent.trim();
        if (uText === (route.uri || '/') && aText === route.app) {
            matchedRow = r;
        }
    });

    const rowHtml = `
        <td class="col-id">${escapeHtml(String(route.id || '+'))}</td>
        <td class="col-uri">
            <span class="uri-badge">[ ${uriDisplay} ]</span>
        </td>
        <td class="col-app">
            <span class="app-tag app-${escapeHtml(route.app.toLowerCase())}">${escapeHtml(route.app)}</span>
        </td>
        <td class="col-controller">
            ${controllerDisplay}
        </td>
        <td class="col-type">
            <span class="type-badge ${isPrivate ? 'type-private' : 'type-public'}">
                ${escapeHtml((route.type || 'PUBLIC').toUpperCase())}
            </span>
        </td>
        <td class="col-roles">
            ${rolesArr.length > 0
                ? `<code class="roles-code-block">${escapeHtml(rolesJson)}</code>`
                : `<span class="roles-public-tag">[*] PÚBLICO_ABIERTO</span>`}
        </td>
        <td class="col-actions">
            <a href="${escapeHtml(route.uri)}" target="_blank" class="asrs-btn-mini" title="Probar ruta en nueva pestaña">
                [ TEST ↗ ]
            </a>
        </td>
    `;

    if (matchedRow) {
        matchedRow.setAttribute('data-type', (route.type || 'PUBLIC').toUpperCase());
        matchedRow.innerHTML = rowHtml;
        matchedRow.classList.add('row-highlight-new');
        setTimeout(() => matchedRow.classList.remove('row-highlight-new'), 2500);
    } else {
        const newTr = document.createElement('tr');
        newTr.className = 'route-row row-highlight-new';
        newTr.setAttribute('data-type', (route.type || 'PUBLIC').toUpperCase());
        newTr.innerHTML = rowHtml;
        tbody.prepend(newTr);
        setTimeout(() => newTr.classList.remove('row-highlight-new'), 2500);
    }

    // Actualizar conteo visible
    filterRoutesTable();
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ==========================================================================
   Filtros y Búsqueda en Vivo
   ========================================================================== */
function filterRoutesTable() {
    const filterInput = document.getElementById('route-filter-input');
    const query = filterInput ? filterInput.value.toLowerCase() : '';
    const rows = document.querySelectorAll('#routes-table tbody tr.route-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const type = row.getAttribute('data-type') || '';
        const matchesQuery = text.includes(query);
        const matchesType = (currentTypeFilter === 'ALL') || (type === currentTypeFilter);

        if (matchesQuery && matchesType) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const counter = document.getElementById('visible-routes-counter');
    if (counter) counter.textContent = `Total: ${visibleCount} rutas`;
}

function applyTypeFilter(type, buttonEl) {
    currentTypeFilter = type;
    document.querySelectorAll('.btn-filter').forEach(btn => btn.classList.remove('active'));
    if (buttonEl) buttonEl.classList.add('active');
    filterRoutesTable();
}

async function triggerCachePurge() {
    const btn = document.getElementById('btn-purge-cache');
    const alertBox = document.getElementById('console-alert');
    const alertMsg = document.getElementById('console-alert-msg');
    const alertIcon = document.getElementById('console-alert-icon');

    if (btn.disabled) return;
    btn.disabled = true;
    btn.textContent = '[ COMPILANDO... ]';

    const tokenB = sessionStorage.getItem('asrs_validator') || localStorage.getItem('asrs_token_b');
    const headers = { 'Accept': 'application/json' };
    if (tokenB) headers['Authorization'] = `Bearer ${tokenB}`;

    try {
        const res = await fetch('admin/cache/regenerate', {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin'
        });
        const data = await res.json();

        alertBox.style.display = 'flex';
        if (res.ok && data.ok) {
            alertBox.className = 'console-alert-banner success';
            alertIcon.textContent = '[OK]';
            alertMsg.textContent = `Cachés compiladas con éxito: ${data.routes?.count || 0} rutas | ${data.roles?.count || 0} roles | ${data.apps?.count || 0} apps sincronizadas.`;
            setTimeout(() => {
                window.location.reload();
            }, 1200);
        } else {
            alertBox.className = 'console-alert-banner error';
            alertIcon.textContent = '[ERROR]';
            alertMsg.textContent = `Fallo al regenerar cachés: ${data.error || 'Acceso denegado'}`;
            btn.disabled = false;
            btn.textContent = '[ REGENERAR CACHÉS ]';
        }
    } catch (err) {
        alertBox.style.display = 'flex';
        alertBox.className = 'console-alert-banner error';
        alertIcon.textContent = '[ERROR]';
        alertMsg.textContent = 'Fallo de conexión perimetral al recompilar caché.';
        btn.disabled = false;
        btn.textContent = '[ REGENERAR CACHÉS ]';
    }
}
</script>
