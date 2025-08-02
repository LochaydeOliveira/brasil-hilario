<?php
header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../includes/db.php';
require_once '../includes/AnunciosManager.php';

// Log para debug
error_log("API registrar-clique-anuncio.php chamada");

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Verificar se os dados necessários foram enviados
$input = json_decode(file_get_contents('php://input'), true);
error_log("Dados recebidos: " . json_encode($input));

if (!isset($input['anuncio_id']) || !isset($input['tipo_clique']) || !isset($input['post_id'])) {
    error_log("Dados incompletos recebidos");
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

$anuncioId = (int) $input['anuncio_id'];
$tipoClique = $input['tipo_clique'];
$postId = (int) $input['post_id'];

error_log("Dados processados - anuncio_id: $anuncioId, tipo_clique: $tipoClique, post_id: $postId");

// Validar tipo de clique
$tiposValidos = ['imagem', 'titulo', 'cta'];
if (!in_array($tipoClique, $tiposValidos)) {
    error_log("Tipo de clique inválido: $tipoClique");
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de clique inválido']);
    exit;
}

try {
    $anunciosManager = new AnunciosManager($pdo);
    
    // Verificar se o anúncio existe e está ativo
    $anuncio = $anunciosManager->getAnuncio($anuncioId);
    if (!$anuncio || !$anuncio['ativo']) {
        error_log("Anúncio não encontrado ou inativo: $anuncioId");
        http_response_code(404);
        echo json_encode(['error' => 'Anúncio não encontrado ou inativo']);
        exit;
    }
    
    // Registrar o clique
    $sucesso = $anunciosManager->registrarClique($anuncioId, $postId, $tipoClique);
    
    if ($sucesso) {
        error_log("Clique registrado com sucesso - anuncio_id: $anuncioId, post_id: $postId, tipo: $tipoClique");
        echo json_encode([
            'success' => true,
            'message' => 'Clique registrado com sucesso',
            'anuncio_id' => $anuncioId,
            'tipo_clique' => $tipoClique,
            'post_id' => $postId
        ]);
    } else {
        error_log("Erro ao registrar clique no banco de dados");
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao registrar clique']);
    }
    
} catch (Exception $e) {
    error_log("Erro ao registrar clique no anúncio: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
} 