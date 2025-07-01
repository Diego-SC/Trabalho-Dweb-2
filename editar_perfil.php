<?php
require_once 'db_connect.php';
require_once 'util.php';
session_start();


// Obter dados do usuário para preencher os campos
$dados_usuario = getUsuario($conexao, $id_usuario);
$nome_usuario = $dados_usuario['nome'] ?? '';
$email_usuario = $dados_usuario['email'] ?? '';
$login_usuario = $dados_usuario['login'] ?? '';

// Obter filmes favoritos do usuário
$filmes_favoritos_ids = getFilmesFavoritos($conexao, $id_usuario); // Função a ser implementada em util.php
$filmes_favoritos_data = [];
foreach ($filmes_favoritos_ids as $tmdb_id) {
    $filmes_favoritos_data[] = getFilme($conexao, $tmdb_id);
}

// URLs das fotos de perfil de exemplo
$fotos = [
    'perfis/perfil1.jpg',
    'perfis/perfil2.jpg',
    'perfis/perfil3.jpg',
    'perfis/perfil4.jpg',
    'perfis/perfil5.jpg',
    'perfis/perfil6.jpg',
];

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
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
        <h2>Editar Perfil</h2>

        <form action="processar_edicao_perfil.php" method="POST" class="profile-settings-form">
            <section class="profile-info-section">
                <h3>Perfil</h3>

                <div class="form-group">
                    <label for="login">Login</label>
                    <div class="input-with-icon">
                        <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($login_usuario); ?>" disabled readonly>
                        <i class="fas fa-lock edit-icon"></i>
                    </div>
                </div>

                <div class="form-group two-columns">
                    <label for="given-name">Nome</label>
                    <input type="text" id="given-name" name="given_name" value="<?php echo htmlspecialchars($nome_usuario); ?>">
                </div>

                <div class="form-group two-columns">
                    <label for="given-name">Senha</label>
                    <input type="text" id="given-password" name="given_password" value="">
                </div>

                <div class="form-group">
                    <label for="email-address">Endereço de email</label>
                    <div class="input-with-icon">
                        <input type="email" id="email-address" name="email_address" value="<?php echo htmlspecialchars($email_usuario); ?>">
                        <i class="fas fa-edit edit-icon"></i>
                    </div>
                </div>
            </section>

            <section class="favorite-films-edit-section">
                <h3>Filmes Favoritos</h3>
                <div class="favorite-films-selection">
                    <?php foreach ($filmes_favoritos_data as $index => $filme): ?>
                        <div class="film-selection-card">
                            <img src="https://image.tmdb.org/t/p/w185<?php echo $filme['poster']; ?>" alt="<?php echo htmlspecialchars($filme['titulo']); ?> Poster">
                            <input type="hidden" name="favorite_films[]" value="<?php echo $filme['id_tmdb']; ?>">
                        </div>
                    <?php endforeach; ?>
                    <?php for ($i = count($filmes_favoritos_data); $i < 4; $i++): // Adiciona slots vazios até 4 filmes ?>
                        <div class="film-selection-card add-film-slot">
                            <button type="button" class="add-film-btn"><i class="fas fa-plus"></i></button>
                            <input type="hidden" name="favorite_films[]" value="">
                        </div>
                    <?php endfor; ?>
                </div>
            </section>

            <section class="profile-avatar-section">
                <h3>Foto de Perfil</h3>
                <div class="avatar-selection-grid">
                    <?php foreach ($fotos as $index => $foto_url): ?>
                        <div class="avatar-option <?php echo ($foto_url == $foto_url_atual ? 'selected' : ''); ?>">
                            <img src="<?php echo $foto_url; ?>" alt="Avatar <?php echo $index + 1; ?>">
                            <input type="radio" name="profile_avatar" value="<?php echo $foto_url; ?>" <?php echo ($foto_url == $foto_url_atual ? 'checked' : ''); ?>>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <button type="submit" class="save-changes-button">SALVAR ALTERAÇÕES</button>
        </form>
    </div>

    <?php require_once 'rodape.php' ?>
</body>

<?php mysqli_close($conexao) ?>