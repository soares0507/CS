<?php
session_start();
include 'conexao.php';

$id_produto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_produto <= 0) {
    header('Location: loja.php');
    exit;
}

// Adiciona ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_carrinho'])) {
    if (isset($_SESSION['usuario_id'])) {
        $id_cliente = $_SESSION['usuario_id'];
        $id_vendedor = "NULL";
    } elseif (isset($_SESSION['vendedor_id'])) {
        $id_vendedor = $_SESSION['vendedor_id'];
        $id_cliente = "NULL";
    } else {
        header("Location: login.php");
        exit;
    }

    // Verifica se já existe carrinho
    $res = $conexao->query("SELECT id_carrinho FROM Carrinho WHERE id_cliente = $id_cliente AND id_vendedor = $id_vendedor");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id_carrinho = $row['id_carrinho'];
    } else {
        $conexao->query("INSERT INTO Carrinho (id_cliente, id_vendedor) VALUES ($id_cliente, $id_vendedor)");
        $id_carrinho = $conexao->insert_id;
    }

    // Verifica se já existe o item no carrinho
    $res_item = $conexao->query("SELECT * FROM Item_Carrinho WHERE id_carrinho = $id_carrinho AND id_produto = $id_produto");
    if ($res_item && $res_item->num_rows > 0) {
        $conexao->query("UPDATE Item_Carrinho SET quantidade = quantidade + 1 WHERE id_carrinho = $id_carrinho AND id_produto = $id_produto");
    } else {
        $conexao->query("INSERT INTO Item_Carrinho (id_carrinho, id_produto, quantidade) VALUES ($id_carrinho, $id_produto, 1)");
    }

    header("Location: carrinho.php");
    exit;
}

// [Demais lógicas da página permanecem inalteradas...]

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pergunta']) && isset($_SESSION['usuario_id'])) {
    $texto = trim($_POST['pergunta']);
    $id_cliente = $_SESSION['usuario_id'];
    if ($texto !== '') {
        $texto_sql = $conexao->real_escape_string($texto);
        $conexao->query("INSERT INTO Pergunta (id_cliente, id_produto, texto) VALUES ('$id_cliente', '$id_produto', '$texto_sql')");
        // Não faz header nem exit
    }
}

