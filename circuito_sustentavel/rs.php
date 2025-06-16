<?php
session_start();
include 'conexao.php'; // Assume que $conexao é configurado aqui

// Lógica para identificar usuário logado (simplificado para o exemplo)
$usuario_logado = false;
$nome_usuario_logado = "Visitante";
$foto_perfil_usuario_logado = "img/user.png"; // Sempre usa o mesmo placeholder
$link_perfil_usuario_logado = "login.php";

if (isset($_SESSION['usuario_id'])) {
    $usuario_logado = true;
    $id_cliente = $_SESSION['usuario_id'];
    // Buscar nome do cliente
    $sql_user = "SELECT nome FROM Cliente WHERE id_cliente = ? LIMIT 1";
    $stmt_user = $conexao->prepare($sql_user);
    if($stmt_user){
        $stmt_user->bind_param("i", $id_cliente);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if($user_data = $result_user->fetch_assoc()){
            $nome_usuario_logado = htmlspecialchars($user_data['nome']);
        }
        $stmt_user->close();
    }
    $link_perfil_usuario_logado = 'usuario.php';
} elseif (isset($_SESSION['vendedor_id'])) {
    $usuario_logado = true;
    $id_vendedor = $_SESSION['vendedor_id'];
    // Buscar nome do vendedor
    $sql_nome_vend = "SELECT nome FROM Vendedor WHERE id_vendedor = ? LIMIT 1";
    $stmt_nome_vend = $conexao->prepare($sql_nome_vend);
    if($stmt_nome_vend){
        $stmt_nome_vend->bind_param("i", $id_vendedor);
        $stmt_nome_vend->execute();
        $result_nome_vend = $stmt_nome_vend->get_result();
        if($nome_vend_data = $result_nome_vend->fetch_assoc()){
            $nome_usuario_logado = htmlspecialchars($nome_vend_data['nome']);
        }
        $stmt_nome_vend->close();
    }
    $link_perfil_usuario_logado = 'vendedor.php';
}

// Lógica do carrinho para o header (simplificada de loja.php)
$imagem_carrinho_header = 'img/carrinho_sem.png';
if ($usuario_logado) {
    // ... (lógica para verificar itens no carrinho e definir $imagem_carrinho_header) ...
    // Por simplicidade, vou assumir que pode estar vazio ou cheio baseado em alguma lógica
}
$link_carrinho_header = 'carrinho.php';
if(isset($_SESSION['vendedor_id'])) { $link_carrinho_header = 'carrinho_vendedor.php'; }


// --- NOVO: Função para criar postagem ---
$post_erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conteudo_postagem'])) {
    $conteudo = trim($_POST['conteudo_postagem']);
    $imagem_path = null;

    // Upload de imagem
    if (isset($_FILES['imagem_postagem']) && $_FILES['imagem_postagem']['error'] === UPLOAD_ERR_OK) {
        $img_tmp = $_FILES['imagem_postagem']['tmp_name'];
        $img_name = uniqid('post_') . '_' . basename($_FILES['imagem_postagem']['name']);
        $img_dest = 'uploads/' . $img_name;
        if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
        if (move_uploaded_file($img_tmp, $img_dest)) {
            $imagem_path = $img_dest;
        }
    }

    if ($conteudo === '' && !$imagem_path) {
        $post_erro = 'A postagem não pode estar vazia.';
    } else {
        $id_cliente = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
        $id_vendedor = isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null;
        $sql = "INSERT INTO Postagem (id_cliente, id_vendedor, conteudo, imagem) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iiss", $id_cliente, $id_vendedor, $conteudo, $imagem_path);
            $stmt->execute();
            $stmt->close();
            // Redireciona para evitar repost em refresh
            header("Location: rs.php");
            exit;
        } else {
            $post_erro = 'Erro ao salvar postagem.';
        }
    }
}

// --- NOVO: Buscar postagens do banco de dados ---
$posts_comunidade = [];
$sql = "SELECT
            p.id_postagem, p.conteudo, p.data, p.imagem,
            p.id_cliente, p.id_vendedor,
            c.nome AS nome_cliente, v.nome AS nome_vendedor,
            (SELECT COUNT(*) FROM Curtida cu WHERE cu.id_postagem = p.id_postagem) AS curtidas
        FROM Postagem p
        LEFT JOIN Cliente c ON p.id_cliente = c.id_cliente
        LEFT JOIN Vendedor v ON p.id_vendedor = v.id_vendedor
        ORDER BY p.data DESC";
