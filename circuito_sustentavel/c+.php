<?php
session_start();
include 'conexao.php'; // Assume que $conexao é configurado aqui

$nome_usuario = "Visitante"; // Padrão
$foto_perfil_usuario = "img/user.png"; // Sempre usa o arquivo padrão
$link_perfil = "login.php";
$usuario_logado = false;

if (isset($_SESSION['usuario_id'])) {
    $usuario_logado = true;
    $id_entidade = $_SESSION['usuario_id'];
    $sql = "SELECT nome FROM Cliente WHERE id_cliente = ?";
    $link_perfil = 'usuario.php';
} elseif (isset($_SESSION['vendedor_id'])) {
    $usuario_logado = true;
    $id_entidade = $_SESSION['vendedor_id'];
    // Para vendedor, busca apenas o nome na tabela Vendedor
    $sql_nome = "SELECT nome FROM Vendedor WHERE id_vendedor = ?";
    $stmt_nome = $conexao->prepare($sql_nome);
    if ($stmt_nome) {
        $stmt_nome->bind_param("i", $id_entidade);
        $stmt_nome->execute();
        $result_nome = $stmt_nome->get_result();
        if ($user_data_nome = $result_nome->fetch_assoc()) {
            $nome_usuario = htmlspecialchars($user_data_nome['nome']);
        }
        $stmt_nome->close();
    }
    $link_perfil = 'vendedor.php';
}

if ($usuario_logado && !isset($sql_nome)) { 
    $stmt = $conexao->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_entidade);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user_data = $result->fetch_assoc()) {
            $nome_usuario = htmlspecialchars($user_data['nome']);
        }
        $stmt->close();
    }
}



$dicas_carbono = [
    "🍃Reduza o consumo de carne vermelha. A pecuária é uma grande fonte de emissões de metano.",
    "🍃Opte por transporte público, bicicleta ou caminhada sempre que possível em vez de usar o carro.",
    "🍃Economize energia em casa: desligue luzes e aparelhos quando não estiver usando, use lâmpadas LED.",
    "🍃Evite o desperdício de alimentos. Planeje suas compras e refeições.",
    "🍃Plante árvores! Elas absorvem CO2 da atmosfera.",
    "🍃Reduza, Reutilize, Recicle. Siga os 3 Rs para diminuir o lixo.",
    "🍃Use menos água quente. Aquecer água consome muita energia.",
    "🍃Considere fontes de energia renovável para sua casa, se possível.",
    "🍃Compre localmente. Produtos locais geralmente têm uma pegada de carbono menor devido ao transporte.",
    "🍃Informe-se e conscientize outras pessoas sobre a importância de reduzir as emissões de carbono."
];
$dica_do_dia = $dicas_carbono[array_rand($dicas_carbono)]; 


$cupons_disponiveis = [
    ['codigo' => 'VERDE15', 'descricao' => '15% OFF em toda a loja.', 'validade' => '01/07/2025'],
    ['codigo' => 'ECOCASA10', 'descricao' => '20% OFF em toda a loja.', 'validade' => '01/07/2025'],
    ['codigo' => 'BEMVINDOSAOCS', 'descricao' => 'R$30 OFF em toda a loja', 'validade' => '01/07/2025'],
];

