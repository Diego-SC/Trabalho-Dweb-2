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
    <title>Watchlist do <?php echo $nome_usuario ?></title>
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
        <?php echo cardPerfil($conexao, $usuario, "watchlist") ?>

        <main class="main-content">
            <div class="watched-film-grid">
                <?php
                $sql = "SELECT * FROM Watchlist WHERE id_usuario = '$id_usuario'";

                $resultado = mysqli_query($conexao, $sql);

                if (mysqli_num_rows($resultado) > 0) {
                    while ($registro = mysqli_fetch_assoc($resultado)) {
                        $filme = getFilme($conexao, $registro['id_filme']);
                        $titulo = $filme['titulo'];
                        $poster_url = $filme['poster'];
                        ?>
                        <div class="watched-movie-card">
                            <img src="https://image.tmdb.org/t/p/w185<?php echo $poster_url; ?>" alt="<?php echo $titulo; ?> Poster">
                        </div>
                        <?php
                    }
                }
                else {
                    echo "<p>Nenhum filme na Watchlist.</p>";
                }
                ?>
            </div>
        </main>

    </div>
    <?php require_once 'rodape.php' ?>
</body>
<?php mysqli_close($conexao) ?>