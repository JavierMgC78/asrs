/**
 * Gestor de Roles — vista estática.
 * Sin lógica de backend ni guardado: los botones solo informan que el módulo es estático.
 */
(function () {
    'use strict';

    document.querySelectorAll('.asrs-roles-management-container .roles-btn-card[data-static="true"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            // Retroalimentación visual breve sin persistir nada
            var original = btn.textContent;
            btn.textContent = '[ MODO ESTÁTICO // SIN ACCIÓN ]';
            btn.disabled = true;
            setTimeout(function () {
                btn.textContent = original;
                btn.disabled = false;
            }, 1400);
        });
    });
    // Despliegue / cierre del catálogo de roles
    var toggleBtn = document.getElementById('btn-toggle-roles-catalog');
    var closeBtn  = document.getElementById('btn-close-roles-catalog');
    var drawer    = document.getElementById('roles-catalog-drawer');

    function setCatalog(open) {
        if (!drawer || !toggleBtn) return;
        drawer.classList.toggle('open', open);
        drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        toggleBtn.classList.toggle('is-open', open);
        toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        var label = toggleBtn.querySelector('.btn-catalog-text');
        if (label) label.textContent = open ? '[ OCULTAR CATÁLOGO ]' : '[ CONSULTAR CATÁLOGO ]';
        if (open) {
            setTimeout(function () { drawer.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }, 200);
        }
    }

    if (toggleBtn) toggleBtn.addEventListener('click', function () { setCatalog(!drawer.classList.contains('open')); });
    if (closeBtn) closeBtn.addEventListener('click', function () { setCatalog(false); });
})();
