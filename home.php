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
    <title>TelaCrítica - Home</title>
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="stylesheet" href="rodape.css">
    <link rel="stylesheet" href="home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php require_once 'cabecalho.php' ?>

    <main class="home-container">
        <div class="welcome-message">
            <p>Bem-vindo de volta, <span><?php echo htmlspecialchars($nome_usuario); ?></span>. Veja o que andam assistindo...</p>
        </div>

        <section class="film-section">
            <div class="section-header">
                <p>FILMES POPULARES</p>
            </div>

            <div class="film-grid">
    
            <?php
                $filmes_populares = getFilmesPopulares();

                foreach ($filmes_populares as $index => $id_filme) {
                    $filme = getFilme($conexao, $id_filme);
                    $poster = $filme['poster'];
                    $titulo = $filme['titulo'];

                    echo "
                    <div class='activity-card'>
                        <a href='filme.php' class='activity-card'>
                            <img src='https://image.tmdb.org/t/p/w500$poster' alt='$titulo Poster'>
                        </a>
                    </div>";
                }
            ?>
            </div>

            <h2>FILMES POPULARES</h2>

            <div class="film-grid">
                <div class="activity-card">
                    <img src="https://a.ltrbxd.com/resized/film-poster/1/6/6/0/7/6/166076-good-boy-0-230-0-345-crop.jpg?v=f0b2f5d21a" alt="Good Boy poster">
                </div>
            </div>
        </section>
    </main>

    <?php require_once 'rodape.php' ?>
</body>
</html>

<?php mysqli_close($conexao) ?>
