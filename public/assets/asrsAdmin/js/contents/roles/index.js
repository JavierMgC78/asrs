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
        tr.dataset.roleId = r.id;
        body.appendChild(tr);
        addEditOption(r);
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

    // Edición de rol vía Fetch (el identificador interno 'name' es de solo lectura)
    var editForm   = document.getElementById('form-role-edit');
    var editSelect = document.getElementById('role-edit-select');
    var editFeedback = document.getElementById('role-edit-feedback');
    var editFields = editForm ? ['role-edit-display', 'role-edit-description', 'role-edit-status'] : [];

    function setEditFeedback(msg, isError) {
        if (!editFeedback) return;
        editFeedback.textContent = msg;
        editFeedback.style.color = isError ? 'var(--neon-red)' : 'var(--neon-cyan)';
    }

    function addEditOption(r) {
        if (!editSelect) return;
        var opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = r.name;
        opt.dataset.name = r.name;
        opt.dataset.display = r.display_name;
        opt.dataset.description = r.description || '';
        opt.dataset.status = r.status;
        editSelect.appendChild(opt);
    }

    function loadEditFields() {
        var opt = editSelect.options[editSelect.selectedIndex];
        var has = !!editSelect.value;
        editFields.forEach(function (id) { document.getElementById(id).disabled = !has; });
        document.getElementById('btn-role-edit').disabled = !has;
        document.getElementById('role-edit-name').value = has ? opt.dataset.name : '';
        document.getElementById('role-edit-display').value = has ? opt.dataset.display : '';
        document.getElementById('role-edit-description').value = has ? opt.dataset.description : '';
        document.getElementById('role-edit-status').value = has ? opt.dataset.status : '1';
    }

    if (editForm && editSelect) {
        editSelect.addEventListener('change', function () { setEditFeedback('', false); loadEditFields(); });

        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!editSelect.value) return;
            var btn = document.getElementById('btn-role-edit');
            btn.disabled = true;
            setEditFeedback('Guardando...', false);

            fetch('admin/roles', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    action: 'update',
                    role_id: editSelect.value,
                    display_name: document.getElementById('role-edit-display').value,
                    description: document.getElementById('role-edit-description').value,
                    status: document.getElementById('role-edit-status').value
                })
            })
                .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                .then(function (r) {
                    if (!r.ok || !r.data.ok) {
                        setEditFeedback('[ERROR] ' + (r.data.error || 'No se pudo actualizar el rol'), true);
                        return;
                    }
                    var role = r.data.role;
                    // Actualiza la opción del selector
                    var opt = editSelect.options[editSelect.selectedIndex];
                    opt.dataset.display = role.display_name;
                    opt.dataset.description = role.description || '';
                    opt.dataset.status = role.status;
                    // Actualiza la fila del listado sin recargar
                    var tr = document.querySelector('#roles-table-body tr[data-role-id="' + role.id + '"]');
                    if (tr && tr.cells.length >= 6) {
                        var active = Number(role.status) === 1;
                        tr.cells[2].textContent = role.display_name;
                        tr.cells[3].textContent = role.description || '—';
                        tr.cells[4].innerHTML = '<span class="roles-status-pill ' + (active ? 'is-active' : 'is-inactive') + '">' + (active ? '● ACTIVO' : '■ INACTIVO') + '</span>';
                    }
                    setEditFeedback('[OK] ' + r.data.message + (r.data.cacheOk ? '' : ' (advertencia: la caché no pudo recompilarse)'), !r.data.cacheOk);
                })
                .catch(function () { setEditFeedback('[ERROR] Fallo de comunicación con el servidor', true); })
                .finally(function () { btn.disabled = !editSelect.value; });
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
