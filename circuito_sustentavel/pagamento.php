<?php
session_start();
include 'conexao.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['pedido_registrado'])) {
    unset($_SESSION['pedido_registrado']);
}

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['vendedor_id'])) {
    $_SESSION['redirect_after_login'] = 'pagamento.php';
    header('Location: login.php');
    exit;
}

$nome_usuario = '';
if (isset($_SESSION['usuario_id'])) {
    $id_cliente = $_SESSION['usuario_id'];
    $sql = "SELECT nome FROM Cliente WHERE id_cliente = '$id_cliente'";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) $nome_usuario = $res->fetch_assoc()['nome'];
} else if (isset($_SESSION['vendedor_id'])) {
    $id_vendedor = $_SESSION['vendedor_id'];
    $sql = "SELECT nome FROM Vendedor WHERE id_vendedor = '$id_vendedor'";
    $res = $conexao->query($sql);
    if ($res && $res->num_rows > 0) $nome_usuario = $res->fetch_assoc()['nome'];
}

$id_produto_unico = isset($_GET['id_produto']) ? intval($_GET['id_produto']) : 0;
$quantidade_unica = isset($_GET['quantidade']) ? max(1, intval($_GET['quantidade'])) : 1;

$produto_unico = null;
$produtos_carrinho = [];
$total = 0;


$valor_assinatura = isset($_GET['assinatura']) ? floatval($_GET['assinatura']) : 0;
$is_assinatura = $valor_assinatura > 0;

