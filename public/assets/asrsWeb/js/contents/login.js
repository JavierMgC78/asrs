/**
 * ASRS Kernel - Interacción Vista Acceder (Login)
 * Gestiona el colapso de secciones y el envío seguro de credenciales.
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log("[ASRS Login] Módulo de autenticación perimétrica listo.");

    // Los botones de colapso ya funcionan de forma global si se carga main.js, 
    // pero aseguramos la lógica específica de la vista si es necesario:
    const collapseButtons = document.querySelectorAll('.asrs-section-bar .asrs-collapse-btn');

    collapseButtons.forEach(button => {
        // Evitar duplicar listeners si el layout global ya los gestiona
        if (button.dataset.initialized) return;
        button.dataset.initialized = "true";

        button.addEventListener('click', (event) => {
            event.preventDefault();
            const section = button.closest('.asrs-section');
            if (!section) return;

            const isCollapsed = section.classList.toggle('collapsed');
            button.setAttribute('aria-expanded', !isCollapsed);
        });
    });

    // Envío de credenciales (Split Token: Parte A llega como cookie HttpOnly, Parte B en el JSON)
    const form = document.getElementById('asrsLoginForm');
    const errorBox = document.getElementById('asrsLoginError');
    if (!form) return;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        errorBox.style.display = 'none';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin'
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                errorBox.textContent = data.error || 'No fue posible iniciar sesión.';
                errorBox.style.color = data.denied ? '#ffb703' : '#ff6b6b';
                errorBox.style.border = data.denied ? '1px solid #ffb703' : 'none';
                errorBox.style.padding = data.denied ? '10px' : '0';
                errorBox.style.display = 'block';
                return;
            }

            // Parte B: sessionStorage (para llamadas fetch con Authorization) y cookie
            // SameSite=Strict para que las navegaciones del navegador la adjunten.
            sessionStorage.setItem('asrs_validator', data.validator);
            const secure = location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = `asrs_val=${data.validator}; path=/; expires=${new Date(data.expires * 1000).toUTCString()}; SameSite=Strict${secure}`;

            window.location.href = data.redirect;
        } catch (err) {
            errorBox.textContent = 'Error de conexión con el servidor.';
            errorBox.style.display = 'block';
        }
    });
});