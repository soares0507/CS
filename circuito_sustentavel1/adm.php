<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['adm_id'])) {
    $_SESSION['redirect_after_login'] = 'adm.php';
    header('Location: login.php');
    exit;
}

$id_adm = $_SESSION['adm_id'];
$sql = "SELECT * FROM ADM WHERE id = '$id_adm'";
$resul = $conexao->query($sql);
if ($resul->num_rows > 0) {
    $adm = $resul->fetch_assoc();
    $usuario = [
        'nome' => 'Administrador',
        'email' => $adm['email']
    ];
} else {
    session_destroy();
    header('Location: login.php');
    exit;
}

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
  <title>Área do Cliente</title>
  
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
.vender-atalho img,
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
  <script>
    function confirmarLogout() {
      return confirm('Tem certeza que deseja encerrar a sessão?');
    }
  </script>
</head>
<body>
    
  <header>
    <div class="logo">
     <a href="loja.php"> <img src="img/logo2.png" alt=""> </a>
  
    </div>
    <div class="search-container">
      <input type="text" placeholder="Buscar...">
      <img src="img/lupa.png" alt="pesquisar" class="lupa">
    </div>
    <div class="user-info">
      <span class="user-icon"> <img src="img/user.png" alt=""></span>
      <p><?= htmlspecialchars($usuario['nome']) ?></p>
    </div>
  </header>

  <main>
    <section class="boas-vindas">
      <div class="avatar"><img src="img/user.png" alt=""></div>
      <div class="mensagem">
        <h1>Olá, <?= htmlspecialchars($usuario['nome']) ?></h1>
        <p>Bem-vindo à área administrativa.</p>
      </div>
    </section>

    <div class="atalhos">
      <div class="dados-atalho"><a href="dados.php"><img src="img/dados.png" alt="Dados"></a></div>
      <div class="assinatura-atalho"><a href="assinatura_usuario.php"><img src="img/assinatura.png" alt="Assinatura"></a></div>
      <div class="perguntas-atalho"><a href="perguntas.php"><img src="img/perguntas.png" alt="Perguntas"></a></div>
      <div class="pedidos-atalho"><a href="pedidos.php"><img src="img/pedidos.png" alt="Pedidos"></a></div>
      <div class="vender-atalho"><a href="vender.php"><img src="img/vender.png" alt="Vender"></a></div>
    </div>

    <div class="painel">
      <div class="box" style="max-width:400px;margin:auto;">
        <img src="img/user.png" alt="">
        <h2>Administrador</h2>
        <p>✉️ <?= htmlspecialchars($usuario['email']) ?></p>
        <form method="post" style="display:inline;" onsubmit="return confirmarLogout();">
          <button class="excluir" type="submit" name="logout" style="background:#aaa;margin-left:10px;">Encerrar Sessão</button>
        </form>
      </div>
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