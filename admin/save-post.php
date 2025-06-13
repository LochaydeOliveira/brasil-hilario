<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado
check_login();

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Processar dados do formulário
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $titulo = trim($_POST['titulo']);
    $slug = trim($_POST['slug']);
    $conteudo = trim($_POST['conteudo']);
    $resumo = trim($_POST['resumo']);
    $categoria_id = (int)$_POST['categoria_id'];
    $publicado = isset($_POST['publicado']) ? 1 : 0;
    $tags = isset($_POST['tags']) ? array_map('trim', explode(',', $_POST['tags'])) : [];

    // Processar imagem destacada
    $imagem_destacada = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        // Se for edição, buscar imagem atual
        $old_image = null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT imagem_destacada FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            $old_image = $stmt->fetchColumn();
        }
        
        $imagem_destacada = process_featured_image($_FILES['featured_image'], $old_image);
        if ($imagem_destacada === false) {
            throw new Exception("Erro ao processar a imagem destacada.");
        }
    }

    // Validar dados
    if (empty($titulo) || empty($slug) || empty($conteudo) || empty($resumo) || empty($categoria_id)) {
        throw new Exception("Todos os campos obrigatórios devem ser preenchidos.");
    }

    // Verificar se o slug já existe (exceto para o próprio post)
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id ?? 0]);
    if ($stmt->fetch()) {
        throw new Exception("Este slug já está em uso. Por favor, escolha outro.");
    }

    if ($id) {
        // Verifica se o usuário tem permissão para editar o post
        $stmt = $pdo->prepare("SELECT autor_id FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        
        if (!$post || !can_edit_post($post['autor_id'])) {
            throw new Exception("Você não tem permissão para editar este post.");
        }

        // Atualizar post existente
        $stmt = $pdo->prepare("
            UPDATE posts 
            SET titulo = ?, slug = ?, conteudo = ?, resumo = ?, categoria_id = ?, publicado = ?, 
                imagem_destacada = COALESCE(?, imagem_destacada), atualizado_em = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $imagem_destacada, $id]);

        // Remover tags antigas
        $stmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $stmt->execute([$id]);
    } else {
        // Inserir novo post
        $stmt = $pdo->prepare("
            INSERT INTO posts (titulo, slug, conteudo, resumo, categoria_id, publicado, autor_id, imagem_destacada, criado_em, atualizado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $_SESSION['usuario_id'], $imagem_destacada]);
        $id = $pdo->lastInsertId();
    }

    // Processar tags
    foreach ($tags as $tag_nome) {
        if (empty($tag_nome)) continue;

        // Criar slug da tag
        $tag_slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]/', '-', $tag_nome)));
        $tag_slug = preg_replace('/-+/', '-', $tag_slug);
        $tag_slug = trim($tag_slug, '-');

        // Verificar se a tag já existe
        $stmt = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
        $stmt->execute([$tag_slug]);
        $tag_id = $stmt->fetchColumn();

        if (!$tag_id) {
            // Inserir nova tag
            $stmt = $pdo->prepare("INSERT INTO tags (nome, slug) VALUES (?, ?)");
            $stmt->execute([$tag_nome, $tag_slug]);
            $tag_id = $pdo->lastInsertId();
        }

        // Relacionar tag com o post
        $stmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
        $stmt->execute([$id, $tag_id]);
    }

    $pdo->commit();
    $_SESSION['success'] = "Post salvo com sucesso!";
    header('Location: posts.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
    exit;
}
