/**
 * Gestor de Apps — conmutador de aplicación predeterminada.
 * Envía por Fetch el app_id a 'admin/apps' y actualiza la interfaz sin recargar.
 */
(function () {
    'use strict';

    var grid = document.getElementById('apps-cards-grid');
    if (!grid) return;

    var banner = document.getElementById('apps-feedback-banner');
    var defaultNameEl = document.getElementById('apps-default-name');
    var bannerTimer = null;

    function showBanner(msg, isError) {
        if (!banner) return;
        if (bannerTimer) clearTimeout(bannerTimer);
        banner.className = 'apps-feedback ' + (isError ? 'error' : 'success');
        banner.style.display = 'block';
        banner.textContent = msg;
        if (!isError) {
            bannerTimer = setTimeout(function () {
                banner.classList.add('fade-out');
                bannerTimer = setTimeout(function () {
                    banner.style.display = 'none';
                    banner.classList.remove('fade-out');
                }, 400);
            }, 4500);
        }
    }

    /** Refleja en la interfaz cuál tarjeta es la predeterminada. */
    function applyDefault(defaultId) {
        grid.querySelectorAll('.app-card').forEach(function (card) {
            var isDef = card.dataset.appId === String(defaultId);
            card.classList.toggle('is-default', isDef);
            var input = card.querySelector('.apps-switch-input');
            if (input) input.checked = isDef;
            var badge = card.querySelector('[data-default-badge]');
            if (badge) badge.hidden = !isDef;
            if (isDef && defaultNameEl) defaultNameEl.textContent = card.dataset.appName || '—';
        });
    }

    /** Restaura el estado previo ante error: la predeterminada vigente sigue marcada. */
    function currentDefaultId() {
        var cur = grid.querySelector('.app-card.is-default');
        return cur ? cur.dataset.appId : null;
    }

    grid.addEventListener('change', function (e) {
        var input = e.target;
        if (!input.classList || !input.classList.contains('apps-switch-input')) return;

        var card = input.closest('.app-card');
        var appId = input.dataset.appId;
        var previousId = currentDefaultId();

        // No se permite quedarse sin app predeterminada: apagar el switch vigente se revierte
        if (!input.checked) {
            input.checked = true;
            showBanner('[!] Debe existir una aplicación predeterminada. Active otra app para cambiarla.', true);
            return;
        }

        card.classList.add('is-saving');
        showBanner('Guardando...', false);

        fetch('admin/apps', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ app_id: Number(appId) })
        })
            .then(function (res) {
                return res.json().then(function (data) { return { ok: res.ok, data: data }; });
            })
            .then(function (r) {
                if (!r.ok || !r.data || !r.data.ok) {
                    throw new Error((r.data && r.data.error) || 'No se pudo establecer la app predeterminada');
                }
                applyDefault(r.data.defaultId || appId);
                showBanner('[OK] ' + r.data.message + (r.data.cacheOk ? '' : ' (advertencia: la caché no pudo recompilarse)'), !r.data.cacheOk);
            })
            .catch(function (err) {
                // Revertir a la predeterminada anterior
                if (previousId) applyDefault(previousId); else input.checked = false;
                showBanner('[ERROR] ' + (err.message || 'Fallo de comunicación con el servidor'), true);
            })
            .finally(function () {
                card.classList.remove('is-saving');
            });
    });
})();
