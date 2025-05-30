<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Página Inicial</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #22a868;
      color: #2e7d32;
    }

    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 40px;
      border-bottom: 1px solid #ccc;
      background-color: #ffffff;
      position: relative;
    }

    .nav-center {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
    }

    .nav-center a {
      margin: 0 20px;
      text-decoration: none;
      color: #2e7d32;
      font-weight: bold;
      font-size: 16px;
    }

    .auth-buttons {
      display: flex;
      gap: 10px;
    }

    .auth-buttons button {
      padding: 8px 15px;
      background-color: #2e7d32;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    /* Estilo do botão "Iniciar Sessão" com borda verde */
    .auth-buttons button:last-child {
      border: 2px solid #2e7d32; /* Borda verde */
      background-color: transparent; /* Fundo transparente para o efeito */
      color: #2e7d32; /* Cor do texto verde */
    }

    .auth-buttons button:last-child:hover {
      background-color: #2e7d32;
      color: white;
      box-shadow: 0 0 10px rgba(46, 125, 50, 0.5); /* Efeito de vinil com sombra */
    }

    .auth-buttons button:hover {
      background-color: #1b5e20;
    }

    .main-content {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 120px;
      padding: 0 60px;
      flex-wrap: wrap;
    }

    .text-section {
      flex: 1;
      min-width: 300px;
    }

    .text-section h1 {
      font-size: 80px;
      max-width: 1500px;
      margin-left: 2%;
      color:white;
      
    }

    /* Estilo para o novo texto abaixo do título */
    .text-section p {
      font-size: 30px;
      /* Letra fina */
      color: white;
      margin-top: 20px;
      margin-left: 5%;
      
    }

    .register-btn {
      margin-top: 30px;
      padding: 20px 35px;
      font-size: 18px;
      background-color: white;
      color: #2e7d32;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      margin-left: 34%;
      font-weight: bold;
      transition: background 0.2s;
    }

    .register-btn:hover {
      background-color: #1f804e;
      color: #fff;
    }

    .image-placeholder {
      flex: 1;
      margin-left: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      max-width: 100%;
      min-width: 700px;
      height: auto;
      margin-top: 50px; /* Desloca a imagem para baixo */
    }

    .image-placeholder img {
      width: 700px;  /* Aumenta o tamanho da imagem */
      height: auto;
      border-radius: 20px;
      object-fit: contain;
    }
    .ma{
      margin-left: 9%;
    }
    .mi{
      margin-left: 29%;
      margin-top: -10%;
    }
    .mate{
      margin-left: 16%;
      font-size: 30px;
      color: white;
      margin-top: -3%;
    }

    .logo {
    width: 300px; /* Ajuste o tamanho da imagem conforme necessário */
    height: auto;
    margin-left: 70%;
  }

  .footer-novo {
  background: #1b2430;
  color: #fff;
  padding: 2.5rem 1rem 1rem 1rem;
  margin-top: 10rem;
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
    
  </style>
</head>
<body>

  <header>
  <div class="logo-container">
    <img src="img/logo2.png" alt="Logo" class="logo">
  </div>

    <div class="nav-center">
      <a href="loja.php">Loja</a>
      <a href="causa.html">Causa</a>
      <a href="suporte.html">Suporte</a>
      <a href="mais.html">Mais</a>
    </div>

    <div class="auth-buttons">
      <button onclick="location.href='cadastro.php'">Registrar</button>
      <button onclick="location.href='login.php'">Iniciar Sessão</button>
    </div>
  </header>

  <div class="main-content">
    <div class="text-section">

      <h1>Melhore o mundo no <br>
      <div class="ma">conforto de sua</div>
      <br>
      <div class="mi">Casa</div>
      </h1>

      <p>O Circuito Sustentável ajuda-o a ser uma pessoa mais
        <br>
        <div class="mate"> sustentável, para melhorar o planeta!</div></p> <!-- Novo texto -->
      <button class="register-btn" onclick="location.href='cadastro.php'">Registre-se</button>
    </div>
    <div class="image-placeholder">
      <img src="img/Design_sem_nome_1_-removebg-preview.png" alt="Imagem ilustrativa">
    </div>
  </div>

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
</body>
</html>