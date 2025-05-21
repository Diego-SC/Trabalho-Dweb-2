<?php require_once 'db_connect.php';
    session_start();

    if (!isset($_SESSION['logado'])){
        header('Location: index.php');
    }

    $id = $_SESSION ['id_usuario'];
    $sql = "SELECT * FROM usuarios WHERE id = '$id'";
    $resultado = mysqli_query($connect, $sql);
    $dados = mysqli_fetch_array($resultado);
    mysqli_close($connect);
?>
<html>
    <head>
    <title>Página Restrita</title >
    </head>
    <body>
    <h1 >Olá <?php echo $dados['nome'];?>, O Pesadelo da Okto Programing</h1>
    <a href = "sair.php">Sair</a>
    </body>
</html>

