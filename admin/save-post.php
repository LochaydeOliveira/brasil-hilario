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
        $file = $_FILES['featured_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Tipo de arquivo não permitido. Apenas imagens JPG, PNG, GIF e WebP são aceitas.");
        }

        if ($file['size'] > $max_size) {
            throw new Exception("O arquivo é muito grande. O tamanho máximo permitido é 5MB.");
        }

        // Criar diretório de uploads se não existir
        $upload_dir = '../uploads/images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Gerar nome único para o arquivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Mover o arquivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Erro ao salvar a imagem destacada.");
        }

        $imagem_destacada = $filename;
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
        $stmt = $pdo->prepare("SELECT autor_id, imagem_destacada FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        
        if (!$post || !can_edit_post($post['autor_id'])) {
            throw new Exception("Você não tem permissão para editar este post.");
        }

        // Se uma nova imagem foi enviada, remover a antiga
        if ($imagem_destacada && $post['imagem_destacada']) {
            $old_image_path = '../uploads/images/' . $post['imagem_destacada'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
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
            INSERT INTO posts (titulo, slug, conteudo, resumo, categoria_id, publicado, autor_id, criado_em, atualizado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $_SESSION['usuario_id']]);
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
