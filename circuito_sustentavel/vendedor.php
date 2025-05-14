<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'vendedor.php';
    header('Location: login.php');
    exit;
}

$id_vendedor = $_SESSION['vendedor_id'];
$sql = "SELECT * FROM Vendedor WHERE id_vendedor = '$id_vendedor'";
$resultado = $conexao->query($sql);
if ($resultado->num_rows > 0) {
    $vendedor = $resultado->fetch_assoc();
} else {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Verifica se o vendedor tem endereço cadastrado
$sql_endereco = "SELECT * FROM Endereco WHERE id_vendedor = '$id_vendedor'";
$res_endereco = $conexao->query($sql_endereco);
$tem_endereco = ($res_endereco && $res_endereco->num_rows > 0);
$endereco = $tem_endereco ? $res_endereco->fetch_assoc() : null;

// Exclusão de conta
$erro_excluir = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_conta'])) {
    $senha = $_POST['senha_excluir'] ?? '';
    $sql = "SELECT senha FROM Vendedor WHERE id_vendedor = '$id_vendedor'";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (password_verify($senha, $row['senha'])) {
            $conexao->query("DELETE FROM Vendedor WHERE id_vendedor = '$id_vendedor'");
            session_destroy();
            header('Location: login.php');
            exit;
        } else {
            $erro_excluir = "Senha incorreta!";
        }
    }
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área do Vendedor</title>
  
  <style>* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: sans-serif;
  background-color: #d4d3c8;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 20px;
}

.logo {
  font-size: 24px;
}

.logo .verde {
  color: green;
}

.logo .sub {
  font-size: 12px;
  margin-top: 4px;
}

.search-container {
      margin: 1rem auto 0 auto;
      display: flex;
      justify-content: center;
      position: relative;
    }

    .search-container input {
      width: 140%;
      padding: 10px 400px 10px 20px;
      border: none;
      background: #f3f2e7;
      border-radius: 20px;
      font-size: 1rem;
      margin-left:-40px;
    }

    .search-container img.lupa {
      position: absolute;
      right: 3%;
      top: 50%;
      transform: translateY(-50%);
      height: 20px;
    }

