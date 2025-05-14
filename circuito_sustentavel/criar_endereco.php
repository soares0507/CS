<?php
session_start();
include 'conexao.php';

$is_cliente = isset($_SESSION['usuario_id']);
$is_vendedor = isset($_SESSION['vendedor_id']);

if (!$is_cliente && !$is_vendedor) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];

    if ($is_cliente) {
        $id_cliente = $_SESSION['usuario_id'];
        $sql = "INSERT INTO Endereco (id_cliente, rua, numero, complemento, bairro, cidade, estado, cep) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("isssssss", $id_cliente, $rua, $numero, $complemento, $bairro, $cidade, $estado, $cep);
    } else {
        $id_vendedor = $_SESSION['vendedor_id'];
        $sql = "INSERT INTO Endereco (id_vendedor, rua, numero, complemento, bairro, cidade, estado, cep) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("isssssss", $id_vendedor, $rua, $numero, $complemento, $bairro, $cidade, $estado, $cep);
    }

    if ($stmt->execute()) {
        header('Location: ' . ($is_cliente ? 'usuario.php' : 'vendedor.php'));
        exit;
    } else {
        $erro = "Erro ao cadastrar endereço.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Endereço</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; background-color: #d4d3c8; }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: white;
      padding: 20px;
    }
    .logo img { height: 40px; }
    main {
      padding: 30px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .form-box {
      background: white;
      border-radius: 10px;
      padding: 30px 40px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      margin-top: 40px;
    }
    .form-box h2 {
      color: #28a060;
      margin-bottom: 20px;
      text-align: center;
    }
    .form-group {
      margin-bottom: 18px;
    }
    .form-group label {
      display: block;
      margin-bottom: 6px;
      color: #333;
      font-weight: bold;
    }
    .form-group input {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
      background: #f3f2e7;
    }
    .form-actions {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }
    .form-actions button {
      background: #28a060;
      color: white;
      padding: 10px 25px;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.2s;
    }
    .form-actions button:hover {
      background: #1b5e20;
    }
    .erro {
      color: #d43131;
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <img src="img/logo2.png" alt="Logo">
    </div>
  </header>
  <main>
    <div class="form-box">
      <h2>Cadastrar Endereço</h2>
      <?php if (!empty($erro)): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="form-group">
          <label for="rua">Rua</label>
          <input type="text" id="rua" name="rua" required>
        </div>
        <div class="form-group">
          <label for="numero">Número</label>
          <input type="text" id="numero" name="numero" required>
        </div>
        <div class="form-group">
          <label for="complemento">Complemento</label>
          <input type="text" id="complemento" name="complemento">
        </div>
        <div class="form-group">
          <label for="bairro">Bairro</label>
          <input type="text" id="bairro" name="bairro" required>
        </div>
        <div class="form-group">
          <label for="cidade">Cidade</label>
          <input type="text" id="cidade" name="cidade" required>
        </div>
        <div class="form-group">
          <label for="estado">Estado</label>
          <input type="text" id="estado" name="estado" required>
        </div>
        <div class="form-group">
          <label for="cep">CEP</label>
          <input type="text" id="cep" name="cep" required>
        </div>
        <div class="form-actions">
          <button type="submit">Salvar Endereço</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
