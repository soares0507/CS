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
      background-color: #f4f4f4; /* Alterado para fundo claro */
      color: #222; /* Texto escuro para melhor contraste */
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
      background: #e9f7ef; /* Fundo claro para a seção principal */
      color: #222; /* Texto escuro na seção principal */
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
      color: #222; /* Garante texto escuro na seção principal */
    }

    h1 {
      font-size: 4rem;
      color: #1f804e; /* Título verde escuro */
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
      color: #222; /* Texto escuro na seção principal */
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
      background: #fff;
      color: #222;
      padding: 2rem;
      border-left: 6px solid var(--verde);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: 
        transform 0.3s,
        box-shadow 0.3s,
        background 0.3s,
        color 0.3s;
    }

    .beneficio h3 {
      color: #1f804e; /* Título verde escuro */
      margin-bottom: 0.5rem;
      transition: color 0.3s;
    }

    .beneficio:hover {
      background: var(--verde);
      color: #fff;
      transform: scale(1.10);
      box-shadow: 0 16px 40px rgba(40, 160, 96, 0.25);
    }

    .beneficio:hover h3,
    .beneficio:hover p {
      color: #fff;
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
      background: #1f804e;
      color: #fff;
      border: 2px solid #1f804e;
    }

    .btn-principal:hover {
      background: #145c36;
      color: #fff;
      border: 2px solid #145c36;
      transform: scale(1.05);
    }

    .btn-secundario {
      background: transparent;
      color: #1f804e;
      border: 2px solid #1f804e;
    }

    .btn-secundario:hover {
      background: #1f804e;
      color: #fff;
      border: 2px solid #1f804e;
      transform: scale(1.05);
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
  
.beneficios {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
  padding: 2rem;
  margin-top: 5rem;
}

.beneficio h3 {
  margin-bottom: 0.5rem;
}

.beneficio p {
  font-weight: normal;
}

/* Ajuste para a seção extra de benefícios no final */
section.beneficios {
  background: #fff;
  color: #222;
  margin-top: 0;
  padding: 2rem 0;
}
section.beneficios .beneficio {
  background: #f4f4f4;
  color: #222;
  border-left: 6px solid #28a060;
}
section.beneficios .beneficio:hover {
  background: #28a060;
  color: #fff;
}

/* FAQ Section */
.faq {
  background: #f4f4f4;
  padding: 3rem 1rem 2rem 1rem;
  max-width: 800px;
  margin: 0 auto 3rem auto;
  border-radius: 12px;
  box-shadow: 0 2px 16px rgba(40,160,96,0.07);
}
.faq h2 {
  color: #1f804e;
  text-align: center;
  margin-bottom: 2rem;
  font-size: 2rem;
}
.faq-item {
  margin-bottom: 1.2rem;
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 1px 6px rgba(40,160,96,0.05);
}
.faq-question {
  width: 100%;
  background: #28a060;
  color: #fff;
  border: none;
  outline: none;
  padding: 1rem 1.5rem;
  text-align: left;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}
.faq-question.active,
.faq-question:hover {
  background: #1f804e;
}
.faq-answer {
  background: #f9f9f9;
  color: #222;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s ease;
  padding: 0 1.5rem;
}
.faq-answer p {
  margin: 1rem 0;
  font-size: 1rem;
}

/* Novo Footer */
.footer-novo {
  background: #1b2430;
  color: #fff;
  padding: 2.5rem 1rem 1rem 1rem;
  margin-top: 2rem;
}
.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 2rem;
  max-width: 1100px;
  margin: 0 auto;
}
.footer-col {
  min-width: 180px;
  flex: 1;
}
.footer-col h4 {
  margin-bottom: 1rem;
  color:rgb(255, 255, 255);
}
.footer-col a {
  color: #cfd8dc;
  text-decoration: none;
  display: block;
  margin-bottom: 0.5rem;
  font-size: 1rem;
  transition: color 0.2s;
}
.footer-col a:hover {
  color: #28a060;
}
.footer-bottom {
  text-align: center;
  color: #aaa;
  font-size: 0.95rem;
  margin-top: 2rem;
  border-top: 1px solid #333;
  padding-top: 1rem;
}
footer p {
 color:rgb(156, 163, 175);
;
}

