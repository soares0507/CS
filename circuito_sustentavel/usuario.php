<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área do Cliente - Circuito Sustentável</title>
  
  <style>* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: sans-serif;
  background-color: #d4d3c8;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 20px;
}

.logo {
  font-size: 24px;
}

.logo .verde {
  color: green;
}

.logo .sub {
  font-size: 12px;
  margin-top: 4px;
}

.search-container {
      margin: 1rem auto 0 auto;
      display: flex;
      justify-content: center;
      position: relative;
    }

    .search-container input {
      width: 140%;
      padding: 10px 400px 10px 20px;
      border: none;
      background: #f3f2e7;
      border-radius: 20px;
      font-size: 1rem;
      margin-left:-40px;
    }

    .search-container img.lupa {
      position: absolute;
      right: 3%;
      top: 50%;
      transform: translateY(-50%);
      height: 20px;
    }

.icon {
  margin-left: 10px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

main {
  padding: 30px;
}

.boas-vindas {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 20px;
}

.avatar {
  font-size: 80px;
}

.mensagem h1 {
  font-size: 32px;
  font-weight: bold;
}

.mensagem p {
  font-size: 18px;
  margin-top: 5px;
}

.atalhos {
  display: flex;
  gap: -400px;
  margin: 30px 0;
  width: 10%;
  margin-top:-40px;
 
}



.painel {
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
}

.box {
  background: white;
  border-radius: 10px;
  padding: 20px;
  flex: 1 1 300px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.box h2 {
  font-size: 22px;search-container
  text-decoration: none;
  color: green;
}

.excluir {
  margin-top: 20px;
  background: #d43131;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

footer .rodape {
  margin-top: 40px;
  padding: 20px;
  background: #f6f3b5;
}

.botao img {
      height: 200px; /* Reduz o tamanho das imagens */
      width: auto; /* Mantém a proporção */
      margin-left:10px;

}
.box img{
    height: 50px; /* Reduz o tamanho das imagens */
      width: auto; 
}
.user-info img{
    height: 50px; /* Reduz o tamanho das imagens */
      width: auto;
}
.im img{
  margin-left:8px;
}search-container

</style>
</head>
<body>
    
  <header>
    <div class="logo">
    <a href="loja.php"> <img src="img/logo2.png" alt=""> </a>
  
    </div>
    <div class="search-container">
      <input type="text" placeholder="Buscar...">
      <img src="img/lupa.png" alt="pesquisar" class="lupa">
    </div>
    <div class="user-info">
      <span class="user-icon"> <img src="img/user.png" alt=""></span>
      <p>Fulano de tal</p>
    </div>
  </header>

  <main>
    <section class="boas-vindas">
      <div class="avatar"><img src="img/user.png" alt=""></div>
      <div class="mensagem">
        <h1>Olá, Fulano de tal</h1>
        <p>Aqui você encontra todas as informações relacionadas à sua conta</p>
      </div>
    </section>

    <div class="atalhos">
      <div class="botao"><a class="im" href="dados.php">  <img src="img/dados.png" alt=""></a></div>
      <div class="botao"><a  href="assinatura_usuario.php">  <img src="img/assinatura.png" alt=""></a></div>
      <div class="botao"><a  href="perguntas.php">  <img src="img/perguntas.png" alt=""></a> </div>
      <div class="botao"><a href="pedidos.php">  <img src="img/pedidos.png" alt=""></a>  </div>
      <div class="botao"><a href="vender.php">  <img src="img/vender.png" alt=""></a>  </div>
    </div>
    </div>

    <div class="painel">
      <div class="box">
        <h2>📍 Endereços</h2>
        <a href="#">Ver todos</a>
      </div>

      <div class="box">
        <h2><img src="img/user.png" alt=""> Meus dados <a href="#" class="editar">Editar</a></h2>
        <p>🧍 Fulano de tal</p>
        <p>✉️ email@gmail.com</p>
        <button class="excluir">EXCLUIR CONTA</button>
      </div>
    </div>
  </main>

  
</body>
</html>
    
