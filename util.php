<?php
function debug_to_console($data): void {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('DEBUG: " . $output . "' );</script>";
}

function formatarDataRegistro($dataMysql) {
    $partes = explode('-', $dataMysql);
    if (count($partes) !== 3) {
        return "(data inválida)";
    }

    $ano = $partes[0];
    $mes = (int)$partes[1];
    $dia = (int)$partes[2];

    // Mapeia números de mês para nomes em português
    $meses = [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Maio',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agos',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez'
    ];

    // Verifica se a data é válida
    if (!checkdate($mes, $dia, $ano)) {
        return "(data inválida)";
    }

    return "$dia de {$meses[$mes]} de $ano";
}

function getDiretor($id_filme) {
    $curl = curl_init();

    curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.themoviedb.org/3/movie/$id_filme/credits?language=en-US",
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

    return "Diretor Não Encontrado.";
}

function inserirFilmeNoBanco($conexao, string $id): void {
    $curl = curl_init();

    curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.themoviedb.org/3/movie/$id?language=pt-BR",
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
    
        // Check if decoding was successful
        if (json_last_error() === JSON_ERROR_NONE) {
            debug_to_console("JSON do Filme id=$id decodificado com sucesso.");
        }
        else {
            echo "Error decoding JSON: " . json_last_error_msg();
            return;
        }

        // print_r($info);
        $titulo = $info['title'];
        $data = $info['release_date'];
        $ano = (int) explode('-', $data)[0];
        $diretor = getDiretor($id);
        $poster_url = $info['poster_path'];
        
        $insert_sql = "INSERT INTO Filme (id_tmdb, titulo, ano, diretor, poster) values ('$id', '$titulo', $ano, '$diretor', '$poster_url')";
        $resultado = mysqli_query($conexao, $insert_sql);
        debug_to_console("Filme $titulo (id=$id) adicionado ao Banco de Dados.");
    }
}

/*
Parametros: (conexao, id_filme)
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

function getFilmesPopulares(): array {
    $movieIds = [];
    $qtd = 6;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.themoviedb.org/3/movie/popular?language=pt-BR&region=BR&page=1",
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
            $movieIds[] = $movie['id'];
            if (count($movieIds) >= $qtd) {
                break;
            }
        }
    }
    
    return array_slice($movieIds, 0, $qtd);
}

function getUsuarioTotalFilmes($conexao, string $id_usuario): int {
    $id_usuario = mysqli_real_escape_string($conexao, $id_usuario);
    
    $sql = "SELECT COUNT(id) as total FROM Filme_Registro WHERE usuario_id_login = '$id_usuario'";
    $res = mysqli_query($conexao, $sql);
    
    if ($res) {
        $linha = mysqli_fetch_assoc($res);
        return (int)$linha['total']; // Return the count as an integer
    }
    
    return 0; // Return 0 if there's an error or no results
}

function getUsuarioFilmesEsseAno($conexao, string $id_usuario): int {
    $id_usuario = mysqli_real_escape_string($conexao, $id_usuario);
    $ano_atual = date('Y'); // Get current year
    
    $sql = "SELECT COUNT(id) as total 
            FROM Filme_Registro 
            WHERE usuario_id_login = '$id_usuario' 
            AND YEAR(data_regis) = $ano_atual";
    
    $res = mysqli_query($conexao, $sql);
    
    if ($res) {
        $linha = mysqli_fetch_assoc($res);
        return (int)$linha['total']; // Return the count as an integer
    }
    
    return 0; // Return 0 if there's an error or no results
}

function getFilmesFavoritos($conexao, string $id_usuario): array {
    // Sanitize the input
    $id_usuario = mysqli_real_escape_string($conexao, $id_usuario);
    
    // Initialize empty array
    $arr = [];
    
    // Query to get all favorite movie IDs for this user
    $sql = "SELECT filme_id_tmdb FROM Filme_Favorito WHERE usuario_id_login = '$id_usuario'";
    $res = mysqli_query($conexao, $sql);
    
    // If query succeeded and returned rows
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $arr[] = $row['filme_id_tmdb']; // Cast to integer
        }
    }
    
    return $arr;
}

function getUsuario($conexao, $id) {
    $sql = "SELECT * from Usuario where login = '$id'";
    $resultado = mysqli_query($conexao, $sql);
    return mysqli_fetch_array($resultado);
    // print_r("Perfil: " . $dados['nome'] . "<br>");
}

function getEstrelas($qtde = 0): string {
    $estrelas = "";
    for ($i=0; $i < $qtde / 2; $i++) { 
        $estrelas = $estrelas . "<i class='fas fa-star'></i>";
    }
    if ($qtde % 2 == 1) $estrelas = $estrelas . "<i class='fas fa-star-half-alt'></i>";
    return $estrelas;
}

function temGostei($flag): string {
    $like = "";
    if ($flag == 1) $like = '<span class="like-icon active"> ❤️ </span>';
    return $like;
}

?>