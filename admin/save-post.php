<?php
require_once '../config/config.php';
require_once '../includes/db.php';  // Aqui seu $pdo deve estar configurado como PDO
require_once 'includes/auth.php';
require_once 'includes/functions.php';

check_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $titulo = trim($_POST['titulo']);
    $slug = strtolower(trim(preg_replace('/[^a-z0-9\-]+/', '-', $_POST['slug'])));
    $slug = trim($slug, '-');
    $conteudo = trim($_POST['conteudo']);
    $resumo = trim($_POST['resumo']);
    $categoria_id = (int)$_POST['categoria_id'];
    $publicado = isset($_POST['publicado']) ? 1 : 0;
    $tags = isset($_POST['tags']) ? array_filter(array_map('trim', explode(',', $_POST['tags']))) : [];

    $imagem_destacada = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['featured_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Tipo de arquivo não permitido.");
        }

        if ($file['size'] > $max_size) {
            throw new Exception("Arquivo muito grande.");
        }

        $upload_dir = '../uploads/images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Erro ao salvar imagem.");
        }

        $imagem_destacada = $filename;
    }

    if (empty($titulo) || empty($slug) || empty($conteudo) || empty($categoria_id)) {
        throw new Exception("Campos obrigatórios faltando.");
    }

    // Verificar slug duplicado
    $id_check = $id ?? 0;
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id_check]);
    if ($stmt->fetch()) {
        throw new Exception("Slug já usado.");
    }

    if ($id) {
        // Editar post existente
        $stmt_post = $pdo->prepare("SELECT autor_id, imagem_destacada FROM posts WHERE id = ?");
        $stmt_post->execute([$id]);
        $post = $stmt_post->fetch(PDO::FETCH_ASSOC);

        if (!$post) throw new Exception("Post não encontrado.");
        if (!can_edit_post($post['autor_id'])) throw new Exception("Sem permissão.");

        if ($_SESSION['usuario_tipo'] === 'admin') {
            $autor_id = isset($_POST['autor_id']) ? (int)$_POST['autor_id'] : $post['autor_id'];

            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = ? AND status = 'ativo'");
            $stmt->execute([$autor_id]);
            if (!$stmt->fetch()) throw new Exception("Autor inválido.");

            $stmt = $pdo->prepare("
                UPDATE posts 
                SET titulo = ?, slug = ?, conteudo = ?, resumo = ?, categoria_id = ?, publicado = ?, 
                    imagem_destacada = COALESCE(?, imagem_destacada), autor_id = ?, atualizado_em = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $imagem_destacada, $autor_id, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE posts 
                SET titulo = ?, slug = ?, conteudo = ?, resumo = ?, categoria_id = ?, publicado = ?, 
                    imagem_destacada = COALESCE(?, imagem_destacada), atualizado_em = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $imagem_destacada, $id]);
        }

        if ($imagem_destacada && $post['imagem_destacada']) {
            $old_image = '../uploads/images/' . $post['imagem_destacada'];
            if (file_exists($old_image)) unlink($old_image);
        }

        $stmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $stmt->execute([$id]);

    } else {
        // Novo post
        $autor_id = isset($_POST['autor_id']) ? (int)$_POST['autor_id'] : $_SESSION['usuario_id'];

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = ? AND status = 'ativo'");
        $stmt->execute([$autor_id]);
        if (!$stmt->fetch()) throw new Exception("Autor inválido.");

        $stmt = $pdo->prepare("
            INSERT INTO posts (titulo, slug, conteudo, resumo, categoria_id, publicado, autor_id, criado_em, atualizado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $autor_id]);

        $id = $pdo->lastInsertId();
    }

    foreach ($tags as $tag_nome) {
        if (empty($tag_nome)) continue;

        $tag_slug = strtolower(trim(preg_replace('/[^a-z0-9-]/', '-', $tag_nome)));
        $tag_slug = preg_replace('/-+/', '-', $tag_slug);
        $tag_slug = trim($tag_slug, '-');

        $stmt = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
        $stmt->execute([$tag_slug]);
        $tag = $stmt->fetch(PDO::FETCH_ASSOC);
        $tag_id = $tag['id'] ?? null;

        if (!$tag_id) {
            $stmt = $pdo->prepare("INSERT INTO tags (nome, slug) VALUES (?, ?)");
            $stmt->execute([$tag_nome, $tag_slug]);
            $tag_id = $pdo->lastInsertId();
        }

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
