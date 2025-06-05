<?php
if (isset($_POST['btn'])) {
    include_once('code/conexao.php');

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $erros = [];

    // Verifica se a senha possui menos de 6 caracteres
    if (strlen($senha) < 6) {
        $erros[] = "A senha deve ter pelo menos 6 caracteres.";
    }

    // Protege contra SQL Injection
    $email = mysqli_real_escape_string($conexao, $email);

    // Verifica se o e-mail já está cadastrado
    $resultado = mysqli_query($conexao, "SELECT email FROM usuario WHERE email = '$email'");
    if (mysqli_num_rows($resultado) > 0) {
        $erros[] = "Este e-mail já está cadastrado.";
    }

    // Se não houver erros, insere no banco
    if (empty($erros)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = mysqli_query($conexao, 
            "INSERT INTO usuario (nome, email, senha) 
             VALUES ('$nome', '$email', '$senha_hash')");

        if ($sql) {
            echo "Usuário cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar usuário: " . mysqli_error($conexao);
        }
    } else {
        foreach ($erros as $erro) {
            echo "<p style='color:red;'>$erro</p>";
        }
    }
}
?>
