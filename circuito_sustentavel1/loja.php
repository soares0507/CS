<?php
session_start();
include 'conexao.php';

$usuario_logado = false;
$usuario_nome = '';

if (isset($_SESSION['usuario_id'])) {
    $usuario_logado = true;
    $id_cliente = $_SESSION['usuario_id'];
    $sql = "SELECT * FROM Cliente WHERE id_cliente = '$id_cliente'";
    $resultado = $conexao->query($sql);
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $usuario_nome = $usuario['nome'];
    }
} elseif (isset($_SESSION['vendedor_id'])) {
    $usuario_logado = true;
    $id_vendedor = $_SESSION['vendedor_id'];
    $sql = "SELECT * FROM Vendedor WHERE id_vendedor = '$id_vendedor'";
    $resultado = $conexao->query($sql);
    if ($resultado->num_rows > 0) {
        $vendedor = $resultado->fetch_assoc();
        $usuario_nome = $vendedor['nome'];
    }
}
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
      margin-left: 269px;
    }

    .search-bar img.lupa {
      position: absolute;
      right: 14%;
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
      margin-left: -119px; /* Move os ícones 10px para a esquerda */
      
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
      color:rgb(0, 0, 0);
      height: 320px; /* Aumentado */
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem; /* Aumentado */
    }

    .item-content {
      text-align: center;
    }

    .item-img {
      width: 160px;
      height: 160px;
      object-fit: cover;
      border-radius: 10px;
      display: block;
      margin: 0 auto 16px auto;
    }

    .item-nome {
      font-weight: bold;
      font-size: 1.25rem;
    }

    .item-preco {
      font-size: 1.1rem;
    }

    .item-desc {
      font-size: 1rem;
      color: #000000;
    }

    .item-btn-area {
      margin-top: 12px;
    }

    .item-btn-ver {
      background: #28a060;
      color: #fff;
      border: none;
      padding: 10px 22px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
    }

    .item-btn-ver:hover {
      background: #1b5e20;
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

    .user-info img {
      height: 50px; 
      width: auto; 
      margin-left: -139px; 
      margin-top: -13px;
    }

  

    .category-list {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 300px;
      background: white;
      box-shadow: 2px 0 6px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      overflow-y: auto;
      padding: 1rem;
    }

    .category-list img {
      height: 30px; 
      width: auto; 
     
    }

    .category-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .category-header img {
      height: 30px;
      width: auto;
    }

    .close-btn {
      font-size: 1.5rem;
      font-weight: bold;
      color: #28a060;
      cursor: pointer;
      margin-right: 10px;
      margin-top: 5px;
    }

    .close-btn:hover {
      color: #1b5e20;
    }

    .category-list h3 {
      text-align: center;
      margin-bottom: 1rem;
      font-size: 1.2rem;
      color: #28a060;
    }

    .category-list ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .category-list ul li {
      padding: 0.5rem 0;
      border-bottom: 1px solid #ddd;
      font-size: 1rem;
      color: #333;
      cursor: pointer;
    }

    .category-list ul li:last-child {
      border-bottom: none;
    }

    .category-list ul li:hover {
      color: #28a060;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    .footer-novo {
  background: #1b2430;
  color: #fff;
  padding: 2.5rem 1rem 1rem 1rem;
  margin-top: 2rem;
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
    function toggleCategoryList() {
      const overlay = document.getElementById('overlay');
      const categoryList = document.getElementById('category-list');
      const isVisible = categoryList.style.display === 'block';

      categoryList.style.display = isVisible ? 'none' : 'block';
      overlay.style.display = isVisible ? 'none' : 'block';
    }
  </script>
</head>
<body>
  <div id="overlay" class="overlay" onclick="toggleCategoryList()"></div>
  <header>
    <div class="menu-btn" onclick="toggleCategoryList()">
      <span>☰</span>
      CATEGORIAS
    </div>

    <div id="category-list" class="category-list">
      <div class="category-header">
        <img src="img/logo2.png" alt="Logo" />
        <span class="close-btn" onclick="toggleCategoryList()">X</span>
      </div>
      <h3>CATEGORIAS</h3>
      <ul>
        <li>Processadores</li>
        <li>Placas de Vídeo</li>
        <li>Memórias RAM</li>
        <li>Placas-Mãe</li>
        <li>Fontes de Alimentação</li>
        <li>Coolers</li>
        <li>Gabinetes</li>
        <li>Armazenamento (HDD/SSD)</li>
      </ul>
    </div>

    <div class="logo-container">
      <img src="img/logo2.png" alt="Circuito Sustentável Logo" />
    </div>

    <div class="search-bar">
      <form method="get" action="loja.php" style="width:100%;">
        <input type="text" name="busca" placeholder="Pesquisar produtos..." value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>" />
        <button type="submit" style="position:absolute;right:14%;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;">
          <img src="img/lupa.png" alt="Pesquisar" class="lupa" />
        </button>
      </form>
    </div>

    <div class="auth-section">
      <?php if ($usuario_logado): ?>
        <div class="user-info">
          <a href="<?= isset($_SESSION['usuario_id']) ? 'usuario.php' : 'vendedor.php' ?>">
            <img src="img/user.png" alt="Usuário" />
          </a>
         
        </div>
      <?php else: ?>
        <div class="auth-buttons">
          <button onclick="location.href='cadastro.php'">Registrar</button>
          <button onclick="location.href='login.php'">Iniciar Sessão</button>
        </div>
      <?php endif; ?>
      <div class="icons">
        <a href="c+.php">
          <img src="img/C+.png" alt="">
        </a>
        <a href="carrinho.php">
          <img src="img/carrinho.png" alt="">
        </a>
        
      </div>
    </div>
  </header>

  <main>
    <div class="promo">PROMOÇÃO SUPER FODA</div>

    <div class="produtos">
      <h2>Produtos</h2>
      <div class="items">
        <?php
        // Buscar produtos do banco
        $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
        if ($busca !== '') {
          $busca_sql = $conexao->real_escape_string($busca);
          $sql_prod = "SELECT * FROM Produto WHERE nome LIKE '%$busca_sql%' OR descricao LIKE '%$busca_sql%'";
        } else {
          $sql_prod = "SELECT * FROM Produto";
        }
        $res_prod = $conexao->query($sql_prod);
        if ($res_prod && $res_prod->num_rows > 0):
          while ($prod = $res_prod->fetch_assoc()):
            // Decodifica imagens (JSON ou lista)
            $imagens = [];
            if (!empty($prod['imagens'])) {
              $imagens = json_decode($prod['imagens'], true);
              if (!is_array($imagens)) {
                $imagens = explode(',', $prod['imagens']);
              }
            }
            $img = !empty($imagens[0]) ? $imagens[0] : 'img/sem-imagem.png';
        ?>
        <div class="item">
          <div class="item-content">
            <img src="<?= htmlspecialchars($img) ?>" alt="Produto" class="item-img">
            <div class="item-nome"><?= htmlspecialchars($prod['nome']) ?></div>
            <div class="item-preco">R$ <?= number_format($prod['preco'],2,',','.') ?></div>
            <div class="item-desc"><?= htmlspecialchars(mb_strimwidth($prod['descricao'],0,60,'...')) ?></div>
            <div class="item-btn-area">
              <a href="aba_produto.php?id=<?= $prod['id_produto'] ?>">
                <button class="item-btn-ver">Ver</button>
              </a>
            </div>
          </div>
        </div>
        <?php endwhile; else: ?>
        <div class="item">Nenhum produto cadastrado.</div>
        <?php endif; ?>
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