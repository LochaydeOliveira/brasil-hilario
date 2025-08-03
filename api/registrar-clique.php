<?php
// API SIMPLES para registrar cliques - Integrada com sistema existente
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Receber dados
$dados = json_decode(file_get_contents('php://input'), true);

// Validar dados básicos
if (!$dados || !isset($dados['anuncio_id'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

try {
    // Usar a conexão existente do projeto
    require_once '../config/database.php';
    require_once '../includes/AnunciosManager.php';
    
    // Usar o AnunciosManager existente
    $anunciosManager = new AnunciosManager($pdo);
    
    // Registrar clique usando o método existente
    $sucesso = $anunciosManager->registrarClique(
        $dados['anuncio_id'],
        $dados['post_id'] ?? 0,
        $dados['tipo_clique'] ?? 'imagem'
    );
    
    if ($sucesso) {
        echo json_encode(['success' => true, 'message' => 'Clique registrado']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao registrar clique']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro no servidor']);
}
?> 