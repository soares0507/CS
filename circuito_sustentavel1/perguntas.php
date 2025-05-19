<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['redirect_after_login'] = 'perguntas.php';
    header('Location: login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

// Busca perguntas feitas pelo usuário
$sql = "SELECT p.id_pergunta, p.texto AS pergunta, p.data, pr.nome AS produto_nome, r.texto AS resposta
        FROM Pergunta p
        JOIN Produto pr ON p.id_produto = pr.id_produto
        LEFT JOIN Resposta r ON r.id_pergunta = p.id_pergunta
        WHERE p.id_cliente = '$id_cliente'
        ORDER BY p.data DESC";
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
    <title>Minhas Perguntas</title>
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
            max-width: 700px;
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
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
            <span class="header-title">Minhas Perguntas</span>
        </div>
    </header>
    <main>
        <h1>Minhas Perguntas</h1>
        <?php if (empty($perguntas)): ?>
            <div class="sem-perguntas">Você ainda não fez nenhuma pergunta.</div>
        <?php else: ?>
            <?php foreach ($perguntas as $p): ?>
                <div class="pergunta-card">
                    <div class="pergunta-produto">Produto: <?= htmlspecialchars($p['produto_nome']) ?></div>
                    <div class="pergunta-texto">Pergunta: <?= htmlspecialchars($p['pergunta']) ?></div>
                    <div class="pergunta-data"><?= date('d/m/Y H:i', strtotime($p['data'])) ?></div>
                    <?php if (!empty($p['resposta'])): ?>
                        <div class="resposta-status respondida">Respondida</div>
                        <div class="resposta-texto"><?= htmlspecialchars($p['resposta']) ?></div>
                    <?php else: ?>
                        <div class="resposta-status">Aguardando resposta do vendedor</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <button class="voltar-btn" onclick="window.location.href='usuario.php'">Voltar</button>
    </main>
</body>
</html>
