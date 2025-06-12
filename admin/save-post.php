<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Verifica se o usuário está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

$post_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$titulo = trim($_POST['title']);
$slug = trim($_POST['slug']);
$conteudo = trim($_POST['content']);
$resumo = trim($_POST['excerpt']);
$categoria_id = (int)$_POST['category_id'];
$publicado = isset($_POST['published']) ? 1 : 0;
$editor_type = 'tinymce';
$autor_id = $_SESSION['usuario_id'] ?? null;

// Validações básicas
if (empty($titulo) || empty($slug) || empty($conteudo) || empty($categoria_id)) {
    $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
    header('Location: editar-post.php' . ($post_id ? "?id=$post_id" : '') . '?error=campos_vazios');
    exit;
}

try {
    // Verifica se o slug já existe
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $post_id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Este slug já está em uso. Por favor, escolha outro.";
        header('Location: editar-post.php' . ($post_id ? "?id=$post_id" : '') . '?error=slug_duplicado');
        exit;
    }

    // Processa a imagem destacada
    $imagem_destacada = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['featured_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validações
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Tipo de arquivo não permitido");
        }

        if ($file['size'] > $max_size) {
            throw new Exception("Arquivo muito grande");
        }

        // Cria o diretório de uploads se não existir
        $upload_dir = '../uploads/featured/' . date('Y/m');
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Gera um nome único para o arquivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $upload_dir . '/' . $filename;

        // Move o arquivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $imagem_destacada = '/uploads/featured/' . date('Y/m') . '/' . $filename;
        } else {
            throw new Exception("Erro ao salvar a imagem");
        }
    }

    if ($post_id > 0) {
        // Atualiza o post existente
        $sql = "UPDATE posts SET 
                titulo = ?, 
                slug = ?, 
                conteudo = ?, 
                resumo = ?, 
                categoria_id = ?, 
                publicado = ?, 
                editor_type = ?, 
                atualizado_em = CURRENT_TIMESTAMP";
        
        $params = [$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $editor_type];

        if ($imagem_destacada) {
            $sql .= ", imagem_destacada = ?";
            $params[] = $imagem_destacada;
        }

        $sql .= " WHERE id = ?";
        $params[] = $post_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Post atualizado com sucesso!";
    } else {
        // Insere um novo post
        $sql = "INSERT INTO posts (titulo, slug, conteudo, resumo, categoria_id, publicado, editor_type, autor_id, criado_em, atualizado_em";
        $values = "?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP";
        $params = [$titulo, $slug, $conteudo, $resumo, $categoria_id, $publicado, $editor_type, $autor_id];

        if ($imagem_destacada) {
            $sql .= ", imagem_destacada";
            $values .= ", ?";
            $params[] = $imagem_destacada;
        }

        $sql .= ") VALUES (" . $values . ")";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Post criado com sucesso!";
    }

    header('Location: posts.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Erro ao salvar o post: " . $e->getMessage();
    header('Location: ' . ($post_id ? "editar-post.php?id=$post_id" : "novo-post.php") . '&error=db_error');
    exit;
} 