.icon {
  margin-left: 10px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

main {
  padding: 30px;
}

.boas-vindas {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 20px;
}

.avatar {
  font-size: 80px;
}

.mensagem h1 {
  font-size: 32px;
  font-weight: bold;
}

.mensagem p {
  font-size: 18px;
  margin-top: 5px;
}

.atalhos {
  display: flex;
  gap: 50px;
  margin: 30px 0;
  
  width: auto;
  margin-top:-30px;
 
}

/* Adicione este bloco para ajustar o tamanho das imagens dos atalhos */
.atalhos img {
  /* Remova ou comente esta linha para evitar conflito */
  /* height: 80px; */
  width: auto;
}

/* Estilos específicos para cada botão de atalho */

.assinatura-atalho img,
.produtos-atalho img,
.perguntas-atalho img,
.pedidos-atalho img {
  height: 90px;
  width: auto;
}
.dados-atalho img {
  height: 80px;
  width: auto;
  margin-top: 4px;
}

.painel {
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
}

.box {
  background: white;
  border-radius: 10px;
  padding: 20px;
  flex: 1 1 300px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.box h2 {
  font-size: 22px;
  text-decoration: none;
  color: green;
}

.excluir {
  margin-top: 20px;
  background: #d43131;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

footer .rodape {
  margin-top: 40px;
  padding: 20px;
  background: #f6f3b5;
}

.botao img {
      height: 200px; /* Reduz o tamanho das imagens */
      width: auto; /* Mantém a proporção */
      margin-left:10px;

}
.box img{
    height: 50px; /* Reduz o tamanho das imagens */
      width: auto; 
}
.user-info img{
    height: 50px; /* Reduz o tamanho das imagens */
      width: auto;
}

.modal-excluir {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}
.modal-excluir .modal-content {
  background: #fff;
  padding: 30px 25px;
  border-radius: 10px;
  box-shadow: 0 0 10px #0002;
  text-align: center;
  min-width: 300px;
}
.modal-excluir input[type="password"] {
  width: 90%;
  padding: 10px;
  margin: 15px 0;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 1rem;
  background: #f3f2e7;
}
.modal-excluir button {
  background: #d43131;
  color: white;
  padding: 8px 18px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin: 0 5px;
}
.modal-excluir .cancelar {
  background: #aaa;
}
.erro-excluir {
  color: #d43131;
  margin-bottom: 10px;
}
</style>
  <script>
    function abrirModalExcluir() {
      document.getElementById('modal-excluir').style.display = 'flex';
    }
    function fecharModalExcluir() {
      document.getElementById('modal-excluir').style.display = 'none';
    }
    function confirmarLogout() {
      return confirm('Tem certeza que deseja encerrar a sessão?');
    }
  </script>
</head>
<body>
    
  <header>
    <div class="logo">
     <a href="loja.php"> <img src="img/logo2.png" alt=""></a>
  
    </div>
    <div class="search-container">
      <input type="text" placeholder="Buscar...">
      <img src="img/lupa.png" alt="pesquisar" class="lupa">
    </div>
    <div class="user-info">
      <span class="user-icon"> <img src="img/user.png" alt=""></span>
      <p><?= htmlspecialchars($vendedor['nome']) ?></p>
    </div>
  </header>

  <main>
    <section class="boas-vindas">
      <div class="avatar"><img src="img/user.png" alt=""></div>
      <div class="mensagem">
        <h1>Olá, <?= htmlspecialchars($vendedor['nome']) ?></h1>
        <p>Aqui você encontra todas as informações relacionadas à sua conta</p>
      </div>
    </section>

    <div class="atalhos">
      <div class="dados-atalho"><a href="dados.php"><img src="img/dados.png" alt=""></a></div>
      <div class="assinatura-atalho"><a href="assinatura_usuario.php"><img src="img/assinatura.png" alt=""></a></div>
      <div class="perguntas-atalho"><a href="perguntas.php"><img src="img/perguntas.png" alt=""></a></div>
      <div class="pedidos-atalho"><a href="pedidos.php"><img src="img/pedidos.png" alt=""></a></div>
       <div class="produtos-atalho"><a href="produtos.php"><img src="img/produtos.png" alt=""></a></div>
    </div>

    <div class="painel">
      <div class="box">
        <h2>📍 Endereços</h2>
        <?php if ($tem_endereco): ?>
          <div style="margin-bottom:10px;">
            <strong><?= htmlspecialchars($endereco['rua']) ?>, <?= htmlspecialchars($endereco['numero']) ?></strong>
            <?php if (!empty($endereco['complemento'])): ?>
              <br><?= htmlspecialchars($endereco['complemento']) ?>
            <?php endif; ?>
            <br><?= htmlspecialchars($endereco['bairro']) ?>, <?= htmlspecialchars($endereco['cidade']) ?> - <?= htmlspecialchars($endereco['estado']) ?>
            <br>CEP: <?= htmlspecialchars($endereco['cep']) ?>
          </div>
          <a href="#">Ver todos</a>
        <?php else: ?>
          <a href="criar_endereco.php">
            <button style="background:#28a060;color:white;padding:10px 15px;border:none;border-radius:5px;cursor:pointer;">Criar Endereço</button>
          </a>
        <?php endif; ?>
      </div>

      <div class="box">
        <h2> Meus dados <a href="#" class="editar">Editar</a></h2>
        <img src="img/user.png" alt="">
        <p>🧍 <?= htmlspecialchars($vendedor['nome']) ?></p>
        <p>✉️ <?= htmlspecialchars($vendedor['email']) ?></p>
        <form method="post" style="display:inline;">
          <button class="excluir" type="button" onclick="abrirModalExcluir()">EXCLUIR CONTA</button>
        </form>
        <form method="post" style="display:inline;" onsubmit="return confirmarLogout();">
          <button class="excluir" type="submit" name="logout" style="background:#aaa;margin-left:10px;">Encerrar Sessão</button>
        </form>
      </div>
    </div>
  </main>

  <!-- Modal de exclusão -->
  <div class="modal-excluir" id="modal-excluir">
    <div class="modal-content">
      <form method="post">
        <h3>Confirme sua senha para excluir a conta</h3>
        <?php if (!empty($erro_excluir)): ?>
          <div class="erro-excluir"><?= htmlspecialchars($erro_excluir) ?></div>
        <?php endif; ?>
        <input type="password" name="senha_excluir" placeholder="Digite sua senha" required>
        <br>
        <button type="submit" name="excluir_conta">Excluir</button>
        <button type="button" class="cancelar" onclick="fecharModalExcluir()">Cancelar</button>
      </form>
    </div>
  </div>
  <script>
    // Fecha modal ao clicar fora do conteúdo
    document.addEventListener('click', function(e) {
      var modal = document.getElementById('modal-excluir');
      if (modal && e.target === modal) fecharModalExcluir();
    });
    // Se houve erro, reabre o modal
    <?php if (!empty($erro_excluir)): ?>
      abrirModalExcluir();
    <?php endif; ?>
  </script>
</body>
</html>
