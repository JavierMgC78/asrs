/* ==========================================================================
   ASRS Admin - Interfaz de Gestión de Rutas y Modal Flotante
   Estética Neon Dark & Wireframe Outline UI
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // Escucha global para el botón de apertura del modal de rutas si está presente
    const btnOpenModal = document.getElementById('btn-open-create-modal');
    if (btnOpenModal) {
        btnOpenModal.addEventListener('click', openCreateRouteModal);
    }

    // Escucha para el botón de apertura del modal de aprovisionamiento de usuario (USR-02)
    const btnOpenUserModal = document.getElementById('btn-open-create-user-modal');
    if (btnOpenUserModal) {
        btnOpenUserModal.addEventListener('click', openCreateUserModal);
    }

    // Escucha para el formulario de creación de ruta
    const formRoute = document.getElementById('form-create-route');
    if (formRoute) {
        formRoute.addEventListener('submit', handleRouteFormSubmit);
    }

    // Escucha para el formulario de aprovisionamiento de usuario (USR-02)
    const formUser = document.getElementById('form-create-user');
    if (formUser) {
        formUser.addEventListener('submit', handleUserFormSubmit);
    }

    // Cierre con Escape o clic en el backdrop
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCreateRouteModal();
            closeCreateUserModal();
        }
    });

    document.addEventListener('click', (e) => {
        const modal = document.getElementById('modal-create-route');
        if (modal && e.target === modal) {
            closeCreateRouteModal();
        }
        const modalUser = document.getElementById('modal-create-user');
        if (modalUser && e.target === modalUser) {
            closeCreateUserModal();
        }
    });
});

/**
 * Despliega la ventana modal flotante de creación de rutas.
 */
function openCreateRouteModal() {
    const modal = document.getElementById('modal-create-route');
    const alertBanner = document.getElementById('modal-alert-banner');
    if (modal) {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        if (alertBanner) alertBanner.style.display = 'none';

        // Auto-focus en el primer campo tras renderizado
        setTimeout(() => {
            const uriInput = document.getElementById('route-input-uri');
            if (uriInput) uriInput.focus();
        }, 100);
    }
}

/**
 * Oculta la ventana modal flotante.
 */
function closeCreateRouteModal() {
    const modal = document.getElementById('modal-create-route');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

/**
 * Ajusta dinámicamente los roles recomendados según el tipo de acceso.
 */
function handleTypeChange(val) {
    const rolesInput = document.getElementById('route-input-roles');
    if (!rolesInput) return;
    if (val === 'public') {
        if (rolesInput.value === '["superadmin"]') {
            rolesInput.value = '[]';
        }
    } else {
        if (rolesInput.value === '[]' || !rolesInput.value) {
            rolesInput.value = '["superadmin"]';
        }
    }
}

/**
 * Asigna rápidamente un string de roles JSON al campo de roles.
 */
function setRoleTag(val) {
    const input = document.getElementById('route-input-roles');
    if (input) input.value = val;
}

/**
 * Envío asíncrono del formulario mediante Fetch para guardar la ruta
 * y actualizar la tabla en tiempo real sin recargar la página.
 */
async function handleRouteFormSubmit(event) {
    if (event) event.preventDefault();

    const submitBtn = document.getElementById('btn-submit-route');
    const alertBanner = document.getElementById('modal-alert-banner');

    const uri = document.getElementById('route-input-uri')?.value.trim() || '';
    const app = document.getElementById('route-input-app')?.value.trim() || 'asrsAdmin';
    const type = document.getElementById('route-input-type')?.value || 'private';
    const content = document.getElementById('route-input-content')?.value.trim() || '';
    const controller = document.getElementById('route-input-controller')?.value.trim() || '';
    const method = document.getElementById('route-input-method')?.value.trim() || '';
    const rolesRaw = document.getElementById('route-input-roles')?.value.trim() || '';

    // Validación básica de formato de roles JSON
    let rolesArray = [];
    if (rolesRaw) {
        try {
            rolesArray = JSON.parse(rolesRaw);
            if (!Array.isArray(rolesArray)) {
                throw new Error('Debe ser un array JSON');
            }
        } catch (err) {
            showAlertModal(alertBanner, '[!] Formato de roles inválido: debe ser un array JSON válido, ej: ["superadmin"]');
            return;
        }
    }

    // Efecto visual de carga
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="btn-indicator">⚡</span> [ GUARDANDO EN DISCO... ]';
    }

    const payload = {
        uri: uri,
        app: app,
        type: type,
        content: content,
        controller: controller,
        method: method,
        layout: 'default',
        roles: rolesArray
    };

    const tokenB = sessionStorage.getItem('asrs_validator') || localStorage.getItem('asrs_token_b');
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };
    if (tokenB) headers['Authorization'] = `Bearer ${tokenB}`;

    try {
        const response = await fetch('admin/routes', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (response.ok && data.ok) {
            // 1. Cerrar el modal
            closeCreateRouteModal();

            // 2. Limpiar el formulario
            const form = document.getElementById('form-create-route');
            if (form) form.reset();
            setRoleTag('["superadmin"]');

            // 3. Notificación tipo consola en la página principal
            showConsoleBanner(`[OK] Ruta '/${data.route.uri}' persistida en BD y caché routes.php recompilada con éxito.`);

            // 4. Actualizar tabla en tiempo real sin recargar página
            insertOrUpdateTableRow(data.route);

            // 5. Actualizar contadores
            if (data.routesCount !== undefined) {
                const countBadge = document.getElementById('count-routes');
                if (countBadge) {
                    countBadge.textContent = String(data.routesCount).padStart(2, '0');
                }
            }
        } else {
            showAlertModal(alertBanner, `[ERROR] ${data.error || 'Fallo al guardar la ruta'}`);
        }
    } catch (err) {
        showAlertModal(alertBanner, '[ERROR] Fallo de conexión perimetral al procesar la ruta.');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="btn-indicator">⚡</span> [ GUARDAR & COMPILAR RUTA ]';
        }
    }
}

