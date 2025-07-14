<?php

/*
    Printar detalhes da execução do backend no console
*/
function debug_to_console($data): void {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('DEBUG: " . $output . "' );</script>";
}

function numeroParaMesAbreviado($numero) {
    $meses = [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez'
    ];
    
    return $meses[$numero] ?? 'Inválido';
}

/*
    Formatar Data de Registro
    Parâmetros: (data)
    Retorno: Data formatada (string)
*/
function formatarDataRegistro($dataMysql): string {
    $partes = explode('-', $dataMysql);
    if (count($partes) !== 3) {
        return "(data inválida)";
    }

    $ano = $partes[0];
    $mes = (int)$partes[1];
    $dia = (int)$partes[2];
    $mes_abrev = numeroParaMesAbreviado($mes);

    // Verifica se a data é válida
    if (!checkdate($mes, $dia, $ano)) {
        return "(data inválida)";
    }

    return "$dia de {$mes_abrev} de $ano";
}

/*
    Extrair Diretor de um filme via API
    Parâmetros: (id_filme)
    Retorno: Nome do Diretor (string)
*/
function getDiretor($id_filme): string {
    $curl = curl_init();

    curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.themoviedb.org/3/movie/$id_filme/credits?language=pt-BR",
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

    curl_close($curl);

    if ($err) {
        error_log("cURL Error: " . $err);
        return [];
    }

    $data = json_decode($response, true);

    if (isset($data['crew'])) {
        foreach ($data['crew'] as $person) {
            if (isset($person['job']) && $person['job'] === 'Director') {
                return $person['name'];
            }
        }
    }

    return "";
}

/*
    Inserir filme no Banco de Dados
    Parâmetros: (conexao, id_filme)
*/
function inserirFilmeNoBanco($conexao, string $id_filme): void {
    $id_filme = mysqli_real_escape_string($conexao, $id_filme);
    $curl = curl_init();

    curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.themoviedb.org/3/movie/$id_filme?language=pt-BR",
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

    curl_close($curl);

    if ($err) {
        debug_to_console("cURL Error #:" . $err);
    } else {
        $info = json_decode($response, true);
    
        if (json_last_error() === JSON_ERROR_NONE) {
            debug_to_console("JSON do Filme id=$id_filme decodificado com sucesso.");
        }
        else {
            echo "Error decoding JSON: " . json_last_error_msg();
            return;
        }

        // Escape all string values before inserting into database
        $titulo = mysqli_real_escape_string($conexao, $info['title']);
        $data = $info['release_date'];
        $ano = (int) explode('-', $data)[0];
        $diretor = mysqli_real_escape_string($conexao, getDiretor($id_filme));
        $poster_url = mysqli_real_escape_string($conexao, $info['poster_path']);
        $sinopse = mysqli_real_escape_string($conexao, $info['overview']);

        if (empty($sinopse)) {
            $curl = curl_init();
            curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.themoviedb.org/3/movie/$id_filme?language=en-US",
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
            curl_close($curl);
            if ($err) {
                debug_to_console("cURL Error #:" . $err);
            }
            else {
                $info = json_decode($response, true);
                $sinopse = mysqli_real_escape_string($conexao, $info['overview']);
            }
        }
        
        $insert_sql = "INSERT INTO Filme (id_tmdb, titulo, ano, diretor, poster, sinopse) 
                      VALUES ('$id_filme', '$titulo', $ano, '$diretor', '$poster_url', '$sinopse')";
        
        $resultado = mysqli_query($conexao, $insert_sql);
        
        if (!$resultado) {
            debug_to_console("Error inserting movie id = : " . $id_filme . "<br>" . mysqli_error($conexao));
        } else {
            debug_to_console("Filme $titulo (id=$id_filme) adicionado ao Banco de Dados.");
        }
    }
}

/*
    Extrair dados de um filme
    Parâmetros: (conexao, id_filme)
    Retorno: Array com detalhes do filme
*/
function getFilme($conexao, string $id_filme): array {
    $sql = "SELECT * FROM Filme
            WHERE id_tmdb = '$id_filme'";
        
    $resultado = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        debug_to_console("Filme id=$id_filme está no Banco!");
    }
    else {
        debug_to_console("Filme id=$id_filme NÃO está no Banco!");
        inserirFilmeNoBanco($conexao, $id_filme);
    }

    $resultado = mysqli_query($conexao, $sql);
    return mysqli_fetch_array($resultado);
}

