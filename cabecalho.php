<?php
    require_once 'db_connect.php';

    if (!isset($_SESSION['logado'])){
        header('Location: login.php');
    }

    $id_usuario = $_SESSION['id_usuario'];
    $sql = "SELECT * FROM Usuario WHERE login = '$id_usuario'";
    $resultado = mysqli_query($conexao, $sql);
    $dados = mysqli_fetch_array($resultado);
    $nome_usuario = getUsuario($conexao, $id_usuario);
    $nome_usuario = $nome_usuario['nome'];
?>

<header class="main-header">
    <div class="logo">
       
    </div>
    <nav class="main-nav">
        <ul>
            <li><a href="perfil.php"><?php echo $nome_usuario; ?> <i class="fas fa-chevron-down"></i></a></li>
            <li><a href="home.php">HOME</a></li>
            <li><a href="sair.php">SAIR</a></li>
        </ul>
    </nav>
    <div class="header-actions">
        <button class="icon-button"><i class="fas fa-search"></i></button>
        <button class="icon-button"><i class="fas fa-plus"></i></button>
        <button class="log-button">
            <i class="fas fa-check"></i> LOG
        </button>
    </div>
</header>