<?php
header('Content-Type: application/json');

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!empty($_FILES['file']['name'])) {
    $file = $_FILES['file'];
    $filename = uniqid() . '-' . basename($file['name']);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo json_encode(['location' => $targetFile]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao mover o arquivo.']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Nenhum arquivo enviado.']);