// Processa envio de resposta do vendedor
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['resposta']) &&
    isset($_POST['id_pergunta']) &&
    isset($_SESSION['vendedor_id'])
) {
    // Verifica se o vendedor é o dono do produto
    $id_vendedor = $_SESSION['vendedor_id'];
    $sql_verifica = "SELECT id_vendedor FROM Produto WHERE id_produto = '$id_produto'";
    $res_verifica = $conexao->query($sql_verifica);
    if ($res_verifica && $res_verifica->num_rows > 0) {
        $row = $res_verifica->fetch_assoc();
        if ($row['id_vendedor'] == $id_vendedor) {
            $texto_resp = trim($_POST['resposta']);
            $id_pergunta = intval($_POST['id_pergunta']);
            if ($texto_resp !== '') {
                $texto_resp_sql = $conexao->real_escape_string($texto_resp);
                $conexao->query("INSERT INTO Resposta (id_pergunta, id_vendedor, texto) VALUES ('$id_pergunta', '$id_vendedor', '$texto_resp_sql')");
                header("Location: aba_produto.php?id=$id_produto");
                exit;
            }
        }
    }
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

// Busca perguntas e respostas
$sql_perg = "SELECT pe.id_pergunta, pe.texto, c.nome, pe.data FROM Pergunta pe JOIN Cliente c ON pe.id_cliente = c.id_cliente WHERE pe.id_produto = '$id_produto' ORDER BY pe.data DESC";
$res_perg = $conexao->query($sql_perg);
$perguntas = $res_perg ? $res_perg->fetch_all(MYSQLI_ASSOC) : [];

// Busca respostas para as perguntas (apenas a mais recente para cada pergunta)
$respostas = [];
if (!empty($perguntas)) {
    $ids_perguntas = array_column($perguntas, 'id_pergunta');
    $ids_in = implode(',', array_map('intval', $ids_perguntas));
    if ($ids_in) {
        $sql_resp = "
            SELECT r1.id_pergunta, r1.texto, r1.data, v.nome
            FROM Resposta r1
            LEFT JOIN Vendedor v ON r1.id_vendedor = v.id_vendedor
            INNER JOIN (
                SELECT id_pergunta, MAX(data) as max_data
                FROM Resposta
                WHERE id_pergunta IN ($ids_in)
                GROUP BY id_pergunta
            ) r2 ON r1.id_pergunta = r2.id_pergunta AND r1.data = r2.max_data
        ";
        $res_resp = $conexao->query($sql_resp);
        if ($res_resp && $res_resp->num_rows > 0) {
            while ($row = $res_resp->fetch_assoc()) {
                $respostas[$row['id_pergunta']] = $row;
            }
        }
    }
}

$pergunta_para_responder = null;
if (
    isset($_SESSION['vendedor_id']) &&
    isset($_GET['responder']) &&
    is_numeric($_GET['responder'])
) {
    $pergunta_para_responder = intval($_GET['responder']);
}
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

    .mt-4 h5{
      font-weight: bold;
    }
    .btn-responder {
      display: inline-block;
      background: #28a060;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 5px 18px;
      font-size: 1rem;
      font-weight: bold;
      margin-left: 10px;
      cursor: pointer;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px rgba(40,160,96,0.07);
      vertical-align: middle;
    }
    .btn-responder:hover, .btn-responder.active {
      background: #1f804e;
      color: #fff;
      box-shadow: 0 4px 16px rgba(40,160,96,0.13);
    }
    .form-resposta-animada {
      animation: fadeInResposta 0.4s;
      margin-top: 10px;
    }
    @keyframes fadeInResposta {
      from { opacity: 0; transform: translateY(-10px);}
      to { opacity: 1; transform: translateY(0);}
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
          <?php
          // Verifica se o usuário está logado e se o carrinho está vazio
          $mostrar_carrinho_vazio = false;
          if (isset($_SESSION['usuario_id'])) {
              $id_cliente = $_SESSION['usuario_id'];
              $sql_carrinho = "SELECT id_carrinho FROM Carrinho WHERE id_cliente = '$id_cliente'";
              $res_carrinho = $conexao->query($sql_carrinho);
              $tem_item = false;
              if ($res_carrinho && $res_carrinho->num_rows > 0) {
                  while ($row_carrinho = $res_carrinho->fetch_assoc()) {
                      $id_carrinho = $row_carrinho['id_carrinho'];
                      $sql_itens = "SELECT 1 FROM Item_Carrinho WHERE id_carrinho = '$id_carrinho' LIMIT 1";
                      $res_itens = $conexao->query($sql_itens);
                      if ($res_itens && $res_itens->num_rows > 0) {
                          $tem_item = true;
                          break;
                      }
                  }
              }
              if (!$tem_item) $mostrar_carrinho_vazio = true;
          }
          ?>
          <img src="<?= ($mostrar_carrinho_vazio ? 'img/carrinho_sem.png' : 'img/carrinho.png') ?>" alt="">
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
        <form method="post" id="form-carrinho">
          <input type="number" name="quantidade" min="1" max="<?= $produto['estoque'] ?>" value="1" required <?= $produto['estoque'] == 0 ? 'disabled' : '' ?>>
          <button type="submit" class="btn buy-btn" name="add_carrinho" <?= $produto['estoque'] == 0 ? 'disabled' : '' ?>>Adicionar ao carrinho</button>
          <button type="submit" class="btn buy-btn" name="comprar" style="background:#28a060;color:#fff;" <?= $produto['estoque'] == 0 ? 'disabled' : '' ?>>Comprar Agora</button>
        </form>
        <div id="msg-carrinho" style="display:none;margin-top:10px;" class="alert alert-success"></div>
      </div>
      <div class="mt-4">
        <h5>Detalhes do produto:</h5>
        <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
      </div>
    </div>
  </div>

  <div class="container mt-5">
    <h4>Perguntas e Respostas</h4>
    <hr>
    <?php if (!empty($perguntas)): ?>
      <?php foreach ($perguntas as $p): ?>
        <div class="mb-3" id="pergunta-<?= $p['id_pergunta'] ?>">
          <strong><?php echo htmlspecialchars($p['nome']); ?>:</strong> <?php echo htmlspecialchars($p['texto']); ?><br>
          <small class="text-muted"><?php echo date('d/m/Y', strtotime($p['data'])); ?></small>
          <?php if (!empty($respostas[$p['id_pergunta']])): ?>
            <?php $resp = $respostas[$p['id_pergunta']]; ?>
              <div style="margin-left:20px;margin-top:5px;">
                <span style="color:#1f804e;font-weight:bold;">Resposta do vendedor:</span>
                <?= htmlspecialchars($resp['texto']) ?>
                <br>
                <small class="text-muted">
                    <?= $resp['nome'] ? htmlspecialchars($resp['nome']) . ' - ' : '' ?>
                    <?= date('d/m/Y', strtotime($resp['data'])) ?>
                </small>
              </div>
          <?php endif; ?>

          <?php
          // Exibe botão "Responder" apenas para o vendedor dono do produto E se ainda não respondeu
          if (
              isset($_SESSION['vendedor_id']) &&
              isset($produto['id_vendedor']) &&
              $_SESSION['vendedor_id'] == $produto['id_vendedor'] &&
              empty($respostas[$p['id_pergunta']])
          ): ?>
            <button class="btn-responder<?= ($pergunta_para_responder == $p['id_pergunta']) ? ' active' : '' ?>"
              onclick="mostrarFormResposta(<?= $p['id_pergunta'] ?>, event)">Responder</button>
          <?php endif; ?>

          <?php if (
              isset($_SESSION['vendedor_id']) &&
              isset($produto['id_vendedor']) &&
              $_SESSION['vendedor_id'] == $produto['id_vendedor'] &&
              empty($respostas[$p['id_pergunta']])
          ): ?>
            <div id="form-resposta-<?= $p['id_pergunta'] ?>" style="display:none;">
              <form method="post" class="form-resposta-animada form-resposta-vendedor">
                <input type="hidden" name="id_pergunta" value="<?= $p['id_pergunta'] ?>">
                <textarea name="resposta" class="form-control" placeholder="Responder..." required></textarea>
                <button type="submit" class="btn btn-success mt-2">Responder</button>
              </form>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">Nenhuma pergunta ainda.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['usuario_id'])): ?>
      <form method="post" class="mt-4" id="form-pergunta">
        <textarea name="pergunta" class="form-control" placeholder="Faça uma pergunta sobre o produto..." required></textarea>
        <button type="submit" class="btn btn-success mt-2">Enviar Pergunta</button>
      </form>
      <script>
        // Envio AJAX para perguntas
        document.getElementById('form-pergunta').addEventListener('submit', function(e) {
          e.preventDefault();
          var form = this;
          var formData = new FormData(form);
          var xhr = new XMLHttpRequest();
          xhr.open('POST', window.location.href, true);
          xhr.onload = function() {
            if (xhr.status === 200) {
              location.reload();
            }
          };
          xhr.send(formData);
        });
      </script>
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
  <script>
    function mostrarFormResposta(id, evt) {
      evt.preventDefault();
      // Esconde todos os forms de resposta
      document.querySelectorAll('[id^="form-resposta-"]').forEach(function(div) {
        div.style.display = 'none';
      });
      // Remove classe active de todos os botões
      document.querySelectorAll('.btn-responder').forEach(function(btn) {
        btn.classList.remove('active');
      });
      // Mostra o form da pergunta selecionada
      var formDiv = document.getElementById('form-resposta-' + id);
      if (formDiv) {
        formDiv.style.display = 'block';
        // Adiciona classe active ao botão clicado
        if (evt.target) evt.target.classList.add('active');
        // Scroll até a pergunta
        var perguntaDiv = document.getElementById('pergunta-' + id);
        if (perguntaDiv) perguntaDiv.scrollIntoView({behavior: 'smooth', block: 'center'});
      }
      // Atualiza a URL sem recarregar a página
      if (history.pushState) {
        var url = new URL(window.location);
        url.searchParams.set('id', '<?= $id_produto ?>');
        url.searchParams.set('responder', id);
        history.replaceState(null, '', url);
      }
    }
    // Se houver uma pergunta selecionada na URL, mostra o form ao carregar
    window.onload = function() {
      var responder = '<?= $pergunta_para_responder ?>';
      if (responder) {
        mostrarFormResposta(responder, {preventDefault:function(){}, target:document.querySelector('.btn-responder.active')});
      }
    };

    // Envio AJAX para respostas do vendedor
    document.querySelectorAll('.form-resposta-vendedor').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, true);
        xhr.onload = function() {
          if (xhr.status === 200) {
            location.reload();
          }
        };
        xhr.send(formData);
      });
    });

    // Envio AJAX para adicionar ao carrinho
    document.getElementById('form-carrinho').addEventListener('submit', function(e) {
      var btn = document.activeElement;
      if (btn && btn.name === 'add_carrinho') {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        formData.append('id_produto', '<?= $id_produto ?>');
        formData.append('ajax_add_carrinho', '1');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, true);
        xhr.onload = function() {
          if (xhr.status === 200) {
            // Mostra mensagem de sucesso
            var msg = document.getElementById('msg-carrinho');
            msg.innerText = 'Produto adicionado ao carrinho!';
            msg.style.display = 'block';
            setTimeout(function() {
              msg.style.display = 'none';
              location.reload();
            }, 1200);
          }
        };
        xhr.send(formData);
      }
      // Se for "comprar agora", deixa o submit normal
    });
  </script>
