<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Verifica se o usuário está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

// Obtém os dados do formulário
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$titulo = trim($_POST['titulo']);
$slug = trim($_POST['slug']);
$conteudo = trim($_POST['conteudo']);
$resumo = trim($_POST['resumo']);
$categoria_id = (int)$_POST['categoria_id'];
$publicado = isset($_POST['publicado']) ? 1 : 0;

// Validação básica
if (empty($titulo) || empty($slug) || empty($conteudo) || $categoria_id <= 0) {
    $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
    header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
    exit;
}

try {
    // Verifica se o slug já existe
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Este slug já está em uso. Por favor, escolha outro.";
        header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
        exit;
    }

    if ($id > 0) {
        // Atualiza o post existente
        $stmt = $pdo->prepare("UPDATE posts SET 
                titulo = ?, 
                slug = ?, 
                conteudo = ?, 
                resumo = ?, 
                categoria_id = ?, 
                publicado = ?,
                atualizado_em = NOW()
                WHERE id = ?");
        $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $id]);

        $_SESSION['success'] = "Post atualizado com sucesso!";
    } else {
        // Insere um novo post
        $stmt = $pdo->prepare("INSERT INTO posts (titulo, slug, conteudo, resumo, categoria_id, publicado, criado_em) 
                              VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado]);

        $_SESSION['success'] = "Post criado com sucesso!";
    }

    header('Location: posts.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao salvar o post: " . $e->getMessage();
    header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
    exit;
}
