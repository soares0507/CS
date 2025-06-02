<!DOCTYPE html>
<html lang="pt-BR" class="snap-container"> <head>
  <meta charset="UTF-8">
  <title>Bem-vindo ao Circuito Sustentável - Inovação e Consciência</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <style>
    /* Reset Básico e Configurações Globais */
    :root {
        --verde: #28a060;
        --verde-escuro: #1e7c4b;
        --verde-claro-fundo: #f0f9f4;
        --verde-destaque-hero: #6ef2a5;
        --verde-destaque-parallax: #8cffc7;
        --cinza-claro: #f4f6f8;
        --cinza-texto: #5f6c7b;
        --cinza-escuro: #2c3e50;
        --branco: #ffffff;
        --sombra-padrao: 0 8px 25px rgba(40, 160, 96, 0.07);
        --sombra-hover-forte: 0 12px 35px rgba(40, 160, 96, 0.18);
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --transition-fast: 0.2s;
        --transition-std: 0.4s;
        --transition-long: 0.6s;
        --font-principal: 'Poppins', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html.snap-container {
        scroll-behavior: smooth;
        scroll-snap-type: y proximity; /* ALTERADO AQUI para permitir acesso ao footer */
    }

    body {
        font-family: var(--font-principal);
        line-height: 1.7;
        color: var(--cinza-texto);
        background-color: var(--branco);
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

  

    .scroll-snap-section {
        scroll-snap-align: start;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 100px 0;
        position: relative;
        overflow: hidden; /* Mantido para elementos internos, como as shapes do hero */
    }
    .hero-section {
        padding-top: 0;
    }

    /* Garante que o footer não seja um ponto de snap e tenha espaço para ser visto */
    .site-footer-bottom {
        /* scroll-snap-align: none;  Opcional, geralmente não necessário com proximity */
        padding: 70px 0 40px; /* Padding já existente */
        /* Adicionar um min-height pode ajudar em alguns casos extremos, mas geralmente não é preciso com proximity */
        /* min-height: 50vh; */
    }


    .container { width: 90%; max-width: 1140px; margin: 0 auto; padding: 0 20px; }

    /* Botões Globais */
    .btn {
        display: inline-block; padding: 14px 32px; font-weight: 600;
        text-decoration: none; border-radius: var(--border-radius-md);
        transition: all var(--transition-std) cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer; border: 2px solid transparent; font-size: 1em;
        box-shadow: var(--sombra-padrao); letter-spacing: 0.5px;
    }
    .btn-primary { background-color: var(--verde); color: var(--branco); }
    .btn-primary:hover {
        background-color: var(--verde-escuro);
        transform: translateY(-4px) scale(1.03);
        box-shadow: var(--sombra-hover-forte);
    }
    .btn-outline { background-color: transparent; color: var(--verde); border-color: var(--verde); }
    .btn-outline:hover {
        background-color: var(--verde); color: var(--branco);
        transform: translateY(-4px) scale(1.03);
        box-shadow: var(--sombra-hover-forte);
    }
    .btn-large { padding: 18px 45px; font-size: 1.15em; }
    .btn-pulse { animation: pulseAnimation 2s infinite ease-in-out; }

    @keyframes pulseAnimation {
        0% { transform: scale(1); box-shadow: var(--sombra-padrao); }
        50% { transform: scale(1.05); box-shadow: var(--sombra-hover-forte); }
        100% { transform: scale(1); box-shadow: var(--sombra-padrao); }
    }

    /* Header */
    .site-header {
        position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
        padding: 18px 0;
        background-color: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        transition: background-color var(--transition-std), box-shadow var(--transition-std), padding var(--transition-std);
    }
    .site-header.scrolled {
        background-color: var(--branco);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 12px 0;
    }
    .header-container { display: flex; align-items: center; justify-content: space-between; }
    .logo { width: 170px; transition: transform var(--transition-std); }
    .logo:hover { transform: scale(1.05) rotate(-2deg); }

    .main-nav { display: flex; gap: 30px; }
    .main-nav a {
        color: var(--cinza-escuro); font-weight: 500; text-decoration: none;
        position: relative; padding-bottom: 8px; font-size: 0.95em;
    }
    .main-nav a::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 0%; height: 2.5px;
        background-color: var(--verde);
        transition: width var(--transition-long) cubic-bezier(0.19, 1, 0.22, 1);
    }
    .main-nav a:hover::after, .main-nav a.active::after { width: 100%; }
    .main-nav a:hover { color: var(--verde); }
    .auth-buttons-header { display: flex; gap: 12px; }

    .menu-toggle { display: none; background: none; border: none; cursor: pointer; padding: 10px; z-index: 1001; }
    .menu-toggle span { display: block; width: 28px; height: 3px; background-color: var(--cinza-escuro); margin: 6px 0; transition: all var(--transition-std); }
    .menu-toggle.active span:nth-child(1) { transform: translateY(9px) rotate(45deg); }
    .menu-toggle.active span:nth-child(2) { opacity: 0; transform: translateX(-10px); }
    .menu-toggle.active span:nth-child(3) { transform: translateY(-9px) rotate(-45deg); }

    /* 1. Hero Section */
    .hero-section {
        background: linear-gradient(135deg, var(--verde-claro-fundo) 0%, var(--cinza-claro) 100%);
        color: var(--cinza-escuro); text-align: center;
    }
    .hero-background-shapes { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: 0;}
    .hero-background-shapes .shape {
        position: absolute; border-radius: 50%; opacity: 0.1;
        background: var(--verde);
        transition: transform 0.1s linear;
    }
    .shape-1 { width: 200px; height: 200px; top: 10%; left: 15%; }
    .shape-2 { width: 100px; height: 100px; bottom: 20%; right: 10%; background: var(--verde-destaque-hero); }
    .shape-3 { width: 150px; height: 150px; top: 50%; left: 5%; opacity: 0.05; }

    .hero-content { position: relative; z-index: 1; }
    .hero-tag {
        display: inline-block; padding: 6px 15px; background-color: rgba(40, 160, 96, 0.1);
        color: var(--verde-escuro); border-radius: var(--border-radius-sm);
        font-size: 0.85em; font-weight: 600; letter-spacing: 1px;
        margin-bottom: 25px; text-transform: uppercase;
    }
    .hero-title {
        font-size: clamp(3rem, 7vw, 5.2rem); font-weight: 800; line-height: 1.15;
        margin-bottom: 25px; color: var(--cinza-escuro);
    }
    .highlight-green { color: var(--verde); }
    .hero-subtitle {
        font-size: clamp(1.1rem, 2.5vw, 1.5rem); font-weight: 400;
        max-width: 750px; margin: 0 auto 45px; color: var(--cinza-texto);
    }
    .btn-hero {
        padding: 20px 50px; font-size: 1.25em;
        background-color: var(--verde); color: var(--branco);
        box-shadow: 0 8px 25px rgba(40,160,96,0.3);
    }
    .btn-hero:hover {
        background-color: var(--verde-escuro); color: var(--branco);
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 12px 35px rgba(40,160,96,0.4);
    }
    .scroll-down-indicator {
        position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%);
        color: var(--verde); opacity: 0.6;
        transition: opacity var(--transition-std), transform var(--transition-std);
        animation: bounceUpDown 2.5s infinite ease-in-out;
    }
    .scroll-down-indicator:hover { opacity: 1; transform: translateX(-50%) scale(1.15); }
    @keyframes bounceUpDown {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-12px); }
    }

    .animate-fade-in-down { opacity: 0; transform: translateY(-30px); animation: fadeInDown 1s var(--transition-std) forwards; }
    @keyframes fadeInDown { to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { opacity: 0; transform: translateY(30px); animation: fadeInUp 1s var(--transition-std) forwards; }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    .delay-1 { animation-delay: 0.5s !important; }
    .delay-2 { animation-delay: 1s !important; }

    /* 2. Features Section */
    .features-section { background-color: var(--branco); }
    .section-title {
        font-size: clamp(2.2rem, 4.5vw, 3.2rem); color: var(--cinza-escuro);
        font-weight: 700; text-align: center; margin-bottom: 20px;
    }
    .section-subtitle {
        font-size: clamp(1rem, 2vw, 1.25rem); color: var(--cinza-texto);
        text-align: center; max-width: 750px; margin: 0 auto 70px;
    }
    .features-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 35px;
    }
    .feature-card {
        background-color: var(--branco); padding: 40px;
        border-radius: var(--border-radius-lg); box-shadow: var(--sombra-padrao);
        text-align: left;
        transition: transform var(--transition-std) cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow var(--transition-std) cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid var(--cinza-claro);
    }
    .feature-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 50px rgba(40,160,96,0.12);
        border-color: var(--verde-claro-fundo);
    }
    .feature-icon-wrapper {
        display: inline-flex; background-color: var(--verde-claro-fundo);
        padding: 18px; border-radius: 50%;
        margin-bottom: 25px; color: var(--verde);
    }
    .feature-icon { width: 36px; height: 36px; }
    .feature-card h3 {
        font-size: 1.6em; color: var(--cinza-escuro); font-weight: 600;
        margin-bottom: 12px;
    }
    .feature-card p { font-size: 0.95em; line-height: 1.8; }

    .animate-on-scroll { opacity: 0; transform: translateY(50px); transition: opacity 0.8s ease-out, transform 0.8s ease-out; }
    .animate-on-scroll.in-view { opacity: 1; transform: translateY(0); }
    .animate-on-scroll.delay-0-2s { transition-delay: 0.2s; }
    .animate-on-scroll.delay-0-4s { transition-delay: 0.4s; }
    .animate-on-scroll.delay-0-6s { transition-delay: 0.6s; }
    .animate-on-scroll.delay-0-8s { transition-delay: 0.8s; }

    /* 3. Nossa Visão Section (Antiga Parallax/Impact Section) */
    .parallax-section {
        background-image: url('https://images.unsplash.com/photo-1476231682828-37e571bc172f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1674&q=80');
        background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover;
        text-align: center;
    }
    .parallax-content {
        background-color: rgba(10, 25, 41, 0.70);
        padding: 80px 40px; border-radius: var(--border-radius-lg);
        display: inline-block; max-width: 850px;
    }
    .light-text { color: var(--branco); text-shadow: 0 2px 8px rgba(0,0,0,0.6); }
    .highlight-parallax { color: var(--verde-destaque-parallax); }

    .vision-text-block {
        margin-top: 30px; text-align: justify;
        font-size: 1.05em; line-height: 1.8;
        opacity: 0.95;
    }
    .vision-text-block p {
        margin-bottom: 20px;
    }
    .vision-quote {
        font-size: 1.1em; font-style: italic;
        color: var(--cinza-claro); padding-top: 15px;
        border-top: 1px solid rgba(255,255,255,0.2);
        margin-top: 25px; text-align: center;
    }
    .vision-quote em::before { content: "“"; margin-right: 5px; font-size: 1.2em; }
    .vision-quote em::after { content: "”"; margin-left: 5px; font-size: 1.2em; }

    /* 4. CTA Section */
    .cta-section { background-color: var(--cinza-claro); }
    .cta-container-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        align-items: center; gap: 60px; text-align: left;
    }
    .cta-image-side img {
        max-width: 100%; height: auto; border-radius: var(--border-radius-lg);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
    .cta-text-content .section-title, .cta-text-content .section-subtitle { text-align: left; margin-left:0; margin-right:0; }
    .cta-action { margin-top: 30px; }
    .cta-login { margin-top: 25px; font-size: 0.95em; }
    .cta-login a { color: var(--verde); font-weight: 600; text-decoration: none; }
    .cta-login a:hover { text-decoration: underline; color: var(--verde-escuro); }

    /* Footer */
    .site-footer-bottom {
        background-color: var(--cinza-escuro); color: #b0bec5;
        /* scroll-snap-align: end; */ /* Pode ajudar, mas proximity é mais importante */
        padding: 70px 0 40px; font-size: 0.95em;
    }
    .footer-content-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 45px; margin-bottom: 50px;
    }
    .footer-col h4 { font-size: 1.25em; color: var(--branco); font-weight: 600; margin-bottom: 20px; }
    .footer-col p, .footer-col a { color: #b0bec5; text-decoration: none; margin-bottom: 10px; display: block; }
    .footer-col a:hover { color: var(--verde); transform: translateX(3px); transition: transform var(--transition-fast); }
    .footer-copyright { text-align: center; padding-top: 40px; border-top: 1px solid #4a5c6a; color: #78909c; }

    /* Responsividade */
    @media (max-width: 992px) {
        /* html.snap-container { scroll-snap-type: y proximity; } /* Mantido proximity para mobile */
        .main-nav, .auth-buttons-header {
            display: none; flex-direction: column; position: absolute;
            top: 100%; left: 0; width: 100%;
            background-color: var(--branco); padding: 20px 0;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            text-align: center; gap: 0;
        }
        .main-nav.active, .auth-buttons-header.active { display: flex; }
        .main-nav a { padding: 15px 20px; width: 100%; }
        .auth-buttons-header { top: calc(100% + (4 * 58px)); /* Ajuste (num_links * altura_aprox_link) */ }
        .auth-buttons-header .btn { width: calc(100% - 40px); margin: 10px 20px; }
        .menu-toggle { display: block; }

        .scroll-snap-section { padding: 80px 0; }
        .cta-container-grid { grid-template-columns: 1fr; text-align: center; }
        .cta-image-side { order: -1; margin-bottom: 40px; }
        .cta-text-content .section-title, .cta-text-content .section-subtitle { text-align: center; }
    }

    @media (max-width: 768px) {
        .features-grid { grid-template-columns: 1fr; gap: 25px; }
        .feature-card { padding: 30px; }
        .parallax-content { padding: 60px 25px; }
        .vision-text-block { font-size: 1em; }
        .vision-quote { font-size: 1em; }
        .hero-title { font-size: 2.5rem; }
        .hero-subtitle { font-size: 1rem; }
        .section-title { font-size: 2rem; }
        .section-subtitle { font-size: 0.95rem; margin-bottom: 50px; }
    }
  </style>
</head>
<body>

  <header class="site-header">
    <div class="container header-container">
      <div class="logo-container">
        <a href="#hero"><img src="img/logo2.png" alt="Logo Circuito Sustentável" class="logo"></a>
      </div>
      <nav class="main-nav">
        <a href="#features">Como Funciona</a>
        <a href="#nossa-visao">Nossa Visão</a>
        <a href="#cta">Participe</a>
        <a href="loja.php">Loja</a>
      </nav>
      <div class="auth-buttons-header">
        <button class="btn btn-outline" onclick="location.href='login.php'">Entrar</button>
        <button class="btn btn-primary" onclick="location.href='cadastro.php'">Cadastrar</button>
      </div>
      <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <main>

    <section id="hero" class="hero-section scroll-snap-section">
      <div class="hero-background-shapes">
          <div class="shape shape-1"></div>
          <div class="shape shape-2"></div>
          <div class="shape shape-3"></div>
      </div>
      <div class="container hero-content">
        <span class="hero-tag animate-fade-in-down">INOVAÇÃO SUSTENTÁVEL</span>
        <h1 class="hero-title animate-fade-in-up">Transforme o <span class="highlight-green">Planeta</span>,<br>Começando em <span class="highlight-green">Casa</span>.</h1>
        <p class="hero-subtitle animate-fade-in-up delay-1">O Circuito Sustentável é seu guia para um estilo de vida mais consciente, prático e recompensador.</p>
        <a href="#features" class="btn btn-hero animate-fade-in-up delay-2">Descubra Como</a>
      </div>
      <a href="#features" class="scroll-down-indicator" aria-label="Rolar para baixo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="48px" height="48px"><path d="M12 15.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0l5-5a1 1 0 00-1.414-1.414L12 15.586zM12 2a1 1 0 00-1 1v10a1 1 0 002 0V3a1 1 0 00-1-1z"/></svg>
      </a>
    </section>

    <section id="features" class="features-section scroll-snap-section">
      <div class="container">
        <h2 class="section-title animate-on-scroll">Tecnologia e Sustentabilidade de Mãos Dadas</h2>
        <p class="section-subtitle animate-on-scroll delay-0-2s">Facilitamos sua jornada para um impacto positivo com ferramentas inteligentes, intuitivas e uma comunidade engajada.</p>
        <div class="features-grid">
          <div class="feature-card animate-on-scroll">
            <div class="feature-icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="feature-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
              </svg>
            </div>
            <h3>Metas Inteligentes</h3>
            <p>Receba desafios personalizados e acompanhe seu progresso real na redução do impacto ambiental diário.</p>
          </div>
          <div class="feature-card animate-on-scroll delay-0-2s">
            <div class="feature-icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="feature-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
              </svg>
            </div>
            <h3>Comunidade Verde</h3>
            <p>Junte-se a uma rede de pessoas engajadas, troque ideias, compartilhe suas vitórias e inspire a mudança coletiva.</p>
          </div>
          <div class="feature-card animate-on-scroll delay-0-4s">
            <div class="feature-icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="feature-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
              </svg>
            </div>
            <h3>Loja Consciente</h3>
            <p>Descubra produtos eletrônicos usados que compartilham nossos valores.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="nossa-visao" class="parallax-section scroll-snap-section">
      <div class="parallax-content container">
        <h2 class="section-title light-text animate-on-scroll">Semeando um <span class="highlight-parallax">Futuro Sustentável</span></h2>
        <p class="section-subtitle light-text animate-on-scroll delay-0-2s">
          Acreditamos que cada pequena ação, multiplicada por muitos, tem o poder de transformar o mundo. Nossa jornada está apenas começando, e convidamos você a cultivar conosco um amanhã onde a tecnologia e a natureza prosperam em harmonia.
        </p>
        <div class="vision-text-block animate-on-scroll delay-0-4s">
            <p>
                Imaginamos um futuro onde viver de forma sustentável não é um desafio, mas uma escolha natural e gratificante, integrada ao dia a dia de todos. 
                Com o Circuito Sustentável, queremos ser a ponte para essa realidade, oferecendo conhecimento, ferramentas e uma comunidade de apoio para que cada pessoa possa ser um agente ativo na construção desse futuro.
            </p>
            <p class="vision-quote">
                <em>"O melhor momento para plantar uma árvore foi há 20 anos. O segundo melhor momento é agora."</em> - Provérbio Chinês
            </p>
        </div>
      </div>
    </section>

    <section id="cta" class="cta-section scroll-snap-section">
      <div class="container cta-container-grid">
        <div class="cta-image-side animate-on-scroll">
            <img src="https://images.pexels.com/photos/38136/pexels-photo-38136.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1 " alt="Pessoas colaborando por um futuro sustentável">
        </div>
        <div class="cta-text-content">
          <h2 class="section-title animate-on-scroll delay-0-2s">Pronto para <span class="highlight-green">Começar</span> sua Jornada?</h2>
          <p class="section-subtitle animate-on-scroll delay-0-4s">Junte-se ao Circuito Sustentável hoje mesmo. É gratuito, fácil e o planeta agradece. Seja parte da mudança!</p>
          <div class="cta-action animate-on-scroll delay-0-6s">
            <button class="btn btn-primary btn-large btn-pulse" onclick="location.href='cadastro.php'">Crie sua Conta Gratuita</button>
            <p class="cta-login">Já tem uma conta? <a href="login.php">Faça Login</a></p>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer class="site-footer-bottom">
    <div class="container footer-content-grid">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Inovação para um futuro mais verde e consciente. Junte-se a nós e faça a diferença!</p>
      </div>
      <div class="footer-col">
        <h4>Navegue</h4>
        <a href="#hero">Início</a>
        <a href="#features">Como Funciona</a>
        <a href="#nossa-visao">Nossa Visão</a>
        <a href="loja.php">Loja</a>
      </div>
      <div class="footer-col">
        <h4>Contato</h4>
        <p>📧 circuito_sustentavel@gmail.com</p>
        <p>📞 (85) 992933310</p>
      </div>
    </div>
    <div class="footer-copyright">
      &copy; <?php echo date("Y"); ?> Circuito Sustentável Inc. Todos os direitos reservados.
    </div>
  </footer>

  <script>
    // Script para menu mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const authButtonsHeader = document.querySelector('.auth-buttons-header');

    if (menuToggle && mainNav && authButtonsHeader) {
        menuToggle.addEventListener('click', () => {
            const isActive = mainNav.classList.toggle('active');
            authButtonsHeader.classList.toggle('active');
            menuToggle.classList.toggle('active');
            menuToggle.setAttribute('aria-expanded', isActive);
        });
        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (mainNav.classList.contains('active')) {
                    mainNav.classList.remove('active');
                    authButtonsHeader.classList.remove('active');
                    menuToggle.classList.remove('active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }

    // Animação de Header ao rolar
    const header = document.querySelector('.site-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Animações ao rolar
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { threshold: 0.1 });
    animatedElements.forEach(el => observer.observe(el));

    // Hero shapes animation
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        const shapes = heroSection.querySelectorAll('.hero-background-shapes .shape');
        heroSection.addEventListener('mousemove', (e) => {
            const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
            shapes.forEach((shape, idx) => {
                const speed = (idx + 1) * 0.5;
                shape.style.transform = `translate(${xAxis * speed}px, ${yAxis * speed}px)`;
            });
        });
    }
  </script>
</body>
</html>