</body>
</html>

<?php
// Processa envio AJAX para adicionar ao carrinho
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['ajax_add_carrinho'])
) {
    // Permite tanto cliente quanto vendedor
    if (isset($_SESSION['usuario_id'])) {
        $id_cliente = $_SESSION['usuario_id'];
    } elseif (isset($_SESSION['vendedor_id'])) {
        $id_cliente = $_SESSION['vendedor_id'];
    } else {
        exit; // Não logado
    }

    $id_produto_post = isset($_POST['id_produto']) ? intval($_POST['id_produto']) : 0;
    $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 1;

    // Busca id_vendedor do produto
    $sql_prod = "SELECT id_vendedor FROM Produto WHERE id_produto = '$id_produto_post'";
    $res_prod = $conexao->query($sql_prod);
    if ($res_prod && $res_prod->num_rows > 0) {
        $row_prod = $res_prod->fetch_assoc();
        $id_vendedor = $row_prod['id_vendedor'];

        // Busca ou cria carrinho para o cliente/vendedor e vendedor do produto
        $sql_carrinho = "SELECT id_carrinho FROM Carrinho WHERE id_cliente = '$id_cliente' AND id_vendedor = '$id_vendedor' LIMIT 1";
        $res_carrinho = $conexao->query($sql_carrinho);
        if ($res_carrinho && $res_carrinho->num_rows > 0) {
            $row_carrinho = $res_carrinho->fetch_assoc();
            $id_carrinho = $row_carrinho['id_carrinho'];
        } else {
            $conexao->query("INSERT INTO Carrinho (id_cliente, id_vendedor) VALUES ('$id_cliente', '$id_vendedor')");
            $id_carrinho = $conexao->insert_id;
        }

        // Adiciona ou atualiza item no carrinho
        $sql_item = "SELECT quantidade FROM Item_Carrinho WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto_post'";
        $res_item = $conexao->query($sql_item);
        if ($res_item && $res_item->num_rows > 0) {
            $row_item = $res_item->fetch_assoc();
            $nova_qtd = $row_item['quantidade'] + $quantidade;
            $conexao->query("UPDATE Item_Carrinho SET quantidade = '$nova_qtd' WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto_post'");
        } else {
            $conexao->query("INSERT INTO Item_Carrinho (id_carrinho, id_produto, quantidade) VALUES ('$id_carrinho', '$id_produto_post', '$quantidade')");
        }
    }
    exit; // Importante para AJAX não retornar HTML
}
