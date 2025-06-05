<?php
include_once("code/conexao.php");
include_once("code/loginC.php"); // Assuming this handles session_start()

if (!isset($_SESSION['nome']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id'];
$user_trees_collection = [];
$id_user_escaped_for_select = $conexao->real_escape_string($id_user);
// Assuming 'cards' table stores tree types for now
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
    <title>Meu Bosque | Treedom</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3a7d44; 
            --secondary: #2d5a34; 
            --accent: #5cb85c; 
            --accent-rgb: 92, 184, 92;
            
            /* Light Theme Palette */
            --bg-light: #f7fbf7; /* Main light background (very soft off-white with green hint) */
            --surface-light: #ffffff; 
            --text-dark: #2c3e50; 
            --text-medium: #586a7a; 
            --border-light: #dfe6e9; 

            /* Subtle gradient colors for animated background */
            --grad-color-1: #f7fbf7; /* Slightly greenish white */
            --grad-color-2: #f2f7f2; /* A bit more green hint */
            --grad-color-3: #f8fafa; /* Slightly cooler white */
            --grad-color-4: #f5f9f5; /* Another variation */


            --item-shadow: rgba(0, 0, 0, 0.07); /* Softer shadow */
            --item-hover-shadow: rgba(0, 0, 0, 0.1); /* Softer hover shadow */
            --accent-shadow-color: rgba(var(--accent-rgb), 0.2); /* Subtler accent shadow */
        }
        
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-dark);
            background-color: var(--bg-light); /* Fallback background */
            line-height: 1.6;
            overflow-x: hidden;
            position: relative; /* Needed for ::before pseudo-element */
        }

        /* Animated subtle light gradient background */
        body::before {
            content: '';
            position: fixed; /* Cover the entire viewport */
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1; /* Behind all other content */
            background: linear-gradient(50deg, /* Adjusted angle */
                var(--grad-color-1) 0%,
                var(--grad-color-2) 25%,
                var(--grad-color-3) 50%,
                var(--grad-color-4) 75%,
                var(--grad-color-1) 100%
            );
            background-size: 250% 250%; /* Controls the "zoom" level of gradient */
            animation: lightGradientBG 35s ease infinite; /* Slower and smoother */
        }

        @keyframes lightGradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Navigation */
        nav {
            background: rgba(255, 255, 255, 0.85); /* Slightly translucent white */
            backdrop-filter: blur(8px); /* Blur effect for modern feel */
            -webkit-backdrop-filter: blur(8px); /* Safari support */
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-light);
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
            font-family: 'Playfair Display', serif;
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        .nav-links { display: flex; list-style: none; margin: 0; padding: 0; }
        .nav-links li { margin-left: 30px; }
        .nav-links a {
            text-decoration: none;
            color: var(--text-medium);
            font-weight: 600;
            padding: 10px 5px;
            display: flex; align-items: center; position: relative;
            transition: color 0.3s ease;
        }
        .nav-links a:hover, .nav-links a:focus {
            color: var(--accent);
        }
        .dropdown { position: relative; }
        .dropdown .seta { font-size: 0.7em; margin-left: 6px; transition: transform 0.3s ease; }
        .submenu {
            position: absolute; top: 100%; left: 0; 
            background: var(--surface-light);
            border-radius: 6px; list-style: none; padding: 8px 0; margin: 10px 0 0 0;
            min-width: 200px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            opacity: 0; visibility: hidden; transform: translateY(10px) scale(0.95);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
            z-index: 10; border: 1px solid var(--border-light);
        }
        .submenu li a { display: block; padding: 12px 20px; color: var(--text-medium); font-weight: 400; font-size: 0.95em; transition: background-color 0.3s, color 0.3s; }
        .submenu li a:hover, .submenu li a:focus { background-color: var(--bg-light); color: var(--primary); }
        .dropdown:hover > .submenu, .dropdown:focus-within > .submenu { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
        .dropdown:hover > a .seta, .dropdown:focus-within > a .seta { transform: rotate(180deg); }

        .page-header {
            text-align: center;
            padding: 50px 20px 40px; 
        }
        .page-header .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.2rem; 
            font-weight: 700; 
            color: var(--primary);
            margin-bottom: 10px;
            text-shadow: 1px 1px 0px var(--surface-light), 2px 2px 0px rgba(var(--accent-rgb), 0.1); 
            letter-spacing: 0.5px;
            animation: titleAppear 1s ease-out forwards;
        }
        @keyframes titleAppear {
            from { opacity:0; transform: translateY(20px); }
            to { opacity:1; transform: translateY(0); }
        }

        .page-header .subtitle {
            font-size: 1.15rem; 
            color: var(--text-medium);
            max-width: 600px;
            margin: 0 auto;
            font-weight: 400;
        }

        .container {
            max-width: 1300px; 
            margin: 0 auto;
            padding: 20px; 
            position: relative; /* Ensure container content is above body::before if needed, though z-index on ::before handles it */
        }
        
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
            gap: 30px; 
        }

        .tree-display-item {
            background: var(--surface-light);
            border-radius: 10px; 
            overflow: hidden; 
            box-shadow: 0 4px 12px var(--item-shadow);
            display: flex;
            flex-direction: column;
            transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
                        box-shadow 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid var(--border-light);
            will-change: transform, box-shadow;
        }
        
        .tree-display-item:hover {
            transform: translateY(-8px) scale(1.03); 
            box-shadow: 0 12px 28px var(--item-hover-shadow), 0 0 20px var(--accent-shadow-color);
            border-color: var(--accent);
        }

        .tree-image-wrapper {
            overflow: hidden; 
            position: relative;
            border-radius: 10px 10px 0 0; 
        }

        .tree-display-item img {
            width: 100%;
            height: auto;
            aspect-ratio: 4/5; 
            object-fit: contain;
            background: #f7fbf7;
            display: block; 
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer; /* Adicionado para indicar que é clicável */
        }
        .tree-display-item:hover img {
            transform: scale(1.1); 
        }
        
        .tree-display-item-content {
            padding: 18px 20px; 
            text-align: center;
            flex-grow: 1;
            background: var(--surface-light); 
            border-top: 1px solid var(--border-light);
        }
        
        .tree-display-item-content h3 {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 1.15rem; 
            /* color removido para usar por classe de raridade */
            font-family: 'Playfair Display', serif; 
            font-weight: 700;
            transition: color 0.3s ease;
        }
        .tree-display-item:hover .tree-display-item-content h3 {
            color: var(--primary);
        }
        /* Cores por raridade */
        .raridade-comum { color: #3a7d44; }      /* verde */
        .raridade-rara { color: #2196f3; }       /* azul */
        .raridade-epica { color: #a259e6; }      /* roxo */
        .raridade-lendaria { 
            color: #ffd700;                      /* dourado */
            text-shadow: 0 0 6px #fffbe6, 0 0 12px #ffd70099;
        }

        /* Scroll Animation for tree items */
        .tree-display-item.scroll-animate {
            opacity: 0;
            transform: translateY(50px) scale(0.95); 
            transition-property: opacity, transform;
            transition-duration: 0.6s;
            transition-timing-function: ease-out; 
        }
        .tree-display-item.scroll-animate.visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* Empty state styling */
        .empty-bosque-message {
            text-align: center;
            font-size: 1.2rem; 
            color: var(--text-medium);
            padding: 40px 20px; 
            border: 2px dashed var(--border-light);
            border-radius: 8px; 
            background: rgba(255,255,255,0.7); /* Slightly translucent white on animated bg */
        }
        .empty-bosque-message a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 1px solid transparent; 
            transition: color 0.3s, border-bottom-color 0.3s;
        }
        .empty-bosque-message a:hover {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        /* Footer */
        footer {
            background: #343a40; /* Match nav transparency */
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: white;
            padding: 25px 20px; 
            text-align: center;
            margin-top: 290px; 
            border-top: 1px solid var(--border-light);
            font-size: 0.9rem;
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

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .item-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px; }
            .page-header .section-title { font-size: 2.8rem; }
        }
        @media (max-width: 768px) {
            .page-header .section-title { font-size: 2.3rem; }
            .nav-container { flex-direction: column; align-items: center; }
            .nav-links { margin-top: 15px; }
            .nav-links li { margin: 0 10px; }
            .item-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px;}
        }
        @media (max-width: 480px) {
            .nav-links { flex-direction: column; align-items: center; width: 100%;}
            .nav-links li { margin: 10px 0; width: 100%; text-align: center;}
            .nav-links li a { display: block; padding: 10px; width:100%; justify-content: center;}
            .dropdown { width: 100%; }
            .dropdown > a { justify-content: center; }
            .submenu { width: 100%; box-sizing: border-box;}
            .page-header .section-title { font-size: 2rem; }
            .page-header .subtitle { font-size: 1rem; }
            .item-grid { grid-template-columns: 1fr; gap: 25px;} 
            .tree-display-item-content h3 { font-size: 1.1rem; } 
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-container">
            <a href="inipage.php" class="logo">Treedom</a>
            <ul class="nav-links">
                <li><a href="inipage.php">Início</a></li>
                <li><a href="loja.php">Loja de Árvores</a></li>
                <li class="dropdown">
                    <?php if(isset($_SESSION['nome'])): ?>
                        <a href="#"><?php echo htmlspecialchars($_SESSION['nome']);?> <span class="seta">&#9660;</span></a>
                        <ul class="submenu">
                            <li><a href="bosque.php">Meu Bosque</a></li>
                            <li><a href="inventario.php">Inventário</a></li>
                            <li><a href="code/logout.php">Sair</a></li>
                        </ul>
                    <?php else: ?>
                        <a href="login.php">Login/Cadastro</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <header class="page-header">
        <h1 class="section-title">Meu Bosque</h1>
        <p class="subtitle scroll-animate" style="transition-delay: 0.2s;">Sua coleção de árvores, crescendo a cada dia.</p>
    </header>

    <div class="container">
        <section id="my-collection"> 
            <?php if (!empty($user_trees_collection)): ?>
                <div class="item-grid">
                    <?php foreach($user_trees_collection as $index => $tree): ?>
                        <?php
                            $raridade = isset($tree['raridade']) ? strtolower($tree['raridade']) : 'comum';
                            $classeRaridade = 'raridade-' . $raridade;
                        ?>
                        <div class="tree-display-item scroll-animate" style="transition-delay: <?php echo $index * 0.08; ?>s;">
                            <div class="tree-image-wrapper">
                                <img src="<?php echo htmlspecialchars($tree['img']); ?>" alt="Ilustração de <?php echo htmlspecialchars($tree['nome']); ?>" class="tree-img-clickable">
                            </div>
                            <div class="tree-display-item-content">
                                <h3 class="<?php echo $classeRaridade; ?>"><?php echo htmlspecialchars($tree['nome']); ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-bosque-message scroll-animate">Seu bosque ainda está começando... Que tal <a href="loja.php">visitar a loja</a> para cultivar novas árvores e expandir sua floresta?</p>
            <?php endif; ?>
        </section>
    </div>

    <footer class="scroll-animate" style="transition-delay: <?php echo (!empty($user_trees_collection) ? count($user_trees_collection) * 0.08 + 0.5 : 0.5) ; ?>s;">
        <p>© <?php echo date("Y"); ?> Treedom. Cultivando um futuro mais verde, com design e afeto.</p>
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
                }, { threshold: 0.05 }); 
                
                animatedElements.forEach(el => {
                    observer.observe(el);
                });
            } else {
                animatedElements.forEach(el => {
                    el.classList.add('visible');
                });
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