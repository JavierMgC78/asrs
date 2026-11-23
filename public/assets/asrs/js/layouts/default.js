document.addEventListener('DOMContentLoaded', () => {
  const navToggle = document.getElementById('asrsNavToggle');
  const nav = document.getElementById('asrsNav');

  const setMenuState = (open) => {
    if (!navToggle || !nav) {
      return;
    }
    nav.classList.toggle('is-open', open);
    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  };

  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';
      setMenuState(!isExpanded);
    });

    nav.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 820) {
          setMenuState(false);
        }
      });
    });

    document.addEventListener('click', (event) => {
      if (!nav.classList.contains('is-open')) {
        return;
      }
      const target = event.target;
      if (target instanceof Node && !nav.contains(target) && !navToggle.contains(target)) {
        setMenuState(false);
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && nav.classList.contains('is-open')) {
        setMenuState(false);
        navToggle.focus();
      }
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth > 820 && nav.classList.contains('is-open')) {
        setMenuState(false);
      }
    });
  }

  // Resaltado del enlace de navegación según la sección visible o hash actual
  const navLinks = Array.from(document.querySelectorAll('.asrs-nav__link[href*="#"]'));
  const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';

  const sameRouteLinks = navLinks.filter((link) => {
    const parsed = new URL(link.href, window.location.origin);
    const linkPath = parsed.pathname.replace(/\/+$/, '') || '/';
    return linkPath === currentPath && Boolean(parsed.hash);
  });

  const setActiveLink = (hashId) => {
    sameRouteLinks.forEach((link) => {
      const parsed = new URL(link.href, window.location.origin);
      link.classList.toggle('is-active', parsed.hash === `#${hashId}`);
    });
  };

  if (sameRouteLinks.length > 0) {
    const sections = sameRouteLinks
      .map((link) => {
        const id = new URL(link.href, window.location.origin).hash.slice(1);
        return document.getElementById(id);
      })
      .filter((el) => el !== null);

    if (window.location.hash) {
      setActiveLink(window.location.hash.slice(1));
    } else if (sections[0]) {
      setActiveLink(sections[0].id);
    }

    if ('IntersectionObserver' in window && sections.length > 0) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              setActiveLink(entry.target.id);
            }
          });
        },
        { rootMargin: '-25% 0px -60% 0px', threshold: 0.05 }
      );

      sections.forEach((section) => observer.observe(section));
    }
  }
});