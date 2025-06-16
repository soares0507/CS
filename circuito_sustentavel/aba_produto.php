<?php
session_start();
include 'conexao.php'; // Assume que $conexao é configurado aqui

// --- INÍCIO DA LÓGICA PHP PARA O HEADER (adaptado de loja.php) ---
$usuario_logado = false;
$usuario_nome = ''; // Pode não ser usado diretamente no header, mas bom ter
$link_perfil = 'login.php'; // Link padrão

if (isset($_SESSION['usuario_id'])) {
    $usuario_logado = true;
    $id_cliente_session = $_SESSION['usuario_id']; // Renomeado para evitar conflito com $id_cliente da página
    $sql_user_header = "SELECT nome FROM Cliente WHERE id_cliente = ?";
    $stmt_user_header = $conexao->prepare($sql_user_header);
    if($stmt_user_header){
        $stmt_user_header->bind_param("i", $id_cliente_session);
        $stmt_user_header->execute();
        $resultado_user_header = $stmt_user_header->get_result();
        if ($resultado_user_header->num_rows > 0) {
            $usuario_header = $resultado_user_header->fetch_assoc();
            $usuario_nome = $usuario_header['nome']; // Define para uso geral se necessário
        }
        $stmt_user_header->close();
    }
    $link_perfil = 'usuario.php';
} elseif (isset($_SESSION['vendedor_id'])) {
    $usuario_logado = true;
    $id_vendedor_session = $_SESSION['vendedor_id']; // Renomeado
    $sql_vend_header = "SELECT nome FROM Vendedor WHERE id_vendedor = ?";
    $stmt_vend_header = $conexao->prepare($sql_vend_header);
    if($stmt_vend_header){
        $stmt_vend_header->bind_param("i", $id_vendedor_session);
        $stmt_vend_header->execute();
        $resultado_vend_header = $stmt_vend_header->get_result();
        if ($resultado_vend_header->num_rows > 0) {
            $vendedor_header = $resultado_vend_header->fetch_assoc();
            $usuario_nome = $vendedor_header['nome'];
        }
        $stmt_vend_header->close();
    }
    $link_perfil = 'vendedor.php';
}

$imagem_carrinho = 'img/carrinho_sem.png'; 
if ($usuario_logado) {
    $id_entidade_carrinho = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : (isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null);
    $coluna_entidade = isset($_SESSION['usuario_id']) ? 'id_cliente' : (isset($_SESSION['vendedor_id']) ? 'id_vendedor' : null);

    if ($id_entidade_carrinho && $coluna_entidade) {
        $sql_carrinho_check = "SELECT COUNT(ic.id_produto) as total_itens 
                           FROM Carrinho c 
                           LEFT JOIN Item_Carrinho ic ON c.id_carrinho = ic.id_carrinho 
                           WHERE c.$coluna_entidade = ?";
        $stmt_carrinho_header = $conexao->prepare($sql_carrinho_check);
        if ($stmt_carrinho_header) {
            $stmt_carrinho_header->bind_param("i", $id_entidade_carrinho);
            $stmt_carrinho_header->execute();
            $resultado_carrinho_header = $stmt_carrinho_header->get_result();
            if ($resultado_carrinho_header && $resultado_carrinho_header->num_rows > 0) {
                 $dados_carrinho_header = $resultado_carrinho_header->fetch_assoc();
                 if ($dados_carrinho_header['total_itens'] > 0) {
                    $imagem_carrinho = 'img/carrinho.png';
                 }
            }
            $stmt_carrinho_header->close();
        }
    }
}
$link_carrinho_header = 'carrinho.php'; 
if(isset($_SESSION['vendedor_id'])) {
    $link_carrinho_header = 'carrinho_vendedor.php'; 
}
// --- FIM DA LÓGICA PHP PARA O HEADER ---


$id_produto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_produto <= 0) {
    header('Location: loja.php');
    exit;
}

