<?php
use asrsCore\apps\asrsAdmin\Controllers\AdminController;

// Control de acceso y carga perimetral para superadmin
$adminController = new AdminController();
$viewData = $adminController->usersIndex();
$currentUser = $viewData['currentUser'] ?? null;
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

    <!-- 2. Grid de las 4 Tarjetas Wireframe / Monospaced -->
    <div class="users-cards-grid">
        <!-- Tarjeta 1: Ver lista de usuarios -->
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
                <button type="button" class="asrs-btn-card btn-cyan disabled" disabled aria-disabled="true">
                    [ CONSULTAR CATÁLOGO ]
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
                <button type="button" class="asrs-btn-card btn-cyan disabled" disabled aria-disabled="true">
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

    <!-- 3. Terminal de Información de Estado -->
    <div class="security-notice-panel">
        <div class="notice-header">
            <span class="notice-icon">[!]</span>
            <span class="notice-title">AVISO DE ARQUITECTURA PERIMETRAL</span>
        </div>
        <p class="notice-body">
            Este módulo se encuentra configurado en modo estático para verificación de interfaz y modelado visual. Las tarjetas representan los vectores de comando disponibles exclusivamente para el superadministrador del núcleo ASRS. Los accionadores funcionales permanecen deshabilitados temporalmente por protocolo de aislamiento estricto.
        </p>
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

/* Grid de Tarjetas */
.users-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.user-action-card {
    background-color: var(--panel-bg);
    border: 1px solid var(--border-wire);
    padding: 1.4rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 1.25rem;
    transition: all 0.25s ease;
    position: relative;
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
    padding-bottom: 0.6rem;
}

.card-code {
    font-size: 0.75rem;
    letter-spacing: 1.5px;
    font-weight: bold;
    color: var(--text-muted);
}

.card-status {
    font-size: 0.75rem;
    letter-spacing: 1px;
    font-weight: bold;
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
    gap: 0.65rem;
}

.card-title {
    font-size: 1.15rem;
    font-weight: bold;
    letter-spacing: 1px;
    color: var(--text-main);
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
    font-size: 0.85rem;
    color: var(--text-muted);
    line-height: 1.5;
    letter-spacing: 0.3px;
    margin: 0;
}

.card-bottom {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    border-top: 1px dashed rgba(255, 255, 255, 0.08);
    padding-top: 0.85rem;
}

.card-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.72rem;
    color: var(--text-muted);
    letter-spacing: 0.5px;
}

.meta-item {
    background: rgba(0, 0, 0, 0.35);
    padding: 0.2rem 0.45rem;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.asrs-btn-card {
    width: 100%;
    padding: 0.65rem 1rem;
    background: transparent;
    font-family: inherit;
    font-size: 0.8rem;
    font-weight: bold;
    letter-spacing: 1.5px;
    cursor: not-allowed;
    text-align: center;
    border: 1px solid transparent;
    transition: all 0.2s;
    opacity: 0.65;
}

.asrs-btn-card.btn-cyan {
    border-color: rgba(0, 240, 255, 0.4);
    color: var(--neon-cyan);
}

.asrs-btn-card.btn-amber {
    border-color: rgba(255, 183, 3, 0.4);
    color: var(--neon-amber);
}

.asrs-btn-card.btn-red {
    border-color: rgba(255, 51, 102, 0.4);
    color: var(--neon-red);
}

.asrs-btn-card.disabled:hover {
    opacity: 0.85;
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
</style>
