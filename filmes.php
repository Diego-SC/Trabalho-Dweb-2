<?php
    require_once 'db_connect.php';
    require_once 'util.php';
    require_once 'sessao.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes do <?php echo $nome_usuario ?></title>
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="stylesheet" href="rodape.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php require_once 'cabecalho.php' ?>

    <div class="container">
        <aside class="sidebar">
            <div class="profile-card">
                <div class="profile-header-section">
                    <img src="https://via.placeholder.com/100x100?text=Avatar" alt="Foto de perfil de Ian" class="profile-avatar">
                    <div class="profile-info">
                        <h1 class="profile-name"><?php echo $nome_usuario ?></h1>
                        <a href="editar_perfil.php"><button class="edit-profile-button">EDITAR PERFIL</button></a>
                    </div>
                </div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo getUsuarioTotalFilmes($conexao, $id_usuario) ?></span>
                        <span class="stat-label">FILMES</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo getUsuarioFilmesEsseAno($conexao, $id_usuario) ?></span>
                        <span class="stat-label">ESTE ANO</span>
                    </div>
                </div>
            </div>

            <nav class="profile-nav">
                <ul>
                    <li><a href="perfil.php">Perfil</a></li>
                    <li><a href="diario.php">Diário</a></li>
                    <li><a href="filmes.php" class="active">Filmes</a></li>
                    <li><a href="reviews.php">Reviews</a></li>
                    <li><a href="watchlist.php">Watchlist</a></li>
                </ul>
                <div class="search-profile">
                    <i class="fas fa-search"></i>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="watched-film-grid">
                <?php
                // Query para obter todos os filmes assistidos pelo usuário
                $sql = "SELECT * FROM Filme_Registro 
                        WHERE id_usuario = '$id_usuario' 
                        ORDER BY data_regis DESC"; // Ordenar pela data mais recente

                $resultado = mysqli_query($conexao, $sql);

                if (mysqli_num_rows($resultado) > 0) {
                    while ($registro = mysqli_fetch_assoc($resultado)) {
                        $filme = getFilme($conexao, $registro['id_filme']);
                        $titulo = $filme['titulo'];
                        $poster_url = $filme['poster'];
                        $estrelas = getEstrelas((int)$registro['nota']);
                        $like = temGostei($registro['curtido']);
                        ?>
                        <div class="watched-movie-card">
                            <img src="https://image.tmdb.org/t/p/w185<?php echo $poster_url; ?>" alt="<?php echo $titulo; ?> Poster">
                            <div class="watched-card-overlay">
                                <div class="watched-card-stats">
                                    <span class="watched-card-rating">
                                        <?php echo $estrelas; ?>
                                    </span>
                                    <span class="watched-card-actions">
                                        <?php echo $like; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>Nenhum filme registrado.</p>";
                }
                ?>
            </div>
        </main>

    </div>
    <?php require_once 'rodape.php' ?>
</body>
<?php mysqli_close($conexao) ?>