// --- SUBSTITUIR LÓGICA AJAX ADD CARRINHO PELO CÓDIGO ANTIGO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_add_carrinho'])) {
    // Verifica se o usuário (cliente ou vendedor) está logado
    if (isset($_SESSION['usuario_id'])) {
        $id_cliente = $_SESSION['usuario_id'];
        $is_vendedor = false;
    } elseif (isset($_SESSION['vendedor_id'])) {
        $id_vendedor_logado = $_SESSION['vendedor_id'];
        $is_vendedor = true;
    } else {
        exit;
    }

    $id_produto_post = isset($_POST['id_produto']) ? intval($_POST['id_produto']) : 0;
    $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 1;

    if ($id_produto_post <= 0 || $quantidade <= 0) {
        exit;
    }

    $sql_prod = "SELECT id_vendedor FROM Produto WHERE id_produto = '$id_produto_post'";
    $res_prod = $conexao->query($sql_prod);

    if ($res_prod && $res_prod->num_rows > 0) {
        $row_prod = $res_prod->fetch_assoc();
        $id_vendedor_produto = $row_prod['id_vendedor'];

        // --- BLOQUEIA O VENDEDOR DE COMPRAR O PRÓPRIO PRODUTO ---
        if ($is_vendedor && $id_vendedor_logado == $id_vendedor_produto) {
            http_response_code(403);
            exit;
        }

        $id_carrinho = null;

        if (!$is_vendedor) {
            $sql_carrinho = "SELECT id_carrinho FROM Carrinho WHERE id_cliente = '$id_cliente' AND id_vendedor = '$id_vendedor_produto' LIMIT 1";
            $res_carrinho = $conexao->query($sql_carrinho);

            if ($res_carrinho && $res_carrinho->num_rows > 0) {
                $id_carrinho = $res_carrinho->fetch_assoc()['id_carrinho'];
            } else {
                $conexao->query("INSERT INTO Carrinho (id_cliente, id_vendedor) VALUES ('$id_cliente', '$id_vendedor_produto')");
                $id_carrinho = $conexao->insert_id;
            }
        } else {
            $sql_carrinho = "SELECT id_carrinho FROM Carrinho WHERE id_cliente IS NULL AND id_vendedor = '$id_vendedor_logado' LIMIT 1";
            $res_carrinho = $conexao->query($sql_carrinho);
            if ($res_carrinho && $res_carrinho->num_rows > 0) {
                $id_carrinho = $res_carrinho->fetch_assoc()['id_carrinho'];
            } else {
                $conexao->query("INSERT INTO Carrinho (id_cliente, id_vendedor) VALUES (NULL, '$id_vendedor_logado')");
                $id_carrinho = $conexao->insert_id;
            }
        }

        if ($id_carrinho) {
            $sql_item = "SELECT quantidade FROM Item_Carrinho WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto_post'";
            $res_item = $conexao->query($sql_item);

            if ($res_item && $res_item->num_rows > 0) {
                $item_existente = $res_item->fetch_assoc();
                $nova_qtd = $item_existente['quantidade'] + $quantidade;
                $conexao->query("UPDATE Item_Carrinho SET quantidade = '$nova_qtd' WHERE id_carrinho = '$id_carrinho' AND id_produto = '$id_produto_post'");
            } else {
                $conexao->query("INSERT INTO Item_Carrinho (id_carrinho, id_produto, quantidade) VALUES ('$id_carrinho', '$id_produto_post', '$quantidade')");
            }
            http_response_code(200);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(404);
    }
    exit;
}


// Lógica para Perguntas e Respostas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pergunta']) && (isset($_SESSION['usuario_id']) || isset($_SESSION['vendedor_id']))) {
    $texto_pergunta = trim($_POST['pergunta']);
    // Permite que tanto cliente quanto vendedor faça pergunta
    $id_cliente_pergunta = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $id_vendedor_pergunta = isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null;
    if ($texto_pergunta !== '') {
        if ($id_cliente_pergunta) {
            $sql_insert_pergunta = "INSERT INTO Pergunta (id_cliente, id_produto, texto) VALUES (?, ?, ?)";
            $stmt_insert_pergunta = $conexao->prepare($sql_insert_pergunta);
            $stmt_insert_pergunta->bind_param("iis", $id_cliente_pergunta, $id_produto, $texto_pergunta);
            $stmt_insert_pergunta->execute();
            $stmt_insert_pergunta->close();
        } else if ($id_vendedor_pergunta) {
            $sql_insert_pergunta = "INSERT INTO Pergunta (id_vendedor, id_produto, texto) VALUES (?, ?, ?)";
            $stmt_insert_pergunta = $conexao->prepare($sql_insert_pergunta);
            $stmt_insert_pergunta->bind_param("iis", $id_vendedor_pergunta, $id_produto, $texto_pergunta);
            $stmt_insert_pergunta->execute();
            $stmt_insert_pergunta->close();
        }
        // Para evitar reenvio do formulário ao atualizar, redireciona ou limpa o POST
        header("Location: aba_produto.php?id=$id_produto&pergunta_enviada=1#secao-perguntas");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resposta'], $_POST['id_pergunta']) && isset($_SESSION['vendedor_id'])) {
    $id_vendedor_resposta = $_SESSION['vendedor_id'];
    
    $sql_check_produto_vendedor = "SELECT id_vendedor FROM Produto WHERE id_produto = ?";
    $stmt_check_produto_vendedor = $conexao->prepare($sql_check_produto_vendedor);
    $stmt_check_produto_vendedor->bind_param("i", $id_produto);
    $stmt_check_produto_vendedor->execute();
    $res_check_produto_vendedor = $stmt_check_produto_vendedor->get_result();

    if ($res_check_produto_vendedor && $res_check_produto_vendedor_row = $res_check_produto_vendedor->fetch_assoc()) {
        if ($res_check_produto_vendedor_row['id_vendedor'] == $id_vendedor_resposta) { // Verifica se o vendedor logado é o dono do produto
            $texto_resposta = trim($_POST['resposta']);
            $id_pergunta_resposta = intval($_POST['id_pergunta']);
            if ($texto_resposta !== '') {
                $sql_insert_resposta = "INSERT INTO Resposta (id_pergunta, id_vendedor, texto) VALUES (?, ?, ?)";
                $stmt_insert_resposta = $conexao->prepare($sql_insert_resposta);
                $stmt_insert_resposta->bind_param("iis", $id_pergunta_resposta, $id_vendedor_resposta, $texto_resposta);
                $stmt_insert_resposta->execute();
                $stmt_insert_resposta->close();
                header("Location: aba_produto.php?id=$id_produto&resposta_enviada=1#pergunta-$id_pergunta_resposta");
                exit;
            }
        }
    }
    $stmt_check_produto_vendedor->close();
}

