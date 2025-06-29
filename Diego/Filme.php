<?php 

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Train Your Dragon</title>
    <link rel="stylesheet" href="especifica.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="cabecalho.css">
    

    <!-- Cabeçalho -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="contem">
        <?php require_once 'cabeca.php'?>
        <div class="secao-fundo">
            <img src="fundo.jpg" alt="How to Train Your Dragon fundo" class="image-fundo">
            <div class="efeito-image"></div>
        </div>

        <div class="conteudo-principal">
            <div class="poster-filme">
                <img src="how.jpg" alt="How to Train Your Dragon poster" class="poster-filme-detalhes">
                <div class="poster-status">
                    <!-- colocar os dados de quantos assistiram talvez-->
                    <span><i class="fas fa-eye"></i> 2.3M</span>
                    <span><i class="fas fa-heart"></i> 200K</span>
                </div>
                <div class="poster-botao">
                    <a href="#" class="action-link"><i class="fas fa-eye"></i> Assistido</a>
                </div>
            </div>

            <div class="filme-detalhes">
                <h1 class="filme-titulo">How to Train Your Dragon</h1>
                <p class="filme-info">2010 · Directed by Dean DeBlois, Chris Sanders</p>
                <p class="descricao">As the son of a Viking leader on the cusp of manhood, shy Hiccup Horrendous Haddock III faces a rite of passage: he must kill a dragon to prove his warrior mettle.
                But after downing a feared dragon, he realizes that he no longer wants to destroy it, and instead befriends the beast - which he names Toothless - much to the chagrin of his warrior father.</p>
                <!-- Equipe -->
                <div class="secao-cabecalho">
                    <h2 class="secao-titulo">EQUIPE</h2>
                </div>
                <hr class="title-divider">

                <div class="review-card">
                    <p class="review-text">
                        I wish this was my autobiography.
                    </p>
                </div>

                <!-- Gêneros -->
                <div class="secao-cabecalho">
                    <h2 class="secao-titulo">Gêneros</h2>
                </div>
                <hr class="title-divider">

                <div class="review-card">
                    <p class="review-text">
                        I wish this was my autobiography.
                    </p>
                </div>
            </div>
            
            <aside class="sidebar">
                <!--<div class="interacao-botao">
                    <button class="interacao-btn-active"><i class="fas fa-eye"></i> Assistido</button>
                    <button class="interacao-btn"><i class="fas fa-heart"></i> Curtido</button>
                    <button class="interacao-btn"><i class="fas fa-bookmark"></i> Watchlist</button>
                </div>
                -->
                <div class="rating-section">
                    <div class="rated-label">Avaliação</div>
                    <div class="star-rating">
                        <i class="fas fa-star filled"></i>
                        <i class="fas fa-star filled"></i>
                        <i class="fas fa-star filled"></i>
                        <i class="fas fa-star-half-alt filled"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="activity-links">
                    <a href="#" class="activity-link-gostei">Gostei</a>
                    <a href="#" class="activity-link-watchlist">Watchlist</a>
                </div>
            </aside>
        </div>

        <!-- Comentarios -->
            <div class="reviews-section-container">
                <div class="secao-cabecalho">
                    <h2 class="secao-titulo">SUA REVIEW</h2>
                </div>
                <hr class="title-divider">

                <div class="review-card-comentario">
                    <p class="review-text">
                        A obra prima do cinema animado!!
                    </p>
                </div>
            </div>
    </div>
</body>
</html>