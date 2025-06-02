<?php
session_start();
$erro = ''; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conexao.php'; 

    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? ''; 
    $telefone = $_POST['telefone'] ?? '';
    $senha_raw = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($cpf) || empty($telefone) || empty($senha_raw)) {
        $erro = "Todos os campos de dados pessoais são obrigatórios!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Formato de e-mail inválido!";
    } else {
        $senha_hashed = password_hash($senha_raw, PASSWORD_BCRYPT);

        $sql_verifica = "SELECT id_cliente FROM Cliente WHERE email = ? OR cpf = ?";
        $stmt_verifica = $conexao->prepare($sql_verifica);
        if ($stmt_verifica) {
            $stmt_verifica->bind_param("ss", $email, $cpf);
            $stmt_verifica->execute();
            $stmt_verifica->store_result();

            if ($stmt_verifica->num_rows > 0) {
                $erro = "CPF ou e-mail já está registrado!";
            }
            $stmt_verifica->close();
        } else {
            $erro = "Erro na preparação da consulta de verificação: " . $conexao->error;
        }
        

        if (empty($erro)) { 
            $sql_cliente = "INSERT INTO Cliente (nome, email, cpf, senha, telefone) VALUES (?, ?, ?, ?, ?)";
            $stmt_cliente = $conexao->prepare($sql_cliente);
            if ($stmt_cliente) {
                $stmt_cliente->bind_param("sssss", $nome, $email, $cpf, $senha_hashed, $telefone);

                if ($stmt_cliente->execute()) {
                    $id_cliente = $conexao->insert_id;
                    $stmt_cliente->close(); 

                    $carro = $_POST['carro'] ?? 'nenhum';
                    $onibus = $_POST['onibus'] ?? 'nenhum';
                    $luz = $_POST['luz'] ?? 'ate100';
                    $gas = $_POST['gas'] ?? '2meses';
                    $carne = $_POST['carne'] ?? '0';
                    $reciclagem = $_POST['reciclagem'] ?? 'nunca';

                    $sql_cotidiano = "INSERT INTO Cotidiano (id_cliente, carro, onibus, luz, gas, carne, reciclagem) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt_cotidiano = $conexao->prepare($sql_cotidiano);
                    if ($stmt_cotidiano) {
                        $stmt_cotidiano->bind_param("issssss", $id_cliente, $carro, $onibus, $luz, $gas, $carne, $reciclagem);
                        
                        if ($stmt_cotidiano->execute()) {
                            $_SESSION['usuario_id'] = $id_cliente; 
                            $stmt_cotidiano->close();
                            if (isset($conexao)) $conexao->close();
                            header('Location: loja.php');
                            exit;
                        } else {
                            $erro = "Erro ao salvar informações do cotidiano: " . $stmt_cotidiano->error;
                        }
                        if(isset($stmt_cotidiano)) $stmt_cotidiano->close();
                    }  else {
                        $erro = "Erro na preparação da consulta de cotidiano: " . $conexao->error;
                    }
                } else {
                    $erro = "Erro ao salvar cadastro: " . $stmt_cliente->error;
                }
                if(isset($stmt_cliente)) $stmt_cliente->close();
            } else {
                 $erro = "Erro na preparação da consulta de cliente: " . $conexao->error;
            }
        }
    }
    if (isset($conexao) && !empty($erro)) $conexao->close(); // Fecha a conexão se houver erro e ela existir
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro - Circuito Sustentável</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        --vermelho-erro: #d9534f; /* Tom de vermelho para erro */
        --sombra-padrao: 0 8px 25px rgba(0, 0, 0, 0.08);
        --sombra-hover-forte: 0 12px 35px rgba(0, 0, 0, 0.12);
        --border-radius-sm: 4px;
        --border-radius-md: 8px;
        --border-radius-lg: 16px;
        --transition-fast: 0.2s;
        --transition-std: 0.3s;
        --transition-slide: 0.4s; /* Duração da transição de slide */
        --font-principal: 'Poppins', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: var(--font-principal);
        line-height: 1.6;
        color: var(--cinza-texto);
        background-color: var(--verde);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .container-geral {
        width: 100%;
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
    }

    .cadastro-wrapper {
        display: flex;
        background-color: var(--branco);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--sombra-padrao);
        max-width: 1100px;
        width: 100%;
    }

    .cadastro-form-content {
        flex-grow: 1;
        padding: 30px 25px;
        display: flex;
        flex-direction: column;
        width: 100%;
        overflow: hidden; /* Adicionado para conter os steps */
    }
    
    .cadastro-image-container {
        flex-basis: 40%;
        background-color: var(--verde-claro-fundo);
        display: none; 
        align-items: center;
        justify-content: center;
        padding: 30px;
        border-top-right-radius: var(--border-radius-lg);
        border-bottom-right-radius: var(--border-radius-lg);
    }
    .cadastro-image-container img {
        max-width: 100%;
        height: auto;
        max-height: 550px;
        object-fit: contain;
        border-radius: var(--border-radius-md);
    }

    .cadastro-form-content h1 { 
        color: var(--cinza-escuro); font-weight: 700; text-align: center;
        font-size: 2.2em; margin-bottom: 20px;
    }
    .form-step h2 {
        color: var(--cinza-escuro); font-weight: 600;
        font-size: 1.6em; margin-bottom: 20px; text-align: left; 
        padding-bottom:10px; border-bottom: 2px solid var(--verde-claro-fundo);
    }

    label {
        display: block; font-weight: 600; margin-top: 15px;
        margin-bottom: 6px; font-size: 0.9em; color: var(--cinza-escuro);
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
        width: 100%; padding: 11px 14px; margin-bottom: 10px;
        border-radius: var(--border-radius-md); border: 1px solid #ccd0d5;
        font-size: 0.95em; font-family: var(--font-principal);
        color: var(--cinza-escuro); background-color: var(--branco);
        transition: border-color var(--transition-std), box-shadow var(--transition-std);
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
        outline: none; border-color: var(--verde);
        box-shadow: 0 0 0 3px rgba(40, 160, 96, 0.15);
    }

    select {
        appearance: none; -webkit-appearance: none; -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%235f6c7b' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 14px center;
        background-size: 16px 12px; padding-right: 40px;
    }

    .toggle-container {
        margin-top: 8px; margin-bottom: 15px; display: flex;
        align-items: center; gap: 8px; cursor: pointer;
    }
    .bolinha {
        width: 16px; height: 16px; border-radius: 50%;
        border: 2px solid var(--cinza-texto); background-color: var(--branco);
        transition: background-color var(--transition-std), border-color var(--transition-std);
    }
    .bolinha.ativa { background-color: var(--verde); border-color: var(--verde); }
    .toggle-label { font-size: 0.85em; color: var(--cinza-texto); }

    .php-error-message { /* Estilo para erro do PHP */
        color: var(--vermelho-erro); font-size: 0.9em; text-align: center; /* Centralizado */
        margin-bottom: 15px; padding: 10px; background-color: #fdecea; 
        border-radius:var(--border-radius-sm); font-weight: 500; 
        border-left: 3px solid var(--vermelho-erro);
    }
    .step1-js-error-message { /* Estilo para erro JS do Passo 1 */
        color: var(--vermelho-erro);
        font-size: 0.85em;
        margin-top: 15px;
        margin-bottom: 10px; /* Espaço antes do botão */
        font-weight: 500;
        min-height: 1.5em; /* Para evitar pulo de layout */
        text-align: left;
    }
    .step1-js-error-message ul {
        list-style-type: disc;
        margin-left: 20px;
        padding-left: 0;
    }
    .step1-js-error-message li {
        margin-bottom: 3px;
    }


    /* Stepper Styles */
    .form-stepper-container { /* Este é o container que tem overflow:hidden */
        width: 100%;
        overflow: hidden;
        position: relative;
    }

    .form-steps-wrapper {
        display: flex;
        width: 200%; /* Duas seções */
        transition: transform var(--transition-slide) ease-in-out; /* ANIMAÇÃO MAIS SIMPLES */
    }

    .form-step {
        width: 50%; /* Cada passo ocupa metade do wrapper de 200% */
        padding-right: 15px; /* Espaço para evitar que o conteúdo do próximo passo apareça na transição */
        flex-shrink: 0;
    }
    .form-step:first-child{
        padding-left: 0; /* Sem padding à esquerda para o primeiro */
        padding-right: 10px; /* Espaço entre os forms */
    }
     .form-step:last-child{
        padding-left: 10px; /* Espaço entre os forms */
        padding-right: 0; /* Sem padding à direita para o último */
    }


    .form-steps-wrapper.step-2-active {
        transform: translateX(-50%);
    }

    .step-navigation {
        margin-top: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .step-navigation .btn {
        min-width: 120px;
    }
    /* Garante que o botão "Continuar" do passo 1 seja alinhado à direita se não houver botão "Voltar" */
    #step1 .step-navigation {
        justify-content: flex-end;
    }
    
    .btn {
        display: inline-block; padding: 12px 28px; font-weight: 600;
        text-decoration: none; border-radius: var(--border-radius-md);
        transition: all var(--transition-std) cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer; border: 2px solid transparent; font-size: 1em;
        box-shadow: var(--sombra-padrao);
    }
    .btn-primary { background-color: var(--verde); color: var(--branco); }
    .btn-primary:hover {
        background-color: var(--verde-escuro);
        transform: translateY(-3px);
        box-shadow: var(--sombra-hover-forte);
    }
    .btn-secondary { 
        background-color: var(--cinza-claro);
        color: var(--cinza-escuro);
        border: 1px solid #ccd0d5;
        box-shadow: none;
    }
    .btn-secondary:hover {
        background-color: #e4e6e9;
        transform: translateY(-2px);
    }
    .btn-large { padding: 14px 35px; font-size: 1.05em;}
    
    .site-header {
        width: 100%; padding: 15px 0; background-color: var(--branco);
        box-shadow: 0 2px 10px rgba(0,0,0,0.08); z-index: 1000;
        position: relative;
    }
    .header-container {
        width: 90%; max-width: 1140px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between;
    }
    .site-header img.logo { height: 45px; width: auto; }
    .btn-close-header {
        background: none; border: none; font-size: 1.8em;
        color: var(--cinza-texto); cursor: pointer;
        padding: 5px; line-height: 1;
        transition: color var(--transition-fast);
    }
    .btn-close-header:hover { color: var(--cinza-escuro); }

    .site-footer-bottom {
        background-color: var(--cinza-escuro); color: #b0bec5;
        padding: 50px 0 30px; font-size: 0.9em; width: 100%;
        margin-top: auto;
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

    @media (max-width: 991px) {
        .cadastro-wrapper { flex-direction: column; }
        .cadastro-image-container { display: none; }
        .cadastro-form-content { width: 100%; padding: 30px 20px; }
        .form-step { padding: 0; } /* Sem padding extra em mobile */
    }

    @media (min-width: 992px) {
        .cadastro-form-content { flex-basis: 60%; flex-grow: 1; }
        .cadastro-image-container { display: flex; flex-basis: 40%; }
    }
    
    @media (max-width: 767px) {
        .site-header img.logo { height: 40px; }
        .btn-close-header { font-size: 1.6em; }
        .cadastro-form-content h1 { font-size: 1.8em; }
        .form-step h2 { font-size: 1.3em; }
        label, input, select, .toggle-label { font-size: 0.85em; }
        .btn { padding: 10px 20px; font-size: 0.9em; }
        .btn-large { padding: 12px 30px; font-size: 0.95em; }
        .step-navigation { flex-direction: column; gap: 10px; }
        .step-navigation .btn { width: 100%; }
        #step1 .step-navigation { justify-content: center; } /* Centraliza botão único no passo 1 mobile */
        #step2 .step-navigation .btn-secondary { order: -1; margin-bottom:10px; } /* Botão voltar em cima */
    }

  </style>
</head>
<body>

<header class="site-header">
  <div class="header-container">
    <a href="tela_inicial.php">
      <img style="margin-left: 104%;" class="logo" src="img/logo2.png" alt="Circuito Sustentável"/>
    </a>
    <button style="margin-left: 100%;" onclick="window.location.href='tela_inicial.php'" class="btn-close-header" aria-label="Fechar cadastro">&#10005;</button>
  </div>
</header>

<div class="container-geral">
    <div class="cadastro-wrapper">
        <div class="cadastro-form-content">
            <h1>Crie sua Conta</h1>
            <?php if (!empty($erro)):  ?>
              <p class="php-error-message"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <form method="POST" action="cadastro.php" id="cadastroForm">
                <div class="form-stepper-container">
                    <div class="form-steps-wrapper">
                        <div class="form-step active-step" id="step1">
                            <h2>Dados Pessoais</h2>
                            <label for="nome">Nome completo</label>
                            <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" />

                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" required oninput="this.value = formatarCPF(this.value)" maxlength="14" value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>" />

                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" required oninput="this.value = formatarTelefone(this.value)" maxlength="15" value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>" />

                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" required />

                            <div class="toggle-container" onclick="toggleSenha()">
                                <div id="bolinha" class="bolinha"></div>
                                <span class="toggle-label">Mostrar senha</span>
                            </div>
                            <div class="step1-js-error-message"></div> <div class="step-navigation">
                                <button type="button" class="btn btn-primary" id="btnContinuarPasso1">Continuar</button>
                            </div>
                        </div>

                        <div class="form-step" id="step2">
                            <h2>Seu Cotidiano</h2>
                            <label for="carro">Quanto tempo você dirige por dia?</label>
                            <select name="carro" id="carro" required>
                                <option value="nenhum" <?= ($_POST['carro'] ?? '') == 'nenhum' ? 'selected' : '' ?>> Não dirijo</option>
                                <option value="15_30" <?= ($_POST['carro'] ?? '') == '15_30' ? 'selected' : '' ?>> 15–30 minutos</option>
                                <option value="30_60" <?= ($_POST['carro'] ?? '') == '30_60' ? 'selected' : '' ?>> 30–60 minutos</option>
                                <option value="mais60" <?= ($_POST['carro'] ?? '') == 'mais60' ? 'selected' : '' ?>> Mais de 60 minutos</option>
                            </select>

                            <label for="onibus">Transporte público, bicicleta ou caminhada?</label>
                            <select name="onibus" id="onibus" required>
                                <option value="nenhum" <?= ($_POST['onibus'] ?? '') == 'nenhum' ? 'selected' : '' ?>> Menos de 15 minutos</option>
                                <option value="15_30" <?= ($_POST['onibus'] ?? '') == '15_30' ? 'selected' : '' ?>> 15–30 minutos</option>
                                <option value="30_60" <?= ($_POST['onibus'] ?? '') == '30_60' ? 'selected' : '' ?>> 30–60 minutos</option>
                                <option value="mais60" <?= ($_POST['onibus'] ?? '') == 'mais60' ? 'selected' : '' ?>> Mais de 60 minutos</option>
                            </select>

                            <label for="luz">Valor médio da sua conta de luz por mês?</label>
                            <select name="luz" id="luz" required>
                                <option value="ate100" <?= ($_POST['luz'] ?? '') == 'ate100' ? 'selected' : '' ?>> Até R$ 100</option>
                                <option value="100_300" <?= ($_POST['luz'] ?? '') == '100_300' ? 'selected' : '' ?>> R$ 100–300</option>
                                <option value="300_500" <?= ($_POST['luz'] ?? '') == '300_500' ? 'selected' : '' ?>> R$ 300–500</option>
                                <option value="mais500" <?= ($_POST['luz'] ?? '') == 'mais500' ? 'selected' : '' ?>> Mais de R$ 500</option>
                            </select>

                            <label for="gas">Frequência de troca do botijão de gás?</label>
                            <select name="gas" id="gas" required>
                                <option value="2meses" <?= ($_POST['gas'] ?? '') == '2meses' ? 'selected' : '' ?>> A cada 2 meses ou menos</option>
                                <option value="3_4meses" <?= ($_POST['gas'] ?? '') == '3_4meses' ? 'selected' : '' ?>> A cada 3–4 meses</option>
                                <option value="5_6meses" <?= ($_POST['gas'] ?? '') == '5_6meses' ? 'selected' : '' ?>> A cada 5–6 meses</option>
                                <option value="mais6" <?= ($_POST['gas'] ?? '') == 'mais6' ? 'selected' : '' ?>> Mais de 6 meses</option>
                            </select>

                            <label for="carne">Com que frequência você come carne vermelha?</label>
                            <select name="carne" id="carne" required>
                                <option value="0" <?= ($_POST['carne'] ?? '') == '0' ? 'selected' : '' ?>> Nunca</option>
                                <option value="1_2" <?= ($_POST['carne'] ?? '') == '1_2' ? 'selected' : '' ?>> 1–2 vezes</option>
                                <option value="3_5" <?= ($_POST['carne'] ?? '') == '3_5' ? 'selected' : '' ?>> 3–5 vezes</option>
                                <option value="todos" <?= ($_POST['carne'] ?? '') == 'todos' ? 'selected' : '' ?>> Todos os dias</option>
                            </select>

                            <label for="reciclagem">Você separa o lixo reciclável?</label>
                            <select name="reciclagem" id="reciclagem" required>
                                <option value="sempre" <?= ($_POST['reciclagem'] ?? '') == 'sempre' ? 'selected' : '' ?>> Sempre</option>
                                <option value="as_vezes" <?= ($_POST['reciclagem'] ?? '') == 'as_vezes' ? 'selected' : '' ?>> Às vezes</option>
                                <option value="raro" <?= ($_POST['reciclagem'] ?? '') == 'raro' ? 'selected' : '' ?>> Raramente</option>
                                <option value="nunca" <?= ($_POST['reciclagem'] ?? '') == 'nunca' ? 'selected' : '' ?>> Nunca</option>
                            </select>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="btnVoltarPasso2">Voltar</button>
                                <button type="submit" class="btn btn-primary btn-large">Cadastrar e Ver Meu Impacto</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="cadastro-image-container">
            <img src="img/cadastrop.png" alt="Ilustração para página de cadastro do Circuito Sustentável"/>
        </div>
    </div>
</div>

<footer class="site-footer-bottom">
    <div class="footer-content-grid">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Oferecendo solução para o meio ambiente e seu bolso.</p>
      </div>
      <div class="footer-col">
        <h4>Navegue</h4>
        <a href="tela_inicial.php">Início</a>
        <a href="login.php">Login</a>
        <a href="loja.php">Loja</a>
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
  function toggleSenha() {
    const senha = document.getElementById("senha");
    const bolinha = document.getElementById("bolinha");
    if (senha.type === "password") {
      senha.type = "text";
      bolinha.classList.add("ativa");
    } else {
      senha.type = "password";
      bolinha.classList.remove("ativa");
    }
  }

  function formatarCPF(cpf) {
    cpf = cpf.replace(/\D/g, ''); 
    if (cpf.length > 11) cpf = cpf.slice(0,11);
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2'); 
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2'); 
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2'); 
    return cpf;
  }

  function formatarTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    if (telefone.length > 11) telefone = telefone.slice(0,11);
    if (telefone.length > 10) { 
        telefone = telefone.replace(/^(\d{2})(\d{5})(\d{4}).*/,"($1) $2-$3");
    } else if (telefone.length > 6) { 
        telefone = telefone.replace(/^(\d{2})(\d{4})(\d{0,4}).*/,"($1) $2-$3");
    } else if (telefone.length > 2) { 
        telefone = telefone.replace(/^(\d{2})(\d{0,4}).*/,"($1) $2");
    } else if (telefone.length > 0) { 
        telefone = telefone.replace(/^(\d*)/,"($1");
    }
    return telefone;
  }

  
  const btnContinuarPasso1 = document.getElementById('btnContinuarPasso1');
  const btnVoltarPasso2 = document.getElementById('btnVoltarPasso2');
  const formStepsWrapper = document.querySelector('.form-steps-wrapper');
  const errorDisplayJS = document.querySelector('#step1 .step1-js-error-message');

  function validarPasso1() {
      const nome = document.getElementById('nome').value.trim();
      const email = document.getElementById('email').value.trim();
      const cpf = document.getElementById('cpf').value.trim();
      const telefone = document.getElementById('telefone').value.trim();
      const senha = document.getElementById('senha').value;
      let errosPasso1 = [];

      if(errorDisplayJS) errorDisplayJS.innerHTML = ""; // Limpa erros JS anteriores

      if (nome === '') errosPasso1.push("<li>Nome completo é obrigatório.</li>");
      if (email === '') {
        errosPasso1.push("<li>Email é obrigatório.</li>");
      } else {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errosPasso1.push("<li>Formato de email inválido.</li>");
        }
      }
      if (cpf === '' || cpf.length < 14) errosPasso1.push("<li>CPF inválido ou incompleto (use o formato XXX.XXX.XXX-XX).</li>");
      if (telefone === '' || telefone.length < 14) errosPasso1.push("<li>Telefone inválido ou incompleto (use o formato (XX) XXXXX-XXXX).</li>");
      if (senha === '') errosPasso1.push("<li>Senha é obrigatória.</li>");
      else if (senha.length < 6) errosPasso1.push("<li>Senha deve ter no mínimo 6 caracteres.</li>");

      if (errosPasso1.length > 0) {
          if(errorDisplayJS) errorDisplayJS.innerHTML = "<ul>" + errosPasso1.join("") + "</ul>";
          return false;
      }
      return true;
  }

  if (btnContinuarPasso1 && formStepsWrapper) {
    btnContinuarPasso1.addEventListener('click', () => {
        if (validarPasso1()) {
            formStepsWrapper.classList.add('step-2-active');
            if(errorDisplayJS) errorDisplayJS.innerHTML = ""; 
            const phpErrorDisplay = document.querySelector('.php-error-message');
            if(phpErrorDisplay) phpErrorDisplay.innerHTML = "";
        }
    });
  }

  if (btnVoltarPasso2 && formStepsWrapper) {
    btnVoltarPasso2.addEventListener('click', () => {
        formStepsWrapper.classList.remove('step-2-active');
        if(errorDisplayJS) errorDisplayJS.innerHTML = ""; // Limpa mensagens de erro ao voltar
    });
  }
  
  // Lógica para manter o passo e os dados em caso de erro PHP
  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($erro)): ?>
    const camposDoPasso2Preenchidos = 
      <?= (isset($_POST['carro']) && $_POST['carro'] !== '') ? 'true' : 'false'; ?> ||
      <?= (isset($_POST['onibus']) && $_POST['onibus'] !== '') ? 'true' : 'false'; ?>; // Adicione mais campos do passo 2 se necessário

  
    let irParaPasso2 = camposDoPasso2Preenchidos;
    <?php
     
      $errosPasso1PHP = ["Todos os campos de dados pessoais são obrigatórios!", "Formato de e-mail inválido!"];
      if (in_array($erro, $errosPasso1PHP)) {
          echo "irParaPasso2 = false;";
      }
    ?>

    if (irParaPasso2 && formStepsWrapper) {
        formStepsWrapper.classList.add('step-2-active');
    }

    <?php foreach ($_POST as $key => $value): ?>
      <?php if ($key !== 'senha'): ?>
        const field = document.querySelector(`[name="<?= htmlspecialchars($key, ENT_QUOTES) ?>"]`);
        if (field && (field.type === 'text' || field.type === 'email' || field.tagName === 'SELECT')) {
          field.value = `<?= htmlspecialchars(is_array($value) ? $value[0] : $value, ENT_QUOTES) ?>`;
        }
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>

</script>
</body>
</html>