<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Assinatura Premium</title>
  <style>
    :root {
      --verde: #28a060;
      --verde-escuro: #1f804e;
      --fundo: #28a060;
      --texto: #ffffff;
      --branco: #ffffff;
      --titulo: #ffffff;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--fundo);
      color: var(--texto);
      line-height: 1.6;
    }

    header {
      position: fixed;
      top: 0;
      width: 100%;
      background: var(--branco);
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    header img {
      height: 40px;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    nav a {
      text-decoration: none;
      color: var(--fundo);
      font-weight: 500;
      transition: color 0.3s;
    }

    nav a:hover {
      color: var(--verde);
    }

    .btn-topo {
      background: var(--verde);
      color: white;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-topo:hover {
      background: var(--verde-escuro);
    }

    .section {
      min-height: 100vh;
      padding: 7rem 2rem 4rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      animation: fadeIn 1.5s ease;
    }

    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(30px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .container {
      max-width: 1000px;
      width: 100%;
      text-align: center;
    }

    h1 {
      font-size: 4rem;
      color: white;
      margin-bottom: 1rem;
      animation: titleAnimation 1s ease-in-out;
    }

    @keyframes titleAnimation {
      0% {
        opacity: 0;
        transform: scale(0.8);
        color: var(--verde-escuro);
      }
      50% {
        opacity: 1;
        transform: scale(1.1);
        color: var(--verde);
      }
      100% {
        opacity: 1;
        transform: scale(1);
        color: var(--verde);
      }
    }

    p {
      font-size: 1.2rem;
      margin-bottom: 3rem;
      color: #f4f4f4;
      animation: fadeIn 1.5s ease-in-out;
    }

    .beneficios {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-bottom: 4rem;
      animation: fadeIn 1.5s ease-in-out;
    }

    .beneficio {
      background: var(--branco);
      padding: 2rem;
      border-left: 6px solid var(--verde);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
      opacity: 0;
      animation: fadeInText 1s ease-out forwards;
    }

    @keyframes fadeInText {
      0% {
        opacity: 0;
        transform: translateY(10px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .beneficio:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      background: var(--verde-escuro);
      color: var(--branco);
    }

    .beneficio h3 {
      color: var(--verde);
      margin-bottom: 0.5rem;
    }

    .btns {
      display: flex;
      justify-content: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .btn {
      padding: 1rem 2rem;
      font-size: 1rem;
      border-radius: 8px;
      border: 2px solid var(--verde);
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: bold;
      text-transform: uppercase;
    }

    .btn-principal {
      background: var(--verde);
      color: white;
    }

    .btn-principal:hover {
      background: var(--verde-escuro);
      transform: scale(1.05);
    }

    .btn-secundario {
      background: transparent;
      color: var(--verde);
    }

    .btn-secundario:hover {
      background: var(--verde);
      color: white;
      transform: scale(1.05);
    }

    footer {
      background-color: #ccc;
      padding: 1rem;
      text-align: center;
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }

      .beneficios {
        grid-template-columns: 1fr;
      }

      h1 {
        font-size: 2.2rem;
      }
    }
  </style>
</head>
<body>

  <header>
    <img src="img/logo2.png" alt="Logo" />
    <nav>
      <a href="#">Início</a>
      <a href="#">Benefícios</a>
      <a href="#">Planos</a>
      <a href="#">Contato</a>
      <button class="btn-topo">Assinar</button>
    </nav>
  </header>

  <section class="section">
    <div class="container">
      <h1>Transforme sua experiência com nossa Assinatura Premium</h1>
      <p>
        Torne-se um membro exclusivo e tenha acesso ilimitado a recursos, conteúdos, atualizações VIP e muito mais. Você merece o melhor!
      </p>

      <div class="beneficios">
        <div class="beneficio">
          <h3>✔️ Acesso Ilimitado</h3>
          <p>Obtenha acesso completo e sem restrições a todas as nossas ferramentas.</p>
        </div>
        <div class="beneficio">
          <h3>✔️ Suporte Rápido e Prioritário</h3>
          <p>Obtenha suporte ágil e especializado, com prioridade total.</p>
        </div>
        <div class="beneficio">
          <h3>✔️ Atualizações Antecipadas</h3>
          <p>Seja o primeiro a experimentar novos recursos e funcionalidades.</p>
        </div>
        <div class="beneficio">
          <h3>✔️ Comunidade Exclusiva</h3>
          <p>Participe de discussões e troque experiências com outros membros premium.</p>
        </div>
        <div class="beneficio">
          <h3>✔️ Personalização Completa</h3>
          <p>Customize sua experiência de uso e aproveite recursos avançados de personalização.</p>
        </div>
        <div class="beneficio">
          <h3>✔️ Acesso a Eventos Exclusivos</h3>
          <p>Participe de webinars e eventos fechados para assinantes Premium.</p>
        </div>
      </div>

      <div class="btns">
        <button class="btn btn-principal">Assine Agora</button>
        <button class="btn btn-secundario">Veja os Planos</button>
      </div>
    </div>
  </section>

  <footer>
    &copy; 2025 Ezequiel Inc. Todos os direitos reservados.
  </footer>

</body>
</html>
