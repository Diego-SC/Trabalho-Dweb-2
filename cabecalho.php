<?php
    require_once 'sessao.php';
?>
<header class="main-header">
    <div class="logo">
        <a href="home.php"><img class="logo" src="./imagens/logo.png" alt="TelaCritica Logo"></a>
    </div>
    <nav class="main-nav">
        <ul>
            <li><a href="perfil.php"><?php echo $nome_usuario ?? 'Usuário'; ?> <i class="fas fa-chevron-down"></i></a></li>
            <li><a href="home.php">HOME</a></li>
            <li><a href="sair.php">SAIR</a></li>
        </ul>
    </nav>
    <div class="header-actions">
        <button id="search-toggle" class="icon-button"><i class="fas fa-search"></i></button>
    </div>
</header>

<div id="search-bar" class="search-bar">
    <div class="search-bar-content">
        <form action="pesquisa.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="Pesquisar filmes..." class="search-input" required>
            <button type="submit" class="search-button"><i class="fas fa-arrow-right"></i></button>
        </form>
        <button id="close-search" class="close-search-button"><i class="fas fa-times"></i></button>
    </div>
</div>

<script>
    document.getElementById('search-toggle').addEventListener('click', function() {
        const searchBar = document.getElementById('search-bar');
        searchBar.classList.add('active'); // Adiciona a classe 'active' para mostrar
        searchBar.querySelector('.search-input').focus(); // Foca no input
    });

    document.getElementById('close-search').addEventListener('click', function() {
        const searchBar = document.getElementById('search-bar');
        searchBar.classList.remove('active'); // Remove a classe 'active' para esconder
    });

    // Opcional: Esconder a barra de pesquisa ao pressionar ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('search-bar').classList.remove('active');
        }
    });
</script>