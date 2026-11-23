<div class="asrs-home">
  <!-- Hero / Estado del Núcleo -->
  <section class="asrs-hero" id="inicio" aria-labelledby="asrsHeroTitle">
    <div class="asrs-hero__grid">
      <div class="asrs-hero__main">
        <div class="asrs-badge">
          <span class="asrs-badge__dot" aria-hidden="true"></span>
          <span>KERNEL ACTIVE // ASRS CORE v4</span>
        </div>

        <h1 class="asrs-hero__title" id="asrsHeroTitle">
          Axe Secure Router System
          <span class="asrs-hero__accent">Infraestructura, Enrutamiento y Seguridad Perimetral.</span>
        </h1>

        <p class="asrs-hero__lead">
          Motor PHP de alto rendimiento diseñado bajo segregación estricta de responsabilidades:
          un núcleo blindado (<code>asrsCore</code>) que gobierna el tráfico y aplicaciones modulares
          independientes (<code>appChild</code>) desacopladas de la lógica central.
        </p>

        <div class="asrs-hero__actions">
          <a href="#arquitectura" class="asrs-btn asrs-btn--gold">Explorar Arquitectura</a>
          <a href="#estado-operativo" class="asrs-btn asrs-btn--outline">Diagnóstico del Sistema</a>
        </div>
      </div>

      <!-- Consola de Telemetría de Petición -->
      <aside class="asrs-terminal" aria-label="Flujo de ejecución del Kernel">
        <div class="asrs-terminal__bar">
          <span class="asrs-terminal__title">asrs://pipeline/lifecycle</span>
          <span class="asrs-terminal__status">READY</span>
        </div>
        <ol class="asrs-terminal__list">
          <li>
            <span class="asrs-step-tag">01</span>
            <div>
              <strong>public/index.php</strong>
              <p>Punto de entrada único &amp; Autoloader PSR-4</p>
            </div>
          </li>
          <li>
            <span class="asrs-step-tag">02</span>
            <div>
              <strong>caches/asrs_views.php</strong>
              <p>Resolución O(1) en arreglo PHP nativo + OPcache</p>
            </div>
          </li>
          <li>
            <span class="asrs-step-tag">03</span>
            <div>
              <strong>Asrs\Core\Middleware</strong>
              <p>Control perimetral (<code>is_public</code> / Split Token)</p>
            </div>
          </li>
          <li>
            <span