// Busca dados do produto
$sql_produto_detalhe = "SELECT p.*, v.nome as nome_vendedor FROM Produto p JOIN Vendedor v ON p.id_vendedor = v.id_vendedor WHERE p.id_produto = ?";
$stmt_produto_detalhe = $conexao->prepare($sql_produto_detalhe);
$stmt_produto_detalhe->bind_param("i", $id_produto);
$stmt_produto_detalhe->execute();
$res_produto_detalhe = $stmt_produto_detalhe->get_result();

if (!$res_produto_detalhe || $res_produto_detalhe->num_rows == 0) {
    header('Location: loja.php');
    exit;
}
$produto = $res_produto_detalhe->fetch_assoc();
$stmt_produto_detalhe->close();

// --- BLOQUEIA O VENDEDOR DE COMPRAR O PRÓPRIO PRODUTO (FRONTEND) ---
$bloqueia_vendedor_compra = false;
if (isset($_SESSION['vendedor_id']) && $_SESSION['vendedor_id'] == $produto['id_vendedor']) {
    $bloqueia_vendedor_compra = true;
}

$imagens = [];
if (!empty($produto['imagens'])) {
    $imagens_json = json_decode($produto['imagens'], true);
    if (is_array($imagens_json)) {
        foreach($imagens_json as $img_path_raw) {
            $img_trim = trim($img_path_raw);
            // Corrige para garantir que o caminho seja relativo à pasta uploads_produtos
            if (strpos($img_trim, 'uploads_produtos/') === 0) {
                $imagens[] = $img_trim;
            } elseif (file_exists('uploads_produtos/' . $img_trim)) {
                $imagens[] = 'uploads_produtos/' . $img_trim;
            } else {
                $imagens[] = $img_trim;
            }
        }
    } elseif (!empty($produto['imagens'])) { // Fallback para string separada por vírgula
        $temp_imagens = explode(',', $produto['imagens']);
        foreach($temp_imagens as $img_path_raw) {
            $img_trim = trim($img_path_raw);
            if (strpos($img_trim, 'uploads_produtos/') === 0) {
                $imagens[] = $img_trim;
            } elseif (file_exists('uploads_produtos/' . $img_trim)) {
                $imagens[] = 'uploads_produtos/' . $img_trim;
            } else {
                $imagens[] = $img_trim;
            }
        }
    }
}
$img_principal = !empty($imagens[0]) ? htmlspecialchars($imagens[0]) : 'img/sem-imagem.png';


// Busca perguntas e respostas
$sql_perg = "SELECT pe.id_pergunta, pe.texto, 
                CASE 
                    WHEN pe.id_cliente IS NOT NULL THEN c.nome
                    WHEN pe.id_vendedor IS NOT NULL THEN v.nome
                    ELSE 'Usuário'
                END as nome_cliente, 
                pe.data 
             FROM Pergunta pe 
             LEFT JOIN Cliente c ON pe.id_cliente = c.id_cliente 
             LEFT JOIN Vendedor v ON pe.id_vendedor = v.id_vendedor
             WHERE pe.id_produto = ? ORDER BY pe.data DESC";
$stmt_perg = $conexao->prepare($sql_perg);
$stmt_perg->bind_param("i", $id_produto);
$stmt_perg->execute();
$res_perg = $stmt_perg->get_result();
$perguntas = $res_perg ? $res_perg->fetch_all(MYSQLI_ASSOC) : [];
$stmt_perg->close();

