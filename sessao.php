<?php
    session_start();
    if (!isset($_SESSION['logado'])){
        header('Location: login.php');
        exit();
    }

    $id_usuario = $_SESSION['id_usuario'];
    $sql = "SELECT * FROM Usuario WHERE login = '$id_usuario'";
    $resultado = mysqli_query($conexao, $sql);
    $dados = mysqli_fetch_array($resultado);
    $nome_usuario = getUsuario($conexao, $id_usuario);
    $nome_usuario = $nome_usuario['nome'];
?>