/* ==========================================================================
   ASRS Admin - Lógica de Interfaz y Control de Sesión (Split Token)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
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
});