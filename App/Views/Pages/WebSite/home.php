<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CEA – Inicio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

  <style>
    :root {
      --cea-navy:       #0D1F35;
      --cea-navy-mid:   #1A3557;
      --cea-navy-light: #2B4F7A;
      --cea-gold:       #B8944A;
      --cea-gold-light: #D4B06A;
      --cea-gold-pale:  #F0E8D5;
      --cea-cream:      #F7F4EF;
      --cea-white:      #FFFFFF;
      --cea-gray-100:   #F0ECE6;
      --cea-gray-200:   #E2DDD5;
      --cea-gray-300:   #C8BFB0;
      --cea-gray-500:   #8A7E70;
      --cea-gray-700:   #4A4138;
      --cea-text:       #1E1811;

      --font-display: 'Cormorant Garamond', Georgia, serif;
      --font-body:    'DM Sans', sans-serif;
      --container:    1200px;
      --radius-sm:    4px;
      --radius-md:    10px;
      --radius-lg:    18px;
      --transition:   .24s cubic-bezier(.4,0,.2,1);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { font-size: 16px; scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
    body { font-family: var(--font-body); color: var(--cea-text); background: var(--cea-cream); line-height: 1.6; }
    img { max-width: 100%; display: block; }
    a { color: inherit; text-decoration: none; }

    .container {
      width: 100%;
      max-width: var(--container);
      margin: 0 auto;
      padding: 0 clamp(20px, 4vw, 56px);
    }

    /* ── Shared buttons ── */
    .btn {
      display: inline-flex; align-items: center; gap: 8px;
      font-family: var(--font-body); font-size: .8125rem; font-weight: 500;
      letter-spacing: .08em; text-transform: uppercase;
      border-radius: var(--radius-sm); padding: 12px 26px;
      cursor: pointer; transition: var(--transition); border: 1.5px solid transparent;
      white-space: nowrap;
    }
    .btn svg { width: 14px; height: 14px; flex-shrink: 0; }
    .btn-primary { background: var(--cea-gold); color: #fff; border-color: var(--cea-gold); }
    .btn-primary:hover { background: var(--cea-gold-light); border-color: var(--cea-gold-light); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(184,148,74,.3); }
    .btn-outline-navy { background: transparent; color: var(--cea-navy); border-color: var(--cea-navy); }
    .btn-outline-navy:hover { background: var(--cea-navy); color: #fff; }
    .btn-ghost-white { background: rgba(255,255,255,.1); color: #fff; border-color: rgba(255,255,255,.35); }
    .btn-ghost-white:hover { background: rgba(255,255,255,.2); border-color: rgba(255,255,255,.6); }

    /* ── Section label ── */
    .section-label {
      display: inline-flex; align-items: center; gap: 10px;
      font-size: .6875rem; font-weight: 500; letter-spacing: .18em;
      text-transform: uppercase; color: var(--cea-gold); margin-bottom: 16px;
    }
    .section-label::before {
      content: '';
      display: block; width: 28px; height: 1.5px;
      background: var(--cea-gold);
    }

    /* ── Scroll reveal ── */
    .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    .reveal-delay-1 { transition-delay: .1s; }
    .reveal-delay-2 { transition-delay: .2s; }
    .reveal-delay-3 { transition-delay: .3s; }
    .reveal-delay-4 { transition-delay: .4s; }

    /* =====================================================
       1. HERO
    ===================================================== */
    .hero {
      position: relative;
      min-height: clamp(580px, 88vh, 820px);
      display: flex;
      align-items: center;
      overflow: hidden;
      background: var(--cea-navy);
    }

    /* Geometric background */
    .hero-bg {
      position: absolute; inset: 0;
      background:
        linear-gradient(135deg, #0D1F35 0%, #1A3557 50%, #0f2a45 100%);
    }

    .hero-bg-grid {
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(184,148,74,.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(184,148,74,.06) 1px, transparent 1px);
      background-size: 60px 60px;
      mask-image: radial-gradient(ellipse 80% 70% at 60% 50%, black 30%, transparent 100%);
    }

    .hero-bg-glow {
      position: absolute;
      width: 700px; height: 700px;
      right: -100px; top: 50%;
      transform: translateY(-50%);
      background: radial-gradient(circle, rgba(184,148,74,.12) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero-bg-glow2 {
      position: absolute;
      width: 400px; height: 400px;
      left: -80px; bottom: -80px;
      background: radial-gradient(circle, rgba(43,79,122,.5) 0%, transparent 70%);
      pointer-events: none;
    }

    /* Diagonal cut at bottom */
    .hero::after {
      content: '';
      position: absolute; bottom: -1px; left: 0; right: 0;
      height: 80px;
      background: var(--cea-cream);
      clip-path: polygon(0 100%, 100% 0, 100% 100%);
    }

    .hero-inner {
      position: relative; z-index: 2;
      display: grid;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      gap: 64px;
      padding: 80px 0 120px;
      width: 100%;
    }

    /* Left: text */
    .hero-text {}

    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(184,148,74,.15); border: 1px solid rgba(184,148,74,.3);
      border-radius: 20px; padding: 5px 14px;
      font-size: .6875rem; font-weight: 500; letter-spacing: .14em;
      text-transform: uppercase; color: var(--cea-gold-light);
      margin-bottom: 28px;
      animation: fadeSlideIn .7s ease both;
    }
    .hero-badge span { width: 6px; height: 6px; background: var(--cea-gold); border-radius: 50%; flex-shrink: 0; }

    .hero-title {
      font-family: var(--font-display);
      font-size: clamp(2.4rem, 4.5vw, 3.75rem);
      font-weight: 400;
      line-height: 1.15;
      color: #fff;
      margin-bottom: 24px;
      animation: fadeSlideIn .7s .12s ease both;
    }
    .hero-title em {
      font-style: italic;
      color: var(--cea-gold-light);
    }

    .hero-subtitle {
      font-size: 1.0625rem;
      font-weight: 300;
      line-height: 1.75;
      color: rgba(255,255,255,.65);
      max-width: 460px;
      margin-bottom: 40px;
      animation: fadeSlideIn .7s .22s ease both;
    }

    .hero-actions {
      display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
      animation: fadeSlideIn .7s .32s ease both;
    }

    /* Right: image/card stack */
    .hero-visual {
      position: relative;
      animation: fadeSlideIn .8s .2s ease both;
    }

    .hero-img-frame {
      position: relative;
      border-radius: var(--radius-lg);
      overflow: hidden;
      aspect-ratio: 4/3.2;
      background: var(--cea-navy-mid);
      border: 1px solid rgba(255,255,255,.08);
      box-shadow: 0 32px 80px rgba(0,0,0,.35), 0 0 0 1px rgba(184,148,74,.15);
    }

    /* Placeholder photo — replace with real img */
    .hero-img-placeholder {
      width: 100%; height: 100%;
      background:
        linear-gradient(160deg, #1A3557 0%, #2B4F7A 50%, #1A3557 100%);
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      gap: 12px; color: rgba(255,255,255,.25);
    }
    .hero-img-placeholder svg { width: 48px; height: 48px; opacity: .4; }
    .hero-img-placeholder span { font-size: .75rem; letter-spacing: .1em; text-transform: uppercase; }

    /* Gold frame accent */
    .hero-img-frame::before {
      content: '';
      position: absolute; inset: 0;
      border: 2px solid transparent;
      border-image: linear-gradient(135deg, rgba(184,148,74,.6), transparent 60%) 1;
      border-radius: var(--radius-lg);
      z-index: 1; pointer-events: none;
    }

    /* Floating stat cards */
    .hero-stat-card {
      position: absolute;
      background: rgba(255,255,255,.97);
      border-radius: var(--radius-md);
      padding: 14px 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,.18), 0 2px 8px rgba(0,0,0,.1);
      backdrop-filter: blur(10px);
    }

    .hero-stat-card--years {
      bottom: -20px; left: -28px;
    }
    .hero-stat-card--trust {
      top: -16px; right: -24px;
    }

    .hero-stat-card .stat-number {
      font-family: var(--font-display);
      font-size: 2rem; font-weight: 600;
      color: var(--cea-navy); line-height: 1;
    }
    .hero-stat-card .stat-number span {
      font-size: 1.1rem; color: var(--cea-gold);
    }
    .hero-stat-card .stat-label {
      font-size: .6875rem; font-weight: 500; letter-spacing: .1em;
      text-transform: uppercase; color: var(--cea-gray-500);
      margin-top: 4px;
    }

    .hero-stat-card--trust {
      display: flex; align-items: center; gap: 12px;
    }
    .trust-icon {
      width: 38px; height: 38px;
      background: var(--cea-gold-pale);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .trust-icon svg { width: 18px; height: 18px; color: var(--cea-gold); }

    @keyframes fadeSlideIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* =====================================================
       2. POR QUÉ CEA — 3 pilares
    ===================================================== */
    .porque-cea {
      padding: 96px 0 100px;
      background: var(--cea-cream);
    }

    .porque-header {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 48px;
      align-items: end;
      margin-bottom: 72px;
    }

    .porque-header h2 {
      font-family: var(--font-display);
      font-size: clamp(2rem, 3.5vw, 3rem);
      font-weight: 400;
      line-height: 1.2;
      color: var(--cea-navy);
    }
    .porque-header h2 em { font-style: italic; color: var(--cea-gold); }

    .porque-header p {
      font-size: 1.0625rem;
      line-height: 1.75;
      color: var(--cea-gray-500);
      padding-bottom: 4px;
    }

    .pilares-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }

    .pilar-card {
      background: var(--cea-white);
      border-radius: var(--radius-lg);
      padding: 40px 36px;
      border: 1px solid var(--cea-gray-200);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .pilar-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      background: var(--cea-gold);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform .35s ease;
    }
    .pilar-card:hover {
      border-color: var(--cea-gray-300);
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(13,31,53,.09);
    }
    .pilar-card:hover::before { transform: scaleX(1); }

    .pilar-icon {
      width: 52px; height: 52px;
      background: var(--cea-navy);
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 24px;
      position: relative;
    }
    .pilar-icon::after {
      content: '';
      position: absolute;
      inset: -3px;
      border-radius: calc(var(--radius-md) + 2px);
      border: 1.5px solid var(--cea-gold-pale);
    }
    .pilar-icon svg { width: 24px; height: 24px; color: var(--cea-gold-light); }

    .pilar-card h3 {
      font-family: var(--font-display);
      font-size: 1.4375rem;
      font-weight: 500;
      color: var(--cea-navy);
      margin-bottom: 12px;
    }

    .pilar-card p {
      font-size: .9375rem;
      line-height: 1.75;
      color: var(--cea-gray-500);
    }

    .pilar-card .pilar-tag {
      display: inline-block;
      margin-top: 24px;
      font-size: .6875rem; font-weight: 500; letter-spacing: .12em;
      text-transform: uppercase; color: var(--cea-gold);
      border-bottom: 1px solid var(--cea-gold-pale);
      padding-bottom: 2px;
    }

    /* =====================================================
       3. NIVELES EDUCATIVOS
    ===================================================== */
    .niveles {
      padding: 96px 0;
      background: var(--cea-navy);
      position: relative;
      overflow: hidden;
    }

    .niveles::before {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(184,148,74,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(184,148,74,.04) 1px, transparent 1px);
      background-size: 80px 80px;
    }

    .niveles-inner { position: relative; z-index: 1; }

    .niveles-header {
      text-align: center;
      margin-bottom: 64px;
    }

    .niveles-header h2 {
      font-family: var(--font-display);
      font-size: clamp(2rem, 3.5vw, 3rem);
      font-weight: 400;
      color: #fff;
      margin-bottom: 16px;
    }
    .niveles-header h2 em { font-style: italic; color: var(--cea-gold-light); }

    .niveles-header p {
      font-size: 1.0625rem;
      color: rgba(255,255,255,.55);
      max-width: 500px;
      margin: 0 auto;
    }

    .niveles-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .nivel-card {
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: var(--radius-lg);
      padding: 40px 32px;
      position: relative;
      overflow: hidden;
      transition: var(--transition);
      cursor: default;
    }
    .nivel-card:hover {
      background: rgba(255,255,255,.07);
      border-color: rgba(184,148,74,.3);
      transform: translateY(-4px);
    }

    .nivel-number {
      font-family: var(--font-display);
      font-size: 5rem;
      font-weight: 300;
      color: rgba(184,148,74,.12);
      line-height: 1;
      position: absolute;
      top: 24px; right: 28px;
      user-select: none;
      transition: var(--transition);
    }
    .nivel-card:hover .nivel-number { color: rgba(184,148,74,.22); }

    .nivel-etapa {
      display: inline-block;
      font-size: .6875rem; font-weight: 500; letter-spacing: .16em;
      text-transform: uppercase;
      padding: 4px 12px;
      border-radius: 20px;
      margin-bottom: 20px;
    }
    .nivel-card--preescolar .nivel-etapa { background: rgba(184,148,74,.15); color: var(--cea-gold-light); }
    .nivel-card--primaria   .nivel-etapa { background: rgba(43,79,122,.4);   color: #90B4E0; }
    .nivel-card--secundaria .nivel-etapa { background: rgba(255,255,255,.07); color: rgba(255,255,255,.7); }

    .nivel-card h3 {
      font-family: var(--font-display);
      font-size: 1.75rem;
      font-weight: 400;
      color: #fff;
      margin-bottom: 14px;
    }

    .nivel-card p {
      font-size: .9375rem;
      line-height: 1.75;
      color: rgba(255,255,255,.55);
      margin-bottom: 28px;
    }

    .nivel-features {
      list-style: none;
      display: flex; flex-direction: column; gap: 8px;
    }
    .nivel-features li {
      display: flex; align-items: flex-start; gap: 10px;
      font-size: .875rem; color: rgba(255,255,255,.6);
    }
    .nivel-features li::before {
      content: '';
      width: 5px; height: 5px;
      background: var(--cea-gold);
      border-radius: 50%;
      margin-top: 7px;
      flex-shrink: 0;
    }

    .nivel-cta {
      margin-top: 28px;
      display: inline-flex; align-items: center; gap: 6px;
      font-size: .8rem; font-weight: 500; letter-spacing: .08em;
      text-transform: uppercase; color: var(--cea-gold-light);
      transition: gap var(--transition);
    }
    .nivel-cta svg { width: 13px; height: 13px; }
    .nivel-cta:hover { gap: 10px; }

    .niveles-bottom {
      text-align: center;
      margin-top: 56px;
    }

    /* =====================================================
       4. PROGRAMA C.A.R.E.
    ===================================================== */
    .care {
      padding: 100px 0;
      background: var(--cea-cream);
    }

    .care-inner {
      display: grid;
      grid-template-columns: 1fr 1.15fr;
      gap: 80px;
      align-items: center;
    }

    .care-text {}

    .care-text h2 {
      font-family: var(--font-display);
      font-size: clamp(1.9rem, 3vw, 2.75rem);
      font-weight: 400;
      color: var(--cea-navy);
      line-height: 1.2;
      margin-bottom: 20px;
    }
    .care-text h2 em { font-style: italic; color: var(--cea-gold); }

    .care-text > p {
      font-size: 1.0625rem;
      line-height: 1.8;
      color: var(--cea-gray-500);
      margin-bottom: 40px;
    }

    .care-services {
      display: flex; flex-direction: column; gap: 4px;
    }

    .care-service {
      display: flex; align-items: flex-start; gap: 16px;
      padding: 18px 20px;
      border-radius: var(--radius-md);
      border: 1px solid transparent;
      transition: var(--transition);
      cursor: default;
    }
    .care-service:hover {
      background: var(--cea-white);
      border-color: var(--cea-gray-200);
      box-shadow: 0 4px 16px rgba(13,31,53,.06);
    }

    .care-service-icon {
      width: 44px; height: 44px;
      background: var(--cea-gold-pale);
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      transition: var(--transition);
    }
    .care-service:hover .care-service-icon {
      background: var(--cea-navy);
    }
    .care-service-icon svg { width: 20px; height: 20px; color: var(--cea-gold); transition: var(--transition); }
    .care-service:hover .care-service-icon svg { color: var(--cea-gold-light); }

    .care-service-body h4 {
      font-size: .9375rem;
      font-weight: 500;
      color: var(--cea-navy);
      margin-bottom: 4px;
    }
    .care-service-body p {
      font-size: .875rem;
      line-height: 1.6;
      color: var(--cea-gray-500);
    }

    /* Right: visual panel */
    .care-visual {
      position: relative;
    }

    .care-panel {
      background: var(--cea-navy);
      border-radius: var(--radius-lg);
      padding: 48px 44px;
      position: relative;
      overflow: hidden;
    }

    .care-panel::before {
      content: '';
      position: absolute; top: 0; right: 0;
      width: 220px; height: 220px;
      background: radial-gradient(circle, rgba(184,148,74,.15) 0%, transparent 70%);
    }

    .care-panel-label {
      font-size: .6875rem; font-weight: 500; letter-spacing: .2em;
      text-transform: uppercase; color: var(--cea-gold);
      margin-bottom: 10px;
    }

    .care-panel-title {
      font-family: var(--font-display);
      font-size: 1.75rem;
      font-weight: 400;
      color: #fff;
      margin-bottom: 8px;
    }

    .care-panel-sub {
      font-size: .9rem;
      color: rgba(255,255,255,.5);
      margin-bottom: 36px;
    }

    .care-hours {
      display: flex; flex-direction: column; gap: 16px;
      margin-bottom: 36px;
    }

    .care-hour-row {
      display: flex; align-items: center; justify-content: space-between;
      padding-bottom: 14px;
      border-bottom: 1px solid rgba(255,255,255,.07);
    }
    .care-hour-row:last-child { border-bottom: none; padding-bottom: 0; }

    .care-hour-label {
      display: flex; align-items: center; gap: 10px;
      font-size: .875rem; color: rgba(255,255,255,.65);
    }
    .care-hour-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--cea-gold); flex-shrink: 0; }
    .care-hour-time { font-size: .875rem; font-weight: 500; color: #fff; }

    .care-panel-badge {
      background: rgba(184,148,74,.15);
      border: 1px solid rgba(184,148,74,.25);
      border-radius: var(--radius-md);
      padding: 14px 18px;
      display: flex; align-items: center; gap: 12px;
    }
    .care-panel-badge svg { width: 20px; height: 20px; color: var(--cea-gold); flex-shrink: 0; }
    .care-panel-badge p { font-size: .875rem; line-height: 1.5; color: rgba(255,255,255,.7); }
    .care-panel-badge strong { color: #fff; }

    .care-deco-shape {
      position: absolute;
      bottom: -30px; left: -30px;
      width: 120px; height: 120px;
      background: rgba(255,255,255,.02);
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,.05);
    }

    /* =====================================================
       5. ADMISIONES CTA STRIP
    ===================================================== */
    .admisiones-strip {
      padding: 80px 0;
      background: var(--cea-white);
      border-top: 1px solid var(--cea-gray-200);
      border-bottom: 1px solid var(--cea-gray-200);
    }

    .admisiones-inner {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 48px;
      align-items: center;
    }

    .admisiones-content {}

    .admisiones-steps {
      display: flex; gap: 0;
      margin-top: 32px;
    }

    .adm-step {
      display: flex; align-items: flex-start; gap: 14px;
      flex: 1;
      position: relative;
    }

    .adm-step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 16px;
      left: calc(32px + 14px);
      right: -32px;
      height: 1px;
      background: linear-gradient(90deg, var(--cea-gold), var(--cea-gray-200));
      z-index: 0;
    }

    .adm-step-num {
      width: 32px; height: 32px;
      background: var(--cea-navy);
      color: var(--cea-white);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: .75rem; font-weight: 600;
      flex-shrink: 0;
      position: relative; z-index: 1;
    }
    .adm-step:first-child .adm-step-num { background: var(--cea-gold); }

    .adm-step-body {}
    .adm-step-body h4 {
      font-size: .875rem; font-weight: 500;
      color: var(--cea-navy); margin-bottom: 4px;
    }
    .adm-step-body p { font-size: .8125rem; color: var(--cea-gray-500); line-height: 1.5; }

    .admisiones-aside {
      background: var(--cea-gold-pale);
      border: 1px solid rgba(184,148,74,.25);
      border-radius: var(--radius-lg);
      padding: 36px 36px;
      text-align: center;
      min-width: 240px;
    }

    .admisiones-aside .year {
      font-family: var(--font-display);
      font-size: 3rem; font-weight: 300;
      color: var(--cea-navy); line-height: 1;
    }
    .admisiones-aside .year-label {
      font-size: .75rem; font-weight: 500; letter-spacing: .12em;
      text-transform: uppercase; color: var(--cea-gold);
      margin-bottom: 20px;
    }
    .admisiones-aside p {
      font-size: .875rem; color: var(--cea-gray-500);
      line-height: 1.6; margin-bottom: 20px;
    }

    /* =====================================================
       6. IDENTIDAD & VALORES
    ===================================================== */
    .valores {
      padding: 96px 0;
      background: var(--cea-cream);
    }

    .valores-inner {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 96px;
      align-items: start;
    }

    .valores-left h2 {
      font-family: var(--font-display);
      font-size: clamp(2rem, 3vw, 2.75rem);
      font-weight: 400;
      color: var(--cea-navy);
      line-height: 1.2;
      margin-bottom: 20px;
    }
    .valores-left h2 em { font-style: italic; color: var(--cea-gold); }

    .valores-left > p {
      font-size: 1.0625rem;
      line-height: 1.8;
      color: var(--cea-gray-500);
      margin-bottom: 36px;
    }

    .historia-stat {
      display: flex; gap: 32px;
      padding-top: 32px;
      border-top: 1px solid var(--cea-gray-200);
    }
    .hist-item {}
    .hist-item .num {
      font-family: var(--font-display);
      font-size: 2.25rem; font-weight: 400;
      color: var(--cea-navy); line-height: 1;
    }
    .hist-item .num em { font-style: italic; color: var(--cea-gold); font-size: 1.5rem; }
    .hist-item span {
      display: block; font-size: .75rem; font-weight: 500; letter-spacing: .1em;
      text-transform: uppercase; color: var(--cea-gray-500); margin-top: 4px;
    }

    /* Right: valores cards */
    .valores-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .valor-card {
      background: var(--cea-white);
      border: 1px solid var(--cea-gray-200);
      border-radius: var(--radius-lg);
      padding: 28px 24px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .valor-card:hover {
      border-color: var(--cea-gold);
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(13,31,53,.07);
    }

    .valor-num {
      font-family: var(--font-display);
      font-size: 3rem; font-weight: 300;
      color: var(--cea-gray-200);
      line-height: 1;
      position: absolute; top: 16px; right: 20px;
    }

    .valor-emoji {
      font-size: 1.5rem;
      margin-bottom: 12px;
    }

    .valor-card h3 {
      font-family: var(--font-display);
      font-size: 1.3125rem;
      font-weight: 500;
      color: var(--cea-navy);
      margin-bottom: 8px;
    }

    .valor-card p {
      font-size: .875rem;
      line-height: 1.65;
      color: var(--cea-gray-500);
    }

    /* =====================================================
       7. CTA FINAL (BOTTOM BANNER)
    ===================================================== */
    .cta-final {
      padding: 96px 0;
      background: linear-gradient(135deg, var(--cea-navy) 0%, #1a3a6b 100%);
      position: relative;
      overflow: hidden;
    }

    .cta-final::before {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(184,148,74,.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(184,148,74,.05) 1px, transparent 1px);
      background-size: 60px 60px;
    }

    .cta-final-inner {
      position: relative; z-index: 1;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 64px;
      align-items: center;
    }

    .cta-final h2 {
      font-family: var(--font-display);
      font-size: clamp(1.9rem, 3.5vw, 3rem);
      font-weight: 400;
      color: #fff;
      line-height: 1.2;
      margin-bottom: 14px;
    }
    .cta-final h2 em { font-style: italic; color: var(--cea-gold-light); }

    .cta-final p {
      font-size: 1.0625rem;
      color: rgba(255,255,255,.55);
      max-width: 540px;
    }

    .cta-final-actions {
      display: flex; flex-direction: column; gap: 12px;
      align-items: flex-end;
      flex-shrink: 0;
    }

    .cta-contact-hint {
      display: flex; align-items: center; gap: 8px;
      font-size: .8125rem;
      color: rgba(255,255,255,.4);
    }
    .cta-contact-hint svg { width: 14px; height: 14px; }

    /* =====================================================
       RESPONSIVE
    ===================================================== */
    @media (max-width: 1024px) {
      .hero-inner { grid-template-columns: 1fr; gap: 48px; }
      .hero-visual { max-width: 520px; }
      .porque-header { grid-template-columns: 1fr; }
      .care-inner { grid-template-columns: 1fr; }
      .admisiones-inner { grid-template-columns: 1fr; }
      .valores-inner { grid-template-columns: 1fr; gap: 56px; }
      .cta-final-inner { grid-template-columns: 1fr; gap: 36px; }
      .cta-final-actions { align-items: flex-start; flex-direction: row; }
    }

    @media (max-width: 768px) {
      .pilares-grid { grid-template-columns: 1fr; }
      .niveles-grid { grid-template-columns: 1fr; }
      .valores-grid { grid-template-columns: 1fr; }
      .admisiones-steps { flex-direction: column; gap: 20px; }
      .adm-step::after { display: none; }
      .historia-stat { gap: 24px; flex-wrap: wrap; }
      .hero-stat-card--years { bottom: -16px; left: 16px; }
      .hero-stat-card--trust { top: 16px; right: 16px; }
      .hero::after { height: 48px; }
    }

    @media (max-width: 480px) {
      .care-panel { padding: 32px 24px; }
      .cta-final-actions { flex-direction: column; }
    }
  </style>
</head>
<body>

<!-- ════════════════════════════════════════════════════════════
     NOTA: Este archivo contiene SOLO el <main>.
     En producción, envuelve con la plantilla base (header/footer).
════════════════════════════════════════════════════════════ -->

<main id="main-content">

  <!-- ================================================
       SECCIÓN 1 · HERO
  ================================================= -->
  <section class="hero" aria-labelledby="hero-title">
    <div class="hero-bg"></div>
    <div class="hero-bg-grid" aria-hidden="true"></div>
    <div class="hero-bg-glow" aria-hidden="true"></div>
    <div class="hero-bg-glow2" aria-hidden="true"></div>

    <div class="container">
      <div class="hero-inner">

        <!-- Copy -->
        <div class="hero-text">
          <div class="hero-badge">
            <span></span>
            Ciclo Escolar 2025–2026 · Admisiones Abiertas
          </div>

          <h1 class="hero-title" id="hero-title">
            Educación, Formación<br>
            y <em>Desarrollo</em><br>en un entorno seguro
          </h1>

          <p class="hero-subtitle">
            Formamos ciudadanos íntegros mientras te brindamos la tranquilidad de que tu hijo está seguro, aprendiendo y siendo conocido por su nombre.
          </p>

          <div class="hero-actions">
            <a href="/admisiones" class="btn btn-primary">
              Conoce el proceso de Admisión
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="/nosotros" class="btn btn-ghost-white">Conoce CEA</a>
          </div>
        </div>

        <!-- Visual -->
        <div class="hero-visual">
          <div class="hero-img-frame">
            <!-- Reemplaza con: <img src="/img/alumnos-hero.jpg" alt="Alumnos CEA" style="width:100%;height:100%;object-fit:cover"> -->
            <div class="hero-img-placeholder" aria-label="Foto de alumnos CEA">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              <span>Foto institucional · Alta resolución</span>
            </div>
          </div>

          <!-- Stat: años de experiencia -->
          <div class="hero-stat-card hero-stat-card--years">
            <div class="stat-number">28<span>+</span></div>
            <div class="stat-label">Años formando<br>ciudadanos íntegros</div>
          </div>

          <!-- Trust indicator -->
          <div class="hero-stat-card hero-stat-card--trust">
            <div class="trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
            </div>
            <div>
              <div class="stat-number" style="font-size:1.25rem">Entorno<br>Seguro</div>
              <div class="stat-label">Certificado</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- ================================================
       SECCIÓN 2 · POR QUÉ CEA
  ================================================= -->
  <section class="porque-cea" aria-labelledby="porque-title">
    <div class="container">

      <div class="porque-header">
        <div>
          <div class="section-label reveal">Por qué elegirnos</div>
          <h2 id="porque-title" class="reveal reveal-delay-1">
            La escuela que conoce<br>
            a tu hijo <em>por su nombre</em>
          </h2>
        </div>
        <p class="reveal reveal-delay-2">
          No somos una escuela donde tu hijo es "un número más". Cada alumno es una persona que merece atención personalizada, comunicación real y un ambiente donde puede crecer seguro.
        </p>
      </div>

      <div class="pilares-grid">

        <!-- Pilar 1 -->
        <div class="pilar-card reveal">
          <div class="pilar-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <h3>Atención Personalizada</h3>
          <p>Grupos reducidos donde cada maestro conoce el ritmo, las fortalezas y las áreas de oportunidad de cada alumno. Tu hijo nunca se perderá en la multitud.</p>
          <span class="pilar-tag">Grupos reducidos</span>
        </div>

        <!-- Pilar 2 -->
        <div class="pilar-card reveal reveal-delay-1">
          <div class="pilar-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <h3>Comunicación Abierta</h3>
          <p>Canales directos y ágiles con docentes y directivos. Plataforma digital para seguimiento académico en tiempo real, sin intermediarios ni burocracia.</p>
          <span class="pilar-tag">Plataforma CEA · SIGE</span>
        </div>

        <!-- Pilar 3 -->
        <div class="pilar-card reveal reveal-delay-2">
          <div class="pilar-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
          </div>
          <h3>Seguridad Integral</h3>
          <p>Protocolos de seguridad que abarcan desde el acceso a las instalaciones hasta el bienestar emocional. Tu tranquilidad es nuestra prioridad número uno.</p>
          <span class="pilar-tag">Seguridad certificada</span>
        </div>

      </div>
    </div>
  </section>


  <!-- ================================================
       SECCIÓN 3 · NIVELES EDUCATIVOS
  ================================================= -->
  <section class="niveles" aria-labelledby="niveles-title">
    <div class="container niveles-inner">

      <div class="niveles-header reveal">
        <div class="section-label" style="justify-content:center; color:var(--cea-gold-light)">
          <span style="background:var(--cea-gold-light)"></span>
          El camino de crecimiento
        </div>
        <h2 id="niveles-title">Acompaña a tu hijo<br>en <em>cada etapa</em></h2>
        <p>Desde la curiosidad más temprana hasta la preparación para el futuro. Un proyecto educativo continuo y coherente.</p>
      </div>

      <div class="niveles-grid">

        <!-- Preescolar -->
        <div class="nivel-card nivel-card--preescolar reveal">
          <div class="nivel-number" aria-hidden="true">01</div>
          <span class="nivel-etapa">Preescolar</span>
          <h3>Descubrimiento y Curiosidad</h3>
          <p>Un ambiente cálido y seguro donde la curiosidad natural de cada niño es el motor del aprendizaje.</p>
          <ul class="nivel-features">
            <li>Estimulación temprana integral</li>
            <li>Ambiente de confianza y exploración</li>
            <li>Desarrollo socioemocional guiado</li>
            <li>Transición suave a Primaria</li>
          </ul>
          <a href="/niveles/preescolar" class="nivel-cta">
            Conocer Preescolar
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

        <!-- Primaria -->
        <div class="nivel-card nivel-card--primaria reveal reveal-delay-1">
          <div class="nivel-number" aria-hidden="true">02</div>
          <span class="nivel-etapa">Primaria</span>
          <h3>Habilidades y Pertenencia</h3>
          <p>Consolidamos las habilidades fundamentales y fortalecemos el sentido de responsabilidad y comunidad.</p>
          <ul class="nivel-features">
            <li>Habilidades académicas sólidas</li>
            <li>Responsabilidad y autonomía</li>
            <li>Sentido de pertenencia al grupo</li>
            <li>Formación en valores CEA</li>
          </ul>
          <a href="/niveles/primaria" class="nivel-cta">
            Conocer Primaria
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

        <!-- Secundaria -->
        <div class="nivel-card nivel-card--secundaria reveal reveal-delay-2">
          <div class="nivel-number" aria-hidden="true">03</div>
          <span class="nivel-etapa">Secundaria</span>
          <h3>Liderazgo y Pensamiento Crítico</h3>
          <p>Preparamos a jóvenes con liderazgo ético, capacidad analítica y visión clara para su futuro.</p>
          <ul class="nivel-features">
            <li>Liderazgo ético y consciente</li>
            <li>Pensamiento crítico aplicado</li>
            <li>Preparación para educación media</li>
            <li>Proyectos de impacto comunitario</li>
          </ul>
          <a href="/niveles/secundaria" class="nivel-cta">
            Conocer Secundaria
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

      </div>

      <div class="niveles-bottom reveal">
        <a href="/niveles" class="btn btn-outline-navy" style="border-color:rgba(255,255,255,.3); color:#fff;">
          Ver todos los niveles educativos
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>

    </div>
  </section>


  <!-- ================================================
       SECCIÓN 4 · PROGRAMA C.A.R.E.
  ================================================= -->
  <section class="care" aria-labelledby="care-title">
    <div class="container">
      <div class="care-inner">

        <!-- Left -->
        <div class="care-text">
          <div class="section-label reveal">Servicios complementarios</div>
          <h2 id="care-title" class="reveal reveal-delay-1">
            Programa <em>C.A.R.E.</em><br>
            Tu aliado logístico
          </h2>
          <p class="reveal reveal-delay-2">
            Sabemos que los papás que trabajan necesitan más que una buena educación: necesitan un aliado que cuide a sus hijos con la misma dedicación que ellos ponen en su trabajo.
          </p>

          <div class="care-services">

            <div class="care-service reveal">
              <div class="care-service-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="1" y="3" width="15" height="13" rx="2"/>
                  <path d="M16 8h4l3 5v3h-7V8z M5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z M18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                </svg>
              </div>
              <div class="care-service-body">
                <h4>Transporte Escolar Seguro</h4>
                <p>Unidades propias con GPS y personal capacitado. Rutas monitoreadas en tiempo real para tu tranquilidad.</p>
              </div>
            </div>

            <div class="care-service reveal reveal-delay-1">
              <div class="care-service-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 2h1l3 10h10l3-10h1M8 12l1 6h6l1-6"/>
                  <circle cx="9" cy="21" r="1"/><circle cx="15" cy="21" r="1"/>
                </svg>
              </div>
              <div class="care-service-body">
                <h4>Comedor Escolar</h4>
                <p>Menús equilibrados diseñados por nutriólogos para el máximo rendimiento académico y bienestar de tus hijos.</p>
              </div>
            </div>

            <div class="care-service reveal reveal-delay-2">
              <div class="care-service-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
              </div>
              <div class="care-service-body">
                <h4>Estancia Extendida · After School</h4>
                <p>Cuidado supervisado hasta las 5:00 p.m. Úsalo de manera permanente o esporádica, según lo necesites.</p>
              </div>
            </div>

          </div>
        </div>

        <!-- Right panel -->
        <div class="care-visual reveal reveal-delay-1">
          <div class="care-panel">
            <div class="care-deco-shape" aria-hidden="true"></div>
            <div class="care-panel-label">Horarios del Programa</div>
            <div class="care-panel-title">C.A.R.E.</div>
            <div class="care-panel-sub">Cuidado · Alimentación · Ruta · Estancia</div>

            <div class="care-hours">
              <div class="care-hour-row">
                <span class="care-hour-label">
                  <span class="care-hour-dot"></span>
                  Entrada y Desayuno
                </span>
                <span class="care-hour-time">7:00 – 8:00 am</span>
              </div>
              <div class="care-hour-row">
                <span class="care-hour-label">
                  <span class="care-hour-dot"></span>
                  Jornada Escolar
                </span>
                <span class="care-hour-time">8:00 – 2:30 pm</span>
              </div>
              <div class="care-hour-row">
                <span class="care-hour-label">
                  <span class="care-hour-dot"></span>
                  Comedor
                </span>
                <span class="care-hour-time">12:30 – 1:30 pm</span>
              </div>
              <div class="care-hour-row">
                <span class="care-hour-label">
                  <span class="care-hour-dot" style="background:#4CAF50"></span>
                  Estancia Extendida
                </span>
                <span class="care-hour-time">2:30 – 5:00 pm</span>
              </div>
            </div>

            <div class="care-panel-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <p><strong>Servicio flexible:</strong> Usa la estancia extendida de forma permanente o solo los días que lo necesites.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- ================================================
       SECCIÓN 5 · ADMISIONES STRIP
  ================================================= -->
  <section class="admisiones-strip" aria-labelledby="admisiones-title">
    <div class="container">
      <div class="admisiones-inner">

        <div class="admisiones-content">
          <div class="section-label reveal">Proceso de Admisión</div>
          <h2 id="admisiones-title" class="reveal reveal-delay-1" style="font-family:var(--font-display);font-size:clamp(1.75rem,3vw,2.5rem);font-weight:400;color:var(--cea-navy);line-height:1.2;margin-bottom:8px;">
            Asegura el lugar de tu hijo
          </h2>
          <p class="reveal reveal-delay-2" style="font-size:1.0625rem;color:var(--cea-gray-500);margin-bottom:0">
            Un proceso simple en 3 pasos. Sin filas, sin papeleo interminable.
          </p>

          <div class="admisiones-steps">

            <div class="adm-step reveal">
              <div class="adm-step-num">1</div>
              <div class="adm-step-body">
                <h4>Pre-alta en Línea</h4>
                <p>Llena el formulario con los datos esenciales. Solo toma 5 minutos.</p>
              </div>
            </div>

            <div class="adm-step reveal reveal-delay-1">
              <div class="adm-step-num">2</div>
              <div class="adm-step-body">
                <h4>Asegura tu Lugar</h4>
                <p>Sube el comprobante de pago de inscripción para reservar el lugar.</p>
              </div>
            </div>

            <div class="adm-step reveal reveal-delay-2">
              <div class="adm-step-num">3</div>
              <div class="adm-step-body">
                <h4>Formaliza en 10 min</h4>
                <p>Visita nuestras oficinas. Los datos ya están en SIGE; solo firmas.</p>
              </div>
            </div>

          </div>
        </div>

        <div class="admisiones-aside reveal reveal-delay-2">
          <div class="year">2026</div>
          <div class="year-label">Ciclo escolar</div>
          <p>Lugares limitados.<br>Proceso de admisión activo.</p>
          <a href="/admisiones" class="btn btn-primary" style="width:100%;justify-content:center;">
            Iniciar Pre-alta
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

      </div>
    </div>
  </section>


  <!-- ================================================
       SECCIÓN 6 · IDENTIDAD & VALORES
  ================================================= -->
  <section class="valores" aria-labelledby="valores-title">
    <div class="container">
      <div class="valores-inner">

        <div class="valores-left">
          <div class="section-label reveal">Nuestra identidad</div>
          <h2 id="valores-title" class="reveal reveal-delay-1">
            Más de dos décadas<br>
            formando <em>ciudadanos íntegros</em>
          </h2>
          <p class="reveal reveal-delay-2">
            Fundado en 1997, CEA nació de la convicción de que la educación de calidad debe ir acompañada de formación en valores, comunicación real con las familias y un ambiente donde cada niño se sienta seguro y visto.
          </p>
          <p class="reveal reveal-delay-2" style="margin-bottom:0; color:var(--cea-gray-500); font-size:1.0625rem; line-height:1.8;">
            <strong style="color:var(--cea-navy)">Nuestra misión:</strong> Formar personas íntegras con habilidades académicas sólidas, valores firmes y capacidad de impactar positivamente su entorno.
          </p>

          <div class="historia-stat reveal reveal-delay-3">
            <div class="hist-item">
              <div class="num">28<em>+</em></div>
              <span>Años de trayectoria</span>
            </div>
            <div class="hist-item">
              <div class="num">3</div>
              <span>Niveles educativos</span>
            </div>
            <div class="hist-item">
              <div class="num">4</div>
              <span>Valores rectores</span>
            </div>
          </div>
        </div>

        <div class="valores-grid">

          <div class="valor-card reveal">
            <div class="valor-num" aria-hidden="true">01</div>
            <div class="valor-emoji">🛡️</div>
            <h3>Seguridad</h3>
            <p>Entornos físicos y emocionales seguros donde cada alumno puede aprender sin miedo y con confianza.</p>
          </div>

          <div class="valor-card reveal reveal-delay-1">
            <div class="valor-num" aria-hidden="true">02</div>
            <div class="valor-emoji">🤝</div>
            <h3>Empatía</h3>
            <p>Escuchamos, comprendemos y actuamos con sensibilidad hacia las necesidades únicas de cada familia.</p>
          </div>

          <div class="valor-card reveal reveal-delay-2">
            <div class="valor-num" aria-hidden="true">03</div>
            <div class="valor-emoji">⚖️</div>
            <h3>Responsabilidad</h3>
            <p>Asumimos con seriedad el compromiso que los padres depositan en nosotros cada día que nos confían a sus hijos.</p>
          </div>

          <div class="valor-card reveal reveal-delay-3">
            <div class="valor-num" aria-hidden="true">04</div>
            <div class="valor-emoji">🌱</div>
            <h3>Mejora Continua</h3>
            <p>Revisamos, aprendemos y evolucionamos de manera constante para ofrecer siempre lo mejor a nuestra comunidad.</p>
          </div>

        </div>

      </div>
    </div>
  </section>


  <!-- ================================================
       SECCIÓN 7 · CTA FINAL
  ================================================= -->
  <section class="cta-final" aria-labelledby="cta-title">
    <div class="container">
      <div class="cta-final-inner">

        <div class="reveal">
          <div class="section-label" style="color:var(--cea-gold-light);"><span style="background:var(--cea-gold)"></span>Da el primer paso</div>
          <h2 id="cta-title">
            Tu hijo merece una escuela<br>
            que lo <em>conozca por su nombre</em>
          </h2>
          <p>El proceso de admisión es simple, rápido y sin complicaciones. Empieza hoy y asegura su lugar para el ciclo 2026.</p>
        </div>

        <div class="cta-final-actions reveal reveal-delay-1">
          <a href="/admisiones" class="btn btn-primary">
            Iniciar Pre-alta 2026
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
          <a href="https://wa.me/520000000000" class="btn btn-ghost-white">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:15px;height:15px;">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
            </svg>
            Escríbenos por WhatsApp
          </a>
          <div class="cta-contact-hint">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.34 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.72 16z"/></svg>
            O llámanos al (000) 000-0000
          </div>
        </div>

      </div>
    </div>
  </section>

</main>


<script>
  // ── Scroll Reveal ──────────────────────────────────────
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
</script>

</body>
</html>