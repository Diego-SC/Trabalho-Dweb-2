<?php 
	declare(strict_types=1);
	require_once 'db_connect.php';
	session_start();

	$erros = array();

	if (isset($_POST['btn-entrar'])) {
		$login = mysqli_real_escape_string($conexao, $_POST['login'] ?? '');
		$senha = mysqli_real_escape_string($conexao, $_POST['senha'] ?? '');

		if (empty($login) || empty($senha)) {
			$erros[] = "<li>O campo login/senha precisa ser preenchido.</li>";
		}
		else {
			// Verificação se o usuário existe
			$sql = "SELECT * FROM Usuario WHERE login = '$login'";
			$resultado = mysqli_query($conexao, $sql);
			
			if (mysqli_num_rows($resultado) > 0) {
				$usuario = mysqli_fetch_assoc($resultado);
				
				if (password_verify($senha, $usuario['senha'])) {
					$_SESSION['logado'] = true;
					$_SESSION['id_usuario'] = $usuario['login'];
					$_SESSION['nome_usuario'] = $usuario['nome'];
					header('Location: home.php');
					exit();
				}
				else {
					$erros[] = "<li>Usuário e senha não conferem.</li>";
				}
			}
			else {
				$erros[] = "<li>Usuário inexistente.</li>";
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TelaCrítica</title>
	<link rel="stylesheet" href="padrao.css">
    <link rel="stylesheet" href="login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="join-container">
        <h2>Login TelaCrítica</h2>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="login-form">
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>

			
			<?php if (!empty($erros)): ?>
				<div class="error-messages">
					<ul>
						<?php foreach ($erros as $erro): ?>
							<?php echo $erro; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<a href="cadastro.php">Primeira vez aqui? Faça seu cadastro.</a>
            <button type="submit" name="btn-entrar" class="submit-button">Entrar</button>
        </form>
    </div>
</body>
</html>