@media (max-width: 900px) {
  .footer-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 1.5rem;
  }
  .faq {
    padding: 2rem 0.5rem;
  }
}

</style>
</head>
<body>

  <header>
   <a href="loja.php"> <img src="img/logo2.png" alt="Logo" /></a>
    <nav>
      <a href="#">Início</a>
      <a href="#">Contato</a>
      <button class="btn-topo">Assinar</button>
    </nav>
  </header>

  <section class="section">
    <div class="container">
      <h1>Eleve sua experiência com nossa Assinatura Premium</h1>
      <p>
        Acesse recursos exclusivos, conteúdo personalizado e suporte prioritário com nossa assinatura. Junte-se hoje e transforme seus projetos.
      </p>

      <div class="beneficios">
        <div class="beneficio">
          <h3> Cupons Exclusivos</h3>
          <p>Obtenha acesso completo a uma lista de cupons exclusivos.</p>
        </div>
        <div class="beneficio">
          <h3> Suporte Rápido e Prioritário</h3>
          <p>Obtenha suporte ágil e especializado, com prioridade total.</p>
        </div>
        <div class="beneficio">
          <h3>Dobro de Moedas</h3>
          <p>Sua compra tera o dobro de moedas.</p>
        </div>
        <div class="beneficio">
          <h3> Selo Exclusivo</h3>
          <p>Selo exclusivo para assinantes na sua foto de perfil.</p>
        </div>
        <div class="beneficio">
          <h3> Dicas diarias</h3>
          <p>Dicas de como emitir menos carbono.</p>
        </div>
        <div class="beneficio">
          <h3> Entrega mais rápida</h3>
          <p>Seu pedido sera entregue mais rápido.</p>
        </div>
      </div>

      <div class="btns">
        <button class="btn btn-principal">Assine Agora</button>
        
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="faq">
    <h2>Perguntas Frequentes</h2>
    <div class="faq-item">
      <button class="faq-question">Como funciona a assinatura premium?</button>
      <div class="faq-answer">
        <p>Ao assinar, você terá acesso imediato a todos os benefícios exclusivos, incluindo suporte prioritário, eventos e muito mais.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Posso cancelar quando quiser?</button>
      <div class="faq-answer">
        <p>Sim! Você pode cancelar sua assinatura a qualquer momento sem taxas adicionais.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Quais formas de pagamento são aceitas?</button>
      <div class="faq-answer">
        <p>Aceitamos cartões de crédito, débito e boleto bancário.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question">Como entro em contato com o suporte?</button>
      <div class="faq-answer">
        <p>Você pode entrar em contato pelo e-mail circuito_sustentavel@gmail.com ou pelo telefone (85) 992933310.</p>
      </div>
    </div>
  </section>

  <footer class="footer-novo">
    <div class="footer-container">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Oferecendo solução para o meio ambiente e seu bolso.</p>
      </div>
      <div class="footer-col">
        <h4>Contato</h4>
        <p>📧 circuito_sustentavel@gmail.com</p>
        <p>📞 (85) 992933310</p>
      </div>
     
    </div>
    <div class="footer-bottom">
      &copy; 2025 Circuito Sustentável Inc. Todos os direitos reservados.
    </div>
  </footer>

  <script>
    // FAQ toggle
    document.querySelectorAll('.faq-question').forEach(btn => {
      btn.addEventListener('click', function() {
        this.classList.toggle('active');
        const answer = this.nextElementSibling;
        if (answer.style.maxHeight) {
          answer.style.maxHeight = null;
        } else {
          answer.style.maxHeight = answer.scrollHeight + "px";
        }
      });
    });
  </script>

</body>
</html>
