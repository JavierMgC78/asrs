<div class="asrs-dashboard-container">
    <!-- Encabezado de la Sección -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">[ DASHBOARD GENERAL / NÚCLEO ASRS ]</h1>
        <p class="dashboard-subtitle">Monitoreo en tiempo real del motor, cachés y protocolos de seguridad.</p>
    </div>

    <!-- Grid de Tarjetas Informativas ASRS -->
    <div class="metrics-grid">
        <!-- Tarjeta 1: Estado de Enrutamiento O(1) -->
        <div class="metric-card">
            <div class="card-header">
                <span class="card-label">CATÁLOGO DE RUTAS</span>
                <span class="status-indicator active"></span>
            </div>
            <div class="card-body">
                <span class="metric-value" id="routes-count">--</span>
                <span class="metric-desc">Rutas compiladas en caché O(1)</span>
            </div>
            <div class="card-footer">
                <span class="tech-tag">CacheRoutesBuilder.php</span>
            </div>
        </div>

        <!-- Tarjeta 2: Roles y Permisos -->
        <div class="metric-card">
            <div class="card-header">
                <span class="card-label">CONTROL DE ROLES (ACL)</span>
                <span class="status-indicator active"></span>
            </div>
            <div class="card-body">
                <span class="metric-value" id="roles-count">--</span>
                <span class="metric-desc">Roles institucionales activos</span>
            </div>
            <div class="card-footer">
                <span class="tech-tag">CacheRolesBuilder.php</span>
            </div>
        </div>

        <!-- Tarjeta 3: Seguridad Perimetral -->
        <div class="metric-card">
            <div class="card-header">
                <span class="card-label">AUTENTICACIÓN</span>
                <span class="status-indicator secure"></span>
            </div>
            <div class="card-body">
                <span class="metric-value">SPLIT TOKEN</span>
                <span class="metric-desc">HttpOnly Cookie + Header / LocalStorage</span>
            </div>
            <div class="card-footer">
                <span class="tech-tag">Security.php (Active)</span>
            </div>
        </div>

        <!-- Tarjeta 4: Integridad del Kernel -->
        <div class="metric-card">
            <div class="card-header">
                <span class="card-label">FRONT CONTROLLER</span>
                <span class="status-indicator active"></span>
            </div>
            <div class="card-body">
                <span class="metric-value">PHP 8.1+</span>
                <span class="metric-desc">Ciclo de vida optimizado por Kernel</span>
            </div>
            <div class="card-footer">
                <span class="tech-tag">ASRS Engine v1.0</span>
            </div>
        </div>
    </div>

    <!-- Sección de Actividad o Log del Sistema -->
    <div class="system-log-section">
        <div class="log-header">
            <span>[ REGISTRO DE ESTADO DEL SISTEMA ]</span>
            <button id="btn-refresh-cache" class="asrs-btn-outline">REGENERAR CACHÉS</button>
        </div>
        <div class="log-terminal" id="log-terminal-output">
            <p>[INFO] Inicializando Kernel ASRS...</p>
            <p>[OK] Verificando integridad de archivos de caché en /asrsCore/Cache/...</p>
            <p>[SECURE] Conexión de base de datos Singleton PDO establecida correctamente.</p>
            <p>[READY] Sistema operativo y listo para despacho de peticiones.</p>
        </div>
    </div>
</div>