/*
    Pegar lista de filmes populares
    Parâmetros: (page)
    Retorno: Array 6 filmes
*/
function getFilmesPopulares($page=1): array {
    $movieIds = [];
    $qtd = 6;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=pt-BR&page=$page&sort_by=popularity.desc",
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
    
    $res = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error #:" . $err;
        return [];
    }
    
    $data = json_decode($res, true);
    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['results'])) {
        echo "Error decoding JSON or invalid response format";
        return [];
    }
    
    foreach ($data['results'] as $movie) {
        if (isset($movie['id'])) {
            // Não incluir filmes sem sinopse / Filtrar filmes impróprios
            if (empty($movie['overview'])) continue;
            $movieIds[] = $movie['id'];
            if (count($movieIds) >= $qtd) {
                break;
            }
        }
    }
    return array_slice($movieIds, 0, $qtd);
}

/*
    Pegar a quantidade total de filmes do usuário
    Parâmetros: ($conexao, id_usuario)
    Retorno: Qunatitade total de filmes do usuário (int) 
*/
function getUsuarioTotalFilmes($conexao, string $id_usuario): int {
    $id_usuario = mysqli_real_escape_string($conexao, $id_usuario);
    
    $sql = "SELECT COUNT(id) as total FROM Filme_Registro WHERE id_usuario = '$id_usuario'";
    $res = mysqli_query($conexao, $sql);
    
    if ($res) {
        $linha = mysqli_fetch_assoc($res);
        return (int)$linha['total']; // Return the count as an integer
    }
    
    return 0; // Return 0 if there's an error or no results
}

/*
    Pegar a quantidade total de filmes do usuário este ano
    Parâmetros: ($conexao, id_usuario)
    Retorno: Qunatitade total de filmes do usuário este ano (int) 
*/
function getUsuarioFilmesEsseAno($conexao, string $id_usuario): int {
    $id_usuario = mysqli_real_escape_string($conexao, $id_usuario);
    $ano_atual = date('Y'); // Get current year
    
    $sql = "SELECT COUNT(id) as total 
            FROM Filme_Registro 
            WHERE id_usuario = '$id_usuario' 
            AND YEAR(data_regis) = $ano_atual";
    
    $res = mysqli_query($conexao, $sql);
    
    if ($res) {
        $linha = mysqli_fetch_assoc($res);
        return (int)$linha['total']; // Return the count as an integer
    }
    
    return 0; // Return 0 if there's an error or no results
}

/*
    Pegar lista de filmes favoritos do usuário
    Parâmetros: ($conexao, id_usuario)
    Retorno: Array com os filmes favoritos do usuário
*/
function getFilmesFavoritos($conexao, string $id_usuario): array {
    $id_usuario = mysqli_real_escape_string($conexao, $id_usuario);
    
    $arr = [];
    $sql = "SELECT id_filme FROM Filme_Favorito WHERE id_usuario = '$id_usuario'";
    $res = mysqli_query($conexao, $sql);
    
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $arr[] = $row['id_filme']; // Cast to integer
        }
    }
    
    return $arr;
}

/*
    Pegar estatísticas do filme específicas do site
    Parâmetros: ($conexao, id_filme)
    Retorno: Array com o total de registros, curtidas e salvos na watchlist
*/
function getEstatisticasFilme($conexao, $id_filme) {
    $stats = [
        'registros' => 0,
        'curtidas' => 0,
        'watchlist' => 0
    ];
    
    $sql = "SELECT COUNT(*) as total FROM Filme_Registro WHERE id_filme = '$id_filme'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['registros'] = $row['total'];
        mysqli_free_result($result);
    }
    
    $sql = "SELECT COUNT(*) as total FROM Filme_Registro WHERE id_filme = '$id_filme' AND curtido = 1";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['curtidas'] = $row['total'];
        mysqli_free_result($result);
    }
    
    $sql = "SELECT COUNT(*) as total FROM Watchlist WHERE id_filme = '$id_filme'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['watchlist'] = $row['total'];
        mysqli_free_result($result);
    }
    
    return $stats;
}

/*
    Pegar dados do usuário
    Parâmetros: ($conexao, id_usuario)
    Retorno: Array com o dados do usuario
*/
function getUsuario($conexao, $id) {
    $sql = "SELECT * from Usuario where login = '$id'";
    $resultado = mysqli_query($conexao, $sql);
    return mysqli_fetch_array($resultado);
}

/*
    Pegar HTML para a respectiva nota passada
    Parâmetros: ($qtde)
    Retorno: HTML das Estrelas (string)
*/
function getEstrelas($qtde = 0): string {
    $estrelas = "";
    for ($i=1; $i < ($qtde+1) / 2; $i++) { 
        $estrelas = $estrelas . "<i class='fas fa-star'></i>";
    }
    if ($qtde % 2 == 1) $estrelas = $estrelas . "<i class='fas fa-star-half-alt'></i>";
    return $estrelas;
}

