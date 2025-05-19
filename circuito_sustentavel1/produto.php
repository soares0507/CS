<?php
session_start();
include 'conexao.php';

$id_produto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_produto <= 0) {
    header('Location: loja.php');
    exit;
}

$sql = "SELECT p.*, v.nome as nome_vendedor FROM Produto p JOIN Vendedor v ON p.id_vendedor = v.id_vendedor WHERE p.id_produto = '$id_produto'";
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
$img = !empty($imagens[0]) ? $imagens[0] : 'img/sem-imagem.png';

// Lógica para perguntas (exibição)
$perguntas = [];
$sql_perg = "SELECT pe.texto, c.nome, pe.data FROM Pergunta pe JOIN Cliente c ON pe.id_cliente = c.id_cliente WHERE pe.id_produto = '$id_produto' ORDER BY pe.data DESC";
$res_perg = $conexao->query($sql_perg);
if ($res_perg && $res_perg->num_rows > 0) {
    while ($row = $res_perg->fetch_assoc()) {
        $perguntas[] = $row;
    }
}

// Lógica para enviar pergunta
$msg_pergunta = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pergunta']) && isset($_SESSION['usuario_id'])) {
    $texto = trim($_POST['pergunta']);
    $id_cliente = $_SESSION['usuario_id'];
    if ($texto !== '') {
        $texto_sql = $conexao->real_escape_string($texto);
        $conexao->query("INSERT INTO Pergunta (id_cliente, id_produto, texto) VALUES ('$id_cliente', '$id_produto', '$texto_sql')");
        $msg_pergunta = "Pergunta enviada!";
        header("Location: produto.php?id=$id_produto");
        exit;
    }
}

// Lógica para adicionar ao carrinho
$msg_carrinho = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_carrinho']) && isset($_SESSION['usuario_id'])) {
    $id_cliente = $_SESSION['usuario_id'];
    $qtd = max(1, intval($_POST['quantidade'] ?? 1));
    // Busca ou cria carrinho
    $res_carrinho = $conexao->query("SELECT id_carrinho FROM Carrinho WHERE id_cliente = '$id_cliente' LIMIT 1");
    if ($res_carrinho && $res_carrinho->num_rows > 0) {
        $row = $res_carrinho->fetch_assoc();
        $id_carrinho = $row['id_carrinho'];
    } else {
        $conexao->query("INSERT INTO Carrinho (id_cliente) VALUES ('$id_cliente')");
        $id_carrinho = $conexao->insert_id;
    }
    // Adiciona ou atualiza item
    $res_item = $conexao->query("SELECT quantidade FROM Item_Carrinho WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto'");
    if ($res_item && $res_item->num_rows > 0) {
        $conexao->query("UPDATE Item_Carrinho SET quantidade = quantidade + $qtd WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto'");
    } else {
        $conexao->query("INSERT INTO Item_Carrinho (id_carrinho, id_produto, quantidade) VALUES ('$id_carrinho', '$id_produto', '$qtd')");
    }
    $msg_carrinho = "Produto adicionado ao carrinho!";
}

