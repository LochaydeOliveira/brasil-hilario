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
$title = trim($_POST['title']);
$slug = trim($_POST['slug']);
$content = trim($_POST['content']);
$excerpt = trim($_POST['excerpt']);
$category_id = (int)$_POST['category_id'];
$published = isset($_POST['published']) ? 1 : 0;

// Validação básica
if (empty($title) || empty($slug) || empty($content) || $category_id <= 0) {
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

    // Processa a imagem destacada
    $featured_image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['error'] = "Tipo de arquivo não permitido. Use apenas JPG, JPEG, PNG ou GIF.";
            header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
            exit;
        }

        $featured_image = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $featured_image;

        if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
            $_SESSION['error'] = "Erro ao fazer upload da imagem.";
            header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
            exit;
        }

        // Se estiver editando e houver uma nova imagem, remove a antiga
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT featured_image FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            $old_image = $stmt->fetchColumn();
            if ($old_image && file_exists($upload_dir . $old_image)) {
                unlink($upload_dir . $old_image);
            }
        }
    }

    if ($id > 0) {
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
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Post atualizado com sucesso!";
    } else {
        // Insere um novo post
        $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, excerpt, category_id, published, featured_image, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $slug, $content, $excerpt, $category_id, $published, $featured_image]);

        $_SESSION['success'] = "Post criado com sucesso!";
    }

    header('Location: posts.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao salvar o post: " . $e->getMessage();
    header('Location: ' . ($id ? "editar-post.php?id=$id" : 'novo-post.php'));
    exit;
}
