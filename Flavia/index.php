<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do 'Usuário'</title>
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="tudo">
    <?php require_once 'cabecalho.php' ?>
    <main>
        <section class="filmes-populares">
            <h2 class="frase">POPULAR IN LETTERBOXD</h2>
            <hr class="linha-frase">
            <div class="grade-filmes">
                <div class="filme">
                    <img src="imagens/filme1.jpg" alt="Filme 1">
                    <p>Barbie</p>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="filme">
                    <a href="wicked.php">
                        <img src="imagens/filme2.jpg" alt="Filme 2">
                    </a>
                    <a href="wicked.php">
                        <p>Wicked</p>
                    </a>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="filme">
                    <img src="imagens/filme3.jpg" alt="Filme 3">
                    <p>Conclave</p>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
                <div class="filme">
                    <img src="imagens/filme4.jpg" alt="Filme 3">
                    <p>Lilo & Stitch</p>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
            </div>
        </section>
        <section class="filmes-populares">
            <h2 class="frase">POPULAR FILMS THIS WEEK</h2>
            <hr class="linha-frase">
            <div class="grade-filmes">
                <div class="filme">
                    <img src="imagens/filme5.jpg" alt="Filme 5">
                    <p>How to Train Your Dragon</p>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="filme">
                    <img src="imagens/filme6.jpg" alt="Filme 6">
                    <p>M3GAN 2.0</p>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="filme">
                    <img src="imagens/filme7.jpg" alt="Filme 7">
                    <p>Elio</p>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
                <div class="filme">
                    <img src="imagens/filme8.jpg" alt="Filme 8">
                    <p>KPop Demon Hunters</p>
                    <div class="avaliacao">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>