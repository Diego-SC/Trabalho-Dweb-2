<?php
    require_once 'db_connect.php';
    require_once 'util.php';
    require_once 'sessao.php';

    if(!isset($_GET['id'])) {
        header("Location: home.php");
        exit;
    }

    $id_filme = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $filme = getFilme($conexao, $id_filme);
    $titulo = $filme['titulo'];
    $poster = $filme['poster'];
    $diretor = $filme['diretor'];
    $sinopse = $filme['sinopse'];
    $ano = $filme['ano'];

    // Registro
    $sql = "SELECT * FROM Filme_Registro WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
    $resultado = mysqli_query($conexao, $sql);

    $assistido = false;
    $curtido = false;
    $na_watchlist = false;

    if (mysqli_num_rows($resultado) > 0) {
        $registro = mysqli_fetch_assoc($resultado);
        $assistido = true;
        $curtido = $registro['curtido'];
        $review = $registro['review'];
        $nota = $registro['nota'];
        $data = $registro['data_regis'];
    }

    // Watchlist
    $na_watchlist = naWatchlist($conexao, $id_usuario, $id_filme);

    $estat = getEstatisticasFilme($conexao, $id_filme);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['toggle_curtido'])) {
            toggleCurtido($conexao, $id_usuario, $id_filme, $assistido, $curtido);
        }
        elseif (isset($_POST['toggle_watchlist'])) {
            toggleWatchlist($conexao, $id_usuario, $id_filme, $assistido, $na_watchlist);
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - TelaCrítica</title>
    <link rel="stylesheet" href="padrao.css">
    <link rel="stylesheet" href="filme.css">
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="stylesheet" href="rodape.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php require_once 'cabecalho.php'; ?>

    <main class="movie-page-container">
        <div class="movie-header">
            <div class="movie-poster-large">
                <?php echo "<img src='https://image.tmdb.org/t/p/w500$poster' alt='$titulo Poster'>" ?>
                <div class="stats-overlay">
                    <span class="stat-count"> <?php echo $estat['registros']?> <i class="fas fa-eye"></i></span>
                    <span class="stat-count"> <?php echo $estat['curtidas']?> <i class="fas fa-heart"></i></span>
                    <span class="stat-count"> <?php echo $estat['watchlist']?> <i class="fas fa-list"></i></span>
                </div>
            </div>
            <div class="movie-details">
                <h1><?php echo $titulo; ?> <span class="movie-year"><?php echo $ano; ?></span></h1>
                <p class="movie-director"><?php echo $diretor ? "Dirigido por <span> $diretor </span>" : "Diretor não Encontrado"  ?></p>
                <p class="movie-overview"><?php echo $sinopse; ?></p>
                <div class="movie-meta">
                    <!-- <span><?php echo $duracao; ?></span> -->
                    <?php 
                        if ($assistido) {
                            if ($nota != 0) echo '<span class="your-rating">Sua Avaliação:</span>';
                            echo '<div class="star-rating">' . getEstrelas($nota) . '</div>';
                        }
                    ?>
                </div>

                <div class="movie-rating-section">
                </div>

                <div class="movie-actions">
                    <div>
                        <button class="action-button watched <?php echo $assistido ? 'active' : ''; ?>">
                            <i class="<?php echo $assistido ? 'fas fa-eye' : 'far fa-eye'; ?>"></i> <?php echo $assistido ? 'Assistido' : 'Assistir'; ?>
                        </button>
                    </div>

                    <form method="POST">
                        <button type="submit" name="toggle_curtido" class="action-button <?php echo $curtido ? 'active' : ''; ?>">
                            <i class="<?php echo $curtido ? 'fas fa-heart' : 'fa fa-heart'; ?>"></i> <?php echo $curtido ? 'Remover' : 'Curtir'; ?>
                        </button>
                    </form>

                    <form method="POST">
                        <button type="submit" name="toggle_watchlist" class="action-button <?php echo $na_watchlist ? 'active' : ''; ?>">
                            <i class="<?php echo $na_watchlist ? 'fa fa-check' : 'far fa-plus-square'; ?>"></i> <?php echo $na_watchlist ? 'Remover' : 'Watchlist'; ?>
                        </button>
                    </form>

                    <a href="registrar_filme.php?id=<?php echo $id_filme; ?>" class="review-log-button">
                        <i class="fas fa-pen"></i> <?php echo $assistido ? 'Editar Registro' : 'Registrar...'; ?>
                    </a>
                </div>
            </div>
        </div>

        <?php
            if ($assistido && $review != "") {
                $data_formatada = formatarDataRegistro($data);
                $foto_perfil = $usuario['foto_perfil'];
                echo "<section class='reviews-from-friends-section'>
                    <div class='section-header-film'>
                        <p>SUA REVIEW</p>
                    </div>
                    <div class='review-entry-film'>
                        <img src='$foto_perfil' alt='Avatar do $nome_usuario' class='reviewer-avatar'>
                        <div class='review-content-film'>
                            <p class='reviewer-info'>Review por <span class='reviewer-name'>$nome_usuario</span>
                            em $data_formatada</p>";

                if ($nota !=0 || $curtido)
                    echo "<span class='review-stars'>".getEstrelas($nota) . getCurtida($curtido)."</span>";
                            
                echo "
                            <p class='review-text-film'>$review</p>
                        </div>
                    </div>
                </section>";
            }
        ?>
        
    </main>

    <?php require_once 'rodape.php'; ?>
</body>
</html>
<?php mysqli_close($conexao) ?>