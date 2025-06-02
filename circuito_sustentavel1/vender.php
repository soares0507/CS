<?php
session_start();
include 'conexao.php';

$erro = '';
$sucesso = '';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['redirect_after_login'] = 'vender.php';
    header('Location: login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$sql_cliente_check = "SELECT * FROM Cliente WHERE id_cliente = '$id_cliente'";
$res_cliente_check = $conexao->query($sql_cliente_check);
if ($res_cliente_check && $res_cliente_check->num_rows > 0) {
    $cliente = $res_cliente_check->fetch_assoc();
} else {
    session_destroy();
    header('Location: login.php?erro=sessao_invalida');
    exit;
}

$sql_v = "SELECT * FROM Vendedor WHERE email = '{$cliente['email']}'";
$res_v = $conexao->query($sql_v);
if ($res_v && $res_v->num_rows > 0) {
    header('Location: vendedor.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['virar_vendedor'])) {
    $foto_rg_frente = '';
    $foto_rg_verso = '';
    $foto_usuario = '';
    $upload_dir = 'img/';

    if (isset($_FILES['foto_usuario']) && $_FILES['foto_usuario']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto_usuario']['name'], PATHINFO_EXTENSION);
        $foto_usuario = 'vendedor_foto_' . uniqid() . '.' . $ext;
        if (!move_uploaded_file($_FILES['foto_usuario']['tmp_name'], $upload_dir . $foto_usuario)) {
            $erro = "Erro ao fazer upload da foto do usuário.";
        }
    } else if (isset($_FILES['foto_usuario']) && $_FILES['foto_usuario']['error'] != UPLOAD_ERR_NO_FILE) {
        $erro = "Erro no upload da foto do usuário: " . $_FILES['foto_usuario']['error'];
    }

    if (isset($_FILES['foto_rg_frente']) && $_FILES['foto_rg_frente']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto_rg_frente']['name'], PATHINFO_EXTENSION);
        $foto_rg_frente = 'rg_frente_' . uniqid() . '.' . $ext;
         if (!move_uploaded_file($_FILES['foto_rg_frente']['tmp_name'], $upload_dir . $foto_rg_frente)) {
            $erro = "Erro ao fazer upload da foto do RG (frente).";
        }
    } else if (isset($_FILES['foto_rg_frente']) && $_FILES['foto_rg_frente']['error'] != UPLOAD_ERR_NO_FILE) {
         $erro = "Erro no upload da foto do RG (frente): " . $_FILES['foto_rg_frente']['error'];
    }

    if (isset($_FILES['foto_rg_verso']) && $_FILES['foto_rg_verso']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto_rg_verso']['name'], PATHINFO_EXTENSION);
        $foto_rg_verso = 'rg_verso_' . uniqid() . '.' . $ext;
        if (!move_uploaded_file($_FILES['foto_rg_verso']['tmp_name'], $upload_dir . $foto_rg_verso)) {
            $erro = "Erro ao fazer upload da foto do RG (verso).";
        }
    } else if (isset($_FILES['foto_rg_verso']) && $_FILES['foto_rg_verso']['error'] != UPLOAD_ERR_NO_FILE) {
        $erro = "Erro no upload da foto do RG (verso): " . $_FILES['foto_rg_verso']['error'];
    }
    
    if (empty($foto_usuario) || empty($foto_rg_frente) || empty($foto_rg_verso)) {
        if(empty($erro)) $erro = "Todos os documentos (foto pessoal, RG frente e verso) são obrigatórios.";
    }

    if (empty($erro)) {
        $nome = $cliente['nome'];
        $email = $cliente['email'];
        $cpf = $cliente['cpf'];
        $telefone = $cliente['telefone'];
        $senha = $cliente['senha']; 

        $sql_verifica = "SELECT id_vendedor FROM Vendedor WHERE email = ?";
        $stmt_check = $conexao->prepare($sql_verifica);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $res_verifica = $stmt_check->get_result();

        if ($res_verifica && $res_verifica->num_rows > 0) {
            $erro = "Já existe um vendedor cadastrado com este e-mail!";
        } else {
            $conexao->begin_transaction();
            try {
                $sql_insert_vendedor = "INSERT INTO Vendedor (nome, email, senha, cpf, telefone) VALUES (?, ?, ?, ?, ?)";
                $stmt_vendedor = $conexao->prepare($sql_insert_vendedor);
                $stmt_vendedor->bind_param("sssss", $nome, $email, $senha, $cpf, $telefone);

                if ($stmt_vendedor->execute()) {
                    $id_vendedor = $conexao->insert_id;

                    $sql_insert_docs = "INSERT INTO DocumentosVendedor (id_vendedor, foto_usuario, foto_rg_frente, foto_rg_verso) VALUES (?, ?, ?, ?)";
                    $stmt_docs = $conexao->prepare($sql_insert_docs);
                    $stmt_docs->bind_param("isss", $id_vendedor, $foto_usuario, $foto_rg_frente, $foto_rg_verso);
                    $stmt_docs->execute();

                    $tabelas_para_atualizar = ['Cotidiano', 'Endereco', 'Moeda', 'Assinatura', 'Pedido', 'Carrinho', 'Postagem', 'Comentario', 'Pergunta'];
                    foreach ($tabelas_para_atualizar as $tabela) {
                        // Atualiza id_vendedor e zera id_cliente para evitar violação de FK
                        $sql_update_fk = "UPDATE $tabela SET id_vendedor = ?, id_cliente = NULL WHERE id_cliente = ?";
                        $stmt_update = $conexao->prepare($sql_update_fk);
                        $stmt_update->bind_param("ii", $id_vendedor, $id_cliente);
                        if (!$stmt_update->execute()) {
                            throw new Exception("Erro ao atualizar $tabela: " . $stmt_update->error);
                        }
                    }
                    
                    $sql_delete_cliente = "DELETE FROM Cliente WHERE id_cliente = ?";
                    $stmt_delete = $conexao->prepare($sql_delete_cliente);
                    $stmt_delete->bind_param("i", $id_cliente);
                    if (!$stmt_delete->execute()) {
                        throw new Exception("Erro ao deletar cliente: " . $stmt_delete->error);
                    }

                    $conexao->commit();

                    $_SESSION['vendedor_id'] = $id_vendedor;
                    unset($_SESSION['usuario_id']);
                    $_SESSION['nome_usuario'] = $nome;
                    $_SESSION['tipo_usuario'] = 'vendedor';

                    $sucesso = "Parabéns! Você agora é um vendedor! Redirecionando para o seu painel...";
                    echo "<script>setTimeout(function(){ window.location.href = 'vendedor.php'; }, 3000);</script>";

                } else {
                    throw new Exception("Erro ao criar conta de vendedor: " . $stmt_vendedor->error);
                }
            } catch (Exception $e) {
                $conexao->rollback();
                $erro = $e->getMessage();
                if ($foto_rg_frente && file_exists($upload_dir . $foto_rg_frente)) unlink($upload_dir . $foto_rg_frente);
                if ($foto_rg_verso && file_exists($upload_dir . $foto_rg_verso)) unlink($upload_dir . $foto_rg_verso);
                if ($foto_usuario && file_exists($upload_dir . $foto_usuario)) unlink($upload_dir . $foto_usuario);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seja um Vendedor - Circuito Sustentável</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="img/favicon.ico" type="image/x-icon"> 
  <style>
    :root {
        --verde: #28a060;
        --verde-escuro: #1e7c4b;
        --verde-claro-fundo: #f0f9f4; /* Para cards de benefício */
        --cinza-claro: #f4f6f8; /* Fundo principal da página */
        --cinza-texto: #5f6c7b; /* Texto secundário */
        --cinza-escuro: #2c3e50; /* Texto principal, títulos */
        --branco: #ffffff; /* Para cards, header */
        
        --sombra-padrao: 0 8px 25px rgba(40, 160, 96, 0.07);
        --sombra-hover-forte: 0 12px 35px rgba(40, 160, 96, 0.15);
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --transition-fast: 0.2s;
        --transition-std: 0.4s;
        --transition-long: 0.6s;
        --font-principal: 'Poppins', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html { scroll-behavior: smooth; }

    body {
        font-family: var(--font-principal);
        line-height: 1.7;
        color: var(--cinza-escuro); /* Texto principal escuro */
        background-color: var(--cinza-claro); /* Fundo da página claro */
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .container { width: 90%; max-width: 1140px; margin: 0 auto; padding: 0 20px; }

    .btn {
        display: inline-block; padding: 14px 32px; font-weight: 600;
        text-decoration: none; border-radius: var(--border-radius-md);
        transition: all var(--transition-std) cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer; border: 2px solid transparent; font-size: 1em;
        box-shadow: var(--sombra-padrao); letter-spacing: 0.5px;
        text-align: center;
    }
    .btn-primary { background-color: var(--verde); color: var(--branco); }
    .btn-primary:hover {
        background-color: var(--verde-escuro);
        transform: translateY(-4px) scale(1.03);
        box-shadow: var(--sombra-hover-forte);
    }
     .btn-outline {
        background-color: transparent; color: var(--verde); border-color: var(--verde);
    }
    .btn-outline:hover {
        background-color: var(--verde); color: var(--branco);
        transform: translateY(-4px) scale(1.03);
        box-shadow: var(--sombra-hover-forte);
    }
    .btn-large { padding: 18px 45px; font-size: 1.15em; }

    .site-header {
        position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
        padding: 18px 0;
        background-color: rgba(255, 255, 255, 0.7); /* Fundo branco translúcido */
        backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        transition: background-color var(--transition-std), box-shadow var(--transition-std), padding var(--transition-std);
    }
    .site-header.scrolled {
        background-color: var(--branco); /* Fundo branco opaco */
        box-shadow: 0 4px 15px rgba(0,0,0,0.08); /* Sombra sutil */
        padding: 12px 0;
    }
    .header-container { display: flex; align-items: center; justify-content: space-between; }
    .logo { height: 40px; width: auto; transition: transform var(--transition-std); } /* Remover filtro */
    .logo:hover { transform: scale(1.05); }

    .main-nav { display: flex; gap: 30px; }
    .main-nav a {
        color: var(--cinza-escuro); font-weight: 500; text-decoration: none;
        position: relative; padding-bottom: 8px; font-size: 0.95em;
    }
    .main-nav a::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 0%; height: 2.5px;
        background-color: var(--verde);
        transition: width var(--transition-long) cubic-bezier(0.19, 1, 0.22, 1);
    }
    .main-nav a:hover::after, .main-nav a.active::after { width: 100%; }
    .main-nav a:hover { color: var(--verde); }
    
    .menu-toggle { display: none; }

    .hero-vender-section {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 140px 20px 80px;
        background: linear-gradient(135deg, var(--branco) 0%, var(--cinza-claro) 100%); /* Gradiente suave claro */
        position: relative;
    }
    .hero-vender-section h1 {
        font-size: clamp(2.5rem, 6vw, 4.5rem); font-weight: 700; line-height: 1.2;
        margin-bottom: 20px; color: var(--cinza-escuro); /* Texto escuro */
    }
    .hero-vender-section .highlight-green { color: var(--verde); }
    .hero-vender-section p.subtitle {
        font-size: clamp(1rem, 2.5vw, 1.3rem); font-weight: 400;
        max-width: 700px; margin: 0 auto 40px; color: var(--cinza-texto); /* Texto secundário escuro */
    }
    .scroll-down-indicator {
        position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);
        color: var(--verde); opacity: 0.7;
        transition: opacity var(--transition-std), transform var(--transition-std);
        animation: bounceUpDown 2.5s infinite ease-in-out;
        cursor: pointer;
    }
    .scroll-down-indicator svg path { fill: var(--verde); }
    .scroll-down-indicator:hover { opacity: 1; transform: translateX(-50%) scale(1.1); }
    @keyframes bounceUpDown {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-10px); }
    }

    .beneficios-section {
        padding: 80px 0;
        background-color: var(--branco); /* Fundo branco para esta seção */
    }
    .section-title {
        font-size: clamp(2rem, 4vw, 2.8rem); color: var(--cinza-escuro);
        font-weight: 600; text-align: center; margin-bottom: 60px;
    }
    .beneficios-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;
    }
    .beneficio-card {
        background-color: var(--verde-claro-fundo); /* Fundo de card verde bem claro */
        color: var(--cinza-escuro); /* Texto escuro no card */
        padding: 30px;
        border-left: 5px solid var(--verde);
        border-radius: var(--border-radius-md);
        box-shadow: var(--sombra-padrao);
        transition: transform var(--transition-std), box-shadow var(--transition-std);
    }
    .beneficio-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--sombra-hover-forte);
    }
    .beneficio-card h3 {
        color: var(--verde-escuro); /* Título do card um pouco mais escuro */
        font-size: 1.5em; font-weight: 600;
        margin-bottom: 12px;
    }
    .beneficio-card p {
        font-size: 0.95em; line-height: 1.8; color: var(--cinza-texto); /* Texto do card */
    }
    .cta-convite-vendedor { text-align: center; margin-top: 60px; }

    .form-vendedor-container {
        padding: 0; /* Inicialmente sem padding */
        background-color: var(--cinza-claro); /* Fundo claro */
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        transform: translateY(30px);
        transition: opacity var(--transition-long) ease-out, max-height var(--transition-long) ease-out, transform var(--transition-long) ease-out, padding var(--transition-long) ease-out;
    }
    .form-vendedor-container.show {
        opacity: 1;
        max-height: 2000px; 
        transform: translateY(0);
        padding: 60px 0; /* Padding ao mostrar */
    }
    .form-vendedor-wrapper {
        background: var(--branco); /* Fundo do formulário branco */
        border-radius: var(--border-radius-lg);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); /* Sombra mais sutil */
        padding: 30px 40px;
        max-width: 650px;
        margin: 0 auto;
        color: var(--cinza-escuro); /* Texto escuro no formulário */
    }
    .form-vendedor-wrapper h2 {
        text-align: center; color: var(--verde);
        margin-bottom: 30px; font-size: 1.8em; font-weight: 600;
    }
    .form-vendedor-wrapper label {
        font-weight: 500; color: var(--cinza-texto);
        display: block; margin-top: 20px; margin-bottom: 8px;
        font-size: 0.9em;
    }
    .form-vendedor-wrapper input[type="text"],
    .form-vendedor-wrapper input[type="email"],
    .form-vendedor-wrapper input[type="file"] {
        width: 100%;
        padding: 12px 16px;
        border-radius: var(--border-radius-sm);
        border: 1px solid #ccc; /* Borda cinza claro */
        font-size: 1em;
        margin-bottom: 10px;
        background: var(--branco); /* Fundo do input branco */
        color: var(--cinza-escuro); /* Texto do input escuro */
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        outline: none;
    }
    .form-vendedor-wrapper input[readonly] {
        background: var(--cinza-claro); /* Fundo para readonly */
        color: var(--cinza-texto);
        border-color: #ddd;
        cursor: not-allowed;
    }
     .form-vendedor-wrapper input:focus:not([readonly]) {
        border-color: var(--verde);
        box-shadow: 0 0 0 3px rgba(40, 160, 96, 0.2);
    }
    .form-vendedor-wrapper input[type="file"] {
        padding: 10px;
        border: 1px dashed #ccc;
    }
    .form-vendedor-wrapper input[type="file"]::-webkit-file-upload-button {
        background: var(--verde); color: var(--branco); border: none;
        padding: 8px 15px; border-radius: var(--border-radius-sm); cursor: pointer;
        transition: background-color var(--transition-fast); margin-right: 10px;
    }
    .form-vendedor-wrapper input[type="file"]::-webkit-file-upload-button:hover {
        background: var(--verde-escuro);
    }
    .form-vendedor-wrapper .btn-primary {
        width: 100%; margin-top: 30px; padding: 15px 0; font-size: 1.1em;
    }

    .msg-feedback {
        padding: 15px; margin-bottom: 20px; border-radius: var(--border-radius-sm);
        font-weight: 500; text-align: center;
    }
    .msg-erro {
        background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
    }
    .msg-sucesso {
        background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;
    }

    .site-footer-bottom {
        background-color: var(--cinza-escuro); /* Rodapé escuro */
        color: #b0bec5; /* Texto claro no rodapé */
        padding: 60px 0 30px; font-size: 0.9em;
    }
    .footer-content-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px; margin-bottom: 40px;
    }
    .footer-col h4 { font-size: 1.2em; color: var(--branco); font-weight: 600; margin-bottom: 18px; }
    .footer-col p, .footer-col a { color: #b0bec5; text-decoration: none; margin-bottom: 10px; display: block; }
    .footer-col a:hover { color: var(--verde); transform: translateX(3px); transition: transform var(--transition-fast); }
    .footer-copyright { text-align: center; padding-top: 30px; border-top: 1px solid #4a5c6a; color: #78909c; }

    .animate-on-scroll { opacity: 0; transform: translateY(40px); transition: opacity 0.7s ease-out, transform 0.7s ease-out; }
    .animate-on-scroll.in-view { opacity: 1; transform: translateY(0); }
    .animate-on-scroll.delay-200ms { transition-delay: 0.2s; }
    .animate-on-scroll.delay-400ms { transition-delay: 0.4s; }
    
    @media (max-width: 992px) {
        .main-nav { display: none; }
        .menu-toggle { display: block; }
        .hero-vender-section { padding: 120px 20px 60px; }
    }
     @media (max-width: 768px) {
        .hero-vender-section h1 { font-size: 2.2rem; }
        .hero-vender-section p.subtitle { font-size: 1rem; }
        .section-title { font-size: 1.8rem; margin-bottom: 40px; }
        .beneficios-grid { grid-template-columns: 1fr; gap: 25px; }
        .beneficio-card { padding: 25px; }
        .form-vendedor-wrapper { padding: 25px; }
        .form-vendedor-wrapper h2 { font-size: 1.5em; }
        .footer-content-grid { grid-template-columns: 1fr; text-align: center;}
        .footer-col h4 { margin-top: 20px;}
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="container header-container">
      <a href="loja.php"><img src="img/logo2.png" alt="Logo Circuito Sustentável" class="logo" /></a>
      <nav class="main-nav">
        <a href="loja.php">Loja</a>
        <a href="tela_inicial.php">Início</a>
        <a href="suporte.html">Suporte</a>
      </nav>
       <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false" style="display:none;">
            <span></span><span></span><span></span>
       </button>
    </div>
  </header>

  <main>
    <section id="hero-vender" class="hero-vender-section">
      <div class="container">
        <h1 class="animate-on-scroll">Transforme-se em um <span class="highlight-green">Vendedor</span> no Circuito Sustentável</h1>
        <p class="subtitle animate-on-scroll delay-200ms">
          Alcance novos clientes, ganhe dinheiro vendendo produtos que promovem a sustentabilidade e faça parte de uma comunidade que valoriza o futuro do nosso planeta.
        </p>
        <div class="cta-convite-vendedor animate-on-scroll delay-400ms">
             <button class="btn btn-primary btn-large" id="mostrar-form-btn" type="button">Quero ser vendedor</button>
        </div>
      </div>
      <a href="#beneficios" class="scroll-down-indicator" aria-label="Ver Benefícios">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40px" height="40px"><path d="M11.9999 13.1714L16.9497 8.22168L18.3639 9.63589L11.9999 15.9999L5.63599 9.63589L7.0502 8.22168L11.9999 13.1714Z"></path></svg>
      </a>
    </section>

    <section id="beneficios" class="beneficios-section">
      <div class="container">
        <h2 class="section-title animate-on-scroll">Vantagens Exclusivas para Você</h2>
        <div class="beneficios-grid">
          <div class="beneficio-card animate-on-scroll">
            <h3>+ Visibilidade Estratégica</h3>
            <p>Seus produtos sustentáveis em destaque para milhares de consumidores conscientes e engajados.</p>
          </div>
          <div class="beneficio-card animate-on-scroll delay-200ms">
            <h3>Gestão Simplificada</h3>
            <p>Painel de vendedor intuitivo para cadastrar, gerenciar produtos e acompanhar seus pedidos com facilidade.</p>
          </div>
          <div class="beneficio-card animate-on-scroll delay-400ms">
            <h3>Recebimentos Ágeis</h3>
            <p>Processo de pagamento seguro e eficiente, com repasses rápidos direto para sua conta.</p>
          </div>
          <div class="beneficio-card animate-on-scroll">
            <h3>Suporte Dedicado</h3>
            <p>Nossa equipe de suporte está pronta para te auxiliar em cada etapa, ajudando a otimizar suas vendas.</p>
          </div>
          <div class="beneficio-card animate-on-scroll delay-200ms">
            <h3>Comunidade Ativa</h3>
            <p>Conecte-se com outros vendedores, troque experiências e faça parte de um movimento por um consumo mais verde.</p>
          </div>
          <div class="beneficio-card animate-on-scroll delay-400ms">
            <h3>Comece Sem Custos</h3>
            <p>Cadastre-se gratuitamente como vendedor e inicie suas vendas sem taxa de adesão ou mensalidade inicial.</p>
          </div>
        </div>
      </div>
    </section>
    
    <section id="form-vendedor-container" class="form-vendedor-container">
      <div class="container">
        <div class="form-vendedor-wrapper">
          <h2>Complete seu Cadastro de Vendedor</h2>
          <?php if ($erro): ?>
            <div class="msg-feedback msg-erro"><?= htmlspecialchars($erro) ?></div>
          <?php endif; ?>
          <?php if ($sucesso): ?>
            <div class="msg-feedback msg-sucesso"><?= htmlspecialchars($sucesso) ?></div>
          <?php endif; ?>

          <?php if (empty($sucesso)): ?>
          <form class="form-vendedor" method="post" enctype="multipart/form-data" autocomplete="off">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" value="<?= htmlspecialchars($cliente['nome']) ?>" readonly>
            
            <label for="email">E-mail</label>
            <input type="email" id="email" value="<?= htmlspecialchars($cliente['email']) ?>" readonly>
            
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" value="<?= htmlspecialchars($cliente['cpf']) ?>" readonly>
            
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" readonly>
            
            <label for="foto_usuario">Uma foto sua (para perfil)</label>
            <input type="file" name="foto_usuario" id="foto_usuario" accept="image/jpeg, image/png, image/webp" required>
            
            <label for="foto_rg_frente">Foto do RG (frente)</label>
            <input type="file" name="foto_rg_frente" id="foto_rg_frente" accept="image/jpeg, image/png, image/webp" required>
            
            <label for="foto_rg_verso">Foto do RG (verso)</label>
            <input type="file" name="foto_rg_verso" id="foto_rg_verso" accept="image/jpeg, image/png, image/webp" required>
            
            <button type="submit" class="btn btn-primary" name="virar_vendedor">Cadastrar como Vendedor</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer-bottom">
    <div class="container footer-content-grid">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Conectando pessoas e negócios por um futuro mais verde e consciente.</p>
      </div>
      <div class="footer-col">
        <h4>Navegar</h4>
        <a href="loja.php">Loja</a>
        <a href="#hero-vender">Seja Vendedor</a>
        <a href="tela_inicial.php">Início</a>
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

    const mostrarFormBtn = document.getElementById('mostrar-form-btn');
    const formContainer = document.getElementById('form-vendedor-container');

    if (mostrarFormBtn && formContainer) {
        mostrarFormBtn.onclick = function() {
            this.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
            this.style.transform = 'translateY(-20px) scale(0.95)';
            this.style.opacity = '0';
            
            setTimeout(() => {
                if(this.style) this.style.display = 'none'; 
                formContainer.classList.add('show');
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 400); 
        };
    }
    
    <?php if (!empty($sucesso) && empty($erro)): ?>
    // Se houve sucesso e não há erro, e o botão de mostrar formulário existe, esconda-o.
    window.addEventListener('DOMContentLoaded', () => {
        if (mostrarFormBtn) {
            mostrarFormBtn.style.display = 'none';
        }
        if (formContainer) {
            // Garante que o container do formulário seja exibido para mostrar a mensagem de sucesso.
            // Isso pode ser redundante se o PHP já não renderiza o form, mas garante visibilidade da msg.
             if (!formContainer.classList.contains('show')) {
                formContainer.classList.add('show'); // Mostra para a mensagem de sucesso ser visível
             }
             const feedbackMessage = formContainer.querySelector('.msg-feedback.msg-sucesso');
             if (feedbackMessage) {
                feedbackMessage.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
             }
        }
    });
    <?php elseif (!empty($erro)): ?>
     window.addEventListener('DOMContentLoaded', () => {
        // Se há erro, o formulário já deve estar visível pelo clique ou por padrão (se não escondido por JS)
        // Apenas garante que o container do formulário esteja visível para a mensagem de erro
        if (formContainer && !formContainer.classList.contains('show')) {
            if (mostrarFormBtn) mostrarFormBtn.style.display = 'none'; // Esconde o botão se ainda estiver lá
            formContainer.classList.add('show');
        }
        const feedbackMessage = document.querySelector('.msg-feedback.msg-erro');
        if (feedbackMessage) {
            feedbackMessage.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
        }
     });
    <?php endif; ?>

  </script>
</body>
</html>