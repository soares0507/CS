<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'editar_produto.php';
    header('Location: login.php');
    exit;
}

$id_vendedor = $_SESSION['vendedor_id'];
$id_produto = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM Produto WHERE id_produto = '$id_produto' AND id_vendedor = '$id_vendedor'";
$resultado = $conexao->query($sql);
$produto = $resultado ? $resultado->fetch_assoc() : null;

if (!$produto) {
    echo "<p style='color:red;text-align:center;margin-top:2rem;'>Produto não encontrado ou acesso negado.</p>";
    exit;
}

$imagens = [];
if (!empty($produto['imagens'])) {
    $imagens = json_decode($produto['imagens'], true);
    if (!is_array($imagens)) {
        $imagens = explode(',', $produto['imagens']);
    }
}

$erro = '';
$sucesso = '';

$pasta_upload = __DIR__ . '/uploads_produtos/';
$url_upload = 'uploads_produtos/';
if (!is_dir($pasta_upload)) {
    mkdir($pasta_upload, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $estoque = $_POST['estoque'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    $imagens_atualizadas = $imagens;
    for ($i = 0; $i < 6; $i++) {
        if (isset($_FILES['imagens']['name'][$i]) && $_FILES['imagens']['name'][$i]) {
            if ($_FILES['imagens']['error'][$i] === UPLOAD_ERR_OK) {
                $caminho_temporario = $_FILES['imagens']['tmp_name'][$i];
                $extensao = strtolower(pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION));
                $permitidas = ['jpg','jpeg','png','gif','webp'];
                if (!in_array($extensao, $permitidas)) continue;
                $nome_arquivo = uniqid('produto_', true) . '.' . $extensao;
                $destino = $pasta_upload . $nome_arquivo;
                if (move_uploaded_file($caminho_temporario, $destino)) {
                    $imagens_atualizadas[$i] = $url_upload . $nome_arquivo;
                }
            }
        } elseif (isset($_POST["imagem_existente_$i"])) {
            $imagens_atualizadas[$i] = $_POST["imagem_existente_$i"];
        }
    }
    $imagens_atualizadas = array_slice($imagens_atualizadas, 0, 6);
    $imagens_json = json_encode($imagens_atualizadas);

    $sql = "UPDATE Produto SET nome='$nome', descricao='$descricao', preco='$preco', estoque='$estoque', imagens='$imagens_json' WHERE id_produto='$id_produto' AND id_vendedor='$id_vendedor'";
    if ($conexao->query($sql)) {
        $sucesso = "Produto atualizado com sucesso!";
        $produto['nome'] = $nome;
        $produto['descricao'] = $descricao;
        $produto['preco'] = $preco;
        $produto['estoque'] = $estoque;
        $imagens = $imagens_atualizadas;
    } else {
        $erro = "Erro ao atualizar produto!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Produto</title>
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
      max-width: 600px;
      width: 100%;
      margin: 2rem auto;
      text-align: center;
    }
    h1 {
      color: #1f804e;
      font-size: 2rem;
      margin-bottom: 1.5rem;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 1.1rem;
      align-items: stretch;
    }
    label {
      text-align: left;
      font-weight: bold;
      margin-bottom: 0.2rem;
    }
    input[type="text"], input[type="number"], textarea {
      padding: 0.7rem;
      border-radius: 8px;
      border: 1px solid #bbb;
      font-size: 1rem;
      background: #fff;
      margin-bottom: 0.5rem;
    }
    textarea {
      resize: vertical;
      min-height: 60px;
      max-height: 200px;
    }
    .imagens-preview {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 1rem;
      justify-content: center;
    }
    .imagens-preview img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ccc;
      background: #fff;
    }
    .imagem-label {
      display: block;
      margin-top: 0.5rem;
      font-size: 0.95rem;
      color: #145c36;
    }
    .botoes {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 1rem;
    }
    .botao-acao {
      padding: 0.8rem 1.5rem;
      font-size: 1rem;
      border-radius: 8px;
      border: none;
      background: #28a060;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .botao-acao:hover {
      background: #1f804e;
      transform: scale(1.04);
    }
    .mensagem-erro {
      color: #d43131;
      margin-bottom: 1rem;
    }
    .mensagem-sucesso {
      color: #1f804e;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
      <span class="header-title">Painel do Vendedor</span>
    </div>
  </header>
  <main>
    <div class="container">
      <h1>Editar Produto</h1>
      <?php if ($erro): ?>
        <div class="mensagem-erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
      <?php if ($sucesso): ?>
        <div class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <label for="nome">Nome do Produto*</label>
        <input type="text" id="nome" name="nome" required maxlength="100" value="<?= htmlspecialchars($produto['nome']) ?>">

        <label for="preco">Preço (R$)*</label>
        <input type="number" id="preco" name="preco" step="0.01" min="0" required value="<?= htmlspecialchars($produto['preco']) ?>">

        <label for="estoque">Quantidade em Estoque*</label>
        <input type="number" id="estoque" name="estoque" min="0" required value="<?= htmlspecialchars($produto['estoque']) ?>">

        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" maxlength="1000"><?= htmlspecialchars($produto['descricao']) ?></textarea>

        <label>Imagem:</label>
        <div class="imagens-preview">
          <?php if (!empty($imagens[0])): ?>
            <img src="<?= htmlspecialchars($imagens[0]) ?>" alt="Imagem 1">
          <?php else: ?>
            <img src="img/sem-imagem.png" alt="Sem imagem">
          <?php endif; ?>
        </div>
        <label class="imagem-label" for="imagem0">Alterar Imagem:</label>
        <input type="file" id="imagem0" name="imagens[]" accept="image/*">
        <?php if (!empty($imagens[0])): ?>
          <input type="hidden" name="imagem_existente_0" value="<?= htmlspecialchars($imagens[0]) ?>">
        <?php endif; ?>

        <div class="botoes">
          <button type="submit" class="botao-acao">Salvar Alterações</button>
          <button type="button" class="botao-acao" onclick="window.location.href='ver_produtos.php'">Voltar</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
