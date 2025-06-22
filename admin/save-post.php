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
    // Iniciar transação para garantir atomicidade
    $conn->autocommit(FALSE);

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
    $id_para_verificacao = $id ?? 0;
    $stmt = $conn->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->bind_param("si", $slug, $id_para_verificacao);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->fetch_assoc()) {
        throw new Exception("Este slug já está em uso. Por favor, escolha outro.");
    }

    if ($id) {
        // Verifica se o usuário tem permissão para editar o post
        $stmt = $conn->prepare("SELECT autor_id, imagem_destacada FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        
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
        $stmt = $conn->prepare("
            UPDATE posts 
            SET titulo = ?, slug = ?, conteudo = ?, resumo = ?, categoria_id = ?, publicado = ?, 
                imagem_destacada = COALESCE(?, imagem_destacada), atualizado_em = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("ssssiisi", $titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $imagem_destacada, $id);
        $stmt->execute();

        // Remover tags antigas
        $stmt = $conn->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } else {
        // Inserir novo post
        $autor_id = $_SESSION['usuario_id'];
        $stmt = $conn->prepare("
            INSERT INTO posts (titulo, slug, conteudo, resumo, categoria_id, publicado, autor_id, criado_em, atualizado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("ssssiii", $titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $autor_id);
        $stmt->execute();
        $id = $conn->insert_id;
    }

    // Processar tags
    foreach ($tags as $tag_nome) {
        if (empty($tag_nome)) continue;

        // Criar slug da tag
        $tag_slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]/', '-', $tag_nome)));
        $tag_slug = preg_replace('/-+/', '-', $tag_slug);
        $tag_slug = trim($tag_slug, '-');

        // Verificar se a tag já existe
        $stmt = $conn->prepare("SELECT id FROM tags WHERE slug = ?");
        $stmt->bind_param("s", $tag_slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $tag = $result->fetch_assoc();
        $tag_id = $tag['id'] ?? null;

        if (!$tag_id) {
            // Inserir nova tag
            $stmt = $conn->prepare("INSERT INTO tags (nome, slug) VALUES (?, ?)");
            $stmt->bind_param("ss", $tag_nome, $tag_slug);
            $stmt->execute();
            $tag_id = $conn->insert_id;
        }

        // Relacionar tag com o post
        $stmt = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $id, $tag_id);
        $stmt->execute();
    }

    $conn->commit();
    $_SESSION['success'] = "Post salvo com sucesso!";
    header('Location: posts.php');
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
    exit;
}
