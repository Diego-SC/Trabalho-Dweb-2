<?php
    require_once 'db_connect.php';
    require_once 'sessao.php';
    require_once 'util.php';

    $query = $_GET['query'] ?? '';
    $resultados = [];
    $mensagem_erro = '';

    if (!empty($query)) {
        $encoded_query = urlencode($query);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.themoviedb.org/3/search/movie?include_adult=false&language=pt-BR&page=1&query=" . $encoded_query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI3ZTBiNTFhMTdhYjU1ZTc5YzgyMzZlNGQyMWNlMTAxMiIsIm5iZiI6MTc0ODI4MDY3My40NDEsInN1YiI6IjY4MzRhNTYxOGFkYzUyMzAxZmI2YjkyOSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.QbT9MwCftUih_rKG-s0m8sPv9owN7ffze8ZWPLbkpaM",
                "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            $mensagem_erro = "Erro cURL: " . $err;
        } elseif ($http_code !== 200) {
            $mensagem_erro = "Erro ao conectar com a API do TMDB. Código HTTP: {$http_code}.";
        }
        else {
            $data = json_decode($response, true);
            if (isset($data['results'])) {
                $resultados = $data['results'];
                if (empty($resultados)) {
                    $mensagem_erro = "Nenhum resultado encontrado para '{$query}'.";
                }
            }
            else {
                $mensagem_erro = "Resposta inválida da API do TMDB.";
            }
        }
    }
    else {
        $mensagem_erro = "Por favor, digite um termo para pesquisar.";
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Pesquisa para "<?php echo htmlspecialchars($query); ?>" - TelaCrítica</title>
    <link rel="stylesheet" href="padrao.css">
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="stylesheet" href="rodape.css">
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="pesquisa.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php require_once 'cabecalho.php'; ?>

    <main class="search-results-container">
        <h1>Resultados da Pesquisa para "<?php echo htmlspecialchars($query); ?>"</h1>
        
        <?php if (!empty($mensagem_erro)): ?>
            <p class="no-results-message"><?php echo htmlspecialchars($mensagem_erro); ?></p>
        <?php elseif (!empty($resultados)): ?>
            <div class="search-results-grid">
                <?php foreach ($resultados as $dados): ?>
                    <?php
                        if (empty($dados['poster_path']) || empty($dados['overview'])) continue;

                        $filme = getFilme($conexao, $dados['id']);
                        $id_filme = $dados['id'];
                        $poster = $filme['poster'];
                        $ano = $filme['ano'];
                        $titulo = $filme['titulo'];
                    ?>
                    <a href="filme.php?id=<?php echo htmlspecialchars($id_filme); ?>" class="search-movie-card">
                        <img src="https://image.tmdb.org/t/p/w200<?php echo htmlspecialchars($poster); ?>" alt="<?php echo $titulo; ?> Poster">
                        <p class="movie-title"><?php echo $titulo; ?></p>
                        <p class="movie-year"><?php echo $ano; ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results-message">Digite algo na barra de pesquisa para encontrar filmes.</p>
        <?php endif; ?>
    </main>

    <?php require_once 'rodape.php'; ?>
</body>
</html>