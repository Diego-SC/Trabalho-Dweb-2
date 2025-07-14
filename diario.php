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
    <title>Diário de <?php echo htmlspecialchars($nome_usuario); ?></title>
    
    <link rel="stylesheet" href="padrao.css">
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="stylesheet" href="rodape.css">
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="diario.css"> <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php require_once 'cabecalho.php'; // Inclui o cabeçalho ?>

    <main class="container">
        <?php echo cardPerfil($conexao, $dados_usuario, "diario") ?>
        
        <main class="main-content">
            <table class="diary-table">
                <thead>
                    <tr>
                        <th>Mês</th>
                        <th>Dia</th>
                        <th class="left">Filme</th>
                        <th>Ano</th>
                        <th class="left">Nota</th>
                        <th>Curtida</th>
                        <th>Review</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $sql = "SELECT * FROM Filme_Registro 
                            WHERE id_usuario = '$id_usuario' 
                            ORDER BY data_regis DESC";

                    $mes_atual = date("n") + 1;
                    $ano_atual = (int)date("Y");
                    $res = mysqli_query($conexao, $sql);
                    if (mysqli_num_rows($res) > 0) {
                        while ($registro = mysqli_fetch_assoc($res)) {
                            $id_filme = $registro['id_filme'];
                            $filme = getFilme($conexao, $id_filme);
                            $titulo = $filme['titulo'];
                            $poster = $filme['poster'];
                            $ano_filme = $filme['ano'];

                            $partes = explode('-', $registro['data_regis']);
                            $ano = $partes[0];
                            $mes = (int)$partes[1];
                            $dia = (int)$partes[2];
                            $mes_abrev = numeroParaMesAbreviado($mes);

                            $estrelas = getEstrelas($registro['nota']);
                            $curtida = getCurtida($registro['curtido']);
                            $tem_review = !empty($registro['review']) ? "<i class='fas fa-align-left'></i>" : '';
                            
                            $date_html = "";
                            if ($mes != $mes_atual || $ano_atual != $ano) {
                                $date_html =
                                "<div class='date'>
                                    <span class='month'>$mes_abrev</span>
                                    <span class='year'>$ano</span>
                                </div>";
                                $mes_atual = $mes;
                                $ano_atual = $ano;
                            }
                            echo "
                            <tr>
                                <td>
                                    $date_html
                                </td>
                                <td><span class='day center'>$dia</span></td>
                                <td>
                                    <div class='movie-container'>
                                        <a href='filme.php?id=$id_filme'><img class='poster' src='https://image.tmdb.org/t/p/w92$poster' alt='Poster'></a>
                                        <a href='filme.php?id=$id_filme'><p> $titulo </p></a>
                                    </div>
                                </td>
                                <td><span class='center'>$ano_filme</span></td>
                                <td class='left'>
                                    <div class='rating'>
                                        $estrelas
                                    </div>
                                </td>
                                <td class='heart'>
                                   <span class='center'> $curtida </span>
                                </td>
                                <td class='review'>
                                    <span class='center'> $tem_review </span>
                                </td>
                                <td class='edit'>
                                    <a class='center' href='registrar_filme.php?id=$id_filme'><i class='fas fa-pen'></i></a>
                                </td>
                            </tr>";
                        }
                    }
                ?>
                </tbody>
            </table>
        </main>
    </main>

    <?php require_once 'rodape.php';?>
</body>
</html>