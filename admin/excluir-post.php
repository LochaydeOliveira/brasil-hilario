<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Garante que o usuário esteja logado e seja admin
check_login();
if (!is_admin()) {
    $_SESSION['error_message'] = "Você não tem permissão para realizar esta ação.";
    header('Location: ' . ADMIN_URL . '/index.php');
    exit;
}

// Verifica se o ID do post foi fornecido
$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (empty($post_id)) {
    $_SESSION['error_message'] = "ID do post não fornecido.";
    header('Location: ' . ADMIN_URL . '/posts.php');
    exit;
}

try {
    $conn->autocommit(FALSE);

    // Excluir registros relacionados na tabela post_tags
    $stmt = $conn->prepare("DELETE FROM post_tags WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    // Excluir o post da tabela posts
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $conn->commit();

    $_SESSION['success_message'] = "Post excluído com sucesso!";
    header('Location: ' . ADMIN_URL . '/posts.php');
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = "Erro ao excluir o post: " . $e->getMessage();
    header('Location: ' . ADMIN_URL . '/posts.php');
    exit;
}
?> 