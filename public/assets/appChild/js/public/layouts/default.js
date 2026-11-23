document.addEventListener('DOMContentLoaded', () => {
  const header = document.getElementById('ceaHeader');
  const menuToggle = document.getElementById('ceaMenuToggle');
  const nav = document.getElementById('ceaNav');

  // Marcar ruta activa en el menú principal
  const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
  document.querySelectorAll('.cea-nav__link').forEach((link) => {
    const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
    if (linkPath === currentPath) {
      link.setAttribute('aria-current', 'page');
    }
  });

  // Alternar menú responsive y cerrar con Escape
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => {
      const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
      const nextState = !expanded;
      menuToggle.setAttribute('aria-expanded', String(nextState));
      nav.classList.toggle('is-open', nextState);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && menuToggle.getAttribute('aria-expanded') === 'true') {
        menuToggle.setAttribute('aria-expanded', 'false');
        nav.classList.remove('is-open');
        menuToggle.focus();
      }
    });
  }

  // Elevar header al hacer scroll
  if (header) {
    const toggleScrollShadow = () => {
      header.classList.toggle('is-scrolled', window.scrollY > 8);
    };
    toggleScrollShadow();
    window.addEventListener('scroll', toggleScrollShadow, { passive: true });
  }
});