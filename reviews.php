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
    <title>Reviews do <?php echo $nome_usuario ?></title>
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
        <?php echo cardPerfil($conexao, $dados_usuario, "reviews") ?>

        <main class="main-content">
            <section class="recent-reviews-section">
                <?php
                    $sql = "SELECT * FROM Filme_Registro 
                            WHERE id_usuario = '$id_usuario' 
                            AND review IS NOT NULL 
                            AND review != '' 
                            ORDER BY data_regis DESC";
                    $resultado = mysqli_query($conexao, $sql);

                    if (mysqli_num_rows($resultado) > 0) {
                        
                        while ($registro = mysqli_fetch_assoc($resultado)) {
                            $filme = getFilme($conexao, $registro['id_filme']);
                            $titulo = $filme['titulo'];
                            $ano = $filme['ano'];
                            $poster = $filme['poster'];
                            $review = $registro['review'];
                            $id_filme = $registro['id_filme'];
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
    </div>
    <?php require_once 'rodape.php' ?>
</body>

<?php mysqli_close($conexao) ?>