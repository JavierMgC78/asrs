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
    // Alta de rol vía Fetch
    var createForm = document.getElementById('form-role-create');
    var feedback   = document.getElementById('role-create-feedback');

    function esc(s) {
        var d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
    }

    function setFeedback(msg, isError) {
        if (!feedback) return;
        feedback.textContent = msg;
        feedback.style.color = isError ? 'var(--neon-red)' : 'var(--neon-cyan)';
    }

    function appendRoleRow(r) {
        var body = document.getElementById('roles-table-body');
        if (!body) return;
        var empty = body.querySelector('.roles-empty');
        if (empty) empty.closest('tr').remove();
        var active = Number(r.status) === 1;
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><span class="roles-id-badge">#' + String(r.id).padStart(2, '0') + '</span></td>' +
            '<td class="roles-mono">' + esc(r.name) + '</td>' +
            '<td>' + esc(r.display_name) + '</td>' +
            '<td class="roles-desc-cell">' + esc(r.description || '—') + '</td>' +
            '<td><span class="roles-status-pill ' + (active ? 'is-active' : 'is-inactive') + '">' + (active ? '● ACTIVO' : '■ INACTIVO') + '</span></td>' +
            '<td><span class="roles-views-badge">' + Number(r.views_count || 0) + '</span></td>';
        body.appendChild(tr);
    }

    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('btn-role-create');
            var payload = {
                name: createForm.elements['name'].value,
                display_name: createForm.elements['display_name'].value,
                description: createForm.elements['description'].value,
                status: createForm.elements['status'].value
            };
            btn.disabled = true;
            setFeedback('Guardando...', false);

            fetch('admin/roles', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                .then(function (r) {
                    if (!r.ok || !r.data.ok) {
                        setFeedback('[ERROR] ' + (r.data.error || 'No se pudo guardar el rol'), true);
                        return;
                    }
                    appendRoleRow(r.data.role);
                    var c = r.data.rolesCount;
                    var dc = document.getElementById('roles-drawer-count');
                    var mc = document.getElementById('roles-meta-count');
                    if (dc) dc.textContent = '[ ' + c + ' REGISTRO(S) ]';
                    if (mc) mc.textContent = 'Roles: ' + c;
                    createForm.reset();
                    setFeedback('[OK] ' + r.data.message, false);
                })
                .catch(function () { setFeedback('[ERROR] Fallo de comunicación con el servidor', true); })
                .finally(function () { btn.disabled = false; });
        });
    }

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