$respostas = [];
if (!empty($perguntas)) {
    $ids_perguntas = array_map('intval', array_column($perguntas, 'id_pergunta'));
    if (!empty($ids_perguntas)) {
        $placeholders = implode(',', array_fill(0, count($ids_perguntas), '?'));
        $tipos = str_repeat('i', count($ids_perguntas));

        $sql_resp = "
            SELECT r.id_pergunta, r.texto, r.data, v.nome as nome_vendedor_resposta
            FROM Resposta r
            JOIN Vendedor v ON r.id_vendedor = v.id_vendedor
            WHERE r.id_pergunta IN ($placeholders)
            ORDER BY r.data DESC 
        "; 
        // Nota: A lógica original para pegar apenas a ÚLTIMA resposta foi simplificada aqui.
        // Para pegar apenas a última, seria necessário um subselect ou lógica PHP adicional.
        // Por simplicidade, pegamos todas as respostas e o PHP pode decidir qual mostrar se houver múltiplas.
        // Para pegar apenas a mais recente:
        // SELECT r1.*, v.nome as nome_vendedor_resposta FROM Resposta r1 
        // JOIN Vendedor v ON r1.id_vendedor = v.id_vendedor
        // INNER JOIN (SELECT id_pergunta, MAX(data) as max_data FROM Resposta WHERE id_pergunta IN ($placeholders) GROUP BY id_pergunta) r2 
        // ON r1.id_pergunta = r2.id_pergunta AND r1.data = r2.max_data

        $stmt_resp = $conexao->prepare($sql_resp);
        $stmt_resp->bind_param($tipos, ...$ids_perguntas);
        $stmt_resp->execute();
        $res_resp = $stmt_resp->get_result();
        if ($res_resp) {
            while ($row = $res_resp->fetch_assoc()) {
                // Se múltiplas respostas são possíveis por pergunta, agrupe-as
                // Se só uma (a mais recente) é esperada, pode sobrescrever
                if (!isset($respostas[$row['id_pergunta']])) { // Pega a primeira (mais recente devido ao ORDER BY)
                     $respostas[$row['id_pergunta']] = $row;
                }
            }
        }
        $stmt_resp->close();
    }
}