function showAlertModal(bannerEl, msg) {
    if (!bannerEl) return;
    bannerEl.className = 'modal-alert error';
    bannerEl.textContent = msg;
    bannerEl.style.display = 'block';
}

function showConsoleBanner(msg) {
    const alertBox = document.getElementById('console-alert');
    const alertMsg = document.getElementById('console-alert-msg');
    const alertIcon = document.getElementById('console-alert-icon');
    if (alertBox && alertMsg) {
        alertBox.className = 'console-alert-banner success';
        if (alertIcon) alertIcon.textContent = '[OK]';
        alertMsg.textContent = msg;
        alertBox.style.display = 'flex';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 5000);
    }
}

/**
 * Inserta o actualiza la fila de la ruta en la tabla de forma reactiva.
 */
function insertOrUpdateTableRow(route) {
    const tbody = document.querySelector('#routes-table tbody');
    if (!tbody) return;

    // Si la tabla contenía el mensaje de vacía, removerlo
    const emptyMsg = tbody.querySelector('.empty-table-msg');
    if (emptyMsg) {
        emptyMsg.closest('tr').remove();
    }

    const isPrivate = (route.type || 'public') === 'private';
    const rolesArr = route.roles_array || [];
    const rolesJson = rolesArr.length > 0 ? JSON.stringify(rolesArr) : '[]';
    const uriDisplay = (route.uri === '') ? '<span class="text-subtle">/ (raíz)</span>' : escapeHtml(route.uri);
    const controllerDisplay = route.controller
        ? escapeHtml(route.controller) + (route.method ? '::' + escapeHtml(route.method) : '')
        : `<span class="text-subtle">[Kernel / ${escapeHtml(route.content || 'vista')}]</span>`;

    // Buscar si ya existe una fila con esa URI y App
    const existingRows = tbody.querySelectorAll('tr.route-row');
    let matchedRow = null;
    existingRows.forEach(r => {
        const uText = r.querySelector('.col-uri')?.textContent.replace(/\[|\]/g, '').trim();
        const aText = r.querySelector('.col-app')?.textContent.trim();
        if (uText === (route.uri || '/') && aText === route.app) {
            matchedRow = r;
        }
    });

    const rowHtml = `
        <td class="col-id">${escapeHtml(String(route.id || '+'))}</td>
        <td class="col-uri">
            <span class="uri-badge">[ ${uriDisplay} ]</span>
        </td>
        <td class="col-app">
            <span class="app-tag app-${escapeHtml(route.app.toLowerCase())}">${escapeHtml(route.app)}</span>
        </td>
        <td class="col-controller">
            ${controllerDisplay}
        </td>
        <td class="col-type">
            <span class="type-badge ${isPrivate ? 'type-private' : 'type-public'}">
                ${escapeHtml((route.type || 'PUBLIC').toUpperCase())}
            </span>
        </td>
        <td class="col-roles">
            ${rolesArr.length > 0
                ? `<code class="roles-code-block">${escapeHtml(rolesJson)}</code>`
                : `<span class="roles-public-tag">[*] PÚBLICO_ABIERTO</span>`}
        </td>
        <td class="col-actions">
            <a href="${escapeHtml(route.uri)}" target="_blank" class="asrs-btn-mini" title="Probar ruta en nueva pestaña">
                [ TEST ↗ ]
            </a>
        </td>
    `;

    if (matchedRow) {
        matchedRow.setAttribute('data-type', (route.type || 'PUBLIC').toUpperCase());
        matchedRow.innerHTML = rowHtml;
        matchedRow.classList.add('row-highlight-new');
        setTimeout(() => matchedRow.classList.remove('row-highlight-new'), 2500);
    } else {
        const newTr = document.createElement('tr');
        newTr.className = 'route-row row-highlight-new';
        newTr.setAttribute('data-type', (route.type || 'PUBLIC').toUpperCase());
        newTr.innerHTML = rowHtml;
        tbody.prepend(newTr);
        setTimeout(() => newTr.classList.remove('row-highlight-new'), 2500);
    }

    // Actualizar filtro y conteo visible
    if (typeof filterRoutesTable === 'function') {
        filterRoutesTable();
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ==========================================================================
   Gestor de Usuarios - Modal de Aprovisionamiento (USR-02)
   Estética Neon Dark & Wireframe Outline UI
   ========================================================================== */
function openCreateUserModal() {
    const modal = document.getElementById('modal-create-user');
    if (modal) {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');

        // Auto-focus en el primer campo
        setTimeout(() => {
            const nameInput = document.getElementById('user-input-name');
            if (nameInput) nameInput.focus();
        }, 100);
    }
}

function closeCreateUserModal() {
    const modal = document.getElementById('modal-create-user');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

/**
 * Envío asíncrono del formulario mediante Fetch para aprovisionar un nuevo usuario
 * y actualizar la tabla en tiempo real sin recargar la página.
 */
async function handleUserFormSubmit(event) {
    if (event) event.preventDefault();

    const submitBtn = document.getElementById('btn-submit-user');
    const alertBanner = document.getElementById('modal-user-alert-banner');

    const name = document.getElementById('user-input-name')?.value.trim() || '';
    const email = document.getElementById('user-input-email')?.value.trim() || '';
    const role = document.getElementById('user-input-role')?.value || 'student';
    const password = document.getElementById('user-input-password')?.value || '';

    // Validaciones locales
    if (!name || !email || !password) {
        if (alertBanner) {
            alertBanner.className = 'modal-alert error';
            alertBanner.style.display = 'block';
            alertBanner.textContent = '[!] Todos los campos obligatorios deben ser completados.';
        }
        return;
    }

    if (password.length < 6) {
        if (alertBanner) {
            alertBanner.className = 'modal-alert error';
            alertBanner.style.display = 'block';
            alertBanner.textContent = '[!] La contraseña debe contener al menos 6 caracteres.';
        }
        return;
    }

    // Estado visual de carga
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="btn-indicator">⚡</span> [ APROVISIONANDO... ]';
    }

    const payload = {
        name: name,
        email: email,
        role: role,
        password: password
    };

    const tokenB = sessionStorage.getItem('asrs_validator') || localStorage.getItem('asrs_token_b');
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };
    if (tokenB) headers['Authorization'] = `Bearer ${tokenB}`;

    try {
        const response = await fetch('admin/users', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (response.ok && data.ok) {
            // 1. Cerrar el modal
            closeCreateUserModal();

            // 2. Limpiar el formulario
            const form = document.getElementById('form-create-user');
            if (form) form.reset();

            // 3. Notificación tipo consola en la página principal
            if (typeof showConsoleBanner === 'function') {
                showConsoleBanner(`[OK] ${data.message || 'Usuario aprovisionado exitosamente.'}`);
            }

            // 4. Actualizar tabla en tiempo real sin recargar página
            insertUserTableRow(data.user);

            // 5. Actualizar contador de usuarios
            if (data.usersCount !== undefined) {
                const countBadges = document.querySelectorAll('.drawer-count-badge');
                countBadges.forEach(badge => {
                    badge.textContent = `[ ${data.usersCount} REGISTRO(S) ]`;
                });
            }

            // 6. Abrir drawer de catálogo si está cerrado para visualizar el nuevo registro
            const drawer = document.getElementById('users-catalog-drawer');
            const btnToggle = document.getElementById('btn-toggle-users-catalog');
            if (drawer && !drawer.classList.contains('open') && typeof openCatalog === 'function') {
                openCatalog();
            } else if (drawer && !drawer.classList.contains('open')) {
                drawer.classList.add('open');
                drawer.setAttribute('aria-hidden', 'false');
                if (btnToggle) {
                    btnToggle.setAttribute('aria-expanded', 'true');
                    btnToggle.classList.add('is-open');
                    const btnText = btnToggle.querySelector('.btn-catalog-text');
                    if (btnText) btnText.textContent = '[ OCULTAR CATÁLOGO ▲ ]';
                }
            }
        } else {
            if (alertBanner) {
                alertBanner.className = 'modal-alert error';
                alertBanner.style.display = 'block';
                alertBanner.textContent = `[ERROR] ${data.error || 'Fallo al aprovisionar el usuario.'}`;
            }
        }
    } catch (error) {
        if (alertBanner) {
            alertBanner.className = 'modal-alert error';
            alertBanner.style.display = 'block';
            alertBanner.textContent = `[ERROR DE RED] No se pudo establecer conexión con el núcleo ASRS: ${error.message}`;
        }
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = '[ GUARDAR ]';
        }
    }
}

/**
 * Inserta un nuevo usuario al inicio de la tabla del catálogo con resaltado wireframe.
 */
function insertUserTableRow(user) {
    if (!user) return;
    const tbody = document.getElementById('users-table-body');
    if (!tbody) return;

    // Eliminar fila vacía si existe
    const emptyRow = tbody.querySelector('.empty-row');
    if (emptyRow) {
        emptyRow.remove();
    }

    const isSuper = (user.role === 'superadmin');
    const roleClass = isSuper ? 'role-super' : 'role-standard';
    const isActive = (user.status === 1 || user.status === '1');
    const statusClass = isActive ? 'status-active' : 'status-inactive';
    const statusText = isActive ? '● ACTIVO' : '■ INACTIVO';
    const idFormatted = '#' + String(user.id || '+').padStart(2, '0');

    const newTr = document.createElement('tr');
    newTr.className = 'user-row-item row-highlight-new';
    newTr.innerHTML = `
        <td class="col-id">
            <span class="id-badge">${escapeHtml(idFormatted)}</span>
        </td>
        <td class="col-name">
            <span class="user-name-text">${escapeHtml(user.name)}</span>
        </td>
        <td class="col-email">
            <span class="user-email-text">${escapeHtml(user.email)}</span>
        </td>
        <td class="col-role">
            <div class="roles-tag-group">
                <span class="role-pill ${roleClass}">
                    [ ${escapeHtml(user.role)} ]
                </span>
            </div>
        </td>
        <td class="col-status">
            <span class="status-pill ${statusClass}">
                ${statusText}
            </span>
        </td>
        <td class="col-created">
            <span class="date-text">${escapeHtml(user.created_at || 'Justo ahora')}</span>
        </td>
    `;

    tbody.prepend(newTr);

    setTimeout(() => {
        newTr.classList.remove('row-highlight-new');
    }, 3000);
}
