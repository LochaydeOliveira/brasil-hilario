<?php
/**
 * Arquivo: excluir-post.php
 * Descrição: Exclusão de posts do painel administrativo
 * Funcionalidades:
 * - Verifica se o post existe
 * - Remove o post do banco de dados
 * - Redireciona com mensagem de sucesso/erro
 */

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado
check_login();

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    setError('ID do post não fornecido.');
    redirect('posts.php');
}

// Conecta ao banco de dados
$conn = connectDB();

try {
    // Verifica se o post existe
    $post = getPost($conn, $_GET['id']);
    
    if (!$post) {
        setError('Post não encontrado.');
        redirect('posts.php');
    }
    
    // Exclui o post
    if (deletePost($conn, $_GET['id'])) {
        setSuccess('Post excluído com sucesso!');
    } else {
        setError('Erro ao excluir post.');
    }
} catch (PDOException $e) {
    setError('Erro ao processar a exclusão do post.');
}

// Redireciona de volta para a lista de posts
redirect('posts.php'); 