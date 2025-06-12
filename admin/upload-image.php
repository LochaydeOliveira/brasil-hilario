<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Verifica se o usuário está logado
if (!check_login()) {
    $_SESSION['error'] = 'Você precisa estar logado para acessar esta página.';
    header('Location: login.php');
    exit;
}

// Verifica se o usuário é admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = 'Você não tem permissão para acessar esta página.';
    header('Location: index.php');
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método não permitido.';
    header('Location: index.php');
    exit;
}

// Verifica se um arquivo foi enviado
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Nenhum arquivo foi enviado ou ocorreu um erro no upload.';
    header('Location: index.php');
    exit;
}

$file = $_FILES['file'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

// Verifica o tipo do arquivo
if (!in_array($file['type'], $allowed_types)) {
    $_SESSION['error'] = 'Tipo de arquivo não permitido. Apenas imagens JPG, PNG e GIF são aceitas.';
    header('Location: index.php');
    exit;
}

// Verifica o tamanho do arquivo
if ($file['size'] > $max_size) {
    $_SESSION['error'] = 'O arquivo é muito grande. O tamanho máximo permitido é 5MB.';
    header('Location: index.php');
    exit;
}

// Cria o diretório de uploads se não existir
$upload_dir = '../uploads/images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Gera um nome único para o arquivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Move o arquivo para o diretório de uploads
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Retorna a URL da imagem
    $image_url = '/uploads/images/' . $filename;
    echo json_encode(['location' => $image_url]);
} else {
    $_SESSION['error'] = 'Erro ao salvar o arquivo.';
    header('Location: index.php');
    exit;
}
?>