// Lógica para comprar (redireciona para carrinho)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar']) && isset($_SESSION['usuario_id'])) {
    $id_cliente = $_SESSION['usuario_id'];
    $qtd = max(1, intval($_POST['quantidade'] ?? 1));
    // Mesmo processo do carrinho
    $res_carrinho = $conexao->query("SELECT id_carrinho FROM Carrinho WHERE id_cliente = '$id_cliente' LIMIT 1");
    if ($res_carrinho && $res_carrinho->num_rows > 0) {
        $row = $res_carrinho->fetch_assoc();
        $id_carrinho = $row['id_carrinho'];
    } else {
        $conexao->query("INSERT INTO Carrinho (id_cliente) VALUES ('$id_cliente')");
        $id_carrinho = $conexao->insert_id;
    }
    $res_item = $conexao->query("SELECT quantidade FROM Item_Carrinho WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto'");
    if ($res_item && $res_item->num_rows > 0) {
        $conexao->query("UPDATE Item_Carrinho SET quantidade = quantidade + $qtd WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto'");
    } else {
        $conexao->query("INSERT INTO Item_Carrinho (id_carrinho, id_produto, quantidade) VALUES ('$id_carrinho', '$id_produto', '$qtd')");
    }
    header("Location: carrinho.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($produto['nome']) ?> - Circuito Sustentável</title>
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
    .logo-container {
      text-align: center;
      margin-top: 20px;
    }
    .logo-container img {
      height: 40px;
    }
    .produto-container {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 16px rgba(40,160,96,0.08);
      padding: 2.5rem 2rem 2rem 2rem;
      max-width: 900px;
      width: 100%;
      margin: 2rem auto;
      display: flex;
      gap: 2rem;
      align-items: flex-start;
    }
    .produto-imgs {
      flex: 1;
      min-width: 220px;
      text-align: center;
    }
    .produto-imgs img {
      width: 220px;
      height: 220px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 1rem;
      background: #f3f2e7;
    }
    .produto-info {
      flex: 2;
      min-width: 250px;
    }
    .produto-nome {
      font-size: 2rem;
      font-weight: bold;
      color: #1f804e;
      margin-bottom: 0.5rem;
    }
    .produto-preco {
      font-size: 1.5rem;
      color: #28a060;
      margin-bottom: 1rem;
    }
    .produto-desc {
      font-size: 1.1rem;
      margin-bottom: 1.2rem;
      color: #222;
    }
    .produto-vendedor {
      font-size: 1rem;
      color: #555;
      margin-bottom: 1.2rem;
    }
    .produto-actions {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.2rem;
      align-items: center;
    }
    .produto-actions input[type="number"] {
      width: 60px;
      padding: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
      margin-right: 10px;
    }
    .produto-actions button {
      background: #28a060;
      color: #fff;
      border: none;
      padding: 10px 22px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      transition: background 0.2s;
    }
    .produto-actions button:hover {
      background: #1b5e20;
    }
    .msg {
      color: #28a060;
      font-weight: bold;
      margin-bottom: 1rem;
    }
    .perguntas-container {
      background: #f3f2e7;
      border-radius: 12px;
      padding: 1.5rem;
      margin: 2rem auto 0 auto;
      max-width: 900px;
      width: 100%;
    }
    .perguntas-container h3 {
      color: #1f804e;
      margin-bottom: 1rem;
    }
    .pergunta-form textarea {
      width: 100%;
      min-height: 60px;
      border-radius: 8px;
      border: 1px solid #ccc;
      padding: 8px;
      font-size: 1rem;
      margin-bottom: 0.7rem;
      resize: vertical;
    }
    .pergunta-form button {
      background: #28a060;
      color: #fff;
      border: none;
      padding: 8px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 1rem;
      transition: background 0.2s;
    }
    .pergunta-form button:hover {
      background: #1b5e20;
    }
    .pergunta-list {
      margin-top: 1.5rem;
    }
    .pergunta-item {
      background: #fff;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 1px 6px rgba(40,160,96,0.07);
    }
    .pergunta-nome {
      font-weight: bold;
      color: #28a060;
      margin-bottom: 0.2rem;
    }
    .pergunta-texto {
      color: #222;
      margin-bottom: 0.2rem;
    }
    .pergunta-data {
      font-size: 0.95rem;
      color: #888;
    }
    @media (max-width: 900px) {
      .produto-container, .perguntas-container { flex-direction: column; max-width: 98vw; }
      .produto-container { gap: 1rem; }
      .produto-imgs img { width: 100%; max-width: 320px; }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo-container">
      <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
    </div>
  </header>
  <main>
    <div class="produto-container">
      <div class="produto-imgs">
        <img src="<?= htmlspecialchars($img) ?>" alt="Produto">
        <?php if (count($imagens) > 1): ?>
          <div>
            <?php foreach ($imagens as $i): ?>
              <img src="<?= htmlspecialchars($i) ?>" alt="Produto" style="width:50px;height:50px;margin:2px;object-fit:cover;cursor:pointer;" onclick="document.querySelector('.produto-imgs img').src=this.src;">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="produto-info">
        <div class="produto-nome"><?= htmlspecialchars($produto['nome']) ?></div>
        <div class="produto-preco">R$ <?= number_format($produto['preco'],2,',','.') ?></div>
        <div class="produto-desc"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></div>
        <div class="produto-vendedor">Vendedor: <?= htmlspecialchars($produto['nome_vendedor']) ?></div>
        <?php if (!empty($msg_carrinho)): ?>
          <div class="msg"><?= $msg_carrinho ?></div>
        <?php endif; ?>
        <form method="post" class="produto-actions" style="margin-bottom:0;">
          <input type="number" name="quantidade" min="1" max="<?= $produto['estoque'] ?>" value="1" required>
          <button type="submit" name="add_carrinho">Adicionar ao Carrinho</button>
          <button type="submit" name="comprar">Comprar Agora</button>
        </form>
      </div>
    </div>
    <div class="perguntas-container">
      <h3>Perguntas sobre o produto</h3>
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <form method="post" class="pergunta-form">
          <textarea name="pergunta" placeholder="Faça uma pergunta sobre o produto..." required></textarea>
          <button type="submit">Enviar Pergunta</button>
        </form>
        <?php if ($msg_pergunta): ?>
          <div class="msg"><?= $msg_pergunta ?></div>
        <?php endif; ?>
      <?php else: ?>
        <p style="color:#1f804e;">Faça login para perguntar sobre o produto.</p>
      <?php endif; ?>
      <div class="pergunta-list">
        <?php if (count($perguntas) > 0): foreach ($perguntas as $p): ?>
          <div class="pergunta-item">
            <div class="pergunta-nome"><?= htmlspecialchars($p['nome']) ?></div>
            <div class="pergunta-texto"><?= htmlspecialchars($p['texto']) ?></div>
            <div class="pergunta-data"><?= date('d/m/Y H:i', strtotime($p['data'])) ?></div>
          </div>
        <?php endforeach; else: ?>
          <div style="color:#888;">Nenhuma pergunta ainda.</div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>