if ($is_assinatura) {
    $total = $valor_assinatura;
    $produto_unico = null;
    $produtos_carrinho = [];
} else {
    if ($id_produto_unico > 0) {
        $sql = "SELECT * FROM Produto WHERE id_produto = '$id_produto_unico'";
        $res = $conexao->query($sql);
        if ($res && $res->num_rows > 0) {
            $produto_unico = $res->fetch_assoc();
            $img_produto = 'img/sem-imagem.png';
            if (!empty($produto_unico['imagens'])) {
                $imagens = json_decode($produto_unico['imagens'], true);
                if (!is_array($imagens)) $imagens = explode(',', $produto_unico['imagens']);
                if (!empty($imagens[0])) $img_produto = $imagens[0];
            }
            $estoque = (int)$produto_unico['estoque'];
            if ($quantidade_unica > $estoque) $quantidade_unica = $estoque;
            $total = $produto_unico['preco'] * $quantidade_unica;
        }
    } else {
        if (isset($_SESSION['usuario_id'])) {
            $id_cliente = $_SESSION['usuario_id'];
            $sql = "SELECT c.id_carrinho, ic.id_produto, ic.quantidade, p.nome, p.preco, p.imagens, p.estoque
                    FROM Carrinho c
                    JOIN Item_Carrinho ic ON c.id_carrinho = ic.id_carrinho
                    JOIN Produto p ON ic.id_produto = p.id_produto
                    WHERE c.id_cliente = '$id_cliente'";
            $res = $conexao->query($sql);
            if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $img_produto = 'img/sem-imagem.png';
                    if (!empty($row['imagens'])) {
                        $imagens = json_decode($row['imagens'], true);
                        if (!is_array($imagens)) $imagens = explode(',', $row['imagens']);
                        if (!empty($imagens[0])) $img_produto = $imagens[0];
                    }
                    $row['img_produto'] = $img_produto;
                    $row['subtotal'] = $row['preco'] * $row['quantidade'];
                    $total += $row['subtotal'];
                    $produtos_carrinho[] = $row;
                }
            }
        } else if (isset($_SESSION['vendedor_id'])) {
            $id_vendedor = $_SESSION['vendedor_id'];
            $sql = "SELECT c.id_carrinho, ic.id_produto, ic.quantidade, p.nome, p.preco, p.imagens, p.estoque
                    FROM Carrinho c
                    JOIN Item_Carrinho ic ON c.id_carrinho = ic.id_carrinho
                    JOIN Produto p ON ic.id_produto = p.id_produto
                    WHERE c.id_vendedor = '$id_vendedor' AND c.id_cliente IS NULL";
            $res = $conexao->query($sql);
            if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $img_produto = 'img/sem-imagem.png';
                    if (!empty($row['imagens'])) {
                        $imagens = json_decode($row['imagens'], true);
                        if (!is_array($imagens)) $imagens = explode(',', $row['imagens']);
                        if (!empty($imagens[0])) $img_produto = $imagens[0];
                    }
                    $row['img_produto'] = $img_produto;
                    $row['subtotal'] = $row['preco'] * $row['quantidade'];
                    $total += $row['subtotal'];
                    $produtos_carrinho[] = $row;
                }
            }
        }
    }
}

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo = $_POST['metodo'] ?? '';
    if ($metodo === 'cartao') {
        $mensagem = "Pagamento com cartão aprovado!";
    } elseif ($metodo === 'pix') {
        $mensagem = " Pagamento via Pix gerado! Use o QR Code abaixo para pagar.";
    } elseif ($metodo === 'boleto') {
        $mensagem = "Boleto gerado!.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rua'], $_POST['numero'], $_POST['bairro'], $_POST['cidade'], $_POST['estado'], $_POST['cep'])) {
    $rua = $conexao->real_escape_string(trim($_POST['rua']));
    $numero = $conexao->real_escape_string(trim($_POST['numero']));
    $complemento = $conexao->real_escape_string(trim($_POST['complemento'] ?? ''));
    $bairro = $conexao->real_escape_string(trim($_POST['bairro']));
    $cidade = $conexao->real_escape_string(trim($_POST['cidade']));
    $estado = $conexao->real_escape_string(trim($_POST['estado']));
    $cep = $conexao->real_escape_string(trim($_POST['cep']));
    $id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;
    $id_vendedor = isset($_SESSION['vendedor_id']) ? intval($_SESSION['vendedor_id']) : null;

    if ($id_cliente) {
        $conexao->query("INSERT INTO Endereco (id_cliente, rua, numero, complemento, bairro, cidade, estado, cep) VALUES ('$id_cliente', '$rua', '$numero', '$complemento', '$bairro', '$cidade', '$estado', '$cep')");
    } elseif ($id_vendedor) {
        $conexao->query("INSERT INTO Endereco (id_vendedor, rua, numero, complemento, bairro, cidade, estado, cep) VALUES ('$id_vendedor', '$rua', '$numero', '$complemento', '$bairro', '$cidade', '$estado', '$cep')");
    }

    // Preservar parâmetros da compra ao redirecionar
    $params = [];
    if (isset($_GET['id_produto'])) $params[] = 'id_produto=' . urlencode($_GET['id_produto']);
    if (isset($_GET['quantidade'])) $params[] = 'quantidade=' . urlencode($_GET['quantidade']);
    if (isset($_GET['assinatura'])) $params[] = 'assinatura=' . urlencode($_GET['assinatura']);
    $query = $params ? ('?' . implode('&', $params)) : '';
    header("Location: pagamento.php$query");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['metodo']) && in_array($_POST['metodo'], ['cartao','pix','boleto'])) {
    
    if (!isset($_SESSION['pedido_registrado'])) {
        $id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;
        $id_vendedor = isset($_SESSION['vendedor_id']) ? intval($_SESSION['vendedor_id']) : null;
        $forma_pagamento = $_POST['metodo'];
       
        $status_pedido = 'Pedido enviado ao vendedor';

       
        $itens = [];
        $total_pedido = 0;
        if ($is_assinatura) {
            
            if ($id_cliente) {
                $conexao->query("UPDATE Cliente SET premium = 1 WHERE id_cliente = '$id_cliente'");
            } elseif ($id_vendedor) {
                $conexao->query("UPDATE Vendedor SET premium = 1 WHERE id_vendedor = '$id_vendedor'");
            }
        } else if ($id_produto_unico > 0 && $produto_unico) {
            $itens[] = [
                'id_produto' => $produto_unico['id_produto'],
                'quantidade' => $quantidade_unica,
                'preco' => $produto_unico['preco']
            ];
            $total_pedido = $produto_unico['preco'] * $quantidade_unica;
            $id_vendedor_pedido = $produto_unico['id_vendedor'];
        } else {
            if (!empty($produtos_carrinho)) {
                foreach ($produtos_carrinho as $prod) {
                    $itens[] = [
                        'id_produto' => $prod['id_produto'],
                        'quantidade' => $prod['quantidade'],
                        'preco' => $prod['preco']
                    ];
                    $total_pedido += $prod['subtotal'];
                    $id_vendedor_pedido = null; 
                    if (isset($prod['id_vendedor'])) $id_vendedor_pedido = $prod['id_vendedor'];
                }
            }
        }

        
        if (!$is_assinatura && ($id_cliente || $id_vendedor)) {
            $sql_pedido = "INSERT INTO Pedido (id_cliente, id_vendedor, status) VALUES (" .
                ($id_cliente ? "'$id_cliente'" : "NULL") . "," .
                ($id_vendedor ? "'$id_vendedor'" : (isset($id_vendedor_pedido) ? "'$id_vendedor_pedido'" : "NULL")) . "," .
                "'$status_pedido')";
            if ($conexao->query($sql_pedido)) {
                $id_pedido = $conexao->insert_id;
                
                foreach ($itens as $item) {
                    $id_produto = intval($item['id_produto']);
                    $qtd = intval($item['quantidade']);
                    $preco = floatval($item['preco']);
                    $conexao->query("INSERT INTO Item_Pedido (id_pedido, id_produto, quantidade, preco) VALUES ('$id_pedido', '$id_produto', '$qtd', '$preco')");
                    
                    $conexao->query("UPDATE Produto SET estoque = estoque - $qtd WHERE id_produto = '$id_produto'");
                }
                
                $conexao->query("INSERT INTO Pagamento (id_pedido, forma_pagamento, status) VALUES ('$id_pedido', '$forma_pagamento', 'Confirmado')");
               
                if (!$id_produto_unico && isset($_SESSION['usuario_id'])) {
                    $id_cliente = $_SESSION['usuario_id'];
                    $conexao->query("DELETE FROM Item_Carrinho WHERE id_carrinho IN (SELECT id_carrinho FROM Carrinho WHERE id_cliente = '$id_cliente')");
                } else if (!$id_produto_unico && isset($_SESSION['vendedor_id'])) {
                    $id_vendedor = $_SESSION['vendedor_id'];
                    $conexao->query("DELETE FROM Item_Carrinho WHERE id_carrinho IN (SELECT id_carrinho FROM Carrinho WHERE id_vendedor = '$id_vendedor' AND id_cliente IS NULL)");
                }
            }
        }
        $_SESSION['pedido_registrado'] = true;
    }
}


