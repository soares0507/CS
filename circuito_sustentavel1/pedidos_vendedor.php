<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'pedidos_vendedor.php';
    header('Location: login.php');
    exit;
}

$id_vendedor = $_SESSION['vendedor_id'];
$mensagem = '';

// Atualização de status do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'], $_POST['novo_status'])) {
    $id_pedido = intval($_POST['id_pedido']);
    $novo_status = $_POST['novo_status'];
    $codigo_rastreio = isset($_POST['codigo_rastreio']) ? trim($_POST['codigo_rastreio']) : null;

    // Verifica se o vendedor tem produtos neste pedido
    $verifica = $conexao->prepare("SELECT COUNT(*) FROM Item_Pedido ip JOIN Produto pr ON ip.id_produto = pr.id_produto WHERE ip.id_pedido=? AND pr.id_vendedor=?");
    $verifica->bind_param("ii", $id_pedido, $id_vendedor);
    $verifica->execute();
    $verifica->bind_result($tem_produto);
    $verifica->fetch();
    $verifica->close();

    if ($tem_produto > 0) {
        if ($novo_status === 'Pedido enviado para os Correios' && $codigo_rastreio) {
            $sql = "UPDATE Pedido SET status=?, codigo_rastreio=? WHERE id_pedido=?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssi", $novo_status, $codigo_rastreio, $id_pedido);
        } else {
            $sql = "UPDATE Pedido SET status=?, codigo_rastreio=NULL WHERE id_pedido=?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $novo_status, $id_pedido);
        }
        if ($stmt->execute()) {
            $mensagem = "Status do pedido atualizado!";
        } else {
            $mensagem = "Erro ao atualizar status!";
        }
        $stmt->close();
    } else {
        $mensagem = "Você não tem permissão para alterar este pedido.";
    }
}

// Busca todos os pedidos dos produtos do vendedor
$sql = "
SELECT 
    p.id_pedido, p.data, p.status, p.codigo_rastreio,
    c.nome AS cliente_nome, c.email AS cliente_email, c.telefone AS cliente_telefone,
    e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep,
    pr.id_produto, pr.nome AS produto_nome, pr.imagens, pr.preco, ip.quantidade
FROM Pedido p
JOIN Item_Pedido ip ON p.id_pedido = ip.id_pedido
JOIN Produto pr ON ip.id_produto = pr.id_produto
LEFT JOIN Cliente c ON p.id_cliente = c.id_cliente
LEFT JOIN Endereco e ON e.id_cliente = c.id_cliente AND e.id_endereco = (
    SELECT MAX(id_endereco) FROM Endereco WHERE id_cliente = c.id_cliente
)
WHERE pr.id_vendedor = ?
ORDER BY p.data DESC, p.id_pedido DESC
";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$res = $stmt->get_result();

