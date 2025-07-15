<?php
    session_start();
    if (!isset($_SESSION['logado'])){
        header('Location: login.php');
        exit();
    }

    $id_usuario = $_SESSION['id_usuario'];
    $usuario = getUsuario($conexao, $id_usuario);
    $nome_usuario = $usuario['nome'];
?>