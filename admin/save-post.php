<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Verifica se o usuário está autenticado
check_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

// Coletar e sanitizar os dados
$post_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title']);
$slug = strtolower(trim(preg_replace('/[^a-z0-9-]+/', '-', $_POST['slug'])));
$content = trim($_POST['content']);
$excerpt = trim($_POST['excerpt']);
$category_id = (int)$_POST['category_id'];
$published = isset($_POST['published']) ? 1 : 0;

// Validação
if (empty($title) || empty($slug) || empty($content) || !$category_id) {
    $_SESSION['error'] = "Preencha todos os campos obrigatórios.";
    header('Location: ' . ($post_id ? "editar-post.php?id=$post_id" : "novo-post.php"));
    exit;
}

try {
    // Verificar slug duplicado
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $post_id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Este slug já está em uso. Escolha outro.";
        header('Location: ' . ($post_id ? "editar-post.php?id=$post_id" : "novo-post.php"));
        exit;
    }

    // Processar imagem destacada
    $featured_image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['featured_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Tipo de imagem não permitido.");
        }

        if ($file['size'] > $max_size) {
            throw new Exception("Imagem muito grande. Máximo: 5MB.");
        }

        // Criar diretório de upload
        $upload_dir = '../uploads/featured/' . date('Y/m');
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $upload_dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Erro ao salvar imagem.");
        }

        $featured_image = '/uploads/featured/' . date('Y/m') . '/' . $filename;
    }

    if ($post_id > 0) {
        // Atualizar post existente
        $sql = "UPDATE posts SET title = ?, slug = ?, content = ?, excerpt = ?, category_id = ?, published = ?, updated_at = NOW()";
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
        // Inserir novo post
        $sql = "INSERT INTO posts (title, slug, content, excerpt, category_id, published, created_at, updated_at";
        $values = "?, ?, ?, ?, ?, ?, NOW(), NOW()";
        $params = [$title, $slug, $content, $excerpt, $category_id, $published];

        if ($featured_image) {
            $sql .= ", featured_image";
            $values .= ", ?";
            $params[] = $featured_image;
        }

        $sql .= ") VALUES ($values)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Post criado com sucesso!";
    }

    header('Location: posts.php');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Erro ao salvar o post: " . $e->getMessage();
    header('Location: ' . ($post_id ? "editar-post.php?id=$post_id" : "novo-post.php"));
    exit;
}