?>
<!DOCTYPE html>
<html lang="pt-BR" class="snap-container">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C+ | Vantagens - Circuito Sustentável</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <style>
        :root {
            --verde: #28a060;
            --verde-escuro: #1e7c4b;
            --verde-claro-fundo: #f0f9f4;
            --verde-destaque-parallax: #8cffc7; 
            --cinza-claro: #f4f6f8;
            --cinza-texto: #5f6c7b;
            --cinza-escuro: #2c3e50;
            --branco: #ffffff;
            --sombra-padrao: 0 8px 25px rgba(0,0,0, 0.07);
            --sombra-hover-forte: 0 10px 30px rgba(40, 160, 96, 0.15);
            --border-radius-sm: 4px;
            --border-radius-md: 8px;
            --border-radius-lg: 16px;
            --transition-fast: 0.2s;
            --transition-std: 0.3s;
            --transition-long: 0.5s; 
            --font-principal: 'Poppins', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html.snap-container {
            scroll-behavior: smooth;
            scroll-snap-type: y proximity; /* ALTERADO PARA PROXIMITY */
        }

        body {
            font-family: var(--font-principal);
            line-height: 1.6;
            color: var(--cinza-texto);
            background-color: var(--branco);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex; /* Para o sticky footer */
            flex-direction: column; /* Para o sticky footer */
            min-height: 100vh; /* Para o sticky footer */
        }
        
        main.cplus-main {
            width: 100%;
            flex-grow: 1; /* Para o sticky footer */
        }

        .scroll-snap-section {
            scroll-snap-align: start;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 100px 20px;
            position: relative;
            overflow: hidden;
        }
        .scroll-snap-section:first-child {
            padding-top: 120px; 
        }
        /* A última seção de snap precisa de mais padding inferior para o footer não ser puxado */
        .scroll-snap-section#cplus-interactive-section {
             min-height: 100vh; 
             justify-content: flex-start; 
             padding-top: 80px; 
             padding-bottom: 200px !important; /* AUMENTADO PARA DAR ESPAÇO AO FOOTER */
        }


        .container { width: 90%; max-width: 1000px; margin: 0 auto; text-align: center; }

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

        .main-nav-cplus { display: flex; align-items:center; gap: 20px; }
        .main-nav-cplus a, .main-nav-cplus .nav-user-name {
            color: var(--cinza-escuro); font-weight: 500; text-decoration: none;
            font-size: 0.95em; padding: 8px 0;
        }
        .main-nav-cplus a { position: relative; }
        .main-nav-cplus a::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 0%; height: 2px;
            background-color: var(--verde);
            transition: width var(--transition-std) ease-out;
        }
        .main-nav-cplus a:hover::after, .main-nav-cplus a.active::after { width: 100%; }
        .main-nav-cplus a:hover { color: var(--verde); }
        .nav-user-profile { display: flex; align-items: center; gap: 10px; }
        .nav-user-profile img { height: 32px; width: 32px; border-radius: 50%; object-fit: cover;}
        .btn-logout {
            background: none;
            border: none;
            color: var(--cinza-escuro);
            padding: 0;
            margin-left: 8px;
            font-size: 1.2em;
            box-shadow: none;
            border-radius: 0;
            transition: color var(--transition-fast);
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .btn-logout:hover {
            color: var(--verde);
            background: none;
        }

        .section-title {
            font-size: clamp(2.2rem, 5vw, 3.5rem); color: var(--cinza-escuro);
            font-weight: 700; text-align: center; margin-bottom: 20px;
        }
        .section-subtitle {
            font-size: clamp(1rem, 2.5vw, 1.3rem); color: var(--cinza-texto);
            text-align: center; max-width: 700px; margin: 0 auto 40px;
            line-height: 1.8;
        }
        .btn {
            display: inline-block; padding: 12px 28px; font-weight: 600;
            text-decoration: none; border-radius: var(--border-radius-md);
            transition: all var(--transition-std); cursor: pointer; 
            border: 2px solid transparent; font-size: 1em;
            box-shadow: var(--sombra-padrao); letter-spacing: 0.5px;
        }
        .btn-primary { background-color: var(--verde); color: var(--branco); }
        .btn-primary:hover {
            background-color: var(--verde-escuro); transform: translateY(-3px);
            box-shadow: var(--sombra-hover-forte);
        }
        .btn-secondary {
            background-color: var(--cinza-claro); color: var(--cinza-escuro);
            border-color: #ccd0d5; /* Borda mais sutil para secundário */
        }
        .btn-secondary:hover {
            background-color: #e9ecef; /* Cinza um pouco mais escuro no hover */
             transform: translateY(-2px);
        }

        .cplus-hero-section {
            background: linear-gradient(135deg, var(--verde-claro-fundo) 0%, var(--cinza-claro) 100%);
            text-align: center;
        }
        .cplus-hero-section .section-title span { color: var(--verde); }
        .user-avatar-welcome {
            width: 100px; height: 100px; border-radius: 50%;
            object-fit: cover; margin-bottom: 20px;
            border: 4px solid var(--branco);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        #cplus-interactive-section .container {
            max-width: 1100px; 
            position: relative; 
            min-height: 60vh; 
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .cplus-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 30px;
            width: 100%;
            transition: opacity var(--transition-std) ease-out, transform var(--transition-std) ease-out, visibility 0s var(--transition-std);
            visibility: visible;
        }
        .cplus-options-grid.view-hidden {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
            visibility: hidden; 
        }

        .option-card {
            background-color: var(--branco);
            padding: 30px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--sombra-padrao);
            text-align: center;
            cursor: pointer;
            transition: transform var(--transition-std), box-shadow var(--transition-std);
        }
        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--sombra-hover-forte);
        }
        .option-card-icon { margin-bottom: 20px; }
        .option-card-icon svg { width: 48px; height: 48px; color: var(--verde); } 
        .option-card h3 {
            font-size: 1.5em; color: var(--cinza-escuro);
            font-weight: 600; margin-bottom: 10px;
        }
        .option-card p { font-size: 0.9em; color: var(--cinza-texto); margin-bottom:0; line-height: 1.5;}


        .cplus-details-view {
            display: none; 
            opacity: 0;
            transform: translateY(20px); 
            transition: opacity var(--transition-std) ease-out, transform var(--transition-std) ease-out;
            width: 100%;
            text-align: left;
            padding: 25px;
            background-color: var(--branco);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--sombra-padrao);
        }
        .cplus-details-view.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--cinza-claro);
        }
        .details-header h2 {
            font-size: 1.8em; color: var(--verde-escuro);
            margin: 0; display:flex; align-items:center; gap: 10px;
        }
        .details-header h2 svg { width: 28px; height: 28px; } /* Ícone no título da view */

        .btn-back-cplus {
            font-size: 0.9em;
            padding: 8px 18px;
        }

        .coupons-list { list-style: none; padding: 0; }
        .coupon-item {
            background-color: var(--verde-claro-fundo);
            padding: 15px 20px;
            border-radius: var(--border-radius-md);
            margin-bottom: 15px;
            border-left: 4px solid var(--verde);
        }
        .coupon-item .coupon-code {
            font-weight: 700; color: var(--verde-escuro);
            font-size: 1.2em; display: block; margin-bottom: 5px;
        }
        .coupon-item .coupon-desc { font-size: 0.9em; margin-bottom: 5px; }
        .coupon-item .coupon-store, .coupon-item .coupon-expiry {
            font-size: 0.8em; color: var(--cinza-texto);
        }

        .dicas-list { list-style: none; padding: 0; }
        .dica-item {
            padding: 12px 0; /* Reduzido padding vertical */
            border-bottom: 1px dashed var(--cinza-medio);
            display: flex; 
            align-items: flex-start; /* Alinha ícone e texto no topo */
            gap: 12px;
        }
        .dica-item:last-child { border-bottom: none; }
        .dica-item-icon svg { 
             width: 24px; height: 24px; color: var(--verde); flex-shrink:0; margin-top: 3px;
        }
        .dica-item-text { font-size: 0.95em; line-height: 1.6; } 
        
        .view-slide-out-left { animation: slideOutLeft var(--transition-std) forwards ease-in-out; }
        .view-slide-in-right { animation: slideInRight var(--transition-std) forwards ease-in-out; }
        .view-slide-out-right { animation: slideOutRight var(--transition-std) forwards ease-in-out; }
        .view-slide-in-left { animation: slideInLeft var(--transition-std) forwards ease-in-out; }

        @keyframes slideOutLeft {
            from { opacity: 1; transform: translateX(0) scale(1); }
            to { opacity: 0; transform: translateX(-30px) scale(0.95); visibility: hidden; }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px) scale(0.95); visibility: visible; }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes slideOutRight {
            from { opacity: 1; transform: translateX(0) scale(1); }
            to { opacity: 0; transform: translateX(30px) scale(0.95); visibility: hidden; }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px) scale(0.95); visibility: visible; }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        .site-footer-bottom {
            background-color: var(--cinza-escuro); color: #b0bec5;
            padding: 70px 0 40px; font-size: 0.95em;
            /* margin-top: -9%; // Removido, pois body é flex e main é flex-grow */
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

        .animate-on-scroll {
            opacity: 0; transform: translateY(30px);
            transition: opacity 0.7s ease-out, transform 0.7s ease-out;
        }
        .animate-on-scroll.in-view { opacity: 1; transform: translateY(0); }
        .delay-0-2s { transition-delay: 0.2s !important; }
        .delay-0-4s { transition-delay: 0.4s !important; }
        .delay-0-6s { transition-delay: 0.6s !important; }

        @media (max-width: 768px) {
            .scroll-snap-section { padding: 60px 15px; }
            .scroll-snap-section:first-child { padding-top: 120px; /* Aumentar para header responsivo */ }
            .scroll-snap-section#cplus-interactive-section { padding-top: 60px; padding-bottom: 120px !important; }
            .section-title { font-size: clamp(1.8rem, 5vw, 2.2rem); } /* Reduzido */
            .section-subtitle { font-size: clamp(0.9rem, 2.5vw, 1.05rem); } /* Reduzido */
            .cplus-options-grid { grid-template-columns: 1fr; }
            .header-container { flex-wrap: wrap; justify-content: center; }
            .site-header .logo { margin-bottom: 10px; }
            .main-nav-cplus { width: 100%; justify-content: center; margin-top:10px; gap: 10px; flex-wrap:wrap;}
            .main-nav-cplus a, .main-nav-cplus .nav-user-name { font-size: 0.85em; padding: 6px 8px;} /* Reduzido */
            .btn-logout { padding: 6px 12px; font-size: 0.8em;}  /* Reduzido */
             .details-header h2 { font-size: 1.5em; }
             .dica-item-text { font-size: 0.9em; }
        }


    </style>
</head>
<body>

    <header class="site-header">
        <div class="header-container">
            
                <img src="img/logo2.png" alt="Circuito Sustentável Logo" class="logo" />
            
            <nav class="main-nav-cplus">
                <?php if ($usuario_logado): ?>
                    
                    <a href="<?= htmlspecialchars($link_perfil) ?>" class="nav-user-profile">
                        <img src="<?= htmlspecialchars($foto_perfil_usuario) ?>" alt="Minha Conta">
                        <span>Minha Conta</span>
                    </a>
                    <a href="loja.php" class="btn-logout" aria-label="Sair">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=c+.php">Login</a>
                    <a href="cadastro.php">Cadastrar</a>
                <?php endif; ?>
            </nav>
            </div>
    </header>

    <main class="cplus-main">
        <section id="cplus-hero" class="scroll-snap-section cplus-hero-section">
            <div class="container">
                <?php if ($usuario_logado): ?>
                    <img src="<?= htmlspecialchars($foto_perfil_usuario) ?>" alt="Avatar de <?= $nome_usuario ?>" class="user-avatar-welcome animate-on-scroll">
                    <h1 class="section-title animate-on-scroll delay-0-2s">Bem-vindo(a) ao <span>C+</span>, <?= $nome_usuario ?>!</h1>
                    <p class="section-subtitle animate-on-scroll delay-0-4s">Explore seus benefícios exclusivos, dicas personalizadas e cupons para uma jornada mais sustentável e recompensadora.</p>
                <?php else: ?>
                    <h1 class="section-title animate-on-scroll">Descubra o C<span style="color:var(--verde);">+</span></h1>
                    <p class="section-subtitle animate-on-scroll delay-0-2s">Uma área exclusiva com vantagens para sua jornada sustentável. <a href="login.php?redirect=c+.php" style="color:var(--verde); font-weight:600;">Faça login</a> ou <a href="cadastro.php" style="color:var(--verde); font-weight:600;">cadastre-se</a> para acessar.</p>
                <?php endif; ?>
                 <a href="#cplus-interactive-section" class="btn btn-primary animate-on-scroll delay-0-6s" style="margin-top:10px;">Explorar C+</a>
            </div>
        </section>

        <section id="cplus-interactive-section" class="scroll-snap-section">
            <div class="container">
                <div id="cplusOptionsView" class="cplus-options-grid">
                    <div class="option-card animate-on-scroll" data-view="cupons">
                        <div class="option-card-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                             <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                            </svg>
                        </div>
                        <h3>Meus Cupons</h3>
                        <p>Acesse descontos exclusivos em produtos e serviços sustentáveis.</p>
                    </div>
                    <div class="option-card animate-on-scroll delay-0-2s" data-view="dicas">
                        <div class="option-card-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </div>
                        <h3>Dicas Diárias</h3>
                        <p>Receba conselhos práticos para reduzir sua pegada de carbono no dia a dia.</p>
                    </div>
                </div>

                <div id="cuponsDetailsView" class="cplus-details-view">
                    <div class="details-header">
                        <h2>Seus Cupons Exclusivos</h2>
                        <button class="btn btn-secondary btn-back-cplus" data-targetview="cplusOptionsView">Voltar</button>
                    </div>
                    <ul class="coupons-list">
                        <?php if($usuario_logado && !empty($cupons_disponiveis)): ?>
                            <?php foreach($cupons_disponiveis as $cupom): ?>
                                <li class="coupon-item">
                                    <span class="coupon-code">CÓDIGO: <?= htmlspecialchars($cupom['codigo']) ?></span>
                                    <p class="coupon-desc"><?= htmlspecialchars($cupom['descricao']) ?></p>
                                    <span class="coupon-expiry">Validade: <?= htmlspecialchars($cupom['validade']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php elseif ($usuario_logado): ?>
                            <p>Você ainda não possui cupons. Continue participando da comunidade!</p>
                        <?php else: ?>
                             <p>Faça login para ver seus cupons disponíveis.</p>
                        <?php endif; ?>
                    </ul>
                </div>

                <div id="dicasDetailsView" class="cplus-details-view">
                    <div class="details-header">
                        <h2>Dicas Diárias para Você</h2>
                        <button class="btn btn-secondary btn-back-cplus" data-targetview="cplusOptionsView">Voltar</button>
                    </div>
                    <ul class="dicas-list">
                        <?php if($usuario_logado): ?>
                            <li class="dica-item" style="font-weight:600; color:var(--verde-escuro);">Sua dica de hoje:</li>
                            <li class="dica-item"><?= htmlspecialchars($dica_do_dia) ?></li>
                            <h5 style="margin-top:20px; margin-bottom:10px; color: var(--cinza-escuro);">Mais dicas para reduzir emissões:</h5>
                            <?php 
                                // Exibe mais algumas dicas além da dica do dia
                                $count = 0;
                                foreach($dicas_carbono as $dica): 
                                    if ($dica == $dica_do_dia && $count == 0) { // Evita repetir a dica do dia logo em seguida, se for a primeira
                                        // Não faz nada, já exibiu
                                    } else {
                                        echo "<li class='dica-item'>" . htmlspecialchars($dica) . "</li>";
                                        $count++;
                                    }
                                    if ($count >= 4) break; // Limita a mais 4 dicas
                                endforeach; 
                            ?>
                        <?php else: ?>
                             <p>Faça login para receber dicas personalizadas.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer-bottom">
        <div class="container footer-content-grid">
            <div class="footer-col">
                <h4>Circuito Sustentável C+</h4>
                <p>Sua central de vantagens e conhecimento para um mundo mais verde.</p>
            </div>
            <div class="footer-col">
                <h4>Links Úteis</h4>
                <a href="loja.php">Loja</a>
                <a href="assinatura_usuario.php">Assinatura Premium</a>
                <a href="suporte.html">Suporte</a>
            </div>
            <div class="footer-col">
                <h4>Contato</h4>
                <p>📧 cplus@circuitosustentavel.com</p>
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

    // Lógica de Transição de Views (Cupons/Dicas)
    const optionCards = document.querySelectorAll('.option-card');
    const mainOptionsView = document.getElementById('cplusOptionsView');
    const detailViews = document.querySelectorAll('.cplus-details-view');
    const backButtons = document.querySelectorAll('.btn-back-cplus');

    let currentVisibleDetailView = null;

    function showDetailView(viewId) {
        const targetView = document.getElementById(viewId + "DetailsView");

        if (mainOptionsView) {
            mainOptionsView.classList.add('view-slide-out-left');
            mainOptionsView.addEventListener('animationend', function handler() {
                mainOptionsView.style.display = 'none';
                mainOptionsView.classList.remove('view-slide-out-left');
                mainOptionsView.removeEventListener('animationend', handler);

                if (targetView) {
                    detailViews.forEach(v => { 
                        v.style.display = 'none'; 
                        v.classList.remove('active', 'view-slide-in-right', 'view-slide-in-left', 'view-slide-out-right');
                    });
                    targetView.style.display = 'block'; // Garante display block antes de animar
                    targetView.classList.add('active', 'view-slide-in-right');
                    currentVisibleDetailView = targetView;
                }
            }, { once: true });
        }
    }

    function showMainOptionsView(originView) {
        if (originView && mainOptionsView) {
            originView.classList.remove('view-slide-in-right', 'view-slide-in-left');
            originView.classList.add('view-slide-out-right');
            
            originView.addEventListener('animationend', function handler() {
                originView.style.display = 'none';
                originView.classList.remove('active', 'view-slide-out-right');
                originView.removeEventListener('animationend', handler);

                mainOptionsView.style.display = 'grid'; // Volta para grid
                mainOptionsView.classList.remove('view-hidden'); // Se usou antes
                mainOptionsView.classList.add('view-slide-in-left');
                currentVisibleDetailView = null;
                 // Remove a classe de animação de entrada após ela ocorrer
                setTimeout(() => mainOptionsView.classList.remove('view-slide-in-left'), 500);


            }, { once: true });
        } else if (mainOptionsView) { // Caso genérico para voltar
             detailViews.forEach(v => { v.style.display = 'none'; v.classList.remove('active');});
             mainOptionsView.style.display = 'grid';
             mainOptionsView.classList.remove('view-hidden');
             currentVisibleDetailView = null;
        }
    }


    optionCards.forEach(card => {
        card.addEventListener('click', function() {
            const viewName = this.dataset.view;
            if (viewName) {
                showDetailView(viewName);
            }
        });
    });

    backButtons.forEach(button => {
        button.addEventListener('click', function() {
            if(currentVisibleDetailView){
                showMainOptionsView(currentVisibleDetailView);
            }
        });
    });

</script>
</body>
</html>
<?php if(isset($conexao)) $conexao->close(); ?>