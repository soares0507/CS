<?php
session_start();
include 'conexao.php';

$id_produto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_produto <= 0) {
    header('Location: loja.php');
    exit;
}

$sql = "SELECT p.*, v.nome as nome_vendedor, v.email as email_vendedor FROM Produto p JOIN Vendedor v ON p.id_vendedor = v.id_vendedor WHERE p.id_produto = '$id_produto'";
$res = $conexao->query($sql);
if (!$res || $res->num_rows == 0) {
    header('Location: loja.php');
    exit;
}
$produto = $res->fetch_assoc();
$imagens = [];
if (!empty($produto['imagens'])) {
    $imagens = json_decode($produto['imagens'], true);
    if (!is_array($imagens)) {
        $imagens = explode(',', $produto['imagens']);
    }
}
$img_principal = !empty($imagens[0]) ? $imagens[0] : 'img/sem-imagem.png';

$sql_perg = "SELECT pe.texto, c.nome, pe.data FROM Pergunta pe JOIN Cliente c ON pe.id_cliente = c.id_cliente WHERE pe.id_produto = '$id_produto' ORDER BY pe.data DESC";
$res_perg = $conexao->query($sql);
$perguntas = $res_perg ? $res_perg->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($produto['nome']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fff;
      font-family: Arial, sans-serif;
      background-color: #d5d3c7;
    }
    .container-amazon {
      max-width: 1200px;
      margin: 30px auto;
    }
    .left-column {
      width: 50%;
    }
    .right-column {
      width: 45%;
    }
    .thumbnail-img {
      width: 60px;
      margin-bottom: 10px;
      cursor: pointer;
      border: 1px solid #ddd;
      padding: 2px;
    }
    .main-img {
      width: 100%;
      max-height: 400px;
      object-fit: contain;
      border: 1px solid #ddd;
    }
    .product-title {
      font-size: 1.5rem;
      font-weight: 500;
    }
    .price {
      color: #B12704;
      font-size: 1.8rem;
      font-weight: bold;
      margin-top: 10px;
    }
    .buy-box {
      border: 1px solid #ddd;
      padding: 16px;
      border-radius: 8px;
      background-color: #f6f6f6;
    }
    .buy-btn {
      background-color: #FFD814;
      border-color: #007600;
      color: #111;
      width: 100%;
      font-weight: bold;
      border-radius: 8px;
      padding: 10px;
      margin-top: 10px;
    }
    .buy-btn:hover {
      background-color: #F7CA00;
    }
    .stock {
      color: #007600;
      font-weight: bold;
      margin-top: 10px;
    }
    .rating {
      color: #FFA41C;
      font-size: 1rem;
    }
    /* Header igual loja.php */
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
      flex-direction: column;
      align-items: flex-end;
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
      height: 40px;
      width: auto;
      margin-left: -119px;
    }
    .user-info img {
      height: 50px;
      width: auto;
      margin-left: -139px;
      margin-top: -13px;
    }
    .auth-buttons button {
      padding: 8px 15px;
      background-color: #28a060;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .auth-buttons button:last-child {
      border: 2px solid #28a060;
      background-color: transparent;
      color: #2e7d32;
    }
    .auth-buttons button:last-child:hover {
      background-color: #2e7d32;
      color: white;
      box-shadow: 0 0 10px rgba(46, 125, 50, 0.5);
    }
    .auth-buttons button:hover {
      background-color: #1b5e20;
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
  </style>
</head>
<body>
  <!-- Header igual loja.php -->
  <div id="overlay" class="overlay" style="display:none;" onclick="toggleCategoryList()"></div>
  <header>
    <div class="menu-btn" onclick="toggleCategoryList()">
      <span>☰</span>
      CATEGORIAS
    </div>
    <div id="category-list" class="category-list" style="display:none;">
      <div class="category-header">
        <img src="img/logo2.png" alt="Logo"/>
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
      <a href="loja.php"><img src="img/logo2.png" alt="Circuito Sustentável Logo" /></a>
    </div>
    <div class="search-bar">
      <form method="get" action="loja.php" style="width:100%;">
        <input type="text" name="busca" placeholder="Pesquisar produtos..." value="" />
        <button type="submit" style="position:absolute;right:14%;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;">
          <img src="img/lupa.png" alt="Pesquisar" class="lupa" />
        </button>
      </form>
    </div>
    <div class="auth-section">
      <?php if (isset($_SESSION['usuario_id']) || isset($_SESSION['vendedor_id'])): ?>
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
  <script>
    function toggleCategoryList() {
      const overlay = document.getElementById('overlay');
      const categoryList = document.getElementById('category-list');
      const isVisible = categoryList.style.display === 'block';
      categoryList.style.display = isVisible ? 'none' : 'block';
      overlay.style.display = isVisible ? 'none' : 'block';
    }
  </script>
  <!-- Fim header igual loja.php -->

  <div class="container container-amazon d-flex gap-5">
    <!-- Coluna da Imagem -->
    <div class="left-column">
      <div class="d-flex gap-3">
        <div class="d-flex flex-column">
          <?php foreach ($imagens as $img): ?>
            <img src="<?php echo $img; ?>" class="thumbnail-img" onclick="document.getElementById('mainImage').src=this.src">
          <?php endforeach; ?>
        </div>
        <div class="flex-fill">
          <img id="mainImage" src="<?php echo $img_principal; ?>" class="main-img">
        </div>
      </div>
    </div>

    <!-- Coluna da Descrição e Compra -->
    <div class="right-column">
      <h1 class="product-title"><?php echo htmlspecialchars($produto['nome']); ?></h1>
      <div class="rating">★★★★★ <span class="text-muted">(123)</span></div>
      <hr>
      <div class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
      <div class="stock"><?= $produto['estoque'] > 0 ? 'Em estoque' : 'Indisponível' ?></div>
      <div class="buy-box mt-3">
        <p><strong>Vendidos por:</strong> <?php echo htmlspecialchars($produto['nome_vendedor']); ?></p>
        <form method="post">
          <input type="number" name="quantidade" min="1" max="<?= $produto['estoque'] ?>" value="1" required <?= $produto['estoque'] == 0 ? 'disabled' : '' ?>>
          <button type="submit" class="btn buy-btn" name="add_carrinho" <?= $produto['estoque'] == 0 ? 'disabled' : '' ?>>Adicionar ao carrinho</button>
          <button type="submit" class="btn buy-btn" name="comprar" style="background:#28a060;color:#fff;" <?= $produto['estoque'] == 0 ? 'disabled' : '' ?>>Comprar Agora</button>
        </form>
      </div>
      <div class="mt-4">
        <h5>Detalhes do produto</h5>
        <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
      </div>
    </div>
  </div>

  <div class="container mt-5">
    <h4>Perguntas e Respostas</h4>
    <hr>
    <?php if (!empty($perguntas)): ?>
      <?php foreach ($perguntas as $p): ?>
        <div class="mb-3">
          <strong><?php echo htmlspecialchars($p['nome']); ?>:</strong> <?php echo htmlspecialchars($p['texto']); ?><br>
          <small class="text-muted"><?php echo date('d/m/Y', strtotime($p['data'])); ?></small>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">Nenhuma pergunta ainda.</p>
    <?php endif; ?>
    <?php if (isset($_SESSION['usuario_id'])): ?>
      <form method="post" class="mt-4">
        <textarea name="pergunta" class="form-control" placeholder="Faça uma pergunta sobre o produto..." required></textarea>
        <button type="submit" class="btn btn-success mt-2">Enviar Pergunta</button>
      </form>
    <?php else: ?>
      <p style="color:#1f804e;">Faça login para perguntar sobre o produto.</p>
    <?php endif; ?>
  </div>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
