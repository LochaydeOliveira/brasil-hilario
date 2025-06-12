<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Verifica se o usuário está autenticado
if (!isLoggedIn()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// Verifica se um arquivo foi enviado
if (!isset($_FILES['file'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Nenhum arquivo enviado']);
    exit;
}

$file = $_FILES['file'];

// Verifica se houve erro no upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Erro no upload do arquivo']);
    exit;
}

// Verifica o tipo do arquivo
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowed_types)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Tipo de arquivo não permitido']);
    exit;
}

// Cria o diretório de uploads se não existir
$upload_dir = '../uploads/' . date('Y/m');
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Gera um nome único para o arquivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$filepath = $upload_dir . '/' . $filename;

// Move o arquivo para o diretório de uploads
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Retorna a URL da imagem
    $image_url = '/uploads/' . date('Y/m') . '/' . $filename;
    echo json_encode(['location' => $image_url]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erro ao salvar o arquivo']);
}
?>
