<!DOCTYPE html>
<html lang="pt-BR" class="snap-container">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Assinatura Premium - Circuito Sustentável</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <style>
    :root {
        --verde: #28a060;
        --verde-escuro: #1e7c4b; /* Usado nos títulos dos benefícios originais */
        --verde-claro-fundo: #f0f9f4; 
        --verde-destaque-parallax: #8cffc7; 
        --cinza-claro: #f4f6f8;
        --cinza-texto: #5f6c7b; /* Texto padrão */
        --cinza-texto-beneficio-original: #222; /* Texto dos benefícios no original */
        --cinza-escuro: #2c3e50;
        --branco: #ffffff;
        --sombra-padrao: 0 8px 25px rgba(40, 160, 96, 0.08);
        --sombra-hover-beneficio-original: 0 16px 40px rgba(40,160,96,0.25);
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px; /* Usado no preço */
        --border-radius-beneficio-original: 10px;
        --transition-fast: 0.2s;
        --transition-std: 0.3s;
        --transition-long: 0.5s;
        --font-principal: 'Poppins', sans-serif; /* Usaremos Poppins, o original usava Segoe UI */
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html.snap-container {
        scroll-behavior: smooth;
        scroll-snap-type: y proximity;
    }

    body {
        font-family: var(--font-principal);
        line-height: 1.6;
        color: var(--cinza-texto-beneficio-original); /* Cor de texto padrão do body original */
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
        padding: 100px 20px;
        position: relative;
        overflow: hidden;
    }
    .scroll-snap-section:first-child {
        padding-top: 120px;
    }
    .faq-section { 
        padding-bottom: 180px !important; 
    }

    .container { width: 90%; max-width: 1000px; margin: 0 auto; text-align: center; }

    .btn {
        display: inline-block; padding: 12px 28px; font-weight: 600;
        text-decoration: none; border-radius: var(--border-radius-md);
        transition: all var(--transition-std) cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer; border: 2px solid transparent; font-size: 1em;
        box-shadow: var(--sombra-padrao); letter-spacing: 0.5px;
    }
    .btn-primary { background-color: var(--verde); color: var(--branco); }
    .btn-primary:hover {
        background-color: var(--verde-escuro);
        transform: translateY(-4px);
        box-shadow: var(--sombra-hover-forte);
    }
    .btn-outline {
        background-color: transparent; color: var(--verde); border-color: var(--verde);
    }
    .btn-outline:hover {
        background-color: var(--verde); color: var(--branco);
        transform: translateY(-4px);
        box-shadow: var(--sombra-hover-forte);
    }
    .btn-large { padding: 16px 40px; font-size: 1.1em; }

    .site-header {
        position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
        padding: 15px 0;
        background-color: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        transition: background-color var(--transition-std), box-shadow var(--transition-std), padding var(--transition-std);
    }
    .site-header.scrolled {
        background-color: var(--branco);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 10px 0;
    }
    .header-container {
        width: 90%; max-width: 1140px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between;
    }
    .site-header .logo { height: 45px; transition: transform var(--transition-std); }
    .site-header .logo:hover { transform: scale(1.05); }

    .main-nav { display: flex; align-items:center; gap: 25px; }
    .main-nav a {
        color: var(--cinza-escuro); /* Alterado de var(--fundo) do original para consistência */
        font-weight: 500; text-decoration: none;
        position: relative; padding-bottom: 5px; font-size: 0.95em;
         transition: color 0.3s; /* Transição original */
    }
    .main-nav a:hover { color: var(--verde); } /* Cor hover original */
    /* Removido ::after para manter mais próximo do nav original, mas pode ser readicionado */
    .main-nav .btn { padding: 0.5rem 1rem; font-size:0.9em; }

    .section-title {
        font-size: clamp(2.2rem, 5vw, 3.5rem); color: var(--cinza-escuro);
        font-weight: 700; text-align: center; margin-bottom: 20px;
    }
    /* Título H1 principal da página de assinatura, mantendo o estilo original se desejado */
    .hero-assinatura .section-title {
        font-size: clamp(2.5rem, 6vw, 4rem); /* Tamanho original era 4rem */
        color: var(--verde-escuro); /* Cor original #1f804e */
        margin-bottom: 1rem;
        /* Mantendo a animação do título original se desejado */
        /* animation: titleAnimation 1s ease-in-out; */
    }
    /* @keyframes titleAnimation { ... } // Animação original do título, se for manter */

    .section-subtitle {
        font-size: clamp(1rem, 2.5vw, 1.3rem); /* Meu tamanho sugerido */
        /* font-size: 1.2rem; // Tamanho original */
        color: var(--cinza-texto-beneficio-original); /* Cor de texto do parágrafo original */
        text-align: center; max-width: 700px; margin: 0 auto 40px;
        line-height: 1.8;
        margin-bottom: 3rem; /* Margem original */
    }

    .hero-assinatura {
      background: #e9f7ef; /* Fundo claro original da primeira seção */
    }
    /* .hero-assinatura .section-title { color: var(--verde-escuro); } // Já definido acima */

    .preco-chamativo-area {
      margin: 2rem auto 2.5rem auto;
      display: flex; justify-content: center; align-items: center; flex-direction: column;
      animation: fadeInUp 1s var(--transition-std) forwards;
      opacity:0;
    }
    .preco-chamativo {
      background: linear-gradient(120deg, var(--verde) 0%, var(--verde-escuro) 100%);
      color: var(--branco); font-size: clamp(2.8rem, 6vw, 3.5rem); /* Ajustado o clamp, original era 3.5rem */
      font-weight: bold; /* Original */
      padding: 1.2rem 3.5rem; /* Original */
      border-radius: 30px; /* Original */
      box-shadow: 0 8px 32px rgba(40,160,96,0.25), 0 0 0 8px #e9f7ef; /* Sombra e borda original */
      letter-spacing: 2px; /* Original */
      text-shadow: 0 4px 16px #145c36, 0 1px 0 #fff; /* Original */
      animation: pulsePrice 1.2s infinite alternate; /* Renomeado de pulse */
      border: 4px solid #fff; /* Original */
      position: relative;
    }
    @keyframes pulsePrice { /* Renomeado de pulse */
      0% { box-shadow: 0 0 0 8px #e9f7ef, 0 8px 32px rgba(40,160,96,0.25);}
      100% { box-shadow: 0 0 0 16px #e9f7ef, 0 16px 48px rgba(40,160,96,0.35);}
    }
    .preco-chamativo span.valor-centavos { font-size: 0.7em; /* Original era 2.5rem absoluto, mudei para relativo */ }
    .preco-chamativo span.valor-so { font-size: 0.4em; vertical-align: super; /* Original era 1.5rem */ }
    .preco-chamativo span.valor-mes { font-size: 0.4em; vertical-align: super; /* Original era 1.2rem */ }
    .preco-chamativo-promo {
      position: absolute; top: -30px; right: -30px;
      background: #ffeb3b; color: var(--verde-escuro); /* Original era #1f804e */
      font-size: 1.1rem; font-weight: bold;
      padding: 0.5rem 1.2rem; border-radius: 18px;
      box-shadow: 0 2px 8px #28a06055; transform: rotate(10deg);
      border: 2px solid #fff;
      animation: bounce 1.5s infinite alternate;
    }
    @keyframes bounce { /* Original */
      0% { transform: rotate(10deg) translateY(0);}
      100% { transform: rotate(10deg) translateY(-10px);}
    }
    .preco-chamativo-desc {
      margin-top: 1.2rem; font-size: 1.3rem; color: var(--verde); /* Original era #28a060 */
      font-weight: bold; text-shadow: 0 1px 0 #fff;
      letter-spacing: 1px;
      animation: glow 1.5s infinite alternate;
    }
     @keyframes glow { /* Original */
      0% { color: var(--verde); text-shadow: 0 0 8px #28a06055;}
      100% { color: var(--verde-escuro); text-shadow: 0 0 18px #28a06099;}
    }

    /* Benefícios Section - Estilo Original Reaplicado */
    .beneficios-section {
      background-color: var(--branco); /* Fundo da seção */
    }
    .beneficios { /* Container dos cards - Nome da classe original */
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Layout original */
      gap: 2rem; /* Espaçamento original */
      margin-bottom: 4rem; /* Margem original */
      margin-top: 2rem; /* Adicionado para espaçar do título */
      width:100%;
    }
    .beneficio { /* Card individual - Nome e estilo original */
      background: var(--branco);
      color: var(--cinza-texto-beneficio-original);
      padding: 2rem;
      border-left: 6px solid var(--verde);
      border-radius: var(--border-radius-beneficio-original);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: 
        transform 0.3s,
        box-shadow 0.3s,
        background 0.3s,
        color 0.3s;
      text-align: left; /* Garantir alinhamento original */
    }
    .beneficio:hover {
      background: var(--verde);
      color: var(--branco);
      transform: scale(1.10); /* Efeito de scale original */
      box-shadow: var(--sombra-hover-beneficio-original);
    }
    .beneficio h3 {
      color: var(--verde-escuro); /* Original #1f804e */
      margin-bottom: 0.5rem;
      font-size: 1.3em; /* Ajustado para Poppins */
      font-weight: 600;
      transition: color 0.3s; /* Transição de cor original */
    }
    .beneficio p { 
        font-size: 0.95em; 
        line-height: 1.7; /* Ajustado para Poppins */
        margin-bottom:0; 
        color: inherit; /* Herda a cor do .beneficio (muda para branco no hover) */
        font-weight: normal; /* Original */
    }
    .beneficio:hover h3,
    .beneficio:hover p {
      color: var(--branco); /* Cor do texto no hover original */
    }
    
    .parallax-break-section {
        background-image: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1742&q=80');
        background-attachment: fixed; background-position: center;
        background-repeat: no-repeat; background-size: cover;
        display: flex; align-items: center; justify-content: center;
        text-align:center;
        color: var(--branco);
    }
    .parallax-break-section .overlay-content {
        background-color: rgba(10, 25, 41, 0.65);
        padding: 50px 40px;
        border-radius: var(--border-radius-md);
        max-width: 750px;
    }
    .parallax-break-section h2 {
        font-size: clamp(2rem, 4.5vw, 3rem);
        font-weight: 700;
        margin-bottom: 25px;
        color: var(--verde-destaque-parallax);
    }
    .parallax-break-section p {
        font-size: clamp(1.05rem, 2.2vw, 1.25rem);
        margin-bottom: 30px;
        opacity: 0.9;
    }

    /* FAQ Section */
    .faq-section {
      background-color: var(--verde-claro-fundo);
    }
    .faq-container {
      max-width: 800px;
      width: 100%;
      margin: 2rem auto 0 auto;
    }
    .faq-item {
      margin-bottom: 1rem;
      border-radius: var(--border-radius-md);
      background: var(--branco);
      box-shadow: var(--sombra-padrao);
      overflow: hidden;
    }
    .faq-question {
      width: 100%; background: transparent; color: var(--cinza-escuro);
      border: none; outline: none; padding: 18px 25px;
      text-align: left; font-size: 1.1rem; font-weight: 600;
      cursor: pointer; transition: background-color var(--transition-fast);
      display: flex; justify-content: space-between; align-items: center;
    }
    .faq-question::after {
      content: '+'; font-size: 1.5rem;
      color: var(--verde); transition: transform var(--transition-std);
    }
    .faq-question.active::after { transform: rotate(45deg); }
    .faq-question:hover { background-color: #f9f9f9; }
    .faq-answer {
      background: var(--branco); color: var(--cinza-texto);
      max-height: 0; overflow: hidden;
      /* A transição de max-height será controlada pelo JS para precisão */
      transition: padding var(--transition-std) ease-in-out, max-height var(--transition-std) ease-in-out;
      padding: 0 25px; font-size: 0.95em;
    }
    .faq-answer p { margin: 0; line-height: 1.8; color: var(--cinza-texto); }
    /* Estilos para quando o JS adiciona o padding e max-height */
    .faq-answer.open {
        padding-top: 15px;
        padding-bottom: 20px;
    }


    .animate-on-scroll, .animate-fade-in-up {
        opacity: 0;
        transform: translateY(40px);
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .animate-on-scroll.in-view, .animate-fade-in-up.in-view {
        opacity: 1;
        transform: translateY(0);
    }
    .delay-0-2s { transition-delay: 0.2s !important; }
    .delay-0-4s { transition-delay: 0.4s !important; }
    .delay-0-6s { transition-delay: 0.6s !important; }

    .site-footer-bottom {
        background-color: var(--cinza-escuro); color: #b0bec5;
        padding: 70px 0 40px; font-size: 0.95em;
    }
    .footer-content-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 45px; margin-bottom: 50px;
        width: 90%; max-width: 1140px; margin: 0 auto 30px auto;
    }
    .footer-col h4 { font-size: 1.25em; color: var(--branco); font-weight: 600; margin-bottom: 20px; }
    .footer-col p, .footer-col a { color: #b0bec5; text-decoration: none; margin-bottom: 10px; display: block; }
    .footer-col a:hover { color: var(--verde); transform: translateX(3px); transition: transform var(--transition-fast); }
    .footer-copyright { text-align: center; padding-top: 40px; border-top: 1px solid #4a5c6a; color: #78909c; width: 90%; max-width: 1140px; margin: 0 auto; }

    @media (max-width: 992px) {
        .main-nav { gap: 15px; }
        .main-nav a { font-size: 0.9em; }
        .main-nav .btn { display:none; }
    }

    @media (max-width: 768px) {
        .site-header { padding: 10px 0; }
        .scroll-snap-section { padding: 80px 15px; }
        .scroll-snap-section:first-child { padding-top: 100px; }
        .faq-section { padding-bottom: 120px !important; }
        .section-title { font-size: clamp(1.8rem, 5vw, 2.5rem); }
        .section-subtitle { font-size: clamp(0.9rem, 2.5vw, 1.1rem); margin-bottom: 30px; }
        .preco-chamativo { font-size: clamp(2.2rem, 5vw, 3rem); padding: 0.8rem 2rem; }
        .beneficios { grid-template-columns: 1fr; gap: 20px; } /* Nome da classe original */
        .faq-question { font-size: 1rem; padding: 15px 20px;}
        .faq-answer { font-size: 0.9rem; padding: 0 20px; }
        .faq-answer.open { padding-top: 15px; padding-bottom: 20px;} /* Para o JS */
        .parallax-break-section .overlay-content { padding: 30px 20px; }
        .parallax-break-section h2 { font-size: clamp(1.6rem, 4vw, 2.2rem); }
        .parallax-break-section p { font-size: clamp(0.95rem, 2vw, 1.1rem); }
    }

  </style>
</head>
<body>

  <header class="site-header">
    <div class="header-container">
      <a href="loja.php"> <img src="img/logo2.png" alt="Logo Circuito Sustentável" class="logo" /></a>
      <nav class="main-nav">
        <a href="tela_inicial.php">Início</a>
        <a href="#faq-section">Contato & FAQ</a>
        <button class="btn btn-primary btn-sm" id="btn-topo-assinar">Assinar Agora</button>
      </nav>
    </div>
  </header>

  <main>
    <section id="hero-assinatura" class="scroll-snap-section hero-assinatura">
      <div class="container">
        <h1 class="section-title animate-on-scroll">Eleve sua Experiência com a Assinatura <span style="color:var(--verde);">Premium</span></h1>
        <p class="section-subtitle animate-on-scroll delay-0-2s">
          Desbloqueie acesso total a recursos exclusivos, conteúdo personalizado e suporte prioritário. Junte-se à comunidade Premium e transforme sua jornada sustentável!
        </p>
        <div class="preco-chamativo-area animate-on-scroll delay-0-4s">
          <div class="preco-chamativo">
            <span class="valor-so">Só</span>
            R$14<span class="valor-centavos">,99</span>
            <span class="valor-mes">/mês</span>
            <span class="preco-chamativo-promo">Oferta Imperdível!</span>
          </div>
          <div class="preco-chamativo-desc">
            Todos os benefícios por um valor que cabe no seu bolso.
          </div>
        </div>
         <button class="btn btn-primary btn-large animate-on-scroll delay-0-6s" id="btn-hero-assinar" style="margin-top: 20px;">Quero ser Premium!</button>
      </div>
    </section>

    <section id="beneficios-section" class="scroll-snap-section beneficios-section">
      <div class="container">
        <h2 class="section-title animate-on-scroll">Vantagens Exclusivas para Assinantes</h2>
        <div class="beneficios"> <div class="beneficio animate-on-scroll"> <h3>Cupons Exclusivos</h3>
            <p>Obtenha acesso completo a uma lista de cupons exclusivos.</p>
          </div>
          <div class="beneficio animate-on-scroll delay-0-2s">
            <h3>Suporte Rápido e Prioritário</h3>
            <p>Obtenha suporte ágil e especializado, com prioridade total.</p>
          </div>
          
        
          <div class="beneficio animate-on-scroll delay-0-2s">
            <h3>Dicas diarias</h3>
            <p>Dicas de como emitir menos carbono.</p>
          </div>
          <div class="beneficio animate-on-scroll delay-0-4s">
            <h3>Entrega mais rápida</h3>
            <p>Seu pedido sera entregue mais rápido.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="scroll-snap-section parallax-break-section">
      <div class="overlay-content animate-on-scroll">
        <h2>Junte-se ao Movimento Sustentável</h2>
        <p>Faça parte de uma comunidade que está mudando o mundo, uma escolha consciente de cada vez. Com a assinatura Premium, seu impacto é ainda maior.</p>
        <button class="btn btn-primary btn-large" id="btn-parallax-assinar" style="margin-top: 30px;">Quero ser Premium Agora!</button>
      </div>
    </section>

    <section id="faq-section" class="scroll-snap-section faq-section">
      <div class="container">
        <h2 class="section-title animate-on-scroll">Perguntas Frequentes</h2>
        <div class="faq-container animate-on-scroll delay-0-2s">
          <div class="faq-item">
            <button class="faq-question">Como funciona a assinatura premium?</button>
            <div class="faq-answer">
              <p>Ao assinar, você terá acesso imediato a todos os benefícios listados, como cupons, suporte prioritário, dobro de moedas, selo exclusivo, dicas personalizadas e entrega mais rápida.</p>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-question">Posso cancelar quando quiser?</button>
            <div class="faq-answer">
              <p>Sim! Você pode gerenciar e cancelar sua assinatura a qualquer momento através do seu painel de usuário, sem taxas ou complicações.</p>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-question">Quais formas de pagamento são aceitas?</button>
            <div class="faq-answer">
              <p>Aceitamos os principais cartões de crédito (Visa, Mastercard, Elo, etc.) e Pix para sua conveniência.</p>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-question">Como entro em contato com o suporte prioritário?</button>
            <div class="faq-answer">
              <p>Como assinante Premium, você terá acesso a um canal de suporte dedicado. As informações de contato e acesso serão disponibilizadas em seu painel de usuário após a confirmação da assinatura.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <footer class="site-footer-bottom">
      <div class="container footer-content-grid">
        <div class="footer-col">
          <h4>Circuito Sustentável</h4>
          <p>Inovação para um futuro mais verde e consciente. Junte-se a nós!</p>
        </div>
         <div class="footer-col">
            <h4>Navegue</h4>
            <a href="tela_inicial.php">Página Inicial</a>
            <a href="loja.php">Loja</a>
            <a href="#hero-assinatura">Planos de Assinatura</a>
            <a href="#faq-section">FAQ</a>
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

  </main>

  <script>
    const header = document.querySelector('.site-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    document.querySelectorAll('.faq-question').forEach(btn => {
      btn.addEventListener('click', function() {
        const isActive = this.classList.contains('active');
        this.classList.toggle('active');
        const answer = this.nextElementSibling;
        if (isActive) {
          answer.style.maxHeight = null;
          answer.classList.remove('open');
        } else {
          answer.classList.add('open'); // Adiciona padding via classe
          answer.style.maxHeight = answer.scrollHeight + "1px"; // Define a altura correta
        }
      });
    });

    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            } else {
                // entry.target.classList.remove('in-view'); 
            }
        });
    }, { threshold: 0.1 });
    animatedElements.forEach(el => observer.observe(el));

    const precoAssinatura = '14.99';
    const urlPagamento = `pagamento.php?assinatura=${precoAssinatura}`;

    const btnTopoAssinar = document.getElementById('btn-topo-assinar');
    if(btnTopoAssinar) {
      btnTopoAssinar.onclick = function() { window.location.href = urlPagamento; };
    }
    
    const btnHeroAssinar = document.getElementById('btn-hero-assinar');
    if(btnHeroAssinar) {
        btnHeroAssinar.onclick = function() { window.location.href = urlPagamento; };
    }
    
    const btnParallaxAssinar = document.getElementById('btn-parallax-assinar');
    if(btnParallaxAssinar) {
        btnParallaxAssinar.onclick = function() { window.location.href = urlPagamento; };
    }
  </script>

</body>
</html>