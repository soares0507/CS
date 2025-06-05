<?php
include_once("code/conexao.php");
include_once("code/loginC.php"); 


if (!isset($_SESSION['nome']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id'];
$trees_newly_acquired = []; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ganhar'])) {
        $sql = "SELECT id, nome, img, raridade FROM cards ORDER BY RAND() LIMIT 2";
    } elseif (isset($_POST['ganhar5'])) {
        $sql = "SELECT id, nome, img, raridade FROM cards ORDER BY RAND() LIMIT 5";
    }
    if (isset($sql)) {
        $result = $conexao->query($sql);
        if ($result) {
            while ($linha = $result->fetch_assoc()) {
                $trees_newly_acquired[] = $linha;
            }
            foreach ($trees_newly_acquired as $tree) {
                $tree_id_escaped = $conexao->real_escape_string($tree['id']);
                $id_user_escaped = $conexao->real_escape_string($id_user);
                $sql_in = "INSERT INTO user_cards (id_user, id_card) VALUES ('$id_user_escaped', '$tree_id_escaped')";
                if (!$conexao->query($sql_in)) {
                    error_log("Error associating tree with user $id_user: " . $conexao->error);
                }
            }
        } else {
            error_log("Error fetching new trees: " . $conexao->error);
        }
    }
}

