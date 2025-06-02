<?php
session_start();
include 'conexao.php'; // Assume que $conexao é configurado aqui

$usuario_logado = false;
$usuario_nome = ''; 
$link_perfil = 'login.php'; // Link padrão se não logado

if (isset($_SESSION['usuario_id'])) {
    $usuario_logado = true;
    $id_cliente = $_SESSION['usuario_id'];
    $sql_user = "SELECT nome FROM Cliente WHERE id_cliente = ?";
    $stmt_user = $conexao->prepare($sql_user);
    if($stmt_user){
        $stmt_user->bind_param("i", $id_cliente);
        $stmt_user->execute();
        $resultado_user = $stmt_user->get_result();
        if ($resultado_user->num_rows > 0) {
            $usuario = $resultado_user->fetch_assoc();
            $usuario_nome = $usuario['nome'];
        }
        $stmt_user->close();
    }
    $link_perfil = 'usuario.php'; // CORRIGIDO: Link para perfil do cliente
} elseif (isset($_SESSION['vendedor_id'])) {
    $usuario_logado = true;
    $id_vendedor = $_SESSION['vendedor_id'];
    $sql_vend = "SELECT nome FROM Vendedor WHERE id_vendedor = ?";
    $stmt_vend = $conexao->prepare($sql_vend);
    if($stmt_vend){
        $stmt_vend->bind_param("i", $id_vendedor);
        $stmt_vend->execute();
        $resultado_vend = $stmt_vend->get_result();
        if ($resultado_vend->num_rows > 0) {
            $vendedor = $resultado_vend->fetch_assoc();
            $usuario_nome = $vendedor['nome'];
        }
        $stmt_vend->close();
    }
    $link_perfil = 'vendedor.php';
}

