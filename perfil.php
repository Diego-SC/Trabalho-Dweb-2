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
    <title>Perfil de <?php echo $nome_usuario ?></title>
    <link rel="stylesheet" href="padrao.css">
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
        <?php echo cardPerfil($conexao, $dados_usuario, "perfil") ?>

        <main class="main-content">
            <section class="favorite-films-section">
                <h2>FILMES FAVORITOS</h2>
                <?php
                    $sql = "SELECT * FROM Filme_Registro 
                            WHERE id_usuario = '$id_usuario' 
                            ORDER BY data_regis DESC 
                            LIMIT 4";

                    $resultado = mysqli_query($conexao, $sql);

                    echo "<div class='movie-grid'>";
                    if (mysqli_num_rows($resultado) > 0) {
                        
                        while ($registro = mysqli_fetch_assoc($resultado)) {
                            $filme = getFilme($conexao, $registro['id_filme']);
                            $id_filme = $registro['id_filme'];
                            $titulo = $filme['titulo'];
                            $poster = $filme['poster'];
                            $estrelas = getEstrelas((int)$registro['nota']);
                            $curtida = getCurtida($registro['curtido']);

                            echo "<a href='filme.php?id=$id_filme'><div class='movie-card'>";
                            echo "<img src='https://image.tmdb.org/t/p/w500$poster' alt='$titulo Poster'>";
                            echo "<div class='film-rating'>
                                $estrelas
                                $curtida
                            </div>";
                            echo "</div></a>";
                        }
                    } else {
                        echo "Usuário não possui filmes registrados.";
                    }
                    echo "</div>";
                ?>
            </section>

            <section class="recent-activity-section">
                <h2 class="section-title">ATIVIDADE RECENTE<a href="filmes.php" class="all-link">MAIS</a></h2>

                <?php
                    $sql = "SELECT * FROM Filme_Registro 
                            WHERE id_usuario = '$id_usuario' 
                            ORDER BY data_regis DESC 
                            LIMIT 4";

                    $resultado = mysqli_query($conexao, $sql);

                    echo "<div class='movie-grid'>";
                    if (mysqli_num_rows($resultado) > 0) {
                        
                        while ($registro = mysqli_fetch_assoc($resultado)) {
                            $filme = getFilme($conexao, $registro['id_filme']);
                            $id_filme = $registro['id_filme'];
                            $titulo = $filme['titulo'];
                            $poster = $filme['poster'];
                            $estrelas = getEstrelas((int)$registro['nota']);
                            $curtida = getCurtida($registro['curtido']);

                            echo "<a href='filme.php?id=$id_filme'><div class='movie-card'>";
                            echo "<img src='https://image.tmdb.org/t/p/w500$poster' alt='$titulo Poster'>";
                            echo "<div class='film-rating'>
                                $estrelas
                                $curtida
                            </div>";
                            echo "</div></a>";
                        }
                    } else {
                        echo "Usuário não possui filmes registrados.";
                    }
                    echo "</div>";
                    ?>
            </section>

            <section class="recent-reviews-section">
                <h2 class="section-title">REVIEWS RECENTES<a href="reviews.php" class="more-link">MAIS</a></h2>

                <?php
                    $sql = "SELECT * FROM Filme_Registro 
                            WHERE id_usuario = '$id_usuario' 
                            AND review IS NOT NULL 
                            AND review != '' 
                            ORDER BY data_regis DESC 
                            LIMIT 3";
                    $resultado = mysqli_query($conexao, $sql);

                    if (mysqli_num_rows($resultado) > 0) {
                        
                        while ($registro = mysqli_fetch_assoc($resultado)) {
                            $filme = getFilme($conexao, $registro['id_filme']);
                            $id_filme = $registro['id_filme'];

                            $titulo = $filme['titulo'];
                            $ano = $filme['ano'];
                            $poster = $filme['poster'];
                            $review = $registro['review'];
                            $data = formatarDataRegistro($registro['data_regis']);
                            $estrelas = getEstrelas((int)$registro['nota']);
                            $curtida = getCurtida($registro['curtido']);

                            echo "<div class='review-entry'>";
                            echo "<a href='filme.php?id=$id_filme'><img src='https://image.tmdb.org/t/p/w92$poster' alt='$titulo Poster' class='review-movie-poster'></a>";
                            echo
                            "<div class='review-content'>
                                <a href='filme.php?id=$id_filme'><h3 class='review-movie-title'>$titulo <span class='movie-year'>$ano</span></h3></a>
                                <p class='review-watched-info'>
                                    $estrelas
                                    $curtida
                                </p>
                                <p class='review-watched-info'>
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

        <div class="right-sidebar">
            <section class="watchlist-section">
                <h2><a href="watchlist.php"> WATCHLIST</a> <span class="watchlist-count"><?php echo getUsuarioTotalWatchlist($conexao, $id_usuario) ?></span></h2>
                <div class="watchlist-grid">
                    <?php
                        $sql = "SELECT * FROM Watchlist 
                                WHERE id_usuario = '$id_usuario' LIMIT 9";
                        $resultado = mysqli_query($conexao, $sql);

                        if (mysqli_num_rows($resultado) > 0) {
                            
                            while ($dados = mysqli_fetch_assoc($resultado)) {
                                $filme = getFilme($conexao, $dados['id_filme']);
                                $id_filme = $dados['id_filme'];
                                $poster = $filme['poster'];
                                $titulo = $filme['titulo'];
                                
                                echo "<a href='filme.php?id=$id_filme'> <img src='https://image.tmdb.org/t/p/w92$poster' alt='$titulo'> </a>";
                            }
                        }
                    ?>
                </div>
            </section>

            <section class="diary-section">
                <h2>DIARY <span class="diary-count">131</span></h2>
                <div class="diary-entries">
                    <div class="diary-entry">
                        <div class="entry-date">
                            <span class="month">JUN</span>
                            <span class="day">15</span>
                        </div>
                        <div class="entry-details">
                            <p class="movie-title">The Babadook</p>
                            <img src="https://image.tmdb.org/t/p/w92/5fS8J92o1yVzGg6x40qP6kIq7YQ.jpg" alt="The Babadook Poster" class="diary-movie-poster">
                            <div class="entry-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="diary-entry">
                        <div class="entry-date">
                            <span class="month">JUN</span>
                            <span class="day">7</span>
                        </div>
                        <div class="entry-details">
                            <p class="movie-title">Predator: Killer Killers</p>
                            <img src="https://image.tmdb.org/t/p/w92/sY9NfT1fPz4SjH0qN6vV9N6j5Hk.jpg" alt="Predator: Killer Killers Poster" class="diary-movie-poster">
                            <div class="entry-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="diary-entry">
                        <div class="entry-date">
                            <span class="month">MAY</span>
                            <span class="day">24</span>
                        </div>
                        <div class="entry-details">
                            <p class="movie-title">Sound of Metal</p>
                            <img src="https://image.tmdb.org/t/p/w92/y6C7y2oD6fM7n9Y2vKj81g0h2mJ.jpg" alt="Sound of Metal Poster" class="diary-movie-poster">
                            <div class="entry-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="diary-entry">
                        <div class="entry-date">
                            <span class="month">MAY</span>
                            <span class="day">11</span>
                        </div>
                        <div class="entry-details">
                            <p class="movie-title">Memories</p>
                            <img src="https://image.tmdb.org/t/p/w92/r5s5b0j6Yt4qR05kH7X9Wz5c9l.jpg" alt="Memories Poster" class="diary-movie-poster">
                            <div class="entry-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php require_once 'rodape.php' ?>
</body>
<?php mysqli_close($conexao) ?>