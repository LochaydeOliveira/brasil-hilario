<?php
/**
 * Arquivo: save-post.php
 * Descrição: Controla o salvamento e atualização de posts no banco de dados
 * Funcionalidades:
 * - Processa formulários de novo post e edição
 * - Valida dados recebidos
 * - Salva/atualiza no banco de dados
 * - Gerencia redirecionamentos e mensagens
 */

// Inclui arquivos necessários
require_once '../config/config.php';  // Configurações gerais
require_once '../includes/db.php';    // Conexão com banco de dados
require_once 'includes/auth.php';     // Funções de autenticação

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

// Obtém e sanitiza os dados do formulário
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;  // ID do post (0 para novo post)
$titulo = trim($_POST['titulo']);                   // Título do post
$slug = trim($_POST['slug']);                       // URL amigável
$conteudo = trim($_POST['conteudo']);               // Conteúdo principal
$resumo = trim($_POST['resumo']);                   // Resumo do post
$categoria_id = (int)$_POST['categoria_id'];        // ID da categoria
$publicado = isset($_POST['publicado']) ? 1 : 0;    // Status de publicação

// Validação básica dos campos obrigatórios
if (empty($titulo) || empty($slug) || empty($conteudo) || $categoria_id <= 0) {
    $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
    header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
    exit;
}

try {
    // Verifica se o slug já existe (evita duplicação)
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Este slug já está em uso. Por favor, escolha outro.";
        header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
        exit;
    }

    if ($id > 0) {
        // Atualiza um post existente
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

    // Redireciona para a lista de posts após salvar
    header('Location: posts.php');
    exit;
} catch (PDOException $e) {
    // Em caso de erro no banco de dados
    $_SESSION['error'] = "Erro ao salvar o post: " . $e->getMessage();
    header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
    exit;
}
