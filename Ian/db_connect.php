<?php
    require_once 'util.php';
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $host = 'localhost';
    $user = 'root';
    $password = 'Ian84939333'; // your MySQL password
    $database = 'TelaCritica';

    // Create connection
    $conexao = mysqli_connect($host, $user, $password, $database);

    // Check connection
    if (mysqli_connect_error()){
        debug_to_console(" Falha na conexão com o Banco de Dados: " . mysqli_connect_error());
        exit;
    }
    debug_to_console("Banco de Dados Connectado com sucesso!");
    $id_usuario = 'i_reis';
    $nome_usuario = getUsuario($conexao, $id_usuario);
    $nome_usuario = $nome_usuario['nome'];
?>