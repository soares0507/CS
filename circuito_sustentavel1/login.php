<?php
session_start();
include 'conexao.php'; // Presumo que este arquivo configura $conexao

// Verifica se o usuário já está logado e redireciona
if (isset($_SESSION['usuario_id']) || isset($_SESSION['vendedor_id']) || isset($_SESSION['adm_id'])) {
    if (isset($_SESSION['adm_id'])) {
        header('Location: adm.php'); // Admins para adm.php
    } else {
        header('Location: loja.php'); // Clientes e Vendedores para loja.php
    }
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Tenta logar como Cliente
    $sql = "SELECT id_cliente, senha FROM Cliente WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    if ($stmt) {
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
        }
        $stmt->close();
    }

    // Tenta logar como Vendedor
    $sql_vendedor = "SELECT id_vendedor, senha FROM Vendedor WHERE email = ?";
    $stmt_vendedor = $conexao->prepare($sql_vendedor);
    if ($stmt_vendedor) {
        $stmt_vendedor->bind_param("s", $email);
        $stmt_vendedor->execute();
        $stmt_vendedor->store_result();
        if ($stmt_vendedor->num_rows > 0) {
            $stmt_vendedor->bind_result($id_vendedor, $senha_hash_vendedor);
            $stmt_vendedor->fetch();
            if (password_verify($senha, $senha_hash_vendedor)) {
                $_SESSION['vendedor_id'] = $id_vendedor;
                $redirect = $_SESSION['redirect_after_login'] ?? 'loja.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
        }
        $stmt_vendedor->close();
    }
    
    // Tenta logar como ADM
    $sql_adm = "SELECT id, senha FROM ADM WHERE email = ?";
    $stmt_adm = $conexao->prepare($sql_adm);
    if ($stmt_adm) {
        $stmt_adm->bind_param("s", $email);
        $stmt_adm->execute();
        $stmt_adm->store_result();
        if ($stmt_adm->num_rows > 0) {
            $stmt_adm->bind_result($adm_id, $adm_senha_hash);
            $stmt_adm->fetch();
            if (password_verify($senha, $adm_senha_hash)) {
                $_SESSION['adm_id'] = $adm_id;
                header('Location: adm.php'); // Página do administrador
                exit;
            }
        }
        $stmt_adm->close();
    }

    $erro = "E-mail ou senha inválidos!";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Circuito Sustentável</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <style>
    :root {
        --verde: #28a060;
        --verde-escuro: #1e7c4b;
        --verde-claro-fundo: #f0f9f4;
        --cinza-claro: #f4f6f8;
        --cinza-texto: #5f6c7b;
        --cinza-escuro: #2c3e50;
        --branco: #ffffff;
        --vermelho-erro: #e74c3c;
        --sombra-padrao: 0 8px 25px rgba(0, 0, 0, 0.08);
        --sombra-hover-forte: 0 12px 35px rgba(0, 0, 0, 0.12);
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --transition-fast: 0.2s;
        --transition-std: 0.3s;
        --font-principal: 'Poppins', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: var(--font-principal);
        line-height: 1.6;
        color: var(--cinza-texto);
        background-color: var(--verde);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .container-geral {
        width: 100%;
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        margin-bottom: 60px; /* ADICIONADO: Espaço antes do footer */
    }

    .login-wrapper {
        display: flex;
        background-color: var(--branco);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--sombra-padrao);
        overflow: hidden;
        max-width: 950px;
        width: 100%;
    }

    .login-image-container {
        flex-basis: 45%;
        display: none; /* Oculto por padrão, visível em telas maiores */
        align-items: center;
        justify-content: center;
        padding: 0; /* Removido padding para a imagem preencher se for o caso */
        /* background-color: var(--verde-claro-fundo); Removido para imagem com fundo próprio */
    }
    .login-image-container img {
        width: 100%; /* Imagem ocupa toda a largura do container */
        height: 100%; /* Imagem ocupa toda a altura do container */
        object-fit: cover; /* Cobre o espaço, pode cortar partes da imagem */
    }

    .login-form-section {
        flex-basis: 100%;
        padding: 30px 40px;
    }

    .login-form-section h1 {
        font-size: 2.2em;
        color: var(--cinza-escuro);
        font-weight: 700;
        text-align: center;
        margin-bottom: 25px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-top: 18px;
        margin-bottom: 7px;
        font-size: 0.9em;
        color: var(--cinza-escuro);
    }

    input[type="email"],
    input[type="password"],
    input[type="text"] { /* Aplicado a input[type="text"] também */
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 10px;
        border-radius: var(--border-radius-md);
        border: 1px solid #ccd0d5;
        font-size: 1em;
        font-family: var(--font-principal);
        color: var(--cinza-escuro);
        background-color: var(--branco);
        transition: border-color var(--transition-std), box-shadow var(--transition-std);
    }
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="text"]:focus { /* Aplicado a input[type="text"] também */
        outline: none;
        border-color: var(--verde);
        box-shadow: 0 0 0 3px rgba(40, 160, 96, 0.15);
    }

    .toggle-container {
        margin-top: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .bolinha {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid var(--cinza-texto);
        background-color: var(--branco);
        transition: background-color var(--transition-std), border-color var(--transition-std);
    }
    .bolinha.ativa {
        background-color: var(--verde);
        border-color: var(--verde);
    }

    .toggle-label {
        font-size: 0.85em;
        color: var(--cinza-texto);
    }

    .error-message {
        color: var(--vermelho-erro);
        font-size: 0.9em;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .button-row {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }

    .btn {
        display: inline-block; padding: 12px 25px; font-weight: 600;
        text-decoration: none; border-radius: var(--border-radius-md);
        transition: all var(--transition-std) cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer; border: 2px solid transparent; font-size: 0.95em;
        box-shadow: var(--sombra-padrao); width: 100%;
        text-align: center;
    }
    .btn-primary { background-color: var(--verde); color: var(--branco); }
    .btn-primary:hover {
        background-color: var(--verde-escuro);
        transform: translateY(-3px);
        box-shadow: var(--sombra-hover-forte);
    }
    .btn-secondary {
        background-color: var(--cinza-claro);
        color: var(--cinza-escuro);
        border: 1px solid #ccd0d5;
        box-shadow: none;
    }
    .btn-secondary:hover {
        background-color: #e4e6e9;
        transform: translateY(-2px);
    }

    /* Header Estilo */
    .site-header {
        width: 100%;
        padding: 15px 0;
        background-color: var(--branco);
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        z-index: 1000;
    }
    .header-container {
        width: 90%;
        max-width: 1140px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .site-header img.logo {
        height: 50px;
        width: auto;
    }

    /* Footer Estilo */
    .site-footer-bottom {
        background-color: var(--cinza-escuro); color: #b0bec5;
        padding: 50px 0 30px;
        font-size: 0.9em;
        width: 100%;
        margin-top: 5%;
        /* margin-top: auto;  Normalmente não é necessário se flex-grow funciona bem */
    }
    .footer-content-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px; margin-bottom: 30px;
        width: 90%; max-width: 1140px; margin: 0 auto 30px auto;
    }
    .footer-col h4 { font-size: 1.1em; color: var(--branco); font-weight: 600; margin-bottom: 15px; }
    .footer-col p, .footer-col a { color: #b0bec5; text-decoration: none; margin-bottom: 8px; display: block; font-size: 0.95em; }
    .footer-col a:hover { color: var(--verde); transform: translateX(2px); transition: transform var(--transition-fast); }
    .footer-copyright { text-align: center; padding-top: 30px; border-top: 1px solid #4a5c6a; color: #78909c; width: 90%; max-width: 1140px; margin: 0 auto; }

    /* Responsividade */
    @media (min-width: 768px) {
        .login-form-section {
            flex-basis: 55%;
        }
        .login-image-container {
            display: flex;
            flex-basis: 45%;
        }
        .button-row {
            flex-direction: row;
            justify-content: space-between;
        }
        .btn {
            width: auto;
        }
        .button-row .btn:first-child {
            flex-grow: 1;
            margin-right: 10px;
        }
    }
    @media (min-width: 992px) {
        .login-form-section {
            padding: 40px 50px;
        }
        .login-form-section h1 {
            font-size: 2.5em;
        }
    }
  </style>
</head>
<body>

<header class="site-header">
  <div class="header-container">
    <a href="tela_inicial.php">
      <img class="logo" src="img/logo2.png" alt="Circuito Sustentável"/>
    </a>
  </div>
</header>

<div class="container-geral">
    <div class="login-wrapper">
        <div class="login-image-container">
            <img style="width:70%; height:auto;" src="img/user.png" alt="Design moderno e abstrato para login"/>
        </div>
        <div class="login-form-section">
            <h1>Acesse sua Conta</h1>
            <form method="POST" action="login.php">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />

                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required />

                <div class="toggle-container" onclick="toggleSenha()">
                    <div id="bolinha" class="bolinha"></div>
                    <span class="toggle-label">Mostrar senha</span>
                </div>

                <?php if (!empty($erro)): ?>
                <p class="error-message"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>

                <div class="button-row">
                    <button type="submit" class="btn btn-primary">Continuar</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='cadastro.php'">Criar Conta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="site-footer-bottom">
    <div class="footer-content-grid">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Oferecendo solução para o meio ambiente e seu bolso.</p>
      </div>
      <div class="footer-col">
        <h4>Navegue</h4>
        <a href="tela_inicial.php">Início</a>
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
    function toggleSenha() {
      const senhaInput = document.getElementById("senha");
      const bolinha = document.getElementById("bolinha");

      if (senhaInput.type === "password") {
        senhaInput.type = "text";
        bolinha.classList.add("ativa");
      } else {
        senhaInput.type = "password";
        bolinha.classList.remove("ativa");
      }
    }
  </script>
</body>
</html>