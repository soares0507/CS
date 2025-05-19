<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Site CS - Assinatura Premium</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #d8d3c5;
      color: #2d2d2d;
    }
    header {
      background: #fff;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    header h1 {
      color: #2e7d32;
      margin: 0;
      font-size: 24px;
    }
    nav a {
      margin-left: 20px;
      text-decoration: none;
      color: #333;
      font-weight: 500;
    }
    .container {
      display: flex;
      padding: 60px 40px;
      justify-content: space-between;
    }
    .left {
      max-width: 50%;
    }
    .left h2 {
      font-size: 40px;
      font-weight: 700;
    }
    .left h2 span {
      color: #43a047;
    }
    .left p {
      font-size: 16px;
      margin: 20px 0;
    }
    .left button,
    .left a.button {
      margin-right: 10px;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      font-size: 16px;
    }
    .left button {
      background-color: #2e7d32;
      color: white;
    }
    .left a.button {
      background-color: white;
      color: #2e7d32;
      border: 2px solid #2e7d32;
      text-decoration: none;
    }
    .right {
      background-color: #2e7d32;
      border-radius: 12px;
      padding: 30px;
      color: white;
      max-width: 350px;
    }
    .right h3 {
      margin: 0;
      font-size: 20px;
    }
    .price {
      font-size: 28px;
      font-weight: bold;
      margin: 10px 0;
    }
    .right ul {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }
    .right ul li {
      margin-bottom: 10px;
      padding-left: 20px;
      position: relative;
    }
    .right ul li::before {
      content: '✔';
      position: absolute;
      left: 0;
      color: #66bb6a;
    }
    .right button {
      width: 100%;
      background-color: #2e7d32;
      color: white;
      border: none;
      padding: 12px;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
    }
    .right small {
      display: block;
      text-align: center;
      margin-top: 10px;
      color: #ccc;
    }
    .faq {
      background: #f8f7f2;
      padding: 60px 40px;
    }
    .faq h2 {
      text-align: center;
      margin-bottom: 40px;
    }
    .faq-item {
      background: white;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 10px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }
    .cta {
      background-color: #2e7d32;
      color: white;
      text-align: center;
      padding: 40px 20px;
    }
    .cta h2 {
      margin-bottom: 10px;
    }
    .cta p {
      margin-bottom: 20px;
    }
    .cta button {
      padding: 12px 20px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      color: #2e7d32;
      background-color: white;
      cursor: pointer;
    }
    footer {
      background-color: #1b2430;
      color: white;
      padding: 40px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    footer div {
      max-width: 300px;
    }
    footer h4 {
      margin-top: 0;
    }
    footer a {
      display: block;
      color: #cfd8dc;
      text-decoration: none;
      margin: 5px 0;
    }
    footer p, footer a {
      font-size: 14px;
    }
    footer hr {
      border: none;
      border-top: 1px solid #333;
      margin: 20px 0;
      width: 100%;
    }
    footer small {
      display: block;
      width: 100%;
      text-align: center;
      margin-top: 10px;
      font-size: 12px;
      color: #aaa;
    }
  </style>
</head>
<body>
  <header>
    <h1>Site CS</h1>
    <nav>
      <a href="#">Início</a>
      <a href="#">Recursos</a>
      <a href="#">Contato</a>
    </nav>
  </header>

  <section class="container">
    <div class="left">
      <h2>Eleve sua experiência com nossa <span>assinatura premium</span></h2>
      <p>Acesse recursos exclusivos, conteúdo personalizado e suporte prioritário com nossa assinatura. Junte-se hoje e transforme seus projetos.</p>
      <button>Assinar agora</button>
      <a href="#" class="button">Saiba mais</a>
    </div>
    <div class="right">
      <h3>Plano Premium</h3>
      <div class="price">R$29 <small>/mês</small></div>
      <ul>
        <li>Acesso completo a todos os recursos</li>
        <li>Conteúdo exclusivo atualizado semanalmente</li>
        <li>Suporte prioritário 24/7</li>
        <li>Sem compromisso, cancele quando quiser</li>
      </ul>
      <button>Começar agora</button>
      <small>7 dias de teste grátis, sem cobrança</small>
    </div>
  </section>

  <section class="faq">
    <h2>Perguntas frequentes</h2>
    <div class="faq-item">
      <h4>Como funciona a assinatura?</h4>
      <p>Após assinar, você terá acesso imediato a todos os recursos premium da plataforma. Você pode cancelar a qualquer momento sem compromisso.</p>
    </div>
    <div class="faq-item">
      <h4>Posso cancelar quando quiser?</h4>
      <p>Sim, você pode cancelar sua assinatura a qualquer momento. Não há taxas de cancelamento ou contratos de longo prazo.</p>
    </div>
    <div class="faq-item">
      <h4>Como funciona o período de teste?</h4>
      <p>Você tem 7 dias para experimentar todos os recursos premium sem nenhuma cobrança. Se não gostar, cancele antes do final do período de teste.</p>
    </div>
  </section>

  <section class="cta">
    <h2>Pronto para começar?</h2>
    <p>Junte-se a milhares de usuários satisfeitos e eleve sua experiência hoje mesmo.</p>
    <button>Assinar agora</button>
  </section>

  <footer>
    <div>
      <h4>Circuito Sustentavel</h4>
      <p>Oferecendo solução para o meio ambiente e seu bolço.</p>
    </div>
    
    <div>
      <h4>Contato</h4>
      <p>📧 circuito_sustentavel@gmail.com</p>
      <p>📞 (85) 992933310</p>
    </div>
    <hr>
     &copy; 2025 Circuito Sustentável Inc. Todos os direitos reservados.
  </footer>
</body>
</html>
