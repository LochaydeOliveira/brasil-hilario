<?php
/**
 * Arquivo: excluir-categoria.php
 * Descrição: Exclusão de categorias do painel administrativo
 * Funcionalidades:
 * - Verifica se a categoria existe
 * - Verifica se a categoria está em uso
 * - Remove a categoria do banco de dados
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
    setError('ID da categoria não fornecido.');
    redirect('categorias.php');
}

// Conecta ao banco de dados
$conn = connectDB();

try {
    // Verifica se a categoria existe
    $categoria = getCategory($conn, $_GET['id']);
    
    if (!$categoria) {
        setError('Categoria não encontrada.');
        redirect('categorias.php');
    }
    
    // Verifica se a categoria está em uso
    $sql = "SELECT COUNT(*) FROM posts WHERE categoria_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        setError('Não é possível excluir esta categoria pois ela está sendo usada em posts.');
        redirect('categorias.php');
    }
    
    // Exclui a categoria
    if (deleteCategory($conn, $_GET['id'])) {
        setSuccess('Categoria excluída com sucesso!');
    } else {
        setError('Erro ao excluir categoria.');
    }
} catch (PDOException $e) {
    setError('Erro ao processar a exclusão da categoria.');
}

// Redireciona de volta para a lista de categorias
redirect('categorias.php'); 