$enderecos = [];
$endereco_selecionado = '';
if (isset($_SESSION['usuario_id'])) {
    $id_cliente = $_SESSION['usuario_id'];
    $sql_end = "SELECT id_endereco, rua, numero, complemento, bairro, cidade, estado, cep FROM Endereco WHERE id_cliente = '$id_cliente'";
    $res_end = $conexao->query($sql_end);
    if ($res_end && $res_end->num_rows > 0) {
        while ($row = $res_end->fetch_assoc()) {
            $enderecos[] = $row;
        }
        if (!empty($enderecos)) {
            $endereco_selecionado = $enderecos[0]['id_endereco'];
        }
    }
} else if (isset($_SESSION['vendedor_id'])) {
    $id_vendedor = $_SESSION['vendedor_id'];
    $sql_end = "SELECT id_endereco, rua, numero, complemento, bairro, cidade, estado, cep FROM Endereco WHERE id_vendedor = '$id_vendedor'";
    $res_end = $conexao->query($sql_end);
    if ($res_end && $res_end->num_rows > 0) {
        while ($row = $res_end->fetch_assoc()) {
            $enderecos[] = $row;
        }
        if (!empty($enderecos)) {
            $endereco_selecionado = $enderecos[0]['id_endereco'];
        }
    }
}

