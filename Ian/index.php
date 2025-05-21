<?php declare(strict_types=1);
	require_once 'db_connect.php';
	session_start();

	if (isset($_POST['btn-entrar'])){
		$erros = array();
		$login = mysqli_escape_string ($connect, $_POST['login']);
		$senha = mysqli_escape_string ($connect, $_POST['senha']);
		if (empty($login) or empty ($senha)){
			$erros[] = "<li >O campo login/senha precisa ser preenchido. </li >";
		}
		else{
			$sql = "SELECT login FROM usuarios WHERE login ='$login'";
			$resultado = mysqli_query( $connect, $sql);
			if (mysqli_num_rows ( $resultado ) > 0){
				// Existe um registro com o login que foi informado
				$senha = md5($senha);
				$sql = "SELECT * FROM usuarios WHERE login ='$login' AND senha ='$senha'";
				$resultado = mysqli_query ($connect, $sql);
				mysqli_close($connect);
				if (mysqli_num_rows($resultado) == 1){
					$dados = mysqli_fetch_array($resultado);
					// var_dump($dados);
					$_SESSION['logado'] = true ;
					$_SESSION['id_usuario'] = $dados['id'];
					header('Location: home.php');
				}
				else{
					$erros[] = "<li>Usuário e senha não conferem. </li>";
				}
			}
			else{
				// Não existe um registro com o login que foi informado
				$erros[] = "<li >Usuário inexistente.</li>";
			}
		}
	}
?>

<html>
<head>
<title>Login </title>
</head>
<body>
	<?php
	if (! empty ( $erros )):
	foreach ( $erros as $erro ):
	echo $erro ;
	endforeach ;
	endif ;
	?>

    <h1>Login</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method ="POST">
        Login: <input type ="text" name ="login"><br>
        Senha: <input type ="password" name ="senha"><br>
        <button type ="submit" name="btn-entrar">Entrar</button>
    </form>
    <a href= "cadastro.php">Cadastre-se</a>
</body>
</html>

<style>
	:root {
		font-family: Comic Sans MS;
		font-size: 36px;
		background-color: black;
		color: white;
	}
	strong {
		color: red;
	}
</style>