<?php
include("conexao.php"); // Arquivo de conexão

session_start();
$id_usuario = $_SESSION['id_user'];
var_dump($id_usuario);

// fica peg as cartas(tem q adaptar)
$cartinhas = [];
$sql = "SELECT id, nome, img FROM cards ORDER BY RAND() LIMIT 2";
$resultado = $cone->query($sql);
while ($linha = $resultado->fetch_assoc()) {
    $cartinhas[] = $linha;
}

// Salva as cartas novas para o usuário
foreach ($cartinhas as $cartinha) {
    $sql_inserir = "INSERT INTO cli_cards (cli_id, card_id) VALUES ($id_usuario, {$cartinha['id']})";
    $cone->query($sql_inserir);
}

// Busca todas as cartas que o usuário já ganhou
$cartas_usuario = [];
$sql_todas = "SELECT c.nome, c.img FROM cli_cards cc JOIN cards c ON cc.card_id = c.id WHERE cc.cli_id = $id_usuario";
$resultado_todas = $cone->query($sql_todas);
while ($linha = $resultado_todas->fetch_assoc()) {
    $cartas_usuario[] = $linha;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Cartinhas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .cartinhas-container { display: flex; gap: 20px; flex-wrap: wrap; }
        .cartinha { border:1px solid #ccc; padding:10px; text-align:center; background: #fff; border-radius: 8px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Suas novas cartinhas:</h2>
    <div class='cartinhas-container'>
    <?php
    foreach ($cartinhas as $cartinha) {
        echo "<div class='cartinha'>";
        echo "<h3>" . htmlspecialchars($cartinha['nome']) . "</h3>";
        echo "<img src='" . htmlspecialchars($cartinha['img']) . "' alt='" . htmlspecialchars($cartinha['nome']) . "' style='width:120px;'><br>";
        echo "</div>";
    }
    ?>
    </div>

    <h2>Todas as cartinhas que você já ganhou:</h2>
    <div class='cartinhas-container'>
    <?php
    foreach ($cartas_usuario as $cartinha) {
        echo "<div class='cartinha'>";
        echo "<h3>" . htmlspecialchars($cartinha['nome']) . "</h3>";
        echo "<img src='" . htmlspecialchars($cartinha['img']) . "' alt='" . htmlspecialchars($cartinha['nome']) . "' style='width:120px;'><br>";
        echo "</div>";
    }
    ?>
    </div>
</body>
</html>