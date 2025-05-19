<?php
session_start();
include 'conexao.php';

$erro = '';
$sucesso = '';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['redirect_after_login'] = 'vender.php';
    header('Location: login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$sql = "SELECT * FROM Cliente WHERE id_cliente = '$id_cliente'";
$res = $conexao->query($sql);
if ($res && $res->num_rows > 0) {
    $cliente = $res->fetch_assoc();
} else {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Se já é vendedor, redireciona
$sql_v = "SELECT * FROM Vendedor WHERE email = '{$cliente['email']}'";
$res_v = $conexao->query($sql_v);
if ($res_v && $res_v->num_rows > 0) {
    header('Location: vendedor.php');
    exit;
}

// Processa formulário de virar vendedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['virar_vendedor'])) {
    $foto_rg_frente = '';
    $foto_rg_verso = '';
    $foto_usuario = '';
    if (isset($_FILES['foto_rg_frente']) && $_FILES['foto_rg_frente']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto_rg_frente']['name'], PATHINFO_EXTENSION);
        $foto_rg_frente = 'rg_frente_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto_rg_frente']['tmp_name'], 'img/' . $foto_rg_frente);
    }
    if (isset($_FILES['foto_rg_verso']) && $_FILES['foto_rg_verso']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto_rg_verso']['name'], PATHINFO_EXTENSION);
        $foto_rg_verso = 'rg_verso_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto_rg_verso']['tmp_name'], 'img/' . $foto_rg_verso);
    }
    if (isset($_FILES['foto_usuario']) && $_FILES['foto_usuario']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto_usuario']['name'], PATHINFO_EXTENSION);
        $foto_usuario = 'vendedor_foto_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto_usuario']['tmp_name'], 'img/' . $foto_usuario);
    }

    $nome = $cliente['nome'];
    $email = $cliente['email'];
    $cpf = $cliente['cpf'];
    $telefone = $cliente['telefone'];
    $senha = $cliente['senha'];

    // Verifica se já existe vendedor com o mesmo email
    $sql_verifica = "SELECT id_vendedor FROM Vendedor WHERE email = '$email'";
    $res_verifica = $conexao->query($sql_verifica);
    if ($res_verifica && $res_verifica->num_rows > 0) {
        $erro = "Já existe um vendedor com este e-mail!";
    } else {
        $sql_insert = "INSERT INTO Vendedor (nome, email, senha, cpf, telefone) VALUES ('$nome', '$email', '$senha', '$cpf', '$telefone')";
        if ($conexao->query($sql_insert) === TRUE) {
            $id_vendedor = $conexao->insert_id;
            // Salva as fotos em uma tabela separada
            if ($foto_rg_frente || $foto_rg_verso || $foto_usuario) {
                $sql_foto = "INSERT INTO DocumentosVendedor (id_vendedor, foto_usuario, foto_rg_frente, foto_rg_verso) VALUES ('$id_vendedor', '$foto_usuario', '$foto_rg_frente', '$foto_rg_verso')";
                $conexao->query($sql_foto);
            }
            // Atualiza registros relacionados para manter histórico
            $conexao->query("UPDATE Cotidiano SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Endereco SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Moeda SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Assinatura SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Pedido SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Carrinho SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Postagem SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Comentario SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Pergunta SET id_vendedor = '$id_vendedor' WHERE id_cliente = '$id_cliente'");
            // Agora pode remover o id_cliente das tabelas filhas
            $conexao->query("UPDATE Cotidiano SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Endereco SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Moeda SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Assinatura SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Pedido SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Carrinho SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Postagem SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Comentario SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            $conexao->query("UPDATE Pergunta SET id_cliente = NULL WHERE id_cliente = '$id_cliente'");
            // Agora exclui o cliente
            $conexao->query("DELETE FROM Cliente WHERE id_cliente = '$id_cliente'");
            $_SESSION['vendedor_id'] = $id_vendedor;
            unset($_SESSION['usuario_id']);
            $sucesso = "Agora você é um vendedor! Redirecionando...";
            echo "<script>setTimeout(function(){ window.location.href = 'vendedor.php'; }, 2000);</script>";
        } else {
            $erro = "Erro ao criar conta de vendedor: " . $conexao->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Seja um Vendedor</title>
  <style>
    :root {
      --verde: #28a060;
      --verde-escuro: #1f804e;
      --fundo: #28a060;
      --texto: #222;
      --branco: #ffffff;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
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
    .section {
      min-height: 100vh;
      padding: 7rem 2rem 4rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: #e9f7ef;
      color: var(--texto);
      animation: fadeIn 1.5s ease;
    }
    @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(30px);}
      100% { opacity: 1; transform: translateY(0);}
    }
    .container {
      max-width: 1000px;
      width: 100%;
      text-align: center;
      color: var(--texto);
    }
    h1 {
      font-size: 3rem;
      color: #1f804e;
      margin-bottom: 1rem;
      animation: titleAnimation 1s ease-in-out;
    }
    @keyframes titleAnimation {
      0% { opacity: 0; transform: scale(0.8); color: var(--verde-escuro);}
      50% { opacity: 1; transform: scale(1.1); color: var(--verde);}
      100% { opacity: 1; transform: scale(1); color: var(--verde);}
    }
    p {
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      color: #222;
      animation: fadeIn 1.5s ease-in-out;
    }
    .beneficios {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
      animation: fadeIn 1.5s ease-in-out;
    }
    .beneficio {
      background: #fff;
      color: #222;
      padding: 2rem;
      border-left: 6px solid var(--verde);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s, box-shadow 0.3s, background 0.3s, color 0.3s;
    }
    .beneficio h3 {
      color: #1f804e;
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
      margin-bottom: 2.5rem;
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
    .form-vendedor {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(40,160,96,0.10);
      padding: 2rem 2rem 1.5rem 2rem;
      max-width: 420px;
      margin: 0 auto;
      margin-top: 2rem;
      text-align: left;
      color: #222;
      animation: fadeIn 1.2s;
    }
    .form-vendedor label {
      font-weight: bold;
      color: #28a060;
      display: block;
      margin-top: 1.2rem;
      margin-bottom: 0.3rem;
    }
    .form-vendedor input[type="text"],
    .form-vendedor input[type="email"],
    .form-vendedor input[type="password"],
    .form-vendedor input[type="file"] {
      width: 100%;
      padding: 10px 14px;
      border-radius: 10px;
      border: 2px solid #28a060;
      font-size: 1.08rem;
      margin-bottom: 10px;
      background: #f3f2e7;
      color: #222;
      transition: border 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px rgba(40,160,96,0.07);
      outline: none;
    }
    .form-vendedor input[readonly] {
      background: #e9e9e9;
      color: #888;
      border: 2px solid #ccc;
      cursor: not-allowed;
    }
    .form-vendedor .btn-principal {
      width: 100%;
      margin-top: 1.5rem;
      font-size: 1.1rem;
      padding: 1rem 0;
    }
    .msg-erro {
      color: #d43131;
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
    }
    .msg-sucesso {
      color: #28a060;
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
    }
    .footer-novo {
      background: #1b2430;
      color: #fff;
      padding: 2.5rem 1rem 1rem 1rem;
      margin-top: 2rem;
      width: 100vw;
      position: relative;
      left: 50%;
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
    }
    @media (max-width: 768px) {
      header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
      .beneficios { grid-template-columns: 1fr; }
      h1 { font-size: 2.2rem; }
      .form-vendedor { padding: 1.2rem 0.5rem; }
    }
    .fade-in-form {
      opacity: 0;
      transform: translateY(40px);
      pointer-events: none;
      transition: opacity 0.6s cubic-bezier(.4,0,.2,1), transform 0.6s cubic-bezier(.4,0,.2,1);
    }
    .fade-in-form.show {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }
  </style>
</head>
<body>
  <header>
    <a href="loja.php"><img src="img/logo2.png" alt="Logo" /></a>
    <nav>
      <a href="loja.php" style="color:#1f804e;font-weight:bold;">Loja</a>
      <a href="causa.html">Causa</a>
      <a href="suporte.html">Suporte</a>
      <a href="mais.html">Mais</a>
    </nav>
  </header>
  <section class="section">
    <div class="container">
      <h1>Transforme-se em um Vendedor Circuito Sustentável</h1>
      <p>
        Ganhe dinheiro vendendo produtos sustentáveis, alcance novos clientes e faça parte de uma comunidade que valoriza o meio ambiente. 
        <br>Cadastre-se como vendedor e tenha acesso a ferramentas exclusivas para impulsionar suas vendas!
      </p>
      <div class="beneficios">
        <div class="beneficio">
          <h3>+ Visibilidade</h3>
          <p>Seus produtos em destaque para milhares de clientes conscientes.</p>
        </div>
        <div class="beneficio">
          <h3>Gestão Fácil</h3>
          <p>Painel intuitivo para cadastrar, atualizar e acompanhar seus produtos e pedidos.</p>
        </div>
        <div class="beneficio">
          <h3>Receba Rápido</h3>
          <p>Pagamentos seguros e rápidos direto na sua conta.</p>
        </div>
        <div class="beneficio">
          <h3>Suporte Exclusivo</h3>
          <p>Equipe dedicada para ajudar você a vender mais e melhor.</p>
        </div>
        <div class="beneficio">
          <h3>Comunidade Verde</h3>
          <p>Faça parte de um movimento por um planeta mais sustentável.</p>
        </div>
        <div class="beneficio">
          <h3>Sem Taxa Inicial</h3>
          <p>Cadastre-se gratuitamente e comece a vender agora mesmo!</p>
        </div>
      </div>
      <div class="btns">
        <button class="btn btn-principal" id="mostrar-form-btn" type="button">Quero ser vendedor</button>
      </div>
      <div id="form-vendedor" class="fade-in-form" style="display:none;">
        <form class="form-vendedor" method="post" enctype="multipart/form-data" autocomplete="off">
          <h2 style="text-align:center;color:#1f804e;margin-bottom:1rem;">Confirme seus dados e complete o cadastro</h2>
          <?php if ($erro): ?>
            <div class="msg-erro"><?= $erro ?></div>
          <?php endif; ?>
          <?php if ($sucesso): ?>
            <div class="msg-sucesso"><?= $sucesso ?></div>
          <?php endif; ?>
          <label>Nome completo</label>
          <input type="text" value="<?= htmlspecialchars($cliente['nome']) ?>" readonly>
          <label>E-mail</label>
          <input type="email" value="<?= htmlspecialchars($cliente['email']) ?>" readonly>
          <label>CPF</label>
          <input type="text" value="<?= htmlspecialchars($cliente['cpf']) ?>" readonly>
          <label>Telefone</label>
          <input type="text" value="<?= htmlspecialchars($cliente['telefone']) ?>" readonly>
          <label for="foto_usuario">Uma foto sua</label>
          <input type="file" name="foto_usuario" id="foto_usuario" accept="image/*" required>
          <label for="foto_rg_frente">Foto do RG (frente)</label>
          <input type="file" name="foto_rg_frente" id="foto_rg_frente" accept="image/*" required>
          <label for="foto_rg_verso">Foto do RG (verso)</label>
          <input type="file" name="foto_rg_verso" id="foto_rg_verso" accept="image/*" required>
          <button type="submit" class="btn btn-principal" name="virar_vendedor">Cadastrar como Vendedor</button>
        </form>
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
    // Animação do botão para o formulário
    document.getElementById('mostrar-form-btn').onclick = function() {
      var btn = this;
      var form = document.getElementById('form-vendedor');
      btn.style.transition = 'transform 0.5s cubic-bezier(.4,0,.2,1), opacity 0.5s cubic-bezier(.4,0,.2,1)';
      btn.style.transform = 'translateY(-60px) scale(0.7)';
      btn.style.opacity = '0';
      setTimeout(function() {
        btn.style.display = 'none';
        form.style.display = 'block';
        setTimeout(function() {
          form.classList.add('show');
        }, 10);
        form.scrollIntoView({ behavior: 'smooth' });
      }, 500);
    };
  </script>
</body>
</html>