$res = $conexao->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        if ($row['id_cliente']) {
            $autor_nome = $row['nome_cliente'];
            $autor_foto = "img/user.png";
        } else {
            $autor_nome = $row['nome_vendedor'] . " (Vendedor)";
            $autor_foto = "img/user.png";
        }
        $tempo_postagem = date('d/m/Y H:i', strtotime($row['data']));
        $comentarios_total = 0;
        $sql_coment = "SELECT COUNT(*) AS total FROM Comentario WHERE id_postagem = ?";
        $stmt_coment = $conexao->prepare($sql_coment);
        if ($stmt_coment) {
            $stmt_coment->bind_param("i", $row['id_postagem']);
            $stmt_coment->execute();
            $stmt_coment->bind_result($comentarios_total);
            $stmt_coment->fetch();
            $stmt_coment->close();
        }
        // Verifica se o usuário já curtiu
        $curtiu = false;
        if ($usuario_logado) {
            $sql_curtiu = "SELECT 1 FROM Curtida WHERE id_postagem = ? AND ".
                (isset($_SESSION['usuario_id']) ? "id_cliente=?" : "id_vendedor=?") . " LIMIT 1";
            $stmt_curtiu = $conexao->prepare($sql_curtiu);
            $user_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : $_SESSION['vendedor_id'];
            $stmt_curtiu->bind_param("ii", $row['id_postagem'], $user_id);
            $stmt_curtiu->execute();
            $stmt_curtiu->store_result();
            $curtiu = $stmt_curtiu->num_rows > 0;
            $stmt_curtiu->close();
        }
        $posts_comunidade[] = [
            'id' => $row['id_postagem'],
            'id_cliente' => $row['id_cliente'],     // Adicionado para verificação de autoria
            'id_vendedor' => $row['id_vendedor'],   // Adicionado para verificação de autoria
            'autor_nome' => $autor_nome,
            'autor_foto' => $autor_foto,
            'tempo_postagem' => $tempo_postagem,
            'conteudo_texto' => $row['conteudo'],
            'conteudo_imagem' => $row['imagem'],
            'curtidas' => $row['curtidas'],
            'curtiu' => $curtiu,
            'comentarios_total' => $comentarios_total
        ];
    }
}

// --- AJAX: Curtir Post ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'curtir') {
    $id_post = intval($_POST['id_post']);
    $id_cliente = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $id_vendedor = isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null;
    if ($usuario_logado && $id_post) {
        // Verifica se já curtiu
        $sql = "SELECT id_curtida FROM Curtida WHERE id_postagem=? AND ".
            ($id_cliente ? "id_cliente=?" : "id_vendedor=?");
        $stmt = $conexao->prepare($sql);
        $user_id = $id_cliente ?: $id_vendedor;
        $stmt->bind_param("ii", $id_post, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Já curtiu, remove curtida
            $sql_del = "DELETE FROM Curtida WHERE id_postagem=? AND ".
                ($id_cliente ? "id_cliente=?" : "id_vendedor=?");
            $stmt_del = $conexao->prepare($sql_del);
            $stmt_del->bind_param("ii", $id_post, $user_id);
            $stmt_del->execute();
            $stmt_del->close();
            $curtiu = false;
        } else {
            // Adiciona curtida
            $sql_add = "INSERT INTO Curtida (id_postagem, id_cliente, id_vendedor) VALUES (?, ?, ?)";
            $stmt_add = $conexao->prepare($sql_add);
            $stmt_add->bind_param("iii", $id_post, $id_cliente, $id_vendedor);
            $stmt_add->execute();
            $stmt_add->close();
            $curtiu = true;
        }
        $stmt->close();
        // Retorna novo total de curtidas
        $sql_count = "SELECT COUNT(*) FROM Curtida WHERE id_postagem=?";
        $stmt_count = $conexao->prepare($sql_count);
        $stmt_count->bind_param("i", $id_post);
        $stmt_count->execute();
        $stmt_count->bind_result($total_curtidas);
        $stmt_count->fetch();
        $stmt_count->close();
        // Adicione instrução para reload
        echo json_encode(['reload' => true]);
        exit;
    }
}

// --- AJAX: Comentar Post ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'comentar') {
    $id_post = intval($_POST['id_post']);
    $comentario = trim($_POST['comentario']);
    $id_cliente = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $id_vendedor = isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null;
    if ($usuario_logado && $id_post && $comentario !== '') {
        $sql = "INSERT INTO Comentario (id_postagem, id_cliente, id_vendedor, conteudo) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iiis", $id_post, $id_cliente, $id_vendedor, $comentario);
        $stmt->execute();
        $stmt->close();
        // Retorna sucesso e reload
        echo json_encode(['reload' => true]);
        exit;
    }
}

// --- AJAX: Buscar Comentários de um Post ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'listar_comentarios') {
    $id_post = intval($_POST['id_post']);
    $comentarios = [];
    $sql = "SELECT c.conteudo, c.data, c.id_cliente, c.id_vendedor,
                   cli.nome AS nome_cliente, v.nome AS nome_vendedor
            FROM Comentario c
            LEFT JOIN Cliente cli ON c.id_cliente = cli.id_cliente
            LEFT JOIN Vendedor v ON c.id_vendedor = v.id_vendedor
            WHERE c.id_postagem = ?
            ORDER BY c.data ASC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_post);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $autor = $row['id_cliente'] ? $row['nome_cliente'] : ($row['nome_vendedor'] . " (Vendedor)");
        $comentarios[] = [
            'autor' => $autor,
            'conteudo' => $row['conteudo'],
            'data' => date('d/m/Y H:i', strtotime($row['data']))
        ];
    }
    $stmt->close();
    echo json_encode(['comentarios' => $comentarios]);
    exit;
}

