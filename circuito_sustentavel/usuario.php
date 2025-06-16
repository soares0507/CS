<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['redirect_after_login'] = 'usuario.php';
    header('Location: login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

// Usar prepared statements para segurança
$sql = "SELECT * FROM Cliente WHERE id_cliente = ?";
$stmt_user = $conexao->prepare($sql);
$stmt_user->bind_param("i", $id_cliente);
$stmt_user->execute();
$resultado = $stmt_user->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
} else {
    session_destroy();
    header('Location: login.php');
    exit;
}
$stmt_user->close();


$sql_endereco = "SELECT * FROM Endereco WHERE id_cliente = ?";
$stmt_endereco = $conexao->prepare($sql_endereco);
$stmt_endereco->bind_param("i", $id_cliente);
$stmt_endereco->execute();
$res_endereco = $stmt_endereco->get_result();
$tem_endereco = ($res_endereco && $res_endereco->num_rows > 0);
$endereco = $tem_endereco ? $res_endereco->fetch_assoc() : null;
$stmt_endereco->close();

// Buscar o estado do usuário na tabela Cotidiano
$estado_usuario = null;
$sql_estado = "SELECT estado FROM Cotidiano WHERE id_cliente = ? LIMIT 1";
$stmt_estado = $conexao->prepare($sql_estado);
$stmt_estado->bind_param("i", $id_cliente);
$stmt_estado->execute();
$res_estado = $stmt_estado->get_result();
if ($res_estado && $res_estado->num_rows > 0) {
    $row_estado = $res_estado->fetch_assoc();
    $estado_usuario = $row_estado['estado'];
}
$stmt_estado->close();

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minha Conta - Circuito Sustentável</title>
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
        --sombra-padrao: 0 8px 25px rgba(0,0,0, 0.07);
        --sombra-hover-forte: 0 10px 30px rgba(40, 160, 96, 0.15);
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --transition-std: 0.3s;
        --font-principal: 'Poppins', sans-serif;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: var(--font-principal);
        line-height: 1.6;
        color: var(--cinza-texto);
        background-color: var(--cinza-claro);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    main {
        flex-grow: 1;
        padding-top: 100px;
    }
    .container-page {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
    }

    /* Header e Footer */
    .site-header {
        position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
        padding: 15px 0; background-color: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        transition: all var(--transition-std); border-bottom: 1px solid transparent;
    }
    .site-header.scrolled {
        background-color: var(--branco); box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        padding: 12px 0; border-bottom: 1px solid var(--cinza-claro);
    }
    .header-container {
        width: 90%; max-width: 1200px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between;
    }
    .site-header .logo { height: 45px; transition: transform var(--transition-std); }
    .site-header .logo:hover { transform: scale(1.05); }
    .header-actions { display: flex; align-items: center; gap: 15px; }
    .header-actions .user-info-header {
        font-size: 0.9em; color: var(--cinza-escuro); font-weight: 500;
        display: flex; align-items: center; gap: 8px;
    }
    .header-actions .user-info-header img {
        width: 32px; height: 32px; border-radius: 50%; object-fit: cover;
    }
    .btn {
        display: inline-block; padding: 8px 18px; font-weight: 600;
        text-decoration: none; border-radius: var(--border-radius-md);
        transition: all var(--transition-std); cursor: pointer;
        border: 2px solid transparent; font-size: 0.9em;
    }
    .btn-logout {
        background-color: var(--verde-claro-fundo); color: var(--verde-escuro);
        border: 1px solid var(--verde);
    }
    .btn-logout:hover { background-color: var(--verde); color: var(--branco); }

    /* Dashboard Layout */
    .dashboard-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 30px;
        align-items: flex-start;
    }
    .dashboard-sidebar, .dashboard-main {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
    }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Sidebar */
    .dashboard-sidebar .profile-card {
        background-color: var(--branco);
        padding: 25px;
        border-radius: var(--border-radius-lg);
        text-align: center;
        box-shadow: var(--sombra-padrao);
        margin-bottom: 20px;
    }
    .profile-card .avatar {
        width: 100px; height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 15px auto;
        border: 4px solid var(--verde-claro-fundo);
    }
    .profile-card h2 {
        font-size: 1.4em; color: var(--cinza-escuro);
        font-weight: 600;
    }
    .profile-card p { font-size: 0.9em; margin-bottom: 15px; }
    .estado-badge {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.9em; font-weight: 600;
        padding: 5px 12px; border-radius: 20px;
        margin-top: 5px; border: 1px solid;
    }
    .estado-badge.saudavel { color: #1e7d36; background: #e6f9ec; border-color: #a3d9b8; }
    .estado-badge.moderado { color: #b36b00; background: #fff7e0; border-color: #ffdda1; }
    .estado-badge.critico { color: #b91c1c; background: #ffeaea; border-color: #f7c5c5; }
    
    .sidebar-nav {
        background-color: var(--branco);
        padding: 15px;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--sombra-padrao);
    }
    .sidebar-nav ul { list-style: none; }
    .sidebar-nav ul li a {
        display: flex; align-items: center; gap: 12px;
        padding: 12px; text-decoration: none;
        color: var(--cinza-texto); font-weight: 500; font-size: 0.95em;
        border-radius: var(--border-radius-md);
        transition: all var(--transition-fast);
    }
    .sidebar-nav ul li a svg { width: 20px; height: 20px; }
    .sidebar-nav ul li a:hover, .sidebar-nav ul li a.active {
        background-color: var(--verde-claro-fundo);
        color: var(--verde);
    }

    /* Conteúdo Principal */
    .dashboard-main .welcome-message {
        background-color: var(--branco);
        padding: 25px 30px;
        border-radius: var(--border-radius-lg);
        margin-bottom: 20px;
        box-shadow: var(--sombra-padrao);
    }
    .welcome-message h1 {
        font-size: 1.8em; color: var(--cinza-escuro); font-weight: 600;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    .info-card {
        background-color: var(--branco);
        padding: 25px;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--sombra-padrao);
    }
    .info-card h3 {
        font-size: 1.2em; color: var(--verde-escuro);
        margin-bottom: 15px; padding-bottom: 10px;
        border-bottom: 1px solid var(--cinza-claro);
    }
    .info-card p, .info-card .user-data-item { font-size: 0.95em; margin-bottom: 8px; }
    .info-card .user-data-item strong {
        color: var(--cinza-escuro);
        min-width: 80px; /* Alinhamento dos dados */
        display: inline-block;
    }

    .info-card .btn {
        margin-top: 15px;
        background-color: var(--verde);
        color: var(--branco);
        padding: 8px 15px;
        font-size: 0.9em;
        text-decoration: none;
        display: inline-block;
    }
    .info-card .btn:hover {
        background-color: var(--verde-escuro);
        transform: translateY(-2px);
    }


    /* Footer */
    .site-footer-bottom {
        background-color: var(--cinza-escuro); color: #b0bec5;
        padding: 50px 0 30px; font-size: 0.9em; width: 100%;
        margin-top: 60px;
    }
    .footer-content-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 30px; width: 90%; max-width: 1140px; margin: 0 auto 30px auto; }
    .footer-col h4 { font-size: 1.1em; color: var(--branco); font-weight: 600; margin-bottom: 15px; }
    .footer-col p, .footer-col a { color: #b0bec5; text-decoration: none; margin-bottom: 8px; display: block; font-size: 0.95em; }
    .footer-col a:hover { color: var(--verde); transform: translateX(2px); transition: transform var(--transition-fast); }
    .footer-copyright { text-align: center; padding-top: 30px; border-top: 1px solid #4a5c6a; color: #78909c; width: 90%; max-width: 1140px; margin: 0 auto; }

    /* Responsividade */
    @media (max-width: 992px) {
        .dashboard-layout { grid-template-columns: 1fr; }
        .dashboard-sidebar { position: static; }
        main { padding-top: 80px; }
    }
     @media (max-width: 768px) {
        .header-container { flex-wrap:wrap; justify-content:center; gap:10px; }
        main { padding-top: 130px; }
     }
  </style>
  <script>
    function confirmarLogout() {
      return confirm('Tem certeza que deseja encerrar a sessão?');
    }
  </script>
</head>
<body>
    
<header class="site-header">
    <div class="header-container">
        <a href="loja.php"> <img src="img/logo2.png" alt="Logo Circuito Sustentável" class="logo"> </a>
        <div class="header-actions">
            <span class="user-info-header">
                <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?? 'img/user.png') ?>" alt="Foto de Perfil">
                <span>Olá, <?= htmlspecialchars(explode(" ", $usuario['nome'])[0]) ?>!</span>
            </span>
            <form method="post" onsubmit="return confirmarLogout();" style="margin:0;">
                <button class="btn btn-logout" type="submit" name="logout">Sair</button>
            </form>
        </div>
    </div>
</header>

<main>
    <div class="container-page">
        <div class="dashboard-layout">
            <aside class="dashboard-sidebar">
                <div class="profile-card animate-on-scroll">
                    <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?? 'img/user.png') ?>" alt="Avatar" class="avatar">
                    <h2><?= htmlspecialchars($usuario['nome']) ?></h2>
                    <p><?= htmlspecialchars($usuario['email']) ?></p>
                    <?php if ($estado_usuario): 
                        $badgeClass = ''; $badgeIcon = ''; $badgeText = '';
                        if ($estado_usuario === 'saudavel') {
                            $badgeClass = 'saudavel'; $badgeIcon = '🟢'; $badgeText = 'Saudável';
                        } elseif ($estado_usuario === 'moderado') {
                            $badgeClass = 'moderado'; $badgeIcon = '🟠'; $badgeText = 'Moderado';
                        } elseif ($estado_usuario === 'critico') {
                            $badgeClass = 'critico'; $badgeIcon = '🔴'; $badgeText = 'Crítico';
                        }
                    ?>
                    <span class="estado-badge <?= $badgeClass ?>"><?= $badgeIcon ?> Estado: <?= $badgeText ?></span>
                    <?php endif; ?>
                </div>
                <nav class="sidebar-nav animate-on-scroll" style="animation-delay: 0.2s;">
                    <ul>
                        <li><a href="dados.php">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            Meus Dados
                        </a></li>
                        <li><a href="<?= ($usuario['premium'] ? 'c+.php' : 'assinatura_usuario.php') ?>">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM18 15.75l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 18l-1.035.259a3.375 3.375 0 00-2.456 2.456L18 21.75l-.259-1.035a3.375 3.375 0 00-2.456-2.456L14.25 18l1.035-.259a3.375 3.375 0 002.456-2.456L18 15.75z" /></svg>
                            C+ Assinatura
                        </a></li>
                         <li><a href="pedidos.php">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10.5 11.25h3M12 15h.008" />
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125V6.375c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v.001c0 .621.504 1.125 1.125 1.125z" />
</svg>
                            Minhas Compras
                        </a></li>
                        <li><a href="perguntas.php">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Minhas Perguntas
                        </a></li>
                         <li><a href="vender.php">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
  <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
</svg>
</svg>
                            Quero Vender
                        </a></li>
                    </ul>
                </nav>
            </aside>
            <div class="dashboard-main animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="welcome-message">
                    <h1>Seja bem-vindo(a) de volta!</h1>
                    <p>Gerencie sua conta e explore seus benefícios no Circuito Sustentável.</p>
                </div>
                <div class="info-grid">
                    <div class="info-card">
                        <h3>📍 Endereço Principal</h3>
                        <?php if ($tem_endereco): ?>
                        <p>
                            <?= htmlspecialchars($endereco['rua']) ?>, <?= htmlspecialchars($endereco['numero']) ?><br>
                            <?= htmlspecialchars($endereco['bairro']) ?>, <?= htmlspecialchars($endereco['cidade']) ?> - <?= htmlspecialchars($endereco['estado']) ?><br>
                            CEP: <?= htmlspecialchars($endereco['cep']) ?>
                        </p>
                        <a href="#" class="btn">Gerenciar Endereços</a>
                        <?php else: ?>
                        <p>Você ainda não cadastrou um endereço.</p>
                        <a href="criar_endereco.php" class="btn">Cadastrar Endereço</a>
                        <?php endif; ?>
                    </div>
                     <div class="info-card">
                        <h3>👤 Meus Dados</h3>
                        <div class="user-data-item"><strong>Nome:</strong> <span><?= htmlspecialchars($usuario['nome']) ?></span></div>
                        <div class="user-data-item"><strong>Email:</strong> <span><?= htmlspecialchars($usuario['email']) ?></span></div>
                        <div class="user-data-item"><strong>CPF:</strong> <span><?= htmlspecialchars($usuario['cpf']) ?></span></div>
                        <div class="user-data-item"><strong>Telefone:</strong> <span><?= htmlspecialchars($usuario['telefone']) ?></span></div>
                        <a href="dados.php" class="btn">Editar Dados</a>
                    </div>
                </div>
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
        <a href="loja.php">Loja</a>
        <a href="assinatura_usuario.php">Assinatura</a>
        <a href="suporte.html">Suporte</a>
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
</script>
</body>
</html>