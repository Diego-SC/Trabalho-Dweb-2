<?php
    require_once 'util.php';
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $host = 'centerbeam.proxy.rlwy.net';
    $port = 55823;
    $user = 'root';
    $password = 'BylUqWSCunqMupAzHnXzHhhEJLPKiYmI'; // your MySQL password
    $database = 'railway';

    // // LOCAL
    // $host = 'centerbeam.proxy.rlwy.net';
    // $port = 55823;
    // $user = 'root';
    // $password = '12345678'; // your MySQL password
    // $database = 'TelaCritica';

    // Create connection
    $conexao = mysqli_connect($host, $user, $password, $database, $port);

    // Check connection
    if (mysqli_connect_error()){
        debug_to_console(" Falha na conexão com o Banco de Dados: " . mysqli_connect_error());
        exit;
    }
    debug_to_console("Banco de Dados Connectado com sucesso!");
?>