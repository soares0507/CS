<?php
session_start();
include 'conexao.php';
$usuario_id = $_SESSION['usuario_id'] ?? 1; 
$nome_usuario = "Usuário";


$sql = "SELECT nome FROM Cliente WHERE id_cliente = ?";
$stmt = $conexao->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($nome_usuario_bd);
    if ($stmt->fetch() && $nome_usuario_bd) {
        $nome_usuario = $nome_usuario_bd;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Assinante</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <style>
      /* Reset Básico e Configurações Globais */
:root {
    --verde: #28a060;
    --cinza-claro: #d4d3c8;
    --cinza-texto: #555;
    --cinza-escuro: #333;
    --branco: #ffffff;
    --sombra-suave: 0 4px 15px rgba(0, 0, 0, 0.08);
    --sombra-card-hover: 0 8px 25px rgba(40, 160, 96, 0.15);
    --border-radius: 8px;
    --transition-speed: 0.3s;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth; /* Rolagem suave para âncoras */
}

body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    color: var(--cinza-texto);
    background-color: var(--branco); /* Fundo branco para mais "espaço em branco" */
    overflow-x: hidden; /* Evita scroll horizontal indesejado */
}

.container {
    width: 90%;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Loader Elegante */
.loader-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--branco);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    transition: opacity var(--transition-speed) ease, visibility var(--transition-speed) ease;
}

