/* ==========================================================================
   ASRS Admin - Lógica de Interfaz, Navegación Acordeón y Control de Sesión
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicialización de los menús colapsables tipo acordeón del sidebar
    initSidebarAccordions();

    // 2. Control de cierre de sesión perimetral (Split Token)
    initLogout();
});

/**
 * Gestiona el comportamiento interactivo del sidebar colapsable.
 * Alterna visibilidad y sincroniza los indicadores de consola ([+] / [-]).
 */
function initSidebarAccordions() {
    const accordionToggles = document.querySelectorAll('.nav-accordion-toggle');

    accordionToggles.forEach(toggle => {
        const group = toggle.closest('.sidebar-nav-group');
        const icon = toggle.querySelector('.accordion-icon');
        const hasActiveSub = group && group.querySelector('.nav-link.active');

        // Si el grupo contiene el enlace activo actual, iniciarlo desplegado
        if (hasActiveSub && group) {
            group.classList.add('open');
            toggle.setAttribute('aria-expanded', 'true');
            if (icon) icon.textContent = '[-]';
        }

        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            if (!group) return;

            const isOpen = group.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (icon) {
                icon.textContent = isOpen ? '[-]' : '[+]';
            }
        });
    });
}

/**
 * Control del flujo de logout con Split Token.
 */
function initLogout() {
    const btnLogout = document.getElementById('btn-logout');
    
    if (btnLogout) {
        btnLogout.addEventListener('click', async () => {
            if (confirm('[SISTEMA ASRS] ¿Desea cerrar la sesión perimetral activa?')) {
                try {
                    // Petición al endpoint de logout (borra el token en BD y las cookies)
                    await fetch('auth/logout', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    });
                } catch (error) {
                    console.error('[ERROR] Fallo al procesar el cierre de sesión:', error);
                }

                // Limpieza de la Parte B del Split Token en el cliente
                sessionStorage.removeItem('asrs_validator');
                document.cookie = 'asrs_val=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';

                // Redirección al login público (relativa al <base href> del framework)
                window.location.href = 'asrsweb/login';
            }
        });
    }
}