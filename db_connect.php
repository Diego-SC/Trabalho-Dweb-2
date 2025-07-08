<?php
    require_once 'util.php';
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // $host = 'centerbeam.proxy.rlwy.net';
    // $port = 55823;
    // $user = 'root';
    // $password = 'BylUqWSCunqMupAzHnXzHhhEJLPKiYmI';
    // $database = 'railway';

    // LOCAL
    $host = 'localhost';
    // $port = 3306;
    $user = 'root';
    $password = 'Ian84939333'; 
    $database = 'TelaCritica';

    // $conexao = mysqli_connect($host, $user, $password, $database, $port);
    $conexao = mysqli_connect($host, $user, $password, $database);

    if (mysqli_connect_error()){
        debug_to_console(" Falha na conexão com o Banco de Dados: " . mysqli_connect_error());
        exit;
    }
    debug_to_console("Banco de Dados Connectado com sucesso!");
?>