.loader {
    border: 6px solid var(--cinza-claro);
    border-top: 6px solid var(--verde);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loaded .loader-wrapper {
    opacity: 0;
    visibility: hidden;
}


/* Cabeçalho */
.site-header {
    background-color: var(--branco);
    padding: 20px 0;
    box-shadow: var(--sombra-suave);
    position: sticky; /* Cabeçalho fixo ao rolar */
    top: 0;
    z-index: 1000;
    transition: background-color var(--transition-speed) ease;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo a {
    text-decoration: none;
    color: var(--verde);
    font-size: 1.8em;
    font-weight: 700;
    transition: color var(--transition-speed) ease;
}

.logo a:hover {
    color: var(--cinza-escuro);
}

.main-nav ul {
    list-style: none;
    display: flex;
}

.main-nav ul li {
    margin-left: 25px;
}

.main-nav ul li a {
    text-decoration: none;
    color: var(--cinza-texto);
    font-weight: 400;
    padding: 5px 0;
    position: relative; /* Para o efeito de sublinhado */
    transition: color var(--transition-speed) ease;
}

.main-nav ul li a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: var(--verde);
    transition: width var(--transition-speed) ease;
}

.main-nav ul li a:hover,
.main-nav ul li a.active {
    color: var(--verde);
}

.main-nav ul li a:hover::after,
.main-nav ul li a.active::after {
    width: 100%;
}

.logout-button {
    background-color: var(--verde);
    color: var(--branco);
    padding: 8px 18px;
    border-radius: var(--border-radius);
    transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease;
}

.logout-button:hover {
    background-color: #1e7c4b; /* Tom mais escuro do verde */
    transform: translateY(-2px);
    color: var(--branco) !important; /* Manter a cor do texto */
}
.logout-button:hover::after { /* Remove sublinhado no hover do botão */
    width: 0;
}


/* Menu Hamburguer (Mobile) */
.menu-toggle {
    display: none; /* Oculto em desktop */
    background: none;
    border: none;
    cursor: pointer;
    padding: 10px;
}

.hamburger {
    display: block;
    width: 25px;
    height: 3px;
    background-color: var(--cinza-escuro);
    position: relative;
    transition: transform var(--transition-speed) ease;
}

.hamburger::before,
.hamburger::after {
    content: '';
    position: absolute;
    width: 25px;
    height: 3px;
    background-color: var(--cinza-escuro);
    left: 0;
    transition: transform var(--transition-speed) ease, top var(--transition-speed) ease;
}

.hamburger::before {
    top: -8px;
}

.hamburger::after {
    top: 8px;
}

/* Estado ativo do menu hamburguer (X) */
.menu-toggle.active .hamburger {
    transform: rotate(45deg);
}
.menu-toggle.active .hamburger::before {
    top: 0;
    transform: rotate(90deg);
}
.menu-toggle.active .hamburger::after {
    top: 0;
    transform: rotate(90deg);
    opacity: 0; /* Esconde a barra do meio */
}


/* Conteúdo Principal */
.main-content {
    padding: 40px 0;
}

.content-section {
    padding: 60px 0;
    border-bottom: 1px solid var(--cinza-claro); /* Divisor sutil */
}
.content-section:last-child {
    border-bottom: none;
}

.welcome-section {
    background-color: var(--cinza-claro); /* Leve destaque para a boas-vindas */
    text-align: center;
    padding: 80px 0;
}

.section-title {
    font-size: 2.5em;
    color: var(--cinza-escuro);
    margin-bottom: 15px;
    font-weight: 600;
}

.section-subtitle {
    font-size: 1.2em;
    color: var(--cinza-texto);
    max-width: 600px;
    margin: 0 auto 30px;
}

/* Cards de Informação */
.info-cards .container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.card {
    background-color: var(--branco);
    border: 1px solid transparent; /* Para transição suave da borda */
    border-radius: var(--border-radius);
    padding: 30px;
    text-align: center;
    box-shadow: var(--sombra-suave);
    transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease, border-color var(--transition-speed) ease;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: var(--sombra-card-hover);
    border-color: var(--verde); /* Borda verde sutil no hover */
}

.card-icon {
    margin-bottom: 20px;
    color: var(--verde); /* Cor dos ícones */
}

.card-icon svg { /* Para garantir que o SVG herde a cor */
    fill: currentColor;
}

.card-title {
    font-size: 1.6em;
    color: var(--cinza-escuro);
    margin-bottom: 15px;
    font-weight: 600;
}

.card p {
    font-size: 1em;
    color: var(--cinza-texto);
    margin-bottom: 25px;
    min-height: 50px; /* Garante alinhamento se os textos tiverem tamanhos diferentes */
}

/* Botões */
.btn {
    display: inline-block;
    padding: 12px 28px;
    text-decoration: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background-color: var(--verde);
    color: var(--branco);
}

.btn-primary:hover {
    background-color: #1e7c4b; /* Verde mais escuro */
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(40, 160, 96, 0.3);
}

/* Rodapé */
.site-footer-bottom {
    background-color: var(--cinza-escuro);
    color: var(--cinza-claro);
    text-align: center;
    padding: 30px 0;
    font-size: 0.9em;
}

.site-footer-bottom a {
    color: var(--branco);
    text-decoration: none;
    transition: color var(--transition-speed) ease;
}

.site-footer-bottom a:hover {
    color: var(--verde);
}

/* Responsividade */
@media (max-width: 992px) {
    .section-title {
        font-size: 2.2em;
    }
    .card-title {
        font-size: 1.4em;
    }
}

@media (max-width: 768px) {
    .header-container {
        flex-wrap: wrap; /* Permite que o menu vá para baixo */
    }

    .menu-toggle {
        display: block; /* Mostra o botão hamburguer */
        order: 1; /* Coloca o botão à direita do logo */
    }

    .main-nav {
        width: 100%;
        max-height: 0; /* Começa fechado */
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Efeito de "mola" */
        order: 2; /* Coloca o menu abaixo do logo e hamburguer */
        background-color: var(--branco); /* Fundo para o menu mobile */
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        border-top: 1px solid var(--cinza-claro);
        margin-top: 10px;
    }

    .main-nav.active {
        max-height: 500px; /* Altura suficiente para os itens */
        padding: 10px 0;
    }

    .main-nav ul {
        flex-direction: column;
        align-items: center;
    }

    .main-nav ul li {
        margin: 15px 0;
        width: 100%;
        text-align: center;
    }

    .main-nav ul li a::after { /* Ajuste no sublinhado para mobile */
        left: 50%;
        transform: translateX(-50%);
    }
    .main-nav ul li a:hover::after,
    .main-nav ul li a.active::after {
        width: 50%; /* Sublinhado menor e centralizado */
    }

    .logout-button {
        width: calc(100% - 40px);
        margin: 10px 20px;
        text-align: center;
    }

    .welcome-section {
        padding: 60px 0;
    }
    .section-title {
        font-size: 2em;
    }
    .section-subtitle {
        font-size: 1.1em;
    }

    .info-cards .container {
        grid-template-columns: 1fr; /* Uma coluna em telas menores */
    }
}

@media (max-width: 480px) {
    .logo a {
        font-size: 1.5em;
    }
    .section-title {
        font-size: 1.8em;
    }
    .section-subtitle {
        font-size: 1em;
    }
    .btn {
        padding: 10px 22px;
        font-size: 0.9em;
    }
    .card {
        padding: 25px;
    }
}
    </style>
</head>
<body>
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <header class="site-header">
        <div class="container header-container">
            <div class="logo">
                <a href="#">
                    <img src="img/logo2.png" alt="Logo" style="height:48px;vertical-align:middle;">
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="loja.php" class="active">Loja</a></li>
                    <li><a href="rs.php">Rede Social</a></li>
                    <li><a href="#assinaturas">Minha Assinatura</a></li>
                    <li><a href="suport.php">Ajuda</a></li>
                    <li><a href="#" class="logout-button">Sair</a></li>
                </ul>
            </nav>
            <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
                <span class="hamburger"></span>
            </button>
        </div>
    </header>

    <main class="main-content">
        <section id="dashboard" class="content-section welcome-section">
            <div class="container">
                <h1 class="section-title">Bem-vindo(a) de volta, <?php echo htmlspecialchars($nome_usuario); ?>!</h1>
                <p class="section-subtitle">Aqui você pode gerenciar sua assinatura e acessar conteúdos exclusivos.</p>
            </div>
        </section>

        <section class="content-section info-cards">
            <div class="container">
                <div class="card">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="48px" height="48px"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                    </div>
                    <h2 class="card-title">Minha Assinatura</h2>
                    <p>Visualize detalhes do seu plano, data de renovação e histórico de pagamentos.</p>
                    <a href="#assinaturas" class="btn btn-primary">Ver Detalhes</a>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="48px" height="48px"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 7h10v2H7zm0 4h10v2H7zm0 4h7v2H7z"/></svg>
                    </div>
                    <h2 class="card-title">Conteúdo Exclusivo</h2>
                    <p>Acesse vídeos, artigos e materiais disponíveis apenas para assinantes.</p>
                    <a href="#conteudo" class="btn btn-primary">Explorar Conteúdo</a>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="48px" height="48px"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <h2 class="card-title">Gerenciar Perfil</h2>
                    <p>Atualize suas informações pessoais, senha e preferências de comunicação.</p>
                    <a href="#perfil" class="btn btn-primary">Editar Perfil</a>
                </div>
            </div>
        </section>

        </main>

    <footer class="site-footer-bottom">
        <div class="container">
            <p>&copy; 2024 Seu Nome ou Nome da Empresa. Todos os direitos reservados.</p>
            <p><a href="#termos">Termos de Uso</a> | <a href="#privacidade">Política de Privacidade</a></p>
        </div>
    </footer>
<script>
  // Loader
window.addEventListener('load', () => {
    const loaderWrapper = document.querySelector('.loader-wrapper');
    if (loaderWrapper) {
        document.body.classList.add('loaded');
    }
});

// Menu Mobile
const menuToggle = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('.main-nav');

if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', () => {
        mainNav.classList.toggle('active');
        menuToggle.classList.toggle('active');
        const isExpanded = mainNav.classList.contains('active');
        menuToggle.setAttribute('aria-expanded', isExpanded);
        if (isExpanded) {
            menuToggle.setAttribute('aria-label', 'Fechar menu');
        } else {
            menuToggle.setAttribute('aria-label', 'Abrir menu');
        }
    });

    // Fecha o menu ao clicar em um link (opcional, mas bom para SPAs ou navegação na mesma página)
    const navLinks = mainNav.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (mainNav.classList.contains('active')) {
                mainNav.classList.remove('active');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-label', 'Abrir menu');
            }
        });
    });
}

// Adiciona a classe 'active' ao link de navegação da seção visível (opcional)
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.content-section');
    const navLi = document.querySelectorAll('.main-nav ul li a');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            // Adiciona uma margem para ativar o link um pouco antes da seção atingir o topo
            if (pageYOffset >= (sectionTop - sectionHeight / 3)) {
                current = section.getAttribute('id');
            }
        });

        navLi.forEach(a => {
            a.classList.remove('active');
            if (a.getAttribute('href') === `#${current}`) {
                a.classList.add('active');
            }
        });
        // Caso especial para o topo da página ou se nenhuma seção estiver "ativa"
        if (!current && window.pageYOffset < sections[0].offsetTop - (sections[0].clientHeight / 3) ) {
            const dashboardLink = document.querySelector('.main-nav ul li a[href="#dashboard"]');
            if(dashboardLink) dashboardLink.classList.add('active');
        }
    });
});
</script>
    <script src="script.js"></script>
</body>
</html>
<?php $conexao->close(); ?>