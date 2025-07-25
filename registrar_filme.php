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
    $titulo_filme = $filme['titulo'];
    $poster_filme = $filme['poster'];
    $ano_filme = $filme['ano'];

    $review = "";
    $nota = 0.0;
    $curtido = 0;
    $favorito = false;
    $assistido = false;
   
    $sql_check = "SELECT * FROM Filme_Registro WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
    $result_check = mysqli_query($conexao, $sql_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        $registro_existente = mysqli_fetch_assoc($result_check);
        $review = $registro_existente['review'];
        $nota = $registro_existente['nota'] / 2;
        $curtido = $registro_existente['curtido'];
        $assistido = true;
        
        // Favorito
        $sql = "SELECT * FROM Filme_Favorito WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
        $res = mysqli_query($conexao, $sql);
        if (mysqli_num_rows($res) > 0) {
            $favorito = true;
        }
    }
   
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['delete_review'])) {
            deletarRegistro($conexao, $id_usuario, $id_filme);
        }
        else {
            $review_post = filter_var($_POST['review'], FILTER_SANITIZE_SPECIAL_CHARS);
        
            $nota_post = isset($_POST['nota']) ? (float)filter_var($_POST['nota'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;
            $nota_post = (int) ($nota_post * 2);

            $curtido_post = (int) $_POST['curtido'];
            $favorito_post = (int) $_POST['favorito'];
            $sql_fav_cnt = "SELECT COUNT(*) as total FROM Filme_Favorito WHERE id_usuario = '$id_usuario'";
            $res = mysqli_query($conexao,  $sql_fav_cnt);
            $fav_cnt = (int) mysqli_fetch_assoc($res)['total'];

            $bloq_insert = ($favorito_post === 1 && $fav_cnt >= 5 && $nota_post == 0 && empty($review_post) && $curtido_post == 0);
            if ($bloq_insert) {
                echo "<p class='error-message'>Limite de 5 filmes favoritos atingido. Caso deseje adicionar esse filme como favorito é necessário remover um filme dos favoritos.</p>";
            }
            else {
                if ($assistido) {
                    $sql_update = "UPDATE Filme_Registro SET review = '$review_post', nota = '$nota_post', curtido = '$curtido_post' WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
                    if (mysqli_query($conexao, $sql_update)) {
                        echo "<p class='success-message'>Registro atualizado com sucesso!</p>";
                    
                        // Deletar da Watchlist
                        if (naWatchlist($conexao, $id_usuario, $id_filme)) {
                            $sql_del = "DELETE FROM Watchlist WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
                            mysqli_query($conexao, $sql_del);
                        }
                        $review = $review_post;
                        $nota = $nota_post;
                        $curtido = $curtido_post;
                    }
                    else {
                        echo "<p class='error-message'>Erro ao atualizar registro: " . mysqli_error($conexao) . "</p>";
                    }
                }
                else {
                    $sql_insert = "INSERT INTO Filme_Registro (id_usuario, id_filme, review, nota, curtido, data_regis) VALUES ('$id_usuario', '$id_filme', '$review_post', '$nota_post', '$curtido_post', NOW())";
                    debug_to_console($sql_insert);
                    if (mysqli_query($conexao, $sql_insert)) {
                        echo "<p class='success-message'>Registro adicionado com sucesso!</p>";
                        
                        // Deletar da Watchlist
                        if (naWatchlist($conexao, $id_usuario, $id_filme)) {
                            $sql_del = "DELETE FROM Watchlist WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
                            mysqli_query($conexao, $sql_del);
                        }
                        
                        $review = $review_post;
                        $nota = $nota_post;
                        $curtido = $curtido_post;
                        $assistido = true;
                    }
                    else {
                        echo "<p class='error-message'>Erro ao adicionar registro: " . mysqli_error($conexao) . "</p>";
                    }
                }

                if ($favorito_post) {
                    if ($fav_cnt >= 5 || $favorito == $favorito_post) {
                        echo "<p class='error-message'>Limite de 5 filmes favoritos atingido. Caso deseje adicionar esse filme como favorito é necessário remover um filme dos favoritos.</p>";
                    }
                    else {
                        $sql_insert = "INSERT INTO Filme_Favorito (id_usuario, id_filme) VALUES ('$id_usuario', '$id_filme')";
                        if (mysqli_query($conexao, $sql_insert)) {
                            $favorito = $favorito_post;
                            echo "<p class='success-message'>Filme favorito adicionado com sucesso!</p>";
                        }
                    }
                }
                else {
                    $sql_del = "DELETE FROM Filme_Favorito WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
                    mysqli_query($conexao, $sql_del);
                    echo "<p class='success-message'>Filme favorito removido com sucesso!</p>";
                }
                header("Location: filme.php?id=$id_filme");
            }
        }
    }
    mysqli_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($assistido ? 'Editar' : 'Registrar'); ?> Registro para <?php echo $titulo_filme; ?> - TelaCrítica</title>
    <link rel="stylesheet" href="padrao.css">
    <link rel="stylesheet" href="registrar_filme.css">
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="rodape.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php require_once 'cabecalho.php'; ?>

    <main>
        <div class="modal-container">
            <div class="modal-header">
                <h2>Eu assisti...</h2>
                <button class="close-button" onclick="window.history.back();">&times;</button>
            </div>
            <div class="modal-content">
                <div class="movie-info">
                    <div class="movie-poster-small">
                        <?php echo "<img src='https://image.tmdb.org/t/p/w500$poster_filme' alt='$titulo_filme Poster'>" ?>
                    </div>
                </div>
                <div class="review-section">
                    <div class="review-header">
                        <h3><?php echo $titulo_filme; ?> | <?php echo $ano_filme; ?></h3>
                    </div>
                    <form action="registrar_filme.php?id=<?php echo $id_filme; ?>" method="POST">
                        <textarea id="review" name="review" placeholder="Adicionar um comentário..."><?php echo htmlspecialchars($review); ?></textarea>

                        <div class="rating-and-like">
                            <div class="rating-selector-container">
                                <label for="nota">Your Rating:</label>
                                <select id="nota" name="nota" class="rating-select">
                                    <?php for ($i = 0; $i <= 50; $i += 5):
                                        $value = $i / 10;
                                    ?>
                                        <option value="<?php echo $value; ?>" <?php echo ($nota == $value) ? 'selected' : ''; ?>>
                                            <?php echo number_format($value, 1); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="rating-btns">
                                <button type="button" class="favorito-button <?php echo ($favorito == 1) ? 'active' : ''; ?>" id="favoritoToggle">
                                    <i class="fa-solid fa-bookmark"></i>
                                </button>
                                <input type="hidden" name="favorito" id="favoritoInput" value="<?php echo $favorito; ?>">
                                
                                <button type="button" class="like-button <?php echo ($curtido == 1) ? 'active' : ''; ?>" id="likeToggle">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <input type="hidden" name="curtido" id="curtidoInput" value="<?php echo $curtido; ?>">
                            </div>
                        </div>

                        <div class="save-button-container">
                            <button type="submit" class="save-button">SALVAR</button>
                            <?php if ($assistido): ?>
                                <button type="submit" name="delete_review" class="save-button delete">DELETAR</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once 'rodape.php'; ?>

    <script>
        // Funcionalidade visual dos botões
        document.addEventListener('DOMContentLoaded', function() {
            const likeButton = document.getElementById('likeToggle');
            const curtidoInput = document.getElementById('curtidoInput');

            likeButton.addEventListener('click', function() {
                const currentStatus = parseInt(curtidoInput.value);
                if (currentStatus === 1) {
                    curtidoInput.value = 0;
                    likeButton.classList.remove('active');
                }
                else {
                    curtidoInput.value = 1;
                    likeButton.classList.add('active');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const favoritoButton = document.getElementById('favoritoToggle');
            const favoritoInput = document.getElementById('favoritoInput');

            favoritoButton.addEventListener('click', function() {
                const currentStatus = parseInt(favoritoInput.value);
                if (currentStatus === 1) {
                    favoritoInput.value = 0;
                    favoritoButton.classList.remove('active');
                }
                else {
                    favoritoInput.value = 1;
                    favoritoButton.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>