// --- AJAX: Excluir Post ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir_post') {
    $id_post = intval($_POST['id_post']);
    if ($usuario_logado && $id_post) {
        $id_cliente = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
        $id_vendedor = isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null;

        // Determina a coluna de verificação (id_cliente ou id_vendedor)
        $user_id_column = $id_cliente ? "id_cliente" : "id_vendedor";
        $user_id = $id_cliente ?: $id_vendedor;

        // Primeiro, exclui a imagem associada, se houver
        $sql_img = "SELECT imagem FROM Postagem WHERE id_postagem = ? AND $user_id_column = ?";
        $stmt_img = $conexao->prepare($sql_img);
        $stmt_img->bind_param("ii", $id_post, $user_id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();
        if ($img_data = $result_img->fetch_assoc()) {
            if ($img_data['imagem'] && file_exists($img_data['imagem'])) {
                unlink($img_data['imagem']); // Deleta o arquivo da imagem
            }
        }
        $stmt_img->close();

        // Exclui primeiro curtidas e comentários associados
        $conexao->query("DELETE FROM Curtida WHERE id_postagem = $id_post");
        $conexao->query("DELETE FROM Comentario WHERE id_postagem = $id_post");

        // Agora exclui o post
        $sql_del = "DELETE FROM Postagem WHERE id_postagem = ? AND $user_id_column = ?";
        $stmt_del = $conexao->prepare($sql_del);
        $stmt_del->bind_param("ii", $id_post, $user_id);
        if ($stmt_del->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha ao excluir.']);
        }
        $stmt_del->close();
        exit;
    }
}

