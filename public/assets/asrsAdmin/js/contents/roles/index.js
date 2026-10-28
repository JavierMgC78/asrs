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

    function setElementFeedback(el, msg, isError) {
        if (!el) return;
        if (el._fadeTimer) {
            clearTimeout(el._fadeTimer);
            el._fadeTimer = null;
        }
        el.classList.remove('fade-out');
        el.style.opacity = '1';
        el.textContent = msg;
        el.style.color = isError ? 'var(--neon-red)' : 'var(--neon-cyan)';

        // Auto-desvanecimiento tras 4.5 segundos en avisos de éxito
        if (!isError && msg) {
            el._fadeTimer = setTimeout(function () {
                el.classList.add('fade-out');
                el._fadeTimer = setTimeout(function () {
                    el.textContent = '';
                    el.classList.remove('fade-out');
                    el.style.opacity = '1';
                    el._fadeTimer = null;
                }, 500); // 500ms coincide con la transición CSS
            }, 4500);
        }
    }

    function setFeedback(msg, isError) {
        setElementFeedback(feedback, msg, isError);
    }

    function syncRoleInSelect(sel, role, isDelete) {
        if (!sel || !role) return;
        var realId = (role.id !== undefined && role.id !== null && role.id !== '') ? role.id : (role.role_id || role.id_role || role.name);
        if (!realId && realId !== 0) return;
        var idStr = String(realId);

        // Buscar si ya existe la opción por value o por data-name
        var opt = sel.querySelector('option[value="' + idStr + '"]');
        if (!opt && role.name) {
            opt = sel.querySelector('option[data-name="' + role.name + '"]');
        }

        if (!opt) {
            opt = document.createElement('option');
            sel.appendChild(opt);
        }

        // Asignación explícita de value con el ID primario real
        opt.value = realId;
        opt.textContent = role.name;

        opt.setAttribute('data-id', idStr);
        opt.setAttribute('data-name', role.name || '');
        opt.dataset.id = idStr;
        opt.dataset.name = role.name || '';

        if (isDelete) {
            var views = String(role.views_count !== undefined && role.views_count !== null ? role.views_count : 0);
            opt.setAttribute('data-views', views);
            opt.dataset.views = views;
        } else {
            opt.setAttribute('data-display', role.display_name || '');
            opt.setAttribute('data-description', role.description || '');
            opt.setAttribute('data-status', role.status !== undefined ? String(role.status) : '1');
            opt.dataset.display = role.display_name || '';
            opt.dataset.description = role.description || '';
            opt.dataset.status = role.status !== undefined ? String(role.status) : '1';
        }
    }

    function repopulateRoleSelectors(roleOrList) {
        var editSel = document.getElementById('role-edit-select');
        var delSel  = document.getElementById('role-delete-select');
        if (!editSel && !delSel) return;

        // Si se recibe un rol individual recién creado o modificado
        if (roleOrList && !Array.isArray(roleOrList) && typeof roleOrList === 'object') {
            var rId = (roleOrList.id !== undefined && roleOrList.id !== null && roleOrList.id !== '') ? roleOrList.id : (roleOrList.role_id || roleOrList.id_role || roleOrList.name);
            if (rId) {
                syncRoleInSelect(editSel, roleOrList, false);
                syncRoleInSelect(delSel, roleOrList, true);
                return;
            }
        }

        // Si se recibe una lista o se sincronizan desde el catálogo en tabla
        var list = Array.isArray(roleOrList) ? roleOrList : [];
        if (list.length === 0) {
            var rows = document.querySelectorAll('#roles-table-body tr[data-role-id]');
            rows.forEach(function (tr) {
                var id = tr.dataset.roleId;
                if (!id) return;
                var nameCell   = tr.cells[1] ? tr.cells[1].textContent.trim() : '';
                var dispCell   = tr.cells[2] ? tr.cells[2].textContent.trim() : '';
                var descCell   = tr.cells[3] ? tr.cells[3].textContent.trim() : '';
                if (descCell === '—') descCell = '';
                var statPill   = tr.cells[4] ? tr.cells[4].querySelector('.roles-status-pill') : null;
                var status     = (statPill && statPill.classList.contains('is-active')) ? '1' : '0';
                var viewsBadge = tr.cells[5] ? tr.cells[5].querySelector('.roles-views-badge') : null;
                var views      = viewsBadge ? viewsBadge.textContent.trim() : '0';
                list.push({
                    id: id,
                    name: nameCell,
                    display_name: dispCell,
                    description: descCell,
                    status: status,
                    views_count: views
                });
            });
        }

        var prevEdit = editSel ? editSel.value : '';
        var prevDel  = delSel ? delSel.value : '';

        if (editSel) {
            editSel.innerHTML = '<option value="">-- Seleccione un rol --</option>';
            list.forEach(function (r) {
                syncRoleInSelect(editSel, r, false);
            });
            if (prevEdit) editSel.value = prevEdit;
            loadEditFields();
        }

        if (delSel) {
            delSel.innerHTML = '<option value="">-- Seleccione un rol --</option>';
            list.forEach(function (r) {
                syncRoleInSelect(delSel, r, true);
            });
            if (prevDel) delSel.value = prevDel;
            refreshDelState();
        }
    }
    var addEditOption = repopulateRoleSelectors;

    // 1. DEFINIR EL LISTENER: Evento personalizado 'roleCacheUpdated' para repoblar selectores tras regeneración de caché
    document.addEventListener('roleCacheUpdated', function (e) {
        var detail = (e && e.detail) ? e.detail : {};
        if (detail.action === 'delete') {
            repopulateRoleSelectors();
            return;
        }
        var roleOrList = detail.role || detail.roles || detail;
        repopulateRoleSelectors(roleOrList);
    });

    function getMaxTableRoleId() {
        var maxId = 0;
        var rows = document.querySelectorAll('#roles-table-body tr[data-role-id]');
        rows.forEach(function (tr) {
            var trId = parseInt(tr.dataset.roleId || tr.getAttribute('data-role-id') || '0', 10);
            if (!isNaN(trId) && trId > maxId) {
                maxId = trId;
            }
        });
        return maxId;
    }

    function extractRoleIdFromResponse(res, payload) {
        if (!res || typeof res !== 'object') {
            return getMaxTableRoleId() + 1;
        }

        // 1. Inspección exhaustiva de todas las posibles variantes de clave
        var candidates = [
            res.id,
            res.role_id,
            res.id_role,
            res.newId,
            res.new_id,
            res.insert_id,
            res.insertId,
            res.lastInsertId,
            res.last_insert_id,
            res.role ? res.role.id : null,
            res.role ? res.role.role_id : null,
            res.role ? res.role.id_role : null,
            res.data ? res.data.id : null,
            res.data ? res.data.role_id : null,
            res.data ? res.data.id_role : null,
            res.data ? res.data.insert_id : null
        ];

        for (var i = 0; i < candidates.length; i++) {
            var val = candidates[i];
            if (val !== undefined && val !== null && val !== '') {
                var num = Number(val);
                if (!isNaN(num) && num > 0) {
                    return num;
                }
            }
        }

        // 2. Inspección en colecciones (res.roles o res.data)
        var list = Array.isArray(res.roles) ? res.roles : (Array.isArray(res.data) ? res.data : null);
        if (list && list.length > 0) {
            if (payload && payload.name) {
                for (var j = 0; j < list.length; j++) {
                    var item = list[j];
                    if (item && item.name === payload.name) {
                        var itemId = Number(item.id || item.role_id || item.id_role);
                        if (!isNaN(itemId) && itemId > 0) return itemId;
                    }
                }
            }
            var last = list[list.length - 1];
            if (last) {
                var lastId = Number(last.id || last.role_id || last.id_role);
                if (!isNaN(lastId) && lastId > 0) return lastId;
            }
        }

        // 3. Respaldo de seguridad: ID incremental garantizado a partir del catálogo actual
        var fallback = getMaxTableRoleId() + 1;
        return fallback > 0 ? fallback : 1;
    }

    function appendRoleRow(r) {
        var body = document.getElementById('roles-table-body');
        if (!body || !r) return;
        var empty = body.querySelector('.roles-empty');
        if (empty) empty.closest('tr').remove();

        var numId = Number((r && r.id !== undefined && r.id !== null) ? r.id : (r.role_id || r.id_role || 0));
        if (isNaN(numId) || numId <= 0) {
            numId = getMaxTableRoleId() + 1;
        }

        var idBadge = String(numId).padStart(2, '0');
        var realId = numId;
        var active = Number(r.status) === 1;
        var tr = document.createElement('tr');
        tr.dataset.roleId = String(realId);
        tr.innerHTML =
            '<td><span class="roles-id-badge">#' + idBadge + '</span></td>' +
            '<td class="roles-mono">' + esc(r.name) + '</td>' +
            '<td>' + esc(r.display_name) + '</td>' +
            '<td class="roles-desc-cell">' + esc(r.description || '—') + '</td>' +
            '<td><span class="roles-status-pill ' + (active ? 'is-active' : 'is-inactive') + '">' + (active ? '● ACTIVO' : '■ INACTIVO') + '</span></td>' +
            '<td><span class="roles-views-badge">' + Number(r.views_count || 0) + '</span></td>';
        body.appendChild(tr);
    }

    // -------------------------------------------------------------------------
    // Control del Modal de Alta de Rol (ROL-01)
    // -------------------------------------------------------------------------
    function openCreateRoleModal() {
        var modal = document.getElementById('modal-create-role');
        var alertBanner = document.getElementById('modal-role-alert-banner');
        if (modal) {
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            if (alertBanner) {
                alertBanner.style.display = 'none';
                alertBanner.textContent = '';
            }
            var btnOpen = document.getElementById('btn-open-create-role-modal');
            if (btnOpen) btnOpen.setAttribute('aria-expanded', 'true');
            setTimeout(function () {
                var nameInput = document.getElementById('role-create-name');
                if (nameInput) nameInput.focus();
            }, 100);
        }
    }

    function closeCreateRoleModal() {
        var modal = document.getElementById('modal-create-role');
        if (modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            var btnOpen = document.getElementById('btn-open-create-role-modal');
            if (btnOpen) {
                btnOpen.setAttribute('aria-expanded', 'false');
                btnOpen.focus();
            }
        }
    }

    window.openCreateRoleModal = openCreateRoleModal;
    window.closeCreateRoleModal = closeCreateRoleModal;

    var btnOpenRoleModal = document.getElementById('btn-open-create-role-modal');
    if (btnOpenRoleModal) {
        btnOpenRoleModal.addEventListener('click', function (e) {
            e.preventDefault();
            openCreateRoleModal();
        });
    }

    var btnCloseRoleModalX = document.getElementById('btn-close-role-modal-x');
    if (btnCloseRoleModalX) {
        btnCloseRoleModalX.addEventListener('click', function (e) {
            e.preventDefault();
            closeCreateRoleModal();
        });
    }

    var btnCloseRoleModalBtn = document.getElementById('btn-close-role-modal');
    if (btnCloseRoleModalBtn) {
        btnCloseRoleModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeCreateRoleModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCreateRoleModal();
        }
    });

    document.addEventListener('click', function (e) {
        var modal = document.getElementById('modal-create-role');
        if (modal && e.target === modal) {
            closeCreateRoleModal();
        }
    });

    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('btn-role-create');
            var alertBanner = document.getElementById('modal-role-alert-banner');

            var nameInput = createForm.elements['name'];
            var displayInput = createForm.elements['display_name'];
            var descInput = createForm.elements['description'];
            var statusInput = createForm.elements['status'];

            var nameVal = (nameInput ? nameInput.value : '').trim();
            var displayVal = (displayInput ? displayInput.value : '').trim();
            var descVal = descInput ? descInput.value.trim() : '';
            var statusVal = statusInput ? statusInput.value : '1';

            function showModalAlert(msg, isErr) {
                if (alertBanner) {
                    alertBanner.className = 'modal-alert ' + (isErr ? 'error' : 'success');
                    alertBanner.style.display = 'block';
                    alertBanner.textContent = msg;
                }
                setFeedback(msg, isErr);
            }

            // Validaciones locales
            if (!nameVal || !displayVal) {
                showModalAlert('[!] Los campos Nombre y Nombre a mostrar son obligatorios.', true);
                return;
            }

            var payload = {
                name: nameVal,
                display_name: displayVal,
                description: descVal,
                status: statusVal
            };

            btn.disabled = true;
            var originalBtnText = btn.textContent;
            btn.textContent = '[ GUARDANDO... ]';
            if (alertBanner) alertBanner.style.display = 'none';
            setFeedback('Guardando...', false);

            fetch('admin/roles', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(function (res) { return res.json(); })
                .then(function (response) {
                    if (!response || (!response.ok && !response.success)) {
                        showModalAlert('[ERROR] ' + (response && response.error ? response.error : 'No se pudo guardar el rol'), true);
                        return;
                    }

                    // 1. EXTRACCIÓN BLINDADA Y EXHAUSTIVA DEL ID DEL SERVIDOR
                    var realId = extractRoleIdFromResponse(response, payload);

                    var serverRole = response.role || response.data || {};
                    var roleData = {
                        id: realId,
                        name: serverRole.name || payload.name,
                        display_name: serverRole.display_name || payload.display_name,
                        description: serverRole.description !== undefined ? serverRole.description : (payload.description || ''),
                        status: serverRole.status !== undefined ? serverRole.status : payload.status,
                        views_count: serverRole.views_count !== undefined ? Number(serverRole.views_count) : 0
                    };

                    // Pintar fila en el catálogo con el ID real del servidor
                    appendRoleRow(roleData);

                    // Sincronizar selectores con el ID real del servidor
                    repopulateRoleSelectors(roleData);

                    // Disparar el evento con los datos oficiales del servidor
                    document.dispatchEvent(new CustomEvent('roleCacheUpdated', {
                        bubbles: true,
                        detail: {
                            role: roleData,
                            id: roleData.id,
                            cacheOk: response.cacheOk,
                            rolesCount: response.rolesCount
                        }
                    }));

                    var c = response.rolesCount;
                    var dc = document.getElementById('roles-drawer-count');
                    var mc = document.getElementById('roles-meta-count');
                    if (dc && c !== undefined) dc.textContent = '[ ' + c + ' REGISTRO(S) ]';
                    if (mc && c !== undefined) mc.textContent = 'Roles: ' + c;

                    // Limpieza y cierre automático del modal
                    createForm.reset();
                    closeCreateRoleModal();
                    setFeedback('[OK] ' + (response.message || 'Rol creado exitosamente'), false);
                })
                .catch(function (err) {
                    showModalAlert('[ERROR] Fallo de comunicación con el servidor: ' + (err.message || 'Error de red'), true);
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.textContent = originalBtnText;
                });
        });
    }

    // Edición de rol vía Fetch (el identificador interno 'name' es de solo lectura)
    var editForm   = document.getElementById('form-role-edit');
    var editSelect = document.getElementById('role-edit-select');
    var editFeedback = document.getElementById('role-edit-feedback');
    var editFields = editForm ? ['role-edit-display', 'role-edit-description', 'role-edit-status'] : [];

    function setEditFeedback(msg, isError) {
        setElementFeedback(editFeedback, msg, isError);
    }

    function loadEditFields() {
        if (!editSelect) editSelect = document.getElementById('role-edit-select');
        if (!editSelect) return;
        var val = editSelect.value;
        var has = val !== '' && val !== null && val !== undefined;
        var opt = editSelect.selectedIndex >= 0 ? editSelect.options[editSelect.selectedIndex] : null;

        editFields.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.disabled = !has;
        });

        var btn = document.getElementById('btn-role-edit');
        if (btn) btn.disabled = !has;

        var nameEl = document.getElementById('role-edit-name');
        var dispEl = document.getElementById('role-edit-display');
        var descEl = document.getElementById('role-edit-description');
        var statEl = document.getElementById('role-edit-status');

        if (nameEl) nameEl.value = (has && opt && opt.dataset) ? (opt.dataset.name || '') : '';
        if (dispEl) dispEl.value = (has && opt && opt.dataset) ? (opt.dataset.display || '') : '';
        if (descEl) descEl.value = (has && opt && opt.dataset) ? (opt.dataset.description || '') : '';
        if (statEl) statEl.value = (has && opt && opt.dataset) ? (opt.dataset.status !== undefined ? opt.dataset.status : '1') : '1';
    }

    if (editForm && editSelect) {
        editSelect.addEventListener('change', function () {
            setEditFeedback('', false);
            loadEditFields();
        });
        editSelect.addEventListener('input', function () {
            setEditFeedback('', false);
            loadEditFields();
        });

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
                    // Actualiza la opción del selector y dataset
                    syncRoleInSelect(editSelect, role, false);
                    syncRoleInSelect(delSelect, role, true);

                    // Actualiza la fila del listado sin recargar
                    var tr = document.querySelector('#roles-table-body tr[data-role-id="' + role.id + '"]');
                    if (tr && tr.cells.length >= 6) {
                        var active = Number(role.status) === 1;
                        tr.cells[2].textContent = role.display_name;
                        tr.cells[3].textContent = role.description || '—';
                        tr.cells[4].innerHTML = '<span class="roles-status-pill ' + (active ? 'is-active' : 'is-inactive') + '">' + (active ? '● ACTIVO' : '■ INACTIVO') + '</span>';
                    }

                    document.dispatchEvent(new CustomEvent('roleCacheUpdated', {
                        bubbles: true,
                        detail: { role: role, cacheOk: r.data.cacheOk }
                    }));

                    setEditFeedback('[OK] ' + r.data.message + (r.data.cacheOk ? '' : ' (advertencia: la caché no pudo recompilarse)'), !r.data.cacheOk);
                })
                .catch(function () { setEditFeedback('[ERROR] Fallo de comunicación con el servidor', true); })
                .finally(function () { btn.disabled = !editSelect.value; });
        });
    }

    // Eliminación de rol vía Fetch (el backend bloquea si hay vistas o usuarios asignados)
    var delForm     = document.getElementById('form-role-delete');
    var delSelect   = document.getElementById('role-delete-select');
    var delFeedback = document.getElementById('role-delete-feedback');
    var delViewsEl  = document.getElementById('role-delete-views-count');
    var delBtn      = document.getElementById('btn-role-delete');

    function setDelFeedback(msg, isError) {
        setElementFeedback(delFeedback, msg, isError);
    }

    function refreshDelState() {
        if (!delSelect) delSelect = document.getElementById('role-delete-select');
        if (!delBtn) delBtn = document.getElementById('btn-role-delete');
        if (!delViewsEl) delViewsEl = document.getElementById('role-delete-views-count');
        if (!delSelect) return;
        var val = delSelect.value;
        var has = val !== '' && val !== null && val !== undefined;
        if (!has) {
            if (delBtn) delBtn.disabled = true;
            if (delViewsEl) {
                delViewsEl.textContent = '— (pendiente de validación)';
                delViewsEl.style.color = 'var(--neon-amber)';
            }
            return;
        }

        var opt = delSelect.options[delSelect.selectedIndex];
        var n = Number(opt && opt.dataset ? (opt.dataset.views || 0) : 0);
        if (delViewsEl) {
            delViewsEl.textContent = n > 0 ? n + ' vista(s) — eliminación bloqueada' : '0 — libre de asignaciones';
            delViewsEl.style.color = n > 0 ? 'var(--neon-red)' : 'var(--neon-cyan)';
        }
        if (delBtn) {
            delBtn.disabled = (n > 0);
        }
    }

    if (delForm && delSelect) {
        delSelect.addEventListener('change', function (e) {
            setDelFeedback('', false);
            refreshDelState();
        });
        delSelect.addEventListener('input', function (e) {
            setDelFeedback('', false);
            refreshDelState();
        });

        // Vinculación delegada para garantizar reconocimiento en tiempo real de nodos dinámicos
        document.addEventListener('change', function (e) {
            if (!e.target) return;
            if (e.target.id === 'role-edit-select') {
                setEditFeedback('', false);
                loadEditFields();
            } else if (e.target.id === 'role-delete-select') {
                setDelFeedback('', false);
                refreshDelState();
            }
        });

        document.addEventListener('input', function (e) {
            if (!e.target) return;
            if (e.target.id === 'role-edit-select') {
                setEditFeedback('', false);
                loadEditFields();
            } else if (e.target.id === 'role-delete-select') {
                setDelFeedback('', false);
                refreshDelState();
            }
        });

        delForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!delSelect.value) return;
            var roleName = delSelect.options[delSelect.selectedIndex].dataset.name;
            if (!window.confirm("¿Eliminar definitivamente el rol '" + roleName + "'?")) return;
            delBtn.disabled = true;
            setDelFeedback('Eliminando...', false);

            fetch('admin/roles', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'delete', role_id: delSelect.value })
            })
                .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                .then(function (r) {
                    if (!r.ok || !r.data.ok) {
                        setDelFeedback('[BLOQUEADO] ' + (r.data.error || 'No se pudo eliminar el rol'), true);
                        return;
                    }
                    var id = String(r.data.roleId);
                    // Quita la fila y las opciones de ambos selectores
                    var tr = document.querySelector('#roles-table-body tr[data-role-id="' + id + '"]');
                    if (tr) tr.remove();
                    [delSelect, editSelect].forEach(function (sel) {
                        if (!sel) return;
                        var o = sel.querySelector('option[value="' + id + '"]');
                        if (o) o.remove();
                        sel.value = '';
                    });
                    if (editSelect) loadEditFields();
                    var body = document.getElementById('roles-table-body');
                    if (body && !body.querySelector('tr')) {
                        body.innerHTML = '<tr><td colspan="6" class="roles-empty">[ NO SE ENCONTRARON ROLES REGISTRADOS ]</td></tr>';
                    }
                    var c = r.data.rolesCount;
                    var dc = document.getElementById('roles-drawer-count');
                    var mc = document.getElementById('roles-meta-count');
                    if (dc) dc.textContent = '[ ' + c + ' REGISTRO(S) ]';
                    if (mc) mc.textContent = 'Roles: ' + c;

                    document.dispatchEvent(new CustomEvent('roleCacheUpdated', {
                        bubbles: true,
                        detail: { action: 'delete', roleId: id, rolesCount: c, cacheOk: r.data.cacheOk }
                    }));

                    setDelFeedback('[OK] ' + r.data.message + (r.data.cacheOk ? '' : ' (advertencia: la caché no pudo recompilarse)'), !r.data.cacheOk);
                })
                .catch(function () { setDelFeedback('[ERROR] Fallo de comunicación con el servidor', true); })
                .finally(function () { refreshDelState(); });
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