/*
    Pegar HTML para um coração de curtido
    Parâmetros: ($flag: bool)
    Retorno: HTML do curtido (string)
*/
function getCurtida(bool $flag): string {
    $curtida = "";
    if ($flag == 1) $curtida = '<i class="fas fa-heart" style="color:red;"></i>';
    return $curtida;
}

function naWatchlist($conexao, $id_usuario, $id_filme) {
    $sql = "SELECT * FROM Watchlist WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
    $resultado = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        return true;
    }
    return false;
}

function toggleCurtido($conexao, $id_usuario, $id_filme, &$assistido, &$curtido): void {
    $novo_curtido = $curtido ? 0 : 1; // Toggle

    if (!$assistido) {
        $sql = "INSERT INTO Filme_Registro (id_usuario, id_filme, review, nota, curtido, data_regis) VALUES ('$id_usuario', '$id_filme', '', '0', '1', NOW())";
    }
    else {
        $sql = "UPDATE Filme_Registro SET curtido = '$novo_curtido' WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
    }
    
    if (mysqli_query($conexao, $sql)) {
        $curtido = $novo_curtido;
        $assistido = true;
    }
    header("Location: filme.php?id=$id_filme");
    exit();
}

function toggleWatchlist($conexao, $id_usuario, $id_filme, &$assistido, &$na_watchlist): void {
    if ($assistido) {
        return;
    }

    if ($na_watchlist) {
        $sql = "DELETE FROM Watchlist WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
    }
    else {
        $sql = "INSERT INTO Watchlist (id_usuario, id_filme) VALUES ('$id_usuario', '$id_filme')";
    }
    
    if (mysqli_query($conexao, $sql)) {
        $na_watchlist = !$na_watchlist;
    }
    header("Location: filme.php?id=$id_filme");
    exit();
}

/*
    Pegar a quantidade total de filmes do usuário na watchlist
    Parâmetros: ($conexao, id_usuario)
    Retorno: Qunatitade total de filmes do usuário na watchlist (int) 
*/
function getUsuarioTotalWatchlist($conexao, string $id_usuario): int {
    $id_usuario = mysqli_real_escape_string($conexao, $id_usuario);
    
    $sql = "SELECT COUNT(*) as total FROM Watchlist WHERE id_usuario = '$id_usuario'";
    $res = mysqli_query($conexao, $sql);
    
    if ($res) {
        $linha = mysqli_fetch_assoc($res);
        return (int)$linha['total']; // Return the count as an integer
    }
    
    return 0; // Return 0 if there's an error or no results
}


function deletarRegistro($conexao, $id_usuario, $id_filme) {
    $sql_delete = "DELETE FROM Filme_Registro WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";

    if (mysqli_query($conexao, $sql_delete)) {
        header("Location: filme.php?id=$id_filme");
        exit();
    }
    else {
        echo "<p class='error-message'>Erro ao deletar registro: " . mysqli_error($conexao) . "</p>";
    }
}

function cardPerfil($conexao, $dados, string $pagina) {
    $id_usuario = $dados['login'];
    $nome_usuario = $dados['nome'];
    $html = '
    <aside class="sidebar">
        <div class="profile-card">
            <div class="profile-header-section">
                <img src="./assets/perfil2.jpg" alt="Foto de perfil de'. $nome_usuario . '?>" class="profile-avatar">
                <div class="profile-info">
                    <h1 class="profile-name">'. $nome_usuario . '</h1>
                    <a href="editar_perfil.php"><button class="edit-profile-button">EDITAR PERFIL</button></a>
                    <!-- <p class="profile-bio">bio</p> -->
                </div>
            </div>

            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-number">'. getUsuarioTotalFilmes($conexao, $id_usuario) . '</span>
                    <span class="stat-label">FILMES</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">'. getUsuarioFilmesEsseAno($conexao, $id_usuario) . '</span>
                    <span class="stat-label">ESTE ANO</span>
                </div>
            </div>
        </div>

        <nav class="profile-nav">
            <ul>
                <li><a href="perfil.php"' . ($pagina === 'perfil' ? 'class="active"' : '') . '>Perfil</a></li>
                <li><a href="diario.php"' . ($pagina === 'diario' ? 'class="active"' : '') . '>Diário</a></li>
                <li><a href="filmes.php"' . ($pagina === 'filmes' ? 'class="active"' : '') . '>Filmes</a></li>
                <li><a href="reviews.php"' . ($pagina === 'reviews' ? 'class="active"' : '') . '>Reviews</a></li>
                <li><a href="watchlist.php"' . ($pagina === 'watchlist' ? 'class="active"' : '') . '>Watchlist</a></li>
            </ul>
            
        </nav>
    </aside>';
    return $html;
}
?>