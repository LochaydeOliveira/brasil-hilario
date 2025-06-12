<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

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
$title = trim($_POST['title']);
$slug = trim($_POST['slug']);
$content = trim($_POST['content']);
$excerpt = trim($_POST['excerpt']);
$category_id = (int)$_POST['category_id'];
$published = isset($_POST['published']) ? 1 : 0;

// Validações básicas
if (empty($title) || empty($slug) || empty($content) || empty($category_id)) {
    $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
    header('Location: edit-post.php' . ($post_id ? "?id=$post_id" : ''));
    exit;
}

try {
    // Verifica se o slug já existe
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $post_id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Este slug já está em uso. Por favor, escolha outro.";
        header('Location: edit-post.php' . ($post_id ? "?id=$post_id" : ''));
        exit;
    }

    // Processa a imagem destacada
    $featured_image = null;
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
            $featured_image = '/uploads/featured/' . date('Y/m') . '/' . $filename;
        } else {
            throw new Exception("Erro ao salvar a imagem");
        }
    }

    if ($post_id > 0) {
        // Atualiza o post existente
        $sql = "UPDATE posts SET 
                title = ?, 
                slug = ?, 
                content = ?, 
                excerpt = ?, 
                category_id = ?, 
                published = ?";
        
        $params = [$title, $slug, $content, $excerpt, $category_id, $published];

        if ($featured_image) {
            $sql .= ", featured_image = ?";
            $params[] = $featured_image;
        }

        $sql .= " WHERE id = ?";
        $params[] = $post_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Post atualizado com sucesso!";
    } else {
        // Insere um novo post
        $sql = "INSERT INTO posts (title, slug, content, excerpt, category_id, published, created_at";
        $params = [$title, $slug, $content, $excerpt, $category_id, $published, date('Y-m-d H:i:s')];

        if ($featured_image) {
            $sql .= ", featured_image";
            $params[] = $featured_image;
        }

        $sql .= ") VALUES (" . str_repeat("?,", count($params) - 1) . "?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Post criado com sucesso!";
    }

    header('Location: posts.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Erro ao salvar o post: " . $e->getMessage();
    header('Location: edit-post.php' . ($post_id ? "?id=$post_id" : ''));
    exit;
} 