// --- AJAX: Editar Post ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'editar_post') {
    $id_post = intval($_POST['id_post']);
    $conteudo = trim($_POST['conteudo']);

    if ($usuario_logado && $id_post && $conteudo !== '') {
        $id_cliente = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
        $id_vendedor = isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null;

        $user_id_column = $id_cliente ? "id_cliente" : "id_vendedor";
        $user_id = $id_cliente ?: $id_vendedor;

        $sql_upd = "UPDATE Postagem SET conteudo = ? WHERE id_postagem = ? AND $user_id_column = ?";
        $stmt_upd = $conexao->prepare($sql_upd);
        $stmt_upd->bind_param("sii", $conteudo, $id_post, $user_id);
        if ($stmt_upd->execute()) {
            echo json_encode(['success' => true, 'novo_conteudo' => nl2br(htmlspecialchars($conteudo))]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha ao salvar.']);
        }
        $stmt_upd->close();
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Comunidade Verde - Circuito Sustentável</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <style>
    :root {
        --verde: #28a060;
        --verde-escuro: #1e7c4b;
        --verde-claro-fundo: #f0f9f4;
        --cinza-claro: #f4f6f8;
        --cinza-medio: #e9ecef; /* Para bordas e fundos sutis */
        --cinza-texto: #5f6c7b;
        --cinza-escuro: #2c3e50;
        --branco: #ffffff;
        --azul-link: #007bff; /* Cor para links e interações */
        --sombra-padrao: 0 4px 15px rgba(0,0,0, 0.06);
        --sombra-card: 0 2px 10px rgba(0,0,0, 0.08);
        --sombra-hover-forte: 0 8px 25px rgba(40, 160, 96, 0.12);
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --transition-fast: 0.2s;
        --transition-std: 0.3s;
        --font-principal: 'Poppins', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: var(--font-principal);
        line-height: 1.6;
        color: var(--cinza-texto);
        background-color: var(--cinza-claro);
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    main.cv-main-content {
        padding-top: 80px; /* Espaço para o header fixo */
        display: flex;
        justify-content: center; /* Centraliza o layout-container */
    }

    .cv-layout-container {
        width: 100%;
        max-width: 1200px; /* Largura máxima para o conteúdo */
        display: flex;
        gap: 25px;
        padding: 25px 15px; /* Padding lateral para o container */
    }

    /* Header Modernizado (Copiado e Adaptado de loja.php) */
    .site-header {
        position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
        padding: 15px 0;
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        transition: background-color var(--transition-std), box-shadow var(--transition-std), padding var(--transition-std);
        border-bottom: 1px solid transparent;
    }
    .site-header.scrolled {
        background-color: var(--branco);
        box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        padding: 12px 0;
        border-bottom: 1px solid var(--cinza-claro);
    }
    .header-container {
        width: 90%; max-width: 1200px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between;
    }
    .site-header .logo { height: 45px; transition: transform var(--transition-std); }
    .site-header .logo:hover { transform: scale(1.05); }

    /* Simplificando o centro do header para a comunidade, pode ter busca ou não */
    .header-center-comunidade {
        flex-grow: 1;
        display: flex;
        justify-content: center; /* Ou flex-start se quiser alinhar com o logo */
    }
    .search-bar-comunidade { /* Opcional */
        position: relative; width: 100%; max-width: 400px;
    }
    .search-bar-comunidade input {
        width: 100%; padding: 9px 40px 9px 18px;
        border: 1px solid #dde1e6; background: var(--branco);
        border-radius: var(--border-radius-lg); font-size: 0.9em;
        font-family: var(--font-principal);
    }
    .search-bar-comunidade input:focus {
        outline: none; border-color: var(--verde);
        box-shadow: 0 0 0 3px rgba(40, 160, 96, 0.15);
    }
    .search-bar-comunidade .search-submit-btn {
        position: absolute; right: 3px; top: 3px; bottom: 3px;
        width: 38px; background: var(--verde); border: none; cursor: pointer;
        border-top-right-radius: var(--border-radius-lg);
        border-bottom-right-radius: var(--border-radius-lg);
        display: flex; align-items:center; justify-content:center;
        transition: background-color var(--transition-std);
    }
    .search-bar-comunidade .search-submit-btn:hover { background-color: var(--verde-escuro); }
    .search-bar-comunidade .search-submit-btn svg { width: 16px; height: 16px; color: var(--branco); }


    .header-actions { display: flex; align-items: center; gap: 15px; }
    .header-actions a { display: flex; align-items: center; padding: 5px; }
    .header-actions img.action-icon { /* Ícones como <img> */
        height: 28px; width: auto;
        transition: transform var(--transition-fast);
    }
     .header-actions a .icon-svg { /* Para ícones SVG se usar no futuro */
        width: 24px; height: 24px; color: var(--cinza-escuro);
        transition: color var(--transition-std), transform var(--transition-std);
    }
    .header-actions a:hover img.action-icon,
    .header-actions a:hover .icon-svg {
        transform: scale(1.1);
        color: var(--verde); /* Para SVGs */
    }
    .auth-buttons-header .btn { padding: 7px 14px; font-size: 0.85em; margin-left:5px;}
    .auth-buttons-header .btn-outline { border-color: var(--verde-escuro); color: var(--verde-escuro); }
    .auth-buttons-header .btn-outline:hover { background-color: var(--verde-escuro); color: var(--branco); }

    /* Sidebar */
    .cv-sidebar {
        flex: 0 0 280px; /* Largura fixa, não encolhe, não cresce */
        background-color: var(--branco);
        padding: 20px;
        border-radius: var(--border-radius-md);
        box-shadow: var(--sombra-padrao);
        height: fit-content; /* Para não esticar com o feed */
        position: sticky; /* Sidebar fixa ao rolar */
        top: 100px; /* Abaixo do header fixo */
    }
    .user-profile-snippet { text-align: center; margin-bottom: 25px; }
    .user-profile-snippet img {
        width: 80px; height: 80px; border-radius: 50%;
        object-fit: cover; margin-bottom: 10px;
        border: 3px solid var(--verde-claro-fundo);
    }
    .user-profile-snippet h4 {
        font-size: 1.1em; color: var(--cinza-escuro);
        font-weight: 600; margin-bottom: 3px;
    }
    .user-profile-snippet a.view-profile {
        font-size: 0.85em; color: var(--verde);
        text-decoration: none; font-weight: 500;
    }
    .user-profile-snippet a.view-profile:hover { text-decoration: underline; }

    .sidebar-nav h5 {
        font-size: 0.9em; text-transform: uppercase;
        color: var(--cinza-texto); font-weight: 600;
        margin-bottom: 10px; padding-bottom: 5px;
        border-bottom: 1px solid var(--cinza-medio);
    }
    .sidebar-nav ul { list-style: none; padding: 0; margin: 0 0 25px 0; }
    .sidebar-nav ul li a {
        display: flex; align-items: center;
        padding: 10px 8px; text-decoration: none;
        color: var(--cinza-escuro); font-size: 0.95em;
        border-radius: var(--border-radius-sm);
        transition: background-color var(--transition-fast), color var(--transition-fast);
        font-weight: 500;
    }
    .sidebar-nav ul li a svg {
        width: 20px; height: 20px; margin-right: 12px;
        color: var(--cinza-texto); transition: color var(--transition-fast);
    }
    .sidebar-nav ul li a:hover, .sidebar-nav ul li a.active {
        background-color: var(--verde-claro-fundo);
        color: var(--verde);
    }
    .sidebar-nav ul li a:hover svg, .sidebar-nav ul li a.active svg { color: var(--verde); }

    /* Feed Area */
    .cv-feed-area {
        flex-grow: 1; /* Ocupa o restante do espaço */
        max-width: 700px; /* Limita largura do feed para melhor leitura */
        margin: 0 auto; /* Centraliza se a sidebar for mais estreita */
    }

    /* Criar Postagem */
    .cv-create-post {
        background-color: var(--branco);
        padding: 20px;
        border-radius: var(--border-radius-md);
        box-shadow: var(--sombra-card);
        margin-bottom: 25px;
    }
    .cv-create-post textarea {
        width: 100%;
        min-height: 80px;
        padding: 12px;
        border: 1px solid var(--cinza-medio);
        border-radius: var(--border-radius-sm);
        font-family: var(--font-principal);
        font-size: 0.95em;
        resize: vertical;
        margin-bottom: 10px;
    }
    .cv-create-post textarea:focus {
        outline: none; border-color: var(--verde);
        box-shadow: 0 0 0 2px rgba(40, 160, 96, 0.1);
    }
    .create-post-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .create-post-actions .action-buttons button { /* Botões para imagem, vídeo, etc. */
        background: none; border: none; cursor: pointer;
        padding: 8px; color: var(--cinza-texto);
        transition: color var(--transition-fast);
    }
    .create-post-actions .action-buttons button:hover { color: var(--verde); }
    .create-post-actions .action-buttons button svg { width: 22px; height: 22px; }
    .btn-post { /* Botão de postar */
        padding: 8px 20px;
        font-size: 0.9em;
    }

    /* Card de Postagem */
    .cv-post-card {
        background-color: var(--branco);
        border-radius: var(--border-radius-md);
        box-shadow: var(--sombra-card);
        margin-bottom: 25px;
        padding: 20px;
        opacity: 0; /* Para animação */
        transform: translateY(20px);
    }
    .cv-post-card.in-view {
        opacity: 1; transform: translateY(0);
        transition: opacity 0.5s ease-out, transform 0.5s ease-out;
    }
    .post-header {
        display: flex; align-items: center;
        margin-bottom: 15px;
    }
    .post-author-avatar img {
        width: 45px; height: 45px;
        border-radius: 50%; object-fit: cover;
        margin-right: 12px;
    }
    .post-author-info h5 {
        font-size: 1em; font-weight: 600;
        color: var(--cinza-escuro); margin: 0;
    }
    .post-author-info span.post-time {
        font-size: 0.8em; color: var(--cinza-texto);
    }
    .post-content p {
        font-size: 0.95em; line-height: 1.7;
        color: var(--cinza-escuro); margin-bottom: 15px;
        white-space: pre-wrap; /* Para manter quebras de linha do usuário */
    }
    .post-image img {
        max-width: 100%;
        border-radius: var(--border-radius-sm);
        margin-bottom: 15px;
    }
    .post-actions {
        display: flex;
        justify-content: space-around; /* Ou flex-start com gap */
        padding-top: 10px;
        border-top: 1px solid var(--cinza-claro);
    }
    .post-action-btn {
        background: none; border: none; cursor: pointer;
        color: var(--cinza-texto); font-weight: 500;
        font-size: 0.85em; padding: 8px 10px;
        display: flex; align-items: center; gap: 6px;
        border-radius: var(--border-radius-sm);
        transition: background-color var(--transition-fast), color var(--transition-fast);
    }
    .post-action-btn:hover { background-color: var(--cinza-claro); color: var(--verde); }
    .post-action-btn svg { width: 18px; height: 18px; }
    .post-action-btn.liked { color: var(--verde); font-weight: 600;} /* Exemplo de estado "curtido" */

    .post-stats {
        font-size: 0.8em; color: var(--cinza-texto);
        margin-top: 10px; padding-top: 10px;
        border-top: 1px solid var(--cinza-claro);
        display: flex; justify-content: space-between;
    }
    .comentarios-count {
        color: var(--verde);
        cursor: pointer;
        transition: color 0.2s;
    }
    .comentarios-count:hover {
        color: var(--verde-escuro);
        text-decoration: underline;
    }

    /* Animações de Entrada */
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .animate-on-scroll.in-view { opacity: 1; transform: translateY(0); }
    /* Delays podem ser adicionados com classes .delay-xxx se necessário */


    /* Footer */
    .site-footer-bottom {
        background-color: var(--cinza-escuro); color: #b0bec5;
        padding: 50px 0 30px; font-size: 0.9em; width: 100%;
        margin-top: 40px; /* Espaço após o conteúdo principal */
    }
    .footer-content-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px; margin-bottom: 30px;
        width: 90%; max-width: 1140px; margin: 0 auto 30px auto;
    }
    .footer-col h4 { font-size: 1.1em; color: var(--branco); font-weight: 600; margin-bottom: 15px; }
    .footer-col p, .footer-col a { color: #b0bec5; text-decoration: none; margin-bottom: 8px; display: block; font-size: 0.95em; }
    .footer-col a:hover { color: var(--verde); transform: translateX(2px); transition: transform var(--transition-fast); }
    .footer-copyright { text-align: center; padding-top: 30px; border-top: 1px solid #4a5c6a; color: #78909c; width: 90%; max-width: 1140px; margin: 0 auto; }

    /* Responsividade */
    @media (max-width: 992px) {
        .cv-sidebar { display: none; /* Ocultar sidebar em telas menores, ou transformar em menu off-canvas */ }
        .cv-feed-area { max-width: 100%; } /* Feed ocupa toda a largura disponível */
        .header-center-comunidade .search-bar-comunidade { max-width: 300px; }
    }
     @media (max-width: 768px) {
        .cv-layout-container { padding: 15px 10px; }
        .header-container { flex-wrap: wrap; justify-content: center; }
        .site-header .logo { margin-bottom: 10px; }
        .header-center-comunidade { width:100%; order: 3; padding-top: 10px; }
        .header-actions { order: 2; width: 100%; justify-content: space-between; margin-bottom: 10px; }
        main.cv-main-content { padding-top: 150px; /* Ajustar para header maior em mobile */ }
        .cv-create-post, .cv-post-card { padding: 15px; }
        .post-author-avatar img { width: 40px; height: 40px; }
        .post-author-info h5 {font-size: 0.95em;}
        .post-content p {font-size: 0.9em;}
        .post-action-btn {font-size: 0.8em; gap: 4px;}
        .post-action-btn svg { width: 16px; height: 16px; }
    }

    /* Modal de comentários */
    .modal-comentarios-bg {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(44,62,80,0.25); justify-content: center; align-items: center;
    }
    .modal-comentarios {
        background: #fff; border-radius: 10px; max-width: 400px; width: 95%; max-height: 80vh; overflow-y: auto;
        box-shadow: 0 8px 32px rgba(44,62,80,0.18); padding: 24px 18px 18px 18px; position: relative;
        animation: fadeIn .2s;
    }
    .modal-comentarios h4 { margin-bottom: 12px; font-size: 1.1em; color: var(--verde-escuro);}
    .modal-comentarios .comentario-item { margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;}
    .modal-comentarios .comentario-autor { font-weight: 600; color: var(--cinza-escuro);}
    .modal-comentarios .comentario-data { font-size: 0.85em; color: #888; margin-left: 6px;}
    .modal-comentarios .comentario-conteudo { margin-top: 2px; color: var(--cinza-texto);}
    .modal-comentarios .fechar-modal { position: absolute; right: 10px; top: 10px; background: none; border: none; font-size: 1.2em; color: #888; cursor: pointer;}
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.98);} to { opacity: 1; transform: scale(1);} }
  </style>
</head>
<body>

  <header class="site-header">
    <div class="header-container">
      <a href="loja.php">
        <img src="img/logo2.png" alt="Circuito Sustentável Logo" class="logo" />
      </a>

      <div class="header-center-comunidade">
        <div class="search-bar-comunidade">
          <form method="get" action="comunidade_verde.php">
            <input type="text" name="busca_comunidade" placeholder="Buscar na comunidade..." aria-label="Buscar na comunidade"/>
            <button type="submit" class="search-submit-btn" aria-label="Pesquisar">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
              </svg>
            </button>
          </form>
        </div>
      </div>

      <div class="header-actions">
        <?php if ($usuario_logado): ?>
            <a href="<?= htmlspecialchars($link_perfil_usuario_logado) ?>" aria-label="Meu Perfil" title="Meu Perfil">
                <img src="<?= htmlspecialchars($foto_perfil_usuario_logado) ?>" alt="Meu Perfil" class="action-icon" style="height:32px; width:32px; border-radius:50%; object-fit:cover;">
            </a>
        <?php else: ?>
            <div class="auth-buttons-header">
            <button class="btn btn-outline btn-sm" onclick="location.href='login.php?redirect=comunidade_verde.php'">Entrar</button>
            <button class="btn btn-primary btn-sm" onclick="location.href='cadastro.php'">Registrar</button>
            </div>
        <?php endif; ?>
        <a href="loja.php" aria-label="C+ Moedas" title="Minhas Moedas">
            <img src="img/loja.png" alt="C+ Moedas" class="action-icon">
        </a>
        <a href="<?= htmlspecialchars($link_carrinho_header) ?>" aria-label="Carrinho de Compras" title="Carrinho">
            <img src="<?= htmlspecialchars($imagem_carrinho_header) ?>" alt="Carrinho" class="action-icon">
        </a>
      </div>
    </div>
  </header>

  <main class="cv-main-content">
    <div class="cv-layout-container">
        <aside class="cv-sidebar animate-on-scroll">
            <div class="user-profile-snippet">
                <img src="<?= htmlspecialchars($foto_perfil_usuario_logado) ?>" alt="Foto de Perfil de <?= $nome_usuario_logado ?>">
                <h4><?= $nome_usuario_logado ?></h4>
                <?php if($usuario_logado): ?>
                <a href="<?= htmlspecialchars($link_perfil_usuario_logado) ?>" class="view-profile">Ver Perfil</a>
                <?php else: ?>
                <a href="login.php?redirect=comunidade_verde.php" class="view-profile">Fazer Login</a>
                <?php endif; ?>
            </div>
            <nav class="sidebar-nav">
                <h5>Navegação</h5>
                <ul>
                    <li><a href="#" class="active">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>
                        Meu Feed
                    </a></li>
                </ul>


            </nav>
        </aside>

        <section class="cv-feed-area">
            <?php if ($usuario_logado): ?>
            <div class="cv-create-post animate-on-scroll">
                <?php if ($post_erro): ?>
                    <div style="color:red; margin-bottom:8px;"><?= htmlspecialchars($post_erro) ?></div>
                <?php endif; ?>
                <form method="post" action="rs.php" enctype="multipart/form-data">
                    <textarea name="conteudo_postagem" placeholder="No que você está pensando, <?= $nome_usuario_logado ?>?" required></textarea>
                    <div class="create-post-actions">
                        <div class="action-buttons">
                            <label style="cursor:pointer;">
                                <input type="file" name="imagem_postagem" accept="image/*" style="display:none" id="input-imagem-postagem" onchange="previewImagemPostagem(event)">
                                <button type="button" aria-label="Adicionar Foto" id="btn-add-img" style="opacity:1;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                </button>
                            </label>
                            <span id="preview-img-postagem" style="display:none;vertical-align:middle;"></span>
                        </div>
                        <button class="btn btn-primary btn-post" type="submit">Publicar</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            <div class="comentario-autor">
                <div class="cv-post-feed">
                    <?php
                    $post_delay_animacao = 0;
                    foreach ($posts_comunidade as $post):
                        $post_anim_delay_class = 'delay-0-' . (($post_delay_animacao % 2) * 2 + 1) . 's'; // 0.1s, 0.3s
                    ?>
                    <article class="cv-post-card animate-on-scroll <?= $post_anim_delay_class ?>" data-post-id="<?= $post['id'] ?>">
                        <div class="post-header">
                            <div class="post-author-avatar">
                                <img src="<?= htmlspecialchars($post['autor_foto']) ?>" alt="Foto de <?= htmlspecialchars($post['autor_nome']) ?>">
                        </div>
                        <div class="post-author-info">
                            <h5><?= htmlspecialchars($post['autor_nome']) ?></h5>
                            <span class="post-time"><?= htmlspecialchars($post['tempo_postagem']) ?></span>
                        </div>
                        <div class="post-options-container" style="margin-left:auto; position:relative;">
                            <?php
                            // Verifica se o usuário logado é o autor do post
                            $is_author = false;
                            if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post['id_cliente']) {
                                $is_author = true;
                            } elseif (isset($_SESSION['vendedor_id']) && $_SESSION['vendedor_id'] == $post['id_vendedor']) {
                                $is_author = true;
                            }

                            if ($is_author):
                            ?>
                            <button aria-label="Opções do post" class="post-options-btn" style="background:none; border:none; cursor:pointer; color:var(--cinza-texto);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                </svg>
                            </button>
                            <div class="post-options-menu" style="display:none; position:absolute; right:0; top:25px; background-color:var(--branco); border-radius:var(--border-radius-md); box-shadow:var(--sombra-card); z-index:10; min-width:120px; padding: 8px 0;">
                                <a href="#" class="delete-post-btn" style="display:block; padding:8px 15px; color:red; text-decoration:none; font-size:0.9em;">Excluir Post</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="post-content">
                        <p><?= nl2br(htmlspecialchars($post['conteudo_texto'])) ?></p>
                        <?php if ($post['conteudo_imagem']): ?>
                        <div class="post-image">
                            <img src="<?= htmlspecialchars($post['conteudo_imagem']) ?>" alt="Imagem da postagem" style="max-width:300px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="post-stats">
                        <span class="curtidas-count"><?= htmlspecialchars($post['curtidas']) ?> Curtidas</span>
                        <span class="comentarios-count">
                            <?= htmlspecialchars($post['comentarios_total']) ?> Comentários
                        </span>
                    </div>
                    <div class="comentarios-list-area"></div>
                    <div class="post-actions">
                        <button class="post-action-btn btn-curtir<?= $post['curtiu'] ? ' liked' : '' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="<?= $post['curtiu'] ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                            <span><?= $post['curtiu'] ? 'Curtido' : 'Curtir' ?></span>
                        </button>
                        <button class="post-action-btn btn-comentar">
                            Comentar
                        </button>
                    </div>
                    <div class="comentar-area" style="display:none; margin-top:10px;">
                        <form class="form-comentario" autocomplete="off">
                            <input type="text" name="comentario" placeholder="Digite seu comentário..." style="width:80%;padding:6px;" required>
                            <button type="submit" class="btn btn-primary btn-sm">Enviar</button>
                        </form>
                    </div>
                </article>
                <?php
                    $post_delay_animacao++;
                endforeach;
                if (empty($posts_comunidade)) {
                    echo '<div style="text-align:center; color:#888; margin-top:40px;">Nenhuma postagem ainda. Seja o primeiro a postar!</div>';
                }
                ?>
            </div>
        </section>
    </div>
  </main>

  <footer class="site-footer-bottom">
    <div class="container footer-content-grid">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Conectando pessoas por um futuro mais verde.</p>
      </div>
      <div class="footer-col">
        <h4>Navegue na Comunidade</h4>
        <a href="#">Meu Feed</a>
        <a href="#">Descobrir Pessoas</a>
        <a href="#">Grupos Temáticos</a>
        <a href="#">Eventos Sustentáveis</a>
      </div>
      <div class="footer-col">
        <h4>Contato & Suporte</h4>
        <a href="suporte.html">Central de Ajuda</a>
        <p>📧 comunidade@circuitosustentavel.com</p>
      </div>
    </div>
    <div class="footer-copyright">
      &copy; <?php echo date("Y"); ?> Circuito Sustentável Inc. Todos os direitos reservados.
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

        // Gerenciar menu de opções do post
        document.querySelectorAll('.post-options-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                // Fecha outros menus abertos
                document.querySelectorAll('.post-options-menu').forEach(menu => menu.style.display = 'none');
                // Abre o menu clicado
                const menu = this.nextElementSibling;
                menu.style.display = 'block';
            });
        });

        // Fechar o menu se clicar em qualquer outro lugar da página
        window.addEventListener('click', function() {
            document.querySelectorAll('.post-options-menu').forEach(menu => {
                if (menu.style.display === 'block') {
                    menu.style.display = 'none';
                }
            });
        });

        // Ação para o botão de excluir
        document.querySelectorAll('.delete-post-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const postCard = this.closest('.cv-post-card');
                const postId = postCard.getAttribute('data-post-id');

                if (confirm('Tem certeza que deseja excluir este post? Esta ação não pode ser desfeita.')) {
                    fetch('rs.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `acao=excluir_post&id_post=${postId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            postCard.style.transition = 'opacity 0.5s ease';
                            postCard.style.opacity = '0';
                            setTimeout(() => postCard.remove(), 500);
                        } else {
                            alert('Erro ao excluir o post.');
                        }
                    });
                }
            });
        });

        // Botão "Comentar" mostra/esconde o formulário de comentário
        document.querySelectorAll('.btn-comentar').forEach(btn => {
            btn.addEventListener('click', function() {
                const postCard = btn.closest('.cv-post-card');
                const area = postCard.querySelector('.comentar-area');
                area.style.display = area.style.display === 'none' ? 'block' : 'none';
            });
        });

        // Envio do comentário via AJAX
        document.querySelectorAll('.form-comentario').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const postCard = form.closest('.cv-post-card');
                const postId = postCard.getAttribute('data-post-id');
                const comentario = form.comentario.value;
                fetch('rs.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'acao=comentar&id_post=' + encodeURIComponent(postId) + '&comentario=' + encodeURIComponent(comentario)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.reload) {
                        window.location.reload();
                    }
                });
            });
        });

        // Remover ação para o botão de editar
    });


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

    // Animações ao rolar com IntersectionObserver
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            } else {
                // Opcional: para re-animar ao rolar para cima e para baixo
                // entry.target.classList.remove('in-view');
            }
        });
    }, { threshold: 0.1 }); // 10% do elemento visível
    animatedElements.forEach(el => observer.observe(el));

    // Lógica para "Curtir" (exemplo simples, sem salvar no backend)
    document.querySelectorAll('.post-action-btn').forEach(button => {
        if (button.textContent.trim().toLowerCase() === 'curtir') {
            button.addEventListener('click', function() {
                this.classList.toggle('liked');
                if (this.classList.contains('liked')) {
                    this.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        Curtido
                    `;
                } else {
                     this.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                        Curtir
                    `;
                }
            });
        }
    });

    // Preview da imagem antes do upload
    function previewImagemPostagem(event) {
        const input = event.target;
        const preview = document.getElementById('preview-img-postagem');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width:60px;max-height:60px;border-radius:6px;margin-left:8px;">';
                preview.style.display = 'inline-block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = '';
            preview.style.display = 'none';
        }
    }
    // Ativa botão de imagem
    document.getElementById('btn-add-img').onclick = function(e) {
        e.preventDefault();
        document.getElementById('input-imagem-postagem').click();
    };

    // Curtir via AJAX
    document.querySelectorAll('.btn-curtir').forEach(btn => {
        btn.addEventListener('click', function() {
            const postCard = btn.closest('.cv-post-card');
            const postId = postCard.getAttribute('data-post-id');
            fetch('rs.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'acao=curtir&id_post=' + encodeURIComponent(postId)
            })
            .then(r => r.json())
            .then(data => {
                if (data.reload) {
                    window.location.reload();
                }
            });
        });
    });

   

    // Comentários inline com transição
    document.querySelectorAll('.cv-post-card').forEach(card => {
        const comentariosCount = card.querySelector('.comentarios-count');
        const comentariosArea = card.querySelector('.comentarios-list-area');
        let aberta = false;
        comentariosCount.addEventListener('click', function() {
            if (!aberta) {
                comentariosArea.innerHTML = '<div style="color:#888;text-align:center;">Carregando...</div>';
                comentariosArea.classList.add('aberta');
                fetch('rs.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'acao=listar_comentarios&id_post=' + encodeURIComponent(card.getAttribute('data-post-id'))
                })
                .then(r => r.json())
                .then(data => {
                    if (data.comentarios.length === 0) {
                        comentariosArea.innerHTML = '<div style="color:#888;text-align:center;">Nenhum comentário ainda.</div>';
                    } else {
                        comentariosArea.innerHTML = data.comentarios.map(c =>
                            `<div class="comentario-item">
                                <span style="font-weight: bold;" class="comentario-autor">${c.autor}</span>
                                <span class="comentario-data">${c.data}</span>
                                <div class="comentario-conteudo">${c.conteudo.replace(/</g,"&lt;").replace(/>/g,"&gt;")}</div>
                            </div>`
                        ).join('');
                    }
                });
            } else {
                comentariosArea.classList.remove('aberta');
                setTimeout(() => { comentariosArea.innerHTML = ''; }, 400);
            }
            aberta = !aberta;
        });
    });

    // Modal de comentários
    function fecharModalComentarios() {
        document.getElementById('modal-comentarios-bg').style.display = 'none';
        document.getElementById('modal-comentarios-list').innerHTML = '';
    }

    document.querySelectorAll('.comentarios-count').forEach(span => {
        span.addEventListener('click', function() {
            const postCard = span.closest('.cv-post-card');
            const postId = postCard.getAttribute('data-post-id');
            const modalBg = document.getElementById('modal-comentarios-bg');
            const modalList = document.getElementById('modal-comentarios-list');
            modalList.innerHTML = '<div style="text-align:center;color:#888;">Carregando...</div>';
            modalBg.style.display = 'flex';
            fetch('rs.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'acao=listar_comentarios&id_post=' + encodeURIComponent(postId)
            })
            .then(r => r.json())
            .then(data => {
                if (data.comentarios.length === 0) {
                    modalList.innerHTML = '<div style="color:#888;text-align:center;">Nenhum comentário ainda.</div>';
                } else {
                    modalList.innerHTML = data.comentarios.map(c =>
                        `<div class="comentario-item">
                            <span class="comentario-autor">${c.autor}</span>
                            <span class="comentario-data">${c.data}</span>
                            <div class="comentario-conteudo">${c.conteudo.replace(/</g,"&lt;").replace(/>/g,"&gt;")}</div>
                        </div>`
                    ).join('');
                }
            });
        });
    });

    // Fecha modal ao clicar fora
    document.getElementById('modal-comentarios-bg').addEventListener('click', function(e) {
        if (e.target === this) fecharModalComentarios();
    });

  </script>
</body>
</html>