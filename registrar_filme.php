<?php
    require_once 'db_connect.php';
    require_once 'sessao.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: home.php");
        exit;
    }

    $id_filme = filter_var($_POST['id_filme'], FILTER_SANITIZE_NUMBER_INT);
    $review = isset($_POST['review']) ? trim($_POST['review']) : null;
    $nota = isset($_POST['nota']) ? floatval($_POST['nota']) : 0;
    $curtido = isset($_POST['curtido']) ? 1 : 0;

    $sql = "SELECT * FROM Filme_Registro WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
    $resultado = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        $sql = "UPDATE Filme_Registro SET 
                review = " . ($review ? "'" . mysqli_real_escape_string($conexao, $review) . "'" : "NULL") . ",
                nota = '$nota',
                data_regis = '$data_regis',
                curtido = '$curtido'
                WHERE id_usuario = '$id_usuario' AND id_filme = '$id_filme'";
    } else {
        $sql = "INSERT INTO Filme_Registro (id_usuario, id_filme, review, nota, data_regis, curtido)
                VALUES ('$id_usuario', '$id_filme', " . 
                ($review ? "'" . mysqli_real_escape_string($conexao, $review) . "'" : "NULL") . ", 
                '$nota', 'now()', '$curtido')";
    }

    if (mysqli_query($conexao, $sql)) {
        header("Location: filme.php?id=$id_filme");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conexao);
    }

    mysqli_close($conexao);
?>