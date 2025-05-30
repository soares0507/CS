<?php
session_start();
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conexao.php';

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf']; 
    $telefone = $_POST['telefone'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);

    // Verifica se já existe email ou cpf
    $sql_verifica = "SELECT * FROM Cliente WHERE email = '$email' OR cpf = '$cpf'";
    $res_verifica = $conexao->query($sql_verifica);
    if ($res_verifica && $res_verifica->num_rows > 0) {
        $erro = "CPF ou e-mail já está registrado!";
    } else {
        $sql_cliente = "INSERT INTO Cliente (nome, email, cpf, senha, telefone) VALUES ('$nome', '$email', '$cpf', '$senha', '$telefone')";
        if ($conexao->query($sql_cliente) === TRUE) {
            $id_cliente = $conexao->insert_id;

            $carro = $_POST['carro'];
            $onibus = $_POST['onibus'];
            $luz = $_POST['luz'];
            $gas = $_POST['gas'];
            $carne = $_POST['carne'];
            $reciclagem = $_POST['reciclagem'];

            $sql_cotidiano = "INSERT INTO Cotidiano (id_cliente, carro, onibus, luz, gas, carne, reciclagem) 
                              VALUES ('$id_cliente', '$carro', '$onibus', '$luz', '$gas', '$carne', '$reciclagem')";
            if ($conexao->query($sql_cotidiano) === TRUE) {
                // Login automático após cadastro
                $_SESSION['usuario_id'] = $id_cliente;
                header('Location: loja.php');
                exit;
            } else {
                $erro = "Erro ao salvar cotidiano: " . $conexao->error;
            }
        } else {
            $erro = "Erro ao salvar cadastro: " . $conexao->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background-color: #2ab769;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    
    header {
      width: 100%; /* Faz o header ocupar toda a largura */
      text-align: center;
      margin: 0;
      padding: 15px 0;
      background-color: #ffffff;
      border-bottom: 1px solid #ccc;
      box-sizing: border-box; /* Garante que padding e bordas sejam incluídos no tamanho total */
    }

    header img {
      height: 40px; /* Reduz a altura da imagem */
      max-width: 100%; /* Garante que a imagem não ultrapasse o tamanho do header */
    }

    .container {
      display: flex;
      justify-content: space-between; /* Ajusta o espaço entre os elementos */
      margin-bottom: 50px;
      width: 100%;
      padding: 0 50px; /* Adiciona espaçamento horizontal */
      box-sizing: border-box;
      margin-top: 5%;
    }

    .form-box, .form-box1 {
      flex: 1; /* Faz os formulários ocuparem espaço proporcional */
      margin-right: 20px; /* Espaçamento entre os formulários e a imagem */
    }

    .image-box {
      flex: 1; /* Faz a imagem ocupar espaço proporcional */
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .image-box img {
      max-width: 100%; /* Garante que a imagem não ultrapasse o tamanho do container */
      height: auto;
      border-radius: 20px; /* Adiciona bordas arredondadas à imagem */
      margin-top: 3%;
    }

    .form-box {
      background-color: #2ab769;
      padding: 30px;
      border-radius: 30px;
      width: 320px;
      color: white;
      max-height: 490px; /* Define uma altura máxima */
      overflow-y: auto; /* Adiciona barra de rolagem vertical */
    }

    .form-box h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    .form-box1 {
      background-color: #2ab769;
      padding: 30px;
      border-radius: 30px;
      width: 320px;
      color: white;
    }

    .form-box1 h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    label {
      display: block;
      font-weight: bold;
      margin-top: 15px;
      font-size: 20px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 20px;
      border: 2px solid #4d4d4d;
      font-size: 16px;
      box-sizing: border-box;
    }

    .button {
      display: block;
      margin: 30px auto 0 auto;
      padding: 20px 35px;
      background-color: white;
      border: none;
      border-radius: 10px;
      color: #2e7d32;
      font-weight: bold;
      font-size: 18px;
      cursor: pointer;
      margin-left: 32%;
      transition: background 0.2s;
    }

    .button:hover {
      background-color: #1f804e;
      color: #fff;
    }

    .toggle-container {
      margin-top: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .bolinha {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: 2px solid #2ab769;
      background-color: white;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .bolinha.ativa {
      background-color:rgb(0, 70, 31);
    }

    .toggle-label {
      font-size: 16px;
      color: white;
      margin-top: -1px;
    }

    .im{
        width: 19%;
        height: auto;
        max-width: 600px;
        margin: 0 auto;
        display: block;
    }

    select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 20px;
      border: 2px solid #4d4d4d;
      font-size: 16px;
      box-sizing: border-box;
      background-color: #ffffff;
      color: #2e7d32;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    select:hover {
      border-color: #2ab769;
      background-color: #e8f5e9;
    }

    option {
      padding: 10px;
      background-color: #ffffff;
      color: #2e7d32;
      font-weight: bold;
    }

    option:hover {
      background-color: #e8f5e9;
      color: #1b5e20;
    }

    .vertical-separator {
      width: 10px;
      background-color: #ffffff;
      margin: 0 30px;
      height: auto;
    }

    .btn-x {
      background: none;
      border: none;
      font-size: 2rem;
      color: #1f804e;
      cursor: pointer;
      font-weight: bold;
      margin-top: -50px;
      margin-left: 10px;
    }

    .footer-novo {
      background: #1b2430;
      color: #fff;
      padding: 2.5rem 1rem 1rem 1rem;
      margin-top: 2rem;
      width: 100vw;
      position: relative;
      left: 0%;
      right: 50%;
      margin-left: -50vw;
      margin-right: -50vw;
      box-sizing: border-box;
    }

    
    .footer-novo {
      background: #1b2430;
      color: #fff;
      padding: 2.5rem 1rem 1rem 1rem;
      margin-top: 2rem;
      width: 100vw;
      position: relative;
      left: 0%;
      right: 50%;
      margin-left: -50vw;
      margin-right: -50vw;
      box-sizing: border-box;
    }
.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 2rem;
  max-width: 1100px;
  margin: 0 auto;
}
.footer-col {
  min-width: 180px;
  flex: 1;
}
.footer-col h4 {
  margin-bottom: 1rem;
  color:rgb(255, 255, 255);
}
.footer-col a {
  color: #cfd8dc;
  text-decoration: none;
  display: block;
  margin-bottom: 0.5rem;
  font-size: 1rem;
  transition: color 0.2s;
}
.footer-col a:hover {
  color: #28a060;
}
.footer-bottom {
  text-align: center;
  color: #aaa;
  font-size: 0.95rem;
  margin-top: 2rem;
  border-top: 1px solid #333;
  padding-top: 1rem;
}
footer p {
 color:rgb(156, 163, 175);
;
}
  </style>
</head>
<body>

<header>
  <img class="im" src="img/logo2.png" alt="Circuito Sustentável"/>
  <div class="btn-x-container">
    <button onclick="window.location.href='tela_inicial.php'" class="btn-x">&#10005;</button>
  </div>
</header>
<style>
  .btn-x-container {
    width: 100%;
    display: flex;
    justify-content: flex-start;
  }
</style>


<div class="container">
  <!-- Formulário de cadastro -->
  <div class="form-box1">
    <h2>Cadastre-se</h2>
    <form method="POST" action="cadastro.php">
      <div class="form-section">
        <?php if (!empty($erro)): ?>
          <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" required />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required />

        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" required oninput="this.value = formatarCPF(this.value)" />

        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" required oninput="this.value = formatarTelefone(this.value)" />

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required />

        <div class="toggle-container">
          <div id="bolinha" class="bolinha" onclick="toggleSenha()"></div>
          <label class="toggle-label">Mostrar senha</label>
        </div>
      </div>
  </div>
  <div class="vertical-separator"></div> <!-- Adiciona o traço vertical -->
  <!-- Formulário de cotidiano -->
  <div class="form-box">
    <h2>Conte seu cotidiano</h2>
    <div class="form-section">
      <label for="carro">Quanto tempo você dirige por dia?</label>
      <select name="carro" id="carro" required>
        <option value="nenhum"> Não dirijo</option>
        <option value="15_30"> 15–30 minutos</option>
        <option value="30_60"> 30–60 minutos</option>
        <option value="mais60"> Mais de 60 minutos</option>
      </select>

      <label for="onibus">Quanto tempo você usa transporte público, bicicleta ou caminha?</label>
      <select name="onibus" id="onibus" required>
        <option value="nenhum"> Menos de 15 minutos</option>
        <option value="15_30"> 15–30 minutos</option>
        <option value="30_60"> 30–60 minutos</option>
        <option value="mais60"> Mais de 60 minutos</option>
      </select>

      <label for="luz">Qual o valor médio da sua conta de luz por mês?</label>
      <select name="luz" id="luz" required>
        <option value="ate100"> Até R$ 100</option>
        <option value="100_300"> R$ 100–300</option>
        <option value="300_500"> R$ 300–500</option>
        <option value="mais500"> Mais de R$ 500</option>
      </select>

      <label for="gas">Com que frequência você troca o botijão de gás?</label>
      <select name="gas" id="gas" required>
        <option value="2meses"> A cada 2 meses ou menos</option>
        <option value="3_4meses"> A cada 3–4 meses</option>
        <option value="5_6meses"> A cada 5–6 meses</option>
        <option value="mais6"> Mais de 6 meses</option>
      </select>

      <label for="carne">Quantas vezes por semana você come carne vermelha?</label>
      <select name="carne" id="carne" required>
        <option value="0"> Nunca</option>
        <option value="1_2"> 1–2 vezes</option>
        <option value="3_5"> 3–5 vezes</option>
        <option value="todos"> Todos os dias</option>
      </select>

      <label for="reciclagem">Você separa o lixo reciclável?</label>
      <select name="reciclagem" id="reciclagem" required>
        <option value="sempre"> Sempre</option>
        <option value="as_vezes"> Às vezes</option>
        <option value="raro"> Raramente</option>
        <option value="nunca"> Nunca</option>
      </select>
    </div>
  </div>
  <!-- Adiciona a imagem à direita -->
  <div class="image-box">
    <img src="img/cadastro.png" alt="Imagem Sustentável" />
  </div>
</div>

<button type="submit" class="button" >Continuar</button>
</form>

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
    cpf = cpf.slice(0, 11);
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2'); 
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2'); 
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2'); 
    return cpf;
  }

  function formatarTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    telefone = telefone.slice(0, 11);
    if (telefone.length > 0) {
      telefone = '(' + telefone;
    }
    if (telefone.length > 3) {
      telefone = telefone.slice(0, 3) + ') ' + telefone.slice(3);
    }
    if (telefone.length > 10) {
      telefone = telefone.slice(0, 10) + '-' + telefone.slice(10);
    }
    return telefone;
  }
</script>
<footer class="footer-novo">
    <div class="footer-container">
      <div class="footer-col">
        <h4>Circuito Sustentável</h4>
        <p>Oferecendo solução para o meio ambiente e seu bolso.</p>
      </div>
      <div class="footer-col">
        <h4>Contato</h4>
        <p>📧 circuito_sustentavel@gmail.com</p>
        <p>📞 (85) 992933310</p>
      </div>
    </div>
    <div class="footer-bottom">
      &copy; 2025 Circuito Sustentável Inc. Todos os direitos reservados.
    </div>
</footer>
</body>
</html>