$user_trees_collection = [];
$id_user_escaped_for_select = $conexao->real_escape_string($id_user);
// Assuming 'user_cards' and 'cards' tables are used generically
$sql_todes = "SELECT c.id, c.nome, c.img, c.raridade FROM user_cards cc JOIN cards c ON cc.id_card = c.id WHERE cc.id_user = '$id_user_escaped_for_select' ORDER BY c.nome ASC";
$result_todes = $conexao->query($sql_todes);
if ($result_todes) {
    while ($linha = $result_todes->fetch_assoc()) {
        $user_trees_collection[] = $linha;
    }
} else {
    error_log("Error fetching user's tree collection: " . $conexao->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Bosque e Loja de Árvores | Treedom</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3a7d44;
            --secondary: #2d5a34;
            --accent: #5cb85c;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --card-bg: #ffffff; /* Renaming this var might be good, but CSS class names are generic enough */
            --card-shadow: rgba(0,0,0,0.08);
            --success: #28a745; /* Green for success messages */
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--dark);
            background-color: var(--light);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
        }

        /* Navigation */
        nav {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 10px 0;
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-links li {
            margin-left: 25px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            padding: 10px 5px;
            display: flex; 
            align-items: center; 
            position: relative;
            transition: color 0.3s ease;
        }
        
        .nav-links a:hover,
        .nav-links a:focus {
            color: var(--accent);
        }

        .dropdown { position: relative; }
        .dropdown .seta {
          font-size: 0.7em; margin-left: 6px; line-height: 1;
          transition: transform 0.3s ease; display: inline-block;
        }
        .submenu {
          position: absolute; top: 100%; left: 0; background: var(--dark);
          border-radius: 6px; list-style: none; padding: 8px 0; margin: 8px 0 0 0;
          min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          opacity: 0; visibility: hidden; transform: translateY(10px);
          transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
          z-index: 10;
        }
        .submenu li a {
          display: block; padding: 10px 20px; color: var(--light);
          font-weight: 400; font-size: 0.95em; text-decoration: none;
          white-space: nowrap; transition: background-color 0.3s ease, color 0.3s ease;
        }
        .submenu li a:hover, .submenu li a:focus {
          background-color: var(--secondary); color: var(--white);
        }
        .dropdown:hover > .submenu, .dropdown:focus-within > .submenu {
          opacity: 1; visibility: visible; transform: translateY(0);
        }
        .dropdown:hover > a .seta, .dropdown:focus-within > a .seta {
          transform: rotate(180deg);
        }
        
        .page-title-container {
            text-align: center;
            padding: 30px 20px 10px; /* Adjusted padding */
        }
        .page-title-container h1 {
            font-size: 2.8rem;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px; /* Consistent padding */
        }
        
        .section-title { /* General section title */
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2rem; /* Slightly smaller for sub-sections */
        }

        /* Store Item Section */
        .store-item-section {
            padding: 30px 20px;
            background: var(--white);
            margin-bottom: 40px;
            border-radius: 12px; /* Softer radius */
            box-shadow: 0 8px 25px var(--card-shadow); /* More pronounced shadow for store */
        }

        .store-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            max-width: 700px; 
            margin: 0 auto; 
        }
        @media (min-width: 600px) {
            .store-item {
                flex-direction: row;
                text-align: left;
                gap: 35px;
                align-items: center;
                justify-content: flex-start; 
            }
        }

        .store-item-image {
            width: 100%;
            max-width: 220px;
            height: auto;
            border-radius: 8px;
            transition: transform 0.3s ease;
            display: block;
            /* Ajustes de margem para a imagem do pacote, se necessário, podem ser reavaliados ou removidos se não forem desejados para a imagem da árvore/muda */
            /* margin-left: -25%; */ /* Exemplo de ajuste anterior, pode não ser ideal para árvores */
            margin-right: auto;
            /* margin-top: -15%; */ /* Exemplo de ajuste anterior */
        }
        .store-item-image:hover {
            transform: scale(1.05);
        }

        .store-item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center; 
        }
        @media (min-width: 600px) {
             .store-item-details {
                align-items: flex-start; 
            }
        }


        .store-item-details h3 { 
            font-size: 1.9rem; 
            color: var(--primary);
            margin-top: 0;
            margin-bottom: 8px;
        }

        .store-item-description {
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--dark);
            max-width: 350px; 
            text-align: center;
        }
         @media (min-width: 600px) {
             .store-item-description {
                text-align: left;
            }
        }


        .store-item-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 20px;
        }
        .store-item-price span {
            font-size: 0.9rem;
            font-weight: 400;
            color: var(--dark);
        }

        .btn {
            display: inline-block;
            background: var(--accent);
            color: var(--white) !important; 
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn:hover, .btn:focus {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .btn-buy { 
            background-color: var(--accent); 
            padding: 14px 35px;
            font-size: 1.1rem;
            width: 100%; 
            max-width: 250px; 
        }
        .btn-buy:hover, .btn-buy:focus {
            background-color: var(--primary); 
            transform: translateY(-2px) scale(1.02);
        }

        /* Reveal Section for New Items */
        .reveal-section {
            padding: 30px 20px 40px;
            margin-bottom: 40px;
            background-color: var(--white); 
            border-radius: 12px;
            box-shadow: 0 8px 25px var(--card-shadow);
            text-align: center;
        }

        .reveal-section .congrats-title {
            font-size: 2.8rem;
            color: var(--success); 
            margin-bottom: 10px;
            animation: fadeInDrop 0.5s ease-out forwards;
        }

        .reveal-section .reveal-subtitle {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 30px;
            animation: fadeIn 0.5s ease-out 0.2s forwards; 
            opacity: 0; 
        }
        
        @keyframes fadeInDrop {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Item display styles (generic for tree or card) */
        .item-grid { /* Renamed from card-grid for generality, but CSS can stay if .card-grid is used in HTML */
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
            gap: 20px; 
        }
        
        .item-grid.centered-grid { /* Used for newly acquired items */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: nowrap; 
        }
        .item-grid.centered-grid .tree-display-item { /* Renamed from .game-card for clarity */
            width: 180px;
            min-width: 180px;
            max-width: 180px;
        }
        
        .tree-display-item { /* Renamed from .game-card for clarity */
            background: var(--card-bg);
            border-radius: 10px; 
            overflow: hidden;
            box-shadow: 0 4px 12px var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .tree-display-item:hover { /* Renamed from .game-card:hover */
            transform: translateY(-6px) scale(1.03); 
            box-shadow: 0 7px 20px rgba(0,0,0,0.12);
        }
        
        .tree-display-item img { /* Renamed from .game-card img */
            width: 100%;
            height: auto;
            aspect-ratio: 3/4.2; 
            object-fit: contain;
            background: #f8f9fa;
            border-bottom: 1px solid #eee; 
            cursor: pointer; /* Adicionado para indicar que é clicável */
        }
        
        .tree-display-item-content { /* Renamed from .game-card-content */
            padding: 12px; 
            text-align: center;
            flex-grow: 1; 
        }
        
        .tree-display-item-content h3 { /* Renamed from .game-card-content h3 */
            margin-top: 0;
            margin-bottom: 0; 
            font-size: 1rem; 
            /* color removido para usar por classe de raridade */
            font-family: 'Montserrat', sans-serif; 
            font-weight: 600;
        }
        /* Cores por raridade */
        .raridade-comum { color: #3a7d44; }      /* verde */
        .raridade-rara { color: #2196f3; }       /* azul */
        .raridade-epica { color: #a259e6; }      /* roxo */
        .raridade-lendaria { 
            color: #ffd700;                      /* dourado */
            text-shadow: 0 0 6px #fffbe6, 0 0 12px #ffd70099;
        }

        /* Animation for revealed items */
        .revealed-item { /* Renamed from .revealed-card */
            opacity: 0; 
        }

        @keyframes revealItemAnimation { /* Renamed from revealCardAnimation */
            0% {
                opacity: 0;
                transform: translateY(40px) scale(0.7) rotateY(-90deg);
            }
            60% {
                 transform: translateY(-10px) scale(1.05) rotateY(10deg);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1) rotateY(0deg);
            }
        }

        /* Footer */
        footer {
            background: var(--dark); color: var(--light);
            padding: 30px 20px; text-align: center; margin-top: 240px; /* Reduced margin-top from 130px */
        }
        footer p { margin:0; font-size: 0.9rem; }

        /* Scroll animation (general) */
        .scroll-animate {
            opacity: 0;
            transform: translateY(25px); 
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        .scroll-animate.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title-container h1 { font-size: 2.2rem; }
            .nav-container { flex-direction: column; align-items: center; }
            .nav-links { margin-top: 15px; }
            .nav-links li { margin: 0 8px; }
            .section-title { font-size: 1.8rem; }
            .store-item-section .section-title { font-size: 2rem; } 
            .item-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px;}
        }
        @media (max-width: 480px) {
            .nav-links { flex-direction: column; align-items: center; width: 100%;}
            .nav-links li { margin: 8px 0; width: 100%; text-align: center;}
            .nav-links li a { display: block; padding: 8px; width:100%; justify-content: center;}
            .dropdown { width: 100%; }
            .dropdown > a { justify-content: center; }
            .submenu { width: 100%; box-sizing: border-box;}
            .item-grid { grid-template-columns: 1fr 1fr; gap: 12px;} /* two items per row on smallest screens */
            .store-item-details h3 { font-size: 1.6rem; }
            .store-item-price { font-size: 1.1rem; }
            .btn-buy { padding: 12px 25px; font-size: 1rem;}
            .reveal-section .congrats-title { font-size: 2.2rem; }
            .store-item-image { /* Adjustments for very small screens if image was too pushed */
                 margin-left: auto; /* Center image on small screens if flex-direction is column */
                 margin-right: auto;
                 margin-top: 0;
            }
        }

        /* New CSS for aligning store items side by side */
        .store-items-row {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        @media (min-width: 600px) {
            .store-items-row {
                flex-direction: row;
                justify-content: space-between;
                align-items: flex-start;
            }
        }

        /* Modal para imagem ampliada */
        .modal-img-viewer {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.85);
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s;
        }
        .modal-img-viewer.active {
            display: flex;
        }
        .modal-img-viewer img {
            max-width: 90vw;
            max-height: 85vh;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            background: #fff;
        }
        .modal-img-viewer .close-modal {
            position: absolute;
            top: 30px;
            right: 40px;
            font-size: 2.5rem;
            color: #fff;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 10001;
            font-weight: bold;
            transition: color 0.2s;
        }
        .modal-img-viewer .close-modal:hover {
            color: var(--accent);
        }

    </style>
</head>
<body>
    <nav>
        <div class="nav-container">
            <a href="inipage.php" class="logo">Treedom</a>
            <ul class="nav-links">
                <li><a href="inipage.php#work">Nosso Trabalho</a></li>
                <li><a href="inipage.php#about">Sobre</a></li>
                <li><a href="loja.php">Loja de Árvores</a></li>
                <li class="dropdown">
                    <?php if(isset($_SESSION['nome'])): ?>
                        <a href="#"><?php echo htmlspecialchars($_SESSION['nome']);?> <span class="seta">&#9660;</span></a>
                        <ul class="submenu">
                            <li><a href="loja.php">Minhas Árvores</a></li> 
                            <li><a href="inventario.php">Inventário</a></li> 
                            <li><a href="code/logout.php">Sair</a></li>
                        </ul>
                    <?php else: ?>
                        <a href="#">Login/Cadastro <span class="seta">&#9660;</span></a>
                        <ul class="submenu">
                            <li><a href="login.php">Login</a></li>
                            <li><a href="cadastro.php">Cadastro</a></li>
                        </ul>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <div class="page-title-container scroll-animate">
        <h1>Loja de Árvores</h1>
    </div>
    
    <div class="container">
        <section class="store-item-section scroll-animate">
           
            <div class="store-items-row">
                <div class="store-item">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQPSnpzOOJ_IIWn5ulOjQRCCZnd1S8b7ri4sQ&s" alt="Kit de Plantio de Árvores" class="store-item-image">
                    <div class="store-item-details">
                        <h3>Kit Sementes Raras</h3>
                        <p class="store-item-description">Adquira este kit para cultivar 2 árvores e adicioná-las ao seu bosque!</p>
                        <p class="store-item-price">Preço: <span>R$20</span></p>
                        <form action="loja.php#newly-acquired" method="post">
                            <button type="submit" name="ganhar" class="btn btn-buy">Adquirir Kit</button>
                        </form>
                    </div>
                </div>
                <div class="store-item">
                    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80" alt="Kit Floresta Diversa" class="store-item-image">
                    <div class="store-item-details">
                        <h3>Kit Floresta Diversa</h3>
                        <p class="store-item-description">Receba 5 árvores diferentes para enriquecer ainda mais seu bosque!</p>
                        <p class="store-item-price">Preço: <span>R$50</span></p>
                        <form action="loja.php#newly-acquired" method="post">
                            <button type="submit" name="ganhar5" class="btn btn-buy">Adquirir Kit Floresta</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['ganhar']) || isset($_POST['ganhar5'])) && !empty($trees_newly_acquired)): ?>
        <section id="newly-acquired" class="reveal-section"> 
            <h2 class="congrats-title">Parabéns!</h2>
            <p class="reveal-subtitle">Você cultivou novas árvores:</p>
            <div class="item-grid centered-grid">
                <?php foreach($trees_newly_acquired as $index => $tree): ?>
                    <?php
                        $raridade = isset($tree['raridade']) ? strtolower($tree['raridade']) : 'comum';
                        $classeRaridade = 'raridade-' . $raridade;
                    ?>
                    <div class="tree-display-item revealed-item" style="animation: revealItemAnimation 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) <?php echo $index * 0.25; ?>s forwards;">
                        <img src="<?php echo htmlspecialchars($tree['img']); ?>" alt="<?php echo htmlspecialchars($tree['nome']); ?>" class="tree-img-clickable">
                        <div class="tree-display-item-content">
                            <h3 class="<?php echo $classeRaridade; ?>"><?php echo htmlspecialchars($tree['nome']); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <hr style="margin: 40px auto; border: 0; border-top: 1px solid #ddd; width: 80%;" class="scroll-animate">
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['ganhar']) || isset($_POST['ganhar5'])) && empty($trees_newly_acquired)): ?>
        <section id="no-new-items" class="reveal-section scroll-animate">
            <h2 class="section-title" style="color: var(--primary);">Oops!</h2>
             <p style="text-align:center; font-size: 1.1rem;">Não foi possível cultivar novas árvores desta vez. Parece que o kit não germinou. Tente novamente!</p>
        </section>
        <hr style="margin: 40px auto; border: 0; border-top: 1px solid #ddd; width: 80%;" class="scroll-animate">
        <?php endif; ?>

       
    </div>
    
    <footer class="scroll-animate">
        <p>© <?php echo date("Y"); ?> Treedom. Todos os direitos reservados.</p>
    </footer>

    <!-- Modal para visualização da imagem -->
    <div class="modal-img-viewer" id="modalImgViewer">
        <button class="close-modal" id="closeModalBtn" aria-label="Fechar imagem ampliada">&times;</button>
        <img src="" alt="Árvore ampliada" id="modalImgTag">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.scroll-animate');

            if ("IntersectionObserver" in window) {
                const observer = new IntersectionObserver((entries, observerInstance) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observerInstance.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                animatedElements.forEach(el => {
                    observer.observe(el);
                });
            } else {
                animatedElements.forEach(el => {
                    el.classList.add('visible');
                });
            }

            if (window.location.hash === '#newly-acquired' && document.getElementById('newly-acquired')) {
                setTimeout(() => { 
                    document.getElementById('newly-acquired').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start' 
                    });
                }, 100); 
            }

            // Modal de imagem ampliada
            const modal = document.getElementById('modalImgViewer');
            const modalImg = document.getElementById('modalImgTag');
            const closeModalBtn = document.getElementById('closeModalBtn');
            document.querySelectorAll('.tree-img-clickable').forEach(img => {
                img.addEventListener('click', function() {
                    modalImg.src = this.src;
                    modalImg.alt = this.alt;
                    modal.classList.add('active');
                });
            });
            closeModalBtn.addEventListener('click', () => {
                modal.classList.remove('active');
                modalImg.src = '';
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    modalImg.src = '';
                }
            });
            document.addEventListener('keydown', (e) => {
                if (modal.classList.contains('active') && (e.key === 'Escape' || e.key === 'Esc')) {
                    modal.classList.remove('active');
                    modalImg.src = '';
                }
            });
        });
    </script>
</body>
</html>