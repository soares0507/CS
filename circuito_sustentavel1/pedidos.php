<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['vendedor_id'])) {
    header('Location: login.php');
    exit;
}

$pedidos = [];

if (isset($_SESSION['usuario_id'])) {
    $id_cliente = $_SESSION['usuario_id'];
    
    $sql = "SELECT p.id_pedido, p.data, p.status, ip.id_produto, ip.quantidade, ip.preco, pr.nome AS nome_produto, pr.imagens
            FROM Pedido p
            JOIN Item_Pedido ip ON p.id_pedido = ip.id_pedido
            JOIN Produto pr ON ip.id_produto = pr.id_produto
            WHERE p.id_cliente = '$id_cliente'
            ORDER BY p.data DESC, p.id_pedido DESC";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $pedidos[] = $row;
        }
    }
} elseif (isset($_SESSION['vendedor_id'])) {
    $id_vendedor = $_SESSION['vendedor_id'];
    
    $sql = "SELECT p.id_pedido, p.data, p.status, ip.id_produto, ip.quantidade, ip.preco, pr.nome AS nome_produto, pr.imagens
            FROM Pedido p
            JOIN Item_Pedido ip ON p.id_pedido = ip.id_pedido
            JOIN Produto pr ON ip.id_produto = pr.id_produto
            WHERE p.id_vendedor = '$id_vendedor'
            ORDER BY p.data DESC, p.id_pedido DESC";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $pedidos[] = $row;
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
</head>
<body>
    <header>
        <div class="logo">
            <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
        </div>
        <div style="margin-left:auto;">
            <button onclick="window.history.back()" style="background: none; border: none; font-size: 2rem; color: #1f804e; cursor: pointer; font-weight: bold;">&#10005;</button>
        </div>
    </header>
    <main>
        <h1>Meus Pedidos</h1>
        <?php if (empty($pedidos)): ?>
            <div style="color:#888;text-align:center;font-size:1.1rem;">Nenhum pedido encontrado.</div>
        <?php else: ?>
            <?php foreach ($pedidos as $pedido): 
                $img = 'img/sem-imagem.png';
                if (!empty($pedido['imagens'])) {
                    $imagens = json_decode($pedido['imagens'], true);
                    if (!is_array($imagens)) $imagens = explode(',', $pedido['imagens']);
                    if (!empty($imagens[0])) $img = $imagens[0];
                }
                // Buscar código de rastreio se necessário
                $codigo_rastreio = '';
                if ($pedido['status'] === 'Pedido enviado para os Correios') {
                    $sqlR = "SELECT codigo_rastreio FROM Pedido WHERE id_pedido = '{$pedido['id_pedido']}' LIMIT 1";
                    $resR = $conexao->query($sqlR);
                    if ($resR && $rowR = $resR->fetch_assoc()) {
                        $codigo_rastreio = $rowR['codigo_rastreio'];
                    }
                }
            ?>
            <div class="pedido-box">
                <img src="<?= htmlspecialchars($img) ?>" class="pedido-img" alt="Produto">
                <div class="pedido-info">
                    <span class="pedido-produto-nome"><?= htmlspecialchars($pedido['nome_produto']) ?></span>
                    <span class="pedido-data">Data: <?= date('d/m/Y H:i', strtotime($pedido['data'])) ?></span>
                    <span class="pedido-valor">Valor: R$ <?= number_format($pedido['preco'],2,',','.') ?></span>
                    <span class="pedido-qtd">Quantidade: <?= (int)$pedido['quantidade'] ?></span>
                    <span class="pedido-status">Status: <?= htmlspecialchars($pedido['status']) ?></span>
                    <?php if ($pedido['status'] === 'Pedido enviado para os Correios' && !empty($codigo_rastreio)): ?>
                        <div style="margin-top:8px;color:#1f804e;font-weight:bold;">
                            Código de rastreio: <span style="font-family:monospace;"><?= htmlspecialchars($codigo_rastreio) ?></span>
                        </div>
                    <?php endif; ?>
                    <div style="margin-top:10px;display:flex;gap:10px;">
                        <form method="post" action="reembolso.php" style="display:inline;">
                            <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                            <button type="submit" style="background:#eb3b3b;color:#fff;border:none;border-radius:6px;padding:7px 18px;font-weight:bold;cursor:pointer;">Solicitar Reembolso</button>
                        </form>
                        <form method="post" action="confirmar_recebimento.php" style="display:inline;">
                            <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                            <button type="submit" style="background:#28a060;color:#fff;border:none;border-radius:6px;padding:7px 18px;font-weight:bold;cursor:pointer;">Confirmar Recebimento</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>
