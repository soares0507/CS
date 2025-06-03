<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'cadastrar_produto.php';
    header('Location: login.php');
    exit;
}

$erro = '';
$sucesso = '';

$pasta_upload = __DIR__ . '/uploads_produtos/';
$url_upload = 'uploads_produtos/';
if (!is_dir($pasta_upload)) {
    mkdir($pasta_upload, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_produto = $_POST['nome'] ?? '';
    $preco_produto = $_POST['preco'] ?? '';
    $estoque_produto = $_POST['estoque'] ?? '';
    $descricao_produto = $_POST['descricao'] ?? '';
    $id_vendedor = $_SESSION['vendedor_id'];

    if (!$nome_produto || !$preco_produto || !$estoque_produto) {
        $erro = "Preencha todos os campos obrigatórios!";
    } else {
        $imagens_salvas = [];
        if (!empty($_FILES['imagens']) && isset($_FILES['imagens']['name'][0]) && $_FILES['imagens']['name'][0] != '') {
            $total_imagens = count($_FILES['imagens']['name']);
            for ($i = 0; $i < $total_imagens && $i < 6; $i++) {
                if ($_FILES['imagens']['error'][$i] === UPLOAD_ERR_OK) {
                    $caminho_temporario = $_FILES['imagens']['tmp_name'][$i];
                    $extensao = strtolower(pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION));
                    $permitidas = ['jpg','jpeg','png','gif','webp'];
                    if (!in_array($extensao, $permitidas)) continue;
                    $nome_arquivo = uniqid('produto_', true) . '.' . $extensao;
                    $destino = $pasta_upload . $nome_arquivo;
                    if (move_uploaded_file($caminho_temporario, $destino)) {
                        $imagens_salvas[] = $url_upload . $nome_arquivo;
                    }
                }
            }
        }
        $imagens_json = json_encode($imagens_salvas);

        $sql = "INSERT INTO Produto (nome, descricao, preco, estoque, id_vendedor, imagens) VALUES ('$nome_produto', '$descricao_produto', '$preco_produto', '$estoque_produto', '$id_vendedor', '$imagens_json')";
        if ($conexao->query($sql)) {
            $sucesso = "Produto cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar produto!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cadastrar Produto</title>
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
      max-width: 500px;
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
    input[type="file"] {
      margin-bottom: 0.5rem;
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
    </div>
    <div style="margin-left:78%;">
      <button onclick="window.location.href='produtos.php'" style="background: none; border: none; font-size: 2rem; color: #1f804e; cursor: pointer; font-weight: bold;">&#10005;</button>
    </div>
  </header>
  <main>
    <div class="container">
      <h1>Cadastrar Produto</h1>
      <?php if ($erro): ?>
        <div class="mensagem-erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
      <?php if ($sucesso): ?>
        <div class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <label for="nome">Nome do Produto*</label>
        <input type="text" id="nome" name="nome" required maxlength="100">

        <label for="preco">Preço (R$)*</label>
        <input type="number" id="preco" name="preco" step="0.01" min="0" required>

        <label for="estoque">Quantidade em Estoque*</label>
        <input type="number" id="estoque" name="estoque" min="0" required>

        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" maxlength="1000"></textarea>

        <label for="imagens">Imagens:</label>
        <input type="file" id="imagens" name="imagens[]" accept="image/*" multiple>

        <div class="botoes">
          <button type="submit" class="botao-acao">Cadastrar</button>
          
        </div>
      </form>
    </div>
  </main>
</body>
</html>
