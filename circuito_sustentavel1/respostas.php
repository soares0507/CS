<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'respostas.php';
    header('Location: login.php');
    exit;
}

$id_vendedor = $_SESSION['vendedor_id'];

// Processa exclusão de resposta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_resposta'], $_POST['id_pergunta'])) {
    $id_pergunta = intval($_POST['id_pergunta']);
    $conexao->query("DELETE FROM Resposta WHERE id_pergunta = '$id_pergunta' AND id_vendedor = '$id_vendedor'");
    header("Location: respostas.php");
    exit;
}

// Processa envio de resposta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pergunta'], $_POST['resposta_texto']) && !isset($_POST['excluir_resposta'])) {
    $id_pergunta = intval($_POST['id_pergunta']);
    $resposta_texto = trim($_POST['resposta_texto']);
    $id_vendedor = $_SESSION['vendedor_id'];
    if ($resposta_texto !== '') {
        $stmt = $conexao->prepare("INSERT INTO Resposta (id_pergunta, id_vendedor, texto, data) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $id_pergunta, $id_vendedor, $resposta_texto);
        $stmt->execute();
        $stmt->close();
        // Redireciona para evitar reenvio do formulário
        header("Location: respostas.php");
        exit;
    }
}

// Busca todas as perguntas feitas para produtos do vendedor, junto com as respostas (se houver)
$sql = "
SELECT 
    p.id_pergunta,
    p.texto AS pergunta,
    p.data AS data_pergunta,
    pr.nome AS produto_nome,
    c.nome AS cliente_nome,
    r.texto AS resposta,
    r.data AS data_resposta
FROM Pergunta p
JOIN Produto pr ON p.id_produto = pr.id_produto
JOIN Cliente c ON p.id_cliente = c.id_cliente
LEFT JOIN Resposta r ON r.id_pergunta = p.id_pergunta
WHERE pr.id_vendedor = '$id_vendedor'
ORDER BY p.data DESC
";
$res = $conexao->query($sql);
$perguntas = [];
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $perguntas[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Perguntas Recebidas</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #d4d3c8;
            margin: 0;
            padding: 0;
        }
        header {
            background: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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
            max-width: 800px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(40,160,96,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
        }
        h1 {
            color: #1f804e;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .pergunta-card {
            background: #e9f7ef;
            border-radius: 10px;
            padding: 1.2rem 1rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 8px rgba(40,160,96,0.07);
        }
        .pergunta-produto {
            font-weight: bold;
            color: #145c36;
            margin-bottom: 0.3rem;
        }
        .pergunta-cliente {
            color: #1f804e;
            font-size: 1.05rem;
            margin-bottom: 0.2rem;
        }
        .pergunta-texto {
            color: #222;
            margin-bottom: 0.5rem;
        }
        .pergunta-data {
            color: #888;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        .resposta-status {
            font-weight: bold;
            color: #d43131;
        }
        .resposta-status.respondida {
            color: #28a060;
        }
        .resposta-texto {
            background: #fff;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            margin-top: 0.5rem;
            color: #145c36;
            border-left: 4px solid #28a060;
        }
        .sem-perguntas {
            color: #888;
            text-align: center;
            margin-top: 2rem;
        }
        .voltar-btn {
            margin-top: 2rem;
            padding: 12px 28px;
            background: #28a060;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .voltar-btn:hover {
            background: #1f804e;
        }
        .form-resposta {
            margin-top: 0.7rem;
            background: #f7fff9;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            border-left: 4px solid #1f804e;
        }
        .form-resposta textarea {
            width: 100%;
            min-height: 60px;
            border-radius: 6px;
            border: 1px solid #b7e2c6;
            padding: 8px;
            font-size: 1rem;
            resize: vertical;
        }
        .form-resposta button {
            margin-top: 8px;
            background: #28a060;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .form-resposta button:hover {
            background: #1f804e;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
            <span class="header-title">Perguntas Recebidas</span>
        </div>
    </header>
    <main>
        <h1>Perguntas Recebidas</h1>
        <?php if (empty($perguntas)): ?>
            <div class="sem-perguntas">Nenhuma pergunta recebida para seus produtos.</div>
        <?php else: ?>
            <?php foreach ($perguntas as $p): ?>
                <div class="pergunta-card">
                    <div class="pergunta-produto">Produto: <?= htmlspecialchars($p['produto_nome']) ?></div>
                    <div class="pergunta-cliente">Cliente: <?= htmlspecialchars($p['cliente_nome']) ?></div>
                    <div class="pergunta-texto">Pergunta: <?= htmlspecialchars($p['pergunta']) ?></div>
                    <div class="pergunta-data"><?= date('d/m/Y H:i', strtotime($p['data_pergunta'])) ?></div>
                    <?php if (!empty($p['resposta'])): ?>
                        <div class="resposta-status respondida">Respondida</div>
                        <div class="resposta-texto">
                            <?= htmlspecialchars($p['resposta']) ?>
                            <br><span style="color:#888;font-size:0.95rem;"><?= date('d/m/Y H:i', strtotime($p['data_resposta'])) ?></span>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_pergunta" value="<?= intval($p['id_pergunta']) ?>">
                                <button type="submit" name="excluir_resposta" style="background:#d43131;color:#fff;border:none;border-radius:5px;padding:4px 12px;margin-left:10px;cursor:pointer;">Excluir resposta</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="resposta-status">Aguardando resposta</div>
                        <form class="form-resposta" method="post" action="respostas.php">
                            <input type="hidden" name="id_pergunta" value="<?= intval($p['id_pergunta']) ?>">
                            <label for="resposta_texto_<?= intval($p['id_pergunta']) ?>">Responder:</label>
                            <textarea id="resposta_texto_<?= intval($p['id_pergunta']) ?>" name="resposta_texto" required></textarea>
                            <button type="submit">Enviar resposta</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <button class="voltar-btn" onclick="window.location.href='vendedor.php'">Voltar</button>
    </main>
</body>
</html>