$pergunta_para_responder = (isset($_SESSION['vendedor_id'], $_GET['responder']) && is_numeric($_GET['responder'])) ? intval($_GET['responder']) : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($produto['nome']) ?> - Circuito Sustentável</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <style>
    :root {
        --verde: #28a060;
        --verde-escuro: #1e7c4b;
        --verde-claro-fundo: #f0f9f4;
        --cinza-claro: #f4f6f8; 
        --cinza-texto: #5f6c7b;
        --cinza-escuro: #2c3e50;
        --branco: #ffffff;
        --vermelho-erro: #d9534f;
        --amarelo-compra: #FFD814;
        --amarelo-compra-hover: #F7CA00;
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --font-principal: 'Poppins', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: var(--font-principal);
        line-height: 1.6;
        color: var(--cinza-texto);
        background-color: var(--cinza-claro);
        overflow-x: hidden;
    }
    
    main {
        padding-top: 100px;
        padding-bottom: 40px;
    }

    .container-page {
        width: 90%;
        max-width: 1140px; 
        margin: 20px auto;
    }
    
    .site-header {
        position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
        padding: 15px 0;
        background-color: #fff;
        border-bottom: 1px solid #eee;
    }
    .header-container {
        width: 90%; max-width: 1200px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between;
    }
    .site-header .logo { height: 45px; }

    .header-center {
        display: flex; align-items: center; gap: 20px;
        flex-grow: 1; justify-content: flex-start; 
        margin-left: 25px; 
    }

    .header-actions { display: flex; align-items: center; gap: 15px; }
    .header-actions a { display: flex; align-items: center; padding: 5px; }
    .header-actions img.action-icon {
        height: 28px; width: auto;
    }
    .auth-buttons-header .btn { padding: 7px 14px; font-size: 0.85em; margin-left:5px;}
    .auth-buttons-header .btn-outline { border: 1px solid var(--verde-escuro); color: var(--verde-escuro); background: none;}
    .auth-buttons-header .btn-outline:hover { background-color: var(--verde-escuro); color: var(--branco); }

    .product-detail-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        background-color: var(--branco);
        padding: 30px;
        border-radius: var(--border-radius-lg);
        border: 1px solid #eee;
    }
    .product-images {
        flex: 1 1 450px;
        display: flex;
        gap: 15px;
    }
    .thumbnails {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 450px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .thumbnail-img {
        width: 70px; height: 70px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid var(--cinza-claro);
        border-radius: var(--border-radius-sm);
    }
    .thumbnail-img.active {
        border-color: var(--verde);
    }
    .main-image-container {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .main-img {
        max-width: 100%;
        max-height: 450px;
        object-fit: contain;
        border-radius: var(--border-radius-md);
        border: 1px solid var(--cinza-claro);
    }

    .product-info-column {
        flex: 1 1 400px;
        display: flex;
        flex-direction: column;
    }
    .product-title {
        font-size: 1.8em;
        font-weight: 600;
        color: var(--cinza-escuro);
        margin-bottom: 10px;
        line-height: 1.3;
    }
    .product-vendor {
        font-size: 0.9em;
        color: var(--cinza-texto);
        margin-bottom: 15px;
    }
    .product-vendor a {
        color: var(--verde);
        text-decoration: none;
        font-weight: 500;
    }

    .product-price {
        color: var(--verde-escuro);
        font-size: 2em;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .product-stock {
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 0.95em;
    }
    .product-stock.in-stock { color: var(--verde); }
    .product-stock.out-of-stock { color: var(--vermelho-erro); }

    .buy-box {
        border: 1px solid var(--cinza-claro);
        padding: 20px;
        border-radius: var(--border-radius-md);
        background-color: var(--verde-claro-fundo);
        margin-top: 15px;
    }
    .buy-box label {
        font-size: 0.9em;
        font-weight: 500;
        margin-bottom: 5px;
    }
    .buy-box input[type="number"] {
        width: 80px;
        padding: 8px 10px;
        margin-bottom: 15px;
        text-align: center;
    }
    .buy-box .btn {
        width: 100%;
        padding: 12px;
        font-size: 1em;
        margin-bottom: 10px;
        border: none;
        border-radius: var(--border-radius-sm);
    }
    .buy-box .btn:last-child { margin-bottom: 0; }
    .btn-add-cart {
        background-color: var(--amarelo-compra);
        color: var(--cinza-escuro);
    }
    .btn-add-cart:hover {
        background-color: var(--amarelo-compra-hover);
    }
    /* NOVO: Botão Comprar Agora verde */
    .btn-comprar-agora {
        background-color: var(--verde);
        color: #fff;
    }
    .btn-comprar-agora:hover {
        background-color: var(--verde-escuro);
        color: #fff;
    }
    #msg-carrinho {
        font-size: 0.9em;
        padding: 10px;
        border-radius: var(--border-radius-sm);
        text-align: center;
    }
    #msg-carrinho.alert-success { background-color: #d4edda; color: #155724;}
    #msg-carrinho.alert-danger { background-color: #f8d7da; color: #721c24;}

    .product-description-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--cinza-claro);
    }
    .product-description-section h5 {
        font-size: 1.2em;
        color: var(--cinza-escuro);
        font-weight: 600;
        margin-bottom: 10px;
    }
    .product-description-section p {
        font-size: 0.95em;
        line-height: 1.8;
    }

    .qna-section {
        margin-top: 40px;
        padding: 30px;
        background-color: var(--branco);
        border-radius: var(--border-radius-lg);
        border: 1px solid #eee;
    }
    .qna-section h4 {
        font-size: 1.5em;
        color: var(--cinza-escuro);
        font-weight: 600;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--verde-claro-fundo);
        padding-bottom: 10px;
    }
    .qna-item {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px dashed var(--cinza-claro);
    }
    .qna-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0;}
    .qna-question strong { color: var(--cinza-escuro); font-weight: 600; }
    .qna-question small.text-muted { font-size: 0.8em; color: #999; margin-left: 5px;}
    .qna-answer {
        margin-left: 20px;
        margin-top: 8px;
        padding: 10px;
        background-color: var(--verde-claro-fundo);
        border-radius: var(--border-radius-sm);
        font-size: 0.9em;
    }
    .qna-answer strong { color: var(--verde-escuro); }
    .btn-responder {
        background-color: var(--verde-claro-fundo);
        color: var(--verde-escuro);
        border: 1px solid var(--verde-claro-fundo);
        padding: 6px 12px;
        font-size: 0.85em;
        border-radius: var(--border-radius-sm);
        margin-left: 10px;
        cursor: pointer;
    }
    .form-resposta-vendedor { margin-top: 10px; }
    .form-resposta-vendedor textarea, 
    #form-pergunta textarea {
        width: 100%; padding: 10px; border-radius: var(--border-radius-md);
        border: 1px solid #ccd0d5; font-size: 0.9em; margin-bottom:10px;
        font-family: var(--font-principal); min-height: 70px;
    }
    .form-resposta-vendedor .btn,
    #form-pergunta .btn {
        padding: 8px 18px;
        font-size: 0.9em;
        border: none;
        border-radius: var(--border-radius-sm);
        background: var(--verde);
        color: #fff;
    }

    .site-footer-bottom {
        background-color: var(--cinza-escuro); color: #b0bec5;
        padding: 70px 0 40px; font-size: 0.95em;
    }
    .footer-content-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 45px; margin-bottom: 50px;
        width: 90%; max-width: 1140px; margin: 0 auto 30px auto;
    }
    .footer-col h4 { font-size: 1.25em; color: var(--branco); font-weight: 600; margin-bottom: 20px; }
    .footer-col p, .footer-col a { color: #b0bec5; text-decoration: none; margin-bottom: 10px; display: block; }
    .footer-copyright { text-align: center; padding-top: 40px; border-top: 1px solid #4a5c6a; color: #78909c; width: 90%; max-width: 1140px; margin: 0 auto; }

    @media (max-width: 992px) {
        .header-center { flex-direction: column; align-items: stretch; gap: 10px; margin-left: 15px; margin-right: 15px;}
        .site-header { padding-bottom: 10px; }
        main { padding-top: 170px; }
        .product-detail-container { flex-direction: column; }
        .product-images, .product-info_column { flex: 1 1 100%; }
    }
    @media (max-width: 768px) {
        .header-container { flex-wrap: wrap; justify-content: space-between; }
        .site-header .logo { margin-bottom: 0; }
        .header-center { width:100%; order: 3; margin-left:0; margin-right:0; padding-top: 10px; }
        .header-actions { order: 2; }
        main { padding-top: 190px; }
        .product-images { flex-direction: column-reverse; }
        .thumbnails { flex-direction: row; overflow-x: auto; max-height: none; padding-bottom:5px; padding-right:0; }
        .thumbnail-img { width: 60px; height: 60px; }
        .product-title { font-size: 1.5em; }
        .product-price { font-size: 1.6em; }
    }
    @media (max-width: 480px) {
        .header-actions { gap: 10px; }
        .header-actions img.action-icon { height: 24px; }
        .auth-buttons-header .btn { padding: 6px 10px; font-size: 0.8em; }
        main { padding-top: 180px; }
        .buy-box .btn { font-size: 0.9em; }
    }
  </style>
</head>
<body>

  <header class="site-header">
    <div class="header-container">
      <a href="loja.php"> <img src="img/logo2.png" alt="Circuito Sustentável Logo" class="logo" />
      </a>

      <div class="header-center">

      </div>

      <div class="header-actions">
        <?php if ($usuario_logado): ?>
            <a href="<?= htmlspecialchars($link_perfil) ?>" aria-label="Meu Perfil">
                <img src="img/user.png" alt="Meu Perfil" class="action-icon" />
            </a>
        <?php else: ?>
            <div class="auth-buttons-header">
            <button class="btn btn-outline btn-sm" onclick="location.href='login.php'">Entrar</button>
            <button class="btn btn-primary btn-sm" onclick="location.href='cadastro.php'">Registrar</button>
            </div>
        <?php endif; ?>
        <a href="rs.php" aria-label="C+ Moedas">
            <img src="img/rs.png" alt="C+ Moedas" class="action-icon">
        </a>
        <a href="<?= htmlspecialchars($link_carrinho_header) ?>" aria-label="Carrinho de Compras">
            <img src="<?= htmlspecialchars($imagem_carrinho) ?>" alt="Carrinho" class="action-icon">
        </a>
      </div>
    </div>
  </header>

  <main>
    <div class="container-page">
        <div class="product-detail-container">
            <div class="product-images">
                <div class="thumbnails">
                    <?php if (!empty($imagens)): ?>
                        <?php foreach ($imagens as $i => $img_thumb_path): ?>
                            <img src="<?= htmlspecialchars($img_thumb_path) ?>" 
                                 class="thumbnail-img <?= ($i == 0) ? 'active' : '' ?>" 
                                 alt="Thumbnail <?= $i + 1 ?> do produto <?= htmlspecialchars($produto['nome']) ?>"
                                 onclick="document.getElementById('mainImage').src=this.src; document.querySelectorAll('.thumbnail-img').forEach(t => t.classList.remove('active')); this.classList.add('active');">
                        <?php endforeach; ?>
                    <?php else: ?>
                         <img src="img/sem-imagem.png" class="thumbnail-img active" alt="Imagem indisponível">
                    <?php endif; ?>
                </div>
                <div class="main-image-container">
                    <img id="mainImage" src="<?= $img_principal ?>" alt="Imagem principal do produto <?= htmlspecialchars($produto['nome']) ?>" class="main-img">
                </div>
            </div>

            <div class="product-info-column">
                <h1 class="product-title"><?= htmlspecialchars($produto['nome']) ?></h1>
                <p class="product-vendor">Vendido e entregue por: <a href="#"><?= htmlspecialchars($produto['nome_vendedor']) ?></a></p>
                <hr style="margin: 15px 0; border-color: var(--cinza-claro);">
                <div class="product-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                <div class="product-stock <?= $produto['estoque'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                    <?= $produto['estoque'] > 0 ? ($produto['estoque'] < 10 ? "Restam apenas {$produto['estoque']} unidades!" : 'Em estoque') : 'Produto indisponível' ?>
                </div>
                
                <div class="buy-box">
                    <?php if ($bloqueia_vendedor_compra): ?>
                        <div id="msg-carrinho" class="alert alert-danger" style="display:block;">
                            Você não pode comprar seu próprio produto.
                        </div>
                    <?php endif; ?>
                    <form method="post" id="form-carrinho">
                        <input type="hidden" name="id_produto" value="<?= $id_produto ?>">
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" class="form-control form-control-sm" style="width:80px; display:inline-block; margin-right:10px;" 
                               id="quantidade" name="quantidade" min="1" 
                               max="<?= $produto['estoque'] > 0 ? $produto['estoque'] : '1' ?>" value="1" required 
                               <?= $produto['estoque'] == 0 || $bloqueia_vendedor_compra ? 'disabled' : '' ?>>
                        <br><br>
                        <button type="submit" class="btn btn-add-cart" name="add_carrinho" <?= $produto['estoque'] == 0 || $bloqueia_vendedor_compra ? 'disabled' : '' ?>>Adicionar ao carrinho</button>
                        <button type="submit" class="btn btn-comprar-agora" name="comprar" <?= $produto['estoque'] == 0 || $bloqueia_vendedor_compra ? 'disabled' : '' ?>>Comprar Agora</button>
                    </form>
                    <div id="msg-carrinho" style="display:none; margin-top:15px;" class="alert"></div>
                </div>
            </div>
        </div>

        <div class="product-description-section">
            <h5>Detalhes do produto</h5>
            <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
        </div>

        <div class="qna-section" id="secao-perguntas">
            <h4>Perguntas e Respostas</h4>
            <?php if ($perguntas): foreach ($perguntas as $p): ?>
            <div class="qna-item" id="pergunta-<?= $p['id_pergunta'] ?>">
                <p class="qna-question"><strong><?= htmlspecialchars($p['nome_cliente']) ?>:</strong> <?= htmlspecialchars($p['texto']) ?> <small class="text-muted"><?= date('d/m/Y H:i', strtotime($p['data'])) ?></small></p>
                <?php if (!empty($respostas[$p['id_pergunta']])): $resp = $respostas[$p['id_pergunta']]; ?>
                <div class="qna-answer">
                    <p><strong><?= htmlspecialchars($resp['nome_vendedor_resposta'] ?? 'Vendedor') ?>:</strong> <?= htmlspecialchars($resp['texto']) ?> <small class="text-muted"><?= date('d/m/Y H:i', strtotime($resp['data'])) ?></small></p>
                </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['vendedor_id'], $produto['id_vendedor']) && $_SESSION['vendedor_id'] == $produto['id_vendedor'] && empty($respostas[$p['id_pergunta']])): ?>
                <button class="btn-responder<?= ($pergunta_para_responder == $p['id_pergunta']) ? ' active' : '' ?>"
                        onclick="mostrarFormResposta(<?= $p['id_pergunta'] ?>, event)">Responder</button>
                <div id="form-resposta-<?= $p['id_pergunta'] ?>" style="display:none;">
                    <form method="post" class="form-resposta-vendedor">
                    <input type="hidden" name="id_pergunta" value="<?= $p['id_pergunta'] ?>">
                    <textarea name="resposta" placeholder="Sua resposta..." required></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">Enviar Resposta</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; else: ?>
            <p>Nenhuma pergunta sobre este produto ainda. Seja o primeiro!</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['usuario_id']) || isset($_SESSION['vendedor_id'])): ?>
            <form method="post" class="mt-4" id="form-pergunta" style="margin-top: 30px;">
                <h5>Faça sua pergunta</h5>
                <textarea name="pergunta" placeholder="Escreva sua pergunta aqui..." required></textarea>
                <button type="submit" class="btn btn-primary">Enviar Pergunta</button>
            </form>
            <?php elseif (!isset($_SESSION['vendedor_id'])): // Não mostra para vendedor logado, apenas para não-logado ou cliente ?>
            <p style="margin-top: 30px; font-weight:500;">
                <a href="login.php?redirect=<?= urlencode("aba_produto.php?id=$id_produto") ?>">Faça login</a> para fazer uma pergunta.
            </p>
            <?php endif; ?>
        </div>
    </div>
  </main>

  <footer class="site-footer-bottom">
    <div class="container footer-content-grid">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Oferecendo solução para o meio ambiente e seu bolso.</p>
      </div>
      <div class="footer-col">
        <h4>Navegue</h4>
        <a href="tela_inicial.php">Início</a>
        <a href="loja.php">Loja</a>
        <a href="<?= htmlspecialchars($link_perfil) ?>">Meu Perfil</a>
      </div>
      <div class="footer-col">
        <h4>Contato</h4>
        <p>📧 circuito_sustentavel@gmail.com</p>
        <p>📞 (85) 992933310</p>
      </div>
    </div>
        <p>📞 (85) 992933310</p>
      </div>
    </div>
    <div class="footer-copyright">
      &copy; <?php echo date("Y"); ?> Circuito Sustentável Inc. Todos os direitos reservados.
    </div>
  </footer>

  <script>
    // Header Scroll Effect
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Lógica para thumbnails da imagem do produto
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail-img');
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            if (mainImage) mainImage.src = this.src;
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Lógica para mostrar formulário de resposta do vendedor
    function mostrarFormResposta(idPergunta, event) {
      if (event) event.preventDefault();
      // Esconde todos os outros formulários de resposta
      document.querySelectorAll('[id^="form-resposta-"]').forEach(div => {
        if (div.id !== `form-resposta-${idPergunta}`) {
            div.style.display = 'none';
        }
      });
      // Remove a classe 'active' de todos os outros botões
      document.querySelectorAll('.btn-responder').forEach(btn => {
        if (btn !== event.target) {
            btn.classList.remove('active');
        }
      });

      const formDiv = document.getElementById('form-resposta-' + idPergunta);
      if (formDiv) {
        const isVisible = formDiv.style.display === 'block';
        formDiv.style.display = isVisible ? 'none' : 'block';
        if (event && event.target) {
            event.target.classList.toggle('active', !isVisible);
        }
        if (!isVisible) { // Se está abrindo
            const perguntaDiv = document.getElementById('pergunta-' + idPergunta);
            if (perguntaDiv) {
                // Rola para a pergunta de forma suave
                setTimeout(() => { // Delay para o display block ser aplicado
                    perguntaDiv.scrollIntoView({behavior: 'smooth', block: 'center'});
                }, 50);
            }
        }
      }
      // Atualiza a URL sem recarregar a página
      if (history.pushState) {
        var url = new URL(window.location);
        url.searchParams.set('id', '<?= $id_produto ?>');
        if (formDiv.style.display === 'block') {
            url.searchParams.set('responder', idPergunta);
        } else {
            url.searchParams.delete('responder');
        }
        history.replaceState({path: url.href}, '', url.href);
      }
    }

    // Verifica se há um parâmetro 'responder' na URL ao carregar a página
    window.addEventListener('load', function() { // Alterado de window.onload para addEventListener
      const urlParams = new URLSearchParams(window.location.search);
      const responderId = urlParams.get('responder');
      if (responderId) {
        const responderButton = document.querySelector(`.btn-responder[onclick*="mostrarFormResposta(${responderId}, event)"]`);
        if (responderButton) {
            // Simula um clique no botão para abrir o formulário e posicionar a tela
            // Usar um pequeno timeout para garantir que tudo está carregado
            setTimeout(() => {
                 responderButton.click(); // Simula o clique
            }, 100);
        }
      }
       // Scroll para a mensagem de pergunta ou resposta enviada
        if (urlParams.has('pergunta_enviada') || urlParams.has('resposta_enviada')) {
            const qnaSection = document.getElementById('secao-perguntas');
            if (qnaSection) {
                setTimeout(() => { // Timeout para garantir renderização
                    qnaSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 200);
            }
        }
    });

    // AJAX para enviar resposta do vendedor
    document.querySelectorAll('.form-resposta-vendedor').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, true); // Envia para a mesma página
        xhr.onload = function() {
          if (xhr.status === 200) {
            // Idealmente, o PHP retornaria JSON para atualizar dinamicamente.
            // Por agora, apenas recarrega para mostrar a resposta.
            // Adicionar &resposta_enviada=1&pergunta_id=ID_DA_PERGUNTA para scroll
            const idPergunta = formData.get('id_pergunta');
            window.location.href = `aba_produto.php?id=<?= $id_produto ?>&resposta_enviada=1#pergunta-${idPergunta}`;
          } else {
            alert('Erro ao enviar resposta.');
          }
        };
        xhr.send(formData);
      });
    });
    
    // AJAX para enviar pergunta do cliente
    const formPergunta = document.getElementById('form-pergunta');
    if(formPergunta) {
        formPergunta.addEventListener('submit', function(e) {
          e.preventDefault();
          var formData = new FormData(this);
          var xhr = new XMLHttpRequest();
          xhr.open('POST', window.location.href, true);
          xhr.onload = function() { 
            if (xhr.status === 200) {
                window.location.href = `aba_produto.php?id=<?= $id_produto ?>&pergunta_enviada=1#secao-perguntas`;
            } else {
                alert('Erro ao enviar pergunta.');
            }
          };
          xhr.send(formData);
        });
    }

    // SUBSTITUIR LÓGICA AJAX DO CARRINHO PELO SCRIPT ANTIGO
    document.getElementById('form-carrinho').addEventListener('submit', function(e) {
      var btn = document.activeElement;
      // --- BLOQUEIA O VENDEDOR DE COMPRAR O PRÓPRIO PRODUTO (JS) ---
      var bloqueiaVendedor = <?= $bloqueia_vendedor_compra ? 'true' : 'false' ?>;
      if (bloqueiaVendedor) {
        e.preventDefault();
        var msg = document.getElementById('msg-carrinho');
        msg.innerText = 'Você não pode comprar seu próprio produto.';
        msg.style.display = 'block';
        msg.className = 'alert alert-danger';
        return;
      }
      if (btn && btn.name === 'add_carrinho') {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('id_produto', '<?= $id_produto ?>');
        formData.append('ajax_add_carrinho', '1');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, true);
        xhr.onload = function() {
          if (xhr.status === 200) {
            var msg = document.getElementById('msg-carrinho');
            msg.innerText = 'Produto adicionado ao carrinho!';
            msg.style.display = 'block';
            setTimeout(function() { msg.style.display = 'none'; location.reload(); }, 1200);
          }
        };
        xhr.send(formData);
      }
      // Se for "Comprar Agora", submit normal
    });

    // --- LÓGICA PARA "COMPRAR AGORA" (redireciona para pagamento.php) ---
    document.getElementById('form-carrinho').addEventListener('submit', function(e) {
      var btn = document.activeElement;
      var bloqueiaVendedor = <?= $bloqueia_vendedor_compra ? 'true' : 'false' ?>;
      if (bloqueiaVendedor) {
        e.preventDefault();
        var msg = document.getElementById('msg-carrinho');
        msg.innerText = 'Você não pode comprar seu próprio produto.';
        msg.style.display = 'block';
        msg.className = 'alert alert-danger';
        return;
      }
      if (btn && btn.name === 'comprar') {
        e.preventDefault();
        var formData = new FormData(this);
        var id_produto_compra = formData.get('id_produto');
        var quantidade_compra = formData.get('quantidade');
        window.location.href = 'pagamento.php?id_produto=' + id_produto_compra + '&quantidade=' + quantidade_compra;
      }
    });
  </script>
</body>
</html>