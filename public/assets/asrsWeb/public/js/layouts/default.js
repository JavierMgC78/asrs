document.addEventListener('DOMContentLoaded', () => {
  const burger = document.getElementById('asrsBurger');
  const nav = document.getElementById('asrsNav');
  const runtimeStatus = document.getElementById('asrsRuntimeStatus');

  // Resaltar enlace activo de ASRS
  const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
  document.querySelectorAll('.asrs-core-nav__link').forEach((link) => {
    const targetPath = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
    if (targetPath === currentPath) {
      link.setAttribute('aria-current', 'page');
    }
  });

  // Menú móvil con soporte de tecla Escape
  if (burger && nav) {
    burger.addEventListener('click', () => {
      const expanded = burger.getAttribute('aria-expanded') === 'true';
      const next = !expanded;
      burger.setAttribute('aria-expanded', String(next));
      nav.classList.toggle('is-open', next);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && burger.getAttribute('aria-expanded') === 'true') {
        burger.setAttribute('aria-expanded', 'false');
        nav.classList.remove('is-open');
        burger.focus();
      }
    });
  }

  // Indicador visual si el cuerpo declara modo mantenimiento
  if (runtimeStatus && document.body.dataset.maintenance === '1') {
    runtimeStatus.textContent = 'Modo mantenimiento activo';
    runtimeStatus.classList.add('is-alert');
  }
});