<?php
require_once 'db_connect.php';
require_once 'util.php';
session_start();

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['user_id'];
$mensagem = '';

// Process form data if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get and sanitize form data
        $nome = mysqli_real_escape_string($conexao, $_POST['given_name'] ?? '');
        $email = mysqli_real_escape_string($conexao, $_POST['email_address'] ?? '');
        $senha = $_POST['given_password'] ?? '';
        $foto_perfil = mysqli_real_escape_string($conexao, $_POST['profile_avatar'] ?? '');
        
        // Prepare the base update query
        $sql = "UPDATE Usuario SET nome = ?, email = ?";
        $types = "ss";
        $params = [&$nome, &$email];
        
        // Add password to update if provided
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql .= ", senha = ?";
            $types .= "s";
            $params[] = &$senha_hash;
        }
        
        // Add profile photo if provided
        if (!empty($foto_perfil)) {
            $sql .= ", foto_perfil = ?";
            $types .= "s";
            $params[] = &$foto_perfil;
        }
        
        $sql .= " WHERE login = ?";
        $types .= "s";
        $params[] = &$id_usuario;
        
        // Prepare and execute the statement
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        
        if (mysqli_stmt_execute($stmt)) {
            // Handle favorite movies update
            if (isset($_POST['favorite_films'])) {
                // First, clear existing favorites
                $delete_sql = "DELETE FROM Filme_Favorito WHERE usuario_id_login = ?";
                $delete_stmt = mysqli_prepare($conexao, $delete_sql);
                mysqli_stmt_bind_param($delete_stmt, "s", $id_usuario);
                mysqli_stmt_execute($delete_stmt);
                mysqli_stmt_close($delete_stmt);
                
                // Insert new favorites
                $insert_sql = "INSERT INTO Filme_Favorito (usuario_id_login, filme_id_tmdb) VALUES (?, ?)";
                $insert_stmt = mysqli_prepare($conexao, $insert_sql);
                
                foreach ($_POST['favorite_films'] as $filme_id) {
                    if (!empty($filme_id)) {
                        mysqli_stmt_bind_param($insert_stmt, "si", $id_usuario, $filme_id);
                        mysqli_stmt_execute($insert_stmt);
                    }
                }
                mysqli_stmt_close($insert_stmt);
            }
            
            $mensagem = "Perfil atualizado com sucesso!";
            $_SESSION['success_message'] = $mensagem;
        } else {
            throw new Exception("Erro ao atualizar perfil: " . mysqli_error($conexao));
        }
        
        mysqli_stmt_close($stmt);
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        $mensagem = "Erro ao atualizar perfil. Por favor, tente novamente.";
        $_SESSION['error_message'] = $mensagem;
    }
    
    // Redirect back to profile page
    header("Location: editar_perfil.php");
    exit();
} else {
    header("Location: editar_perfil.php");
    exit();
}