$pedidos = [];
while ($row = $res->fetch_assoc()) {
    $id_pedido = $row['id_pedido'];
    if (!isset($pedidos[$id_pedido])) {
        $pedidos[$id_pedido] = [
            'info' => [
                'data' => $row['data'],
                'status' => $row['status'],
                'codigo_rastreio' => $row['codigo_rastreio'],
                'cliente_nome' => $row['cliente_nome'],
                'cliente_email' => $row['cliente_email'],
                'cliente_telefone' => $row['cliente_telefone'],
                'rua' => $row['rua'],
                'numero' => $row['numero'],
                'complemento' => $row['complemento'],
                'bairro' => $row['bairro'],
                'cidade' => $row['cidade'],
                'estado' => $row['estado'],
                'cep' => $row['cep'],
            ],
            'itens' => []
        ];
    }
    $pedidos[$id_pedido]['itens'][] = [
        'id_produto' => $row['id_produto'],
        'produto_nome' => $row['produto_nome'],
        'imagens' => $row['imagens'],
        'quantidade' => $row['quantidade'],
        'preco' => $row['preco']
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pedidos dos Clientes</title>
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
      box-shadow: 0 10px 16px rgba(40,160,96,0.08);
      padding: 2.5rem 2rem 2rem 2rem;
      max-width: 900px;
      width: 100%;
      margin: 2rem auto;
      text-align: center;
    }
    h1 {
      color: #1f804e;
      font-size: 2rem;
      margin-bottom: 1.5rem;
    }
    .mensagem {
      color: #1f804e;
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
    }
    .pedido-box {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 10px rgba(40,160,96,0.10);
      padding: 1.2rem 1.5rem;
      margin-bottom: 2rem;
      font-size: 1.08rem;
      text-align: left;
      border: 1.5px solid #d2e9df;
    }
    .pedido-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    .pedido-status {
      font-weight: bold;
      color: #145c36;
      font-size: 1.1rem;
    }
    .pedido-produtos {
      margin-bottom: 1rem;
    }
    .produto-info {
      display: flex;
      align-items: center;
      gap: 18px;
      margin-bottom: 10px;
    }
    .produto-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #e9f7ef;
      background: #f3f2e7;
    }
    .produto-nome {
      font-weight: bold;
      color: #1f804e;
      font-size: 1.1rem;
      word-break: break-word;
      white-space: normal;
      display: block;
      max-width: 300px;
      line-height: 1.2;
    }
    .cliente-info, .endereco-info {
      margin-bottom: 8px;
      font-size: 1rem;
    }
    .endereco-info {
      color: #444;
    }
    .pedido-actions {
      margin-top: 1rem;
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
    }
    select, input[type="text"] {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1.5px solid #28a060;
      font-size: 1rem;
      background: #f3f2e7;
      color: #222;
      margin-right: 10px;
    }
    .btn-salvar {
      background: #28a060;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 24px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }
    .btn-salvar:hover {
      background: #1f804e;
      transform: scale(1.04);
    }
    .codigo-rastreio-label {
      color: #145c36;
      font-weight: bold;
      margin-right: 5px;
    }
    @media (max-width: 700px) {
      .container { padding: 1.2rem 0.5rem; }
      .pedido-box { padding: 1rem 0.5rem; }
      .produto-nome { max-width: 100%; font-size: 1rem; }
      .pedido-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    }
  </style>
  <script>
    function toggleRastreio(selectElem, pedidoId) {
      var rastreioDiv = document.getElementById('rastreio-area-' + pedidoId);
      if (selectElem.value === 'Pedido enviado para os Correios') {
        rastreioDiv.style.display = 'inline-block';
      } else {
        rastreioDiv.style.display = 'none';
      }
    }
  </script>
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
      <h1>Pedidos dos Clientes</h1>
      <?php if ($mensagem): ?>
        <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
      <?php endif; ?>
      <?php if (empty($pedidos)): ?>
        <div style="color:#888;text-align:center;">Nenhum pedido encontrado.</div>
      <?php else: ?>
        <?php foreach ($pedidos as $id_pedido => $pedido): 
          $info = $pedido['info'];
          $status = $info['status'];
          $codigo_rastreio = $info['codigo_rastreio'];
        ?>
        <div class="pedido-box">
          <div class="pedido-header">
            <div>
              <span style="color:#888;">Pedido #<?= $id_pedido ?></span>
              <span style="margin-left:18px;color:#888;">Data: <?= date('d/m/Y H:i', strtotime($info['data'])) ?></span>
            </div>
            <div class="pedido-status">Status: <?= htmlspecialchars($status) ?></div>
          </div>
          <div class="pedido-produtos">
            <?php foreach ($pedido['itens'] as $item): 
              $imagens = [];
              if (!empty($item['imagens'])) {
                $imagens = json_decode($item['imagens'], true);
                if (!is_array($imagens)) $imagens = explode(',', $item['imagens']);
              }
              $img = !empty($imagens[0]) ? $imagens[0] : 'img/sem-imagem.png';
            ?>
            <div class="produto-info">
              <img src="<?= htmlspecialchars($img) ?>" class="produto-img" alt="Produto">
              <div>
                <span class="produto-nome"><?= htmlspecialchars($item['produto_nome']) ?></span>
                <div>Qtd: <?= (int)$item['quantidade'] ?> | R$ <?= number_format($item['preco'],2,',','.') ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="cliente-info">
            <strong>Cliente:</strong> <?= htmlspecialchars($info['cliente_nome']) ?> | <?= htmlspecialchars($info['cliente_email']) ?> | <?= htmlspecialchars($info['cliente_telefone']) ?>
          </div>
          <div class="endereco-info">
            <strong>Endereço:</strong>
            <?= htmlspecialchars($info['rua']) ?>, <?= htmlspecialchars($info['numero']) ?>
            <?= $info['complemento'] ? ' - '.htmlspecialchars($info['complemento']) : '' ?>,
            <?= htmlspecialchars($info['bairro']) ?>, <?= htmlspecialchars($info['cidade']) ?>/<?= htmlspecialchars($info['estado']) ?> - CEP: <?= htmlspecialchars($info['cep']) ?>
          </div>
          <form method="post" class="pedido-actions" style="margin-top:1.2rem;">
            <input type="hidden" name="id_pedido" value="<?= $id_pedido ?>">
            <select name="novo_status" onchange="toggleRastreio(this, <?= $id_pedido ?>)">
              <option value="Preparando pedido" <?= $status == 'Preparando pedido' ? 'selected' : '' ?>>Preparando pedido</option>
              <option value="Pedido enviado para os Correios" <?= $status == 'Pedido enviado para os Correios' ? 'selected' : '' ?>>Pedido enviado para os Correios</option>
              <option value="Cancelar pedido" <?= $status == 'Cancelar pedido' ? 'selected' : '' ?>>Cancelar pedido</option>
            </select>
            <span id="rastreio-area-<?= $id_pedido ?>" style="display:<?= $status == 'Pedido enviado para os Correios' ? 'inline-block' : 'none' ?>;">
              <label class="codigo-rastreio-label" for="codigo_rastreio_<?= $id_pedido ?>">Código de rastreio:</label>
              <input type="text" name="codigo_rastreio" id="codigo_rastreio_<?= $id_pedido ?>" value="<?= htmlspecialchars($codigo_rastreio) ?>" style="width:160px;">
            </span>
            <button type="submit" class="btn-salvar">Salvar</button>
          </form>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
