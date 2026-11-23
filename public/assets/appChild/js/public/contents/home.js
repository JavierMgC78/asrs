document.addEventListener('DOMContentLoaded', () => {
  // Cerrar menú móvil cuando se navega a un ancla de la landing
  const menuToggle = document.getElementById('ceaMenuToggle');
  const nav = document.getElementById('ceaNav');
  if (menuToggle && nav) {
    nav.querySelectorAll('a[href*="#"]').forEach((anchor) => {
      anchor.addEventListener('click', () => {
        if (window.innerWidth <= 960 && nav.classList.contains('is-open')) {
          nav.classList.remove('is-open');
          menuToggle.setAttribute('aria-expanded', 'false');
        }
      });
    });
  }

  // Interacción en tarjetas de Niveles Educativos
  const levelCards = document.querySelectorAll('.cea-level-card');
  const detailTitle = document.getElementById('levelDetailTitle');
  const detailBody = document.getElementById('levelDetailBody');

  const levelDescriptions = {
    preescolar: {
      title: 'Preescolar · Creatividad y Juego',
      body: 'Enfoque centrado en el desarrollo neurocognitivo y socioemocional mediante el juego como herramienta principal. Objetivo: que el niño desarrolle autonomía, habilidades de comunicación básica y una estructura emocional sólida para explorar el mundo con confianza.'
    },
    primaria: {
      title: 'Primaria · Estabilidad y Pensamiento Crítico',
      body: 'Modelo de aprendizaje activo para fortalecer lectoescritura, pensamiento lógico-matemático e indagación científica. Objetivo: formar alumnos con pensamiento crítico, bases académicas sólidas, responsabilidad y trabajo colaborativo.'
    },
    secundaria: {
      title: 'Secundaria · Liderazgo y Madurez',
      body: 'Consolidación de conocimientos académicos y del carácter con uso responsable de la información y la tecnología. Objetivo: asegurar hábitos, criterio y confianza para transitar con éxito hacia la educación media superior.'
    }
  };

  const selectLevel = (card) => {
    const key = card.getAttribute('data-level');
    levelCards.forEach((item) => item.classList.toggle('is-selected', item === card));
    if (key && levelDescriptions[key] && detailTitle && detailBody) {
      detailTitle.textContent = levelDescriptions[key].title;
      detailBody.textContent = levelDescriptions[key].body;
    }
  };

  levelCards.forEach((card) => {
    card.addEventListener('click', () => selectLevel(card));
    card.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        selectLevel(card);
      }
    });
  });

  // Animación suave de contadores institucionales al entrar en pantalla
  const metrics = document.querySelectorAll('.cea-metric__value[data-target]');
  if (metrics.length > 0 && 'IntersectionObserver' in window) {
    const animateCounter = (el) => {
      const target = Number(el.getAttribute('data-target')) || 0;
      const prefix = el.getAttribute('data-prefix') || '';
      const suffix = el.getAttribute('data-suffix') || '';
      const duration = 900;
      const startTime = performance.now();

      const step = (now) => {
        const progress = Math.min((now - startTime) / duration, 1);
        const current = Math.floor(progress * target);
        el.textContent = `${prefix}${current}${suffix}`;
        if (progress < 1) {
          requestAnimationFrame(step);
        } else {
          el.textContent = `${prefix}${target}${suffix}`;
        }
      };

      requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.25 }
    );

    metrics.forEach((metric) => observer.observe(metric));
  }

  // Validación básica del formulario de informes / admisiones
  const form = document.getElementById('ceaAdmissionForm');
  const statusEl = document.getElementById('admissionFormStatus');

  if (form && statusEl) {
    form.addEventListener('submit', (event) => {
      const nameInput = document.getElementById('parentName');
      const contactInput = document.getElementById('parentContact');
      const levelSelect = document.getElementById('studentLevel');

      if (!nameInput?.value.trim() || !contactInput?.value.trim() || !levelSelect?.value) {
        event.preventDefault();
        statusEl.textContent = 'Por favor completa nombre, contacto y nivel educativo.';
      } else {
        statusEl.textContent = 'Enviando solicitud...';
      }
    });
  }

    // Desplazamiento suave controlado para anclas de la landing con compensación de header fijo
    const header = document.getElementById('ceaHeader');
    const topMargin = 18;

    const smoothScrollTo = (targetTop, duration = 500) => {
      const startTop = window.scrollY || window.pageYOffset;
      const distance = targetTop - startTop;
      if (Math.abs(distance) < 1) {
        return;
      }

      const startTime = performance.now();
      const easeInOutCubic = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

      const step = (now) => {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        window.scrollTo(0, startTop + distance * easeInOutCubic(progress));
        if (progress < 1) {
          requestAnimationFrame(step);
        }
      };

      requestAnimationFrame(step);
    };

    document.querySelectorAll('a[href*="#"]').forEach((link) => {
      link.addEventListener('click', (event) => {
        const rawHref = link.getAttribute('href');
        if (!rawHref || rawHref === '#') {
          return;
        }

        const url = new URL(link.href, window.location.origin);
        const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
        const targetPath = url.pathname.replace(/\/+$/, '') || '/';

        if (!url.hash || currentPath !== targetPath) {
          return;
        }

        const targetId = decodeURIComponent(url.hash.slice(1));
        const targetElement = document.getElementById(targetId);

        if (!targetElement) {
          return;
        }

        event.preventDefault();

        // Cerrar el menú móvil si está desplegado
        if (nav && menuToggle && nav.classList.contains('is-open')) {
          nav.classList.remove('is-open');
          menuToggle.setAttribute('aria-expanded', 'false');
        }

        const headerHeight = header ? header.getBoundingClientRect().height : 0;
        const targetTop = Math.max(
          0,
          targetElement.getBoundingClientRect().top + (window.scrollY || window.pageYOffset) - headerHeight - topMargin
        );

        smoothScrollTo(targetTop);
        history.pushState(null, '', `#${targetId}`);
      });
    });

});