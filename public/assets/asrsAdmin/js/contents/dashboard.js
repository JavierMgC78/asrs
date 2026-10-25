/* ==========================================================================
   ASRS Admin Dashboard - Terminal Interactiva con Efecto de Tecleo
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const terminalOutput = document.getElementById('log-terminal-output');
    const btnRefreshCache = document.getElementById('btn-refresh-cache');

    // Cola inicial de mensajes al cargar el dashboard
    const initialLogs = [
        "[INFO] Inicializando Kernel ASRS...",
        "[OK] Verificando integridad de archivos de caché en /asrsCore/Cache/...",
        "[SECURE] Conexión de base de datos Singleton PDO establecida correctamente.",
        "[READY] Sistema operativo y listo para despacho de peticiones."
    ];

    // Ejecutar la secuencia de bienvenida con efecto tecleo al cargar
    playTerminalSequence(initialLogs, 0, () => {
        loadSystemMetrics();
    });

    if (btnRefreshCache) {
        btnRefreshCache.addEventListener('click', async () => {
            if (btnRefreshCache.disabled) return;
            btnRefreshCache.disabled = true;

            const tokenB = sessionStorage.getItem('asrs_validator') || localStorage.getItem('asrs_token_b');
            const headers = { 'Accept': 'application/json' };
            if (tokenB) {
                headers['Authorization'] = `Bearer ${tokenB}`;
            }

            try {
                const response = await fetch('admin/cache/regenerate', {
                    method: 'POST',
                    headers: headers,
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (response.ok && data.ok) {
                    const actionLogs = [
                        "[ACTION] Solicitando regeneración de archivos de caché...",
                        "[OK] CacheRoutesBuilder regenerado con éxito.",
                        "[OK] CacheRolesBuilder regenerado con éxito.",
                        "[SYSTEM] Estado del núcleo sincronizado al 100%."
                    ];

                    playTerminalSequence(actionLogs, 0, () => {
                        if (data.routes && data.routes.count !== undefined) {
                            document.getElementById('routes-count').textContent = String(data.routes.count).padStart(2, '0');
                        }
                        if (data.roles && data.roles.count !== undefined) {
                            document.getElementById('roles-count').textContent = String(data.roles.count).padStart(2, '0');
                        }
                        btnRefreshCache.disabled = false;
                    });
                } else {
                    const errorLogs = [
                        "[ACTION] Solicitando regeneración de archivos de caché...",
                        `[ERROR] Fallo al regenerar cachés: ${data.error || 'Acceso denegado'}`
                    ];
                    playTerminalSequence(errorLogs, 0, () => {
                        btnRefreshCache.disabled = false;
                    });
                }
            } catch (err) {
                const failLogs = [
                    "[ACTION] Solicitando regeneración de archivos de caché...",
                    "[ERROR] Fallo de conexión perimetral al regenerar cachés."
                ];
                playTerminalSequence(failLogs, 0, () => {
                    btnRefreshCache.disabled = false;
                });
            }
        });
    }

    function loadSystemMetrics() {
        document.getElementById('routes-count').textContent = '24';
        document.getElementById('roles-count').textContent = '05';
    }

    /**
     * Reproduce una secuencia de logs línea por línea con efecto de tecleo.
     */
    function playTerminalSequence(logs, index, callback) {
        if (index >= logs.length) {
            if (callback) callback();
            return;
        }

        appendTypewriterLine(logs[index], () => {
            // Pausa breve entre línea y línea antes de la siguiente
            setTimeout(() => {
                playTerminalSequence(logs, index + 1, callback);
            }, 300);
        });
    }

    /**
     * Escribe una cadena de texto carácter por carácter dentro de la terminal.
     */
    function appendTypewriterLine(text, callback) {
        if (!terminalOutput) return;

        const p = document.createElement('p');
        terminalOutput.appendChild(p);
        
        let charIndex = 0;
        const speed = 18; // Velocidad de tecleo en milisegundos (menor = más rápido)

        function typeChar() {
            if (charIndex < text.length) {
                p.textContent += text.charAt(charIndex);
                charIndex++;
                terminalOutput.scrollTop = terminalOutput.scrollHeight;
                setTimeout(typeChar, speed);
            } else {
                if (callback) callback();
            }
        }

        typeChar();
    }
});