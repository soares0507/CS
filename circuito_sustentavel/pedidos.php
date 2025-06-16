<?php
session_start();
include 'conexao.php';

// --- NOVO BLOCO PARA ATUALIZAR STATUS DO PEDIDO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido']) && isset($_POST['confirmar_recebimento'])) {
    $id_pedido = intval($_POST['id_pedido']);
    // Atualiza o status do pedido para 'Concluído'
    $sql_update = "UPDATE Pedido SET status = 'Concluído' WHERE id_pedido = $id_pedido";
    $conexao->query($sql_update);
    header('Location: pedidos.php');
    exit;
}

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['vendedor_id'])) {
    header('Location: login.php');
    exit;
}

$pedidos = [];

if (isset($_SESSION['usuario_id'])) {
    $id_cliente = $_SESSION['usuario_id'];
    
    $sql = "SELECT p.id_pedido, p.data, p.status, p.codigo_rastreio, ip.id_produto, ip.quantidade, ip.preco, pr.nome AS nome_produto, pr.imagens
            FROM Pedido p
            JOIN Item_Pedido ip ON p.id_pedido = ip.id_pedido
            JOIN Produto pr ON ip.id_produto = pr.id_produto
            WHERE p.id_cliente = '$id_cliente'
            ORDER BY p.data DESC, p.id_pedido DESC";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $pedidos[$row['id_pedido']]['info'] = [
                'data' => $row['data'],
                'status' => $row['status'],
                'codigo_rastreio' => isset($row['codigo_rastreio']) ? $row['codigo_rastreio'] : '',
            ];
            $pedidos[$row['id_pedido']]['itens'][] = $row;
        }
    }
} elseif (isset($_SESSION['vendedor_id'])) {
    $id_vendedor = $_SESSION['vendedor_id'];
    
    $sql = "SELECT p.id_pedido, p.data, p.status, p.codigo_rastreio, ip.id_produto, ip.quantidade, ip.preco, pr.nome AS nome_produto, pr.imagens
            FROM Pedido p
            JOIN Item_Pedido ip ON p.id_pedido = ip.id_pedido
            JOIN Produto pr ON ip.id_produto = pr.id_produto
            WHERE p.id_vendedor = '$id_vendedor'
            ORDER BY p.data DESC, p.id_pedido DESC";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $pedidos[$row['id_pedido']]['info'] = [
                'data' => $row['data'],
                'status' => $row['status'],
                'codigo_rastreio' => isset($row['codigo_rastreio']) ? $row['codigo_rastreio'] : '',
            ];
            $pedidos[$row['id_pedido']]['itens'][] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #d4d3c8;
            margin: 0;
            padding: 0;
        }
        header {
            background: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
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
            max-width: 900px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(40,160,96,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            min-height: 70vh;
        }
        h1 {
            color: #1f804e;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .pedido-box {
            display: flex;
            align-items: center;
            gap: 22px;
            background: #e9f7ef;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(40,160,96,0.10);
            padding: 1.2rem 1.5rem;
            margin-bottom: 1.7rem;
            font-size: 1.08rem;
            border: 1.5px solid #d2e9df;
        }
        .pedido-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e9f7ef;
            background: #f3f2e7;
            box-shadow: 0 2px 8px rgba(40,160,96,0.07);
        }
        .pedido-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .pedido-produto-nome {
            font-weight: bold;
            color: #1f804e;
            font-size: 1.18rem;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pedido-status {
            font-weight: bold;
            color: #145c36;
            margin-top: 2px;
        }
        .pedido-data {
            color: #888;
            font-size: 0.98rem;
        }
        .pedido-valor {
            color: #145c36;
            font-weight: bold;
        }
        .pedido-qtd {
            color: #555;
        }
        @media (max-width: 600px) {
            main { padding: 1.2rem 0.5rem; }
            .pedido-box { flex-direction: column; align-items: flex-start; gap: 10px; padding: 1rem 0.5rem; }
            .pedido-img { width: 60px; height: 60px; }
            .pedido-produto-nome { max-width: 100%; font-size: 1rem; }
        }
    </style>
    <script>
        function voltarPainel() {
            <?php if (isset($_SESSION['usuario_id'])): ?>
                window.location.href = 'usuario.php';
            <?php elseif (isset($_SESSION['vendedor_id'])): ?>
                window.location.href = 'vendedor.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
        </div>
        <div style="margin-left:auto;">
            <button onclick="voltarPainel()" style="background: none; border: none; font-size: 2rem; color: #1f804e; cursor: pointer; font-weight: bold;">&#10005;</button>
        </div>
    </header>
    <main>
        <h1>Meus Pedidos</h1>
        <?php if (empty($pedidos)): ?>
            <div style="color:#888;text-align:center;font-size:1.1rem;">Nenhum pedido encontrado.</div>
        <?php else: ?>
            <?php foreach ($pedidos as $id_pedido => $pedido): 
                $info = $pedido['info'];
                $status = $info['status'];
                $codigo_rastreio = $info['codigo_rastreio'];
            ?>
            <div class="pedido-box" style="flex-direction:column;align-items:stretch;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <span style="color:#888;">Pedido #<?= $id_pedido ?></span>
                    <span class="pedido-status">Status: <?= htmlspecialchars($status) ?></span>
                </div>
                <div class="pedido-data">Data: <?= date('d/m/Y H:i', strtotime($info['data'])) ?></div>
                <div style="margin:10px 0;">
                    <?php foreach ($pedido['itens'] as $item): 
                        $img = 'img/sem-imagem.png';
                        if (!empty($item['imagens'])) {
                            $imagens = json_decode($item['imagens'], true);
                            if (!is_array($imagens)) $imagens = explode(',', $item['imagens']);
                            if (!empty($imagens[0])) $img = $imagens[0];
                        }
                    ?>
                    <div style="display:flex;align-items:center;gap:22px;margin-bottom:10px;">
                        <img src="<?= htmlspecialchars($img) ?>" class="pedido-img" alt="Produto">
                        <div class="pedido-info">
                            <span class="pedido-produto-nome"><?= htmlspecialchars($item['nome_produto']) ?></span>
                            <span class="pedido-valor">Valor: R$ <?= number_format($item['preco'],2,',','.') ?></span>
                            <span class="pedido-qtd">Quantidade: <?= (int)$item['quantidade'] ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($status === 'Pedido enviado para os Correios' && !empty($codigo_rastreio)): ?>
                    <div style="margin-top:8px;color:#1f804e;font-weight:bold;">
                        Código de rastreio: <span style="font-family:monospace;"><?= htmlspecialchars($codigo_rastreio) ?></span>
                    </div>
                <?php endif; ?>
                <div style="margin-top:10px;display:flex;gap:10px;">
                    <form method="post" action="reembolso.php" style="display:inline;">
                        <input type="hidden" name="id_pedido" value="<?= $id_pedido ?>">
                    </form>
                    <form method="post" action="pedidos.php" style="display:inline;">
                        <input type="hidden" name="id_pedido" value="<?= $id_pedido ?>">
                        <input type="hidden" name="confirmar_recebimento" value="1">
                        <button type="submit" style="background:#28a060;color:#fff;border:none;border-radius:6px;padding:7px 18px;font-weight:bold;cursor:pointer;">Confirmar Recebimento</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>
