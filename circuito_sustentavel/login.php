<?php
session_start();
include 'conexao.php';

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
      margin-top: -50px;
      width: 100%; /* Garante que o container ocupe toda a largura */
      padding-left: 50px; /* Adiciona um espaçamento à esquerda */
      margin-left: 20%;
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
      max-width: 1500px;
      margin-left: 2%;
      color:white;
      min-width: 1580px;
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

    .button {
      display: block;
      margin: 30px auto 0 auto;
      padding: 20px 35px;
      background-color:rgb(0, 102, 46);
      border: none;
      border-radius: 10px;
      color:rgb(255, 255, 255);
      font-weight: bold;
      font-size: 18px;
      cursor: pointer;
      margin-left: 17%; /* Alinha o botão à esquerda */
      margin-top: 6%;
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
  </style>
</head>
<body>

<header>
  <img class="im" src="img/logo2.png" alt="Circuito Sustentável"/>
</header>
<h1 class="mate">Login</h1>
  <div class="container">
    <!-- Formulário de cadastro -->
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

        <button type="submit" class="button">Continuar</button>
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

</body>
</html>
