<?php
session_start();
include 'conexao.php';

$usuario = null;
$tipo = null;
$sucesso = '';
$erro = '';

if (isset($_SESSION['usuario_id'])) {
    $id = $_SESSION['usuario_id'];
    $sql = "SELECT * FROM Cliente WHERE id_cliente = '$id'";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) {
        $usuario = $res->fetch_assoc();
        $tipo = 'cliente';
    }
} elseif (isset($_SESSION['vendedor_id'])) {
    $id = $_SESSION['vendedor_id'];
    $sql = "SELECT * FROM Vendedor WHERE id_vendedor = '$id'";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) {
        $usuario = $res->fetch_assoc();
        $tipo = 'vendedor';
    }
}

if (!$usuario) {
    header('Location: login.php');
    exit;
}

// Função para editar dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_dados'])) {
    $novo_nome = trim($_POST['nome']);
    $novo_email = trim($_POST['email']);
    $novo_telefone = trim($_POST['telefone']);
    $novo_cpf = $usuario['cpf']; // CPF não pode ser alterado

    // Verifica se o email já existe para outro usuário
    if ($tipo === 'cliente') {
        $sql_verifica = "SELECT id_cliente FROM Cliente WHERE email = '$novo_email' AND id_cliente != '$id'";
    } else {
        $sql_verifica = "SELECT id_vendedor FROM Vendedor WHERE email = '$novo_email' AND id_vendedor != '$id'";
    }
    $res_verifica = $conexao->query($sql_verifica);
    if ($res_verifica && $res_verifica->num_rows > 0) {
        $erro = "E-mail já está em uso por outro usuário!";
    } else {
        if ($tipo === 'cliente') {
            $sql_upd = "UPDATE Cliente SET nome='$novo_nome', email='$novo_email', telefone='$novo_telefone' WHERE id_cliente='$id'";
        } else {
            $sql_upd = "UPDATE Vendedor SET nome='$novo_nome', email='$novo_email', telefone='$novo_telefone' WHERE id_vendedor='$id'";
        }
        if ($conexao->query($sql_upd) === TRUE) {
            $sucesso = "Dados atualizados com sucesso!";
            // Atualiza os dados exibidos
            if ($tipo === 'cliente') {
                $sql = "SELECT * FROM Cliente WHERE id_cliente = '$id'";
            } else {
                $sql = "SELECT * FROM Vendedor WHERE id_vendedor = '$id'";
            }
            $res = $conexao->query($sql);
            if ($res && $res->num_rows > 0) {
                $usuario = $res->fetch_assoc();
            }
        } else {
            $erro = "Erro ao atualizar dados: " . $conexao->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Meus Dados</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #d4d3c8;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      overflow-x: hidden; /* Impede rolagem horizontal */
      width: 100vw;
      box-sizing: border-box;
    }
    header {
      width: 100%;
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      position: relative;
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
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-right: 65px;
    }
    .user-info img {
      height: 50px;
      width: auto;
      
    }
    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 60px;
      margin-bottom: 40px;
    }
    .dados-container {
      background: #fff;
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
    .dados-lista {
      text-align: left;
      margin: 0 auto;
      max-width: 350px;
      font-size: 1.1rem;
    }
    .dados-lista dt {
      font-weight: bold;
      color: #28a060;
      margin-top: 1.2rem;
    }
    .dados-lista dd {
      margin-left: 0;
      margin-bottom: 0.5rem;
      color: #222;
    }
    .dados-lista input[type="text"],
    .dados-lista input[type="email"] {
      width: 95%;
      padding: 10px 16px;
      border-radius: 12px;
      border: 2px solid #28a060;
      font-size: 1.08rem;
      margin-top: 6px;
      margin-bottom: 10px;
      background: #f3f2e7;
      color: #222;
      transition: border 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px rgba(40,160,96,0.07);
      outline: none;
    }
    .dados-lista input[type="text"]:focus,
    .dados-lista input[type="email"]:focus {
      border: 2px solid #1f804e;
      box-shadow: 0 0 0 2px #28a06033;
      background: #fff;
    }
    .dados-lista .input-edit[readonly] {
      background: #e9e9e9;
      color: #888;
      border: 2px solid #ccc;
      cursor: not-allowed;
    }
    .voltar-btn {
      margin-top: 2rem;
      padding: 12px 28px;
      background: #28a060;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
    }
    .voltar-btn:hover {
      background: #1f804e;
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
    .edit-btn {
      margin-top: 2rem;
      padding: 12px 28px;
      background: #1f804e;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
      margin-right: 10px;
    }
    .edit-btn:hover {
      background: #28a060;
    }
    .edit-actions {
      text-align: center;
      margin-top: 1.2rem;
      display: flex;
      gap: 10px;
      justify-content: center;
    }
    .msg-sucesso {
      color: #28a060;
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
    }
    .msg-erro {
      color: #d43131;
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
      
    }
  </style>
  <script>
    function habilitarEdicao() {
      document.querySelectorAll('.editavel').forEach(function(span) {
        span.style.display = 'none';
      });
      document.querySelectorAll('.input-edit').forEach(function(input) {
        input.style.display = 'inline-block';
      });
      // Esconde os campos readonly-info para não duplicar informação
      document.querySelectorAll('.readonly-info').forEach(function(span) {
        span.style.display = 'inline-block';
      });
      document.getElementById('edit-btn').style.display = 'none';
      document.getElementById('edit-actions').style.display = 'flex';
      // Esconde os campos editáveis que não são input
      document.querySelectorAll('.editavel').forEach(function(span) {
        span.style.display = 'none';
      });
    }
    function cancelarEdicao() {
      document.querySelectorAll('.editavel').forEach(function(span) {
        span.style.display = '';
      });
      document.querySelectorAll('.input-edit').forEach(function(input) {
        input.style.display = 'none';
      });
      document.querySelectorAll('.readonly-info').forEach(function(span) {
        span.style.display = 'none';
      });
      document.getElementById('edit-btn').style.display = '';
      document.getElementById('edit-actions').style.display = 'none';
    }
    window.onload = function() {
      cancelarEdicao();
    };
  </script>
</head>
<body>
  <header>
    <div class="logo">
      <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
      <span class="header-title">Meus Dados</span>
    </div>
   <div style="margin-left:auto;">
            <button onclick="window.location.href='<?= $tipo === 'cliente' ? 'usuario.php' : 'vendedor.php' ?>'" style="background: none; border: none; font-size: 2rem; color: #1f804e; cursor: pointer; font-weight: bold; ">&#10005;</button>
        </div>
    <div class="user-info">
    </div>
  </header>
   
  <main>
    <div class="dados-container">
      <h1>Meus Dados</h1>
      <?php if ($sucesso): ?>
        <div class="msg-sucesso"><?= $sucesso ?></div>
      <?php endif; ?>
      <?php if ($erro): ?>
        <div class="msg-erro"><?= $erro ?></div>
      <?php endif; ?>
      <form method="post" autocomplete="off">
        <input type="hidden" name="editar_dados" value="1" />
        <dl class="dados-lista">
          <dt>Nome:</dt>
          <dd>
            <span class="editavel"><?= htmlspecialchars($usuario['nome']) ?></span>
            <input type="text" name="nome" class="input-edit" value="<?= htmlspecialchars($usuario['nome']) ?>" style="display:none;" required />
          </dd>
          <dt>Email:</dt>
          <dd>
            <span class="editavel"><?= htmlspecialchars($usuario['email']) ?></span>
            <input type="email" name="email" class="input-edit" value="<?= htmlspecialchars($usuario['email']) ?>" style="display:none;" required />
          </dd>
          <?php if ($tipo === 'cliente' || $tipo === 'vendedor'): ?>
            <dt>CPF:</dt>
            <dd>
              <span class="editavel"><?= htmlspecialchars($usuario['cpf']) ?></span>
              <span class="readonly-info" style="display:none;"><?= htmlspecialchars($usuario['cpf']) ?></span>
            </dd>
          <?php endif; ?>
          <dt>Telefone:</dt>
          <dd>
            <span class="editavel"><?= htmlspecialchars($usuario['telefone']) ?></span>
            <input type="text" name="telefone" class="input-edit" value="<?= htmlspecialchars($usuario['telefone']) ?>" style="display:none;" required />
          </dd>
          <?php if ($tipo === 'cliente' && isset($usuario['premium'])): ?>
            <dt>Premium:</dt>
            <dd>
              <span class="editavel"><?= $usuario['premium'] ? 'Sim' : 'Não' ?></span>
              <span class="readonly-info" style="display:none;"><?= $usuario['premium'] ? 'Sim' : 'Não' ?></span>
            </dd>
          <?php endif; ?>
          <dt>Data de Cadastro:</dt>
          <dd>
            <span class="editavel"><?= date('d/m/Y H:i', strtotime($usuario['data_criacao'])) ?></span>
            <span class="readonly-info" style="display:none;"><?= date('d/m/Y H:i', strtotime($usuario['data_criacao'])) ?></span>
          </dd>
        </dl>
        <button type="button" class="edit-btn" id="edit-btn" onclick="habilitarEdicao()">Editar Dados</button>
        <div class="edit-actions" id="edit-actions" style="display:none;">
          <button type="submit" class="edit-btn">Salvar</button>
          <button type="button" class="edit-btn" style="background:#d43131;" onclick="cancelarEdicao()">Cancelar</button>
        </div>
      </form>
    </div>
  </main>
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