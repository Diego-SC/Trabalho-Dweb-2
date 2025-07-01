<?php
    require_once 'db_connect.php';
    require_once 'util.php';
    session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews do <?php echo $nome_usuario ?></title>
    
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
                        <h1 class="profile-name"><?php $dados = getUsuario($conexao, $id_usuario); echo $dados['nome'] ?></h1>
                        <button class="edit-profile-button">EDITAR PERFIL</button>
                        <!-- <p class="profile-bio">bio</p> -->
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
                    <li><a href="filmes.php">Filmes</a></li>
                    <li><a href="reviews.php" class="active">Reviews</a></li>
                    <li><a href="watchlist.php">Watchlist</a></li>
                </ul>
                <div class="search-profile">
                    <i class="fas fa-search"></i>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <section class="recent-reviews-section">
                <?php
                    $sql = "SELECT * FROM Filme_Registro 
                            WHERE usuario_id_login = '$id_usuario' 
                            AND review IS NOT NULL 
                            AND review != '' 
                            ORDER BY data_regis DESC";
                    $resultado = mysqli_query($conexao, $sql);

                    // Check if there are any results
                    if (mysqli_num_rows($resultado) > 0) {
                        
                        // Loop through each row in the result set
                        while ($registro = mysqli_fetch_assoc($resultado)) {
                            $filme = getFilme($conexao, $registro['filme_id_tmdb']);
                            $titulo = $filme['titulo'];
                            $ano = $filme['ano'];
                            $poster = $filme['poster'];
                            $review = $registro['review'];
                            $data = formatarDataRegistro($registro['data_regis']);
                            $estrelas = getEstrelas((int)$registro['nota']);
                            $like = temGostei($registro['gostei']);

                            echo "<div class='review-entry'>";
                            echo "<img src='https://image.tmdb.org/t/p/w92$poster' alt='$titulo Poster' class='review-movie-poster'>";
                            echo
                            "<div class='review-content'>
                                <h3 class='review-movie-title'>$titulo <span class='movie-year'>$ano</span></h3>
                                <p class='review-watched-info'>
                                    $estrelas
                                    $like
                                    Assistido $data
                                </p>
                                <p class='review-text'>$review</p>
                            </div>";
                            echo "</div>";
                        }
                    } else {
                        echo "Usuário não possui filmes registrados.";
                    }
                ?>
            </section>

        </main>
    </div>
    <?php require_once 'rodape.php' ?>
</body>

<?php mysqli_close($conexao) ?>