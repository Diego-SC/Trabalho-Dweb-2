<?php
    declare(strict_types=1);
    require_once 'db_connect.php';
    session_start();

    $erros = array();

    if (isset($_POST['btn-cadastrar'])) {
        $email = mysqli_real_escape_string($conexao, $_POST['email'] ?? '');
        $nome = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
        $usuario = mysqli_real_escape_string($conexao, $_POST['usuario'] ?? '');
        $senha = mysqli_real_escape_string($conexao, $_POST['senha'] ?? '');

        // Validações básicas
        if (empty($email) || empty($usuario) || empty($senha) || empty($nome)) {
            $erros[] = "<li>Todos os campos (Email, usuario, senha) são obrigatórios.</li>";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "<li>O endereço de e-mail fornecido não é válido.</li>";
        }

        if (strlen($senha) < 8) { // Exemplo de validação de senha
            $erros[] = "<li>A senha deve ter pelo menos 8 caracteres.</li>";
        }

        // Verifica se o usuario ou email já existem
        if (empty($erros)) {
            $sql = "SELECT * FROM Usuario WHERE login = '$usuario' OR email = '$email'";
            $res = mysqli_query($conexao, $sql);

            if (mysqli_num_rows($res) > 0) {
                $dados = mysqli_fetch_assoc($res);
                if ($dados['login'] === $usuario) {
                    $erros[] = "<li>Este nome de usuário já está em uso.</li>";
                }
                if ($dados['email'] === $email) {
                    $erros[] = "<li>Este endereço de e-mail já está em uso.</li>";
                }
            }
        }

        // Se não houver erros, insere o usuário no banco de dados
        if (empty($erros)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql_insert = "INSERT INTO Usuario (login, nome, email, senha) VALUES ('$usuario', '$usuario', '$email', '$senha_hash')";
            // Note: 'nome' está sendo preenchido com o 'usuario' aqui, ajuste conforme sua lógica de negócio.

            if (mysqli_query($conexao, $sql_insert)) {
                $_SESSION['logado'] = true;
                $_SESSION['id_usuario'] = $usuario;
                $_SESSION['nome_usuario'] = $usuario;
                header('Location: home.php');
                exit();
            } else {
                $erros[] = "<li>Erro ao cadastrar usuário: " . mysqli_error($conexao) . "</li>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | TelaCrítica</title>
    <link rel="stylesheet" href="login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="join-container">
        <h2>Cadastrar-se em TelaCrítica</h2>

        <?php if (!empty($erros)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <?php echo $erro; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="join-form">
            <div class="form-group">
                <label for="email">Endereço de Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" minlength="8" maxlength="50" required>
                <small class="pass  word-hint">Mínimo de 8 caracteres</small>
            </div>

            <button type="submit" name="btn-cadastrar" class="submit-button">Cadastrar</button>
        </form>
    </div>
</body>
</html>