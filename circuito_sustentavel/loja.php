<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Circuito Sustentável</title>
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
      background-color: #d5d3c7;
    }

    header {
      background: white;
      padding: 1rem;
      position: relative;
    }

    .menu-btn {
      background: #28a060;
      border-radius: 30px;
      padding: 20px 30px;
      display: flex;
      align-items: center;
      gap: 10px;
      color: black;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      position: absolute;
      top: 1rem;
      left: 1rem;
      margin-top: 1.4%;
    }

    .logo-container {
      text-align: center;
      margin-top: 20px;
    }

    .logo-container img {
      height: 40px;
    }

    .search-bar {
      margin: 1rem auto 0 auto;
      display: flex;
      justify-content: center;
      position: relative;
    }

    .search-bar input {
      width: 70%;
      padding: 10px 40px 10px 20px;
      border: none;
      background: #f3f2e7;
      border-radius: 20px;
      font-size: 1rem;
    }

    .search-bar img.lupa {
      position: absolute;
      right: 16%;
      top: 50%;
      transform: translateY(-50%);
      height: 20px;
    }

    .auth-section {
      position: absolute;
      top: 1rem;
      right: 1rem;
      display: flex;
      flex-direction: column; /* Alinha os elementos verticalmente */
      align-items: flex-end; /* Alinha à direita */
      gap: 10px;
      margin-top: 1.4%;
    }

    .auth-links {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 0.9rem;
    }

    .auth-links a {
      text-decoration: none;
      color: black;
      padding: 5px 10px;
      border: 2px solid #28a060;
      border-radius: 5px;
    }

    .icons {
        width: -9%;
        height: auto;
        max-width: 10%;
        max-height: 10%;
    }

    .icons img {
      height: 40px; /* Reduz o tamanho das imagens */
      width: auto; /* Mantém a proporção */
      margin-left: -113px; /* Move os ícones 10px para a esquerda */
      
    }

    .promo {
      background: #0a7540;
      color: white;
      text-align: center;
      margin: 2rem;
      padding: 4rem 1rem;
      border-radius: 35px;
      font-size: 1.2rem;
    }

    .produtos {
      padding: 2rem;
    }

    .produtos h2 {
      font-size: 1.5rem;
    }

    .items {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .item {
      background: #0a7540;
      color: white;
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
    }

     .auth-buttons button {
      padding: 8px 15px;
      background-color: #28a060;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      
    }

    /* Estilo do botão "Iniciar Sessão" com borda verde */
    .auth-buttons button:last-child {
      border: 2px solid #28a060; /* Borda verde */
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
  </style>
</head>
<body>
  <header>
    <div class="menu-btn">
      <span>☰</span>
      CATEGORIAS
    </div>

    <div class="logo-container">
      <img src="img/logo2.png" alt="Circuito Sustentável Logo" />
    </div>

    <div class="search-bar">
      <input type="text" placeholder="" />
      <img src="img/lupa.png" alt="Pesquisar" class="lupa" />
    </div>

    <div class="auth-section">
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <img src="img/usuario_padrao.png" alt="Foto do Usuário" style="width: 50px; height: 50px; border-radius: 50%;" />
      <?php else: ?>
        <div class="auth-buttons">
          <button onclick="location.href='cadastro.php'">Registrar</button>
          <button onclick="location.href='login.php'">Iniciar Sessão</button>
        </div>
      <?php endif; ?>
      <div class="icons">
        <img src="img/C+.png" alt="C+" />
        <img src="img/carrinho.png" alt="Carrinho de Compras" />
      </div>
    </div>
  </header>

  <main>
    <div class="promo">PROMOÇÃO SUPER FODA</div>

    <div class="produtos">
      <h2>Produtos</h2>
      <div class="items">
        <div class="item">ITEM</div>
        <div class="item">ITEM</div>
        <div class="item">ITEM</div>
        <div class="item">ITEM</div>
      </div>
    </div>
  </main>
</body>
</html>
