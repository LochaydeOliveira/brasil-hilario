<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../config/admin_ips.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Obter dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$postId = $data['post_id'] ?? null;

if (!$postId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do post não fornecido']);
    exit;
}

try {
    // Verificar se deve contar a visualização
    if (shouldCountView($postId)) {
        // Incrementar visualizações
        $stmt = $pdo->prepare("UPDATE posts SET visualizacoes = visualizacoes + 1 WHERE id = ?");
        $stmt->execute([$postId]);
        
        echo json_encode(['success' => true, 'counted' => true]);
    } else {
        echo json_encode(['success' => true, 'counted' => false, 'reason' => 'filtered']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao incrementar visualizações']);
} 