// Lógica para o ícone do carrinho (mantida da versão anterior, parece correta)
$imagem_carrinho = 'img/carrinho_sem.png'; 
if ($usuario_logado) {
    $id_entidade_carrinho = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : (isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : null);
    $coluna_entidade = isset($_SESSION['usuario_id']) ? 'id_cliente' : (isset($_SESSION['vendedor_id']) ? 'id_vendedor' : null);

    if ($id_entidade_carrinho && $coluna_entidade) {
        // Corrigido: usa COUNT(ic.id_produto) pois não existe id_item_carrinho
        $sql_carrinho_check = "SELECT COUNT(ic.id_produto) as total_itens 
                               FROM Carrinho c 
                               JOIN Item_Carrinho ic ON c.id_carrinho = ic.id_carrinho 
                               WHERE c.$coluna_entidade = ?";
        $stmt_carrinho = $conexao->prepare($sql_carrinho_check);
        if ($stmt_carrinho) {
            $stmt_carrinho->bind_param("i", $id_entidade_carrinho);
            $stmt_carrinho->execute();
            $resultado_carrinho = $stmt_carrinho->get_result();
            $dados_carrinho = $resultado_carrinho->fetch_assoc();
            if ($dados_carrinho && $dados_carrinho['total_itens'] > 0) {
                $imagem_carrinho = 'img/carrinho.png';
            }
            $stmt_carrinho->close();
        }
    }
}
$link_carrinho = 'carrinho.php'; 
if(isset($_SESSION['vendedor_id'])) {
    $link_carrinho = 'carrinho_vendedor.php'; 
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Loja - Circuito Sustentável</title>
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
        --sombra-padrao: 0 6px 18px rgba(0,0,0, 0.06);
        --sombra-hover-forte: 0 10px 25px rgba(40, 160, 96, 0.15);
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --transition-fast: 0.2s;
        --transition-std: 0.3s;
        --transition-long: 0.4s; /* Ajustado para slide do menu */
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
    
    main {
        padding-top: 90px; 
        min-height: calc(100vh - 180px); 
    }

    .container-page {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 0;
    }
    
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

    .header-center {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-grow: 1; 
        justify-content: flex-start; /* Alinha à esquerda após o logo */
        margin-left: 25px; /* Espaço após o logo */
    }

    .menu-categorias-btn {
        background: none; border: none; cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        font-family: var(--font-principal); font-weight: 500;
        font-size: 0.95em; color: var(--cinza-escuro);
        padding: 8px 12px; border-radius: var(--border-radius-md);
        transition: background-color var(--transition-std), color var(--transition-std);
    }
    .menu-categorias-btn:hover {
        background-color: var(--verde-claro-fundo);
        color: var(--verde);
    }
    .menu-categorias-btn svg { width: 20px; height: 20px; stroke-width: 2; }

    .search-bar-header {
        position: relative;
        width: 100%;
        max-width: 450px;
    }
    .search-bar-header input {
        width: 100%;
        padding: 10px 45px 10px 20px;
        border: 1px solid #dde1e6;
        background: var(--branco);
        border-radius: var(--border-radius-lg);
        font-size: 0.9em;
        font-family: var(--font-principal);
        transition: border-color var(--transition-std), box-shadow var(--transition-std);
    }
    .search-bar-header input:focus {
        outline: none;
        border-color: var(--verde);
        box-shadow: 0 0 0 3px rgba(40, 160, 96, 0.15);
    }
    .search-bar-header .search-submit-btn {
        position: absolute;
        right: 3px; top: 3px; bottom: 3px; /* Para centralizar o botão */
        width: 40px; /* Largura do botão */
        background: var(--verde); border: none; cursor: pointer;
        border-top-right-radius: var(--border-radius-lg); /* Arredondar canto */
        border-bottom-right-radius: var(--border-radius-lg);
        display: flex; align-items:center; justify-content:center;
        transition: background-color var(--transition-std);
    }
    .search-bar-header .search-submit-btn:hover {
        background-color: var(--verde-escuro);
    }
    .search-bar-header .search-submit-btn svg {
        width: 18px; height: 18px; color: var(--branco);
    }

    .header-actions { display: flex; align-items: center; gap: 15px; /* Gap reduzido */ }
    .header-actions a { /* Para os links dos ícones */
        display: flex; align-items: center;
        padding: 5px; /* Pequeno padding para área de clique */
    }
    .header-actions img.action-icon { /* Estilo para os ícones originais */
        height: 28px; /* Tamanho ajustado para ícones */
        width: auto;
        transition: transform var(--transition-fast);
    }
    .header-actions a:hover img.action-icon {
        transform: scale(1.1);
    }
    .auth-buttons-header .btn { padding: 7px 14px; font-size: 0.85em; margin-left:5px;}
    .auth-buttons-header .btn-outline { border-color: var(--verde-escuro); color: var(--verde-escuro); }
    .auth-buttons-header .btn-outline:hover { background-color: var(--verde-escuro); color: var(--branco); }


    .offcanvas-menu {
        position: fixed;
        top: 0; left: -320px; 
        width: 400px; height: 100%;
        background-color: var(--branco);
        box-shadow: 3px 0 15px rgba(0,0,0,0.1);
        z-index: 1002; /* Acima do overlay e do header */
        padding: 20px;
        overflow-y: auto;
        transition: transform var(--transition-long) cubic-bezier(0.23, 1, 0.32, 1); /* Usar transform para melhor performance */
        transform: translateX(-100%); /* Começa totalmente fora da tela */
    }
    .offcanvas-menu.open { 
        transform: translateX(calc(100% + 20px)); 
    }
     .offcanvas-menu.open {
        transform: translateX(0); /* Correto: desliza para a posição original */
    }
    /* Para o estado inicial, em vez de left: -320px, usamos transform */
    .offcanvas-menu {
        /* ... outros estilos ... */
        left: 0; /* Fixa a posição esquerda */
        transform: translateX(-105%); /* Começa fora da tela (um pouco mais para garantir) */
        /* ... */
    }


    .offcanvas-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 25px; padding-bottom: 15px;
        border-bottom: 1px solid var(--cinza-claro);
    }
    .offcanvas-header .logo-menu { height: 35px; }
    .close-offcanvas-btn {
        background: none; border: none; cursor: pointer;
        font-size: 1.8em; color: var(--cinza-texto);
        padding: 5px; line-height: 1;
        transition: color var(--transition-fast);
    }
    .close-offcanvas-btn:hover { color: var(--cinza-escuro); }
    .offcanvas-menu h3 {
        font-size: 1.3em; color: var(--verde-escuro);
        margin-bottom: 15px; text-align:left;
    }
    .offcanvas-menu ul { list-style: none; padding: 0; margin: 0; }
    .offcanvas-menu ul li a {
        display: block; padding: 10px 5px;
        text-decoration: none; color: var(--cinza-escuro);
        font-size: 0.95em; font-weight: 500;
        border-radius: var(--border-radius-sm);
        transition: background-color var(--transition-fast), color var(--transition-fast), padding-left var(--transition-fast);
    }
    .offcanvas-menu ul li a:hover {
        background-color: var(--verde-claro-fundo);
        color: var(--verde);
        padding-left: 10px; /* Efeito de indentação no hover */
    }
    .page-overlay {
        position: fixed; top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5); /* Overlay mais escuro */
        z-index: 1001; /* Abaixo do menu, acima do header */
        opacity: 0; visibility: hidden;
        transition: opacity var(--transition-long) ease-out, visibility var(--transition-long) ease-out;
    }
    .page-overlay.active { opacity: 1; visibility: visible; }

    .promo-banner {
        background: linear-gradient(135deg, var(--verde) 0%, var(--verde-escuro) 100%);
        color: var(--branco); text-align: center; margin: 20px auto;
        padding: 50px 30px; border-radius: var(--border-radius-lg);
        font-size: 1.3em; font-weight: 500;
        box-shadow: 0 10px 30px rgba(40,160,96,0.3);
        opacity:0; transform: translateY(20px); /* Estado inicial para animação */
    }
    .promo-banner.in-view { /* Classe adicionada por JS */
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .promo-banner h2 { font-size: 1.8em; margin-bottom: 10px; font-weight: 700; }
    .promo-banner p { font-size: 0.9em; opacity:0.9; }

    .produtos-section { padding: 30px 0; }
    .produtos-section .section-title-produtos {
        font-size: clamp(1.8rem, 4vw, 2.8rem); color: var(--cinza-escuro);
        font-weight: 600; text-align: left; margin-bottom: 25px;
        border-bottom: 3px solid var(--verde); padding-bottom: 10px;
        display: inline-block; 
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
    }
    .product-card {
        background-color: var(--branco);
        border-radius: var(--border-radius-md);
        box-shadow: var(--sombra-padrao);
        overflow: hidden; 
        display: flex; flex-direction: column;
        transition: transform var(--transition-std), box-shadow var(--transition-std);
        opacity: 0; /* Para animação de entrada */
        transform: translateY(20px);
    }
     .product-card.in-view { /* Classe adicionada por JS */
        opacity: 1;
        transform: translateY(0);
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--sombra-hover-forte);
    }
    .product-card a.product-link {
        text-decoration: none; color: inherit; display: flex;
        flex-direction: column; height: 100%;
    }
    .product-image-container {
        width: 100%; padding-top: 85%; position: relative;
        background-color: var(--cinza-claro); 
    }
    .product-image-container img {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.4s ease-out;
    }
    .product-card:hover .product-image-container img { transform: scale(1.08); }
    .product-info {
        padding: 18px; text-align: left; flex-grow: 1;
        display: flex; flex-direction: column;
    }
    .product-name {
        font-weight: 600; font-size: 1.05em; color: var(--cinza-escuro);
        margin-bottom: 6px; line-height: 1.3;
        display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2;
        -webkit-box-orient: vertical; overflow: hidden;
        text-overflow: ellipsis; min-height: 2.6em; 
    }
    .product-price {
        font-size: 1.1em; font-weight: 700; color: var(--verde);
        margin-bottom: 8px;
    }
    .product-description-short {
        font-size: 0.85em; color: var(--cinza-texto); margin-bottom: 12px;
        line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3;
        -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;
        flex-grow: 1; 
    }
    .product-actions { margin-top: auto; padding-top: 10px; }
    .btn-ver-produto {
        width: 100%; padding: 10px 15px; font-size: 0.9em;
        text-align: center; background-color: var(--verde-claro-fundo);
        color: var(--verde-escuro); border: 1px solid var(--verde-claro-fundo);
        box-shadow: none;
    }
    .btn-ver-produto:hover {
        background-color: var(--verde); color: var(--branco);
        border-color: var(--verde);
        transform: translateY(-2px);
    }
    .no-products {
        grid-column: 1 / -1; text-align: center; font-size: 1.1em;
        padding: 40px 20px; color: var(--cinza-texto);
    }

    .animate-on-scroll {
        opacity: 0; transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .animate-on-scroll.in-view { opacity: 1; transform: translateY(0); }
    .delay-0-1s { transition-delay: 0.1s !important; }
    .delay-0-2s { transition-delay: 0.2s !important; }
    .delay-0-3s { transition-delay: 0.3s !important; }

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
    .footer-col a:hover { color: var(--verde); transform: translateX(3px); transition: transform var(--transition-fast); }
    .footer-copyright { text-align: center; padding-top: 40px; border-top: 1px solid #4a5c6a; color: #78909c; width: 90%; max-width: 1140px; margin: 0 auto; }

    @media (max-width: 992px) {
        .header-center { flex-direction: column; align-items: stretch; gap: 10px; margin-left: 15px; margin-right: 15px;}
        .menu-categorias-btn { width: auto; justify-content: flex-start; }
        .search-bar-header { max-width: none; }
        .site-header { padding-bottom: 10px; } 
        .site-header.scrolled { padding-bottom:10px; }
        main { padding-top: 150px; }
    }
    @media (max-width: 768px) {
        .header-container { flex-wrap: wrap; justify-content: space-between; } /* Logo e actions na mesma linha */
        .site-header .logo { margin-bottom: 0; } /* Sem margem se estiver na mesma linha */
        .header-center { width:100%; order: 3; margin-left:0; margin-right:0; padding-top: 10px; }
        .header-actions { order: 2; /* Actions antes do search em mobile */ }
        main { padding-top: 170px; /* Aumentar padding se o header crescer */ }

        .promo-banner { padding: 30px 20px; font-size: 1.1em; }
        .promo-banner h2 { font-size: 1.5em; }
        .produtos-section .section-title-produtos { font-size: clamp(1.5rem, 4vw, 2rem); }
        .product-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .product-card a.product-link { min-height: auto; }
    }
     @media (max-width: 480px) {
        .product-grid { grid-template-columns: 1fr; } /* Uma coluna */
        .header-actions { gap: 10px; }
        .header-actions img.action-icon { height: 24px; }
        .auth-buttons-header .btn { padding: 6px 10px; font-size: 0.8em; }
        main { padding-top: 160px; }
    }
  </style>
</head>
<body>

  <div class="page-overlay" id="pageOverlay" onclick="toggleCategoryList()"></div>

  <header class="site-header">
    <div class="header-container">
      <a href="loja.php">
        <img src="img/logo2.png" alt="Circuito Sustentável Logo" class="logo" />
      </a>

      <div class="header-center">
        <button class="menu-categorias-btn" onclick="toggleCategoryList()" aria-label="Abrir menu de categorias" aria-expanded="false">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="none">
            <path d="M3 4H21V6H3V4ZM3 11H21V13H3V11ZM3 18H21V20H3V18Z"></path>
          </svg>
          Categorias
        </button>
        <div class="search-bar-header">
          <form method="get" action="loja.php" style="display:flex; width:100%;">
            <input type="text" name="busca" placeholder="Pesquisar produtos, marcas..." value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>" aria-label="Campo de pesquisa"/>
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
            <img src="img/C+.png" alt="C+ Moedas" class="action-icon">
        </a>
        <a href="<?= htmlspecialchars($link_carrinho) ?>" aria-label="Carrinho de Compras">
            <img src="<?= htmlspecialchars($imagem_carrinho) ?>" alt="Carrinho" class="action-icon">
        </a>
      </div>
    </div>
  </header>

  <nav class="offcanvas-menu" id="category-list">
      <div class="offcanvas-header">
        <img src="img/logo2.png" alt="Circuito Sustentável" class="logo-menu" />
        <button class="close-offcanvas-btn" onclick="toggleCategoryList()" aria-label="Fechar menu de categorias">&#10005;</button>
      </div>
      <h3>CATEGORIAS</h3>
      <ul>
        <li><a href="loja.php?busca=Processador" onclick="toggleCategoryList()">Processadores</a></li>
        <li><a href="loja.php?busca=Placa de Vídeo" onclick="toggleCategoryList()">Placas de Vídeo</a></li>
        <li><a href="loja.php?busca=Memória RAM" onclick="toggleCategoryList()">Memórias RAM</a></li>
        <li><a href="loja.php?busca=Placa-Mãe" onclick="toggleCategoryList()">Placas-Mãe</a></li>
        <li><a href="loja.php?busca=Fonte de Alimentação" onclick="toggleCategoryList()">Fontes de Alimentação</a></li>
        <li><a href="loja.php?busca=Cooler" onclick="toggleCategoryList()">Coolers</a></li>
        <li><a href="loja.php?busca=Gabinete" onclick="toggleCategoryList()">Gabinetes</a></li>
        <li><a href="loja.php?busca=Armazenamento" onclick="toggleCategoryList()">Armazenamento (HDD/SSD)</a></li>
      </ul>
  </nav>


  <main>
    <div class="container-page">
      <!-- Banner achatado ANUNCIO.png -->
      <div style="width: 100%; margin: 20px auto; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(40,160,96,0.3);">
        <img src="img/ANUNCIO.png" alt="Anúncio" style="display: block; width: 100%; height: 260px; object-fit: cover;">
      </div>
      <div class="produtos-section">
        <h2 class="section-title-produtos animate-on-scroll">Nossos Produtos</h2>
        <div class="product-grid">
            <?php
            $busca = isset($_GET['busca']) ? trim($conexao->real_escape_string($_GET['busca'])) : '';
            $categoria_filtro = isset($_GET['categoria']) ? trim($conexao->real_escape_string($_GET['categoria'])) : '';

            $sql_prod = "SELECT * FROM Produto";
            $condicoes = [];
            if ($busca !== '') {
                $condicoes[] = "(nome LIKE '%$busca%' OR descricao LIKE '%$busca%')";
            }
            if ($categoria_filtro !== '' && $categoria_filtro !== 'todos') {
                // Assumindo que você tem uma coluna 'categoria' na tabela Produto
                $condicoes[] = "categoria = '$categoria_filtro'"; 
            }
            if (!empty($condicoes)) {
                $sql_prod .= " WHERE " . implode(' AND ', $condicoes);
            }
            
            $res_prod = $conexao->query($sql_prod);
            if ($res_prod && $res_prod->num_rows > 0):
              $delay_animacao = 0;
              while ($prod = $res_prod->fetch_assoc()):
                // Corrigido: sempre tenta pegar a primeira imagem válida, seja array ou string
                $img_path = 'img/sem-imagem.png';
                $imagens_json = $prod['imagens'];
                $primeira_imagem = '';

                if (!empty($imagens_json)) {
                    // Tenta decodificar como JSON
                    $imagens_array = json_decode($imagens_json, true);
                    if (is_array($imagens_array) && !empty($imagens_array)) {
                        foreach ($imagens_array as $img) {
                            $img = trim($img, " \t\n\r\0\x0B\"'/");
                            if ($img !== '') {
                                $primeira_imagem = $img;
                                break;
                            }
                        }
                    } else {
                        // Fallback: string separada por vírgula
                        foreach ($imagens_lista as $img) {
                            $img = trim($img, " \t\n\r\0\x0B\"'/");
                            if ($img !== '') {
                                $primeira_imagem = $img;
                                break;
                            }
                        }
                    }
                }

                if ($primeira_imagem !== '') {
                    if (
                        strpos($primeira_imagem, 'uploads_produtos/') === 0 ||
                        strpos($primeira_imagem, 'img/') === 0
                    ) {
                        $img_path = $primeira_imagem;
                    } else {
                        
                        $img_path = 'uploads_produtos/' . $primeira_imagem;
                    }
                }

                // Lógica de delay para animação escalonada
                $anim_delay_class = 'delay-0-' . (($delay_animacao % 3) + 1) . 's';
                if ($delay_animacao >=3 ) $anim_delay_class = '';
            ?>
            <div class="product-card animate-on-scroll <?= $anim_delay_class ?>">
              <a href="aba_produto.php?id=<?= $prod['id_produto'] ?>" class="product-link">
                <div class="product-image-container">
                  <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                </div>
                <div class="product-info">
                  <h3 class="product-name"><?= htmlspecialchars($prod['nome']) ?></h3>
                  <p class="product-price">R$ <?= number_format($prod['preco'],2,',','.') ?></p>
                  <p class="product-description-short"><?= htmlspecialchars(mb_strimwidth($prod['descricao'],0,70,'...')) ?></p>
                  <div class="product-actions">
                    <span class="btn btn-primary btn-ver-produto">Ver Detalhes</span> </div>
                </div>
              </a>
            </div>
            <?php 
              $delay_animacao++;
              endwhile; 
            else: ?>
            <div class="no-products">Nenhum produto encontrado <?= ($busca || ($categoria_filtro && $categoria_filtro !== 'todos')) ? "para sua busca/filtro." : "cadastrado no momento." ?></div>
            <?php endif; ?>
        </div>
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
    <div class="footer-copyright">
      &copy; <?php echo date("Y"); ?> Circuito Sustentável Inc. Todos os direitos reservados.
    </div>
  </footer>

  <script>
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

    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { threshold: 0.1 });
    animatedElements.forEach(el => observer.observe(el));

    const categoryList = document.getElementById('category-list');
    const pageOverlay = document.getElementById('pageOverlay');
    const menuCategoriasBtn = document.querySelector('.menu-categorias-btn'); 

    function toggleCategoryList() {
        const isVisible = categoryList.classList.contains('open');
        if (isVisible) {
            categoryList.classList.remove('open');
            pageOverlay.classList.remove('active');
            if(menuCategoriasBtn) menuCategoriasBtn.setAttribute('aria-expanded', 'false');
        } else {
            categoryList.classList.add('open');
            pageOverlay.classList.add('active');
            if(menuCategoriasBtn) menuCategoriasBtn.setAttribute('aria-expanded', 'true');
        }
    }
  </script>
</body>
</html>