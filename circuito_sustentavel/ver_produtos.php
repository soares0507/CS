<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'adicionar_estoque.php';
    header('Location: login.php');
    exit;
}

$id_vendedor = $_SESSION['vendedor_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_produto'])) {
    $id_produto_excluir = intval($_POST['id_produto']);

    // Remover itens do carrinho que usam este produto
    $stmt_del_carrinho = $conexao->prepare("DELETE FROM item_carrinho WHERE id_produto = ?");
    $stmt_del_carrinho->bind_param("i", $id_produto_excluir);
    $stmt_del_carrinho->execute();
    $stmt_del_carrinho->close();

    // Remover itens de pedidos que usam este produto
    $stmt_del_item_pedido = $conexao->prepare("DELETE FROM item_pedido WHERE id_produto = ?");
    $stmt_del_item_pedido->bind_param("i", $id_produto_excluir);
    $stmt_del_item_pedido->execute();
    $stmt_del_item_pedido->close();

    // Remover respostas relacionadas às perguntas deste produto
    $stmt_perguntas = $conexao->prepare("SELECT id_pergunta FROM pergunta WHERE id_produto = ?");
    $stmt_perguntas->bind_param("i", $id_produto_excluir);
    $stmt_perguntas->execute();
    $result_perguntas = $stmt_perguntas->get_result();
    $ids_perguntas = [];
    while ($row = $result_perguntas->fetch_assoc()) {
        $ids_perguntas[] = $row['id_pergunta'];
    }
    $stmt_perguntas->close();

    if (!empty($ids_perguntas)) {
        // Monta uma string de interrogações para o IN (?, ?, ...)
        $placeholders = implode(',', array_fill(0, count($ids_perguntas), '?'));
        $types = str_repeat('i', count($ids_perguntas));
        $stmt_del_respostas = $conexao->prepare("DELETE FROM resposta WHERE id_pergunta IN ($placeholders)");
        $stmt_del_respostas->bind_param($types, ...$ids_perguntas);
        $stmt_del_respostas->execute();
        $stmt_del_respostas->close();
    }

    // Remover perguntas relacionadas ao produto
    $stmt_del_pergunta = $conexao->prepare("DELETE FROM pergunta WHERE id_produto = ?");
    $stmt_del_pergunta->bind_param("i", $id_produto_excluir);
    $stmt_del_pergunta->execute();
    $stmt_del_pergunta->close();
    
    // Agora sim, pode deletar o produto
    $stmt_del = $conexao->prepare("DELETE FROM Produto WHERE id_produto = ? AND id_vendedor = ?");
    $stmt_del->bind_param("ii", $id_produto_excluir, $id_vendedor);
    $stmt_del->execute();
    $stmt_del->close();
}


$sql = "SELECT * FROM Produto WHERE id_vendedor = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$result = $stmt->get_result();
$produtos = [];
while ($row = $result->fetch_assoc()) {
   
    $imagens = [];
    if (!empty($row['imagens'])) {
        $imagens = json_decode($row['imagens'], true);
        if (!is_array($imagens)) {
            $imagens = explode(',', $row['imagens']);
        }
    }
    $row['imagens'] = $imagens;
    $produtos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Atualizar Produto</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      color: #222;
      margin: 0;
      padding: 0;
    }
    header {
      width: 100%;
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      position: fixed;
      top: 0;
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
    main {
      margin-top: 100px;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 80vh;
    }
    .container {
      background: #e9f7ef;
      border-radius: 18px;
      box-shadow: 0 4px 16px rgba(40,160,96,0.08);
      padding: 2.5rem 2rem 2rem 2rem;
      max-width: 900px;
      width: 100%;
      margin: 2rem auto;
      text-align: center;
    }
    h1 {
      color: #1f804e;
      font-size: 2.2rem;
      margin-bottom: 2rem;
    }
    .produtos-lista {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }
    .produto-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 18px rgba(40,160,96,0.13);
      padding: 2.2rem 1.5rem 1.5rem 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: box-shadow 0.2s, transform 0.2s;
      min-height: 420px;
      max-width: 420px;
      margin: 0 auto;
    }
    .produto-card:hover {
      box-shadow: 0 10px 32px rgba(40,160,96,0.18);
      transform: translateY(-6px) scale(1.03);
    }
    .produto-img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 14px;
      margin-bottom: 1.2rem;
      background: #e9f7ef;
      border: 1px solid #d4d4d4;
      box-shadow: 0 2px 8px rgba(40,160,96,0.08);
      transition: box-shadow 0.2s;
    }
    .produto-nome {
      font-size: 1.45rem;
      font-weight: bold;
      color: #1f804e;
      margin-bottom: 0.7rem;
      text-align: center;
    }
    .produto-preco {
      color: #145c36;
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      font-weight: bold;
    }
    .produto-estoque {
      color: #555;
      font-size: 1.08rem;
      margin-bottom: 0.5rem;
    }
    .produto-desc {
      color: #444;
      font-size: 1.08rem;
      margin-bottom: 1.2rem;
      min-height: 48px;
      text-align: center;
    }
    .botao-editar {
      padding: 0.9rem 2rem;
      font-size: 1.08rem;
      border-radius: 8px;
      border: none;
      background: #28a060;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-top: 0.7rem;
      margin-bottom: 0.2rem;
    }
    .botao-editar:hover {
      background: #1f804e;
      transform: scale(1.04);
    }
    @media (max-width: 900px) {
      .produtos-lista {
        grid-template-columns: 1fr;
      }
      .produto-card {
        max-width: 98vw;
        min-height: 350px;
        padding: 1.2rem 0.5rem;
      }
      .produto-img {
        width: 140px;
        height: 140px;
      }
    }
    @media (max-width: 600px) {
      .produto-card {
        padding: 0.7rem 0.2rem;
      }
      .produto-img {
        width: 100px;
        height: 100px;
      }
      .produto-nome, .produto-preco, .produto-desc {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
      
    </div>
    <div style="margin-left:78%;">
      <button onclick="window.location.href='produtos.php'" style="background: none; border: none; font-size: 2rem; color: #1f804e; cursor: pointer; font-weight: bold;">&#10005;</button>
    </div>
  </header>
  <main>
    <div class="container">
      <h1>Atualizar Produto</h1>
      <div class="produtos-lista">
        <?php foreach ($produtos as $produto): ?>
          <div class="produto-card">
            <?php if (!empty($produto['imagens'][0])): ?>
              <img src="<?= htmlspecialchars($produto['imagens'][0]) ?>" class="produto-img" alt="Imagem do produto">
            <?php else: ?>
              <img src="img/sem-imagem.png" class="produto-img" alt="Sem imagem">
            <?php endif; ?>
            <div class="produto-nome"><?= htmlspecialchars($produto['nome']) ?></div>
            <div class="produto-preco">Preço: R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
            <div class="produto-estoque">Estoque: <?= (int)$produto['estoque'] ?></div>
            <div class="produto-desc"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></div>
            <button class="botao-editar" onclick="location.href='editar.php?id=<?= $produto['id_produto'] ?>'">Editar</button>
            <form method="post" style="margin-top:10px;">
              <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">
              
            
            </form>
          </div>
        <?php endforeach; ?>
      </div>
      
    </div>
  </main>
</body>
</html>