// Lógica do cupom
$cupom_aplicado = false;
$cupom_erro = '';
$desconto_cupom = 0;
$codigo_cupom = '';
if (isset($_GET['cupom']) && trim($_GET['cupom']) !== '') {
    $codigo_cupom = $conexao->real_escape_string(trim($_GET['cupom']));
    $sql_cupom = "SELECT * FROM Cupom WHERE codigo = '$codigo_cupom' AND ativo = 1";
    $res_cupom = $conexao->query($sql_cupom);
    if ($res_cupom && $res_cupom->num_rows > 0) {
        $cupom = $res_cupom->fetch_assoc();
        $desconto_cupom = floatval($cupom['desconto']);
        $cupom_aplicado = true;
        // Aplica desconto percentual
        $total = $total - ($total * ($desconto_cupom / 100));
        if ($total < 0) $total = 0;
    } else {
        $cupom_erro = 'Cupom inválido ou inativo.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pagamento</title>
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
    .valor-total {
      font-size: 1.3rem;
      color: #145c36;
      font-weight: bold;
      margin-bottom: 1.5rem;
    }
    .metodos {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }
    .metodo-btn {
      padding: 0.8rem 1.5rem;
      font-size: 1rem;
      border-radius: 8px;
      border: 2px solid #28a060;
      background: #fff;
      color: #28a060;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, color 0.2s, border 0.2s;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .metodo-btn.selected, .metodo-btn:hover {
      background: #28a060;
      color: #fff;
      border: 2px solid #1f804e;
    }
    .form-pagamento {
      margin-top: 1.5rem;
      text-align: left;
      display: none;
      animation: fadeIn 0.5s;
      padding: 10px 20px;
    }
    .form-pagamento.active {
      display: block;
    }
    label {
      font-weight: bold;
      color: #28a060;
      display: block;
      margin-top: 1.2rem;
      margin-bottom: 0.3rem;
    }
    input[type="text"], input[type="number"], input[type="month"] {
      width: 100%;
      padding: 10px 14px;
      border-radius: 10px;
      border: 2px solid #28a060;
      font-size: 1.08rem;
      margin-bottom: 10px;
      background: #f3f2e7;
      color: #222;
      transition: border 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px rgba(40,160,96,0.07);
      outline: none;
      margin-left: -15px;
    }
    select {
      width: 107%;
      padding: 10px 14px;
      border-radius: 10px;
      border: 2px solid #28a060;
      font-size: 1.08rem;
      margin-bottom: 10px;
      background: #f3f2e7;
      color: #222;
      margin-left: -15px;
    }
    .btn-pagar {
      width: 100%;
      margin-top: 1.5rem;
      font-size: 1.1rem;
      padding: 1rem 0;
      background: #28a060;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }
    .btn-pagar:hover {
      background: #1f804e;
      transform: scale(1.04);
    }
    .mensagem {
      color: #1f804e;
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
    }
    .pix-area, .boleto-area {
      text-align: center;
      margin-top: 1.5rem;
    }
    .pix-qrcode {
      width: 180px;
      height: 180px;
      margin: 0 auto 1rem auto;
      background: #fff;
      border: 2px solid #28a060;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: #28a060;
    }
    .boleto-btn {
      background: #145c36;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px 32px;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      margin-top: 1rem;
      transition: background 0.2s;
    }
    .boleto-btn:hover {
      background: #28a060;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px);}
      to { opacity: 1; transform: translateY(0);}
    }
    @media (max-width: 600px) {
      .container { padding: 1.2rem 0.5rem; }
      h1 { font-size: 1.3rem; }
      .metodo-btn { font-size: 0.95rem; padding: 0.7rem 1rem; }
    }
    .produto-unico-box, .produto-carrinho-box {
      display: flex;
      align-items: center;
      gap: 22px;
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 10px rgba(40,160,96,0.10);
      padding: 1.2rem 1.5rem;
      margin-bottom: 1.7rem;
      font-size: 1.08rem;
      justify-content: flex-start;
      border: 1.5px solid #d2e9df;
    }
    .produto-unico-img, .produto-carrinho-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
      border: 2px solid #e9f7ef;
      background: #f3f2e7;
      box-shadow: 0 2px 8px rgba(40,160,96,0.07);
    }
    .produto-unico-info, .produto-carrinho-info {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .produto-unico-nome, .produto-carrinho-nome {
      font-weight: bold;
      color: #1f804e;
      font-size: 2rem;
      margin-bottom: 10px;
      /* Remover white-space e overflow antigos */
      /* white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-left: -80%;
      display: block; */
      word-break: break-word;
      white-space: normal;
      display: block;
      max-width: 100%;
      line-height: 1.2;
      text-align: left;
    }
    .produto-unico-preco {
      color: #145c36;
      font-weight: bold;
      margin-bottom: 2px;
    }
    .produto-unico-qtd, .produto-carrinho-qtd {
      display: flex;
      align-items: center;
      gap: 22px;
      margin-top: 2px;
    }
    .produto-unico-qtd label, .produto-carrinho-qtd label {
      margin: 0;
      color: #145c36;
      font-weight: 500;
    }
    .produto-unico-qtd input[type="number"], .produto-carrinho-qtd input[type="number"] {
      width: 60px;
      padding: 6px 8px;
      border-radius: 6px;
      border: 1.5px solid #28a060;
      font-size: 1rem;
      background: #f3f2e7;
      color: #222;
      text-align: center;
    }
    @media (max-width: 600px) {
      .produto-unico-box, .produto-carrinho-box { flex-direction: column; align-items: flex-start; gap: 10px; padding: 1rem 0.5rem; }
      .produto-unico-img, .produto-carrinho-img { width: 60px; height: 60px; }
      .produto-unico-nome, .produto-carrinho-nome { max-width: 100%; font-size: 1rem; }
    }
    /* Endereço de entrega */
    .endereco-container {
      background: #fff;
      border-radius: 12px;
      padding: 1rem 3rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px rgba(40,160,96,0.07);
      text-align: left;
      max-width: 420px;
      margin-left: auto;
      margin-right: auto;
    }
    .endereco-container h3 {
      color: #1f804e;
      font-size: 1.15rem;
      margin-bottom: 0.7rem;
    }
    .endereco-container select {
      width: 100%;
      padding: 8px 10px;
      border-radius: 8px;
      border: 1.5px solid #28a060;
      margin-bottom: 10px;
    }
    .endereco-form-novo {
      display: flex;
      flex-direction: column;
      gap: 8px;
      max-width: 420px;
    }
    .endereco-form-novo label {
      font-weight: normal;
      color: #222;
      margin-bottom: 2px;
    }
    .endereco-form-novo input[type="text"] {
      width: 100%;
      padding: 8px 10px;
      border-radius: 8px;
      border: 1.5px solid #28a060;
      background: #f3f2e7;
      margin-top: 2px;
      margin-bottom: 2px;
      font-size: 1rem;
    }
    /* Fim endereço de entrega */
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <a href="loja.php"><img src="img/logo2.png" alt="Logo"></a>
      
    </div>
  </header>
  <main>
    <div class="container">
      <h1>Pagamento</h1>
      <?php if ($mensagem): ?>
        <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
        <?php if ($_POST['metodo'] === 'pix'): ?>
          <div class="pix-area">
            <div class="pix-qrcode">
              
              <span><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAdVBMVEX///8AAADb29vV1dWUlJSYmJh0dHQ7OzttbW24uLiCgoLl5eWzs7OioqK/v7/MzMz19fUrKyv5+flcXFxpaWns7OxhYWFzc3Pf399WVlYtLS0zMzMUFBSlpaUmJiY5OTmKiopDQ0N+fn4LCwvFxcUeHh5NTU1Pw2oiAAAF8klEQVR4nO2d7XLaMBBFCWDAYEgwEKB8U9L3f8TONNbetF62si0bCPf81FqyTpIZK9JKarUIIYQQ8kyk7QCk0pwSHClvXUm0ro6A9ksAOtKcEjwrb91LVIo6ITrSVt5Vv+FQeWtfojQsBg1LQ0Ma0tCbZzdcR91CRGvLcC+tRYkDoxvTsEJHbMNIiZpElmFfimIpS/wMK3TENuyWb9jTMJaysIZdGtLwGjTMoGEGDQ1uYziVouQODXfL3lWWY9Nw4rhI0UiKjq7d84ef4djqyK6C4fLFYG4amgyV5kzDudWRZQXDntXwoEHDgdWRHg1pSEMa0vBbGp6/g+GP6XU+krcci4cztGp+GZcq0JCGNKQhDWn4JIZJ6j70D2yoMFEaUXJsHtgwVhqhIQ1pSEMaPr5h2Fn9Coa1zeqP54OrzPF+0zB9dXRf8sQSNQ0jqyNYImp0dU0YWT/+L5iGntCQhlehIQ1p6A0NSzdsGi4aNPTNvlxHBZkphhLsjjN2GOX2dq7M3DMzK9oR3wzaCigzURspwjzNRMq+Q553hayvCtCwNDTMoGF1aFiaJzfUMmhPUla/YdoJANaSFMOFe6p9nGW8b/OGbyE6ou10DoxiCMycqEeBhjS8f2hIw/vniQynSvA2hr7rhxJ8VYLgLXVIBRmX/tQqyGP2TJQEf0iRlsVSZf3Q01AZNmnrh+UN8bdRxbDC75CGNKQhDWlY3VB5Q1jD4t/D1b7/yaa7GH2yQBrLIQv2Dy44Qoddzf4JkxKrUY5k0/+XEz7++FFLR7DBV16Klay99BLzP1PX7n6lGALUAPijU4LoyfuVX9AftPPaTnlDDbNdYGZX/8dQppjelCBSdmZKVPA8gUfDzPoCidkIDWlIQxrSsDHDNKzhJqhhle9hEmcgeDgOMyIXnJwkenZBjPxg2HEV4qGgdGQlb5VXHaW1rZRhODCcuF5q241sQwUkH0dKVIIYZ0ECe7kXLYOxPIYMYinCuPSivF4blxY21DLZgQzzkPUFQ8y1aWcL+xkW/9+ChjSkIQ1peANDbVcQkKD2PcR3u/z3MIzhMTbYnb3GNEOpMJaBCR7reY1pEvwgFMORe2yCwZ2v4cV6DHNt5rgUGbSec202iiGws6A1w4n1mKehlvVVl6GdyU5DGtKQhjSsx9D8HmKNCIbK+sL9GGprT+dDbrVIOPTksa5bVPqy9lTeUNaewNQ8vffilpw2O1mjsu8K8tytbo+8yxtqWdCe41J7NjHIiQM0pCENaUjDnCG/FtcNkUXSb7k80yQf9DW8SCNSpOXT+K6uBUGxQLZJ4RxhJPbYi0pNojhos4mehphiomFz0JCGf0PDW/CUhhW+hzCssP21rn3A2/dsz+8a0zkwXLsdwTOlS9g4jBmuV+v15kpWbXu5T8q7YGj2yRzvaviOS8Ma2nvXPFdIaUhDGtKQhk9t6JltEtgwyHltUrR3Z7OBHc5V6+aCYyT6wvDsXmVL+xqGvfG4MGvF0PdGAk/DsHdYFgYjVDMnioY0pCENafjUhr+UdmszrHCuvpyXb6+pAamANkZSpsz/hDEMcTdC4ZOSbQIbhrjfgoY0pCENaUjD/xsWPlf/UQyl6NDKX28s2OcIa9Mp+TZSjJpuY6jNtYHChgqN5kTRkIY0pCENaVjK8JDmP9L4Wh/yhu2f008+IvfYAlk0i3wj8TarsLVPb2nyTmcNpUuYa7tY7Q6Uqg9naM619ZSqNKQhDWlIw+YMK8zqhzDc+Rkulaq+hrtl7ypLJMRqhj1X1byO+WUuzU1yXHDoWXRxZYN8R3BAbNtVjbVk6brusLRnopRjWWzMLGice3M/t3RK1pd2WqGGmcl+j/eQ0pCGNKThdzVs8sbjsN9DX8N11C3El4uGYShR5APP4uSTGCm3Q3lOsRlIhV9Sdsy/H7MzncSh5R7XlQUN7L1rCvhj8tw2b3PXhuZJycX/t6gADWn4NzTMoCENC2EaYqfzWYkqYPho3jMzb/mRtgOAMZUSxC6ukV9rC78K5u4wQgghhDwFvwHocdPqfozHQwAAAABJRU5ErkJggg=="></span>
            </div>
            <div>Código Pix: <span style="font-family:monospace;">00020126360014BR.GOV.BCB.PIX0114+5585992933310520400005303986540<?= rand(10000,99999) ?></span></div>
            
          </div>
        <?php elseif ($_POST['metodo'] === 'boleto'): ?>
          <div class="boleto-area">
            <div style="font-family:monospace;font-size:1.1rem;margin-bottom:10px;">
              23793.38128 60007.135308 04000.793009 1 900000000<?= rand(100,999) ?>
            </div>
           
            
          </div>
        <?php endif; ?>
        <button class="btn-pagar" onclick="window.location.href='loja.php'">Voltar à Loja</button>
      <?php else: ?>
        <?php if ($cupom_aplicado): ?>
          <div class="mensagem" style="color:#145c36;">
            Cupom <b><?= htmlspecialchars($codigo_cupom) ?></b> aplicado! Desconto de <?= number_format($desconto_cupom,2,',','.') ?>%.
          </div>
        <?php elseif ($cupom_erro): ?>
          <div class="mensagem" style="color:#eb3b3b;">
            <?= htmlspecialchars($cupom_erro) ?>
          </div>
        <?php endif; ?>
        <div class="valor-total">Total: <span style="color:#1f804e;">R$ <?= number_format($total,2,',','.') ?></span></div>
        <?php if ($is_assinatura): ?>
          <div class="produto-unico-box">
            <img src="img/rs.png" class="produto-unico-img" alt="Assinatura">
            <div class="produto-unico-info">
              <span class="produto-unico-nome">Assinatura Premium</span>
              <div style="color:#145c36;font-weight:bold;">R$ <?= number_format($valor_assinatura,2,',','.') ?>/mês</div>
              <div style="color:#888;font-size:1em;">Acesso a todos os benefícios premium</div>
            </div>
          </div>
        <?php elseif ($produto_unico): ?>
          <div class="produto-unico-box">
            <img src="<?= htmlspecialchars($img_produto) ?>" class="produto-unico-img" alt="Produto">
            <div class="produto-unico-info">
              <span class="produto-unico-nome"><?= htmlspecialchars($produto_unico['nome']) ?></span>
              <form method="get" style="margin:0;display:inline;">
                <input type="hidden" name="id_produto" value="<?= $id_produto_unico ?>">
                <div class="produto-unico-qtd">
                  <label for="qtd">Qdt:</label>
                  <input type="number" id="qtd" name="quantidade" min="1" max="<?= (int)$produto_unico['estoque'] ?>" value="<?= $quantidade_unica ?>" onchange="this.form.submit()">
                  <span style="color:#888;font-size:0.97em;">(Estoque: <?= (int)$produto_unico['estoque'] ?>)</span>
                </div>
              </form>
            </div>
          </div>
        <?php elseif (!empty($produtos_carrinho)): ?>
          <?php foreach ($produtos_carrinho as $prod): ?>
            <div class="produto-carrinho-box">
              <img src="<?= htmlspecialchars($prod['img_produto']) ?>" class="produto-carrinho-img" alt="Produto">
              <div class="produto-carrinho-info">
                <span class="produto-carrinho-nome"><?= htmlspecialchars($prod['nome']) ?></span>
                <div class="produto-carrinho-qtd">
                  <label>Qdt:</label>
                  <input type="number" value="<?= (int)$prod['quantidade'] ?>" min="1" max="<?= (int)$prod['estoque'] ?>" disabled>
                  <span style="color:#888;font-size:0.97em;">(Estoque: <?= (int)$prod['estoque'] ?>)</span>
                </div>
                <div class="produto-carrinho-subtotal">Subtotal: R$ <?= number_format($prod['subtotal'],2,',','.') ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- Endereço de entrega -->
        <div class="endereco-container">
          <h3>Endereço de Entrega</h3>
          <?php if (isset($_SESSION['usuario_id']) || isset($_SESSION['vendedor_id'])): ?>
            <?php if (!empty($enderecos)): ?>
              <form method="post" id="form-endereco" style="margin-bottom:1rem;">
                <label for="endereco_select" style="font-weight:bold;color:#145c36;">Selecione um endereço:</label>
                <select name="endereco_selecionado" id="endereco_select">
                  <?php foreach ($enderecos as $end): ?>
                    <option value="<?= $end['id_endereco'] ?>" <?= ($end['id_endereco'] == $endereco_selecionado ? 'selected' : '') ?>>
                      <?= htmlspecialchars($end['rua']) ?>, <?= htmlspecialchars($end['numero']) ?><?= $end['complemento'] ? ' - '.htmlspecialchars($end['complemento']) : '' ?>, <?= htmlspecialchars($end['bairro']) ?>, <?= htmlspecialchars($end['cidade']) ?>/<?= htmlspecialchars($end['estado']) ?> - CEP: <?= htmlspecialchars($end['cep']) ?>
                    </option>
                  <?php endforeach; ?>
                  <option value="novo">Cadastrar novo endereço</option>
                </select>
              </form>
              <div id="novo-endereco-area" style="display:none;">
                <form method="post" id="form-novo-endereco" class="endereco-form-novo"
                  action="pagamento.php<?= 
                    (isset($_GET['id_produto']) || isset($_GET['quantidade']) || isset($_GET['assinatura'])) 
                    ? '?' . http_build_query(array_filter([
                        'id_produto' => $_GET['id_produto'] ?? null,
                        'quantidade' => $_GET['quantidade'] ?? null,
                        'assinatura' => $_GET['assinatura'] ?? null
                      ])) : '' ?>">
                  <label>Rua: <input type="text" name="rua" required></label>
                  <label>Número: <input type="text" name="numero" required></label>
                  <label>Complemento: <input type="text" name="complemento"></label>
                  <label>Bairro: <input type="text" name="bairro" required></label>
                  <label>Cidade: <input type="text" name="cidade" required></label>
                  <label>Estado: <input type="text" name="estado" maxlength="2" required></label>
                  <label>CEP: <input type="text" name="cep" maxlength="9" required></label>
                  <button type="submit" class="btn-pagar" style="margin-top:10px; margin-left:10px;">Salvar Endereço</button>
                </form>
              </div>
              <script>
                document.getElementById('endereco_select').addEventListener('change', function() {
                  if (this.value === 'novo') {
                    document.getElementById('novo-endereco-area').style.display = 'block';
                  } else {
                    document.getElementById('novo-endereco-area').style.display = 'none';
                  }
                });
              </script>
            <?php else: ?>
              <div id="novo-endereco-area">
                <form method="post" id="form-novo-endereco" class="endereco-form-novo"
                  action="pagamento.php<?= 
                    (isset($_GET['id_produto']) || isset($_GET['quantidade']) || isset($_GET['assinatura'])) 
                    ? '?' . http_build_query(array_filter([
                        'id_produto' => $_GET['id_produto'] ?? null,
                        'quantidade' => $_GET['quantidade'] ?? null,
                        'assinatura' => $_GET['assinatura'] ?? null
                      ])) : '' ?>">
                  <label>Rua: <input type="text" name="rua" required></label>
                  <label>Número: <input type="text" name="numero" required></label>
                  <label>Complemento: <input type="text" name="complemento"></label>
                  <label>Bairro: <input type="text" name="bairro" required></label>
                  <label>Cidade: <input type="text" name="cidade" required></label>
                  <label>Estado: <input type="text" name="estado" maxlength="2" required></label>
                  <label>CEP: <input type="text" name="cep" maxlength="9" required></label>
                  <button type="submit" class="btn-pagar" style="margin-top:10px;">Salvar Endereço</button>
                </form>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <div style="color:#888;">Faça login para informar o endereço de entrega.</div>
          <?php endif; ?>

          <!-- Campo de cupom de desconto abaixo do endereço -->
          <form method="get" style="margin: 1.2rem 0 0 0; display: flex; gap: 10px; justify-content: center; align-items: center;">
            <?php
              // Preservar parâmetros existentes na URL
              if (isset($_GET['id_produto'])) echo '<input type="hidden" name="id_produto" value="'.htmlspecialchars($_GET['id_produto']).'">';
              if (isset($_GET['quantidade'])) echo '<input type="hidden" name="quantidade" value="'.htmlspecialchars($_GET['quantidade']).'">';
              if (isset($_GET['assinatura'])) echo '<input type="hidden" name="assinatura" value="'.htmlspecialchars($_GET['assinatura']).'">';
            ?>
            <input type="text" name="cupom" placeholder="Insira o cupom" style="padding:8px 12px; border-radius:8px; border:1.5px solid #28a060; font-size:1rem; background:#f3f2e7; width: 60%;" value="<?= isset($_GET['cupom']) ? htmlspecialchars($_GET['cupom']) : '' ?>">
            <button type="submit" style="background:#28a060; color:#fff; border:none; border-radius:8px; padding:8px 18px; font-weight:bold; cursor:pointer;">Aplicar</button>
          </form>
          <!-- Fim campo cupom -->
        </div>
        <!-- Fim endereço de entrega -->

        <div class="metodos">
          <button type="button" class="metodo-btn selected" id="btn-cartao" onclick="selecionarMetodo('cartao')">Cartão de Crédito</button>
          <button type="button" class="metodo-btn" id="btn-pix" onclick="selecionarMetodo('pix')">Pix</button>
          <button type="button" class="metodo-btn" id="btn-boleto" onclick="selecionarMetodo('boleto')">Boleto</button>
        </div>
        <form method="post" id="form-cartao" class="form-pagamento active" autocomplete="off">
          <input type="hidden" name="metodo" value="cartao">
          <label for="nome_cartao">Nome impresso no cartão</label>
          <input type="text" id="nome_cartao" name="nome_cartao" required>
          <label for="numero_cartao">Número do cartão</label>
          <input type="text" id="numero_cartao" name="numero_cartao" maxlength="19" required oninput="formatarCartao(this)">
          <label for="validade">Validade</label>
          <input type="text" id="validade" name="validade" maxlength="5" required placeholder="MM/AA" oninput="formatarValidade(this)">
          <label for="cvv">CVV</label>
          <input type="text" id="cvv" name="cvv" maxlength="4" required>
          <label for="parcelas">Parcelas</label>
          <select id="parcelas" name="parcelas" required>
            <?php for ($i=1; $i<=12; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?>x R$ <?= number_format($total/$i,2,',','.') ?></option>
            <?php endfor; ?>
          </select>
          <button type="submit" class="btn-pagar">Confirmar Pagamento</button>
        </form>
        <form method="post" id="form-pix" class="form-pagamento" autocomplete="off">
          <input type="hidden" name="metodo" value="pix">
          <div style="text-align:center;margin:2rem 0;">
            <img src="img/pix.png" alt="Pix" style="height:60px;margin-bottom:10px;">
            <div style="font-size:1.1rem;color:#1f804e;">Pagamento instantâneo via Pix</div>
          </div>
          <button type="submit" class="btn-pagar">Gerar Pix</button>
        </form>
        <form method="post" id="form-boleto" class="form-pagamento" autocomplete="off">
          <input type="hidden" name="metodo" value="boleto">
          <div style="text-align:center;margin:2rem 0;">
            <img src="img/boleto.png" alt="Boleto" style="height:60px;margin-bottom:10px;">
            <div style="font-size:1.1rem;color:#1f804e;">Gerar boleto bancário para pagamento</div>
          </div>
          <button type="submit" class="btn-pagar">Gerar Boleto</button>
        </form>
        <button class="btn-pagar" style="background:#eb3b3b;margin-top:1.5rem;" type="button" id="btn-voltar">Cancelar Compra</button>
      <?php endif; ?>
    </div>
  </main>
  <script>
    function selecionarMetodo(metodo) {
      document.querySelectorAll('.metodo-btn').forEach(btn => btn.classList.remove('selected'));
      document.getElementById('btn-' + metodo).classList.add('selected');
      document.querySelectorAll('.form-pagamento').forEach(f => f.classList.remove('active'));
      document.getElementById('form-' + metodo).classList.add('active');
    }
    function formatarCartao(input) {
      let v = input.value.replace(/\D/g,'').slice(0,16);
      v = v.replace(/(\d{4})(?=\d)/g, '$1 ');
      input.value = v;
    }
    function formatarValidade(input) {
      let v = input.value.replace(/\D/g,'');
      if (v.length > 4) v = v.slice(0,4);
      if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
      input.value = v;
    }
    // Confirmação ao clicar em Voltar
    document.addEventListener('DOMContentLoaded', function() {
      var btnVoltar = document.getElementById('btn-voltar');
      if (btnVoltar) {
        btnVoltar.onclick = function() {
          if (confirm('Você realmente deseja cancelar?')) {
            window.location.href = 'loja.php';
          }
        }
      }
    });
  </script>
</body>
</html>