<?php
    require_once 'db_connect.php';
    require_once 'util.php';
    require_once 'sessao.php';

    $email_usuario = $usuario['email'];
    $foto_atual = $usuario['foto_perfil'];

    // Obter filmes favoritos do usuário
    $filmes_favoritos_ids = getFilmesFavoritos($conexao, $id_usuario); // Função a ser implementada em util.php
    $filmes_favoritos_data = [];
    foreach ($filmes_favoritos_ids as $tmdb_id) {
        $filmes_favoritos_data[] = getFilme($conexao, $tmdb_id); // Assume getFilme retrieves movie details
    }

    // URLs das fotos de perfil de exemplo
    $fotos_de_perfil = glob('imagens/perfil*.jpg');
    $messages = []; // Array to store success or error messages

    

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_nova_senha = $_POST['confirmar_nova_senha'] ?? '';

        $nome_novo = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
        $email_novo = mysqli_real_escape_string($conexao, $_POST['email'] ?? '');

        $mudancas = false;
        $updates = [];

        if ($nome_novo !== $nome_usuario) {
            $updates[] = "nome = '$nome_novo'";
            $mudancas = true;
        }

        if ($email_novo !== $email_usuario) {
            $updates[] = "email = '$email_novo'";
        }

        if ($mudancas && empty($messages)) {
            $sql = "UPDATE Usuario SET " . implode(", ", $updates) . " WHERE login = '$id_usuario'";
            
            if (mysqli_query($conexao, $sql)) {
                $messages[] = ['type' => 'success', 'text' => 'Informações do perfil atualizadas com sucesso!'];
                
                $email_usuario = $email_novo;
                $nome_usuario = $nome_novo;
                $_SESSION['nome_usuario'] = $nome_novo;
            } else {
                $messages[] = ['type' => 'error', 'text' => 'Erro ao atualizar as informações do perfil: ' . mysqli_error($conexao)];
            }
        }

        if (!empty($senha_atual) || !empty($nova_senha) || !empty($confirmar_nova_senha)) {
            if (!password_verify($senha_atual, $usuario['senha'])) {
                $messages[] = ['type' => 'error', 'text' => 'A senha atual está incorreta.'];
            }
            elseif (empty($nova_senha)) {
                $messages[] = ['type' => 'error', 'text' => 'A nova senha não pode ser vazia.'];
            }
            elseif ($nova_senha !== $confirmar_nova_senha) {
                $messages[] = ['type' => 'error', 'text' => 'A nova senha e a confirmação de senha não coincidem.'];
            }
            else {
                $hashed_nova_senha = password_hash($nova_senha, PASSWORD_DEFAULT);
                $hashed_nova_senha_sql = mysqli_real_escape_string($conexao, $hashed_nova_senha);
                $sql = "UPDATE Usuario SET senha = '$hashed_nova_senha_sql' WHERE login = '$id_usuario'";

                if (mysqli_query($conexao, $sql)) {
                    $messages[] = ['type' => 'success', 'text' => 'Senha alterada com sucesso!'];
                    // $_SESSION['usuario']['senha'] = $nova_senha;
                }
                else {
                    $messages[] = ['type' => 'error', 'text' => 'Erro ao alterar a senha: ' . mysqli_error($conexao)];
                }
            }
        }

        // --- Handle Profile Avatar Change ---
        if (isset($_POST['profile_avatar'])) {
            $selected_avatar = mysqli_real_escape_string($conexao, $_POST['profile_avatar']);

            // Validate if the selected avatar is one of the allowed options
            if (in_array($selected_avatar, $fotos_de_perfil)) {
                $update_avatar_sql = "UPDATE Usuario SET foto_perfil = '$selected_avatar' WHERE login = '$id_usuario'";
                if (mysqli_query($conexao, $update_avatar_sql)) {
                    $messages[] = ['type' => 'success', 'text' => 'Foto de perfil atualizada com sucesso!'];
                    // Update $usuario in session to reflect new avatar immediately
                    $_SESSION['usuario']['foto_perfil'] = $selected_avatar;
                    $foto_atual = $selected_avatar; // Update local variable for display
                }
                else {
                    $messages[] = ['type' => 'error', 'text' => 'Erro ao atualizar a foto de perfil: ' . mysqli_error($conexao)];
                }
            }
            else {
                $messages[] = ['type' => 'error', 'text' => 'Seleção de foto de perfil inválida.'];
            }
        }
        
        if (empty($messages) && isset($_POST['save_changes'])) {
             $messages[] = ['type' => 'success', 'text' => 'Informações do perfil salvas com sucesso!'];
        }
    }
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="padrao.css">
    <link rel="stylesheet" href="perfil.css">
    <link rel="stylesheet" href="editar_perfil.css">
    <link rel="stylesheet" href="cabecalho.css">
    <link rel="stylesheet" href="rodape.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php require_once 'cabecalho.php' ?>

    <div class="edit-profile-container">
        <div class="header-with-back-button">
            <h2>Editar Perfil</h2>
            <a href="perfil.php" class="back-button"><i class="fas fa-arrow-left"></i> Voltar ao Perfil</a>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="profile-settings-form">
            <section class="profile-info-section">
                <div>
                    <h3>Perfil</h3>
                    
                    <div class="form-group">
                        <label for="login">Login</label>
                        <div class="input-with-icon">
                            <input type="text" id="login" name="login" value="<?php echo $id_usuario; ?>" disabled readonly>
                            <i class="fas fa-lock edit-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <div class="input-with-icon">
                            <input type="text" id="nome" name="nome" value="<?php echo $nome_usuario; ?>">
                            <i class="fas fa-edit edit-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Endereço de email</label>
                        <div class="input-with-icon">
                            <input type="email" id="email" name="email" value="<?php echo $email_usuario; ?>" maxlength="50">
                            <i class="fas fa-edit edit-icon"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3>Alterar Senha</h3>
                    
                    <div class="form-group">
                        <label for="senha_atual">Senha Atual</label>
                        <div class="input-with-icon">
                            <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual" minlength="8" maxlength="50">
                            <i class="fas fa-edit edit-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha</label>
                        <div class="input-with-icon">
                            <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha" minlength="8" maxlength="50">
                            <i class="fas fa-edit edit-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_nova_senha">Confirmar Nova Senha</label>
                        <div class="input-with-icon">
                            <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" placeholder="Confirme sua nova senha" minlength="8" maxlength="50">
                            <i class="fas fa-edit edit-icon"></i>
                        </div>
                    </div>
                </div>
            </section>
            <?php if (!empty($messages)): ?>
                <div class="messages">
                    <?php foreach ($messages as $message): ?>
                        <p class="<?php echo $message['type']; ?>"><?php echo $message['text']; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <section class="profile-avatar-section">
                <h3>Foto de Perfil</h3>
                <div class="avatar-selection-grid">
                    <?php foreach ($fotos_de_perfil as $index => $foto): ?>
                        <div class="avatar-option <?php echo ($foto == $foto_atual ? 'current' : ''); ?>">
                            <img src="<?php echo htmlspecialchars($foto); ?>" alt="Avatar <?php echo $index; ?>">
                            <input type="radio" name="profile_avatar" value="<?php echo htmlspecialchars($foto); ?>" <?php echo ($foto == $foto_atual ? 'checked' : ''); ?>>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <button type="submit" class="save-changes-button">SALVAR ALTERAÇÕES</button>
        </form>
    </div>


    <?php require_once 'rodape.php' ?>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarOptions = document.querySelectorAll('.avatar-option');
        
        avatarOptions.forEach(option => {
            option.addEventListener('click', function() {
                avatarOptions.forEach(opt => {
                    opt.classList.remove('selected');
                    if (opt.classList.contains('current')) {
                        opt.classList.add('current');
                    }
                });
                
                this.classList.add('selected');
                
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });
    });
</script>

<?php mysqli_close($conexao) ?>