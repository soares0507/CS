<?php
session_start();
if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'produtos.php';
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gerenciar Produtos</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      color: #222;
      min-height: 100vh;
    }
    header {
      width: 100%;
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      position: fixed;
      top: 0;
      z-index: 100;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .logo img {
      height: 40px;
    }
    .header-title {
      font-size: 1.5rem;
      color: #1f804e;
      font-weight: bold;
      margin-left: 10px;
    }
    main {
      margin-top: 100px;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 80vh;
    }
    .container {
      background: #fff; /* alterado de #e9f7ef para branco */
      border-radius: 18px;
      box-shadow: 0 4px 16px rgba(40,160,96,0.08);
      padding: 2.5rem 2rem 2rem 2rem;
      max-width: 500px;
      width: 100%;
      margin: 2rem auto;
      text-align: center;
    }
    h1 {
      color: #1f804e;
      font-size: 2.2rem;
      margin-bottom: 2rem;
    }
    .botoes {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
      margin-top: 1.5rem;
    }
    .botao-acao {
      padding: 1rem 2rem;
      font-size: 1.1rem;
      border-radius: 8px;
      border: none;
      background: #28a060;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
      box-shadow: 0 2px 8px rgba(40,160,96,0.08);
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .botao-acao:hover {
      background: #1f804e;
      transform: scale(1.04);
    }
    @media (max-width: 600px) {
      .container {
        padding: 1.2rem 0.5rem;
      }
      h1 {
        font-size: 1.3rem;
      }
      .botao-acao {
        font-size: 1rem;
        padding: 0.8rem 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
      
    </div>
    <div style="margin-top: 10px; margin-left: 5px;">
      <button onclick="window.location.href='vendedor.php'" style="background: none; border: none; font-size: 2rem; color: #1f804e; cursor: pointer; font-weight: bold;">&#10005;</button>
    </div>
  </header>
  <main>
    <div class="container">
      <h1>Gerenciar Produtos</h1>
      <div class="botoes">
        <button class="botao-acao" onclick="location.href='cadastrar_produto.php'">Cadastrar Produto</button>
        <button class="botao-acao" onclick="location.href='ver_produtos.php'">Editar Produto</button>
        <button class="botao-acao" onclick="location.href='pedidos_vendedor.php'">Pedidos Recebidos</button>
        
      </div>
    </div>
  </main>
</body>
</html>
