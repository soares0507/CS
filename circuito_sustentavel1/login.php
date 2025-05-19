<?php
session_start();
include 'conexao.php';

// Redireciona se já estiver logado
if (isset($_SESSION['usuario_id']) || isset($_SESSION['vendedor_id'])) {
    header('Location: loja.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Tenta login como Cliente
    $sql = "SELECT id_cliente, senha FROM Cliente WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_cliente, $senha_hash);
        $stmt->fetch();
        if (password_verify($senha, $senha_hash)) {
            $_SESSION['usuario_id'] = $id_cliente;
            $redirect = $_SESSION['redirect_after_login'] ?? 'loja.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        }
    } else {
        // Tenta login como Vendedor
        $sql = "SELECT id_vendedor, senha FROM Vendedor WHERE email = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id_vendedor, $senha_hash);
            $stmt->fetch();
            if (password_verify($senha, $senha_hash)) {
                $_SESSION['vendedor_id'] = $id_vendedor;
                $redirect = $_SESSION['redirect_after_login'] ?? 'loja.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
        }
    }
    $erro = "E-mail ou senha inválidos!";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>login</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "inter", sans-serif;
      background-color: #2ab769;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-x: hidden; /* Impede rolagem horizontal */
      width: 100vw;
      box-sizing: border-box;
    }

    header {
      width: 100%; /* Faz o header ocupar toda a largura */
      text-align: center;
      margin: 0;
      padding: 15px 0;
      background-color: #ffffff;
      border-bottom: 1px solid #ccc;
      box-sizing: border-box; /* Garante que padding e bordas sejam incluídos no tamanho total */
    }

    header img {
      height: 60px;
      max-width: 100%; /* Garante que a imagem não ultrapasse o tamanho do header */
    }

    .container {
      display: flex;
      justify-content: flex-start; /* Alinha o formulário e o botão à esquerda */
      margin-bottom: 50px;
      margin-top: -54px;
      width: 100%; /* Garante que o container ocupe toda a largura */
      padding-left: 50px; /* Adiciona um espaçamento à esquerda */
      margin-left: -60%; /* Corrige o deslocamento lateral */
      max-width: 600px; /* Limita a largura máxima */
      box-sizing: border-box;
    }

    .form-box {
      background-color: #2ab769;
      padding: 30px;
      border-radius: 30px;
      width: 320px;
      color: white;
    }

    h1 {
      font-size: 80px;
      max-width: 100%;
      margin-left: -70%;
      color:white;
      min-width: 100px;
      text-align: left;
      margin-top: 6%;
    }

    label {
      display: block;
      font-weight: bold;
      margin-top: 15px;
      font-size: 20px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 20px;
      border: 2px solid #4d4d4d;
      font-size: 16px;
      box-sizing: border-box;
    }

    .button-row {
      display: flex;
      gap: 19px;
      justify-content: flex-start;
      margin-top: 6%;
      margin-left: 1%;
    }

    .button {
      padding: 10px 35px;
      background-color:white;
      border: none;
      border-radius: 10px;
      color: #2e7d32;
      font-weight: bold;
      font-size: 18px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .button:hover {
      background-color: #1f804e;
      color: #fff;
    }

    .toggle-container {
      margin-top: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .bolinha {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: 2px solid #2ab769;
      background-color: white;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .bolinha.ativa {
      background-color:rgb(0, 70, 31);
    }

    .toggle-label {
      font-size: 16px;
      color: white;
      margin-top: -1px;
    }

    .im{
        width: 20%;
        height: auto;
        max-width: 600px;
        margin: 0 auto;
        display: block;
    }
    .ma{
        width: 20%;
        height: auto;
        max-width: 600px;
        margin: 0 auto;
        display: block;
        margin-right: 15%;
        margin-top: -20%;
    }
    .footer-novo {
      background: #1b2430;
      color: #fff;
      padding: 2.5rem 1rem 1rem 1rem;
      margin-top: 13rem;
      width: 100vw;
      position: relative;
      left: 0%;
      right: 50%;
      margin-left: -50vw;
      margin-right: -50vw;
      box-sizing: border-box;
      overflow-x: hidden;
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
  <img class="im" src="img/logo2.png" alt="Circuito Sustentável"/>
</header>
<h1 class="mate">Login</h1>
  <div class="container">
    
    <div class="form-box">
     
      <form method="POST" action="login.php">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required />

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required />

        <div class="toggle-container">
          <div id="bolinha" class="bolinha" onclick="toggleSenha()"></div>
          <label class="toggle-label">Mostrar senha</label>
        </div>

        <?php if (isset($erro)): ?>
          <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>

        <div class="button-row">
          <button type="submit" class="button">Continuar</button>
          <button type="button" class="button" onclick="window.location.href='cadastro.php'">Registra-se</button>
        </div>
      </form>
    </div>
  </div>

  <img class="ma" src="img/login.png" alt="Circuito Sustentável"/>

  <script>
    function toggleSenha() {
      const senha = document.getElementById("senha");
      const bolinha = document.getElementById("bolinha");

      if (senha.type === "password") {
        senha.type = "text";
        bolinha.classList.add("ativa");
      } else {
        senha.type = "password";
        bolinha.classList.remove("ativa");
      }
    }
  </script>
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
