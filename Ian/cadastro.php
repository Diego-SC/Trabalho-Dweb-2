<html>
<head>
<title> Cadastro de Usuário </title>
</head>
<body>
    <?php
        if (!empty($erros)){
            foreach ($erros as $erro){
            echo $erro;
        }
    }
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method ="POST">
        <label> Nome: </label><input type ="text" name ="nome" id="nome"> <br>
        <label> Login: </label><input type ="text" name ="login" id="login"> <br>
        <label> Senha: </label><input type ="password" name ="senha" id="senha"> <br>
    <input type ="submit" value ="Cadastrar" id="cadastrar" name ="btn-cadastrar"> 
    </form>
</body>
</html>

<?php
    require_once 'db_connect.php';
    session_start();

    if (isset($_POST['btn-cadastrar'])){
        $erros = array();
        $loginDB = mysqli_escape_string($connect, $_POST['login']);

        $nome = $_POST["nome"];
        $login = $_POST["login"];
        $senha = MD5($_POST["senha"]);
    }
    if (empty($login) or empty ($senha) or empty ($nome)){
        $erros[] = "<li>Os campos nome / login / senha precisam ser preenchidos.</li>";
    }
    else{
        $sql = "SELECT login FROM usuarios WHERE login ='$loginDB'";
        $resultado = mysqli_query($connect, $sql);
    }
    if (mysqli_num_rows($resultado) > 0){
        $erros[] = "<li> Esse login já existe. </li>";
    }
    else{
        $sqlInsert = "INSERT INTO usuarios(nome, login, senha) VALUES ('$nome', '$login','$senha')";
        $insert = mysqli_query($connect, $sqlInsert);
        if ($insert){
            echo '<script language = "javascript" type="text/javascript">;
            alert("Usuário cadastrado com sucesso!");
            window.location.href= "index.php"</script>';
        }
        else {
            $erros[] = "<li >Não foi possível cadastrar o usuário. Tente novamente. </li>";
        }
    }
    if ($insert){
        echo '<script language ="javascript" type ="text/javascript">
        alert("Usuário cadastrado com sucesso!");
        window.location.href ="index.php" </script>';
    }
    else {
        $erros[] = "<li>Não foi possível cadastrar o usuário. Tente novamente. </li>";
    }

?>