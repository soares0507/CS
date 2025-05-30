<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'carrinho_vendedor.php';
    header('Location: login.php');
    exit;
}

$id_vendedor = $_SESSION['vendedor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_qtd'])) {
    $id_carrinho = intval($_POST['id_carrinho']);
    $id_produto = intval($_POST['id_produto']);
    $nova_qtd = max(1, intval($_POST['quantidade']));
    $res_estoque = $conexao->query("SELECT estoque FROM Produto WHERE id_produto = '$id_produto'");
    $estoque = 1;
    if ($res_estoque && $row_estoque = $res_estoque->fetch_assoc()) {
        $estoque = (int)$row_estoque['estoque'];
    }
    if ($nova_qtd > $estoque) $nova_qtd = $estoque;
    $conexao->query("UPDATE Item_Carrinho SET quantidade = '$nova_qtd' WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto'");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_produto'])) {
    $id_carrinho = intval($_POST['id_carrinho']);
    $id_produto = intval($_POST['id_produto']);
    $conexao->query("DELETE FROM Item_Carrinho WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto'");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    header('Location: pagamento.php');
    exit;
}

$sql = "SELECT id_carrinho FROM Carrinho WHERE id_vendedor = '$id_vendedor' AND id_cliente IS NULL LIMIT 1";
$res = $conexao->query($sql);

$id_carrinho = null;
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $id_carrinho = $row['id_carrinho'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho (Vendedor)</title>
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
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
        .carrinho-vendedor {
            margin-bottom: 2.5rem;
            border: 1.5px solid #e9f7ef;
            border-radius: 14px;
            background: #e9f7ef;
            box-shadow: 0 2px 8px rgba(40,160,96,0.07);
            padding: 1.5rem 1rem 1rem 1rem;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-bottom: 1rem;
        }
        th, td {
            padding: 10px 8px;
            background: #fff;
            border-radius: 8px;
            text-align: left;
            font-size: 1rem;
        }
        th {
            background: #e9f7ef;
            color: #1f804e;
            font-weight: bold;
            
        }
        .total {
            font-weight: bold;
            color: #1f804e;
            background: #e9f7ef;
        }
        .sem-carrinho {
            color: #888;
            text-align: center;
            margin-top: 2rem;
            font-size: 1.1rem;
        }
        .btn-finalizar {
            background: #28a060;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 32px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .btn-finalizar:hover {
            background: #1f804e;
            transform: scale(1.04);
        }
        .btn-voltar {
            background: #aaa;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 32px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin: 2rem auto 0 auto;
            display: block;
            transition: background 0.2s;
        }
        .btn-voltar:hover {
            background: #888;
        }
        @media (max-width: 900px) {
            main { padding: 1.2rem 0.5rem; }
            .carrinho-vendedor { padding: 1rem 0.5rem; }
            table, th, td { font-size: 0.95rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
           
        </div>
    </header>
    <main>
        <h1> Carrinho</h1>
        <?php if (!$id_carrinho): ?>
            <div class="sem-carrinho">Seu carrinho está vazio.</div>
        <?php else: ?>
            <div class="carrinho-vendedor">
                <form method="post">
                <table>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                    <?php
                    $sql_itens = "SELECT ic.*, p.nome, p.preco, p.imagens, p.estoque FROM Item_Carrinho ic JOIN Produto p ON ic.id_produto = p.id_produto WHERE ic.id_carrinho = '$id_carrinho'";
                    $res_itens = $conexao->query($sql_itens);
                    $total = 0;
                    if ($res_itens && $res_itens->num_rows > 0):
                        while ($item = $res_itens->fetch_assoc()):
                            $imagens = [];
                            if (!empty($item['imagens'])) {
                                $imagens = json_decode($item['imagens'], true);
                                if (!is_array($imagens)) $imagens = explode(',', $item['imagens']);
                            }
                            $img = !empty($imagens[0]) ? $imagens[0] : 'img/sem-imagem.png';
                            $subtotal = $item['preco'] * $item['quantidade'];
                            $total += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:6px;vertical-align:middle;margin-right:8px;">
                            <?= htmlspecialchars($item['nome']) ?>
                        </td>
                        <td>R$ <?= number_format($item['preco'],2,',','.') ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_carrinho" value="<?= $id_carrinho ?>">
                                <input type="hidden" name="id_produto" value="<?= $item['id_produto'] ?>">
                                <input type="number" name="quantidade" min="1" max="<?= (int)$item['estoque'] ?>" value="<?= (int)$item['quantidade'] ?>" style="width:60px;" <?= ((int)$item['estoque'] <= 0 ? 'disabled' : '') ?>>
                                <button type="submit" name="atualizar_qtd" style="background:#28a060;color:#fff;border:none;border-radius:4px;padding:4px 10px;cursor:pointer;" <?= ((int)$item['estoque'] <= 0 ? 'disabled' : '') ?>>Atualizar</button>
                            </form>
                        </td>
                        <td>R$ <?= number_format($subtotal,2,',','.') ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_carrinho" value="<?= $id_carrinho ?>">
                                <input type="hidden" name="id_produto" value="<?= $item['id_produto'] ?>">
                                <button type="submit" name="remover_produto" style="background:#d43131;color:#fff;border:none;border-radius:4px;padding:4px 10px;cursor:pointer;">Remover</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3" class="total">Total</td>
                        <td class="total">R$ <?= number_format($total,2,',','.') ?></td>
                        
                    </tr>
                    <?php else: ?>
                    <tr><td colspan="5" style="color:#888;">Nenhum produto neste carrinho.</td></tr>
                    <?php endif; ?>
                </table>
                </form>
                <form method="post" style="margin-top:10px;">
                    <button class="btn-finalizar" type="submit" name="finalizar_compra">Finalizar Compra</button>
                </form>
            </div>
        <?php endif; ?>
        <button class="btn-voltar" onclick="window.location.href='loja.php'">Voltar</button>
    </main>
</body>
</html>
